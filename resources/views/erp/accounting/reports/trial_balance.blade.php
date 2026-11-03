@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Trial Balance Statement</h4>
        <p class="text-muted small mb-0">General ledger trial balance: verification of equal Total Debits and Total Credits across all account classes.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.acc-reports.profit-loss') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-graph-up-arrow me-1"></i> Profit & Loss
        </a>
        <a href="{{ route('erp.acc-reports.balance-sheet') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-bank me-1"></i> Balance Sheet
        </a>
        <button type="button" class="btn btn-outline-dark btn-sm px-3" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print Statement
        </button>
    </div>
</div>

<!-- Date Filter Ribbon -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('erp.acc-reports.trial-balance') }}" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white">From:</span>
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
        </div>
        <div class="col-md-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white">To:</span>
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm w-100">Filter Period</button>
            <a href="{{ route('erp.acc-reports.trial-balance') }}" class="btn btn-light border btn-sm">All Time</a>
        </div>
    </form>
</div>

<!-- Balance Status Alert -->
@php
    $diff = abs($totalDebit - $totalCredit);
@endphp
@if($diff < 0.01)
    <div class="alert alert-success d-flex align-items-center gap-2 p-3 mb-4">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>
            <strong>Trial Balance In Equilibrium:</strong>
            Total Debits (৳ {{ number_format($totalDebit, 2) }}) exactly match Total Credits (৳ {{ number_format($totalCredit, 2) }}).
        </div>
    </div>
@else
    <div class="alert alert-danger d-flex align-items-center gap-2 p-3 mb-4">
        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
        <div>
            <strong>Trial Balance Out of Equilibrium:</strong>
            Discrepancy of ৳ {{ number_format($diff, 2) }}.
        </div>
    </div>
@endif

<!-- Trial Balance Table -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3" style="width: 15%;">Account Code</th>
                    <th style="width: 40%;">Account Name & Title</th>
                    <th style="width: 15%;">Account Class</th>
                    <th class="text-end" style="width: 15%;">Debit (BDT)</th>
                    <th class="text-end pe-3" style="width: 15%;">Credit (BDT)</th>
                </tr>
            </thead>
            <tbody>
                @forelse($trialRows as $row)
                    <tr>
                        <td class="ps-3">
                            <span class="badge bg-light text-dark border font-monospace">{{ $row['code'] }}</span>
                        </td>
                        <td class="fw-semibold text-dark">{{ $row['name'] }}</td>
                        <td><span class="badge bg-light text-muted border">{{ ucfirst($row['type']) }}</span></td>
                        <td class="text-end fw-semibold {{ $row['debit'] > 0 ? 'text-dark' : 'text-muted' }}">
                            {{ $row['debit'] > 0 ? '৳ ' . number_format($row['debit'], 2) : '—' }}
                        </td>
                        <td class="text-end pe-3 fw-semibold {{ $row['credit'] > 0 ? 'text-dark' : 'text-muted' }}">
                            {{ $row['credit'] > 0 ? '৳ ' . number_format($row['credit'], 2) : '—' }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-file-earmark-spreadsheet fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No posted transactions found in general ledger for this period.
                        </td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="3" class="text-end fw-bold fs-6">Grand Total Equilibrium:</td>
                    <td class="text-end fw-bold text-success fs-5">৳ {{ number_format($totalDebit, 2) }}</td>
                    <td class="text-end pe-3 fw-bold text-success fs-5">৳ {{ number_format($totalCredit, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection

