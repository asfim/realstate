@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.acc-journals.index') }}" class="text-decoration-none">Journal Vouchers</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $accJournal->voucher_code }}</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <h4 class="fw-bold mb-0 text-dark">Voucher {{ $accJournal->voucher_code }}</h4>
            <span class="badge {{ $accJournal->status === 'posted' ? 'bg-success' : 'bg-secondary' }} px-3 py-1">
                {{ strtoupper($accJournal->status) }}
            </span>
        </div>
        <p class="text-muted small mb-0">Type: <span class="badge bg-light text-dark border">{{ $accJournal->voucher_type }}</span> | Date: {{ \Carbon\Carbon::parse($accJournal->entry_date)->format('F d, Y') }} | FY: {{ $accJournal->fiscalYear->name ?? 'Current Period' }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.acc-journals.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Journals
        </a>
        <button type="button" class="btn btn-outline-dark btn-sm px-3" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print Voucher
        </button>
    </div>
</div>

<!-- Voucher Master Info Card -->
<div class="card card-custom p-4 mb-4">
    <div class="row g-3 small">
        <div class="col-md-3">
            <div class="text-muted text-uppercase extra-small fw-semibold">Voucher Code</div>
            <div class="fs-5 fw-bold text-dark">{{ $accJournal->voucher_code }}</div>
        </div>
        <div class="col-md-3">
            <div class="text-muted text-uppercase extra-small fw-semibold">Voucher Type</div>
            <div class="fs-5 fw-bold text-dark">{{ $accJournal->voucher_type }} (Double-Entry)</div>
        </div>
        <div class="col-md-3">
            <div class="text-muted text-uppercase extra-small fw-semibold">Total Amount</div>
            <div class="fs-4 fw-bold text-success">৳ {{ number_format($accJournal->total_amount, 2) }}</div>
        </div>
        <div class="col-md-3">
            <div class="text-muted text-uppercase extra-small fw-semibold">Posted By</div>
            <div class="fs-6 fw-bold text-dark">{{ $accJournal->creator->name ?? 'Accountant' }}</div>
        </div>
        <div class="col-md-12 border-top pt-2">
            <div class="text-muted text-uppercase extra-small fw-semibold">Narration / Transaction Description</div>
            <div class="fs-6 text-dark mt-1">{{ $accJournal->narration }}</div>
        </div>
    </div>
</div>

<!-- Double-Entry Lines Table -->
<div class="card card-custom mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-table text-erp-gold me-2"></i> General Ledger Accounting Lines
        </h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th class="ps-3" style="width: 15%;">Account Code</th>
                    <th style="width: 35%;">Account Head Name</th>
                    <th style="width: 20%;">Cost Centre</th>
                    <th class="text-end" style="width: 15%;">Debit (BDT)</th>
                    <th class="text-end pe-3" style="width: 15%;">Credit (BDT)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($accJournal->entries as $entry)
                    <tr>
                        <td class="ps-3">
                            <span class="badge bg-light text-dark border font-monospace">{{ $entry->account->account_code ?? '—' }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $entry->account->account_name ?? '—' }}</div>
                            <div class="text-muted extra-small">{{ ucfirst($entry->account->account_type ?? '') }}</div>
                        </td>
                        <td>
                            @if($entry->costCenter)
                                <span class="badge bg-light text-dark border">{{ $entry->costCenter->name }}</span>
                            @else
                                <span class="text-muted">—</span>
                            @endif
                        </td>
                        <td class="text-end fw-bold {{ $entry->debit > 0 ? 'text-dark' : 'text-muted' }}">
                            {{ $entry->debit > 0 ? '৳ ' . number_format($entry->debit, 2) : '—' }}
                        </td>
                        <td class="text-end pe-3 fw-bold {{ $entry->credit > 0 ? 'text-dark' : 'text-muted' }}">
                            {{ $entry->credit > 0 ? '৳ ' . number_format($entry->credit, 2) : '—' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="3" class="text-end fw-bold fs-6">Balanced Totals:</td>
                    <td class="text-end fw-bold text-success fs-6">৳ {{ number_format($accJournal->entries->sum('debit'), 2) }}</td>
                    <td class="text-end pe-3 fw-bold text-success fs-6">৳ {{ number_format($accJournal->entries->sum('credit'), 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
