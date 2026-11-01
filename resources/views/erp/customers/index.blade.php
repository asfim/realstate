@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-people text-primary me-2"></i> Real Estate Customers & Buyers
        </h4>
        <div class="text-muted small">Customer Master Directory, KYC Records, and Active Bookings</div>
    </div>
    <div>
        <a href="{{ route('erp.customers.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus-fill me-1"></i> Register New Customer
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('erp.customers.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-9">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by Name, Code, Phone or NID..." value="{{ request('search') }}">
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">Search</button>
                <a href="{{ route('erp.customers.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
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
                        <th>Customer Code & Name</th>
                        <th>Phone & Email</th>
                        <th>NID / Passport</th>
                        <th>Address</th>
                        <th>Bookings</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($customers as $c)
                        <tr>
                            <td>
                                <a href="{{ route('erp.customers.show', $c) }}" class="fw-bold text-decoration-none text-dark">
                                    {{ $c->name }}
                                </a>
                                <div class="text-muted small font-monospace">{{ $c->customer_code }}</div>
                            </td>
                            <td>
                                <div><i class="bi bi-telephone text-muted me-1"></i> {{ $c->phone }}</div>
                                @if($c->email)
                                    <div class="text-muted small"><i class="bi bi-envelope text-muted me-1"></i> {{ $c->email }}</div>
                                @endif
                            </td>
                            <td class="font-monospace small">{{ $c->nid_or_passport ?? '—' }}</td>
                            <td class="small text-muted">{{ $c->address ?? 'N/A' }}</td>
                            <td>
                                <span class="badge bg-secondary bg-opacity-10 text-dark border">
                                    {{ $c->bookings_count }} Bookings
                                </span>
                            </td>
                            <td>
                                <span class="badge {{ $c->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($c->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('erp.customers.show', $c) }}" class="btn btn-sm btn-light border" title="View Profile">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('erp.bookings.create', ['customer_id' => $c->id]) }}" class="btn btn-sm btn-outline-primary" title="Book Unit for Customer">
                                    <i class="bi bi-bag-plus"></i> Book
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No customers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $customers->links() }}
</div>
@endsection
