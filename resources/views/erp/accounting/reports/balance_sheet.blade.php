@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Balance Sheet (Financial Position)</h4>
        <p class="text-muted small mb-0">Fundamental accounting equation: Total Assets (1000s) = Total Liabilities (2000s) + Total Equity (3000s).</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.acc-reports.trial-balance') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-file-earmark-bar-graph me-1"></i> Trial Balance
        </a>
        <a href="{{ route('erp.acc-reports.profit-loss') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-graph-up-arrow me-1"></i> Profit & Loss
        </a>
        <button type="button" class="btn btn-outline-dark btn-sm px-3" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print Statement
        </button>
    </div>
</div>

@php
    $totalLiabilitiesAndEquity = $totalLiabilities + $totalEquity;
    $balanceDiff = abs($totalAssets - $totalLiabilitiesAndEquity);
@endphp

<!-- Balance Status Alert -->
@if($balanceDiff < 0.01)
    <div class="alert alert-success d-flex align-items-center gap-2 p-3 mb-4">
        <i class="bi bi-check-circle-fill fs-5"></i>
        <div>
            <strong>Balance Sheet In Equilibrium:</strong>
            Total Assets (৳ {{ number_format($totalAssets, 2) }}) equals Total Liabilities & Equity (৳ {{ number_format($totalLiabilitiesAndEquity, 2) }}).
        </div>
    </div>
@else
    <div class="alert alert-warning d-flex align-items-center gap-2 p-3 mb-4">
        <i class="bi bi-info-circle-fill fs-5"></i>
        <div>
            <strong>Unclosed Fiscal Period:</strong>
            Net Period Difference of ৳ {{ number_format($balanceDiff, 2) }} will be transferred to Retained Earnings upon Fiscal Period Year-End Close.
        </div>
    </div>
@endif

<div class="row g-4 mb-4">
    <!-- Assets Section -->
    <div class="col-lg-6">
        <div class="card card-custom h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-wallet2 me-2"></i> Current & Fixed Assets (1000s)
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th class="ps-3">Code</th>
                            <th>Asset Account Head</th>
                            <th class="text-end pe-3">Net Balance (BDT)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($assets as $a)
                            <tr>
                                <td class="ps-3"><span class="badge bg-light text-dark border font-monospace">{{ $a['code'] }}</span></td>
                                <td class="fw-semibold text-dark">{{ $a['name'] }}</td>
                                <td class="text-end pe-3 fw-bold text-dark">৳ {{ number_format($a['amount'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-4 text-muted">No assets with non-zero balances.</td>
                            </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2" class="text-end fw-bold fs-6">Total Assets:</td>
                            <td class="text-end pe-3 fw-bold text-primary fs-5">৳ {{ number_format($totalAssets, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>

    <!-- Liabilities & Equity Section -->
    <div class="col-lg-6">
        <div class="card card-custom h-100">
            <div class="card-header bg-white py-3">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-bank me-2"></i> Liabilities & Shareholder Equity (2000s & 3000s)
                </h6>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th class="ps-3">Code</th>
                            <th>Liabilities & Equity Head</th>
                            <th class="text-end pe-3">Net Balance (BDT)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="table-light">
                            <td colspan="3" class="ps-3 fw-bold text-danger text-uppercase small">Liabilities (2000s)</td>
                        </tr>
                        @forelse($liabilities as $l)
                            <tr>
                                <td class="ps-3"><span class="badge bg-light text-dark border font-monospace">{{ $l['code'] }}</span></td>
                                <td class="fw-semibold text-dark">{{ $l['name'] }}</td>
                                <td class="text-end pe-3 fw-bold text-danger">৳ {{ number_format($l['amount'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-2 text-muted small">No active liabilities.</td>
                            </tr>
                        @endforelse
                        <tr>
                            <td colspan="2" class="text-end fw-semibold small text-muted">Total Liabilities:</td>
                            <td class="text-end pe-3 fw-bold text-danger">৳ {{ number_format($totalLiabilities, 2) }}</td>
                        </tr>

                        <tr class="table-light">
                            <td colspan="3" class="ps-3 fw-bold text-info text-uppercase small">Shareholders Equity (3000s)</td>
                        </tr>
                        @forelse($equities as $e)
                            <tr>
                                <td class="ps-3"><span class="badge bg-light text-dark border font-monospace">{{ $e['code'] }}</span></td>
                                <td class="fw-semibold text-dark">{{ $e['name'] }}</td>
                                <td class="text-end pe-3 fw-bold text-info">৳ {{ number_format($e['amount'], 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center py-2 text-muted small">No equity heads recorded.</td>
                            </tr>
                        @endforelse
                        <tr>
                            <td colspan="2" class="text-end fw-semibold small text-muted">Total Equity:</td>
                            <td class="text-end pe-3 fw-bold text-info">৳ {{ number_format($totalEquity, 2) }}</td>
                        </tr>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="2" class="text-end fw-bold fs-6">Total Liabilities & Equity:</td>
                            <td class="text-end pe-3 fw-bold text-dark fs-5">৳ {{ number_format($totalLiabilitiesAndEquity, 2) }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

