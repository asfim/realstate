@extends('erp.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Edit Land Parcel: {{ $landParcel->parcel_code }}</h4>
        <p class="text-muted small mb-0">Update plot boundaries, acquisition parameters, or financial values.</p>
    </div>
    <a href="{{ route('erp.land-parcels.show', $landParcel) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Dossier
    </a>
</div>

<div class="card card-custom">
    <div class="card-body p-4">
        <form action="{{ route('erp.land-parcels.update', $landParcel) }}" method="POST">
            @csrf
            @method('PUT')
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-geo-alt me-2 text-warning"></i>Land Identification & Area</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Project / Parcel Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $landParcel->title) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Mouza / Location Area</label>
                    <input type="text" name="mouza" class="form-control" value="{{ old('mouza', $landParcel->mouza) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Khatian No.</label>
                    <input type="text" name="khatian_no" class="form-control" value="{{ old('khatian_no', $landParcel->khatian_no) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Dag / Plot No.</label>
                    <input type="text" name="dag_no" class="form-control" value="{{ old('dag_no', $landParcel->dag_no) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Area (Katha) <span class="text-danger">*</span></label>
                    <input type="number" step="0.0001" name="total_area_katha" class="form-control" value="{{ old('total_area_katha', $landParcel->total_area_katha) }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Area (Decimals)</label>
                    <input type="number" step="0.0001" name="total_area_decimal" class="form-control" value="{{ old('total_area_decimal', $landParcel->total_area_decimal) }}">
                </div>
            </div>

            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-file-earmark-ruled me-2 text-warning"></i>Acquisition Terms & JV Sharing</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Acquisition Mode <span class="text-danger">*</span></label>
                    <select name="acquisition_type" class="form-select" required>
                        <option value="joint_venture" {{ old('acquisition_type', $landParcel->acquisition_type) === 'joint_venture' ? 'selected' : '' }}>Joint Venture</option>
                        <option value="outright_purchase" {{ old('acquisition_type', $landParcel->acquisition_type) === 'outright_purchase' ? 'selected' : '' }}>Outright Purchase</option>
                        <option value="leasehold" {{ old('acquisition_type', $landParcel->acquisition_type) === 'leasehold' ? 'selected' : '' }}>Leasehold</option>
                        <option value="power_of_attorney" {{ old('acquisition_type', $landParcel->acquisition_type) === 'power_of_attorney' ? 'selected' : '' }}>Power of Attorney</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="scouting" {{ old('status', $landParcel->status) === 'scouting' ? 'selected' : '' }}>Scouting / Prospecting</option>
                        <option value="legal_vetting" {{ old('status', $landParcel->status) === 'legal_vetting' ? 'selected' : '' }}>Legal Vetting</option>
                        <option value="feasibility_study" {{ old('status', $landParcel->status) === 'feasibility_study' ? 'selected' : '' }}>Feasibility Study</option>
                        <option value="mou_negotiation" {{ old('status', $landParcel->status) === 'mou_negotiation' ? 'selected' : '' }}>MOU Negotiation</option>
                        <option value="registered" {{ old('status', $landParcel->status) === 'registered' ? 'selected' : '' }}>Registered Deed</option>
                        <option value="handed_over" {{ old('status', $landParcel->status) === 'handed_over' ? 'selected' : '' }}>Handed Over to Construction</option>
                        <option value="dropped" {{ old('status', $landParcel->status) === 'dropped' ? 'selected' : '' }}>Dropped / Ineligible</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Landowner Ratio (%)</label>
                    <input type="number" step="0.01" name="landowner_ratio" class="form-control" value="{{ old('landowner_ratio', $landParcel->landowner_ratio) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Developer Ratio (%)</label>
                    <input type="number" step="0.01" name="developer_ratio" class="form-control" value="{{ old('developer_ratio', $landParcel->developer_ratio) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Estimated Land Market Value (BDT)</label>
                    <input type="number" step="0.01" name="estimated_land_value" class="form-control" value="{{ old('estimated_land_value', $landParcel->estimated_land_value) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Signing Money Committed (BDT)</label>
                    <input type="number" step="0.01" name="signing_money_committed" class="form-control" value="{{ old('signing_money_committed', $landParcel->signing_money_committed) }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Signing Money Paid to Date (BDT)</label>
                    <input type="number" step="0.01" name="signing_money_paid" class="form-control" value="{{ old('signing_money_paid', $landParcel->signing_money_paid) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Primary Contact Name</label>
                    <input type="text" name="primary_contact_name" class="form-control" value="{{ old('primary_contact_name', $landParcel->primary_contact_name) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Primary Contact Phone</label>
                    <input type="text" name="primary_contact_phone" class="form-control" value="{{ old('primary_contact_phone', $landParcel->primary_contact_phone) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Target Handover Date</label>
                    <input type="date" name="target_handover_date" class="form-control" value="{{ old('target_handover_date', $landParcel->target_handover_date ? $landParcel->target_handover_date->format('Y-m-d') : '') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Cost Centre Allocation</label>
                    <select name="cost_center_id" class="form-select">
                        <option value="">-- No Direct Cost Centre --</option>
                        @foreach($costCenters as $cc)
                            <option value="{{ $cc->id }}" {{ old('cost_center_id', $landParcel->cost_center_id) == $cc->id ? 'selected' : '' }}>{{ $cc->name }} ({{ $cc->code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-compass me-2 text-warning"></i>Four Boundaries</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">North Boundary</label>
                    <input type="text" name="boundary_north" class="form-control" value="{{ old('boundary_north', $landParcel->boundary_north) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">South Boundary</label>
                    <input type="text" name="boundary_south" class="form-control" value="{{ old('boundary_south', $landParcel->boundary_south) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">East Boundary</label>
                    <input type="text" name="boundary_east" class="form-control" value="{{ old('boundary_east', $landParcel->boundary_east) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">West Boundary</label>
                    <input type="text" name="boundary_west" class="form-control" value="{{ old('boundary_west', $landParcel->boundary_west) }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small">Notes</label>
                    <textarea name="notes" rows="2" class="form-control">{{ old('notes', $landParcel->notes) }}</textarea>
                </div>
            </div>

            <div class="pt-3 border-top d-flex justify-content-end gap-2">
                <a href="{{ route('erp.land-parcels.show', $landParcel) }}" class="btn btn-light px-4">Cancel</a>
                <button type="submit" class="btn btn-erp-gold px-4">Update Parcel</button>
            </div>
        </form>
    </div>
</div>
@endsection

