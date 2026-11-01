@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-pencil-square text-primary me-2"></i> Edit Employee: {{ $employee->name }}
        </h4>
        <div class="text-muted small">Update salary structure, designation, and employment status</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('erp.employees.show', $employee) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-eye me-1"></i> View Profile
        </a>
        <a href="{{ route('erp.employees.index') }}" class="btn btn-light btn-sm border">
            Back to Directory
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="{{ route('erp.employees.update', $employee) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Employee Code</label>
                    <input type="text" class="form-control" value="{{ $employee->employee_code }}" disabled>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Full Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $employee->name) }}" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Designation <span class="text-danger">*</span></label>
                    <input type="text" name="designation" class="form-control" value="{{ old('designation', $employee->designation) }}" required>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Department</label>
                    <select name="department_id" class="form-select">
                        <option value="">-- General --</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ old('department_id', $employee->department_id) == $dept->id ? 'selected' : '' }}>
                                {{ $dept->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Branch</label>
                    <select name="branch_id" class="form-select">
                        <option value="">-- Main Office --</option>
                        @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ old('branch_id', $employee->branch_id) == $b->id ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Employment Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="active" {{ old('status', $employee->status) === 'active' ? 'selected' : '' }}>Active Staff</option>
                        <option value="on_leave" {{ old('status', $employee->status) === 'on_leave' ? 'selected' : '' }}>On Leave</option>
                        <option value="resigned" {{ old('status', $employee->status) === 'resigned' ? 'selected' : '' }}>Resigned</option>
                        <option value="terminated" {{ old('status', $employee->status) === 'terminated' ? 'selected' : '' }}>Terminated</option>
                    </select>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $employee->phone) }}" required>
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">Email</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $employee->email) }}">
                </div>

                <div class="col-12 mt-4">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">Salary Structure</h6>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Basic Salary <span class="text-danger">*</span></label>
                    <input type="number" step="0.01" name="basic_salary" class="form-control" value="{{ old('basic_salary', $employee->basic_salary) }}" required>
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">House Rent</label>
                    <input type="number" step="0.01" name="house_rent" class="form-control" value="{{ old('house_rent', $employee->house_rent) }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Medical Allowance</label>
                    <input type="number" step="0.01" name="medical_allowance" class="form-control" value="{{ old('medical_allowance', $employee->medical_allowance) }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Other Allowances</label>
                    <input type="number" step="0.01" name="other_allowance" class="form-control" value="{{ old('other_allowance', $employee->other_allowance) }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Tax Deduction</label>
                    <input type="number" step="0.01" name="tax_deduction" class="form-control" value="{{ old('tax_deduction', $employee->tax_deduction) }}">
                </div>
                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Provident Fund (PF)</label>
                    <input type="number" step="0.01" name="provident_fund" class="form-control" value="{{ old('provident_fund', $employee->provident_fund) }}">
                </div>

                <div class="col-12 col-md-6 mt-3">
                    <label class="form-label fw-semibold">Bank Name</label>
                    <input type="text" name="bank_name" class="form-control" value="{{ old('bank_name', $employee->bank_name) }}">
                </div>
                <div class="col-12 col-md-6 mt-3">
                    <label class="form-label fw-semibold">Bank Account Number</label>
                    <input type="text" name="bank_account_no" class="form-control" value="{{ old('bank_account_no', $employee->bank_account_no) }}">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('erp.employees.show', $employee) }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> Update Employee
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
