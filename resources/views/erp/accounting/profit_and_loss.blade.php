@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-graph-up-arrow text-success me-2"></i> Profit & Loss (P&L) Statement
        </h4>
        <div class="text-muted small">
            <span>{{ $company->name }}</span> &bull; Currency: <strong>BDT (৳)</strong>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print P&L
        </button>
        <a href="{{ route('erp.accounting.trial-balance') }}" class="btn btn-light btn-sm border">
            Trial Balance
        </a>
        <a href="{{ route('erp.accounting.balance-sheet') }}" class="btn btn-light btn-sm border">
            Balance Sheet
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card shadow-sm border-0 mb-4 d-print-none">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('erp.accounting.profit-and-loss') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-3">
                <label class="form-label text-xs fw-semibold mb-1">Start Date</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
            </div>
            <div class="col-12 col-md-3">
                <label class="form-label text-xs fw-semibold mb-1">End Date</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
            </div>
            <div class="col-12 col-md-4">
                <label class="form-label text-xs fw-semibold mb-1">Project Cost Center</label>
                <select name="project_id" class="form-select form-select-sm">
                    <option value="">All Projects (Consolidated)</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ $projectId == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} ({{ $p->code }})
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-sm btn-primary w-100">
                    <i class="bi bi-filter me-1"></i> Generate
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Profit & Loss Financial Statement Card --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-bottom text-center">
        <h4 class="fw-bold mb-1 text-dark">{{ $company->name }}</h4>
        <h6 class="text-uppercase tracking-wider fw-bold text-muted mb-0">Statement of Profit or Loss</h6>
        <div class="text-muted small mt-1">
            @if($startDate || $endDate)
                Period: {{ $startDate ?? 'Beginning' }} to {{ $endDate ?? 'Present' }}
            @else
                Fiscal Year to Date (Cumulative)
            @endif
        </div>
    </div>

    <div class="card-body p-4">
        <div class="table-responsive">
            <table class="table table-sm align-middle">
                <tbody>
                    {{-- 1. OPERATING REVENUE --}}
                    <tr class="table-light">
                        <th colspan="2" class="text-uppercase fw-bold text-primary py-2">
                            <i class="bi bi-box-arrow-in-down-right me-1"></i> 1. Operating Revenue & Property Sales
                        </th>
                        <th class="text-end text-uppercase fw-bold text-primary py-2">Amount (BDT)</th>
                    </tr>
                    @forelse($pnl['revenues'] as $rev)
                        <tr>
                            <td class="ps-4 w-20 text-muted font-monospace">{{ $rev['code'] }}</td>
                            <td>{{ $rev['name'] }}</td>
                            <td class="text-end fw-semibold">৳ {{ number_format($rev['amount'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="ps-4 text-muted py-2">No revenue posted for this period.</td>
                        </tr>
                    @endforelse
                    <tr class="border-top border-dark fw-bold">
                        <td colspan="2" class="ps-4">Total Operating Revenue (A)</td>
                        <td class="text-end text-success fs-6">৳ {{ number_format($pnl['total_revenue'], 2) }}</td>
                    </tr>

                    {{-- 2. DIRECT PROJECT & CONSTRUCTION COSTS (COGS) --}}
                    <tr><td colspan="3" class="py-2"></td></tr>
                    <tr class="table-light">
                        <th colspan="2" class="text-uppercase fw-bold text-dark py-2">
                            <i class="bi bi-hammer me-1"></i> 2. Direct Project & Construction Cost
                        </th>
                        <th class="text-end text-uppercase fw-bold text-dark py-2">Amount (BDT)</th>
                    </tr>
                    @forelse($pnl['direct_costs'] as $dc)
                        <tr>
                            <td class="ps-4 w-20 text-muted font-monospace">{{ $dc['code'] }}</td>
                            <td>{{ $dc['name'] }}</td>
                            <td class="text-end text-danger">৳ {{ number_format($dc['amount'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="ps-4 text-muted py-2">No direct project costs recorded.</td>
                        </tr>
                    @endforelse
                    <tr class="border-top border-dark fw-bold">
                        <td colspan="2" class="ps-4">Total Direct Construction Cost (B)</td>
                        <td class="text-end text-danger fs-6">৳ {{ number_format($pnl['total_direct_cost'], 2) }}</td>
                    </tr>

                    {{-- GROSS PROFIT --}}
                    <tr class="table-warning border-top border-bottom border-dark fw-bold fs-6">
                        <td colspan="2">
                            <i class="bi bi-star-fill text-warning me-1"></i> GROSS PROFIT (A - B)
                        </td>
                        <td class="text-end {{ $pnl['gross_profit'] >= 0 ? 'text-dark' : 'text-danger' }}">
                            ৳ {{ number_format($pnl['gross_profit'], 2) }}
                        </td>
                    </tr>

                    {{-- 3. OPERATING & HR EXPENSES --}}
                    <tr><td colspan="3" class="py-2"></td></tr>
                    <tr class="table-light">
                        <th colspan="2" class="text-uppercase fw-bold text-dark py-2">
                            <i class="bi bi-people-fill me-1"></i> 3. Operating, Staff & Administrative Expenses
                        </th>
                        <th class="text-end text-uppercase fw-bold text-dark py-2">Amount (BDT)</th>
                    </tr>
                    @forelse($pnl['operating_expenses'] as $oe)
                        <tr>
                            <td class="ps-4 w-20 text-muted font-monospace">{{ $oe['code'] }}</td>
                            <td>{{ $oe['name'] }}</td>
                            <td class="text-end text-danger">৳ {{ number_format($oe['amount'], 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="ps-4 text-muted py-2">No operating expenses recorded.</td>
                        </tr>
                    @endforelse
                    <tr class="border-top border-dark fw-bold">
                        <td colspan="2" class="ps-4">Total Operating Expenses (C)</td>
                        <td class="text-end text-danger fs-6">৳ {{ number_format($pnl['total_operating_expense'], 2) }}</td>
                    </tr>

                    {{-- NET PROFIT / (LOSS) --}}
                    <tr><td colspan="3" class="py-2"></td></tr>
                    @php
                        $isProfit = $pnl['net_profit'] >= 0;
                    @endphp
                    <tr class="{{ $isProfit ? 'table-success' : 'table-danger' }} border-top border-bottom border-3 border-dark fw-bold fs-5">
                        <td colspan="2" class="py-3">
                            <i class="bi {{ $isProfit ? 'bi-trophy-fill text-success' : 'bi-exclamation-octagon-fill text-danger' }} me-2"></i>
                            NET {{ $isProfit ? 'PROFIT' : 'LOSS' }} FOR THE PERIOD
                        </td>
                        <td class="text-end py-3 {{ $isProfit ? 'text-success' : 'text-danger' }}">
                            ৳ {{ number_format($pnl['net_profit'], 2) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

