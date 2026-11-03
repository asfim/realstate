<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\Department;
use App\Models\ERP\Designation;
use App\Models\ERP\HrEmployee;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use App\Services\ERP\DocumentSequenceService;
use Illuminate\Http\Request;

class HrEmployeeController extends Controller
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
        $query = HrEmployee::where('company_id', $currentCompany?->id)
            ->with(['branch', 'department', 'designation'])
            ->latest('joining_date');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->department_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('employee_code', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $employees = $query->paginate(15)->withQueryString();
        $departments = Department::where('company_id', $currentCompany?->id)->get();

        return view('erp.hr.employees.index', compact('employees', 'departments'));
    }

    public function create()
    {
        $currentCompany = $this->context->currentCompany();
        $branches = $this->context->accessibleBranches();
        $departments = Department::where('company_id', $currentCompany?->id)->get();
        $designations = Designation::where('company_id', $currentCompany?->id)->get();

        return view('erp.hr.employees.create', compact('branches', 'departments', 'designations'));
    }

    public function store(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $currentBranch = $this->context->currentBranch();

        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'phone'                => 'required|string|max:50',
            'email'                => 'nullable|email|max:255',
            'nid_no'               => 'nullable|string|max:100',
            'joining_date'         => 'required|date',
            'employment_type'      => 'required|string|in:permanent,probation,contract',
            'branch_id'            => 'nullable|exists:branches,id',
            'department_id'        => 'nullable|exists:departments,id',
            'designation_id'       => 'nullable|exists:designations,id',
            'basic_salary'         => 'required|numeric|min:1',
            'house_rent_allowance' => 'nullable|numeric|min:0',
            'medical_allowance'    => 'nullable|numeric|min:0',
            'conveyance_allowance' => 'nullable|numeric|min:0',
            'bank_name'            => 'nullable|string|max:100',
            'bank_account_no'      => 'nullable|string|max:100',
            'status'               => 'required|string|in:active,resigned,terminated',
        ]);

        $basic = (float) $validated['basic_salary'];
        $house = (float) ($validated['house_rent_allowance'] ?? 0);
        $medical = (float) ($validated['medical_allowance'] ?? 0);
        $conveyance = (float) ($validated['conveyance_allowance'] ?? 0);
        $gross = $basic + $house + $medical + $conveyance;

        $validated['company_id'] = $currentCompany->id;
        $validated['branch_id'] = $validated['branch_id'] ?? $currentBranch?->id;
        $validated['gross_salary'] = $gross;
        $validated['employee_code'] = $this->sequenceService->getNextNumber('employee', $currentCompany->id, $validated['branch_id']);

        $employee = HrEmployee::create($validated);
        $this->auditLog->log('hr.employee_created', $employee, null, $employee->toArray(), $currentCompany->id);

        return redirect()->route('erp.hr-employees.show', $employee)->with('success', "Employee {$employee->name} ({$employee->employee_code}) registered.");
    }

    public function show(HrEmployee $employee)
    {
        $employee->load(['branch', 'department', 'designation', 'payrolls']);
        $hrEmployee = $employee;
        return view('erp.hr.employees.show', compact('employee', 'hrEmployee'));
    }

    public function edit(HrEmployee $employee)
    {
        $currentCompany = $this->context->currentCompany();
        $branches = $this->context->accessibleBranches();
        $departments = Department::where('company_id', $currentCompany?->id)->get();
        $designations = Designation::where('company_id', $currentCompany?->id)->get();
        $hrEmployee = $employee;

        return view('erp.hr.employees.edit', compact('employee', 'hrEmployee', 'branches', 'departments', 'designations'));
    }

    public function update(Request $request, HrEmployee $employee)
    {
        $validated = $request->validate([
            'name'                 => 'required|string|max:255',
            'phone'                => 'required|string|max:50',
            'email'                => 'nullable|email|max:255',
            'nid_no'               => 'nullable|string|max:100',
            'joining_date'         => 'required|date',
            'employment_type'      => 'required|string|in:permanent,probation,contract',
            'branch_id'            => 'nullable|exists:branches,id',
            'department_id'        => 'nullable|exists:departments,id',
            'designation_id'       => 'nullable|exists:designations,id',
            'basic_salary'         => 'required|numeric|min:1',
            'house_rent_allowance' => 'nullable|numeric|min:0',
            'medical_allowance'    => 'nullable|numeric|min:0',
            'conveyance_allowance' => 'nullable|numeric|min:0',
            'bank_name'            => 'nullable|string|max:100',
            'bank_account_no'      => 'nullable|string|max:100',
            'status'               => 'required|string|in:active,resigned,terminated',
        ]);

        $basic = (float) $validated['basic_salary'];
        $house = (float) ($validated['house_rent_allowance'] ?? 0);
        $medical = (float) ($validated['medical_allowance'] ?? 0);
        $conveyance = (float) ($validated['conveyance_allowance'] ?? 0);
        $gross = $basic + $house + $medical + $conveyance;

        $validated['gross_salary'] = $gross;

        $old = $employee->toArray();
        $employee->update($validated);
        $this->auditLog->log('hr.employee_updated', $employee, $old, $employee->toArray(), $employee->company_id);

        return redirect()->route('erp.hr-employees.show', $employee)->with('success', 'Employee profile updated.');
    }

    public function destroy(HrEmployee $employee)
    {
        if ($employee->payrolls()->count() > 0) {
            return back()->with('error', 'Cannot delete employee with existing payroll disbursement records.');
        }

        $old = $employee->toArray();
        $employee->delete();
        $this->auditLog->log('hr.employee_deleted', null, $old, null, $employee->company_id);

        return redirect()->route('erp.hr-employees.index')->with('success', 'Employee deleted successfully.');
    }
}
