@extends('erp.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Register Developer Project</h4>
        <p class="text-muted small mb-0">Define tower specifications, land parcel linkage, sanction numbers, and units count.</p>
    </div>
    <a href="{{ route('erp.dev-projects.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Projects
    </a>
</div>

<div class="card card-custom">
    <div class="card-body p-4">
        <form action="{{ route('erp.dev-projects.store') }}" method="POST">
            @csrf
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-building me-2 text-warning"></i>Project Identification & Master Setup</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Project Commercial Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Aurelia Luminis Tower" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Project Typology <span class="text-danger">*</span></label>
                    <select name="project_type" class="form-select" required>
                        <option value="residential" selected>Residential Luxury Condominium</option>
                        <option value="commercial">Commercial Office Complex</option>
                        <option value="mixed_use">Mixed-Use (Shopping Mall + Offices + Residences)</option>
                        <option value="land_plotted">Land / Plotted Development</option>
                        <option value="warehouse">Logistics Warehouse</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-semibold">City / Zone Location <span class="text-danger">*</span></label>
                    <input type="text" name="location" class="form-control" placeholder="e.g. Gulshan 2, Dhaka" value="{{ old('location') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Full Physical Address</label>
                    <input type="text" name="address" class="form-control" placeholder="Plot 18, Road 44, Gulshan-2, Dhaka 1212" value="{{ old('address') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Acquired Land Parcel (Linkage)</label>
                    <select name="land_parcel_id" class="form-select">
                        <option value="">-- No Direct Land Parcel Linked --</option>
                        @foreach($parcels as $parcel)
                            <option value="{{ $parcel->id }}" {{ old('land_parcel_id') == $parcel->id ? 'selected' : '' }}>
                                {{ $parcel->title }} ({{ $parcel->parcel_code }} - {{ number_format($parcel->total_area_katha, 1) }} Katha)
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label small fw-semibold">Cost Centre Budget Allocation</label>
                    <select name="cost_center_id" class="form-select">
                        <option value="">-- Auto Company Context --</option>
                        @foreach($costCenters as $cc)
                            <option value="{{ $cc->id }}" {{ old('cost_center_id') == $cc->id ? 'selected' : '' }}>{{ $cc->name }} ({{ $cc->code }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-rulers me-2 text-warning"></i>Building Dimensions & Regulatory Sanctions</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Total Floors (G+) <span class="text-danger">*</span></label>
                    <input type="number" name="total_floors" class="form-control" value="{{ old('total_floors', 14) }}" min="1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Underground Basements <span class="text-danger">*</span></label>
                    <input type="number" name="total_basements" class="form-control" value="{{ old('total_basements', 2) }}" min="0" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Total Planned Units <span class="text-danger">*</span></label>
                    <input type="number" name="total_units" class="form-control" value="{{ old('total_units', 24) }}" min="1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label small fw-semibold">Total Parking Bays</label>
                    <input type="number" name="total_parking_slots" class="form-control" value="{{ old('total_parking_slots', 30) }}" min="0">
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Building Sanction / Approval No.</label>
                    <input type="text" name="sanction_no" class="form-control" placeholder="e.g. RAJUK/B-1849/2026" value="{{ old('sanction_no') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Principal Architectural Firm</label>
                    <input type="text" name="architect_name" class="form-control" placeholder="e.g. Studio V Architecture" value="{{ old('architect_name') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Structural Engineering Consultant</label>
                    <input type="text" name="structural_engineer" class="form-control" placeholder="e.g. DDC Structural Consultants" value="{{ old('structural_engineer') }}">
                </div>

                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Construction Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Expected Completion Date</label>
                    <input type="date" name="expected_completion_date" class="form-control" value="{{ old('expected_completion_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label small fw-semibold">Project Stage <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="planning" selected>Planning & Design</option>
                        <option value="sanctioned">Sanction Approved</option>
                        <option value="under_construction">Under Construction</option>
                        <option value="finishing">Finishing Phase</option>
                        <option value="ready">Ready for Possession</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label small fw-semibold">Project Notes / Features</label>
                    <textarea name="notes" rows="2" class="form-control" placeholder="Amenities, double glazing, backup generators...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="pt-3 border-top d-flex justify-content-end gap-2">
                <a href="{{ route('erp.dev-projects.index') }}" class="btn btn-light px-4">Cancel</a>
                <button type="submit" class="btn btn-erp-gold px-4">Create Project</button>
            </div>
        </form>
    </div>
</div>
@endsection

