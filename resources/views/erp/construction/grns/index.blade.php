@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Goods Receipt Notes (GRN)</h4>
        <p class="text-muted small mb-0">Site physical delivery inspections, supplier challan verifications, and real-time site inventory receipts.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.con-stocks.index') }}" class="btn btn-outline-info btn-sm px-3">
            <i class="bi bi-boxes me-1"></i> Site Stock Inventory
        </a>
        <a href="{{ route('erp.con-grns.create') }}" class="btn btn-erp-gold btn-sm px-3">
            <i class="bi bi-box-seam me-1"></i> Receive Material Delivery (GRN)
        </a>
    </div>
</div>

<!-- Search & Filters -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('erp.con-grns.index') }}" class="row g-2 align-items-center">
        <div class="col-md-8">
            <select name="project_id" class="form-select form-select-sm">
                <option value="">-- All Project Sites --</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm w-100">Filter</button>
            <a href="{{ route('erp.con-grns.index') }}" class="btn btn-light border btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- GRNs Table -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">GRN Code</th>
                    <th>PO Code</th>
                    <th>Supplier</th>
                    <th>Project Site</th>
                    <th>Challan / Vehicle #</th>
                    <th>Received Date</th>
                    <th>Verified By</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($grns as $grn)
                    <tr>
                        <td class="ps-3 fw-bold">
                            <a href="{{ route('erp.con-grns.show', $grn) }}" class="text-decoration-none text-dark">
                                {{ $grn->grn_code }}
                            </a>
                        </td>
                        <td>
                            <a href="{{ route('erp.con-pos.show', $grn->purchase_order_id) }}" class="text-decoration-none">
                                {{ $grn->purchaseOrder->po_code ?? '—' }}
                            </a>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $grn->vendor->name ?? '—' }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $grn->project->name ?? '—' }}</div>
                        </td>
                        <td>
                            <div>Challan: <strong>{{ $grn->challan_no }}</strong></div>
                            @if($grn->vehicle_number)
                                <div class="text-muted extra-small">Truck: {{ $grn->vehicle_number }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="small">{{ \Carbon\Carbon::parse($grn->received_date)->format('M d, Y') }}</div>
                        </td>
                        <td>
                            <div class="small">{{ $grn->receivedByAdmin->name ?? 'Site Staff' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-success">{{ ucfirst($grn->status) }}</span>
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('erp.con-grns.show', $grn) }}" class="btn btn-outline-secondary btn-sm px-2 py-1">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-truck fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No Goods Receipt Notes processed yet.
                            <div class="mt-2">
                                <a href="{{ route('erp.con-grns.create') }}" class="btn btn-sm btn-erp-gold">Receive Delivery</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($grns->hasPages())
        <div class="p-3 border-top">
            {{ $grns->links() }}
        </div>
    @endif
</div>
@endsection
