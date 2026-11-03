@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.con-vendors.index') }}" class="text-decoration-none">Suppliers & Vendors</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Vendor</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-1 text-dark">Edit Supplier: {{ $conVendor->name }}</h4>
        <p class="text-muted small mb-0">Code: {{ $conVendor->vendor_code }}</p>
    </div>
    <div>
        <a href="{{ route('erp.con-vendors.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Vendors
        </a>
    </div>
</div>

<div class="card card-custom p-4">
    <form action="{{ route('erp.con-vendors.update', $conVendor) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Company / Vendor Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control form-control-sm" value="{{ old('name', $conVendor->name) }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Key Contact Person</label>
                <input type="text" name="contact_person" class="form-control form-control-sm" value="{{ old('contact_person', $conVendor->contact_person) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="form-control form-control-sm" value="{{ old('phone', $conVendor->phone) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Email Address</label>
                <input type="email" name="email" class="form-control form-control-sm" value="{{ old('email', $conVendor->email) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Supplier Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select form-select-sm" required>
                    <option value="active" {{ old('status', $conVendor->status) === 'active' ? 'selected' : '' }}>Active Approved Supplier</option>
                    <option value="inactive" {{ old('status', $conVendor->status) === 'inactive' ? 'selected' : '' }}>Inactive / On Hold</option>
                    <option value="blacklisted" {{ old('status', $conVendor->status) === 'blacklisted' ? 'selected' : '' }}>Blacklisted</option>
                </select>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Trade License Number</label>
                <input type="text" name="trade_license_no" class="form-control form-control-sm" value="{{ old('trade_license_no', $conVendor->trade_license_no) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Taxpayer ID (TIN / BIN)</label>
                <input type="text" name="tin_no" class="form-control form-control-sm" value="{{ old('tin_no', $conVendor->tin_no) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Bank Account & Settlement Details</label>
                <textarea name="bank_details" class="form-control form-control-sm" rows="3">{{ old('bank_details', $conVendor->bank_details) }}</textarea>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Office / Warehouse Address</label>
                <textarea name="address" class="form-control form-control-sm" rows="3">{{ old('address', $conVendor->address) }}</textarea>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <a href="{{ route('erp.con-vendors.index') }}" class="btn btn-light border px-4">Cancel</a>
            <button type="submit" class="btn btn-erp-gold px-5 fw-semibold">
                <i class="bi bi-check-circle me-1"></i> Update Supplier
            </button>
        </div>
    </form>
</div>
@endsection

