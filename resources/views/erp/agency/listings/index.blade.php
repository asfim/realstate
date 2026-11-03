@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Agency & Brokerage Listings</h4>
        <p class="text-muted small mb-0">Third-party resales, rental listings, landlord mandates, commercial leases, and brokerage inventory.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.agency-deals.create') }}" class="btn btn-outline-primary btn-sm px-3">
            <i class="bi bi-handshake me-1"></i> Close Brokerage Deal
        </a>
        <a href="{{ route('erp.agency-leases.create') }}" class="btn btn-outline-success btn-sm px-3">
            <i class="bi bi-key-fill me-1"></i> New Tenant Lease
        </a>
        <a href="{{ route('erp.agency-listings.create') }}" class="btn btn-erp-gold btn-sm px-3">
            <i class="bi bi-plus-circle-fill me-1"></i> New Mandate Listing
        </a>
    </div>
</div>

<!-- Status Filters Ribbon -->
<div class="d-flex flex-wrap gap-2 mb-4 pb-2 border-bottom">
    <a href="{{ route('erp.agency-listings.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-light border' }} rounded-pill px-3">
        All Listings <span class="badge {{ !request('status') ? 'bg-secondary' : 'bg-light text-dark border' }} ms-1">{{ array_sum($statusCounts) }}</span>
    </a>
    @php
        $statuses = [
            'active'      => ['label' => 'Active Market', 'color' => 'success'],
            'negotiating' => ['label' => 'Negotiating', 'color' => 'warning'],
            'closed_deal' => ['label' => 'Closed Deal', 'color' => 'primary'],
            'scouting'    => ['label' => 'Scouting / Lead', 'color' => 'secondary'],
            'expired'     => ['label' => 'Expired Mandate', 'color' => 'danger'],
            'withdrawn'   => ['label' => 'Withdrawn', 'color' => 'dark'],
        ];
    @endphp
    @foreach($statuses as $key => $meta)
        <a href="{{ route('erp.agency-listings.index', array_merge(request()->query(), ['status' => $key])) }}" 
           class="btn btn-sm {{ request('status') === $key ? 'btn-' . $meta['color'] : 'btn-light border' }} rounded-pill px-3">
            {{ $meta['label'] }}
            <span class="badge {{ request('status') === $key ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">
                {{ $statusCounts[$key] ?? 0 }}
            </span>
        </a>
    @endforeach
</div>

<!-- Search & Filters -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('erp.agency-listings.index') }}" class="row g-2 align-items-center">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search by title, listing code, location, landlord name, phone..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-2">
            <select name="listing_type" class="form-select form-select-sm">
                <option value="">-- All Types --</option>
                <option value="resale" {{ request('listing_type') === 'resale' ? 'selected' : '' }}>Resale / Outright</option>
                <option value="rental" {{ request('listing_type') === 'rental' ? 'selected' : '' }}>Residential Rental</option>
                <option value="commercial_lease" {{ request('listing_type') === 'commercial_lease' ? 'selected' : '' }}>Commercial Lease</option>
                <option value="land_sale" {{ request('listing_type') === 'land_sale' ? 'selected' : '' }}>Land / Plot Sale</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="property_category" class="form-select form-select-sm">
                <option value="">-- Category --</option>
                <option value="apartment" {{ request('property_category') === 'apartment' ? 'selected' : '' }}>Apartment</option>
                <option value="commercial" {{ request('property_category') === 'commercial' ? 'selected' : '' }}>Commercial Floor</option>
                <option value="house" {{ request('property_category') === 'house' ? 'selected' : '' }}>House / Villa</option>
                <option value="land" {{ request('property_category') === 'land' ? 'selected' : '' }}>Land Plot</option>
                <option value="warehouse" {{ request('property_category') === 'warehouse' ? 'selected' : '' }}>Warehouse</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm w-100">Filter</button>
            <a href="{{ route('erp.agency-listings.index') }}" class="btn btn-light border btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- Listings Table -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Code / Title</th>
                    <th>Type & Category</th>
                    <th>Location</th>
                    <th>Seller / Landlord</th>
                    <th>Asking Price</th>
                    <th>Exp. Commission</th>
                    <th>Mandate</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($listings as $item)
                    @php
                        $statusBadges = [
                            'active' => 'bg-success',
                            'negotiating' => 'bg-warning text-dark',
                            'closed_deal' => 'bg-primary',
                            'scouting' => 'bg-secondary',
                            'expired' => 'bg-danger',
                            'withdrawn' => 'bg-dark',
                        ];
                    @endphp
                    <tr>
                        <td class="ps-3">
                            <a href="{{ route('erp.agency-listings.show', $item) }}" class="fw-bold text-decoration-none text-dark">
                                {{ $item->title }}
                            </a>
                            <div class="text-muted extra-small">
                                <span class="badge bg-light text-dark border">{{ $item->listing_code }}</span>
                                @if($item->size_sqft) • {{ number_format($item->size_sqft) }} sft @endif
                                @if($item->bedrooms) • {{ $item->bedrooms }} Bed @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ ucfirst(str_replace('_', ' ', $item->listing_type)) }}</span>
                            <div class="text-muted extra-small mt-1">{{ ucfirst($item->property_category) }}</div>
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark">{{ $item->location }}</div>
                            @if($item->key_held_at_branch)
                                <span class="badge bg-info text-dark extra-small"><i class="bi bi-key-fill me-1"></i>Key in Office</span>
                            @endif
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $item->landlord_seller_name }}</div>
                            <div class="text-muted extra-small"><i class="bi bi-telephone me-1"></i>{{ $item->landlord_seller_phone }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">৳ {{ number_format($item->asking_price, 2) }}</div>
                            @if(in_array($item->listing_type, ['rental', 'commercial_lease']))
                                <div class="text-muted extra-small">/ month</div>
                            @endif
                        </td>
                        <td>
                            <div class="text-success fw-bold">৳ {{ number_format($item->expected_commission, 2) }}</div>
                            <div class="text-muted extra-small">{{ $item->commission_rate_percent }}% rate</div>
                        </td>
                        <td>
                            <div class="small text-capitalize">{{ str_replace('_', ' ', $item->mandate_type) }}</div>
                            @if($item->mandate_expiry_date)
                                <div class="text-muted extra-small">Exp: {{ \Carbon\Carbon::parse($item->mandate_expiry_date)->format('M Y') }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $statusBadges[$item->status] ?? 'bg-secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $item->status)) }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('erp.agency-listings.show', $item) }}" class="btn btn-outline-secondary btn-sm px-2 py-1" title="View Dossier">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-building-slash fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No brokerage listings found.
                            <div class="mt-2">
                                <a href="{{ route('erp.agency-listings.create') }}" class="btn btn-sm btn-erp-gold">Create First Listing Mandate</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($listings->hasPages())
        <div class="p-3 border-top">
            {{ $listings->links() }}
        </div>
    @endif
</div>
@endsection

