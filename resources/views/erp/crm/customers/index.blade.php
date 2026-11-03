@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Customer Master Directory</h4>
        <p class="text-muted small mb-0">Verified buyers, converted leads, KYC identification, and nominee records.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('erp.customers.create') }}" class="btn btn-erp-gold btn-sm px-3">
            <i class="bi bi-person-plus-fill me-1"></i> Add Customer
        </a>
    </div>
</div>

<!-- Search -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('erp.customers.index') }}" class="row g-2 align-items-center">
        <div class="col-md-6">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search by customer name, phone, code, NID..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm flex-grow-1">Search</button>
            <a href="{{ route('erp.customers.index') }}" class="btn btn-light btn-sm border">Reset</a>
        </div>
    </form>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Customer Code</th>
                    <th>Customer Name</th>
                    <th>Phone / Email</th>
                    <th>NID / TIN</th>
                    <th>Nominee Details</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $cust)
                    <tr>
                        <td>
                            <span class="badge bg-dark font-monospace px-2 py-1">{{ $cust->customer_code }}</span>
                            @if($cust->converted_from_lead_id)
                                <small class="d-block text-success" style="font-size: 0.7rem;"><i class="bi bi-check-circle me-1"></i>From Lead</small>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $cust->name }}</div>
                            @if($cust->father_or_husband_name)
                                <small class="text-muted">C/O: {{ $cust->father_or_husband_name }}</small>
                            @endif
                        </td>
                        <td>
                            <div class="small fw-semibold text-dark"><i class="bi bi-telephone text-primary me-1"></i>{{ $cust->phone }}</div>
                            <small class="text-muted">{{ $cust->email ?? 'No email' }}</small>
                        </td>
                        <td>
                            <small class="d-block text-dark">NID: {{ $cust->nid_number ?? 'Not verified' }}</small>
                            <small class="d-block text-muted">TIN: {{ $cust->tin_number ?? 'N/A' }}</small>
                        </td>
                        <td>
                            @if($cust->nominee_name)
                                <div class="small fw-medium text-dark">{{ $cust->nominee_name }}</div>
                                <small class="text-muted">({{ $cust->nominee_relation ?? 'Nominee' }})</small>
                            @else
                                <span class="text-muted small">&mdash;</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $cust->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($cust->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('erp.customers.show', $cust) }}" class="btn btn-sm btn-outline-dark me-1" title="View KYC Profile">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('erp.customers.edit', $cust) }}" class="btn btn-sm btn-outline-primary" title="Edit Profile">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-2 d-block mb-2 text-secondary"></i>
                            No customers found in master directory.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($customers->hasPages())
        <div class="p-3 border-top">
            {{ $customers->links() }}
        </div>
    @endif
</div>
@endsection
