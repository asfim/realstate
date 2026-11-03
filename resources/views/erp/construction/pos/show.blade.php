@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.con-pos.index') }}" class="text-decoration-none">Purchase Orders</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $conPo->po_code }}</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <h4 class="fw-bold mb-0 text-dark">Purchase Order {{ $conPo->po_code }}</h4>
            @php
                $statusBadges = [
                    'issued' => 'bg-primary',
                    'partial_received' => 'bg-warning text-dark',
                    'completed' => 'bg-success',
                    'cancelled' => 'bg-danger',
                ];
            @endphp
            <span class="badge {{ $statusBadges[$conPo->status] ?? 'bg-secondary' }} px-3 py-1">
                {{ strtoupper(str_replace('_', ' ', $conPo->status)) }}
            </span>
        </div>
        <p class="text-muted small mb-0">Issued Date: {{ \Carbon\Carbon::parse($conPo->po_date)->format('F d, Y') }} | Supplier: <strong>{{ $conPo->vendor->name ?? '—' }}</strong></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.con-pos.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to POs
        </a>
        <button type="button" class="btn btn-outline-dark btn-sm px-3" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print PO Voucher
        </button>
        @if(in_array($conPo->status, ['issued', 'partial_received']))
            <a href="{{ route('erp.con-grns.create', ['po_id' => $conPo->id]) }}" class="btn btn-erp-gold btn-sm px-3">
                <i class="bi bi-box-seam me-1"></i> Receive Delivery (GRN)
            </a>
        @endif
    </div>
</div>

<!-- Overview Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-primary h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Grand Total PO Value</div>
            <div class="fs-4 fw-bold text-dark mt-1">৳ {{ number_format($conPo->grand_total, 2) }}</div>
            <div class="text-muted extra-small mt-1">Subtotal: ৳{{ number_format($conPo->subtotal, 2) }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-info h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Tax / VAT Included</div>
            <div class="fs-4 fw-bold text-info mt-1">৳ {{ number_format($conPo->tax_vat_amount, 2) }}</div>
            <div class="text-muted extra-small mt-1">Statutory Withholding</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-warning h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Target Delivery Date</div>
            <div class="fs-5 fw-bold text-dark mt-1">
                {{ $conPo->delivery_due_date ? \Carbon\Carbon::parse($conPo->delivery_due_date)->format('M d, Y') : 'Open Timeline' }}
            </div>
            <div class="text-muted extra-small mt-1">Terms: {{ $conPo->payment_terms ?? 'Standard' }}</div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card card-custom p-3 border-start border-4 border-success h-100">
            <div class="text-muted extra-small text-uppercase fw-semibold">Receiving Completion</div>
            <div class="fs-5 fw-bold text-success mt-1">
                {{ $conPo->status === 'completed' ? '100% Received' : ($conPo->status === 'partial_received' ? 'Partially Received' : 'Awaiting Delivery') }}
            </div>
            <div class="text-muted extra-small mt-1">{{ count($conPo->grns) }} GRN Delivery Batches</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Vendor Details -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-person-badge text-erp-gold me-2"></i> Supplier Vendor Profile
            </h6>
            @if($conPo->vendor)
                <div class="row g-2 small">
                    <div class="col-sm-4 text-muted">Vendor Name:</div>
                    <div class="col-sm-8 fw-bold text-dark">{{ $conPo->vendor->name }} ({{ $conPo->vendor->vendor_code }})</div>

                    <div class="col-sm-4 text-muted">Contact Person:</div>
                    <div class="col-sm-8 text-dark">{{ $conPo->vendor->contact_person ?? '—' }}</div>

                    <div class="col-sm-4 text-muted">Phone Number:</div>
                    <div class="col-sm-8 text-dark">{{ $conPo->vendor->phone }}</div>

                    <div class="col-sm-4 text-muted">Email:</div>
                    <div class="col-sm-8 text-dark">{{ $conPo->vendor->email ?? '—' }}</div>

                    <div class="col-sm-4 text-muted">Trade License:</div>
                    <div class="col-sm-8 text-dark">{{ $conPo->vendor->trade_license_no ?? '—' }}</div>

                    <div class="col-sm-4 text-muted">Payment / Bank:</div>
                    <div class="col-sm-8 text-muted">{{ $conPo->vendor->bank_details ?? 'On file' }}</div>
                </div>
            @endif
        </div>
    </div>

    <!-- Project & Requisition Details -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-building-check text-erp-gold me-2"></i> Project Destination & Origin
            </h6>
            <div class="row g-2 small">
                <div class="col-sm-4 text-muted">Project Site:</div>
                <div class="col-sm-8 fw-bold text-dark">{{ $conPo->project->name ?? '—' }}</div>

                <div class="col-sm-4 text-muted">Site Location:</div>
                <div class="col-sm-8 text-dark">{{ $conPo->project->location ?? '—' }}</div>

                <div class="col-sm-4 text-muted">Source Requisition:</div>
                <div class="col-sm-8">
                    @if($conPo->requisition)
                        <a href="{{ route('erp.con-requisitions.show', $conPo->requisition_id) }}" class="text-decoration-none">
                            {{ $conPo->requisition->requisition_code }}
                        </a>
                    @else
                        <span class="text-muted">Direct Procurement</span>
                    @endif
                </div>

                <div class="col-sm-4 text-muted">Issued By:</div>
                <div class="col-sm-8 text-dark">{{ $conPo->creator->name ?? 'Procurement Officer' }}</div>

                <div class="col-sm-4 text-muted">Commercial Notes:</div>
                <div class="col-sm-8 text-muted fst-italic">{{ $conPo->notes ?? 'None' }}</div>
            </div>
        </div>
    </div>
</div>

<!-- Ordered Items Table -->
<div class="card card-custom mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-cart-check-fill text-erp-gold me-2"></i> Ordered Materials Schedule
        </h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th class="ps-3">#</th>
                    <th>Material Name</th>
                    <th>Unit</th>
                    <th>Ordered Qty</th>
                    <th>Received Qty</th>
                    <th>Pending Qty</th>
                    <th>Unit Rate (BDT)</th>
                    <th class="text-end pe-3">Line Total (BDT)</th>
                </tr>
            </thead>
            <tbody>
                @foreach($conPo->items as $idx => $item)
                    @php
                        $pending = max(0, $item->quantity - $item->received_qty);
                    @endphp
                    <tr>
                        <td class="ps-3 fw-bold">{{ $idx + 1 }}</td>
                        <td class="fw-semibold text-dark">{{ $item->item_name }}</td>
                        <td><span class="badge bg-light text-dark border">{{ strtoupper($item->unit_of_measure) }}</span></td>
                        <td class="fw-bold">{{ number_format($item->quantity, 2) }}</td>
                        <td class="text-success fw-bold">{{ number_format($item->received_qty, 2) }}</td>
                        <td class="{{ $pending > 0 ? 'text-danger fw-semibold' : 'text-muted' }}">{{ number_format($pending, 2) }}</td>
                        <td>৳ {{ number_format($item->unit_rate, 2) }}</td>
                        <td class="text-end pe-3 fw-bold">৳ {{ number_format($item->line_total, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
            <tfoot class="table-light">
                <tr>
                    <td colspan="7" class="text-end fw-semibold">Subtotal:</td>
                    <td class="text-end pe-3 fw-bold">৳ {{ number_format($conPo->subtotal, 2) }}</td>
                </tr>
                @if($conPo->tax_vat_amount > 0)
                    <tr>
                        <td colspan="7" class="text-end fw-semibold">Tax / VAT:</td>
                        <td class="text-end pe-3 text-info fw-semibold">৳ {{ number_format($conPo->tax_vat_amount, 2) }}</td>
                    </tr>
                @endif
                <tr>
                    <td colspan="7" class="text-end fw-bold fs-6">Grand Total Commitment:</td>
                    <td class="text-end pe-3 fw-bold text-success fs-5">৳ {{ number_format($conPo->grand_total, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
</div>

<!-- Linked GRNs Delivery History -->
@if($conPo->grns->isNotEmpty())
    <div class="card card-custom mb-4">
        <div class="card-header bg-white py-3">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-truck-flatbed text-erp-gold me-2"></i> Site Receiving History (GRN Records)
            </h6>
        </div>
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small">
                    <tr>
                        <th class="ps-3">GRN Code</th>
                        <th>Received Date</th>
                        <th>Challan #</th>
                        <th>Vehicle #</th>
                        <th>Received By</th>
                        <th>Status</th>
                        <th class="text-end pe-3">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($conPo->grns as $grn)
                        <tr>
                            <td class="ps-3 fw-bold">
                                <a href="{{ route('erp.con-grns.show', $grn) }}" class="text-decoration-none text-dark">
                                    {{ $grn->grn_code }}
                                </a>
                            </td>
                            <td>{{ \Carbon\Carbon::parse($grn->received_date)->format('M d, Y') }}</td>
                            <td>{{ $grn->challan_no }}</td>
                            <td>{{ $grn->vehicle_number ?? '—' }}</td>
                            <td>{{ $grn->receivedByAdmin->name ?? 'Site Officer' }}</td>
                            <td><span class="badge bg-success">{{ ucfirst($grn->status) }}</span></td>
                            <td class="text-end pe-3">
                                <a href="{{ route('erp.con-grns.show', $grn) }}" class="btn btn-outline-secondary btn-sm px-2 py-1">View GRN</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
@endsection

