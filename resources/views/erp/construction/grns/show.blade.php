@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.con-grns.index') }}" class="text-decoration-none">Goods Receipt Notes</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $conGrn->grn_code }}</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <h4 class="fw-bold mb-0 text-dark">Goods Receipt Note {{ $conGrn->grn_code }}</h4>
            <span class="badge bg-success px-3 py-1">
                {{ strtoupper($conGrn->status) }}
            </span>
        </div>
        <p class="text-muted small mb-0">Received Date: {{ \Carbon\Carbon::parse($conGrn->received_date)->format('F d, Y') }} | Verified By: <strong>{{ $conGrn->receivedByAdmin->name ?? 'Site Staff' }}</strong></p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.con-grns.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to GRNs
        </a>
        <button type="button" class="btn btn-outline-dark btn-sm px-3" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print Delivery Voucher
        </button>
        <a href="{{ route('erp.con-stocks.index', ['project_id' => $conGrn->project_id]) }}" class="btn btn-erp-gold btn-sm px-3">
            <i class="bi bi-boxes me-1"></i> Check Site Stock
        </a>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Receipt Dossier -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-truck text-erp-gold me-2"></i> Delivery Verification Dossier
            </h6>
            <div class="row g-2 small">
                <div class="col-sm-4 text-muted">Supplier Challan #:</div>
                <div class="col-sm-8 fw-bold text-dark">{{ $conGrn->challan_no }}</div>

                <div class="col-sm-4 text-muted">Truck / Vehicle #:</div>
                <div class="col-sm-8 text-dark">{{ $conGrn->vehicle_number ?? 'Not Recorded' }}</div>

                <div class="col-sm-4 text-muted">Purchase Order:</div>
                <div class="col-sm-8">
                    @if($conGrn->purchaseOrder)
                        <a href="{{ route('erp.con-pos.show', $conGrn->purchase_order_id) }}" class="text-decoration-none fw-semibold">
                            {{ $conGrn->purchaseOrder->po_code }}
                        </a>
                    @else
                        <span class="text-muted">Direct GRN</span>
                    @endif
                </div>

                <div class="col-sm-4 text-muted">Supplier Name:</div>
                <div class="col-sm-8 fw-bold text-dark">{{ $conGrn->vendor->name ?? '—' }}</div>

                <div class="col-sm-4 text-muted">Receiving Officer:</div>
                <div class="col-sm-8 text-dark">{{ $conGrn->receivedByAdmin->name ?? 'Site Staff' }}</div>
            </div>
        </div>
    </div>

    <!-- Project Destination -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-building-check text-erp-gold me-2"></i> Unloading Project Site
            </h6>
            <div class="row g-2 small">
                <div class="col-sm-4 text-muted">Project Name:</div>
                <div class="col-sm-8 fw-bold text-dark">{{ $conGrn->project->name ?? '—' }}</div>

                <div class="col-sm-4 text-muted">Location:</div>
                <div class="col-sm-8 text-dark">{{ $conGrn->project->location ?? '—' }}</div>

                <div class="col-sm-4 text-muted">Stock Impact:</div>
                <div class="col-sm-8 text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Site Real-time Stock Updated</div>

                <div class="col-sm-4 text-muted">Quality Remarks:</div>
                <div class="col-sm-8 text-muted fst-italic">{{ $conGrn->remarks ?? 'Inspected and accepted in good condition.' }}</div>
            </div>
        </div>
    </div>
</div>
@endsection
