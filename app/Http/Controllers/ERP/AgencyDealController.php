<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ERP\AgencyDeal;
use App\Models\ERP\AgencyListing;
use App\Models\ERP\CrmCustomer;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use App\Services\ERP\DocumentSequenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgencyDealController extends Controller
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
        $query = AgencyDeal::where('company_id', $currentCompany?->id)
            ->with(['listing', 'customer', 'agent'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('deal_type')) {
            $query->where('deal_type', $request->deal_type);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('deal_code', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($cq) use ($search) {
                        $cq->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('listing', function ($lq) use ($search) {
                        $lq->where('title', 'like', "%{$search}%");
                    });
            });
        }

        $deals = $query->paginate(15)->withQueryString();

        return view('erp.agency.deals.index', compact('deals'));
    }

    public function create(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $listings = AgencyListing::where('company_id', $currentCompany?->id)
            ->whereIn('status', ['active', 'negotiating'])
            ->get();
        $customers = CrmCustomer::where('company_id', $currentCompany?->id)->where('status', 'active')->get();
        $agents = Admin::all();

        $selectedListing = null;
        if ($request->filled('listing_id')) {
            $selectedListing = AgencyListing::find($request->listing_id);
        }

        return view('erp.agency.deals.create', compact('listings', 'customers', 'agents', 'selectedListing'));
    }

    public function store(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $currentBranch = $this->context->currentBranch();

        $validated = $request->validate([
            'listing_id'                         => 'required|exists:agency_listings,id',
            'customer_id'                        => 'required|exists:crm_customers,id',
            'agent_id'                           => 'nullable|exists:admins,id',
            'deal_type'                          => 'required|string',
            'closing_price'                      => 'required|numeric|min:1',
            'company_commission_amount'          => 'required|numeric|min:0',
            'agent_commission_percent'           => 'nullable|numeric|min:0|max:100',
            'external_broker_name'               => 'nullable|string|max:255',
            'external_broker_phone'              => 'nullable|string|max:50',
            'external_broker_commission_amount'  => 'nullable|numeric|min:0',
            'deal_date'                          => 'required|date',
            'notes'                              => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $currentCompany, $currentBranch) {
            $listing = AgencyListing::lockForUpdate()->findOrFail($validated['listing_id']);

            $agentPercent = $validated['agent_commission_percent'] ?? 0;
            $agentCommissionAmount = ($validated['company_commission_amount'] * $agentPercent) / 100;

            $dealCode = $this->sequenceService->getNextNumber('agency_deal', $currentCompany->id, $listing->branch_id ?? $currentBranch?->id);

            $deal = AgencyDeal::create([
                'company_id'                        => $currentCompany->id,
                'branch_id'                         => $listing->branch_id ?? $currentBranch?->id,
                'deal_code'                         => $dealCode,
                'listing_id'                        => $listing->id,
                'customer_id'                       => $validated['customer_id'],
                'agent_id'                          => $validated['agent_id'] ?? auth('admin')->id(),
                'deal_type'                         => $validated['deal_type'],
                'closing_price'                     => $validated['closing_price'],
                'company_commission_amount'         => $validated['company_commission_amount'],
                'agent_commission_percent'          => $agentPercent,
                'agent_commission_amount'           => $agentCommissionAmount,
                'external_broker_name'              => $validated['external_broker_name'] ?? null,
                'external_broker_phone'             => $validated['external_broker_phone'] ?? null,
                'external_broker_commission_amount' => $validated['external_broker_commission_amount'] ?? 0,
                'deal_date'                         => $validated['deal_date'],
                'status'                            => 'pending_settlement',
                'notes'                             => $validated['notes'] ?? null,
                'created_by'                        => auth('admin')->id(),
            ]);

            // Update listing status
            $listing->update(['status' => 'closed_deal']);

            $this->auditLog->log('agency.deal_closed', $deal, null, $deal->toArray(), $currentCompany->id, $deal->branch_id);

            return redirect()->route('erp.agency-deals.show', $deal)->with('success', "Deal {$deal->deal_code} created successfully.");
        });
    }

    public function show(AgencyDeal $agencyDeal)
    {
        $agencyDeal->load(['listing', 'customer', 'agent']);
        return view('erp.agency.deals.show', compact('agencyDeal'));
    }

    public function settle(AgencyDeal $agencyDeal)
    {
        $agencyDeal->update(['status' => 'settled']);
        $this->auditLog->log('agency.deal_settled', $agencyDeal, null, $agencyDeal->toArray(), $agencyDeal->company_id, $agencyDeal->branch_id);

        return back()->with('success', "Deal {$agencyDeal->deal_code} marked as settled.");
    }

    public function edit(AgencyDeal $agencyDeal)
    {
        $currentCompany = $this->context->currentCompany();
        $customers = CrmCustomer::where('company_id', $currentCompany?->id)->where('status', 'active')->get();
        $agents = Admin::all();

        return view('erp.agency.deals.edit', compact('agencyDeal', 'customers', 'agents'));
    }

    public function update(Request $request, AgencyDeal $agencyDeal)
    {
        $validated = $request->validate([
            'closing_price'                     => 'required|numeric|min:1',
            'company_commission_amount'         => 'required|numeric|min:0',
            'agent_commission_percent'          => 'nullable|numeric|min:0|max:100',
            'external_broker_name'              => 'nullable|string|max:255',
            'external_broker_phone'             => 'nullable|string|max:50',
            'external_broker_commission_amount' => 'nullable|numeric|min:0',
            'deal_date'                         => 'required|date',
            'status'                            => 'required|string',
            'notes'                             => 'nullable|string',
        ]);

        $agentPercent = $validated['agent_commission_percent'] ?? 0;
        $validated['agent_commission_amount'] = ($validated['company_commission_amount'] * $agentPercent) / 100;

        $old = $agencyDeal->toArray();
        $agencyDeal->update($validated);
        $this->auditLog->log('agency.deal_updated', $agencyDeal, $old, $agencyDeal->toArray(), $agencyDeal->company_id, $agencyDeal->branch_id);

        return redirect()->route('erp.agency-deals.show', $agencyDeal)->with('success', 'Deal updated successfully.');
    }

    public function destroy(AgencyDeal $agencyDeal)
    {
        if ($agencyDeal->status === 'settled') {
            return back()->with('error', 'Cannot delete a settled deal.');
        }

        $agencyDeal->listing->update(['status' => 'active']);
        $old = $agencyDeal->toArray();
        $agencyDeal->delete();
        $this->auditLog->log('agency.deal_deleted', null, $old, null, $agencyDeal->company_id, $agencyDeal->branch_id);

        return redirect()->route('erp.agency-deals.index')->with('success', 'Deal deleted and listing restored to active.');
    }
}

