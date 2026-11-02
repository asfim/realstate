<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Payroll;
use App\Models\TeamMember;
use App\Models\Transaction;
use App\Models\AccountCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PayrollController extends Controller
{
    public function index(Request $request)
    {
        $query = Payroll::with(['teamMember', 'creator'])->latest('payment_date')->latest('id');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('staff_name', 'like', "%{$search}%")
                  ->orWhere('staff_role', 'like', "%{$search}%")
                  ->orWhere('salary_month', 'like', "%{$search}%");
            });
        }

        if ($request->filled('salary_month')) {
            $query->where('salary_month', $request->salary_month);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $totalDisbursed = (clone $query)->where('status', 'paid')->sum('net_salary');
        $payrolls = $query->paginate(15)->withQueryString();

        $teamMembers = TeamMember::where('is_active', true)->select('id', 'name', 'role')->get();

        return view('backend.accounts.payrolls.index', compact('payrolls', 'teamMembers', 'totalDisbursed'));
    }

    public function create()
    {
        $teamMembers = TeamMember::where('is_active', true)->select('id', 'name', 'role')->get();

        return view('backend.accounts.payrolls.create', compact('teamMembers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'team_member_id' => 'nullable|exists:team_members,id',
            'staff_name' => 'required|string|max:255',
            'staff_role' => 'nullable|string|max:255',
            'salary_month' => 'required|string|max:50',
            'basic_salary' => 'required|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|in:bank_transfer,cash,cheque,mobile_banking',
            'status' => 'required|string|in:paid,pending,partial',
            'note' => 'nullable|string',
            'record_as_expense' => 'nullable|boolean',
        ]);

        $basic = (float)$validated['basic_salary'];
        $allowance = (float)($validated['allowance'] ?? 0);
        $deduction = (float)($validated['deduction'] ?? 0);
        $netSalary = $basic + $allowance - $deduction;

        $validated['allowance'] = $allowance;
        $validated['deduction'] = $deduction;
        $validated['net_salary'] = max(0, $netSalary);
        $validated['created_by'] = Auth::guard('admin')->id();

        $payroll = Payroll::create($validated);

        // Optionally record as expense in transactions
        if ($request->boolean('record_as_expense') && $validated['status'] === 'paid') {
            $category = AccountCategory::where('type', 'expense')->where('name', 'like', '%Salary%')->first();
            Transaction::create([
                'type' => 'expense',
                'account_category_id' => $category?->id,
                'title' => 'Salary Disbursement: ' . $payroll->staff_name . ' (' . $payroll->salary_month . ')',
                'amount' => $payroll->net_salary,
                'transaction_date' => $payroll->payment_date,
                'payment_method' => $payroll->payment_method,
                'reference_no' => 'PAYROLL-#' . $payroll->id,
                'note' => 'Automatic expense recorded from Payroll #' . $payroll->id,
                'created_by' => Auth::guard('admin')->id(),
            ]);
        }

        return redirect()->route('admin.payrolls.index')->with('success', 'Staff salary / payroll recorded successfully.');
    }

    public function edit(Payroll $payroll)
    {
        $teamMembers = TeamMember::where('is_active', true)->select('id', 'name', 'role')->get();

        return view('backend.accounts.payrolls.edit', compact('payroll', 'teamMembers'));
    }

    public function update(Request $request, Payroll $payroll)
    {
        $validated = $request->validate([
            'team_member_id' => 'nullable|exists:team_members,id',
            'staff_name' => 'required|string|max:255',
            'staff_role' => 'nullable|string|max:255',
            'salary_month' => 'required|string|max:50',
            'basic_salary' => 'required|numeric|min:0',
            'allowance' => 'nullable|numeric|min:0',
            'deduction' => 'nullable|numeric|min:0',
            'payment_date' => 'required|date',
            'payment_method' => 'required|string|in:bank_transfer,cash,cheque,mobile_banking',
            'status' => 'required|string|in:paid,pending,partial',
            'note' => 'nullable|string',
        ]);

        $basic = (float)$validated['basic_salary'];
        $allowance = (float)($validated['allowance'] ?? 0);
        $deduction = (float)($validated['deduction'] ?? 0);
        $netSalary = $basic + $allowance - $deduction;

        $validated['allowance'] = $allowance;
        $validated['deduction'] = $deduction;
        $validated['net_salary'] = max(0, $netSalary);

        $payroll->update($validated);

        return redirect()->route('admin.payrolls.index')->with('success', 'Payroll record updated successfully.');
    }

    public function destroy(Payroll $payroll)
    {
        $payroll->delete();

        return redirect()->route('admin.payrolls.index')->with('success', 'Payroll record deleted successfully.');
    }
}

