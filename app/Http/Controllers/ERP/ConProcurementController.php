<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\ConGrn;
use App\Models\ERP\ConMaterialRequisition;
use App\Models\ERP\ConPoItem;
use App\Models\ERP\ConPurchaseOrder;
use App\Models\ERP\ConSiteStock;
use App\Models\ERP\ConVendor;
use App\Models\ERP\DevProject;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use App\Services\ERP\DocumentSequenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConProcurementController extends Controller
{
    public function __construct(
        protected CompanyBranchContext $context,
        protected DocumentSequenceService $sequenceService,
        protected AuditLogService $auditLog
    ) {
    }

    // ── Purchase Orders ─────────────────────────────────────────

    public function posIndex(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $query = ConPurchaseOrder::where('company_id', $currentCompany?->id)
            ->with(['project', 'vendor', 'creator'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('po_code', 'like', "%{$search}%")
                    ->orWhereHas('vendor', function ($vq) use ($search) {
                        $vq->where('name', 'like', "%{$search}%");
                    });
            });
        }

        $pos = $query->paginate(15)->withQueryString();
        $projects = DevProject::where('company_id', $currentCompany?->id)->get();

        return view('erp.construction.pos.index', compact('pos', 'projects'));
    }

    public function poCreate(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $projects = DevProject::where('company_id', $currentCompany?->id)->get();
        $vendors = ConVendor::where('company_id', $currentCompany?->id)->where('status', 'active')->get();
        $approvedRequisitions = ConMaterialRequisition::where('company_id', $currentCompany?->id)
            ->where('status', 'approved')
            ->with('items')
            ->get();

        $selectedReq = null;
        if ($request->filled('requisition_id')) {
            $selectedReq = ConMaterialRequisition::with('items')->find($request->requisition_id);
        }

        return view('erp.construction.pos.create', compact('projects', 'vendors', 'approvedRequisitions', 'selectedReq'));
    }

    public function poStore(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $currentBranch = $this->context->currentBranch();

        $validated = $request->validate([
            'project_id'          => 'required|exists:dev_projects,id',
            'vendor_id'           => 'required|exists:con_vendors,id',
            'requisition_id'      => 'nullable|exists:con_material_requisitions,id',
            'po_date'             => 'required|date',
            'delivery_due_date'   => 'nullable|date',
            'tax_vat_amount'      => 'nullable|numeric|min:0',
            'payment_terms'       => 'nullable|string',
            'notes'               => 'nullable|string',
            'items'               => 'required|array|min:1',
            'items.*.item_name'   => 'required|string|max:255',
            'items.*.unit_of_measure' => 'required|string|max:30',
            'items.*.quantity'    => 'required|numeric|min:0.01',
            'items.*.unit_rate'   => 'required|numeric|min:0.01',
        ]);

        return DB::transaction(function () use ($validated, $currentCompany, $currentBranch) {
            $subtotal = 0;
            foreach ($validated['items'] as $item) {
                $subtotal += ($item['quantity'] * $item['unit_rate']);
            }

            $vat = $validated['tax_vat_amount'] ?? 0;
            $grandTotal = $subtotal + $vat;

            $poCode = $this->sequenceService->getNextNumber('po', $currentCompany->id, $currentBranch?->id);

            $po = ConPurchaseOrder::create([
                'company_id'        => $currentCompany->id,
                'branch_id'         => $currentBranch?->id,
                'project_id'        => $validated['project_id'],
                'vendor_id'         => $validated['vendor_id'],
                'requisition_id'    => $validated['requisition_id'] ?? null,
                'po_code'           => $poCode,
                'po_date'           => $validated['po_date'],
                'delivery_due_date' => $validated['delivery_due_date'] ?? null,
                'subtotal'          => $subtotal,
                'tax_vat_amount'    => $vat,
                'grand_total'       => $grandTotal,
                'payment_terms'     => $validated['payment_terms'] ?? null,
                'status'            => 'issued',
                'notes'             => $validated['notes'] ?? null,
                'created_by'        => auth('admin')->id(),
            ]);

            foreach ($validated['items'] as $item) {
                ConPoItem::create([
                    'purchase_order_id' => $po->id,
                    'item_name'         => $item['item_name'],
                    'unit_of_measure'   => $item['unit_of_measure'],
                    'quantity'          => $item['quantity'],
                    'unit_rate'         => $item['unit_rate'],
                    'line_total'        => $item['quantity'] * $item['unit_rate'],
                    'received_qty'      => 0,
                ]);
            }

            if (!empty($validated['requisition_id'])) {
                ConMaterialRequisition::where('id', $validated['requisition_id'])->update(['status' => 'po_issued']);
            }

            $this->auditLog->log('procurement.po_issued', $po, null, $po->toArray(), $currentCompany->id);

            return redirect()->route('erp.con-pos.show', $po)->with('success', "Purchase Order {$po->po_code} generated.");
        });
    }

    public function poShow(ConPurchaseOrder $conPo)
    {
        $conPo->load(['project', 'vendor', 'requisition', 'items', 'grns']);
        return view('erp.construction.pos.show', compact('conPo'));
    }

    // ── Goods Receipt Notes (GRN) & Stock Updates ────────────────

    public function grnsIndex(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $query = ConGrn::where('company_id', $currentCompany?->id)
            ->with(['project', 'vendor', 'purchaseOrder', 'receivedByAdmin'])
            ->latest();

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        $grns = $query->paginate(15)->withQueryString();
        $projects = DevProject::where('company_id', $currentCompany?->id)->get();

        return view('erp.construction.grns.index', compact('grns', 'projects'));
    }

    public function grnCreate(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $pos = ConPurchaseOrder::where('company_id', $currentCompany?->id)
            ->whereIn('status', ['issued', 'partial_received'])
            ->with(['vendor', 'project', 'items'])
            ->get();

        $selectedPo = null;
        if ($request->filled('po_id')) {
            $selectedPo = ConPurchaseOrder::with(['vendor', 'project', 'items'])->find($request->po_id);
        }

        return view('erp.construction.grns.create', compact('pos', 'selectedPo'));
    }

    public function grnStore(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $currentBranch = $this->context->currentBranch();

        $validated = $request->validate([
            'purchase_order_id' => 'required|exists:con_purchase_orders,id',
            'received_date'     => 'required|date',
            'challan_no'        => 'required|string|max:100',
            'vehicle_number'    => 'nullable|string|max:50',
            'remarks'           => 'nullable|string',
            'items'             => 'required|array|min:1',
            'items.*.po_item_id'=> 'required|exists:con_po_items,id',
            'items.*.received_qty' => 'required|numeric|min:0.01',
        ]);

        return DB::transaction(function () use ($validated, $currentCompany, $currentBranch) {
            $po = ConPurchaseOrder::with('items')->lockForUpdate()->findOrFail($validated['purchase_order_id']);

            $grnCode = $this->sequenceService->getNextNumber('grn', $currentCompany->id, $currentBranch?->id);

            $grn = ConGrn::create([
                'company_id'        => $currentCompany->id,
                'branch_id'         => $currentBranch?->id,
                'project_id'        => $po->project_id,
                'purchase_order_id' => $po->id,
                'vendor_id'         => $po->vendor_id,
                'grn_code'          => $grnCode,
                'received_date'     => $validated['received_date'],
                'challan_no'        => $validated['challan_no'],
                'vehicle_number'    => $validated['vehicle_number'] ?? null,
                'received_by'       => auth('admin')->id(),
                'status'            => 'verified',
                'remarks'           => $validated['remarks'] ?? null,
            ]);

            // Update received quantities on PO items and add to site stocks
            $allCompleted = true;
            foreach ($validated['items'] as $itemData) {
                $poItem = ConPoItem::findOrFail($itemData['po_item_id']);
                $newReceived = $poItem->received_qty + $itemData['received_qty'];
                $poItem->update(['received_qty' => $newReceived]);

                if ($newReceived < $poItem->quantity) {
                    $allCompleted = false;
                }

                // Update Site Stock
                $stock = ConSiteStock::firstOrCreate(
                    [
                        'company_id'    => $currentCompany->id,
                        'project_id'    => $po->project_id,
                        'material_name' => $poItem->item_name,
                    ],
                    [
                        'unit_of_measure'   => $poItem->unit_of_measure,
                        'current_stock_qty' => 0,
                        'total_received_qty'=> 0,
                        'total_consumed_qty'=> 0,
                    ]
                );

                $stock->update([
                    'current_stock_qty'  => $stock->current_stock_qty + $itemData['received_qty'],
                    'total_received_qty' => $stock->total_received_qty + $itemData['received_qty'],
                    'last_received_date' => $validated['received_date'],
                ]);
            }

            $po->update(['status' => $allCompleted ? 'completed' : 'partial_received']);

            $this->auditLog->log('procurement.grn_received', $grn, null, $grn->toArray(), $currentCompany->id);

            return redirect()->route('erp.con-grns.show', $grn)->with('success', "GRN {$grn->grn_code} processed. Site inventory stock updated.");
        });
    }

    public function grnShow(ConGrn $conGrn)
    {
        $conGrn->load(['project', 'vendor', 'purchaseOrder.items', 'receivedByAdmin']);
        return view('erp.construction.grns.show', compact('conGrn'));
    }

    // ── Site Stock Inventory Overview ────────────────────────────

    public function stocksIndex(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $projects = DevProject::where('company_id', $currentCompany?->id)->get();

        $query = ConSiteStock::where('company_id', $currentCompany?->id)->with('project');

        if ($request->filled('project_id')) {
            $query->where('project_id', $request->project_id);
        }

        if ($request->filled('search')) {
            $query->where('material_name', 'like', "%{$request->search}%");
        }

        $stocks = $query->paginate(20)->withQueryString();

        return view('erp.construction.stocks.index', compact('stocks', 'projects'));
    }
}

