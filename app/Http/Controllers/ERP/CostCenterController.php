<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\Branch;
use App\Models\ERP\Company;
use App\Models\ERP\CostCenter;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use Illuminate\Http\Request;

class CostCenterController extends Controller
{
    public function __construct(
        protected CompanyBranchContext $context,
        protected AuditLogService $auditLog
    ) {
    }

    public function index()
    {
        $currentCompany = $this->resolveCompany();
        $costCenters = CostCenter::where('company_id', $currentCompany?->id)
            ->with(['branch', 'parent'])
            ->latest()
            ->paginate(15);
        $branches = Branch::where('company_id', $currentCompany?->id)->where('status', 'active')->get();
        $parents = CostCenter::where('company_id', $currentCompany?->id)->where('status', 'active')->get();

        return view('erp.organization.cost_centers.index', compact('costCenters', 'branches', 'parents'));
    }

    public function store(Request $request)
    {
        $currentCompany = $this->resolveCompany();
        $validated = $request->validate([
            'code'       => 'required|string|max:50',
            'name'       => 'required|string|max:255',
            'branch_id'  => 'nullable|exists:branches,id',
            'parent_id'  => 'nullable|exists:cost_centers,id',
            'status'     => 'required|in:active,inactive',
            'company_id' => 'nullable|exists:companies,id',
        ]);

        $companyId = $validated['company_id'] ?? $currentCompany?->id;
        if (!$companyId) {
            $company = $this->resolveCompany(true);
            $companyId = $company->id;
        }

        $validated['company_id'] = $companyId;
        $costCenter = CostCenter::create($validated);
        $this->auditLog->log('cost_center.created', $costCenter, null, $costCenter->toArray(), $companyId, $costCenter->branch_id);

        return redirect()->route('erp.cost-centers.index')->with('success', 'Cost Center created successfully.');
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

    public function update(Request $request, CostCenter $costCenter)
    {
        $validated = $request->validate([
            'code'      => 'required|string|max:50',
            'name'      => 'required|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'parent_id' => 'nullable|exists:cost_centers,id',
            'status'    => 'required|in:active,inactive',
        ]);

        $before = $costCenter->toArray();
        $costCenter->update($validated);
        $this->auditLog->log('cost_center.updated', $costCenter, $before, $costCenter->toArray(), $costCenter->company_id, $costCenter->branch_id);

        return redirect()->route('erp.cost-centers.index')->with('success', 'Cost Center updated successfully.');
    }

    public function destroy(CostCenter $costCenter)
    {
        $before = $costCenter->toArray();
        $costCenter->delete();
        $this->auditLog->log('cost_center.deleted', null, $before, null, $costCenter->company_id, $costCenter->branch_id);

        return redirect()->route('erp.cost-centers.index')->with('success', 'Cost Center deleted successfully.');
    }
}

