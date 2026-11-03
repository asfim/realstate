<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\HrEmployee;
use App\Models\ERP\HrPayroll;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use App\Services\ERP\DocumentSequenceService;
use Illuminate\Http\Request;

class HrPayrollController extends Controller
{
    public function __construct(
        protected CompanyBranchContext $context,
        protected DocumentSequenceService $sequenceService,
        protected AuditLogService $auditLog
    ) {
    }

    public function index(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $query = HrPayroll::where('company_id', $currentCompany?->id)
            ->with(['employee.department', 'employee.designation'])
            ->latest('salary_year')
            ->latest('salary_month');

        if ($request->filled('salary_month')) {
            $query->where('salary_month', $request->salary_month);
        }

        if ($request->filled('salary_year')) {
            $query->where('salary_year', $request->salary_year);
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        $payrolls = $query->paginate(15)->withQueryString();

        return view('erp.hr.payrolls.index', compact('payrolls'));
    }

    public function create(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $employees = HrEmployee::where('company_id', $currentCompany?->id)->where('status', 'active')->get();

        $selectedEmp = null;
        if ($request->filled('employee_id')) {
            $selectedEmp = HrEmployee::find($request->employee_id);
        }

        return view('erp.hr.payrolls.create', compact('employees', 'selectedEmp'));
    }

    public function store(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $currentBranch = $this->context->currentBranch();

        $validated = $request->validate([
            'employee_id'  => 'required|exists:hr_employees,id',
            'salary_month' => 'required|integer|min:1|max:12',
            'salary_year'  => 'required|integer|min:2020|max:2035',
            'bonus'        => 'nullable|numeric|min:0',
            'deductions'   => 'nullable|numeric|min:0',
        ]);

        $employee = HrEmployee::findOrFail($validated['employee_id']);

        // Check if payroll already processed for this month/year
        $existing = HrPayroll::where('employee_id', $employee->id)
            ->where('salary_month', $validated['salary_month'])
            ->where('salary_year', $validated['salary_year'])
            ->first();

        if ($existing) {
            return back()->withInput()->with('error', "Salary for {$employee->name} for month {$validated['salary_month']}/{$validated['salary_year']} is already processed (Code: {$existing->payroll_code}).");
        }

        $gross = (float) $employee->gross_salary;
        $bonus = (float) ($validated['bonus'] ?? 0);
        $deductions = (float) ($validated['deductions'] ?? 0);
        $netPayable = max(0, $gross + $bonus - $deductions);

        $payrollCode = $this->sequenceService->getNextNumber('payroll', $currentCompany->id, $employee->branch_id ?? $currentBranch?->id);

        $payroll = HrPayroll::create([
            'company_id'     => $currentCompany->id,
            'branch_id'      => $employee->branch_id ?? $currentBranch?->id,
            'employee_id'    => $employee->id,
            'payroll_code'   => $payrollCode,
            'salary_month'   => $validated['salary_month'],
            'salary_year'    => $validated['salary_year'],
            'gross_salary'   => $gross,
            'bonus'          => $bonus,
            'deductions'     => $deductions,
            'net_payable'    => $netPayable,
            'payment_status' => 'unpaid',
            'processed_by'   => auth('admin')->id(),
        ]);

        $this->auditLog->log('hr.payroll_processed', $payroll, null, $payroll->toArray(), $currentCompany->id);

        return redirect()->route('erp.hr-payrolls.show', $payroll)->with('success', "Payroll {$payroll->payroll_code} generated for {$employee->name}.");
    }

    public function pay(Request $request, HrPayroll $payroll)
    {
        $validated = $request->validate([
            'payment_date'   => 'required|date',
            'payment_method' => 'required|string|in:bank_transfer,cheque,cash',
        ]);

        $payroll->update([
            'payment_status' => 'paid',
            'payment_date'   => $validated['payment_date'],
            'payment_method' => $validated['payment_method'],
        ]);

        $this->auditLog->log('hr.payroll_disbursed', $payroll, null, $payroll->toArray(), $payroll->company_id);

        return back()->with('success', "Salary {$payroll->payroll_code} marked as disbursed via " . ucfirst(str_replace('_', ' ', $validated['payment_method'])) . ".");
    }

    public function show(HrPayroll $payroll)
    {
        $payroll->load(['employee.department', 'employee.designation', 'employee.branch']);
        $hrPayroll = $payroll;
        return view('erp.hr.payrolls.show', compact('payroll', 'hrPayroll'));
    }

    public function destroy(HrPayroll $payroll)
    {
        if ($payroll->payment_status === 'paid') {
            return back()->with('error', 'Cannot delete an already disbursed payroll voucher.');
        }

        $old = $payroll->toArray();
        $payroll->delete();
        $this->auditLog->log('hr.payroll_deleted', null, $old, null, $payroll->company_id);

        return redirect()->route('erp.hr-payrolls.index')->with('success', 'Payroll voucher deleted.');
    }
}
