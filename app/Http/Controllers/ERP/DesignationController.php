<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\Company;
use App\Models\ERP\Department;
use App\Models\ERP\Designation;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use Illuminate\Http\Request;

class DesignationController extends Controller
{
    public function __construct(
        protected CompanyBranchContext $context,
        protected AuditLogService $auditLog
    ) {
    }

    public function index()
    {
        $currentCompany = $this->resolveCompany();
        $designations = Designation::where('company_id', $currentCompany?->id)
            ->with('department')
            ->latest()
            ->paginate(20);
        $departments = Department::where('company_id', $currentCompany?->id)->where('status', 'active')->get();

        return view('erp.organization.designations.index', compact('designations', 'departments'));
    }

    public function store(Request $request)
    {
        $currentCompany = $this->resolveCompany();
        $validated = $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'name'          => 'required|string|max:255',
            'grade'         => 'nullable|string|max:50',
            'company_id'    => 'nullable|exists:companies,id',
        ]);

        $companyId = $validated['company_id'] ?? $currentCompany?->id;
        if (!$companyId) {
            $company = $this->resolveCompany(true);
            $companyId = $company->id;
        }

        $validated['company_id'] = $companyId;
        $designation = Designation::create($validated);
        $this->auditLog->log('designation.created', $designation, null, $designation->toArray(), $companyId);

        return redirect()->route('erp.designations.index')->with('success', 'Designation created successfully.');
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

    public function update(Request $request, Designation $designation)
    {
        $validated = $request->validate([
            'department_id' => 'nullable|exists:departments,id',
            'name'          => 'required|string|max:255',
            'grade'         => 'nullable|string|max:50',
        ]);

        $before = $designation->toArray();
        $designation->update($validated);
        $this->auditLog->log('designation.updated', $designation, $before, $designation->toArray(), $designation->company_id);

        return redirect()->route('erp.designations.index')->with('success', 'Designation updated successfully.');
    }

    public function destroy(Designation $designation)
    {
        $before = $designation->toArray();
        $designation->delete();
        $this->auditLog->log('designation.deleted', null, $before, null, $designation->company_id);

        return redirect()->route('erp.designations.index')->with('success', 'Designation deleted successfully.');
    }
}

