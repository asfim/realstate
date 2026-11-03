@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.agency-leases.index') }}" class="text-decoration-none">Tenant Leases</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $agencyLease->lease_code }}</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <h4 class="fw-bold mb-0 text-dark">Lease Contract {{ $agencyLease->lease_code }}</h4>
            <span class="badge {{ $agencyLease->status === 'active' ? 'bg-success' : ($agencyLease->status === 'expired' ? 'bg-secondary' : 'bg-danger') }} px-3 py-1">
                {{ strtoupper($agencyLease->status) }}
            </span>
        </div>
        <p class="text-muted small mb-0">Period: {{ \Carbon\Carbon::parse($agencyLease->lease_start_date)->format('M d, Y') }} — {{ \Carbon\Carbon::parse($agencyLease->lease_end_date)->format('M d, Y') }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.agency-leases.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Leases
        </a>
        <button type="button" class="btn btn-outline-dark btn-sm px-3" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print Agreement
        </button>
        @if($agencyLease->status === 'active')
            <form action="{{ route('erp.agency-leases.terminate', $agencyLease) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-outline-danger btn-sm px-3" onclick="return confirm('Terminate this tenancy lease and release the property back to available listings?')">
                    <i class="bi bi-x-circle me-1"></i> Terminate Lease
                </button>
            </form>
        @endif
    </div>
</div>

<!-- Overview Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-primary h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Monthly Rent</div>
            <div class="fs-4 fw-bold text-dark mt-1">৳ {{ number_format($agencyLease->monthly_rent, 2) }}</div>
            <div class="text-muted extra-small mt-1">Due by {{ $agencyLease->rent_due_day }}th of each month</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-info h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Security Deposit Held</div>
            <div class="fs-4 fw-bold text-info mt-1">৳ {{ number_format($agencyLease->security_deposit_amount, 2) }}</div>
            <div class="text-muted extra-small mt-1">Refundable Caution Money</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-warning h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Monthly Service Charge</div>
            <div class="fs-4 fw-bold text-dark mt-1">৳ {{ number_format($agencyLease->monthly_service_charge, 2) }}</div>
            <div class="text-muted extra-small mt-1">Maintenance & Utilities</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-success h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Key Handover</div>
            <div class="fs-5 fw-bold text-success mt-1">
                {{ $agencyLease->key_handover_date ? \Carbon\Carbon::parse($agencyLease->key_handover_date)->format('M d, Y') : 'Pending' }}
            </div>
            <div class="text-muted extra-small mt-1">Status: {{ ucfirst($agencyLease->status) }}</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Property & Landlord -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-building text-erp-gold me-2"></i> Leased Property & Owner
            </h6>
            @if($agencyLease->listing)
                <div class="row g-2 small">
                    <div class="col-sm-4 text-muted">Property Title:</div>
                    <div class="col-sm-8 fw-bold">
                        <a href="{{ route('erp.agency-listings.show', $agencyLease->listing_id) }}" class="text-decoration-none">
                            {{ $agencyLease->listing->title }}
                        </a>
                    </div>

                    <div class="col-sm-4 text-muted">Location:</div>
                    <div class="col-sm-8 text-dark">{{ $agencyLease->listing->location }}</div>

                    <div class="col-sm-4 text-muted">Address:</div>
                    <div class="col-sm-8 text-dark">{{ $agencyLease->listing->address ?? '—' }}</div>

                    <div class="col-sm-4 text-muted">Specs:</div>
                    <div class="col-sm-8 text-dark">
                        {{ number_format($agencyLease->listing->size_sqft ?? 0) }} sft 
                        ({{ $agencyLease->listing->bedrooms ?? 0 }} Bed, {{ $agencyLease->listing->bathrooms ?? 0 }} Bath)
                    </div>

                    <div class="col-sm-4 text-muted">Landlord:</div>
                    <div class="col-sm-8 text-dark fw-semibold">{{ $agencyLease->listing->landlord_seller_name }} ({{ $agencyLease->listing->landlord_seller_phone }})</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Tenant Dossier -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-person-fill text-erp-gold me-2"></i> Tenant / Lessee Information
            </h6>
            @if($agencyLease->tenant)
                <div class="row g-2 small">
                    <div class="col-sm-4 text-muted">Tenant Name:</div>
                    <div class="col-sm-8 fw-bold text-dark">{{ $agencyLease->tenant->name }} ({{ $agencyLease->tenant->customer_code }})</div>

                    <div class="col-sm-4 text-muted">Phone Number:</div>
                    <div class="col-sm-8 text-dark">{{ $agencyLease->tenant->phone }}</div>

                    <div class="col-sm-4 text-muted">Email Address:</div>
                    <div class="col-sm-8 text-dark">{{ $agencyLease->tenant->email ?? '—' }}</div>

                    <div class="col-sm-4 text-muted">National ID:</div>
                    <div class="col-sm-8 text-dark">{{ $agencyLease->tenant->nid_or_passport ?? '—' }}</div>

                    <div class="col-sm-4 text-muted">Permanent Address:</div>
                    <div class="col-sm-8 text-dark">{{ $agencyLease->tenant->permanent_address ?? '—' }}</div>
                </div>
            @endif
        </div>
    </div>
</div>

@if($agencyLease->special_terms)
    <div class="card card-custom p-4 mb-4">
        <h6 class="fw-bold text-dark mb-2">Legal Tenancy Terms & Conditions</h6>
        <p class="text-muted small mb-0" style="white-space: pre-line;">{{ $agencyLease->special_terms }}</p>
    </div>
@endif
@endsection

