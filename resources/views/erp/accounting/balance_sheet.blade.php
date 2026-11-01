@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-bank text-primary me-2"></i> Balance Sheet (Statement of Financial Position)
        </h4>
        <div class="text-muted small">
            <span>{{ $company->name }}</span> &bull; Currency: <strong>BDT (৳)</strong>
        </div>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print
        </button>
        <a href="{{ route('erp.accounting.profit-and-loss') }}" class="btn btn-light btn-sm border">
            Profit & Loss (P&L)
        </a>
        <a href="{{ route('erp.accounting.trial-balance') }}" class="btn btn-light btn-sm border">
            Trial Balance
        </a>
    </div>
</div>

<div class="row g-4">
    {{-- Left: Assets --}}
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-primary">
                    <i class="bi bi-wallet2 me-2"></i> ASSETS
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th>Code</th>
                                <th>Asset Description</th>
                                <th class="text-end">Amount (BDT)</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($balanceSheet['assets'] as $asset)
                                <tr>
                                    <td class="font-monospace small text-muted">{{ $asset['code'] }}</td>
                                    <td>{{ $asset['name'] }}</td>
                                    <td class="text-end fw-semibold">৳ {{ number_format($asset['amount'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-3 text-muted">No assets found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                        <tfoot class="table-light border-top border-dark fw-bold fs-6">
                            <tr>
                                <td colspan="2" class="text-uppercase text-dark ps-3">Total Assets</td>
                                <td class="text-end text-primary font-monospace">৳ {{ number_format($balanceSheet['total_assets'], 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Right: Liabilities & Equity --}}
    <div class="col-12 col-lg-6">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <h5 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-shield-shaded me-2"></i> LIABILITIES & EQUITY
                </h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th>Code</th>
                                <th>Description</th>
                                <th class="text-end">Amount (BDT)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="table-light">
                                <th colspan="3" class="text-uppercase small fw-bold text-muted ps-3">1. Liabilities</th>
                            </tr>
                            @forelse($balanceSheet['liabilities'] as $liab)
                                <tr>
                                    <td class="font-monospace small text-muted">{{ $liab['code'] }}</td>
                                    <td>{{ $liab['name'] }}</td>
                                    <td class="text-end">৳ {{ number_format($liab['amount'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-2 text-muted">No liabilities.</td>
                                </tr>
                            @endforelse
                            <tr class="fw-semibold">
                                <td colspan="2" class="ps-3 text-muted">Total Liabilities</td>
                                <td class="text-end">৳ {{ number_format($balanceSheet['total_liabilities'], 2) }}</td>
                            </tr>

                            <tr class="table-light">
                                <th colspan="3" class="text-uppercase small fw-bold text-muted ps-3">2. Shareholders' Equity</th>
                            </tr>
                            @forelse($balanceSheet['equity'] as $eq)
                                <tr>
                                    <td class="font-monospace small text-muted">{{ $eq['code'] }}</td>
                                    <td>{{ $eq['name'] }}</td>
                                    <td class="text-end">৳ {{ number_format($eq['amount'], 2) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-2 text-muted">No equity records.</td>
                                </tr>
                            @endforelse
                            <tr class="fw-semibold">
                                <td colspan="2" class="ps-3 text-muted">Total Equity</td>
                                <td class="text-end">৳ {{ number_format($balanceSheet['total_equity'], 2) }}</td>
                            </tr>
                        </tbody>
                        <tfoot class="table-light border-top border-dark fw-bold fs-6">
                            <tr>
                                <td colspan="2" class="text-uppercase text-dark ps-3">Total Liabilities & Equity</td>
                                <td class="text-end text-dark font-monospace">৳ {{ number_format($balanceSheet['total_liabilities_and_equity'], 2) }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
