@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.con-grns.index') }}" class="text-decoration-none">Goods Receipt Notes</a></li>
                <li class="breadcrumb-item active" aria-current="page">Process Delivery</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-1 text-dark">Process Goods Receipt Note (GRN)</h4>
        <p class="text-muted small mb-0">Inspect and record physical material arrivals at project site against an approved purchase order.</p>
    </div>
    <div>
        <a href="{{ route('erp.con-grns.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to GRNs
        </a>
    </div>
</div>

<div class="card card-custom p-4">
    <!-- PO Selector -->
    <div class="p-3 bg-light rounded-3 mb-4 border">
        <label class="form-label fw-bold text-dark mb-1">1. Select Purchase Order (PO)</label>
        <p class="text-muted extra-small mb-2">Select an open PO to load scheduled materials and pending quantities.</p>
        <form method="GET" action="{{ route('erp.con-grns.create') }}">
            <div class="row g-2 align-items-center">
                <div class="col-md-9">
                    <select name="po_id" class="form-select form-select-sm" onchange="this.form.submit()">
                        <option value="">-- Choose Purchase Order --</option>
                        @foreach($pos as $p)
                            <option value="{{ $p->id }}" {{ ($selectedPo && $selectedPo->id == $p->id) ? 'selected' : '' }}>
                                {{ $p->po_code }} — {{ $p->vendor->name }} — Site: {{ $p->project->name }} (৳{{ number_format($p->grand_total) }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-outline-dark btn-sm w-100">Load Scheduled Items</button>
                </div>
            </div>
        </form>
    </div>

    @if(!$selectedPo)
        <div class="text-center py-5 text-muted">
            <i class="bi bi-box-arrow-in-down fs-1 text-secondary opacity-50 d-block mb-2"></i>
            Please select an open Purchase Order from above to verify and receive incoming material deliveries.
        </div>
    @else
        <form action="{{ route('erp.con-grns.store') }}" method="POST">
            @csrf
            <input type="hidden" name="purchase_order_id" value="{{ $selectedPo->id }}">

            <!-- Delivery & Challan Details -->
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-truck text-erp-gold me-2"></i> Delivery Challan & Vehicle Information
            </h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Supplier Challan / Invoice # <span class="text-danger">*</span></label>
                    <input type="text" name="challan_no" class="form-control form-control-sm" placeholder="e.g. CH-2026-98124" value="{{ old('challan_no') }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Delivery Date <span class="text-danger">*</span></label>
                    <input type="date" name="received_date" class="form-control form-control-sm" value="{{ old('received_date', now()->format('Y-m-d')) }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Vehicle / Truck Plate #</label>
                    <input type="text" name="vehicle_number" class="form-control form-control-sm" placeholder="e.g. Dhaka Metro-TA 11-4455" value="{{ old('vehicle_number') }}">
                </div>
                <div class="col-md-12">
                    <label class="form-label fw-semibold">Inspection Remarks / Quality Notes</label>
                    <input type="text" name="remarks" class="form-control form-control-sm" placeholder="e.g. 500 bags checked, no burst bags found, manufacturer test certificate verified" value="{{ old('remarks') }}">
                </div>
            </div>

            <!-- Material Items to Receive -->
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-clipboard2-check text-erp-gold me-2"></i> Material Receipt Quantities
            </h6>

            <div class="table-responsive mb-4">
                <table class="table table-bordered align-middle">
                    <thead class="table-light small">
                        <tr>
                            <th style="width: 35%;">Material Name</th>
                            <th style="width: 15%;">Unit</th>
                            <th style="width: 15%;">PO Ordered Qty</th>
                            <th style="width: 15%;">Previously Received</th>
                            <th style="width: 20%;">This Delivery Quantity <span class="text-danger">*</span></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($selectedPo->items as $idx => $item)
                            @php
                                $pending = max(0, $item->quantity - $item->received_qty);
                            @endphp
                            <tr>
                                <td>
                                    <input type="hidden" name="items[{{ $idx }}][po_item_id]" value="{{ $item->id }}">
                                    <div class="fw-bold text-dark">{{ $item->item_name }}</div>
                                </td>
                                <td><span class="badge bg-light text-dark border">{{ strtoupper($item->unit_of_measure) }}</span></td>
                                <td class="fw-semibold">{{ number_format($item->quantity, 2) }}</td>
                                <td class="text-success">{{ number_format($item->received_qty, 2) }}</td>
                                <td>
                                    <input type="number" step="0.01" name="items[{{ $idx }}][received_qty]" 
                                           class="form-control form-control-sm fw-bold text-primary" 
                                           max="{{ $pending }}" 
                                           value="{{ $pending }}" required>
                                    <div class="text-muted extra-small">Max pending: {{ number_format($pending, 2) }}</div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end gap-2 border-top pt-3">
                <a href="{{ route('erp.con-grns.index') }}" class="btn btn-light border px-4">Cancel</a>
                <button type="submit" class="btn btn-erp-gold px-5 fw-semibold">
                    <i class="bi bi-check2-all me-1"></i> Confirm Site Receiving & Update Stock
                </button>
            </div>
        </form>
    @endif
</div>
@endsection

