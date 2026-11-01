@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-receipt text-primary me-2"></i> Collection Receipts & Payments
        </h4>
        <div class="text-muted small">Customer Money Receipts, Bank Transfers, and MFS Collections</div>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('erp.payments.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-9">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by Receipt No, Bank Ref or Customer..." value="{{ request('search') }}">
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">Search</button>
                <a href="{{ route('erp.payments.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                        <th>Receipt No & Date</th>
                        <th>Customer / Buyer</th>
                        <th>Unit & Booking</th>
                        <th>Payment Mode</th>
                        <th class="text-end">Amount Collected</th>
                        <th>Collected By</th>
                        <th class="text-end">Receipt</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($payments as $p)
                        <tr>
                            <td>
                                <a href="{{ route('erp.payments.show', $p) }}" class="fw-bold font-monospace text-decoration-none text-primary">
                                    {{ $p->receipt_no }}
                                </a>
                                <div class="text-muted small">{{ $p->payment_date?->format('d M Y') }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $p->booking?->customer?->name }}</div>
                                <div class="text-muted small">{{ $p->booking?->customer?->phone }}</div>
                            </td>
                            <td>
                                <strong>{{ $p->booking?->unit?->unit_code }}</strong>
                                <div class="text-muted small">{{ $p->booking?->booking_no }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border text-uppercase">
                                    {{ str_replace('_', ' ', $p->method) }}
                                </span>
                                @if($p->cheque_or_trans_no)
                                    <div class="text-muted text-xs font-monospace mt-1">{{ $p->cheque_or_trans_no }}</div>
                                @endif
                            </td>
                            <td class="text-end font-monospace fw-bold text-success fs-6">
                                ৳ {{ number_format($p->amount, 2) }}
                            </td>
                            <td class="small">{{ $p->collectedBy?->name ?? 'Admin' }}</td>
                            <td class="text-end">
                                <a href="{{ route('erp.payments.show', $p) }}" class="btn btn-sm btn-outline-primary" title="Print Money Receipt">
                                    <i class="bi bi-printer me-1"></i> Print
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No payment records found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $payments->links() }}
</div>
@endsection
