@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Developer Sales Bookings</h4>
        <p class="text-muted small mb-0">Buyer sales contracts, milestone payment schedules, collections, and deed registries.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.dev-projects.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-building me-1"></i> View Projects
        </a>
        <a href="{{ route('erp.dev-bookings.create') }}" class="btn btn-erp-gold btn-sm px-3">
            <i class="bi bi-file-earmark-plus-fill me-1"></i> New Booking Contract
        </a>
    </div>
</div>

<!-- Search & Filters -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('erp.dev-bookings.index') }}" class="row g-2 align-items-center">
        <div class="col-md-4">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search booking code, customer name, phone..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="project_id" class="form-select form-select-sm">
                <option value="">-- Filter by Project --</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">-- All Statuses --</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Contract</option>
                <option value="registered_deed" {{ request('status') === 'registered_deed' ? 'selected' : '' }}>Registered Deed</option>
                <option value="handed_over" {{ request('status') === 'handed_over' ? 'selected' : '' }}>Handed Over</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm w-100">Filter</button>
            <a href="{{ route('erp.dev-bookings.index') }}" class="btn btn-light border btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- Bookings Table -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Booking # / Date</th>
                    <th>Customer</th>
                    <th>Project & Unit</th>
                    <th>Total Contract</th>
                    <th>Paid Amount</th>
                    <th>Due Balance</th>
                    <th>Sales Agent</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($bookings as $booking)
                    @php
                        $paid = $booking->installments->sum('paid_amount');
                        $balance = max(0, $booking->total_contract_amount - $paid);
                        $statusBadges = [
                            'draft' => 'bg-secondary',
                            'active' => 'bg-primary',
                            'registered_deed' => 'bg-info',
                            'handed_over' => 'bg-success',
                            'cancelled' => 'bg-danger',
                        ];
                    @endphp
                    <tr>
                        <td class="ps-3">
                            <a href="{{ route('erp.dev-bookings.show', $booking) }}" class="fw-bold text-decoration-none text-dark">
                                {{ $booking->booking_code }}
                            </a>
                            <div class="text-muted extra-small">{{ \Carbon\Carbon::parse($booking->booking_date)->format('M d, Y') }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $booking->customer->name ?? 'N/A' }}</div>
                            <div class="text-muted extra-small"><i class="bi bi-telephone me-1"></i>{{ $booking->customer->phone ?? '—' }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $booking->project->name ?? '—' }}</div>
                            <div class="text-muted extra-small">
                                Unit <strong>{{ $booking->unit->unit_number ?? '—' }}</strong> 
                                @if($booking->unit)
                                    ({{ number_format($booking->unit->gross_area_sqft) }} sft, Floor {{ $booking->unit->floor_number }})
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">৳ {{ number_format($booking->total_contract_amount, 2) }}</div>
                            <div class="text-muted extra-small">Plan: {{ ucfirst($booking->payment_plan_type) }}</div>
                        </td>
                        <td>
                            <span class="text-success fw-bold">৳ {{ number_format($paid, 2) }}</span>
                            <div class="text-muted extra-small">
                                @if($booking->total_contract_amount > 0)
                                    {{ round(($paid / $booking->total_contract_amount) * 100, 1) }}% collected
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="fw-semibold {{ $balance > 0 ? 'text-danger' : 'text-muted' }}">৳ {{ number_format($balance, 2) }}</span>
                        </td>
                        <td>
                            <div class="small">{{ $booking->salesAgent->name ?? 'Direct' }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $statusBadges[$booking->status] ?? 'bg-secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $booking->status)) }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('erp.dev-bookings.show', $booking) }}" class="btn btn-outline-secondary btn-sm px-2 py-1" title="View Dossier">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-file-earmark-text fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No developer sales bookings found.
                            <div class="mt-2">
                                <a href="{{ route('erp.dev-bookings.create') }}" class="btn btn-sm btn-erp-gold">Create First Contract</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($bookings->hasPages())
        <div class="p-3 border-top">
            {{ $bookings->links() }}
        </div>
    @endif
</div>
@endsection

