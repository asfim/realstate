<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\Branch;
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
        $currentCompany = $this->context->currentCompany();
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
        $currentCompany = $this->context->currentCompany();
        $validated = $request->validate([
            'code'      => 'required|string|max:50',
            'name'      => 'required|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'parent_id' => 'nullable|exists:cost_centers,id',
            'status'    => 'required|in:active,inactive',
        ]);

        $validated['company_id'] = $currentCompany->id;
        $costCenter = CostCenter::create($validated);
        $this->auditLog->log('cost_center.created', $costCenter, null, $costCenter->toArray(), $currentCompany->id, $costCenter->branch_id);

        return redirect()->route('erp.cost-centers.index')->with('success', 'Cost Center created successfully.');
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
