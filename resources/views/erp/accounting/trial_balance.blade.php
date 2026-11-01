@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-scale text-primary me-2"></i> General Ledger Trial Balance
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
        <a href="{{ route('erp.accounting.balance-sheet') }}" class="btn btn-light btn-sm border">
            Balance Sheet
        </a>
    </div>
</div>

@if($trialBalance['is_balanced'])
    <div class="alert alert-success d-flex align-items-center mb-4 shadow-sm border-0">
        <i class="bi bi-check-circle-fill fs-4 me-3"></i>
        <div>
            <strong>Trial Balance is in Perfect Equilibrium!</strong> Total Debits equal Total Credits (৳ {{ number_format($trialBalance['total_debit'], 2) }}).
        </div>
    </div>
@else
    <div class="alert alert-danger d-flex align-items-center mb-4 shadow-sm border-0">
        <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
        <div>
            <strong>Out of Balance Warning:</strong> Total Debits (৳ {{ number_format($trialBalance['total_debit'], 2) }}) do not match Total Credits (৳ {{ number_format($trialBalance['total_credit'], 2) }}).
        </div>
    </div>
@endif

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th class="w-15">Account Code</th>
                        <th>Account Description</th>
                        <th>Classification</th>
                        <th class="text-end w-20">Debit (BDT)</th>
                        <th class="text-end w-20">Credit (BDT)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($trialBalance['rows'] as $row)
                        <tr>
                            <td class="font-monospace fw-bold text-dark">{{ $row['code'] }}</td>
                            <td>{{ $row['name'] }}</td>
                            <td>
                                <span class="badge bg-light text-dark border text-uppercase" style="font-size: 0.7rem;">
                                    {{ $row['type'] }}
                                </span>
                            </td>
                            <td class="text-end font-monospace">
                                {{ $row['debit'] > 0 ? '৳ ' . number_format($row['debit'], 2) : '—' }}
                            </td>
                            <td class="text-end font-monospace">
                                {{ $row['credit'] > 0 ? '৳ ' . number_format($row['credit'], 2) : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light border-top border-dark fw-bold fs-6">
                    <tr>
                        <td colspan="3" class="text-uppercase text-dark ps-3">Total Cumulative Balance</td>
                        <td class="text-end text-primary font-monospace">৳ {{ number_format($trialBalance['total_debit'], 2) }}</td>
                        <td class="text-end text-primary font-monospace">৳ {{ number_format($trialBalance['total_credit'], 2) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
