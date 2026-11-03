<?php

namespace App\Http\Controllers\ERP;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\ERP\AgencyListing;
use App\Services\ERP\AuditLogService;
use App\Services\ERP\CompanyBranchContext;
use App\Services\ERP\DocumentSequenceService;
use Illuminate\Http\Request;

class AgencyListingController extends Controller
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
        $query = AgencyListing::where('company_id', $currentCompany?->id)
            ->with(['branch', 'assignedAgent'])
            ->latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('listing_type')) {
            $query->where('listing_type', $request->listing_type);
        }

        if ($request->filled('property_category')) {
            $query->where('property_category', $request->property_category);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('listing_code', 'like', "%{$search}%")
                    ->orWhere('location', 'like', "%{$search}%")
                    ->orWhere('landlord_seller_name', 'like', "%{$search}%")
                    ->orWhere('landlord_seller_phone', 'like', "%{$search}%");
            });
        }

        $listings = $query->paginate(15)->withQueryString();

        $statusCounts = AgencyListing::where('company_id', $currentCompany?->id)
            ->selectRaw('status, count(*) as count')
            ->groupBy('status')
            ->pluck('count', 'status')
            ->toArray();

        return view('erp.agency.listings.index', compact('listings', 'statusCounts'));
    }

    public function create()
    {
        $currentCompany = $this->context->currentCompany();
        $branches = $this->context->accessibleBranches();
        $agents = Admin::all();

        return view('erp.agency.listings.create', compact('branches', 'agents'));
    }

    public function store(Request $request)
    {
        $currentCompany = $this->context->currentCompany();
        $currentBranch = $this->context->currentBranch();

        $validated = $request->validate([
            'title'                   => 'required|string|max:255',
            'listing_type'            => 'required|string',
            'property_category'       => 'required|string',
            'location'                => 'required|string|max:255',
            'address'                 => 'nullable|string',
            'size_sqft'               => 'nullable|numeric|min:1',
            'bedrooms'                => 'nullable|integer|min:0',
            'bathrooms'               => 'nullable|integer|min:0',
            'floor_number'            => 'nullable|integer',
            'landlord_seller_name'    => 'required|string|max:255',
            'landlord_seller_phone'   => 'required|string|max:50',
            'landlord_seller_email'   => 'nullable|email|max:255',
            'mandate_type'            => 'required|string',
            'mandate_expiry_date'     => 'nullable|date',
            'asking_price'            => 'required|numeric|min:1',
            'reserve_price'           => 'nullable|numeric|min:1',
            'commission_rate_percent' => 'required|numeric|min:0|max:100',
            'key_held_at_branch'      => 'nullable|boolean',
            'status'                  => 'required|string',
            'assigned_agent_id'       => 'nullable|exists:admins,id',
            'branch_id'               => 'nullable|exists:branches,id',
            'notes'                   => 'nullable|string',
        ]);

        $validated['company_id'] = $currentCompany->id;
        $validated['branch_id'] = $validated['branch_id'] ?? $currentBranch?->id;
        $validated['created_by'] = auth('admin')->id();
        $validated['key_held_at_branch'] = $request->has('key_held_at_branch');

        // Calculate expected commission
        $validated['expected_commission'] = ($validated['asking_price'] * $validated['commission_rate_percent']) / 100;

        $validated['listing_code'] = $this->sequenceService->getNextNumber('agency_listing', $currentCompany->id, $validated['branch_id']);

        $listing = AgencyListing::create($validated);
        $this->auditLog->log('agency.listing_created', $listing, null, $listing->toArray(), $currentCompany->id, $listing->branch_id);

        return redirect()->route('erp.agency-listings.show', $listing)->with('success', "Listing {$listing->listing_code} registered.");
    }

    public function show(AgencyListing $agencyListing)
    {
        $agencyListing->load(['branch', 'assignedAgent', 'deals.customer', 'deals.agent', 'leases.tenant']);
        return view('erp.agency.listings.show', compact('agencyListing'));
    }

    public function edit(AgencyListing $agencyListing)
    {
        $branches = $this->context->accessibleBranches();
        $agents = Admin::all();

        return view('erp.agency.listings.edit', compact('agencyListing', 'branches', 'agents'));
    }

    public function update(Request $request, AgencyListing $agencyListing)
    {
        $validated = $request->validate([
            'title'                   => 'required|string|max:255',
            'listing_type'            => 'required|string',
            'property_category'       => 'required|string',
            'location'                => 'required|string|max:255',
            'address'                 => 'nullable|string',
            'size_sqft'               => 'nullable|numeric|min:1',
            'bedrooms'                => 'nullable|integer|min:0',
            'bathrooms'               => 'nullable|integer|min:0',
            'floor_number'            => 'nullable|integer',
            'landlord_seller_name'    => 'required|string|max:255',
            'landlord_seller_phone'   => 'required|string|max:50',
            'landlord_seller_email'   => 'nullable|email|max:255',
            'mandate_type'            => 'required|string',
            'mandate_expiry_date'     => 'nullable|date',
            'asking_price'            => 'required|numeric|min:1',
            'reserve_price'           => 'nullable|numeric|min:1',
            'commission_rate_percent' => 'required|numeric|min:0|max:100',
            'status'                  => 'required|string',
            'assigned_agent_id'       => 'nullable|exists:admins,id',
            'branch_id'               => 'nullable|exists:branches,id',
            'notes'                   => 'nullable|string',
        ]);

        $validated['key_held_at_branch'] = $request->has('key_held_at_branch');
        $validated['expected_commission'] = ($validated['asking_price'] * $validated['commission_rate_percent']) / 100;

        $old = $agencyListing->toArray();
        $agencyListing->update($validated);
        $this->auditLog->log('agency.listing_updated', $agencyListing, $old, $agencyListing->toArray(), $agencyListing->company_id, $agencyListing->branch_id);

        return redirect()->route('erp.agency-listings.show', $agencyListing)->with('success', 'Listing updated successfully.');
    }

    public function destroy(AgencyListing $agencyListing)
    {
        if ($agencyListing->deals()->count() > 0 || $agencyListing->leases()->count() > 0) {
            return back()->with('error', 'Cannot delete an agency listing with associated deals or leases.');
        }

        $old = $agencyListing->toArray();
        $agencyListing->delete();
        $this->auditLog->log('agency.listing_deleted', null, $old, null, $agencyListing->company_id, $agencyListing->branch_id);

        return redirect()->route('erp.agency-listings.index')->with('success', 'Agency listing deleted successfully.');
    }
}

