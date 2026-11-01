@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-file-earmark-check text-primary me-2"></i> Property Sales Bookings & Contracts
        </h4>
        <div class="text-muted small">Confirmed Real Estate Bookings, Installment Plans, and Active Tokens</div>
    </div>
    <div>
        <a href="{{ route('erp.bookings.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> New Sales Booking
        </a>
    </div>
</div>

<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('erp.bookings.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by Booking No, Customer or Unit..." value="{{ request('search') }}">
            </div>
            <div class="col-6 col-md-4">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="confirmed" {{ request('status') === 'confirmed' ? 'selected' : '' }}>Confirmed</option>
                    <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed / Handed Over</option>
                    <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-6 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
                <a href="{{ route('erp.bookings.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                        <th>Booking No & Date</th>
                        <th>Unit & Project</th>
                        <th>Buyer / Customer</th>
                        <th class="text-end">Contract Price</th>
                        <th class="text-end">Collected</th>
                        <th class="text-end">Due Balance</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($bookings as $b)
                        <tr>
                            <td>
                                <a href="{{ route('erp.bookings.show', $b) }}" class="fw-bold font-monospace text-decoration-none text-dark">
                                    {{ $b->booking_no }}
                                </a>
                                <div class="text-muted small">{{ $b->booking_date?->format('d M Y') }}</div>
                            </td>
                            <td>
                                <strong>{{ $b->unit?->unit_code }}</strong>
                                <div class="text-muted small">{{ $b->unit?->project?->name }}</div>
                            </td>
                            <td>
                                <a href="{{ route('erp.customers.show', $b->customer) }}" class="text-decoration-none fw-semibold">
                                    {{ $b->customer?->name }}
                                </a>
                                <div class="text-muted small">{{ $b->customer?->phone }}</div>
                            </td>
                            <td class="text-end font-monospace fw-bold text-dark">
                                ৳ {{ number_format($b->contract_price, 2) }}
                            </td>
                            <td class="text-end font-monospace fw-bold text-success">
                                ৳ {{ number_format($b->total_paid, 2) }}
                            </td>
                            <td class="text-end font-monospace fw-bold text-danger">
                                ৳ {{ number_format($b->due_amount, 2) }}
                            </td>
                            <td>
                                <span class="badge bg-success">{{ ucfirst($b->status) }}</span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('erp.bookings.show', $b) }}" class="btn btn-sm btn-light border" title="View Details">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-5 text-muted">No bookings registered yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $bookings->links() }}
</div>
@endsection
