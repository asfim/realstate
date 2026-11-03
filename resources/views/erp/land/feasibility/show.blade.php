@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-dark font-monospace px-2 py-1">{{ $feasibility->feasibility_code }}</span>
            <h4 class="fw-bold mb-0 text-dark">Feasibility Model: {{ $feasibility->parcel->title }}</h4>
            @php
                $fBg = match($feasibility->status) {
                    'approved'  => 'success',
                    'rejected'  => 'danger',
                    'submitted' => 'warning',
                    default     => 'secondary',
                };
            @endphp
            <span class="badge bg-{{ $fBg }}">{{ ucfirst($feasibility->status) }}</span>
        </div>
        <p class="text-muted small mb-0">Version {{ $feasibility->version }} &bull; Calculated on {{ $feasibility->created_at->format('d M Y, h:i A') }}</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('erp.land-parcels.show', $feasibility->parcel) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Parcel Dossier
        </a>
        @if($feasibility->status !== 'approved')
            <form action="{{ route('erp.land-feasibility.approve', $feasibility) }}" method="POST" class="d-inline" onsubmit="return confirm('Approve this feasibility study for project launching?')">
                @csrf
                <button type="submit" class="btn btn-success btn-sm px-3">
                    <i class="bi bi-check-circle-fill me-1"></i> Approve Feasibility
                </button>
            </form>
        @else
            <span class="badge bg-success px-3 py-2">
                <i class="bi bi-shield-check me-1"></i> Approved by {{ $feasibility->approvedBy?->name ?? 'Management' }}
            </span>
        @endif
    </div>
</div>

<!-- Highlight Metrics Cards -->
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom h-100 p-3">
            <span class="text-muted small fw-semibold text-uppercase">PROJECTED RETURN ON INVESTMENT</span>
            <h2 class="fw-bold text-success mb-1 mt-2">{{ number_format($feasibility->roi_percentage, 1) }}%</h2>
            <small class="text-muted">Payback period: {{ $feasibility->payback_period_months }} Months</small>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom h-100 p-3">
            <span class="text-muted small fw-semibold text-uppercase">PROJECTED NET PROFIT</span>
            <h2 class="fw-bold text-primary mb-1 mt-2">BDT {{ number_format($feasibility->projected_net_profit / 10000000, 2) }} Cr</h2>
            <small class="text-muted">BDT {{ number_format($feasibility->projected_net_profit) }}</small>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom h-100 p-3">
            <span class="text-muted small fw-semibold text-uppercase">TOTAL PROJECT INVESTMENT</span>
            <h2 class="fw-bold text-danger mb-1 mt-2">BDT {{ number_format($feasibility->est_total_project_cost / 10000000, 2) }} Cr</h2>
            <small class="text-muted">Civil, MEP, Approvals & Signing Money</small>
        </div>
    </div>
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card card-custom h-100 p-3">
            <span class="text-muted small fw-semibold text-uppercase">DEVELOPER SELLABLE SHARE</span>
            <h2 class="fw-bold text-dark mb-1 mt-2">{{ number_format($feasibility->developer_share_sqft) }} sft</h2>
            <small class="text-muted">Total Built-up: {{ number_format($feasibility->gross_floor_area_sqft) }} sft</small>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Spatial & Construction Breakdown -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 mb-4">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-buildings text-warning me-2"></i>Spatial & Architectural Metrics</h6>
            <div class="row g-3">
                <div class="col-6">
                    <small class="text-muted d-block">Building Typology</small>
                    <div class="fw-semibold text-dark">{{ $feasibility->proposed_building_type }}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Permissible FAR</small>
                    <div class="fw-bold text-primary fs-5">{{ $feasibility->permissible_far }}</div>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Number of Superstructure Stories</small>
                    <div class="fw-semibold text-dark">{{ $feasibility->num_of_stories }} Floors</div>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Number of Basements</small>
                    <div class="fw-semibold text-dark">{{ $feasibility->num_of_basements }} Basements</div>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Gross Floor Area (GFA)</small>
                    <div class="fw-bold text-dark">{{ number_format($feasibility->gross_floor_area_sqft) }} Sq.Ft.</div>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Net Sellable Area (82% Eff)</small>
                    <div class="fw-bold text-dark">{{ number_format($feasibility->sellable_area_sqft) }} Sq.Ft.</div>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Landowner Share Allocation</small>
                    <div class="fw-bold text-warning">{{ number_format($feasibility->landowner_share_sqft) }} Sq.Ft.</div>
                </div>
                <div class="col-6">
                    <small class="text-muted d-block">Developer Sellable Allocation</small>
                    <div class="fw-bold text-success">{{ number_format($feasibility->developer_share_sqft) }} Sq.Ft.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Financial Model Breakdown -->
    <div class="col-lg-6">
        <div class="card card-custom p-4 mb-4">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-cash-coin text-success me-2"></i>Financial & Revenue Statement</h6>
            <div class="table-responsive">
                <table class="table table-sm align-middle mb-0">
                    <tbody>
                        <tr>
                            <td class="text-muted">Estimated Construction Cost Rate</td>
                            <td class="text-end fw-semibold">BDT {{ number_format($feasibility->est_construction_cost_sqft) }} / sft</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Estimated Superstructure & MEP Cost</td>
                            <td class="text-end fw-semibold">BDT {{ number_format($feasibility->est_total_construction_cost) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Sanction, Approval & Overhead Cost</td>
                            <td class="text-end fw-semibold">BDT {{ number_format($feasibility->est_land_and_approval_cost) }}</td>
                        </tr>
                        <tr class="table-light">
                            <td class="fw-bold text-dark">Total Project Investment (Cost)</td>
                            <td class="text-end fw-bold text-danger">BDT {{ number_format($feasibility->est_total_project_cost) }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Projected Launch Selling Rate</td>
                            <td class="text-end fw-semibold">BDT {{ number_format($feasibility->projected_selling_rate_sqft) }} / sft</td>
                        </tr>
                        <tr class="table-light">
                            <td class="fw-bold text-dark">Projected Developer Gross Revenue</td>
                            <td class="text-end fw-bold text-primary">BDT {{ number_format($feasibility->projected_gross_revenue) }}</td>
                        </tr>
                        <tr class="table-success">
                            <td class="fw-bold text-dark fs-6">Net Developer Profit</td>
                            <td class="text-end fw-bold text-success fs-6">BDT {{ number_format($feasibility->projected_net_profit) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            @if($feasibility->approval_remarks)
                <div class="mt-3 p-3 bg-light rounded-3 small">
                    <strong>Approval Notes:</strong> {{ $feasibility->approval_remarks }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

