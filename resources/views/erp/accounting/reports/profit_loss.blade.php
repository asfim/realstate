@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Profit & Loss (Income Statement)</h4>
        <p class="text-muted small mb-0">Operational real estate earnings statement: Revenue (4000s) minus Expenses (5000s) equals Net Operating Income.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.acc-reports.trial-balance') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-file-earmark-bar-graph me-1"></i> Trial Balance
        </a>
        <a href="{{ route('erp.acc-reports.balance-sheet') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-bank me-1"></i> Balance Sheet
        </a>
        <button type="button" class="btn btn-outline-dark btn-sm px-3" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print Statement
        </button>
    </div>
</div>

<!-- Executive Summary Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-4">
        <div class="card card-custom p-3 border-start border-4 border-success h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Total Revenue & Inflow</div>
            <div class="fs-4 fw-bold text-success mt-1">৳ {{ number_format($totalRevenue, 2) }}</div>
            <div class="text-muted extra-small mt-1">{{ count($revenues) }} Revenue Streams</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-3 border-start border-4 border-danger h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Total Operating Expenses</div>
            <div class="fs-4 fw-bold text-danger mt-1">৳ {{ number_format($totalExpense, 2) }}</div>
            <div class="text-muted extra-small mt-1">{{ count($expenses) }} Cost Centres</div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card card-custom p-3 border-start border-4 {{ $netProfit >= 0 ? 'border-primary' : 'border-danger' }} h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Net Operating Profit / (Loss)</div>
            <div class="fs-4 fw-bold {{ $netProfit >= 0 ? 'text-primary' : 'text-danger' }} mt-1">৳ {{ number_format($netProfit, 2) }}</div>
            <div class="text-muted extra-small mt-1">
                @if($totalRevenue > 0)
                    {{ round(($netProfit / $totalRevenue) * 100, 1) }}% Profit Margin
                @else
                    0% Margin
                @endif
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Revenue Section -->
    <div class="col-lg-6">
        <div class="card card-custom h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-success">
                    <i class="bi bi-arrow-up-circle-fill me-2"></i> Operating Revenues (4000s)
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th class="ps-3">Code</th>
                            <th>Revenue Head</th>
                            <th class="text-end pe-3">Amount (BDT)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($revenues as $rev)
                            <tr>
                                <td class="ps-3"><span class="badge bg-light text-dark border font-monospace">{{ $rev['code'] }}</span></td>
                                <td class="fw-semibold text-dark">{{ $rev['name'] }}</td>
                                <td class="text-end pe-3 fw-bold text-success">৳ {{ number_format($rev['amount'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">No revenue booked in period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2" class="text-end fw-bold">Total Operating Revenue:</td>
                            <td class="text-end pe-3 fw-bold text-success fs-6">৳ {{ number_format($totalRevenue, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Expenses Section -->
    <div class="col-lg-6">
        <div class="card card-custom h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-danger">
                    <i class="bi bi-arrow-down-circle-fill me-2"></i> Operating Expenses (5000s)
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th class="ps-3">Code</th>
                            <th>Expense Head</th>
                            <th class="text-end pe-3">Amount (BDT)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($expenses as $exp)
                            <tr>
                                <td class="ps-3"><span class="badge bg-light text-dark border font-monospace">{{ $exp['code'] }}</span></td>
                                <td class="fw-semibold text-dark">{{ $exp['name'] }}</td>
                                <td class="text-end pe-3 fw-bold text-danger">৳ {{ number_format($exp['amount'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">No expenses booked in period.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2" class="text-end fw-bold">Total Operating Expenses:</td>
                            <td class="text-end pe-3 fw-bold text-danger fs-6">৳ {{ number_format($totalExpense, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

