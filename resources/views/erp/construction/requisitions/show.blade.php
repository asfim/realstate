@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.con-requisitions.index') }}" class="text-decoration-none">Site Requisitions</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $conRequisition->requisition_code }}</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <h4 class="fw-bold mb-0 text-dark">Requisition {{ $conRequisition->requisition_code }}</h4>
            @php
                $statusBadges = [
                    'submitted' => 'bg-warning text-dark',
                    'approved' => 'bg-info text-dark',
                    'po_issued' => 'bg-success',
                    'rejected' => 'bg-danger',
                ];
            @endphp
            <span class="badge {{ $statusBadges[$conRequisition->status] ?? 'bg-secondary' }} px-3 py-1">
                {{ strtoupper(str_replace('_', ' ', $conRequisition->status)) }}
            </span>
        </div>
        <p class="text-muted small mb-0">Project: <strong>{{ $conRequisition->project->name ?? '—' }}</strong> | Requested By: {{ $conRequisition->requestedByAdmin->name ?? 'Site Engr' }}</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.con-requisitions.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back to Requisitions
        </a>
        <button type="button" class="btn btn-outline-dark btn-sm px-3" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print
        </button>
        @if($conRequisition->status === 'submitted')
            <form action="{{ route('erp.con-requisitions.approve', $conRequisition) }}" method="POST" class="d-inline">
                @csrf
                <button type="submit" class="btn btn-success btn-sm px-3" onclick="return confirm('Approve this material requisition for procurement?')">
                    <i class="bi bi-check2-circle me-1"></i> Approve Requisition
                </button>
            </form>
        @endif
        @if($conRequisition->status === 'approved')
            <a href="{{ route('erp.con-pos.create', ['requisition_id' => $conRequisition->id]) }}" class="btn btn-erp-gold btn-sm px-3">
                <i class="bi bi-receipt me-1"></i> Issue Purchase Order
            </a>
        @endif
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Requisition Details -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-info-circle text-erp-gold me-2"></i> Requisition Information
            </h6>
            <div class="row g-2 small">
                <div class="col-sm-4 text-muted">Project Name:</div>
                <div class="col-sm-8 fw-bold text-dark">{{ $conRequisition->project->name ?? '—' }} ({{ $conRequisition->project->project_code ?? '' }})</div>

                <div class="col-sm-4 text-muted">Site Location:</div>
                <div class="col-sm-8 text-dark">{{ $conRequisition->project->location ?? '—' }}</div>

                <div class="col-sm-4 text-muted">Requested Date:</div>
                <div class="col-sm-8 text-dark">{{ $conRequisition->created_at->format('F d, Y, h:i A') }}</div>

                <div class="col-sm-4 text-muted">Required By Date:</div>
                <div class="col-sm-8 text-dark fw-bold">{{ \Carbon\Carbon::parse($conRequisition->required_date)->format('F d, Y') }}</div>

                <div class="col-sm-4 text-muted">Urgency Level:</div>
                <div class="col-sm-8 text-capitalize text-dark fw-semibold">{{ $conRequisition->urgency }}</div>

                <div class="col-sm-4 text-muted">Unloading Spot:</div>
                <div class="col-sm-8 text-dark">{{ $conRequisition->site_location_notes ?? 'Main Gate Unloading Deck' }}</div>
            </div>
        </div>
    </div>

    <!-- Approval Status -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 h-100">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-shield-check text-erp-gold me-2"></i> Engineering Authorization
            </h6>
            <div class="row g-2 small">
                <div class="col-sm-4 text-muted">Site Originator:</div>
                <div class="col-sm-8 text-dark fw-semibold">{{ $conRequisition->requestedByAdmin->name ?? 'Site Supervisor' }}</div>

                <div class="col-sm-4 text-muted">Approval Status:</div>
                <div class="col-sm-8">
                    @if($conRequisition->approved_by)
                        <span class="text-success fw-bold"><i class="bi bi-check-circle-fill me-1"></i>Approved by {{ $conRequisition->approvedByAdmin->name ?? 'Director' }}</span>
                        <div class="text-muted extra-small">{{ $conRequisition->approved_at?->format('M d, Y h:i A') }}</div>
                    @else
                        <span class="text-warning fw-semibold"><i class="bi bi-clock-history me-1"></i>Pending Procurement Review</span>
                    @endif
                </div>

                <div class="col-sm-4 text-muted">Procurement Action:</div>
                <div class="col-sm-8 text-dark">
                    @if($conRequisition->status === 'po_issued')
                        <span class="badge bg-success">Purchase Order Issued</span>
                    @elseif($conRequisition->status === 'approved')
                        <span class="badge bg-info text-dark">Ready for Supplier PO</span>
                    @else
                        <span class="text-muted">Awaiting Authorization</span>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Material Items Table -->
<div class="card card-custom mb-4">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-list-check text-erp-gold me-2"></i> Materials Breakdown
        </h6>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small">
                <tr>
                    <th class="ps-3">#</th>
                    <th>Material Name & Specs</th>
                    <th>Unit of Measure</th>
                    <th>Requested Quantity</th>
                    <th>Approved Quantity</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($conRequisition->items as $idx => $item)
                    <tr>
                        <td class="ps-3 fw-bold">{{ $idx + 1 }}</td>
                        <td class="fw-semibold text-dark">{{ $item->material_name }}</td>
                        <td><span class="badge bg-light text-dark border">{{ strtoupper($item->unit_of_measure) }}</span></td>
                        <td class="fw-bold">{{ number_format($item->requested_qty, 2) }}</td>
                        <td>{{ $item->approved_qty ? number_format($item->approved_qty, 2) : number_format($item->requested_qty, 2) }}</td>
                        <td class="text-muted small">{{ $item->remarks ?? '—' }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
