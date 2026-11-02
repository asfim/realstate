@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-0 fw-bold">Edit Payroll Record</h4>
        <p class="text-muted small mb-0">Modify salary record for {{ $payroll->staff_name }} ({{ $payroll->salary_month }})</p>
    </div>
    <a href="{{ route('admin.payrolls.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-1"></i> Back to Payroll
    </a>
</div>

<div class="card p-4 border-0 shadow-sm" style="max-width: 850px;">
    <form action="{{ route('admin.payrolls.update', $payroll->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="row g-3">
            <!-- Staff Name -->
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Staff Full Name <span class="text-danger">*</span></label>
                <input type="text" name="staff_name" id="staffName" class="form-control @error('staff_name') is-invalid @enderror" value="{{ old('staff_name', $payroll->staff_name) }}" required>
                @error('staff_name')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Staff Role / Designation -->
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Designation / Role</label>
                <input type="text" name="staff_role" id="staffRole" class="form-control @error('staff_role') is-invalid @enderror" value="{{ old('staff_role', $payroll->staff_role) }}">
                @error('staff_role')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Salary Month -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Salary Month <span class="text-danger">*</span></label>
                <input type="text" name="salary_month" class="form-control @error('salary_month') is-invalid @enderror" value="{{ old('salary_month', $payroll->salary_month) }}" required>
                @error('salary_month')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Payment Date -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Disbursement Date <span class="text-danger">*</span></label>
                <input type="date" name="payment_date" class="form-control @error('payment_date') is-invalid @enderror" value="{{ old('payment_date', $payroll->payment_date->format('Y-m-d')) }}" required>
                @error('payment_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Payment Method -->
            <div class="col-12 col-md-4">
                <label class="form-label fw-semibold">Payment Method <span class="text-danger">*</span></label>
                <select name="payment_method" class="form-select @error('payment_method') is-invalid @enderror" required>
                    <option value="bank_transfer" {{ old('payment_method', $payroll->payment_method) == 'bank_transfer' ? 'selected' : '' }}>Bank Wire Transfer</option>
                    <option value="cash" {{ old('payment_method', $payroll->payment_method) == 'cash' ? 'selected' : '' }}>Cash</option>
                    <option value="cheque" {{ old('payment_method', $payroll->payment_method) == 'cheque' ? 'selected' : '' }}>Bank Cheque</option>
                    <option value="mobile_banking" {{ old('payment_method', $payroll->payment_method) == 'mobile_banking' ? 'selected' : '' }}>Mobile Banking (bKash/Nagad)</option>
                </select>
                @error('payment_method')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Salary Breakdown Box -->
            <div class="col-12">
                <div class="p-3 bg-light rounded-3 border">
                    <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-calculator me-1 text-primary"></i> Salary Breakdown</h6>
                    <div class="row g-3">
                        <div class="col-12 col-md-3">
                            <label class="form-label small fw-semibold">Basic Salary ($) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" min="0" name="basic_salary" id="basicSalary" class="form-control @error('basic_salary') is-invalid @enderror" value="{{ old('basic_salary', $payroll->basic_salary) }}" required>
                            @error('basic_salary')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="form-label small fw-semibold text-success">+ Allowance / Bonus ($)</label>
                            <input type="number" step="0.01" min="0" name="allowance" id="allowance" class="form-control @error('allowance') is-invalid @enderror" value="{{ old('allowance', $payroll->allowance) }}">
                            @error('allowance')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="form-label small fw-semibold text-danger">- Deduction / Tax ($)</label>
                            <input type="number" step="0.01" min="0" name="deduction" id="deduction" class="form-control @error('deduction') is-invalid @enderror" value="{{ old('deduction', $payroll->deduction) }}">
                            @error('deduction')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12 col-md-3">
                            <label class="form-label small fw-bold text-dark">= Net Payable Salary</label>
                            <div class="form-control bg-white fw-bold fs-5 text-success" id="netSalaryDisplay">
                                ${{ number_format($payroll->net_salary, 2) }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Status -->
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Payment Status <span class="text-danger">*</span></label>
                <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                    <option value="paid" {{ old('status', $payroll->status) == 'paid' ? 'selected' : '' }}>Paid</option>
                    <option value="pending" {{ old('status', $payroll->status) == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="partial" {{ old('status', $payroll->status) == 'partial' ? 'selected' : '' }}>Partial</option>
                </select>
                @error('status')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Note -->
            <div class="col-12 col-md-6">
                <label class="form-label fw-semibold">Internal Remarks / Note</label>
                <textarea name="note" rows="2" class="form-control @error('note') is-invalid @enderror">{{ old('note', $payroll->note) }}</textarea>
                @error('note')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>

            <!-- Submit -->
            <div class="col-12 mt-4 text-end">
                <button type="submit" class="btn btn-gold px-4 py-2 fw-semibold">
                    <i class="bi bi-check-circle me-1"></i> Update Payroll
                </button>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    function calculateNetSalary() {
        const basic = parseFloat(document.getElementById('basicSalary').value) || 0;
        const allowance = parseFloat(document.getElementById('allowance').value) || 0;
        const deduction = parseFloat(document.getElementById('deduction').value) || 0;

        const net = Math.max(0, basic + allowance - deduction);
        document.getElementById('netSalaryDisplay').textContent = '$' + net.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
    }

    document.getElementById('basicSalary').addEventListener('input', calculateNetSalary);
    document.getElementById('allowance').addEventListener('input', calculateNetSalary);
    document.getElementById('deduction').addEventListener('input', calculateNetSalary);
</script>
@endpush

