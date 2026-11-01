@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 page-title-row d-print-none">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-receipt text-primary me-2"></i> Official Money Receipt: {{ $payment->receipt_no }}
        </h4>
        <div class="text-muted small">Generated on {{ $payment->created_at?->format('d M Y, h:i A') }}</div>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary btn-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print Receipt
        </button>
        <a href="{{ route('erp.bookings.show', $payment->booking) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Booking
        </a>
    </div>
</div>

{{-- Printable Money Receipt Container --}}
<div class="card shadow-sm border p-4 p-md-5 bg-white mx-auto" style="max-width: 850px;">
    {{-- Header --}}
    <div class="border-bottom pb-4 mb-4">
        <div class="row align-items-center">
            <div class="col-8">
                <h3 class="fw-bold text-dark mb-0 tracking-wide">{{ $payment->company->name }}</h3>
                <div class="text-muted small mt-1">
                    {{ $payment->company->address ?? 'Corporate Headquarters, Dhaka, Bangladesh' }}<br>
                    Phone: {{ $payment->company->phone ?? '+880 1700-000000' }} &bull; Email: {{ $payment->company->email ?? 'info@aureliaestates.com' }}
                </div>
            </div>
            <div class="col-4 text-end">
                <span class="badge bg-primary fs-6 px-3 py-2 text-uppercase letter-spacing-1">Money Receipt</span>
                <div class="fw-bold font-monospace mt-2 text-dark">Receipt #: {{ $payment->receipt_no }}</div>
                <div class="text-muted small">Date: {{ $payment->payment_date?->format('d M Y') }}</div>
            </div>
        </div>
    </div>

    {{-- Customer & Property Info --}}
    <div class="row g-3 mb-4">
        <div class="col-6">
            <div class="bg-light p-3 rounded h-100">
                <div class="text-uppercase text-xs fw-bold text-muted mb-2">Received From (Customer)</div>
                <h5 class="fw-bold mb-1 text-dark">{{ $payment->booking->customer->name }}</h5>
                <div class="small text-muted">Customer Code: <strong>{{ $payment->booking->customer->customer_code }}</strong></div>
                <div class="small text-muted">Phone: {{ $payment->booking->customer->phone }}</div>
                @if($payment->booking->customer->address)
                    <div class="small text-muted mt-1">{{ $payment->booking->customer->address }}</div>
                @endif
            </div>
        </div>
        <div class="col-6">
            <div class="bg-light p-3 rounded h-100">
                <div class="text-uppercase text-xs fw-bold text-muted mb-2">Property & Booking Details</div>
                <h5 class="fw-bold mb-1 text-dark">{{ $payment->booking->unit->unit_code }} ({{ ucfirst($payment->booking->unit->type) }})</h5>
                <div class="small text-muted">Project: <strong>{{ $payment->booking->unit->project->name }}</strong></div>
                <div class="small text-muted">Booking Reference: <strong>{{ $payment->booking->booking_no }}</strong></div>
                <div class="small text-muted">Contract Price: ৳ {{ number_format($payment->booking->contract_price, 2) }}</div>
            </div>
        </div>
    </div>

    {{-- Amount & Payment Mode --}}
    <div class="table-responsive mb-4">
        <table class="table table-bordered align-middle">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Payment Method</th>
                    <th>Bank / Transaction Ref</th>
                    <th>Payment Purpose</th>
                    <th class="text-end">Amount Paid (BDT)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td class="fw-bold text-uppercase">{{ str_replace('_', ' ', $payment->method) }}</td>
                    <td class="font-monospace">{{ $payment->bank_name ? $payment->bank_name . ' (' . $payment->cheque_or_trans_no . ')' : 'Cash Deposit' }}</td>
                    <td>{{ $payment->notes ?? 'Property installment / booking collection' }}</td>
                    <td class="text-end font-monospace fw-bold fs-5 text-dark">
                        ৳ {{ number_format($payment->amount, 2) }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Installment Allocation Breakdown --}}
    @if($payment->allocations->count() > 0)
        <div class="mb-4">
            <h6 class="fw-bold text-muted small text-uppercase mb-2">Installment Allocation Breakdown:</h6>
            <ul class="list-group list-group-flush small border rounded">
                @foreach($payment->allocations as $alloc)
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <span>{{ $alloc->installment?->title }} (Due: {{ $alloc->installment?->due_date?->format('d M Y') }})</span>
                        <span class="font-monospace fw-bold text-success">৳ {{ number_format($alloc->amount, 2) }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- In Words & Signatures --}}
    <div class="mt-4 pt-3 border-top">
        <div class="row align-items-end mt-5 pt-4">
            <div class="col-4 text-center">
                <div class="border-top border-dark pt-1 small fw-semibold text-muted">Customer Signature</div>
            </div>
            <div class="col-4 text-center">
                <div class="border-top border-dark pt-1 small fw-semibold text-muted">Prepared By: {{ $payment->collectedBy?->name ?? 'Accounts' }}</div>
            </div>
            <div class="col-4 text-center">
                <div class="border-top border-dark pt-1 small fw-semibold text-muted">Authorized Signature & Seal</div>
            </div>
        </div>
    </div>
</div>
@endsection
