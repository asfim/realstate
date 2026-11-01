@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-person-plus text-primary me-2"></i> Enroll Staff Member / Employee
        </h4>
        <div class="text-muted small">Setup profile, designation, department, bank account, and salary structure</div>
    </div>
    <div>
        <a href="{{ route('erp.employees.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Staff Directory
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="{{ route('erp.employees.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-12">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">1. Personal & Employment Information</h6>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Company <span class="text-danger">*</span></label>
                    <select name="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}">{{ $company->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">-- General / Management --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Branch Location</label>
                    <select name="branch_id" class="form-select">
                        <option value="">-- Main Headquarter --</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Employee ID / Code <span class="text-danger">*</span></label>
                    <input type="text" name="employee_code" class="form-control" value="EMP-{{ rand(100, 999) }}" required>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Kamrul Hasan" required>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Official Designation <span class="text-danger">*</span></label>
                    <input type="text" name="designation" class="form-control" placeholder="e.g. Senior Site Engineer" required>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Mobile Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" placeholder="+880 1..." required>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="employee@aureliaestates.com">
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Joining Date <span class="text-danger">*</span></label>
                    <input type="date" name="joining_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                </div>

                <div class="col-12 mt-4">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">2. Monthly Salary Structure & Allowances (BDT)</h6>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Basic Salary <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">৳</span>
                        <input type="number" step="0.01" name="basic_salary" class="form-control" placeholder="e.g. 50000" required>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">House Rent Allowance</label>
                    <div class="input-group">
                        <span class="input-group-text">৳</span>
                        <input type="number" step="0.01" name="house_rent" class="form-control" value="0.00">
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Medical Allowance</label>
                    <div class="input-group">
                        <span class="input-group-text">৳</span>
                        <input type="number" step="0.01" name="medical_allowance" class="form-control" value="0.00">
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Other Allowances (Conveyance / Mobile)</label>
                    <div class="input-group">
                        <span class="input-group-text">৳</span>
                        <input type="number" step="0.01" name="other_allowance" class="form-control" value="0.00">
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Tax Deduction</label>
                    <div class="input-group">
                        <span class="input-group-text">৳</span>
                        <input type="number" step="0.01" name="tax_deduction" class="form-control" value="0.00">
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Provident Fund (PF) Deduction</label>
                    <div class="input-group">
                        <span class="input-group-text">৳</span>
                        <input type="number" step="0.01" name="provident_fund" class="form-control" value="0.00">
                    </div>
                </div>

                <div class="col-12 col-md-6 mt-3">
                    <label class="form-label fw-semibold">Disbursement Bank Name</label>
                    <input type="text" name="bank_name" class="form-control" placeholder="e.g. BRAC Bank Ltd, Dutch-Bangla Bank">
                </div>

                <div class="col-12 col-md-6 mt-3">
                    <label class="form-label fw-semibold">Bank Account Number</label>
                    <input type="text" name="bank_account_no" class="form-control" placeholder="e.g. 150120...">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('erp.employees.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i> Enroll Staff Member
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
