@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Brokerage Closed Deals Register</h4>
        <p class="text-muted small mb-0">Closed secondary transactions, commission split matrix, co-broker payouts, and financial settlements.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.agency-listings.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-tag me-1"></i> View Mandate Listings
        </a>
        <a href="{{ route('erp.agency-deals.create') }}" class="btn btn-erp-gold btn-sm px-3">
            <i class="bi bi-handshake-fill me-1"></i> Close New Deal
        </a>
    </div>
</div>

<!-- Search & Filters -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('erp.agency-deals.index') }}" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search deal code, buyer, property title..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="deal_type" class="form-select form-select-sm">
                <option value="">-- All Deal Types --</option>
                <option value="resale" {{ request('deal_type') === 'resale' ? 'selected' : '' }}>Resale / Outright</option>
                <option value="rental" {{ request('deal_type') === 'rental' ? 'selected' : '' }}>Rental Lease</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">-- Status --</option>
                <option value="pending_settlement" {{ request('status') === 'pending_settlement' ? 'selected' : '' }}>Pending Settlement</option>
                <option value="settled" {{ request('status') === 'settled' ? 'selected' : '' }}>Settled</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm w-100">Filter</button>
            <a href="{{ route('erp.agency-deals.index') }}" class="btn btn-light border btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- Deals Table -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Deal # / Date</th>
                    <th>Property Listing</th>
                    <th>Buyer / Client</th>
                    <th>Closing Price</th>
                    <th>Company Comm.</th>
                    <th>Agent Share</th>
                    <th>External Co-Broker</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($deals as $deal)
                    <tr>
                        <td class="ps-3">
                            <a href="{{ route('erp.agency-deals.show', $deal) }}" class="fw-bold text-decoration-none text-dark">
                                {{ $deal->deal_code }}
                            </a>
                            <div class="text-muted extra-small">{{ \Carbon\Carbon::parse($deal->deal_date)->format('M d, Y') }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $deal->listing->title ?? 'N/A' }}</div>
                            <div class="text-muted extra-small">{{ $deal->listing->location ?? '' }} • <span class="badge bg-light text-dark border">{{ ucfirst($deal->deal_type) }}</span></div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $deal->customer->name ?? 'N/A' }}</div>
                            <div class="text-muted extra-small"><i class="bi bi-telephone me-1"></i>{{ $deal->customer->phone ?? '—' }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">৳ {{ number_format($deal->closing_price, 2) }}</div>
                        </td>
                        <td>
                            <div class="text-success fw-bold">৳ {{ number_format($deal->company_commission_amount, 2) }}</div>
                        </td>
                        <td>
                            <div class="small fw-semibold">{{ $deal->agent->name ?? 'Direct' }}</div>
                            <div class="text-muted extra-small">৳ {{ number_format($deal->agent_commission_amount, 2) }} ({{ $deal->agent_commission_percent }}%)</div>
                        </td>
                        <td>
                            @if($deal->external_broker_name)
                                <div class="small">{{ $deal->external_broker_name }}</div>
                                <div class="text-danger extra-small">- ৳ {{ number_format($deal->external_broker_commission_amount, 2) }}</div>
                            @else
                                <span class="text-muted extra-small">None</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $deal->status === 'settled' ? 'bg-success' : ($deal->status === 'pending_settlement' ? 'bg-warning text-dark' : 'bg-danger') }}">
                                {{ ucfirst(str_replace('_', ' ', $deal->status)) }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('erp.agency-deals.show', $deal) }}" class="btn btn-outline-secondary btn-sm px-2 py-1" title="View Settlement Voucher">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-handshake fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No brokerage deals recorded yet.
                            <div class="mt-2">
                                <a href="{{ route('erp.agency-deals.create') }}" class="btn btn-sm btn-erp-gold">Record First Closed Deal</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($deals->hasPages())
        <div class="p-3 border-top">
            {{ $deals->links() }}
        </div>
    @endif
</div>
@endsection

