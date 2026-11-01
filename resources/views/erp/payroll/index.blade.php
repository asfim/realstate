@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-wallet2 text-primary me-2"></i> Monthly Staff Payroll & Compensation
        </h4>
        <div class="text-muted small">Salary Run Generation, Tax & Provident Fund Deductions, and GL Salary Vouchers</div>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#payrollModal">
            <i class="bi bi-gear-wide-connected me-1"></i> Generate Monthly Payroll
        </button>
        <a href="{{ route('erp.employees.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-people me-1"></i> Staff Directory
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th>Payroll Title & Period</th>
                        <th>Staff Count</th>
                        <th class="text-end">Total Gross</th>
                        <th class="text-end">Total Deductions</th>
                        <th class="text-end">Total Net Disbursed</th>
                        <th>Status</th>
                        <th class="text-end">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payrollRuns as $run)
                        <tr>
                            <td>
                                <a href="{{ route('erp.payroll.show', $run) }}" class="fw-bold text-decoration-none text-dark">
                                    {{ $run->title }}
                                </a>
                                <div class="text-muted small">Year: {{ $run->year }} &bull; Month: {{ date('F', mktime(0, 0, 0, $run->month, 10)) }}</div>
                            </td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-dark border">
                                    {{ $run->items_count }} Employees
                                </span>
                            </td>
                            <td class="text-end font-monospace">৳ {{ number_format($run->total_gross, 2) }}</td>
                            <td class="text-end font-monospace text-danger">-৳ {{ number_format($run->total_deductions, 2) }}</td>
                            <td class="text-end font-monospace fw-bold text-success fs-6">৳ {{ number_format($run->total_net, 2) }}</td>
                            <td>
                                <span class="badge bg-success">Approved & Paid</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('erp.payroll.show', $run) }}" class="btn btn-sm btn-light border" title="View Payslips">
                                    <i class="bi bi-eye"></i> Payslips
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No monthly payroll runs generated yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $payrollRuns->links() }}
</div>

{{-- Generate Payroll Modal --}}
<div class="modal fade" id="payrollModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('erp.payroll.generate') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="company_id" value="1">

            <div class="modal-header">
                <h5 class="modal-title fw-bold">Generate Monthly Payroll Run</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted small mb-3">
                    This wizard will automatically calculate monthly basic salary, housing, medical allowances, and tax/PF deductions for all active staff, and post the salary disbursement voucher to the General Ledger.
                </p>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Payroll Year <span class="text-danger">*</span></label>
                    <select name="year" class="form-select" required>
                        <option value="2026" selected>2026</option>
                        <option value="2027">2027</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Payroll Month <span class="text-danger">*</span></label>
                    <select name="month" class="form-select" required>
                        @for($m = 1; $m <= 12; $m++)
                            <option value="{{ $m }}" {{ date('n') == $m ? 'selected' : '' }}>
                                {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                            </option>
                        @endfor
                    </select>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i> Generate & Post to Accounts
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

