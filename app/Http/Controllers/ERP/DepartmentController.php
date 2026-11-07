<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\Company;
use App\Models\ERP\Department;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct(
        protected CompanyBranchContext $context,
        protected AuditLogService $auditLog
    ) {
    }

    public function index()
    {
        $currentCompany = $this->resolveCompany();
        $departments = Department::where('company_id', $currentCompany?->id)
            ->withCount('designations')
            ->latest()
            ->paginate(15);

        return view('erp.organization.departments.index', compact('departments'));
    }

    public function store(Request $request)
    {
        $currentCompany = $this->resolveCompany();

        $validated = $request->validate([
            'name'       => 'required|string|max:255',
            'code'       => 'nullable|string|max:50',
            'status'     => 'required|in:active,inactive',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $companyId = $validated['company_id'] ?? $currentCompany?->id;
        if (!$companyId) {
            $company = $this->resolveCompany(true);
            $companyId = $company->id;
        }

        $validated['company_id'] = $companyId;
        $department = Department::create($validated);
        $this->auditLog->log('department.created', $department, null, $department->toArray(), $companyId);

        return redirect()->route('erp.departments.index')->with('success', 'Department created successfully.');
    }

    /**
     * Resolve company with automatic provisioning fallback.
     */
    protected function resolveCompany(bool $forceCreate = false): ?Company
    {
        $company = $this->context->currentCompany();
        if ($company) {
            return $company;
        }

        $company = Company::where('status', 'active')->first() ?? Company::first();
        if ($company) {
            $this->context->setCompany($company);
            return $company;
        }

        if ($forceCreate || Company::count() === 0) {
            try {
                $company = Company::firstOrCreate(
                    ['code' => 'AURELIA'],
                    [
                        'legal_name'        => 'Aurelia Estates & Developers Ltd.',
                        'trade_name'        => 'Aurelia Living',
                        'registration_no'   => 'C-198425/2026',
                        'bin'               => '004829103-0101',
                        'tin'               => '491827401928',
                        'base_currency'     => 'BDT',
                        'fiscal_year_start' => '07-01',
                        'timezone'          => 'Asia/Dhaka',
                        'status'            => 'active',
                    ]
                );
                $this->context->setCompany($company);
                return $company;
            } catch (\Throwable $e) {
                return null;
            }
        }

        return null;
    }

    public function update(Request $request, Department $department)
    {
        $validated = $request->validate([
            'name'   => 'required|string|max:255',
            'code'   => 'nullable|string|max:50',
            'status' => 'required|in:active,inactive',
        ]);

        $before = $department->toArray();
        $department->update($validated);
        $this->auditLog->log('department.updated', $department, $before, $department->toArray(), $department->company_id);

        return redirect()->route('erp.departments.index')->with('success', 'Department updated successfully.');
    }

    public function destroy(Department $department)
    {
        $before = $department->toArray();
        $department->delete();
        $this->auditLog->log('department.deleted', null, $before, null, $department->company_id);

        return redirect()->route('erp.departments.index')->with('success', 'Department deleted successfully.');
    }
}

