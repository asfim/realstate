@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.agency-listings.index') }}" class="text-decoration-none">Agency Listings</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $agencyListing->listing_code }}</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <h4 class="fw-bold mb-0 text-dark">{{ $agencyListing->title }}</h4>
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
            <span class="badge {{ $statusBadges[$agencyListing->status] ?? 'bg-secondary' }} px-3 py-1">
                {{ strtoupper(str_replace('_', ' ', $agencyListing->status)) }}
            </span>
        </div>
        <p class="text-muted small mb-0">Code: <strong>{{ $agencyListing->listing_code }}</strong> | Type: {{ ucfirst(str_replace('_', ' ', $agencyListing->listing_type)) }} ({{ ucfirst($agencyListing->property_category) }})</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.agency-listings.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
        <a href="{{ route('erp.agency-listings.edit', $agencyListing) }}" class="btn btn-outline-dark btn-sm px-3">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        @if(in_array($agencyListing->listing_type, ['rental', 'commercial_lease']) && in_array($agencyListing->status, ['active', 'negotiating']))
            <a href="{{ route('erp.agency-leases.create', ['listing_id' => $agencyListing->id]) }}" class="btn btn-outline-success btn-sm px-3">
                <i class="bi bi-key-fill me-1"></i> Issue Lease Contract
            </a>
        @endif
        @if(in_array($agencyListing->status, ['active', 'negotiating']))
            <a href="{{ route('erp.agency-deals.create', ['listing_id' => $agencyListing->id]) }}" class="btn btn-erp-gold btn-sm px-3">
                <i class="bi bi-handshake me-1"></i> Close Brokerage Deal
            </a>
        @endif
    </div>
</div>

<!-- Overview Metric Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-primary h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Asking Price</div>
            <div class="fs-4 fw-bold text-dark mt-1">৳ {{ number_format($agencyListing->asking_price, 2) }}</div>
            <div class="text-muted extra-small mt-1">
                @if(in_array($agencyListing->listing_type, ['rental', 'commercial_lease']))
                    Per Month Rent
                @else
                    Lump Sum Sale
                @endif
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-success h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Expected Commission</div>
            <div class="fs-4 fw-bold text-success mt-1">৳ {{ number_format($agencyListing->expected_commission, 2) }}</div>
            <div class="text-success extra-small mt-1 fw-semibold">{{ $agencyListing->commission_rate_percent }}% Agreed Rate</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-warning h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Property Scale</div>
            <div class="fs-5 fw-bold text-dark mt-1">{{ number_format($agencyListing->size_sqft ?? 0) }} sft</div>
            <div class="text-muted extra-small mt-1">
                {{ $agencyListing->bedrooms ? $agencyListing->bedrooms . ' Bed' : '' }}
                {{ $agencyListing->bathrooms ? ' • ' . $agencyListing->bathrooms . ' Bath' : '' }}
                {{ $agencyListing->floor_number ? ' • Floor ' . $agencyListing->floor_number : '' }}
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-info h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Key Custody</div>
            <div class="fs-5 fw-bold text-dark mt-1">
                @if($agencyListing->key_held_at_branch)
                    <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>In Office</span>
                @else
                    <span class="text-muted">With Landlord</span>
                @endif
            </div>
            <div class="text-muted extra-small mt-1">Agent: {{ $agencyListing->assignedAgent->name ?? 'Unassigned' }}</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Owner / Landlord Details -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-person-circle text-erp-gold me-2"></i> Landlord / Property Owner Dossier
            </h6>
            <div class="row g-2 small">
                <div class="col-sm-4 text-muted">Owner Name:</div>
                <div class="col-sm-8 fw-bold text-dark">{{ $agencyListing->landlord_seller_name }}</div>

                <div class="col-sm-4 text-muted">Contact Phone:</div>
                <div class="col-sm-8 text-dark">{{ $agencyListing->landlord_seller_phone }}</div>

                <div class="col-sm-4 text-muted">Email Address:</div>
                <div class="col-sm-8 text-dark">{{ $agencyListing->landlord_seller_email ?? '—' }}</div>

                <div class="col-sm-4 text-muted">Representation:</div>
                <div class="col-sm-8 text-capitalize text-dark fw-semibold">{{ str_replace('_', ' ', $agencyListing->mandate_type) }} Mandate</div>

                <div class="col-sm-4 text-muted">Mandate Expiry:</div>
                <div class="col-sm-8 text-dark">{{ $agencyListing->mandate_expiry_date ? \Carbon\Carbon::parse($agencyListing->mandate_expiry_date)->format('F d, Y') : 'Open / Indefinite' }}</div>

                <div class="col-sm-4 text-muted">Reserve Floor:</div>
                <div class="col-sm-8 text-dark">
                    @if($agencyListing->reserve_price)
                        ৳ {{ number_format($agencyListing->reserve_price, 2) }}
                    @else
                        <span class="text-muted">Not specified</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Property Physical Dossier -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-geo-alt-fill text-erp-gold me-2"></i> Property Specifications & Location
            </h6>
            <div class="row g-2 small">
                <div class="col-sm-4 text-muted">Area / Neighborhood:</div>
                <div class="col-sm-8 fw-bold text-dark">{{ $agencyListing->location }}</div>

                <div class="col-sm-4 text-muted">Street Address:</div>
                <div class="col-sm-8 text-dark">{{ $agencyListing->address ?? '—' }}</div>

                <div class="col-sm-4 text-muted">Category:</div>
                <div class="col-sm-8 text-capitalize text-dark">{{ $agencyListing->property_category }}</div>

                <div class="col-sm-4 text-muted">Operating Branch:</div>
                <div class="col-sm-8 text-dark">{{ $agencyListing->branch->name ?? 'Headquarters' }}</div>

                <div class="col-sm-4 text-muted">Relationship Manager:</div>
                <div class="col-sm-8 text-dark">{{ $agencyListing->assignedAgent->name ?? 'Unassigned' }}</div>

                <div class="col-sm-4 text-muted">Viewing Notes:</div>
                <div class="col-sm-8 text-muted fst-italic">{{ $agencyListing->notes ?? 'None' }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Linked Closed Brokerage Deals -->
@if($agencyListing->deals->isNotEmpty())
    <div class="card card-custom mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-handshake-fill text-erp-gold me-2"></i> Closed Brokerage Deal Records
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th class="ps-3">Deal Code</th>
                        <th>Buyer / Tenant</th>
                        <th>Closing Price</th>
                        <th>Brokerage Commission</th>
                        <th>Agent Cut</th>
                        <th>Deal Date</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agencyListing->deals as $deal)
                        <tr>
                            <td class="ps-3 fw-bold">
                                <a href="{{ route('erp.agency-deals.show', $deal) }}" class="text-decoration-none text-dark">
                                    {{ $deal->deal_code }}
                                </a>
                            </td>
                            <td>{{ $deal->customer->name ?? 'N/A' }}</td>
                            <td class="fw-bold">৳ {{ number_format($deal->closing_price, 2) }}</td>
                            <td class="text-success fw-bold">৳ {{ number_format($deal->company_commission_amount, 2) }}</td>
                            <td>৳ {{ number_format($deal->agent_commission_amount, 2) }} ({{ $deal->agent_commission_percent }}%)</td>
                            <td>{{ \Carbon\Carbon::parse($deal->deal_date)->format('M d, Y') }}</td>
                            <td>
                                <span class="badge {{ $deal->status === 'settled' ? 'bg-success' : 'bg-warning text-dark' }}">
                                    {{ ucfirst(str_replace('_', ' ', $deal->status)) }}
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('erp.agency-deals.show', $deal) }}" class="btn btn-outline-secondary btn-sm px-2 py-1">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif

<!-- Linked Tenant Leases -->
@if($agencyListing->leases->isNotEmpty())
    <div class="card card-custom mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-file-earmark-lock-fill text-erp-gold me-2"></i> Tenant Rental Lease Agreements
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th class="ps-3">Lease Code</th>
                        <th>Tenant</th>
                        <th>Monthly Rent</th>
                        <th>Security Deposit</th>
                        <th>Lease Period</th>
                        <th>Rent Due Day</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($agencyListing->leases as $lease)
                        <tr>
                            <td class="ps-3 fw-bold">
                                <a href="{{ route('erp.agency-leases.show', $lease) }}" class="text-decoration-none text-dark">
                                    {{ $lease->lease_code }}
                                </a>
                            </td>
                            <td>{{ $lease->tenant->name ?? 'N/A' }} ({{ $lease->tenant->phone ?? '' }})</td>
                            <td class="fw-bold">৳ {{ number_format($lease->monthly_rent, 2) }}/mo</td>
                            <td>৳ {{ number_format($lease->security_deposit_amount, 2) }}</td>
                            <td>{{ \Carbon\Carbon::parse($lease->lease_start_date)->format('M Y') }} — {{ \Carbon\Carbon::parse($lease->lease_end_date)->format('M Y') }}</td>
                            <td>{{ $lease->rent_due_day }}th of month</td>
                            <td>
                                <span class="badge {{ $lease->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($lease->status) }}
                                </span>
                            </td>
                            <td class="text-end pe-3">
                                <a href="{{ route('erp.agency-leases.show', $lease) }}" class="btn btn-outline-secondary btn-sm px-2 py-1">View</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection
