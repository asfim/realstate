@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-dark font-monospace px-2 py-1">{{ $landParcel->parcel_code }}</span>
            <h4 class="fw-bold mb-0 text-dark">{{ $landParcel->title }}</h4>
            @php
                $sColor = match($landParcel->status) {
                    'handed_over'     => 'success',
                    'registered'      => 'dark',
                    'legal_vetting'   => 'warning',
                    'mou_negotiation' => 'primary',
                    'dropped'         => 'danger',
                    default           => 'secondary',
                };
            @endphp
            <span class="badge bg-{{ $sColor }}">{{ ucfirst(str_replace('_', ' ', $landParcel->status)) }}</span>
        </div>
        <p class="text-muted small mb-0">
            <i class="bi bi-geo-alt-fill text-danger me-1"></i> Mouza: {{ $landParcel->mouza ?? 'N/A' }} 
            &bull; Area: <strong>{{ number_format($landParcel->total_area_katha, 2) }} Katha</strong> ({{ number_format($landParcel->total_area_decimal ?? 0, 2) }} Dec)
        </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('erp.land-parcels.edit', $landParcel) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit Parcel
        </a>
        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addOwnerModal">
            <i class="bi bi-person-plus-fill me-1"></i> Add Landowner
        </button>
        <a href="{{ route('erp.land-feasibility.create', $landParcel) }}" class="btn btn-erp-gold btn-sm">
            <i class="bi bi-calculator-fill me-1"></i> Calculate Feasibility
        </a>
    </div>
</div>

<!-- Tabs Navigation -->
<ul class="nav nav-tabs border-bottom mb-4" id="landTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active fw-semibold" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button">
            <i class="bi bi-info-circle me-1"></i> Overview & Boundaries
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-semibold" id="owners-tab" data-bs-toggle="tab" data-bs-target="#owners" type="button">
            <i class="bi bi-people me-1"></i> Landowners & JV Share ({{ $landParcel->owners->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-semibold" id="vetting-tab" data-bs-toggle="tab" data-bs-target="#vetting" type="button">
            <i class="bi bi-shield-check me-1"></i> Legal Vetting Checklist ({{ $landParcel->legalVettingClearance() }}%)
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-semibold" id="feasibility-tab" data-bs-toggle="tab" data-bs-target="#feasibility" type="button">
            <i class="bi bi-bar-chart-line me-1"></i> Feasibility Studies ({{ $landParcel->feasibilities->count() }})
        </button>
    </li>
</ul>

<div class="tab-content" id="landTabsContent">
    <!-- Tab 1: Overview -->
    <div class="tab-pane fade show active" id="overview">
        <div class="row g-4">
            <div class="col-lg-7">
                <div class="card card-custom p-4 mb-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Survey & Plot Specifications</h6>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Khatian Number(s)</small>
                            <div class="fw-bold text-dark font-monospace">{{ $landParcel->khatian_no ?? 'Pending' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Dag / Plot Number(s)</small>
                            <div class="fw-bold text-dark font-monospace">{{ $landParcel->dag_no ?? 'Pending' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Total Area (Katha)</small>
                            <div class="fw-bold text-primary fs-5">{{ number_format($landParcel->total_area_katha, 2) }} Katha</div>
                            <small class="text-muted">approx {{ number_format($landParcel->total_area_katha * 720) }} Sq.Ft.</small>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Total Area (Decimals)</small>
                            <div class="fw-bold text-dark fs-5">{{ number_format($landParcel->total_area_decimal ?? 0, 2) }} Dec</div>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Acquisition Mode</small>
                            <span class="badge bg-dark text-capitalize">{{ str_replace('_', ' ', $landParcel->acquisition_type) }}</span>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">Target Handover Date</small>
                            <div class="fw-semibold text-dark">{{ $landParcel->target_handover_date ? $landParcel->target_handover_date->format('d M Y') : 'Unscheduled' }}</div>
                        </div>
                    </div>
                </div>

                <div class="card card-custom p-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Four Boundaries (Chouhaddi)</h6>
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <small class="text-muted d-block">North Boundary</small>
                            <div class="fw-medium text-dark"><i class="bi bi-compass me-1 text-primary"></i> {{ $landParcel->boundary_north ?? 'Road / Open' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">South Boundary</small>
                            <div class="fw-medium text-dark"><i class="bi bi-compass me-1 text-primary"></i> {{ $landParcel->boundary_south ?? 'Plot Boundary' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">East Boundary</small>
                            <div class="fw-medium text-dark"><i class="bi bi-compass me-1 text-primary"></i> {{ $landParcel->boundary_east ?? 'Plot Boundary' }}</div>
                        </div>
                        <div class="col-sm-6">
                            <small class="text-muted d-block">West Boundary</small>
                            <div class="fw-medium text-dark"><i class="bi bi-compass me-1 text-primary"></i> {{ $landParcel->boundary_west ?? 'Plot Boundary' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-5">
                <!-- Joint Venture Commercial Terms -->
                <div class="card card-custom p-4 mb-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Joint Venture Sharing Ratio</h6>
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="small fw-semibold text-secondary">Landowner Allocation: <strong>{{ $landParcel->landowner_ratio }}%</strong></span>
                        <span class="small fw-semibold text-primary">Developer Allocation: <strong>{{ $landParcel->developer_ratio }}%</strong></span>
                    </div>
                    <div class="progress mb-4" style="height: 12px;">
                        <div class="progress-bar bg-warning text-dark fw-bold" style="width: {{ $landParcel->landowner_ratio }}%">{{ $landParcel->landowner_ratio }}%</div>
                        <div class="progress-bar bg-primary text-white fw-bold" style="width: {{ $landParcel->developer_ratio }}%">{{ $landParcel->developer_ratio }}%</div>
                    </div>

                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Financial Valuation & Signing Money</h6>
                    <div class="mb-3">
                        <small class="text-muted d-block">Estimated Land Value</small>
                        <div class="fw-bold text-dark">BDT {{ number_format($landParcel->estimated_land_value ?? 0) }}</div>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block">Signing Money Committed</small>
                        <div class="fw-bold text-dark">BDT {{ number_format($landParcel->signing_money_committed ?? 0) }}</div>
                    </div>
                    <div>
                        <small class="text-muted d-block">Signing Money Disbursed</small>
                        <div class="fw-bold text-success fs-5">BDT {{ number_format($landParcel->signing_money_paid ?? 0) }}</div>
                    </div>
                </div>

                <div class="card card-custom p-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-2">Primary Representative</h6>
                    <div class="fw-bold text-dark">{{ $landParcel->primary_contact_name ?? 'Not recorded' }}</div>
                    <div class="small text-muted mb-3">{{ $landParcel->primary_contact_phone }}</div>
                    @if($landParcel->notes)
                        <div class="p-3 bg-light rounded-3 small">
                            <strong>Notes:</strong> {{ $landParcel->notes }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 2: Landowners Matrix -->
    <div class="tab-pane fade" id="owners">
        <div class="card card-custom">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark mb-0">Joint Venture Landowner Registry</h6>
                <button type="button" class="btn btn-sm btn-erp-gold" data-bs-toggle="modal" data-bs-target="#addOwnerModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Co-Owner
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Owner Name</th>
                            <th>Share %</th>
                            <th>Phone / NID</th>
                            <th>Signing Money Allocation</th>
                            <th>MOU Signed</th>
                            <th>Deed No.</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($landParcel->owners as $owner)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $owner->name }}</div>
                                </td>
                                <td>
                                    <span class="badge bg-dark">{{ $owner->share_percentage }}%</span>
                                </td>
                                <td>
                                    <small class="d-block text-dark">{{ $owner->phone ?? 'N/A' }}</small>
                                    <small class="d-block text-muted">NID: {{ $owner->nid_number ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">BDT {{ number_format($owner->signing_money_share ?? 0) }}</span>
                                </td>
                                <td>
                                    @if($owner->is_mou_signed)
                                        <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>Signed</span>
                                    @else
                                        <span class="badge bg-warning text-dark">Pending</span>
                                    @endif
                                </td>
                                <td>
                                    <small class="font-monospace text-muted">{{ $owner->mou_deed_no ?? '&mdash;' }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No landowners recorded in this register yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 3: Legal Vetting Checklist -->
    <div class="tab-pane fade" id="vetting">
        <div class="card card-custom">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold text-dark mb-0">8-Stage Due Diligence & Legal Vetting</h6>
                    <small class="text-muted">Mandatory regulatory vetting required prior to MoU registration and architectural approval.</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="badge bg-{{ $landParcel->legalVettingClearance() >= 80 ? 'success' : 'warning' }} px-3 py-2">
                        {{ $landParcel->legalVettingClearance() }}% Completed
                    </span>
                </div>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Vetting Stage Title</th>
                            <th>Status</th>
                            <th>Vetted By</th>
                            <th>Findings / Legal Opinion</th>
                            <th>Verified At</th>
                            <th class="text-end">Update</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($landParcel->vettings as $v)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $v->stage_title }}</div>
                                </td>
                                <td>
                                    @php
                                        $vBg = match($v->status) {
                                            'verified_clear'     => 'success',
                                            'disputed_red_flag' => 'danger',
                                            'in_progress'        => 'warning',
                                            'waived'             => 'secondary',
                                            default              => 'light text-dark border',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $vBg }} text-uppercase">
                                        {{ str_replace('_', ' ', $v->status) }}
                                    </span>
                                </td>
                                <td>
                                    <small class="fw-medium text-dark">{{ $v->lawyer_or_expert_name ?? $v->verifiedBy?->name ?? '&mdash;' }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ Str::limit($v->findings, 45) ?? '&mdash;' }}</small>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $v->verified_at ? $v->verified_at->format('d M Y') : 'Pending' }}</small>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editVettingModal{{ $v->id }}">
                                        Update
                                    </button>
                                </td>
                            </tr>

                            <!-- Edit Vetting Modal -->
                            <div class="modal fade" id="editVettingModal{{ $v->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="{{ route('erp.land-vettings.update', $v) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Update Vetting: {{ $v->stage_title }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Verification Status <span class="text-danger">*</span></label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="pending" {{ $v->status === 'pending' ? 'selected' : '' }}>Pending Examination</option>
                                                        <option value="in_progress" {{ $v->status === 'in_progress' ? 'selected' : '' }}>In Progress</option>
                                                        <option value="verified_clear" {{ $v->status === 'verified_clear' ? 'selected' : '' }}>Verified & Clear (No Encumbrance)</option>
                                                        <option value="disputed_red_flag" {{ $v->status === 'disputed_red_flag' ? 'selected' : '' }}>Disputed / Legal Red Flag</option>
                                                        <option value="waived" {{ $v->status === 'waived' ? 'selected' : '' }}>Waived / Exempted</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Legal Counsel / Expert Name</label>
                                                    <input type="text" name="lawyer_or_expert_name" class="form-control" value="{{ $v->lawyer_or_expert_name }}" placeholder="e.g. Barrister / Senior Civil Advocate">
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Findings & Title Observations</label>
                                                    <textarea name="findings" rows="3" class="form-control" placeholder="Observations regarding Bia deed mutation, dakhila or boundary encumbrances...">{{ $v->findings }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-erp-gold">Save Vetting Status</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 4: Feasibility Studies -->
    <div class="tab-pane fade" id="feasibility">
        <div class="card card-custom">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <div>
                    <h6 class="fw-bold text-dark mb-0">Architectural & Financial Feasibility Models</h6>
                    <small class="text-muted">Calculated FAR (Floor Area Ratio), GFA, construction budget, projected revenues, and ROI %.</small>
                </div>
                <a href="{{ route('erp.land-feasibility.create', $landParcel) }}" class="btn btn-sm btn-erp-gold">
                    <i class="bi bi-plus-lg me-1"></i> New Feasibility Version
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Version & Code</th>
                            <th>Proposed Type</th>
                            <th>FAR / Stories</th>
                            <th>Built-up Area (GFA)</th>
                            <th>Total Project Cost</th>
                            <th>Projected Profit</th>
                            <th>ROI %</th>
                            <th>Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($landParcel->feasibilities as $feas)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $feas->feasibility_code }}</div>
                                    <small class="text-muted">Version {{ $feas->version }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ $feas->proposed_building_type }}</span>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">FAR {{ $feas->permissible_far }}</div>
                                    <small class="text-muted">{{ $feas->num_of_stories }} Floors ({{ $feas->num_of_basements }} Basements)</small>
                                </td>
                                <td>
                                    <div class="small fw-semibold text-dark">{{ number_format($feas->gross_floor_area_sqft) }} Sq.Ft.</div>
                                    <small class="text-muted">Sellable: {{ number_format($feas->sellable_area_sqft) }} sft</small>
                                </td>
                                <td>
                                    <span class="small fw-semibold text-danger">BDT {{ number_format($feas->est_total_project_cost) }}</span>
                                </td>
                                <td>
                                    <span class="small fw-bold text-success">BDT {{ number_format($feas->projected_net_profit) }}</span>
                                </td>
                                <td>
                                    <span class="badge bg-success fs-6">{{ number_format($feas->roi_percentage, 1) }}%</span>
                                </td>
                                <td>
                                    @php
                                        $fCol = match($feas->status) {
                                            'approved' => 'success',
                                            'rejected' => 'danger',
                                            'submitted'=> 'warning',
                                            default    => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $fCol }}">{{ ucfirst($feas->status) }}</span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('erp.land-feasibility.show', $feas) }}" class="btn btn-sm btn-outline-dark">
                                        View Details
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    No feasibility calculation run yet for this plot. Click "New Feasibility Version" to compute FAR & ROI.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Landowner -->
<div class="modal fade" id="addOwnerModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('erp.land-parcels.owners.store', $landParcel) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Landowner to JV Matrix</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Full Legal Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Alhaj Shamsul Huda" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Land Share (%) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="share_percentage" class="form-control" value="50.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Signing Money Share (BDT)</label>
                            <input type="number" step="0.01" name="signing_money_share" class="form-control" placeholder="e.g. 5000000">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Contact Phone</label>
                            <input type="text" name="phone" class="form-control" placeholder="e.g. +880 1711 000000">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">National ID (NID)</label>
                            <input type="text" name="nid_number" class="form-control" placeholder="e.g. 1984251092837">
                        </div>
                    </div>
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="is_mou_signed" value="1" id="mouSignedCheck">
                            <label class="form-check-label small fw-semibold" for="mouSignedCheck">
                                Memorandum of Understanding (MOU) Deed Signed
                            </label>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Registered Deed No. / Reference</label>
                        <input type="text" name="mou_deed_no" class="form-control" placeholder="e.g. Deed No. 1294/2026 Gulshan Registry">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp-gold">Add Landowner</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
