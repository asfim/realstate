@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4 d-print-none">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.hr-payrolls.index') }}" class="text-decoration-none">Payroll</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $hrPayroll->payroll_code }}</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <h4 class="fw-bold mb-0 text-dark">Salary Voucher / Payslip</h4>
            <span class="badge bg-light text-dark border">{{ $hrPayroll->payroll_code }}</span>
            <span class="badge {{ $hrPayroll->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                {{ ucfirst($hrPayroll->payment_status) }}
            </span>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-printer me-1"></i> Print Payslip
        </button>
        @if($hrPayroll->payment_status !== 'paid')
            <button type="button" class="btn btn-success btn-sm px-3" data-bs-toggle="modal" data-bs-target="#disburseModal">
                <i class="bi bi-cash-stack me-1"></i> Disburse Salary
            </button>
        @endif
        <a href="{{ route('erp.hr-payrolls.index') }}" class="btn btn-light btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<!-- Payslip Card / Printable Document -->
<div class="card card-custom p-4 p-md-5 print-voucher" style="max-width: 900px; margin: 0 auto; background: #fff;">
    <!-- Document Header -->
    <div class="d-flex justify-content-between align-items-start border-bottom pb-4 mb-4">
        <div>
            <h3 class="fw-bold text-erp mb-0">REAL ESTATE ENTERPRISE ERP</h3>
            <div class="text-muted small">Dhaka, Bangladesh &bull; Official Employee Salary Payslip</div>
            <div class="text-muted small">Branch: {{ $hrPayroll->employee?->branch?->name ?? 'Head Office' }}</div>
        </div>
        <div class="text-end">
            <h5 class="fw-bold text-dark mb-1">SALARY PAYSLIP</h5>
            <div class="small fw-semibold text-muted">Voucher: <span class="text-dark">{{ $hrPayroll->payroll_code }}</span></div>
            <div class="small fw-semibold text-muted">Period: <span class="text-dark">{{ date('F', mktime(0, 0, 0, $hrPayroll->salary_month, 10)) }} {{ $hrPayroll->salary_year }}</span></div>
        </div>
    </div>

    <!-- Employee Profile Info -->
    <div class="row g-3 mb-4 p-3 bg-light rounded">
        <div class="col-6 col-md-3">
            <div class="text-muted small">Employee Name</div>
            <div class="fw-bold text-dark">{{ $hrPayroll->employee?->name }}</div>
        </div>
        <div class="col-6 col-md-3">
            <div class="text-muted small">Employee ID</div>
            <div class="fw-semibold text-dark">{{ $hrPayroll->employee?->employee_code }}</div>
        </div>
        <div class="col-6 col-md-3">
            <div class="text-muted small">Designation</div>
            <div class="fw-semibold text-dark">{{ $hrPayroll->employee?->designation?->name ?? '—' }}</div>
        </div>
        <div class="col-6 col-md-3">
            <div class="text-muted small">Department</div>
            <div class="fw-semibold text-dark">{{ $hrPayroll->employee?->department?->name ?? '—' }}</div>
        </div>
        <div class="col-6 col-md-3">
            <div class="text-muted small">Joining Date</div>
            <div class="fw-semibold text-dark">{{ $hrPayroll->employee?->joining_date?->format('d M, Y') ?? '—' }}</div>
        </div>
        <div class="col-6 col-md-3">
            <div class="text-muted small">National ID (NID)</div>
            <div class="fw-semibold text-dark">{{ $hrPayroll->employee?->nid_no ?? '—' }}</div>
        </div>
        <div class="col-6 col-md-3">
            <div class="text-muted small">Bank Name</div>
            <div class="fw-semibold text-dark">{{ $hrPayroll->employee?->bank_name ?? 'Cash / Hand' }}</div>
        </div>
        <div class="col-6 col-md-3">
            <div class="text-muted small">Account Number</div>
            <div class="fw-semibold text-dark">{{ $hrPayroll->employee?->bank_account_no ?? '—' }}</div>
        </div>
    </div>

    <!-- Earnings & Deductions Tables -->
    <div class="row g-4 mb-4">
        <!-- Earnings -->
        <div class="col-md-6">
            <div class="border rounded p-3 h-100">
                <h6 class="fw-bold text-success border-bottom pb-2 mb-3">Earnings</h6>
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Basic Salary:</span>
                    <span class="fw-semibold">৳ {{ number_format($hrPayroll->employee?->basic_salary ?? 0, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">House Rent Allowance:</span>
                    <span class="fw-semibold">৳ {{ number_format($hrPayroll->employee?->house_rent_allowance ?? 0, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Medical Allowance:</span>
                    <span class="fw-semibold">৳ {{ number_format($hrPayroll->employee?->medical_allowance ?? 0, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Conveyance Allowance:</span>
                    <span class="fw-semibold">৳ {{ number_format($hrPayroll->employee?->conveyance_allowance ?? 0, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Festival / Performance Bonus:</span>
                    <span class="fw-semibold text-success">+ ৳ {{ number_format($hrPayroll->bonus, 2) }}</span>
                </div>
                <div class="border-top pt-2 d-flex justify-content-between fw-bold text-dark mt-3">
                    <span>Total Gross Earnings:</span>
                    <span>৳ {{ number_format($hrPayroll->gross_salary + $hrPayroll->bonus, 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Deductions -->
        <div class="col-md-6">
            <div class="border rounded p-3 h-100">
                <h6 class="fw-bold text-danger border-bottom pb-2 mb-3">Deductions</h6>
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Tax / Advance / Fine:</span>
                    <span class="fw-semibold text-danger">৳ {{ number_format($hrPayroll->deductions, 2) }}</span>
                </div>
                <div class="d-flex justify-content-between mb-2 small">
                    <span class="text-muted">Unpaid Leaves:</span>
                    <span class="fw-semibold text-muted">৳ 0.00</span>
                </div>
                <div class="border-top pt-2 d-flex justify-content-between fw-bold text-danger mt-auto">
                    <span>Total Deductions:</span>
                    <span>৳ {{ number_format($hrPayroll->deductions, 2) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Net Pay Highlight -->
    <div class="p-3 bg-light rounded border border-2 border-secondary mb-5 d-flex justify-content-between align-items-center">
        <div>
            <div class="fw-bold fs-5 text-dark">NET PAYABLE AMOUNT:</div>
            <div class="small text-muted">
                Status: <strong>{{ strtoupper($hrPayroll->payment_status) }}</strong>
                @if($hrPayroll->payment_status === 'paid')
                    &bull; Paid on {{ $hrPayroll->payment_date?->format('d M, Y') }} via {{ strtoupper(str_replace('_', ' ', $hrPayroll->payment_method)) }}
                @endif
            </div>
        </div>
        <div class="text-end">
            <div class="fs-3 fw-bold text-success">৳ {{ number_format($hrPayroll->net_payable, 2) }}</div>
        </div>
    </div>

    <!-- Signatures -->
    <div class="row g-4 pt-5 mt-4 text-center border-top">
        <div class="col-4">
            <div class="border-top border-dark pt-2 small text-muted">Prepared By (HR)</div>
        </div>
        <div class="col-4">
            <div class="border-top border-dark pt-2 small text-muted">Employee Signature</div>
        </div>
        <div class="col-4">
            <div class="border-top border-dark pt-2 small text-muted">Managing Director / CFO</div>
        </div>
    </div>
</div>

<!-- Modal: Disburse Salary -->
@if($hrPayroll->payment_status !== 'paid')
<div class="modal fade" id="disburseModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('erp.hr-payrolls.pay', $hrPayroll) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Disburse Salary Payment</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p class="small text-muted mb-3">
                        Mark salary voucher <strong>{{ $hrPayroll->payroll_code }}</strong> for <strong>{{ $hrPayroll->employee?->name }}</strong> as disbursed.
                    </p>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Payment Amount</label>
                        <input type="text" class="form-control form-control-sm fw-bold" value="৳ {{ number_format($hrPayroll->net_payable, 2) }}" readonly>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Disbursement Date <span class="text-danger">*</span></label>
                        <input type="date" name="payment_date" class="form-control form-control-sm" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Payment Method <span class="text-danger">*</span></label>
                        <select name="payment_method" class="form-select form-select-sm" required>
                            <option value="bank_transfer">Bank Transfer / BEFTN / NPSB</option>
                            <option value="cheque">Bank Cheque</option>
                            <option value="cash">Cash in Hand</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success btn-sm px-4">
                        <i class="bi bi-check2-circle me-1"></i> Confirm Disbursement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .print-voucher, .print-voucher * {
        visibility: visible;
    }
    .print-voucher {
        position: absolute;
        left: 0;
        top: 0;
        width: 100%;
        max-width: 100% !important;
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
    }
}
</style>
@endsection

