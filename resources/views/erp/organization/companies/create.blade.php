@extends('erp.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Add New Legal Entity</h4>
        <p class="text-muted small mb-0">Create a company profile for ERP multi-company accounting and operations.</p>
    </div>
    <a href="{{ route('erp.companies.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Entities
    </a>
</div>

<div class="card card-custom">
    <div class="card-body p-4">
        <form action="{{ route('erp.companies.store') }}" method="POST">
            @csrf
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Company Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control" placeholder="e.g. AURELIA" value="{{ old('code') }}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold small">Legal Registered Name <span class="text-danger">*</span></label>
                    <input type="text" name="legal_name" class="form-control" placeholder="e.g. Aurelia Estates & Developers Ltd." value="{{ old('legal_name') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Brand / Trade Name</label>
                    <input type="text" name="trade_name" class="form-control" placeholder="e.g. Aurelia Living" value="{{ old('trade_name') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Registration / Incorporation No.</label>
                    <input type="text" name="registration_no" class="form-control" placeholder="e.g. C-198425/2026" value="{{ old('registration_no') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Business Identification No. (BIN)</label>
                    <input type="text" name="bin" class="form-control" placeholder="e.g. 004829103-0101" value="{{ old('bin') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Tax Identification No. (TIN)</label>
                    <input type="text" name="tin" class="form-control" placeholder="e.g. 491827401928" value="{{ old('tin') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Base Currency <span class="text-danger">*</span></label>
                    <input type="text" name="base_currency" class="form-control" value="{{ old('base_currency', 'BDT') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Fiscal Year Start <span class="text-danger">*</span></label>
                    <input type="text" name="fiscal_year_start" class="form-control" value="{{ old('fiscal_year_start', '07-01') }}" required>
                    <small class="text-muted">Format: MM-DD (e.g. 07-01 for July 1)</small>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Timezone <span class="text-danger">*</span></label>
                    <input type="text" name="timezone" class="form-control" value="{{ old('timezone', 'Asia/Dhaka') }}" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Entity Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                <a href="{{ route('erp.companies.index') }}" class="btn btn-light px-4">Cancel</a>
                <button type="submit" class="btn btn-erp-gold px-4">Create Entity</button>
            </div>
        </form>
    </div>
</div>
@endsection

