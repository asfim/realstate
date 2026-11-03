@extends('erp.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Register Land Parcel</h4>
        <p class="text-muted small mb-0">Record plot details, mouza, dag numbers, joint venture sharing parameters, and boundaries.</p>
    </div>
    <a href="{{ route('erp.land-parcels.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Land Bank
    </a>
</div>

<div class="card card-custom">
    <div class="card-body p-4">
        <form action="{{ route('erp.land-parcels.store') }}" method="POST">
            @csrf
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-geo-alt me-2 text-warning"></i>Land Identification & Area</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Project / Parcel Title <span class="text-danger">*</span></label>
                    <input type="text" name="title" class="form-control" placeholder="e.g. Bashundhara Block-I Lakeview Plot" value="{{ old('title') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Mouza / Location Area</label>
                    <input type="text" name="mouza" class="form-control" placeholder="e.g. Vatara, Badda, Dhaka" value="{{ old('mouza') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Khatian No. (CS / SA / RS / City Survey)</label>
                    <input type="text" name="khatian_no" class="form-control" placeholder="e.g. RS 412, City Survey 109" value="{{ old('khatian_no') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Dag / Plot No. (CS / SA / RS / City Survey)</label>
                    <input type="text" name="dag_no" class="form-control" placeholder="e.g. RS Dag 892" value="{{ old('dag_no') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Area (Katha) <span class="text-danger">*</span></label>
                    <input type="number" step="0.0001" name="total_area_katha" class="form-control" placeholder="e.g. 10.5" value="{{ old('total_area_katha') }}" required>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Area (Decimals)</label>
                    <input type="number" step="0.0001" name="total_area_decimal" class="form-control" placeholder="Auto / e.g. 17.3" value="{{ old('total_area_decimal') }}">
                </div>
            </div>

            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-file-earmark-ruled me-2 text-warning"></i>Acquisition Terms & JV Sharing</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Acquisition Mode <span class="text-danger">*</span></label>
                    <select name="acquisition_type" class="form-select" required>
                        <option value="joint_venture" selected>Joint Venture (Landowner + Developer)</option>
                        <option value="outright_purchase">Outright Purchase (100% Owned)</option>
                        <option value="leasehold">Leasehold Agreement</option>
                        <option value="power_of_attorney">Power of Attorney</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Acquisition / Vetting Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="scouting" selected>Scouting / Prospecting</option>
                        <option value="legal_vetting">Legal Vetting</option>
                        <option value="feasibility_study">Feasibility Study</option>
                        <option value="mou_negotiation">MOU Negotiation</option>
                        <option value="registered">Registered Deed</option>
                        <option value="handed_over">Handed Over to Construction</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Landowner Ratio (%)</label>
                    <input type="number" step="0.01" name="landowner_ratio" class="form-control" value="{{ old('landowner_ratio', 50.00) }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-semibold small">Developer Ratio (%)</label>
                    <input type="number" step="0.01" name="developer_ratio" class="form-control" value="{{ old('developer_ratio', 50.00) }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Estimated Land Market Value (BDT)</label>
                    <input type="number" step="0.01" name="estimated_land_value" class="form-control" placeholder="e.g. 150000000" value="{{ old('estimated_land_value') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Signing Money Committed (BDT)</label>
                    <input type="number" step="0.01" name="signing_money_committed" class="form-control" placeholder="e.g. 20000000" value="{{ old('signing_money_committed') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Signing Money Paid to Date (BDT)</label>
                    <input type="number" step="0.01" name="signing_money_paid" class="form-control" placeholder="e.g. 5000000" value="{{ old('signing_money_paid', 0) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Primary Landowner / Representative Name</label>
                    <input type="text" name="primary_contact_name" class="form-control" placeholder="e.g. Alhaj Shamsul Huda" value="{{ old('primary_contact_name') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Primary Contact Phone</label>
                    <input type="text" name="primary_contact_phone" class="form-control" placeholder="e.g. +880 1711 123456" value="{{ old('primary_contact_phone') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Target Land Handover Date</label>
                    <input type="date" name="target_handover_date" class="form-control" value="{{ old('target_handover_date') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Cost Centre Allocation</label>
                    <select name="cost_center_id" class="form-select">
                        <option value="">-- No Direct Cost Centre --</option>
                        @foreach($costCenters as $cc)
                            <option value="{{ $cc->id }}">{{ $cc->name }} ({{ $cc->code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-compass me-2 text-warning"></i>Four Boundaries (Chouhaddi)</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">North Boundary</label>
                    <input type="text" name="boundary_north" class="form-control" placeholder="e.g. 40-feet Road" value="{{ old('boundary_north') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">South Boundary</label>
                    <input type="text" name="boundary_south" class="form-control" placeholder="e.g. Plot No 43" value="{{ old('boundary_south') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">East Boundary</label>
                    <input type="text" name="boundary_east" class="form-control" placeholder="e.g. Gulshan Lake" value="{{ old('boundary_east') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">West Boundary</label>
                    <input type="text" name="boundary_west" class="form-control" placeholder="e.g. Plot No 41" value="{{ old('boundary_west') }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small">General Notes / Scouting History</label>
                    <textarea name="notes" rows="2" class="form-control" placeholder="Notes on mutation, family inheritance, access road width...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="pt-3 border-top d-flex justify-content-end gap-2">
                <a href="{{ route('erp.land-parcels.index') }}" class="btn btn-light px-4">Cancel</a>
                <button type="submit" class="btn btn-erp-gold px-4">Register Parcel</button>
            </div>
        </form>
    </div>
</div>
@endsection

