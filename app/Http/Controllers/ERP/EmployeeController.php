<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\Branch;
use App\Models\ERP\Company;
use App\Models\ERP\Department;
use App\Models\ERP\Employee;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class EmployeeController extends Controller
{
    public function index(Request $request): View
    {
        $companyId = $request->input('company_id', 1);

        $query = Employee::where('company_id', $companyId)->with(['department', 'branch']);

        if ($request->filled('department_id')) {
            $query->where('department_id', $request->integer('department_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->string('status'));
        }

        if ($request->filled('search')) {
            $search = $request->string('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('employee_code', 'like', "%{$search}%")
                  ->orWhere('designation', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $employees = $query->latest('joining_date')->paginate(15)->withQueryString();
        $departments = Department::where('company_id', $companyId)->get();

        return view('erp.employees.index', compact('employees', 'departments'));
    }

    public function create(): View
    {
        $companies = Company::where('is_active', true)->get();
        $branches = Branch::where('is_active', true)->get();
        $departments = Department::all();

        return view('erp.employees.create', compact('companies', 'branches', 'departments'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'company_id' => ['required', 'exists:erp_companies,id'],
            'branch_id' => ['nullable', 'exists:erp_branches,id'],
            'department_id' => ['nullable', 'exists:erp_departments,id'],
            'employee_code' => ['required', 'string', 'max:50', 'unique:erp_employees,employee_code'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'designation' => ['required', 'string', 'max:100'],
            'joining_date' => ['required', 'date'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'house_rent' => ['nullable', 'numeric', 'min:0'],
            'medical_allowance' => ['nullable', 'numeric', 'min:0'],
            'other_allowance' => ['nullable', 'numeric', 'min:0'],
            'tax_deduction' => ['nullable', 'numeric', 'min:0'],
            'provident_fund' => ['nullable', 'numeric', 'min:0'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_no' => ['nullable', 'string', 'max:100'],
        ]);

        $basic = (float) $validated['basic_salary'];
        $house = (float) ($validated['house_rent'] ?? 0);
        $med = (float) ($validated['medical_allowance'] ?? 0);
        $other = (float) ($validated['other_allowance'] ?? 0);
        $validated['gross_salary'] = $basic + $house + $med + $other;

        $employee = Employee::create($validated);

        return redirect()->route('erp.employees.show', $employee)
            ->with('success', 'Staff member enrolled successfully.');
    }

    public function show(Employee $employee): View
    {
        $employee->load([
            'department',
            'branch',
            'payrollItems.payrollRun',
            'attendances' => function ($q) {
                $q->latest('date')->take(10);
            },
            'leaveRequests',
            'commissions.booking',
        ]);

        return view('erp.employees.show', compact('employee'));
    }

    public function edit(Employee $employee): View
    {
        $companies = Company::where('is_active', true)->get();
        $branches = Branch::where('company_id', $employee->company_id)->get();
        $departments = Department::where('company_id', $employee->company_id)->get();

        return view('erp.employees.edit', compact('employee', 'companies', 'branches', 'departments'));
    }

    public function update(Request $request, Employee $employee): RedirectResponse
    {
        $validated = $request->validate([
            'branch_id' => ['nullable', 'exists:erp_branches,id'],
            'department_id' => ['nullable', 'exists:erp_departments,id'],
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['required', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'designation' => ['required', 'string', 'max:100'],
            'basic_salary' => ['required', 'numeric', 'min:0'],
            'house_rent' => ['nullable', 'numeric', 'min:0'],
            'medical_allowance' => ['nullable', 'numeric', 'min:0'],
            'other_allowance' => ['nullable', 'numeric', 'min:0'],
            'tax_deduction' => ['nullable', 'numeric', 'min:0'],
            'provident_fund' => ['nullable', 'numeric', 'min:0'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_no' => ['nullable', 'string', 'max:100'],
            'status' => ['required', 'in:active,on_leave,resigned,terminated'],
        ]);

        $basic = (float) $validated['basic_salary'];
        $house = (float) ($validated['house_rent'] ?? 0);
        $med = (float) ($validated['medical_allowance'] ?? 0);
        $other = (float) ($validated['other_allowance'] ?? 0);
        $validated['gross_salary'] = $basic + $house + $med + $other;

        $employee->update($validated);

        return redirect()->route('erp.employees.show', $employee)
            ->with('success', 'Staff details updated successfully.');
    }
}

