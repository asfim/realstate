@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.hr-employees.index') }}" class="text-decoration-none">Employees</a></li>
                <li class="breadcrumb-item"><a href="{{ route('erp.hr-employees.show', $hrEmployee) }}" class="text-decoration-none">{{ $hrEmployee->employee_code }}</a></li>
                <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-1 text-dark">Edit Employee: {{ $hrEmployee->name }} ({{ $hrEmployee->employee_code }})</h4>
        <p class="text-muted small mb-0">Modify employee profile, compensation salary breakdown, and department assignment.</p>
    </div>
    <div>
        <a href="{{ route('erp.hr-employees.show', $hrEmployee) }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Profile
        </a>
    </div>
</div>

<div class="card card-custom p-4">
    <form action="{{ route('erp.hr-employees.update', $hrEmployee) }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Personal & Official Details -->
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-person-badge-fill text-erp-gold me-2"></i> Employee Identity & Role Assignment
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Employee Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control form-control-sm" value="{{ old('name', $hrEmployee->name) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="form-control form-control-sm" value="{{ old('phone', $hrEmployee->phone) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Email Address</label>
                <input type="email" name="email" class="form-control form-control-sm" value="{{ old('email', $hrEmployee->email) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Department</label>
                <select name="department_id" class="form-select form-select-sm">
                    <option value="">-- Choose Department --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id', $hrEmployee->department_id) == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Designation / Role</label>
                <select name="designation_id" class="form-select form-select-sm">
                    <option value="">-- Choose Designation --</option>
                    @foreach($designations as $des)
                        <option value="{{ $des->id }}" {{ old('designation_id', $hrEmployee->designation_id) == $des->id ? 'selected' : '' }}>{{ $des->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">National ID (NID)</label>
                <input type="text" name="nid_no" class="form-control form-control-sm" value="{{ old('nid_no', $hrEmployee->nid_no) }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Joining Date <span class="text-danger">*</span></label>
                <input type="date" name="joining_date" class="form-control form-control-sm" value="{{ old('joining_date', $hrEmployee->joining_date?->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Employment Type <span class="text-danger">*</span></label>
                <select name="employment_type" class="form-select form-select-sm" required>
                    <option value="permanent" {{ old('employment_type', $hrEmployee->employment_type) == 'permanent' ? 'selected' : '' }}>Permanent</option>
                    <option value="probation" {{ old('employment_type', $hrEmployee->employment_type) == 'probation' ? 'selected' : '' }}>Probationary</option>
                    <option value="contract" {{ old('employment_type', $hrEmployee->employment_type) == 'contract' ? 'selected' : '' }}>Contractual</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Employment Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select form-select-sm" required>
                    <option value="active" {{ old('status', $hrEmployee->status) == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="resigned" {{ old('status', $hrEmployee->status) == 'resigned' ? 'selected' : '' }}>Resigned</option>
                    <option value="terminated" {{ old('status', $hrEmployee->status) == 'terminated' ? 'selected' : '' }}>Terminated</option>
                </select>
            </div>
        </div>

        <!-- Compensation & Salary Breakdown -->
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-cash-stack text-success me-2"></i> Compensation & Monthly Salary Breakdown (BDT)
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Basic Salary (BDT) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="basic_salary" id="basic_salary" class="form-control form-control-sm calc-sal" value="{{ old('basic_salary', $hrEmployee->basic_salary) }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">House Rent Allowance</label>
                <input type="number" step="0.01" name="house_rent_allowance" id="house_rent" class="form-control form-control-sm calc-sal" value="{{ old('house_rent_allowance', $hrEmployee->house_rent_allowance) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Medical Allowance</label>
                <input type="number" step="0.01" name="medical_allowance" id="medical" class="form-control form-control-sm calc-sal" value="{{ old('medical_allowance', $hrEmployee->medical_allowance) }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Conveyance Allowance</label>
                <input type="number" step="0.01" name="conveyance_allowance" id="conveyance" class="form-control form-control-sm calc-sal" value="{{ old('conveyance_allowance', $hrEmployee->conveyance_allowance) }}">
            </div>
            <div class="col-md-12">
                <div class="alert alert-secondary py-2 px-3 d-flex justify-content-between align-items-center mb-0">
                    <span class="small fw-semibold text-muted">Computed Gross Monthly Salary:</span>
                    <span class="fw-bold fs-5 text-dark" id="gross_display">৳ {{ number_format($hrEmployee->gross_salary, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Banking Details -->
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-bank text-primary me-2"></i> Bank Disbursement Information
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Bank Name</label>
                <input type="text" name="bank_name" class="form-control form-control-sm" placeholder="e.g. Dutch-Bangla Bank PLC, City Bank" value="{{ old('bank_name', $hrEmployee->bank_name) }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Bank Account Number / Routing</label>
                <input type="text" name="bank_account_no" class="form-control form-control-sm" placeholder="e.g. 115.120.987654" value="{{ old('bank_account_no', $hrEmployee->bank_account_no) }}">
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('erp.hr-employees.show', $hrEmployee) }}" class="btn btn-light btn-sm px-4">Cancel</a>
            <button type="submit" class="btn btn-erp btn-sm px-4">
                <i class="bi bi-check2-circle me-1"></i> Update Employee
            </button>
        </div>
    </form>
</div>

<script>
document.querySelectorAll('.calc-sal').forEach(input => {
    input.addEventListener('input', () => {
        const basic = parseFloat(document.getElementById('basic_salary').value) || 0;
        const house = parseFloat(document.getElementById('house_rent').value) || 0;
        const med = parseFloat(document.getElementById('medical').value) || 0;
        const conv = parseFloat(document.getElementById('conveyance').value) || 0;
        const total = basic + house + med + conv;
        document.getElementById('gross_display').innerText = '৳ ' + total.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    });
});
</script>
@endsection

