@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-person-plus text-primary me-2"></i> Register New Customer (KYC)
        </h4>
        <div class="text-muted small">Record customer identification, NID/Passport, TIN and emergency contact</div>
    </div>
    <div>
        <a href="{{ route('erp.customers.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="{{ route('erp.customers.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Company <span class="text-danger">*</span></label>
                    <select name="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Customer Code <span class="text-danger">*</span></label>
                    <input type="text" name="customer_code" class="form-control" value="CUST-{{ rand(100, 999) }}" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Engr. Tanvir Ahmed" required>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Mobile Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" placeholder="+880 1..." required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="name@example.com">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">National ID (NID) / Passport</label>
                    <input type="text" name="nid_or_passport" class="form-control" placeholder="10 or 17 digit NID">
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">TIN (Tax ID Number)</label>
                    <input type="text" name="tin" class="form-control" placeholder="Optional TIN">
                </div>
                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">Emergency Contact & Relation</label>
                    <input type="text" name="emergency_contact" class="form-control" placeholder="e.g. Spouse (+880 1...)">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Permanent / Present Address</label>
                    <textarea name="address" rows="2" class="form-control" placeholder="House, Road, Area, City, Postcode..."></textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('erp.customers.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i> Save Customer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

