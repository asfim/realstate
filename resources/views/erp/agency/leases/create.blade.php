@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.agency-leases.index') }}" class="text-decoration-none">Tenant Leases</a></li>
                <li class="breadcrumb-item active" aria-current="page">New Rental Agreement</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-1 text-dark">Create Rental Lease Agreement</h4>
        <p class="text-muted small mb-0">Record tenancy agreement, security deposit terms, recurring monthly rent due dates, and key transfer.</p>
    </div>
    <div>
        <a href="{{ route('erp.agency-leases.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Leases
        </a>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <strong>Please correct the following errors:</strong>
        <ul class="mb-0 mt-1">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<div class="card card-custom p-4">
    <form action="{{ route('erp.agency-leases.store') }}" method="POST">
        @csrf

        <!-- Property & Tenant Selection -->
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-house-door-fill text-erp-gold me-2"></i> Rental Property & Tenant Assignment
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Select Rental Listing Mandate <span class="text-danger">*</span></label>
                <select name="listing_id" id="listingSelect" class="form-select form-select-sm" required>
                    <option value="">-- Choose Rental Property --</option>
                    @foreach($rentalListings as $listing)
                        <option value="{{ $listing->id }}" 
                                data-rent="{{ $listing->asking_price }}"
                                {{ (old('listing_id', request('listing_id')) == $listing->id || ($selectedListing && $selectedListing->id == $listing->id)) ? 'selected' : '' }}>
                            {{ $listing->listing_code }} - {{ $listing->title }} (৳{{ number_format($listing->asking_price) }}/mo)
                        </option>
                    @endforeach
                </select>
                @if($rentalListings->isEmpty())
                    <div class="text-danger extra-small mt-1">No active rental listings found. <a href="{{ route('erp.agency-listings.create') }}">Create rental listing</a></div>
                @endif
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Tenant / Leaseholder <span class="text-danger">*</span></label>
                <select name="tenant_id" class="form-select form-select-sm" required>
                    <option value="">-- Select Registered Customer --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ old('tenant_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->phone }}) - {{ $c->customer_code }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Rent Terms & Security Deposit -->
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-cash-stack text-erp-gold me-2"></i> Monthly Rent & Financial Terms
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Monthly Rent (BDT) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="monthly_rent" id="monthlyRent" class="form-control form-control-sm" placeholder="e.g. 65000" value="{{ old('monthly_rent') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Security Deposit / Caution Money (BDT) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="security_deposit_amount" id="securityDeposit" class="form-control form-control-sm" placeholder="e.g. 130000 (2 months)" value="{{ old('security_deposit_amount') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Monthly Service Charge / Maintenance (BDT)</label>
                <input type="number" step="0.01" name="monthly_service_charge" class="form-control form-control-sm" placeholder="e.g. 8000" value="{{ old('monthly_service_charge', '0') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Lease Start Date <span class="text-danger">*</span></label>
                <input type="date" name="lease_start_date" class="form-control form-control-sm" value="{{ old('lease_start_date', now()->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Lease End Date <span class="text-danger">*</span></label>
                <input type="date" name="lease_end_date" class="form-control form-control-sm" value="{{ old('lease_end_date', now()->addYear()->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Monthly Rent Due Day (1 - 28) <span class="text-danger">*</span></label>
                <input type="number" name="rent_due_day" class="form-control form-control-sm" min="1" max="28" value="{{ old('rent_due_day', '5') }}" required>
                <div class="form-text">e.g. By 5th of each month.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Key Handover Date</label>
                <input type="date" name="key_handover_date" class="form-control form-control-sm" value="{{ old('key_handover_date', now()->format('Y-m-d')) }}">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold">Special Tenancy Conditions / Clauses</label>
            <textarea name="special_terms" class="form-control" rows="3" placeholder="e.g. 2 months notice period required before termination, painting cost deducted from security deposit...">{{ old('special_terms') }}</textarea>
        </div>

        <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <a href="{{ route('erp.agency-leases.index') }}" class="btn btn-light border px-4">Cancel</a>
            <button type="submit" class="btn btn-erp-gold px-5 fw-semibold">
                <i class="bi bi-file-earmark-check me-1"></i> Finalize Lease Contract
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const listingSelect = document.getElementById('listingSelect');
    const monthlyRent = document.getElementById('monthlyRent');
    const securityDeposit = document.getElementById('securityDeposit');

    if (listingSelect) {
        listingSelect.addEventListener('change', function() {
            const opt = listingSelect.options[listingSelect.selectedIndex];
            if (opt && opt.dataset.rent) {
                const rent = parseFloat(opt.dataset.rent);
                if (!monthlyRent.value) monthlyRent.value = rent;
                if (!securityDeposit.value) securityDeposit.value = rent * 2; // Default 2 months deposit
            }
        });
    }
});
</script>
@endsection

