@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-person-lines-fill text-primary me-2"></i> Customer Subledger (Accounts Receivable)
        </h4>
        <div class="text-muted small">Property Bookings, Receivable Invoices, and Payment Collections by Customer</div>
    </div>
</div>

{{-- Customer Selector --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('erp.accounting.customer-ledger') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-8">
                <select name="customer_id" class="form-select" onchange="this.form.submit()">
                    <option value="">-- Choose Customer to View Ledger --</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}" {{ ($selectedCustomer?->id == $c->id) ? 'selected' : '' }}>
                            {{ $c->name }} ({{ $c->customer_code }}) &bull; Phone: {{ $c->phone }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-12 col-md-4">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i> Load Customer Statement
                </button>
            </div>
        </form>
    </div>
</div>

@if($selectedCustomer)
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
            <div>
                <h5 class="fw-bold mb-0 text-dark">{{ $selectedCustomer->name }}</h5>
                <div class="text-muted small">
                    Code: <strong>{{ $selectedCustomer->customer_code }}</strong> &bull; Phone: {{ $selectedCustomer->phone }} &bull; NID: {{ $selectedCustomer->nid_or_passport ?? 'N/A' }}
                </div>
            </div>
            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i> Print Statement
            </button>
        </div>

        <div class="card-body p-4">
            <h6 class="fw-bold text-primary mb-3">Bookings & Commercial Accounts Summary</h6>

            <div class="table-responsive">
                <table class="table table-bordered align-middle">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Booking No</th>
                            <th>Property Unit</th>
                            <th>Booking Date</th>
                            <th class="text-end">Contract Price</th>
                            <th class="text-end">Total Collected</th>
                            <th class="text-end">Remaining Due</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($selectedCustomer->bookings as $b)
                            <tr>
                                <td class="fw-bold font-monospace">
                                    <a href="{{ route('erp.bookings.show', $b) }}" class="text-decoration-none">
                                        {{ $b->booking_no }}
                                    </a>
                                </td>
                                <td>
                                    <strong>{{ $b->unit?->unit_code }}</strong> ({{ $b->unit?->project?->name }})
                                </td>
                                <td>{{ $b->booking_date?->format('d M Y') }}</td>
                                <td class="text-end font-monospace fw-bold">৳ {{ number_format($b->contract_price, 2) }}</td>
                                <td class="text-end font-monospace text-success fw-bold">৳ {{ number_format($b->total_paid, 2) }}</td>
                                <td class="text-end font-monospace text-danger fw-bold">৳ {{ number_format($b->due_amount, 2) }}</td>
                                <td><span class="badge bg-success">{{ ucfirst($b->status) }}</span></td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-3 text-muted">No bookings registered for this customer.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="card p-5 text-center shadow-sm border-0">
        <i class="bi bi-person-bounding-box fs-1 text-muted mb-3"></i>
        <h5>Select a Customer</h5>
        <p class="text-muted small">Choose a customer from the dropdown above to inspect their real estate ledger and collection history.</p>
    </div>
@endif
@endsection
