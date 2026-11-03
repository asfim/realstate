@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.hr-employees.index') }}" class="text-decoration-none">Employees</a></li>
                <li class="breadcrumb-item active" aria-current="page">Onboard Employee</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-1 text-dark">Onboard Permanent Staff Employee</h4>
        <p class="text-muted small mb-0">Record compensation breakdown (Basic, House Rent, Medical), department role, and bank disbursement info.</p>
    </div>
    <div>
        <a href="{{ route('erp.hr-employees.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>
</div>

<div class="card card-custom p-4">
    <form action="{{ route('erp.hr-employees.store') }}" method="POST">
        @csrf

        <!-- Personal & Official Details -->
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-person-badge-fill text-erp-gold me-2"></i> Employee Identity & Role Assignment
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Employee Full Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Architect Tanvir Ahmed" value="{{ old('name') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Phone Number <span class="text-danger">*</span></label>
                <input type="text" name="phone" class="form-control form-control-sm" placeholder="e.g. +880 1711 000000" value="{{ old('phone') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Email Address</label>
                <input type="email" name="email" class="form-control form-control-sm" placeholder="tanvir@example.com" value="{{ old('email') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Department <span class="text-danger">*</span></label>
                <select name="department_id" class="form-select form-select-sm" required>
                    <option value="">-- Choose Department --</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ old('department_id') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Designation / Role <span class="text-danger">*</span></label>
                <select name="designation_id" class="form-select form-select-sm" required>
                    <option value="">-- Choose Designation --</option>
                    @foreach($designations as $des)
                        <option value="{{ $des->id }}" {{ old('designation_id') == $des->id ? 'selected' : '' }}>{{ $des->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Assigned Branch</label>
                <select name="branch_id" class="form-select form-select-sm">
                    <option value="">-- Corporate Head Office --</option>
                    @foreach($branches as $b)
                        <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">National ID (NID / Smart Card)</label>
                <input type="text" name="nid_no" class="form-control form-control-sm" placeholder="e.g. 19902619..." value="{{ old('nid_no') }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Joining Date <span class="text-danger">*</span></label>
                <input type="date" name="joining_date" class="form-control form-control-sm" value="{{ old('joining_date', now()->format('Y-m-d')) }}" required>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Employment Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select form-select-sm" required>
                    <option value="active" {{ old('status') === 'active' ? 'selected' : '' }}>Active Staff</option>
                    <option value="resigned" {{ old('status') === 'resigned' ? 'selected' : '' }}>Resigned</option>
                    <option value="terminated" {{ old('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Contract Type <span class="text-danger">*</span></label>
                <select name="employment_type" class="form-select form-select-sm" required>
                    <option value="permanent" {{ old('employment_type') === 'permanent' ? 'selected' : '' }}>Permanent Employee</option>
                    <option value="probation" {{ old('employment_type') === 'probation' ? 'selected' : '' }}>Probationary (6 Months)</option>
                    <option value="contract" {{ old('employment_type') === 'contract' ? 'selected' : '' }}>Fixed-term Contract</option>
                </select>
            </div>
        </div>

        <!-- Compensation Structure -->
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-cash-coin text-erp-gold me-2"></i> Monthly Compensation Package (BDT)
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-3">
                <label class="form-label fw-semibold">Basic Salary (BDT) <span class="text-danger">*</span></label>
                <input type="number" step="0.01" name="basic_salary" id="basicSalary" class="form-control form-control-sm" placeholder="e.g. 50000" value="{{ old('basic_salary') }}" required>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">House Rent Allowance (BDT)</label>
                <input type="number" step="0.01" name="house_rent_allowance" id="houseRent" class="form-control form-control-sm" placeholder="e.g. 25000" value="{{ old('house_rent_allowance') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Medical Allowance (BDT)</label>
                <input type="number" step="0.01" name="medical_allowance" id="medical" class="form-control form-control-sm" placeholder="e.g. 5000" value="{{ old('medical_allowance') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Conveyance Allowance (BDT)</label>
                <input type="number" step="0.01" name="conveyance_allowance" id="conveyance" class="form-control form-control-sm" placeholder="e.g. 5000" value="{{ old('conveyance_allowance') }}">
            </div>
            <div class="col-md-12">
                <div class="card bg-light border p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold text-dark fs-6">Calculated Total Gross Monthly Salary:</span>
                        <span class="fs-4 fw-bold text-success" id="dispGross">৳ 0.00</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bank Disbursement Details -->
        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-bank text-erp-gold me-2"></i> Bank Salary Disbursement Account
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Bank Name & Branch</label>
                <input type="text" name="bank_name" class="form-control form-control-sm" placeholder="e.g. City Bank Ltd. - Gulshan Branch" value="{{ old('bank_name') }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-semibold">Bank Account Number (13-16 Digits)</label>
                <input type="text" name="bank_account_no" class="form-control form-control-sm" placeholder="e.g. 1102938475001" value="{{ old('bank_account_no') }}">
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2 border-top pt-3">
            <a href="{{ route('erp.hr-employees.index') }}" class="btn btn-light border px-4">Cancel</a>
            <button type="submit" class="btn btn-erp-gold px-5 fw-semibold">
                <i class="bi bi-check-circle me-1"></i> Complete Employee Onboarding
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const basic = document.getElementById('basicSalary');
    const house = document.getElementById('houseRent');
    const med = document.getElementById('medical');
    const conv = document.getElementById('conveyance');
    const disp = document.getElementById('dispGross');

    function updateGross() {
        const b = parseFloat(basic.value) || 0;
        const h = parseFloat(house.value) || 0;
        const m = parseFloat(med.value) || 0;
        const c = parseFloat(conv.value) || 0;
        const gross = b + h + m + c;
        disp.innerText = '৳ ' + Number(gross.toFixed(2)).toLocaleString();
    }

    [basic, house, med, conv].forEach(el => {
        if (el) el.addEventListener('input', updateGross);
    });

    updateGross();
});
</script>
@endsection

