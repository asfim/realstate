@extends('erp.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Feasibility Study Calculator: {{ $landParcel->title }}</h4>
        <p class="text-muted small mb-0">Compute FAR (Floor Area Ratio), built-up area, construction budget, projected revenues, and ROI %.</p>
    </div>
    <a href="{{ route('erp.land-parcels.show', $landParcel) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Parcel
    </a>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card card-custom p-4">
            <form action="{{ route('erp.land-feasibility.store', $landParcel) }}" method="POST">
                @csrf
                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-building me-2 text-warning"></i>Architectural & Spatial Parameters</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Building Typology <span class="text-danger">*</span></label>
                        <select name="proposed_building_type" class="form-select" required>
                            <option value="Residential" selected>Residential Luxury Apartments</option>
                            <option value="Commercial">Commercial Office Tower</option>
                            <option value="Mixed_Use">Mixed-Use (Commercial Retail + Residential)</option>
                            <option value="Gated_Community">Gated Land Development / Villa</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Permissible FAR (Floor Area Ratio) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="permissible_far" class="form-control" value="3.50" required>
                        <small class="text-muted">FAR approved by RAJUK / Building Code</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Number of Superstructure Stories <span class="text-danger">*</span></label>
                        <input type="number" name="num_of_stories" class="form-control" value="12" min="1" max="60" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Number of Underground Basements <span class="text-danger">*</span></label>
                        <input type="number" name="num_of_basements" class="form-control" value="2" min="0" max="5" required>
                    </div>
                </div>

                <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-cash-stack me-2 text-warning"></i>Financial & Cost Estimates</h6>
                <div class="row g-3 mb-4">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Estimated Construction Cost per Sq.Ft (BDT) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="est_construction_cost_sqft" class="form-control" value="3800" required>
                        <small class="text-muted">Includes civil, MEP, and finishing</small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Projected Selling Rate per Sq.Ft (BDT) <span class="text-danger">*</span></label>
                        <input type="number" step="0.01" name="projected_selling_rate_sqft" class="form-control" value="8500" required>
                        <small class="text-muted">Projected market launch price</small>
                    </div>

                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Legal, Plan Approval & Overhead Cost (BDT)</label>
                        <input type="number" step="0.01" name="est_land_and_approval_cost" class="form-control" value="5000000">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold">Projected Payback Period (Months)</label>
                        <input type="number" name="payback_period_months" class="form-control" value="36" min="1">
                    </div>
                </div>

                <div class="pt-3 border-top d-flex justify-content-end gap-2">
                    <a href="{{ route('erp.land-parcels.show', $landParcel) }}" class="btn btn-light px-4">Cancel</a>
                    <button type="submit" class="btn btn-erp-gold px-4">Run Feasibility Model</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Right Column: Parcel Snapshot Reference -->
    <div class="col-lg-4">
        <div class="card card-custom p-4 mb-4">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Plot Reference Benchmark</h6>
            <div class="mb-3">
                <small class="text-muted d-block">Total Plot Area</small>
                <div class="fw-bold text-dark fs-5">{{ number_format($landParcel->total_area_katha, 2) }} Katha</div>
                <small class="text-muted">({{ number_format($landParcel->total_area_katha * 720) }} Sq.Ft. land footprint)</small>
            </div>
            <div class="mb-3">
                <small class="text-muted d-block">Agreed JV Allocation</small>
                <div class="fw-semibold text-dark">Landowner: {{ $landParcel->landowner_ratio }}% / Developer: {{ $landParcel->developer_ratio }}%</div>
            </div>
            <div class="mb-3">
                <small class="text-muted d-block">Signing Money Disbursed</small>
                <div class="fw-bold text-success">BDT {{ number_format($landParcel->signing_money_paid ?? 0) }}</div>
            </div>
            <div>
                <small class="text-muted d-block">Legal Due Diligence</small>
                <span class="badge bg-{{ $landParcel->legalVettingClearance() >= 80 ? 'success' : 'warning' }} px-2 py-1">
                    {{ $landParcel->legalVettingClearance() }}% Verified
                </span>
            </div>
        </div>

        <div class="alert alert-info py-3 small mb-0">
            <i class="bi bi-info-circle me-1"></i>
            <strong>Calculation Engine:</strong>
            <ul class="mb-0 mt-1 ps-3">
                <li>GFA = Katha &times; 720 &times; FAR</li>
                <li>Net Sellable Efficiency = 82% of GFA</li>
                <li>Developer Share = Net Sellable &times; {{ $landParcel->developer_ratio }}%</li>
                <li>ROI % = (Net Profit / Total Project Cost) &times; 100</li>
            </ul>
        </div>
    </div>
</div>
@endsection

