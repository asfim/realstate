@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Procurement Purchase Orders (PO)</h4>
        <p class="text-muted small mb-0">Official contractor supplier purchase agreements, item rate schedules, delivery dates, and receiving status.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.con-grns.index') }}" class="btn btn-outline-success btn-sm px-3">
            <i class="bi bi-box-seam me-1"></i> Goods Receipt Notes (GRN)
        </a>
        <a href="{{ route('erp.con-pos.create') }}" class="btn btn-erp-gold btn-sm px-3">
            <i class="bi bi-plus-circle-fill me-1"></i> Create Purchase Order
        </a>
    </div>
</div>

<!-- Search & Filters -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('erp.con-pos.index') }}" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search PO code, supplier name..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="project_id" class="form-select form-select-sm">
                <option value="">-- All Projects --</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">-- Status --</option>
                <option value="issued" {{ request('status') === 'issued' ? 'selected' : '' }}>Issued / Dispatched</option>
                <option value="partial_received" {{ request('status') === 'partial_received' ? 'selected' : '' }}>Partial Received</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed / Fully Received</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm w-100">Filter</button>
            <a href="{{ route('erp.con-pos.index') }}" class="btn btn-light border btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- POs Table -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">PO # / Date</th>
                    <th>Supplier / Vendor</th>
                    <th>Project Site</th>
                    <th>Grand Total</th>
                    <th>Delivery Due</th>
                    <th>Issued By</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($pos as $po)
                    @php
                        $statusBadges = [
                            'issued' => 'bg-primary',
                            'partial_received' => 'bg-warning text-dark',
                            'completed' => 'bg-success',
                            'cancelled' => 'bg-danger',
                        ];
                    @endphp
                    <tr>
                        <td class="ps-3 fw-bold">
                            <a href="{{ route('erp.con-pos.show', $po) }}" class="text-decoration-none text-dark">
                                {{ $po->po_code }}
                            </a>
                            <div class="text-muted extra-small">{{ \Carbon\Carbon::parse($po->po_date)->format('M d, Y') }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $po->vendor->name ?? '—' }}</div>
                            <div class="text-muted extra-small"><i class="bi bi-telephone me-1"></i>{{ $po->vendor->phone ?? '—' }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $po->project->name ?? '—' }}</div>
                            <div class="text-muted extra-small">{{ $po->project->location ?? '' }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">৳ {{ number_format($po->grand_total, 2) }}</div>
                            @if($po->tax_vat_amount > 0)
                                <div class="text-muted extra-small">Incl. VAT ৳{{ number_format($po->tax_vat_amount) }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="small">{{ $po->delivery_due_date ? \Carbon\Carbon::parse($po->delivery_due_date)->format('M d, Y') : 'Immediate' }}</div>
                        </td>
                        <td>
                            <div class="small">{{ $po->creator->name ?? 'Procurement' }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $statusBadges[$po->status] ?? 'bg-secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $po->status)) }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('erp.con-pos.show', $po) }}" class="btn btn-outline-secondary btn-sm px-2 py-1" title="View PO">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-receipt fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No purchase orders issued yet.
                            <div class="mt-2">
                                <a href="{{ route('erp.con-pos.create') }}" class="btn btn-sm btn-erp-gold">Create Purchase Order</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($pos->hasPages())
        <div class="p-3 border-top">
            {{ $pos->links() }}
        </div>
    @endif
</div>
@endsection
