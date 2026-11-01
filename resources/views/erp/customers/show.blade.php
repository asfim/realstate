@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-secondary bg-opacity-10 text-dark border">{{ $customer->customer_code }}</span>
            <span class="badge {{ $customer->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($customer->status) }}</span>
        </div>
        <h3 class="fw-bold text-dark mb-0">{{ $customer->name }}</h3>
        <div class="text-muted small mt-1">
            <i class="bi bi-telephone me-1"></i> {{ $customer->phone }}
            @if($customer->email) &bull; <i class="bi bi-envelope me-1"></i> {{ $customer->email }} @endif
        </div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('erp.bookings.create', ['customer_id' => $customer->id]) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-bag-plus me-1"></i> New Booking
        </a>
        <a href="{{ route('erp.accounting.customer-ledger', ['customer_id' => $customer->id]) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-journal-text me-1"></i> View Statement
        </a>
        <a href="{{ route('erp.customers.index') }}" class="btn btn-light btn-sm border">
            Back to List
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-12 col-md-4">
        <div class="card p-3 shadow-sm border-0 h-100">
            <h6 class="fw-bold text-primary mb-3">KYC & Contact Information</h6>
            <div class="small mb-2"><strong>National ID:</strong> {{ $customer->nid_or_passport ?? 'N/A' }}</div>
            <div class="small mb-2"><strong>TIN Number:</strong> {{ $customer->tin ?? 'N/A' }}</div>
            <div class="small mb-2"><strong>Emergency Contact:</strong> {{ $customer->emergency_contact ?? 'N/A' }}</div>
            <div class="small mb-2"><strong>Address:</strong> {{ $customer->address ?? 'N/A' }}</div>
        </div>
    </div>

    <div class="col-12 col-md-8">
        <div class="card p-3 shadow-sm border-0 h-100">
            <h6 class="fw-bold text-primary mb-3">Active Bookings & Property Purchases ({{ $customer->bookings->count() }})</h6>
            <div class="table-responsive">
                <table class="table table-sm table-hover align-middle mb-0">
                    <thead class="table-light small">
                        <tr>
                            <th>Booking No</th>
                            <th>Unit & Project</th>
                            <th>Date</th>
                            <th class="text-end">Contract Price</th>
                            <th class="text-end">Paid</th>
                            <th class="text-end">Due</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($customer->bookings as $b)
                            <tr>
                                <td class="fw-bold font-monospace">{{ $b->booking_no }}</td>
                                <td>{{ $b->unit?->unit_code }} ({{ $b->unit?->project?->name }})</td>
                                <td>{{ $b->booking_date?->format('d M Y') }}</td>
                                <td class="text-end font-monospace">৳ {{ number_format($b->contract_price, 2) }}</td>
                                <td class="text-end font-monospace text-success">৳ {{ number_format($b->total_paid, 2) }}</td>
                                <td class="text-end font-monospace text-danger">৳ {{ number_format($b->due_amount, 2) }}</td>
                                <td>
                                    <a href="{{ route('erp.bookings.show', $b) }}" class="btn btn-xs btn-light border">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-3 text-muted">No bookings found for this customer.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
