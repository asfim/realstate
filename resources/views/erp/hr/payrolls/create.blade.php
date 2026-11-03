@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.hr-payrolls.index') }}" class="text-decoration-none">Payroll</a></li>
                <li class="breadcrumb-item active" aria-current="page">Process Monthly Payroll</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-1 text-dark">Process Monthly Salary Voucher</h4>
        <p class="text-muted small mb-0">Compute employee gross earnings, bonuses, provident/advance deductions, and generate official payroll record.</p>
    </div>
    <div>
        <a href="{{ route('erp.hr-payrolls.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Payrolls
        </a>
    </div>
</div>

<div class="card card-custom p-4">
    <form action="{{ route('erp.hr-payrolls.store') }}" method="POST">
        @csrf

        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-person-check-fill text-erp-gold me-2"></i> Employee & Salary Period
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label class="form-label fw-semibold">Select Employee <span class="text-danger">*</span></label>
                <select name="employee_id" id="employee_select" class="form-select form-select-sm" required>
                    <option value="">-- Choose Active Employee --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" 
                            data-gross="{{ $emp->gross_salary }}"
                            data-basic="{{ $emp->basic_salary }}"
                            data-bank="{{ $emp->bank_name }}"
                            data-acc="{{ $emp->bank_account_no }}"
                            {{ (old('employee_id', $selectedEmp?->id) == $emp->id) ? 'selected' : '' }}>
                            {{ $emp->name }} ({{ $emp->employee_code }}) - ৳ {{ number_format($emp->gross_salary, 2) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Salary Month <span class="text-danger">*</span></label>
                <select name="salary_month" class="form-select form-select-sm" required>
                    @for($m = 1; $m <= 12; $m++)
                        <option value="{{ $m }}" {{ old('salary_month', date('n')) == $m ? 'selected' : '' }}>
                            {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-semibold">Salary Year <span class="text-danger">*</span></label>
                <select name="salary_year" class="form-select form-select-sm" required>
                    @for($y = date('Y') + 1; $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ old('salary_year', date('Y')) == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endfor
                </select>
            </div>
        </div>

        <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
            <i class="bi bi-calculator text-success me-2"></i> Earnings, Adjustments & Net Payable
        </h6>
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label class="form-label fw-semibold">Gross Salary (Base)</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text">৳</span>
                    <input type="number" step="0.01" id="gross_salary_input" class="form-control" value="0.00" readonly>
                </div>
                <div class="form-text small text-muted">Fixed based on employee salary structure.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Festival / Performance Bonus</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text text-success">+ ৳</span>
                    <input type="number" step="0.01" name="bonus" id="bonus_input" class="form-control" value="{{ old('bonus', '0.00') }}" min="0">
                </div>
                <div class="form-text small text-muted">Eid bonus, target incentives, etc.</div>
            </div>
            <div class="col-md-4">
                <label class="form-label fw-semibold">Deductions / Advance</label>
                <div class="input-group input-group-sm">
                    <span class="input-group-text text-danger">- ৳</span>
                    <input type="number" step="0.01" name="deductions" id="deductions_input" class="form-control" value="{{ old('deductions', '0.00') }}" min="0">
                </div>
                <div class="form-text small text-muted">Advance salary recovery, unpaid leave, etc.</div>
            </div>

            <div class="col-12 mt-4">
                <div class="p-3 bg-light rounded border d-flex justify-content-between align-items-center">
                    <div>
                        <div class="fw-bold text-dark">Net Payable Amount</div>
                        <div class="small text-muted" id="calc_formula">Formula: Gross Salary + Bonus - Deductions</div>
                    </div>
                    <div class="text-end">
                        <span class="fs-4 fw-bold text-success" id="net_payable_display">৳ 0.00</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="d-flex justify-content-end gap-2">
            <a href="{{ route('erp.hr-payrolls.index') }}" class="btn btn-light btn-sm px-4">Cancel</a>
            <button type="submit" class="btn btn-erp btn-sm px-4">
                <i class="bi bi-file-earmark-check me-1"></i> Generate Payroll
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const empSelect = document.getElementById('employee_select');
    const grossInput = document.getElementById('gross_salary_input');
    const bonusInput = document.getElementById('bonus_input');
    const dedInput = document.getElementById('deductions_input');
    const netDisplay = document.getElementById('net_payable_display');

    function calculateNet() {
        const selected = empSelect.options[empSelect.selectedIndex];
        const gross = parseFloat(selected ? selected.getAttribute('data-gross') : 0) || 0;
        grossInput.value = gross.toFixed(2);

        const bonus = parseFloat(bonusInput.value) || 0;
        const ded = parseFloat(dedInput.value) || 0;
        const net = Math.max(0, gross + bonus - ded);

        netDisplay.innerText = '৳ ' + net.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    empSelect.addEventListener('change', calculateNet);
    bonusInput.addEventListener('input', calculateNet);
    dedInput.addEventListener('input', calculateNet);

    calculateNet();
});
</script>
@endsection

