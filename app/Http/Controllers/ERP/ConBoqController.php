<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\ConBoqItem;
use App\Models\ERP\DevProject;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use Illuminate\Http\Request;

class ConBoqController extends Controller
{
    public function __construct(
        protected CompanyBranchContext $context,
        protected AuditLogService $auditLog
    ) {
    }

    public function index(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $projects = DevProject::where('company_id', $currentCompany?->id)->get();

        $selectedProjectId = $request->project_id ?? $projects->first()?->id;
        $selectedProject = $selectedProjectId ? DevProject::find($selectedProjectId) : null;

        $boqItems = collect();
        if ($selectedProject) {
            $boqItems = ConBoqItem::where('project_id', $selectedProject->id)->latest()->get();
        }

        $totalEstimatedBudget = $boqItems->sum('total_estimated_cost');
        $totalActualCost = $boqItems->sum('actual_total_cost');

        return view('erp.construction.boq.index', compact('projects', 'selectedProject', 'boqItems', 'totalEstimatedBudget', 'totalActualCost'));
    }

    public function store(Request $request)
    {
        $currentCompany = $this->context->currentCompany();

        $validated = $request->validate([
            'project_id'         => 'required|exists:dev_projects,id',
            'category'           => 'required|string',
            'item_name'          => 'required|string|max:255',
            'unit_of_measure'    => 'required|string|max:30',
            'estimated_quantity' => 'required|numeric|min:0.01',
            'estimated_unit_rate'=> 'required|numeric|min:0.01',
            'specifications'     => 'nullable|string',
        ]);

        $validated['company_id'] = $currentCompany->id;
        $validated['total_estimated_cost'] = $validated['estimated_quantity'] * $validated['estimated_unit_rate'];

        $item = ConBoqItem::create($validated);
        $this->auditLog->log('con.boq_item_added', $item, null, $item->toArray(), $currentCompany->id);

        return back()->with('success', "BOQ Item '{$item->item_name}' added to project budget.");
    }

    public function updateConsumption(Request $request, ConBoqItem $boqItem)
    {
        $validated = $request->validate([
            'actual_consumed_qty' => 'required|numeric|min:0',
            'actual_total_cost'   => 'required|numeric|min:0',
        ]);

        $old = $boqItem->toArray();
        $boqItem->update($validated);
        $this->auditLog->log('con.boq_consumption_updated', $boqItem, $old, $boqItem->toArray(), $boqItem->company_id);

        return back()->with('success', 'Actual consumption updated.');
    }
}

