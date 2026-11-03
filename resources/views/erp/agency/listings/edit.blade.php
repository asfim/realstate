@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.agency-listings.index') }}" class="text-decoration-none">Agency Listings</a></li>
                <li class="breadcrumb-item"><a href="{{ route('erp.agency-listings.show', $agencyListing) }}" class="text-decoration-none">{{ $agencyListing->listing_code }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Mandate</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-1 text-dark">Edit Mandate: {{ $agencyListing->listing_code }}</h4>
        <p class="text-muted small mb-0">{{ $agencyListing->title }}</p>
    </div>
    <div>
        <a href="{{ route('erp.agency-listings.show', $agencyListing) }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Dossier
        </a>
    </div>
</div>

<div class="card card-custom p-4">
    <form action="{{ route('erp.agency-listings.update', $agencyListing) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Listing Title <span class="text-danger">*</span></label>
                <input type="text" name="title" class="form-control form-control-sm" value="{{ old('title', $agencyListing->title) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Listing Type <span class="text-danger">*</span></label>
                <select name="listing_type" class="form-select form-select-sm" required>
                    <option value="resale" {{ old('listing_type', $agencyListing->listing_type) === 'resale' ? 'selected' : '' }}>Resale / Outright Sale</option>
                    <option value="rental" {{ old('listing_type', $agencyListing->listing_type) === 'rental' ? 'selected' : '' }}>Residential Rental</option>
                    <option value="commercial_lease" {{ old('listing_type', $agencyListing->listing_type) === 'commercial_lease' ? 'selected' : '' }}>Commercial Lease</option>
                    <option value="land_sale" {{ old('listing_type', $agencyListing->listing_type) === 'land_sale' ? 'selected' : '' }}>Land / Plot Sale</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Property Category <span class="text-danger">*</span></label>
                <select name="property_category" class="form-select form-select-sm" required>
                    <option value="apartment" {{ old('property_category', $agencyListing->property_category) === 'apartment' ? 'selected' : '' }}>Apartment / Flat</option>
                    <option value="commercial" {{ old('property_category', $agencyListing->property_category) === 'commercial' ? 'selected' : '' }}>Commercial Space</option>
                    <option value="house" {{ old('property_category', $agencyListing->property_category) === 'house' ? 'selected' : '' }}>House / Villa</option>
                    <option value="land" {{ old('property_category', $agencyListing->property_category) === 'land' ? 'selected' : '' }}>Land Plot</option>
                    <option value="warehouse" {{ old('property_category', $agencyListing->property_category) === 'warehouse' ? 'selected' : '' }}>Warehouse</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Location / Area <span class="text-danger">*</span></label>
                <input type="text" name="location" class="form-control form-control-sm" value="{{ old('location', $agencyListing->location) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Full Address</label>
                <input type="text" name="address" class="form-control form-control-sm" value="{{ old('address', $agencyListing->address) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Size (Sqft)</label>
                <input type="number" step="0.01" name="size_sqft" class="form-control form-control-sm" value="{{ old('size_sqft', $agencyListing->size_sqft) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Bedrooms</label>
                <input type="number" name="bedrooms" class="form-control form-control-sm" value="{{ old('bedrooms', $agencyListing->bedrooms) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Bathrooms</label>
                <input type="number" name="bathrooms" class="form-control form-control-sm" value="{{ old('bathrooms', $agencyListing->bathrooms) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Floor Number</label>
                <input type="number" name="floor_number" class="form-control form-control-sm" value="{{ old('floor_number', $agencyListing->floor_number) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Owner / Seller Name <span class="text-danger">*</span></label>
                <input type="text" name="landlord_seller_name" class="form-control form-control-sm" value="{{ old('landlord_seller_name', $agencyListing->landlord_seller_name) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Owner Contact Phone <span class="text-danger">*</span></label>
                <input type="text" name="landlord_seller_phone" class="form-control form-control-sm" value="{{ old('landlord_seller_phone', $agencyListing->landlord_seller_phone) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Owner Email</label>
                <input type="email" name="landlord_seller_email" class="form-control form-control-sm" value="{{ old('landlord_seller_email', $agencyListing->landlord_seller_email) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Mandate Type <span class="text-danger">*</span></label>
                <select name="mandate_type" class="form-select form-select-sm" required>
                    <option value="exclusive" {{ old('mandate_type', $agencyListing->mandate_type) === 'exclusive' ? 'selected' : '' }}>Exclusive Mandate</option>
                    <option value="semi_exclusive" {{ old('mandate_type', $agencyListing->mandate_type) === 'semi_exclusive' ? 'selected' : '' }}>Semi-Exclusive</option>
                    <option value="open" {{ old('mandate_type', $agencyListing->mandate_type) === 'open' ? 'selected' : '' }}>Open Mandate</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Mandate Expiry Date</label>
                <input type="date" name="mandate_expiry_date" class="form-control form-control-sm" value="{{ old('mandate_expiry_date', optional($agencyListing->mandate_expiry_date)->format('Y-m-d')) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Asking Price (BDT) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="asking_price" class="form-control form-control-sm" value="{{ old('asking_price', $agencyListing->asking_price) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Reserve / Bottom Price (BDT)</label>
                <input type="number" step="0.01" name="reserve_price" class="form-control form-control-sm" value="{{ old('reserve_price', $agencyListing->reserve_price) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Agency Commission Rate (%) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="commission_rate_percent" class="form-control form-control-sm" value="{{ old('commission_rate_percent', $agencyListing->commission_rate_percent) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Assigned Agent</label>
                <select name="assigned_agent_id" class="form-select form-select-sm">
                    <option value="">-- Unassigned --</option>
                    @foreach($agents as $ag)
                        <option value="{{ $ag->id }}" {{ old('assigned_agent_id', $agencyListing->assigned_agent_id) == $ag->id ? 'selected' : '' }}>{{ $ag->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select form-select-sm" required>
                    <option value="active" {{ old('status', $agencyListing->status) === 'active' ? 'selected' : '' }}>Active Market</option>
                    <option value="negotiating" {{ old('status', $agencyListing->status) === 'negotiating' ? 'selected' : '' }}>Under Negotiation</option>
                    <option value="closed_deal" {{ old('status', $agencyListing->status) === 'closed_deal' ? 'selected' : '' }}>Closed Deal</option>
                    <option value="expired" {{ old('status', $agencyListing->status) === 'expired' ? 'selected' : '' }}>Expired</option>
                    <option value="withdrawn" {{ old('status', $agencyListing->status) === 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                </select>
            </div>
            <div class="col-md-12">
                <div class="form-check form-switch mb-3">
                    <input class="form-check-input" type="checkbox" name="key_held_at_branch" id="keyHeld" value="1" {{ old('key_held_at_branch', $agencyListing->key_held_at_branch) ? 'checked' : '' }}>
                    <label class="form-check-label fw-semibold" for="keyHeld">Property physical keys held at agency branch office for immediate viewing</label>
                </div>
            </div>
            <div class="col-md-12">
                <label class="form-label fw-semibold">Notes & Viewing Terms</label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $agencyListing->notes) }}</textarea>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <a href="{{ route('erp.agency-listings.show', $agencyListing) }}" class="btn btn-light border px-4">Cancel</a>
            <button type="submit" class="btn btn-erp-gold px-5 fw-semibold">
                <i class="bi bi-check-circle me-1"></i> Update Listing
            </button>
        </div>
    </form>
</div>
@endsection

