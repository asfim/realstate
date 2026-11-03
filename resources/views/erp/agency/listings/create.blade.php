@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.agency-listings.index') }}" class="text-decoration-none">Agency Listings</a></li>
                <li class="breadcrumb-item active" aria-current="page">New Listing Mandate</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-1 text-dark">Register Third-Party Agency Mandate</h4>
        <p class="text-muted small mb-0">Record resale apartments, rental houses, commercial spaces, and landowner brokerage representation.</p>
    </div>
    <div>
        <a href="{{ route('erp.agency-listings.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to List
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
    <form action="{{ route('erp.agency-listings.store') }}" method="POST">
        @csrf

        <!-- General Listing Info -->
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-tag-fill text-erp-gold me-2"></i> Listing Classification & Location
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Listing Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control form-control-sm" placeholder="e.g. 2400 Sft Luxury Apartment in Banani Road 11" value="{{ old('title') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Listing Type <span class="text-danger">*</span></label>
                <select name="listing_type" class="form-select form-select-sm" required>
                    <option value="resale" {{ old('listing_type') === 'resale' ? 'selected' : '' }}>Resale / Outright Sale</option>
                    <option value="rental" {{ old('listing_type') === 'rental' ? 'selected' : '' }}>Residential Rental</option>
                    <option value="commercial_lease" {{ old('listing_type') === 'commercial_lease' ? 'selected' : '' }}>Commercial Lease</option>
                    <option value="land_sale" {{ old('listing_type') === 'land_sale' ? 'selected' : '' }}>Land / Plot Sale</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Property Category <span class="text-danger">*</span></label>
                <select name="property_category" class="form-select form-select-sm" required>
                    <option value="apartment" {{ old('property_category') === 'apartment' ? 'selected' : '' }}>Apartment / Flat</option>
                    <option value="commercial" {{ old('property_category') === 'commercial' ? 'selected' : '' }}>Commercial Space / Office</option>
                    <option value="house" {{ old('property_category') === 'house' ? 'selected' : '' }}>Independent House / Villa</option>
                    <option value="land" {{ old('property_category') === 'land' ? 'selected' : '' }}>Land Plot</option>
                    <option value="warehouse" {{ old('property_category') === 'warehouse' ? 'selected' : '' }}>Warehouse / Industrial</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Location / Area <span class="text-danger">*</span></label>
                <input type="text" name="location" class="form-control form-control-sm" placeholder="e.g. Gulshan 1, Dhaka" value="{{ old('location') }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Full Address / House & Road</label>
                <input type="text" name="address" class="form-control form-control-sm" placeholder="House 12, Road 5, Block B..." value="{{ old('address') }}">
            </div>
        </div>

        <!-- Property Specifications -->
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-aspect-ratio text-erp-gold me-2"></i> Physical Specs & Office Key Status
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Size (Sqft)</label>
                <input type="number" step="0.01" name="size_sqft" class="form-control form-control-sm" placeholder="e.g. 2150" value="{{ old('size_sqft') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Bedrooms</label>
                <input type="number" name="bedrooms" class="form-control form-control-sm" min="0" max="20" placeholder="e.g. 3" value="{{ old('bedrooms') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Bathrooms</label>
                <input type="number" name="bathrooms" class="form-control form-control-sm" min="0" max="20" placeholder="e.g. 3" value="{{ old('bathrooms') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Floor Number</label>
                <input type="number" name="floor_number" class="form-control form-control-sm" placeholder="e.g. 5" value="{{ old('floor_number') }}">
            </div>
            <div class="col-md-6">
                <div class="form-check form-switch mt-4">
                    <input class="form-check-input" type="checkbox" name="key_held_at_branch" id="keyHeld" value="1" {{ old('key_held_at_branch') ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="keyHeld">Property physical keys held at agency branch office for immediate viewing</label>
                </div>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Assigned Branch</label>
                <select name="branch_id" class="form-select form-select-sm">
                    <option value="">-- Company Default --</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Landlord / Seller Information -->
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-person-lines-fill text-erp-gold me-2"></i> Landlord / Property Owner Details
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Owner / Seller Name <span class="text-danger">*</span></label>
                <input type="text" name="landlord_seller_name" class="form-control form-control-sm" placeholder="e.g. Dr. Kazi Mostafa" value="{{ old('landlord_seller_name') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Owner Contact Phone <span class="text-danger">*</span></label>
                <input type="text" name="landlord_seller_phone" class="form-control form-control-sm" placeholder="e.g. +880 1711 002233" value="{{ old('landlord_seller_phone') }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Owner Email</label>
                <input type="email" name="landlord_seller_email" class="form-control form-control-sm" placeholder="owner@gmail.com" value="{{ old('landlord_seller_email') }}">
            </div>
        </div>

        <!-- Commercial Terms & Brokerage Commission -->
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-cash-coin text-erp-gold me-2"></i> Commercial Mandate & Brokerage Commission
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Mandate Type <span class="text-danger">*</span></label>
                <select name="mandate_type" class="form-select form-select-sm" required>
                    <option value="exclusive" {{ old('mandate_type') === 'exclusive' ? 'selected' : '' }}>Exclusive Mandate (Sole Agent)</option>
                    <option value="semi_exclusive" {{ old('mandate_type') === 'semi_exclusive' ? 'selected' : '' }}>Semi-Exclusive</option>
                    <option value="open" {{ old('mandate_type') === 'open' ? 'selected' : '' }}>Open Mandate</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Mandate Expiry Date</label>
                <input type="date" name="mandate_expiry_date" class="form-control form-control-sm" value="{{ old('mandate_expiry_date', now()->addMonths(6)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Asking Price (BDT) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="asking_price" id="askingPrice" class="form-control form-control-sm" placeholder="e.g. 25000000" value="{{ old('asking_price') }}" required>
                <div class="form-text">For rentals, enter monthly rent amount.</div>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Reserve / Bottom Price (BDT)</label>
                <input type="number" step="0.01" name="reserve_price" class="form-control form-control-sm" placeholder="Minimum acceptable price" value="{{ old('reserve_price') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Agency Commission Rate (%) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="commission_rate_percent" id="commRate" class="form-control form-control-sm" value="{{ old('commission_rate_percent', '2.00') }}" required>
                <div class="form-text">Standard: 2.0% on sale, 100% (1 mo rent) on rental.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Expected Commission (BDT)</label>
                <input type="text" id="expectedCommissionDisplay" class="form-control form-control-sm bg-light fw-bold text-success" readonly value="৳ 0.00">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Assigned Real Estate Agent</label>
                <select name="assigned_agent_id" class="form-select form-select-sm">
                    <option value="">-- Unassigned --</option>
                    @foreach($agents as $ag)
                        <option value="{{ $ag->id }}" {{ old('assigned_agent_id', auth('admin')->id()) == $ag->id ? 'selected' : '' }}>{{ $ag->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Initial Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select form-select-sm" required>
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active Market</option>
                    <option value="scouting" {{ old('status') === 'scouting' ? 'selected' : '' }}>Scouting / Lead Phase</option>
                    <option value="negotiating" {{ old('status') === 'negotiating' ? 'selected' : '' }}>Under Negotiation</option>
                </select>
            </div>
            <div class="col-md-8">
                <label class="form-label fw-semibold">Special Terms / Key Instructions</label>
                <input type="text" name="notes" class="form-control form-control-sm" placeholder="e.g. Landlord prefers family tenants, viewing strictly after 4 PM" value="{{ old('notes') }}">
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <a href="{{ route('erp.agency-listings.index') }}" class="btn btn-light border px-4">Cancel</a>
            <button type="submit" class="btn btn-erp-gold px-5 fw-semibold">
                <i class="bi bi-check-circle me-1"></i> Register Listing Mandate
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const asking = document.getElementById('askingPrice');
    const rate = document.getElementById('commRate');
    const disp = document.getElementById('expectedCommissionDisplay');

    function updateComm() {
        const p = parseFloat(asking.value) || 0;
        const r = parseFloat(rate.value) || 0;
        const c = (p * r) / 100;
        disp.value = '৳ ' + Number(c.toFixed(2)).toLocaleString();
    }

    if (asking && rate) {
        asking.addEventListener('input', updateComm);
        rate.addEventListener('input', updateComm);
        updateComm();
    }
});
</script>
@endsection
