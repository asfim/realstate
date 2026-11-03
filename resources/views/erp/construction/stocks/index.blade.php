@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Site Material Inventory & Stock</h4>
        <p class="text-muted small mb-0">Real-time physical material balances per project site (cement, rod, sand, aggregates, bricks, tiles).</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.con-pos.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-receipt me-1"></i> Purchase Orders
        </a>
        <a href="{{ route('erp.con-grns.create') }}" class="btn btn-erp-gold btn-sm px-3">
            <i class="bi bi-box-seam me-1"></i> Receive Delivery (GRN)
        </a>
    </div>
</div>

<!-- Search & Filters -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('erp.con-stocks.index') }}" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search material name..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-5">
            <select name="project_id" class="form-select form-select-sm">
                <option value="">-- All Construction Sites --</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->location }})</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm w-100">Filter</button>
            <a href="{{ route('erp.con-stocks.index') }}" class="btn btn-light border btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- Stock Table -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Project Site</th>
                    <th>Material Description</th>
                    <th>Unit</th>
                    <th>Current Stock on Hand</th>
                    <th>Total Received</th>
                    <th>Total Consumed</th>
                    <th>Last Delivery Date</th>
                    <th class="text-end pe-3">Status</th>
                </tr>
            </thead>
            <tbody>
                @forelse($stocks as $stock)
                    <tr>
                        <td class="ps-3 fw-semibold text-dark">
                            {{ $stock->project->name ?? '—' }}
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $stock->material_name }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ strtoupper($stock->unit_of_measure) }}</span>
                        </td>
                        <td>
                            <span class="fs-6 fw-bold {{ $stock->current_stock_qty > 0 ? 'text-success' : 'text-danger' }}">
                                {{ number_format($stock->current_stock_qty, 2) }}
                            </span>
                        </td>
                        <td>
                            <span class="text-dark">{{ number_format($stock->total_received_qty, 2) }}</span>
                        </td>
                        <td>
                            <span class="text-muted">{{ number_format($stock->total_consumed_qty, 2) }}</span>
                        </td>
                        <td>
                            <div class="small">{{ $stock->last_received_date ? \Carbon\Carbon::parse($stock->last_received_date)->format('M d, Y') : '—' }}</div>
                        </td>
                        <td class="text-end pe-3">
                            @if($stock->current_stock_qty > 0)
                                <span class="badge bg-success">In Stock</span>
                            @else
                                <span class="badge bg-danger">Zero Balance</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-boxes fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No material stock recorded yet. Stock balances are automatically updated upon processing Goods Receipt Notes (GRN).
                            <div class="mt-2">
                                <a href="{{ route('erp.con-grns.create') }}" class="btn btn-sm btn-erp-gold">Process First Delivery</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($stocks->hasPages())
        <div class="p-3 border-top">
            {{ $stocks->links() }}
        </div>
    @endif
</div>
@endsection
