@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Staff Payroll & Monthly Disbursements</h4>
        <p class="text-muted small mb-0">Monthly salary processing, bonus additions, deductions, net payable calculations, and payment vouchers.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('erp.hr-payrolls.create') }}" class="btn btn-erp btn-sm px-3">
            <i class="bi bi-plus-lg me-1"></i> Process Monthly Payroll
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('erp.hr-payrolls.index') }}" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small text-muted mb-1">Month</label>
            <select name="salary_month" class="form-select form-select-sm">
                <option value="">All Months</option>
                @for($m = 1; $m <= 12; $m++)
                    <option value="{{ $m }}" {{ request('salary_month') == $m ? 'selected' : '' }}>
                        {{ date('F', mktime(0, 0, 0, $m, 10)) }}
                    </option>
                @endfor
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted mb-1">Year</label>
            <select name="salary_year" class="form-select form-select-sm">
                <option value="">All Years</option>
                @for($y = date('Y'); $y >= 2024; $y--)
                    <option value="{{ $y }}" {{ request('salary_year') == $y ? 'selected' : '' }}>{{ $y }}</option>
                @endfor
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted mb-1">Disbursement Status</label>
            <select name="payment_status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="unpaid" {{ request('payment_status') == 'unpaid' ? 'selected' : '' }}>Unpaid / Pending</option>
                <option value="paid" {{ request('payment_status') == 'paid' ? 'selected' : '' }}>Disbursed / Paid</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-outline-secondary btn-sm flex-fill">
                <i class="bi bi-funnel me-1"></i> Filter
            </button>
            <a href="{{ route('erp.hr-payrolls.index') }}" class="btn btn-light btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- Payroll Table -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase text-muted">
                <tr>
                    <th>Payroll Code</th>
                    <th>Employee</th>
                    <th>Department</th>
                    <th>Salary Period</th>
                    <th class="text-end">Gross</th>
                    <th class="text-end">Bonus</th>
                    <th class="text-end">Deductions</th>
                    <th class="text-end">Net Payable</th>
                    <th class="text-center">Status</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($payrolls as $pay)
                    <tr>
                        <td class="fw-bold">{{ $pay->payroll_code }}</td>
                        <td>
                            <a href="{{ route('erp.hr-employees.show', $pay->employee) }}" class="fw-semibold text-decoration-none text-dark">
                                {{ $pay->employee?->name }}
                            </a>
                            <div class="text-muted small">{{ $pay->employee?->employee_code }} &bull; {{ $pay->employee?->designation?->name }}</div>
                        </td>
                        <td>{{ $pay->employee?->department?->name ?? '—' }}</td>
                        <td>
                            <span class="badge bg-light text-dark border">
                                {{ date('F', mktime(0, 0, 0, $pay->salary_month, 10)) }} {{ $pay->salary_year }}
                            </span>
                        </td>
                        <td class="text-end">৳ {{ number_format($pay->gross_salary, 2) }}</td>
                        <td class="text-end text-success">+ ৳ {{ number_format($pay->bonus, 2) }}</td>
                        <td class="text-end text-danger">- ৳ {{ number_format($pay->deductions, 2) }}</td>
                        <td class="text-end fw-bold text-dark fs-6">৳ {{ number_format($pay->net_payable, 2) }}</td>
                        <td class="text-center">
                            @if($pay->payment_status === 'paid')
                                <span class="badge bg-success">
                                    <i class="bi bi-check-circle me-1"></i> Paid
                                </span>
                            @else
                                <span class="badge bg-warning text-dark">
                                    <i class="bi bi-clock me-1"></i> Pending
                                </span>
                            @endif
                        </td>
                        <td class="text-end">
                            <a href="{{ route('erp.hr-payrolls.show', $pay) }}" class="btn btn-outline-erp btn-sm">
                                <i class="bi bi-receipt me-1"></i> Payslip
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="10" class="text-center py-5 text-muted">
                            <i class="bi bi-cash-coin display-6 text-muted d-block mb-2"></i>
                            No payroll records found for this criteria. Click "Process Monthly Payroll" to generate one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($payrolls->hasPages())
        <div class="card-footer bg-transparent py-2">
            {{ $payrolls->links() }}
        </div>
    @endif
</div>
@endsection

