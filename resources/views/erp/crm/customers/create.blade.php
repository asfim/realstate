@extends('erp.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Register Customer Master Profile</h4>
        <p class="text-muted small mb-0">Record permanent buyer account with legal identification and nominee details.</p>
    </div>
    <a href="{{ route('erp.customers.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Customers
    </a>
</div>

<div class="card card-custom">
    <div class="card-body p-4">
        <form action="{{ route('erp.customers.store') }}" method="POST">
            @csrf
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-person-vcard me-2 text-warning"></i>Personal & Identification Information</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Father / Husband Name</label>
                    <input type="text" name="father_or_husband_name" class="form-control" value="{{ old('father_or_husband_name') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Mother's Name</label>
                    <input type="text" name="mother_name" class="form-control" value="{{ old('mother_name') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Primary Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Emergency Contact Phone</label>
                    <input type="text" name="emergency_contact" class="form-control" value="{{ old('emergency_contact') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small">National ID (NID) Number</label>
                    <input type="text" name="nid_number" class="form-control" value="{{ old('nid_number') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Tax ID (TIN) Number</label>
                    <input type="text" name="tin_number" class="form-control" value="{{ old('tin_number') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Passport Number</label>
                    <input type="text" name="passport_number" class="form-control" value="{{ old('passport_number') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Present Address</label>
                    <textarea name="present_address" rows="2" class="form-control">{{ old('present_address') }}</textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Permanent Address</label>
                    <textarea name="permanent_address" rows="2" class="form-control">{{ old('permanent_address') }}</textarea>
                </div>
            </div>

            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-shield-check me-2 text-warning"></i>Nominee Details (KYC)</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Nominee Full Name</label>
                    <input type="text" name="nominee_name" class="form-control" value="{{ old('nominee_name') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Relationship with Customer</label>
                    <input type="text" name="nominee_relation" class="form-control" placeholder="e.g. Spouse / Son / Daughter" value="{{ old('nominee_relation') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Nominee NID Number</label>
                    <input type="text" name="nominee_nid" class="form-control" value="{{ old('nominee_nid') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Nominee Phone Number</label>
                    <input type="text" name="nominee_phone" class="form-control" value="{{ old('nominee_phone') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Account Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="active" selected>Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>

            <div class="pt-3 border-top d-flex justify-content-end gap-2">
                <a href="{{ route('erp.customers.index') }}" class="btn btn-light px-4">Cancel</a>
                <button type="submit" class="btn btn-erp-gold px-4">Create Customer</button>
            </div>
        </form>
    </div>
</div>
@endsection

