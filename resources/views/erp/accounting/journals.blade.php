@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-journal-text text-primary me-2"></i> Accounting Vouchers & General Journal
        </h4>
        <div class="text-muted small">Double-Entry Financial Transactions Ledger</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('erp.accounting.journals.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> New Journal Entry
        </a>
        <a href="{{ route('erp.accounting.profit-and-loss') }}" class="btn btn-light btn-sm border">
            Profit & Loss (P&L)
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('erp.accounting.journals') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by Voucher No, Narration or Reference..." value="{{ request('search') }}">
            </div>
            <div class="col-6 col-md-4">
                <select name="voucher_type" class="form-select form-select-sm">
                    <option value="">All Voucher Types</option>
                    <option value="journal" {{ request('voucher_type') === 'journal' ? 'selected' : '' }}>Journal Voucher (JV)</option>
                    <option value="receipt" {{ request('voucher_type') === 'receipt' ? 'selected' : '' }}>Receipt Voucher (RV)</option>
                    <option value="payment" {{ request('voucher_type') === 'payment' ? 'selected' : '' }}>Payment Voucher (PV)</option>
                    <option value="sales" {{ request('voucher_type') === 'sales' ? 'selected' : '' }}>Sales Voucher (SV)</option>
                </select>
            </div>
            <div class="col-6 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
                <a href="{{ route('erp.accounting.journals') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th>Voucher No & Date</th>
                        <th>Type</th>
                        <th>Narration & Particulars</th>
                        <th>Project Cost Center</th>
                        <th class="text-end">Debit Total</th>
                        <th class="text-end">Credit Total</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($journals as $j)
                        <tr>
                            <td>
                                <div class="fw-bold font-monospace text-dark">{{ $j->journal_no }}</div>
                                <div class="text-muted small">{{ $j->date?->format('d M Y') }}</div>
                            </td>
                            <td>
                                @php
                                    $vBadge = match($j->voucher_type) {
                                        'receipt' => 'bg-success',
                                        'payment' => 'bg-danger',
                                        'sales' => 'bg-primary',
                                        default => 'bg-secondary'
                                    };
                                @endphp
                                <span class="badge {{ $vBadge }}">{{ strtoupper($j->voucher_type) }}</span>
                            </td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $j->narration }}</div>
                                @if($j->reference)
                                    <div class="text-muted small">Ref: {{ $j->reference }}</div>
                                @endif
                                {{-- Line particulars breakdown --}}
                                <div class="small mt-1 text-muted">
                                    @foreach($j->lines as $line)
                                        <div>
                                            &bull; {{ $line->account?->name }} ({{ $line->account?->code }}):
                                            @if($line->debit > 0) <span class="text-primary">Dr ৳ {{ number_format($line->debit, 2) }}</span> @endif
                                            @if($line->credit > 0) <span class="text-success">Cr ৳ {{ number_format($line->credit, 2) }}</span> @endif
                                        </div>
                                    @endforeach
                                </div>
                            </td>
                            <td>
                                {{ $j->project?->name ?? 'Corporate / General' }}
                            </td>
                            <td class="text-end font-monospace fw-bold text-dark">
                                ৳ {{ number_format($j->total_debit, 2) }}
                            </td>
                            <td class="text-end font-monospace fw-bold text-dark">
                                ৳ {{ number_format($j->total_credit, 2) }}
                            </td>
                            <td>
                                <span class="badge bg-success">Posted</span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No accounting vouchers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $journals->links() }}
</div>
@endsection
