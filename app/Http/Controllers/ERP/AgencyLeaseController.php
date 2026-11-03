<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\ERP\AgencyListing;
use App\Models\ERP\AgencyTenantLease;
use App\Models\ERP\CrmCustomer;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use App\Services\ERP\DocumentSequenceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AgencyLeaseController extends Controller
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
        $query = AgencyTenantLease::where('company_id', $currentCompany?->id)
            ->with(['listing', 'tenant'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('lease_code', 'like', "%{$search}%")
                    ->orWhereHas('tenant', function ($tq) use ($search) {
                        $tq->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%");
                    })
                    ->orWhereHas('listing', function ($lq) use ($search) {
                        $lq->where('title', 'like', "%{$search}%");
                    });
            });
        }

        $leases = $query->paginate(15)->withQueryString();

        return view('erp.agency.leases.index', compact('leases'));
    }

    public function create(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $rentalListings = AgencyListing::where('company_id', $currentCompany?->id)
            ->whereIn('listing_type', ['rental', 'commercial_lease'])
            ->where('status', 'active')
            ->get();
        $customers = CrmCustomer::where('company_id', $currentCompany?->id)->where('status', 'active')->get();

        $selectedListing = null;
        if ($request->filled('listing_id')) {
            $selectedListing = AgencyListing::find($request->listing_id);
        }

        return view('erp.agency.leases.create', compact('rentalListings', 'customers', 'selectedListing'));
    }

    public function store(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $currentBranch = $this->context->currentBranch();

        $validated = $request->validate([
            'listing_id'              => 'required|exists:agency_listings,id',
            'tenant_id'               => 'required|exists:crm_customers,id',
            'monthly_rent'            => 'required|numeric|min:1',
            'security_deposit_amount' => 'required|numeric|min:0',
            'monthly_service_charge'  => 'nullable|numeric|min:0',
            'lease_start_date'        => 'required|date',
            'lease_end_date'          => 'required|date|after:lease_start_date',
            'rent_due_day'            => 'required|integer|min:1|max:28',
            'key_handover_date'       => 'nullable|date',
            'special_terms'           => 'nullable|string',
        ]);

        return DB::transaction(function () use ($validated, $currentCompany, $currentBranch) {
            $listing = AgencyListing::lockForUpdate()->findOrFail($validated['listing_id']);

            $leaseCode = $this->sequenceService->getNextNumber('tenant_lease', $currentCompany->id, $listing->branch_id ?? $currentBranch?->id);

            $lease = AgencyTenantLease::create([
                'company_id'              => $currentCompany->id,
                'branch_id'               => $listing->branch_id ?? $currentBranch?->id,
                'lease_code'              => $leaseCode,
                'listing_id'              => $listing->id,
                'tenant_id'               => $validated['tenant_id'],
                'monthly_rent'            => $validated['monthly_rent'],
                'security_deposit_amount' => $validated['security_deposit_amount'],
                'monthly_service_charge'  => $validated['monthly_service_charge'] ?? 0,
                'lease_start_date'        => $validated['lease_start_date'],
                'lease_end_date'          => $validated['lease_end_date'],
                'rent_due_day'            => $validated['rent_due_day'],
                'status'                  => 'active',
                'key_handover_date'       => $validated['key_handover_date'] ?? null,
                'special_terms'           => $validated['special_terms'] ?? null,
                'created_by'              => auth('admin')->id(),
            ]);

            // Update listing status
            $listing->update(['status' => 'closed_deal']);

            $this->auditLog->log('agency.lease_executed', $lease, null, $lease->toArray(), $currentCompany->id, $lease->branch_id);

            return redirect()->route('erp.agency-leases.show', $lease)->with('success', "Lease Contract {$lease->lease_code} registered.");
        });
    }

    public function show(AgencyTenantLease $agencyLease)
    {
        $agencyLease->load(['listing', 'tenant']);
        return view('erp.agency.leases.show', compact('agencyLease'));
    }

    public function terminate(Request $request, AgencyTenantLease $agencyLease)
    {
        $agencyLease->update(['status' => 'terminated']);
        $agencyLease->listing->update(['status' => 'active']);

        $this->auditLog->log('agency.lease_terminated', $agencyLease, null, $agencyLease->toArray(), $agencyLease->company_id, $agencyLease->branch_id);

        return back()->with('success', "Lease {$agencyLease->lease_code} terminated and listing restored to active.");
    }
}

