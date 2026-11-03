<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\ConMaterialRequisition;
use App\Models\ERP\ConRequisitionItem;
use App\Models\ERP\DevProject;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use App\Services\ERP\DocumentSequenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConRequisitionController extends Controller
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
        $query = ConMaterialRequisition::where('company_id', $currentCompany?->id)
            ->with(['project', 'requestedByAdmin', 'items'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $requisitions = $query->paginate(15)->withQueryString();
        $projects = DevProject::where('company_id', $currentCompany?->id)->get();

        return view('erp.construction.requisitions.index', compact('requisitions', 'projects'));
    }

    public function create()
    {
        $currentCompany = $this->context->currentCompany();
        $projects = DevProject::where('company_id', $currentCompany?->id)->get();

        return view('erp.construction.requisitions.create', compact('projects'));
    }

    public function store(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $currentBranch = $this->context->currentBranch();

        $validated = $request->validate([
            'project_id'           => 'required|exists:dev_projects,id',
            'required_date'        => 'required|date',
            'urgency'              => 'required|string',
            'site_location_notes'  => 'nullable|string',
            'items'                => 'required|array|min:1',
            'items.*.material_name'=> 'required|string|max:255',
            'items.*.unit_of_measure' => 'required|string|max:30',
            'items.*.requested_qty'   => 'required|numeric|min:0.01',
            'items.*.remarks'         => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $currentCompany, $currentBranch) {
            $reqCode = $this->sequenceService->getNextNumber('requisition', $currentCompany->id, $currentBranch?->id);

            $requisition = ConMaterialRequisition::create([
                'company_id'          => $currentCompany->id,
                'branch_id'           => $currentBranch?->id,
                'project_id'          => $validated['project_id'],
                'requisition_code'    => $reqCode,
                'requested_by'        => auth('admin')->id(),
                'required_date'       => $validated['required_date'],
                'urgency'             => $validated['urgency'],
                'status'              => 'submitted',
                'site_location_notes' => $validated['site_location_notes'] ?? null,
            ]);

            foreach ($validated['items'] as $item) {
                ConRequisitionItem::create([
                    'requisition_id'  => $requisition->id,
                    'material_name'   => $item['material_name'],
                    'unit_of_measure' => $item['unit_of_measure'],
                    'requested_qty'   => $item['requested_qty'],
                    'remarks'         => $item['remarks'] ?? null,
                ]);
            }

            $this->auditLog->log('procurement.requisition_submitted', $requisition, null, $requisition->toArray(), $currentCompany->id);

            return redirect()->route('erp.con-requisitions.show', $requisition)->with('success', "Material Requisition {$requisition->requisition_code} submitted.");
        });
    }

    public function show(ConMaterialRequisition $conRequisition)
    {
        $conRequisition->load(['project', 'requestedByAdmin', 'approvedByAdmin', 'items']);
        return view('erp.construction.requisitions.show', compact('conRequisition'));
    }

    public function approve(Request $request, ConMaterialRequisition $conRequisition)
    {
        $conRequisition->update([
            'status'      => 'approved',
            'approved_by' => auth('admin')->id(),
            'approved_at' => now(),
        ]);

        $this->auditLog->log('procurement.requisition_approved', $conRequisition, null, $conRequisition->toArray(), $conRequisition->company_id);

        return back()->with('success', "Requisition {$conRequisition->requisition_code} approved for PO generation.");
    }
}
