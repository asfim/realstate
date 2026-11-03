@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Tenant Leases & Rental Contracts</h4>
        <p class="text-muted small mb-0">Long-term tenancy contracts, security deposits, monthly rent collections, and key handovers.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.agency-listings.index', ['listing_type' => 'rental']) }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-tag me-1"></i> Rental Listings
        </a>
        <a href="{{ route('erp.agency-leases.create') }}" class="btn btn-erp-gold btn-sm px-3">
            <i class="bi bi-file-earmark-plus-fill me-1"></i> New Lease Agreement
        </a>
    </div>
</div>

<!-- Search & Filters -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('erp.agency-leases.index') }}" class="row g-2 align-items-center">
        <div class="col-md-6">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search lease code, tenant name, phone, property..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">-- All Statuses --</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Tenancy</option>
                <option value="expired" {{ request('status') === 'expired' ? 'selected' : '' }}>Expired</option>
                <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
                <option value="renewed" {{ request('status') === 'renewed' ? 'selected' : '' }}>Renewed</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm w-100">Filter</button>
            <a href="{{ route('erp.agency-leases.index') }}" class="btn btn-light border btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- Leases Table -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Lease Code</th>
                    <th>Rental Property</th>
                    <th>Tenant</th>
                    <th>Monthly Rent</th>
                    <th>Security Deposit</th>
                    <th>Lease Period</th>
                    <th>Due Day</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leases as $lease)
                    <tr>
                        <td class="ps-3">
                            <a href="{{ route('erp.agency-leases.show', $lease) }}" class="fw-bold text-decoration-none text-dark">
                                {{ $lease->lease_code }}
                            </a>
                            <div class="text-muted extra-small">
                                Key Handover: {{ $lease->key_handover_date ? \Carbon\Carbon::parse($lease->key_handover_date)->format('M d, Y') : 'Pending' }}
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $lease->listing->title ?? 'N/A' }}</div>
                            <div class="text-muted extra-small">{{ $lease->listing->location ?? '' }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $lease->tenant->name ?? 'N/A' }}</div>
                            <div class="text-muted extra-small"><i class="bi bi-telephone me-1"></i>{{ $lease->tenant->phone ?? '—' }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">৳ {{ number_format($lease->monthly_rent, 2) }}</div>
                            @if($lease->monthly_service_charge > 0)
                                <div class="text-muted extra-small">+ ৳{{ number_format($lease->monthly_service_charge) }} service</div>
                            @endif
                        </td>
                        <td>
                            <span class="text-primary fw-semibold">৳ {{ number_format($lease->security_deposit_amount, 2) }}</span>
                        </td>
                        <td>
                            <div class="small">{{ \Carbon\Carbon::parse($lease->lease_start_date)->format('M Y') }} — {{ \Carbon\Carbon::parse($lease->lease_end_date)->format('M Y') }}</div>
                            <div class="text-muted extra-small">{{ \Carbon\Carbon::parse($lease->lease_start_date)->diffInMonths(\Carbon\Carbon::parse($lease->lease_end_date)) }} months term</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $lease->rent_due_day }}th</span>
                        </td>
                        <td>
                            <span class="badge {{ $lease->status === 'active' ? 'bg-success' : ($lease->status === 'expired' ? 'bg-secondary' : 'bg-danger') }}">
                                {{ ucfirst($lease->status) }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('erp.agency-leases.show', $lease) }}" class="btn btn-outline-secondary btn-sm px-2 py-1" title="View Agreement">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-key fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No tenant lease contracts registered.
                            <div class="mt-2">
                                <a href="{{ route('erp.agency-leases.create') }}" class="btn btn-sm btn-erp-gold">Create Lease Contract</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($leases->hasPages())
        <div class="p-3 border-top">
            {{ $leases->links() }}
        </div>
    @endif
</div>
@endsection

