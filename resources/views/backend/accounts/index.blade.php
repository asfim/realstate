@extends('backend.layouts.app')

@push('styles')
<style>
    .fin-kpi-card {
        background: #fff;
        border: 1px solid rgba(226, 232, 240, 0.9);
        border-radius: 16px;
        padding: 22px 20px;
        height: 100%;
        transition: transform 0.25s ease, box-shadow 0.25s ease;
        position: relative;
        overflow: hidden;
    }
    .fin-kpi-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 25px -5px rgba(0,0,0,0.06);
    }
    .fin-icon-box {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.3rem;
        flex-shrink: 0;
    }
    .icon-income { background: rgba(16, 185, 129, 0.12); color: #059669; }
    .icon-expense { background: rgba(239, 68, 68, 0.12); color: #dc2626; }
    .icon-payroll { background: rgba(245, 158, 11, 0.12); color: #d97706; }
    .icon-balance { background: rgba(37, 99, 235, 0.12); color: #2563eb; }

    .fin-label {
        font-size: 0.78rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        margin-bottom: 4px;
    }
    .fin-value {
        font-size: 1.75rem;
        font-weight: 800;
        font-family: 'Outfit', sans-serif;
        line-height: 1.1;
    }
    .cat-progress-bar {
        height: 7px;
        border-radius: 6px;
        background: #f1f5f9;
        overflow: hidden;
        margin-top: 6px;
    }
</style>
@endpush

@section('content')

<!-- Header & Date Filter Row -->
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 page-title-row">
    <div>
        <h4 class="mb-0 fw-bold"><i class="bi bi-wallet2 text-warning me-2"></i> Financial Overview & Accounts</h4>
        <p class="text-muted small mb-0">Track real estate revenues, operational costs, staff salaries, and net profitability</p>
    </div>
    
    <div class="d-flex flex-wrap gap-2">
        @can('accounts.create')
            <a href="{{ route('admin.incomes.create') }}" class="btn btn-sm btn-success rounded-3 px-3 fw-semibold">
                <i class="bi bi-plus-circle me-1"></i> Add Income
            </a>
            <a href="{{ route('admin.expenses.create') }}" class="btn btn-sm btn-danger rounded-3 px-3 fw-semibold">
                <i class="bi bi-dash-circle me-1"></i> Add Expense
            </a>
        @endcan
        @can('payroll.create')
            <a href="{{ route('admin.payrolls.create') }}" class="btn btn-sm btn-gold rounded-3 px-3 fw-semibold">
                <i class="bi bi-cash-coin me-1"></i> Disburse Salary
            </a>
        @endcan
    </div>
</div>

<!-- Date Filter Form Card -->
<div class="card p-3 mb-4 shadow-sm border-0">
    <form action="{{ route('admin.accounts.index') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-12 col-sm-4 col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">From Date</label>
            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date', $startDate->format('Y-m-d')) }}">
        </div>
        <div class="col-12 col-sm-4 col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">To Date</label>
            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date', $endDate->format('Y-m-d')) }}">
        </div>
        <div class="col-12 col-sm-4 col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm px-3 flex-grow-1">
                <i class="bi bi-filter me-1"></i> Filter Period
            </button>
            <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary btn-sm" title="Reset to This Month">
                <i class="bi bi-arrow-counterclockwise"></i>
            </a>
        </div>
        <div class="col-12 col-md-3 text-md-end text-muted small">
            <span>Period: <strong>{{ $startDate->format('M d, Y') }}</strong> - <strong>{{ $endDate->format('M d, Y') }}</strong></span>
        </div>
    </form>
</div>

<!-- Primary Financial KPI Cards -->
<div class="row g-3 mb-4">
    <!-- Total Income -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="fin-kpi-card">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <div class="fin-label">Total Revenue / Income</div>
                    <div class="fin-value text-success">${{ number_format($totalIncome, 2) }}</div>
                </div>
                <div class="fin-icon-box icon-income">
                    <i class="bi bi-arrow-down-left-circle-fill"></i>
                </div>
            </div>
            <div class="text-muted small border-top pt-2 mt-2 d-flex justify-content-between">
                <span>Lifetime Income</span>
                <span class="fw-semibold text-dark">${{ number_format($lifetimeIncome, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Total Expense -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="fin-kpi-card">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <div class="fin-label">Total Expenses</div>
                    <div class="fin-value text-danger">${{ number_format($totalExpense, 2) }}</div>
                </div>
                <div class="fin-icon-box icon-expense">
                    <i class="bi bi-arrow-up-right-circle-fill"></i>
                </div>
            </div>
            <div class="text-muted small border-top pt-2 mt-2 d-flex justify-content-between">
                <span>Lifetime Expense</span>
                <span class="fw-semibold text-dark">${{ number_format($lifetimeExpense, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Staff Payroll Paid -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="fin-kpi-card">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <div class="fin-label">Staff Salaries Paid</div>
                    <div class="fin-value text-warning">${{ number_format($totalPayrollPaid, 2) }}</div>
                </div>
                <div class="fin-icon-box icon-payroll">
                    <i class="bi bi-cash-stack"></i>
                </div>
            </div>
            <div class="text-muted small border-top pt-2 mt-2 d-flex justify-content-between">
                <span>Lifetime Payroll</span>
                <span class="fw-semibold text-dark">${{ number_format($lifetimePayroll, 2) }}</span>
            </div>
        </div>
    </div>

    <!-- Net Profit / Cash Flow -->
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="fin-kpi-card">
            <div class="d-flex justify-content-between align-items-start mb-2">
                <div>
                    <div class="fin-label">Net Balance / Profit</div>
                    <div class="fin-value {{ $netBalance >= 0 ? 'text-primary' : 'text-danger' }}">
                        ${{ number_format($netBalance, 2) }}
                    </div>
                </div>
                <div class="fin-icon-box icon-balance">
                    <i class="bi bi-bank2"></i>
                </div>
            </div>
            <div class="text-muted small border-top pt-2 mt-2 d-flex justify-content-between">
                <span>Lifetime Balance</span>
                <span class="fw-semibold {{ $lifetimeBalance >= 0 ? 'text-success' : 'text-danger' }}">
                    ${{ number_format($lifetimeBalance, 2) }}
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Category Breakdown Row -->
<div class="row g-4 mb-4">
    <!-- Income Streams -->
    <div class="col-12 col-lg-6">
        <div class="card p-4 h-100 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-success"><i class="bi bi-pie-chart-fill me-2"></i> Income by Category</h6>
                <a href="{{ route('admin.incomes.index') }}" class="small text-decoration-none">All Incomes</a>
            </div>

            @if($incomeByCategory->count() > 0)
                <div class="d-flex flex-column gap-3">
                    @foreach($incomeByCategory as $inc)
                        @php
                            $pct = $totalIncome > 0 ? round(($inc->total / $totalIncome) * 100, 1) : 0;
                        @endphp
                        <div>
                            <div class="d-flex justify-content-between small fw-semibold">
                                <span>{{ $inc->category?->name ?? 'Uncategorized' }}</span>
                                <span>${{ number_format($inc->total, 2) }} <span class="text-muted fw-normal">({{ $pct }}%)</span></span>
                            </div>
                            <div class="cat-progress-bar">
                                <div class="bg-success h-100 rounded-pill" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 text-muted small">
                    <i class="bi bi-inbox fs-2 mb-2 d-block opacity-50"></i>
                    No income records in this period.
                </div>
            @endif
        </div>
    </div>

    <!-- Expense Breakdown -->
    <div class="col-12 col-lg-6">
        <div class="card p-4 h-100 shadow-sm border-0">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-danger"><i class="bi bi-pie-chart-fill me-2"></i> Expenses by Category</h6>
                <a href="{{ route('admin.expenses.index') }}" class="small text-decoration-none">All Expenses</a>
            </div>

            @if($expenseByCategory->count() > 0)
                <div class="d-flex flex-column gap-3">
                    @foreach($expenseByCategory as $exp)
                        @php
                            $pct = $totalExpense > 0 ? round(($exp->total / $totalExpense) * 100, 1) : 0;
                        @endphp
                        <div>
                            <div class="d-flex justify-content-between small fw-semibold">
                                <span>{{ $exp->category?->name ?? 'Uncategorized' }}</span>
                                <span>${{ number_format($exp->total, 2) }} <span class="text-muted fw-normal">({{ $pct }}%)</span></span>
                            </div>
                            <div class="cat-progress-bar">
                                <div class="bg-danger h-100 rounded-pill" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-4 text-muted small">
                    <i class="bi bi-inbox fs-2 mb-2 d-block opacity-50"></i>
                    No expense records in this period.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Recent Transactions & Payroll Disbursals -->
<div class="row g-4">
    <!-- Transactions List -->
    <div class="col-12 col-xl-7">
        <div class="card p-4 shadow-sm border-0 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-receipt-cutoff me-2 text-primary"></i> Recent Transactions</h6>
                <div class="d-flex gap-2">
                    <a href="{{ route('admin.incomes.index') }}" class="btn btn-outline-success btn-sm py-0 px-2 small">Incomes</a>
                    <a href="{{ route('admin.expenses.index') }}" class="btn btn-outline-danger btn-sm py-0 px-2 small">Expenses</a>
                </div>
            </div>

            @if($recentTransactions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Type</th>
                                <th>Title / Category</th>
                                <th>Date</th>
                                <th>Amount</th>
                                <th class="text-end">Method</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentTransactions as $tx)
                                <tr>
                                    <td>
                                        @if($tx->type === 'income')
                                            <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">Income</span>
                                        @else
                                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle rounded-pill">Expense</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $tx->title }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">
                                            {{ $tx->category?->name ?? 'General' }}
                                            @if($tx->reference_no) &bull; Ref: {{ $tx->reference_no }} @endif
                                        </div>
                                    </td>
                                    <td class="text-muted">{{ $tx->transaction_date->format('M d, Y') }}</td>
                                    <td>
                                        <span class="fw-bold {{ $tx->type === 'income' ? 'text-success' : 'text-danger' }}">
                                            {{ $tx->type === 'income' ? '+' : '-' }}${{ number_format($tx->amount, 2) }}
                                        </span>
                                    </td>
                                    <td class="text-end text-muted text-capitalize">
                                        <span class="badge bg-light text-dark border">{{ str_replace('_', ' ', $tx->payment_method) }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted small">
                    <i class="bi bi-credit-card-2-front fs-1 mb-2 d-block opacity-40"></i>
                    No transactions recorded yet.
                </div>
            @endif
        </div>
    </div>

    <!-- Recent Staff Payrolls -->
    <div class="col-12 col-xl-5">
        <div class="card p-4 shadow-sm border-0 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0"><i class="bi bi-people-fill me-2 text-warning"></i> Recent Staff Salary</h6>
                <a href="{{ route('admin.payrolls.index') }}" class="btn btn-outline-secondary btn-sm py-0 px-2 small">Manage Payroll</a>
            </div>

            @if($recentPayrolls->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover align-middle small mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Staff Name</th>
                                <th>Month</th>
                                <th>Net Salary</th>
                                <th class="text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($recentPayrolls as $pr)
                                <tr>
                                    <td>
                                        <div class="fw-semibold text-dark">{{ $pr->staff_name }}</div>
                                        <div class="text-muted" style="font-size: 0.72rem;">{{ $pr->staff_role ?? 'Staff' }}</div>
                                    </td>
                                    <td class="text-muted">{{ $pr->salary_month }}</td>
                                    <td>
                                        <span class="fw-bold text-dark">${{ number_format($pr->net_salary, 2) }}</span>
                                    </td>
                                    <td class="text-end">
                                        @if($pr->status === 'paid')
                                            <span class="badge bg-success-subtle text-success rounded-pill">Paid</span>
                                        @elseif($pr->status === 'partial')
                                            <span class="badge bg-warning-subtle text-warning rounded-pill">Partial</span>
                                        @else
                                            <span class="badge bg-secondary-subtle text-secondary rounded-pill">Pending</span>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-5 text-muted small">
                    <i class="bi bi-cash fs-1 mb-2 d-block opacity-40"></i>
                    No salary disbursed yet.
                    @can('payroll.create')
                        <div class="mt-2">
                            <a href="{{ route('admin.payrolls.create') }}" class="btn btn-sm btn-gold">Disburse First Salary</a>
                        </div>
                    @endcan
                </div>
            @endif
        </div>
    </div>
</div>

@endsection

