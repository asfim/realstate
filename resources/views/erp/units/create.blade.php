@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-plus-circle text-primary me-2"></i> Add Inventory Unit
        </h4>
        <div class="text-muted small">Register a Flat, Residential Plot, or Commercial Space</div>
    </div>
    <div>
        <a href="{{ route('erp.units.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Inventory
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="{{ route('erp.units.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                <div class="col-12">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">1. Project & Hierarchy Location</h6>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Company <span class="text-danger">*</span></label>
                    <select name="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id') == $company->id ? 'selected' : '' }}>
                                {{ $company->name }} ({{ $company->currency }})
                            </option>
                        @endforeach
                    </select>
                    @error('company_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Project <span class="text-danger">*</span></label>
                    <select name="project_id" id="projectSelect" class="form-select @error('project_id') is-invalid @enderror" required onchange="fetchHierarchy(this.value)">
                        <option value="">-- Select Project --</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ (old('project_id', $selectedProjectId) == $p->id) ? 'selected' : '' }}>
                                {{ $p->name }} ({{ strtoupper($p->type) }})
                            </option>
                        @endforeach
                    </select>
                    @error('project_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Unit Type <span class="text-danger">*</span></label>
                    <select name="type" id="unitTypeSelect" class="form-select @error('type') is-invalid @enderror" required onchange="toggleUnitFields(this.value)">
                        <option value="flat" {{ old('type') == 'flat' ? 'selected' : '' }}>Flat / Apartment</option>
                        <option value="plot" {{ old('type') == 'plot' ? 'selected' : '' }}>Residential Plot</option>
                        <option value="shop" {{ old('type') == 'shop' ? 'selected' : '' }}>Retail Shop</option>
                        <option value="office" {{ old('type') == 'office' ? 'selected' : '' }}>Corporate Office</option>
                        <option value="commercial_other" {{ old('type') == 'commercial_other' ? 'selected' : '' }}>Other Commercial</option>
                    </select>
                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Building & Floor (For Flat / Commercial) --}}
                <div class="col-12 col-md-6" id="buildingContainer">
                    <label class="form-label fw-semibold">Tower / Building</label>
                    <select name="building_id" id="buildingSelect" class="form-select @error('building_id') is-invalid @enderror">
                        <option value="">-- Select Building --</option>
                        @foreach($buildings as $b)
                            <option value="{{ $b->id }}" {{ old('building_id') == $b->id ? 'selected' : '' }}>
                                {{ $b->name }} ({{ $b->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('building_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Block & Road (For Plot) --}}
                <div class="col-12 col-md-6" id="blockContainer" style="display: none;">
                    <label class="form-label fw-semibold">Block / Sector</label>
                    <select name="block_id" id="blockSelect" class="form-select @error('block_id') is-invalid @enderror">
                        <option value="">-- Select Block --</option>
                        @foreach($blocks as $blk)
                            <option value="{{ $blk->id }}" {{ old('block_id') == $blk->id ? 'selected' : '' }}>
                                {{ $blk->name }} ({{ $blk->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('block_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">Unit Code <span class="text-danger">*</span></label>
                    <input type="text" name="unit_code" class="form-control @error('unit_code') is-invalid @enderror" value="{{ old('unit_code') }}" placeholder="e.g. FLAT-4A, PLOT-B05, SHOP-G02" required>
                    <div class="form-text">Must be unique within the selected project.</div>
                    @error('unit_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">Unit Title / Name</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" value="{{ old('title') }}" placeholder="e.g. South Lakeview Executive 4A">
                    @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">Lifecycle Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="available" {{ old('status', 'available') == 'available' ? 'selected' : '' }}>Available for Sale</option>
                        <option value="held" {{ old('status') == 'held' ? 'selected' : '' }}>Held / Reserved</option>
                        <option value="draft" {{ old('status') == 'draft' ? 'selected' : '' }}>Draft</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">Facing / Orientation</label>
                    <select name="facing" class="form-select @error('facing') is-invalid @enderror">
                        <option value="">-- Optional --</option>
                        <option value="south" {{ old('facing') == 'south' ? 'selected' : '' }}>South Facing</option>
                        <option value="south_east" {{ old('facing') == 'south_east' ? 'selected' : '' }}>South-East</option>
                        <option value="east" {{ old('facing') == 'east' ? 'selected' : '' }}>East Facing</option>
                        <option value="north" {{ old('facing') == 'north' ? 'selected' : '' }}>North Facing</option>
                        <option value="west" {{ old('facing') == 'west' ? 'selected' : '' }}>West Facing</option>
                        <option value="north_east" {{ old('facing') == 'north_east' ? 'selected' : '' }}>North-East</option>
                    </select>
                    @error('facing') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Dimension & Pricing --}}
                <div class="col-12 mt-4">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">2. Dimensions & Precise Financial Pricing (BDT)</h6>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Area / Size <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" step="0.0001" name="area" class="form-control @error('area') is-invalid @enderror" value="{{ old('area') }}" placeholder="e.g. 2150" required>
                        <select name="area_unit" class="form-select" style="max-width: 110px;">
                            <option value="sqft" {{ old('area_unit', 'sqft') == 'sqft' ? 'selected' : '' }}>Sq Ft</option>
                            <option value="katha" {{ old('area_unit') == 'katha' ? 'selected' : '' }}>Katha</option>
                            <option value="decimal" {{ old('area_unit') == 'decimal' ? 'selected' : '' }}>Decimal</option>
                            <option value="sqm" {{ old('area_unit') == 'sqm' ? 'selected' : '' }}>Sq M</option>
                        </select>
                    </div>
                    @error('area') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Asking Price (BDT) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">৳</span>
                        <input type="number" step="0.01" name="asking_price" class="form-control @error('asking_price') is-invalid @enderror" value="{{ old('asking_price') }}" placeholder="e.g. 25000000.00" required>
                    </div>
                    <div class="form-text">Authoritative decimal price for contract and installments.</div>
                    @error('asking_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Minimum Approved Price (BDT)</label>
                    <div class="input-group">
                        <span class="input-group-text">৳</span>
                        <input type="number" step="0.01" name="min_price" class="form-control @error('min_price') is-invalid @enderror" value="{{ old('min_price') }}" placeholder="e.g. 24000000.00">
                    </div>
                    <div class="form-text">Lowest acceptable price threshold for sales negotiation.</div>
                    @error('min_price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Flat Specifications --}}
                <div class="row g-3 mt-2" id="flatSpecsRow">
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-semibold">Bedrooms</label>
                        <input type="number" name="bedrooms" class="form-control form-control-sm" value="{{ old('bedrooms') }}" min="0">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-semibold">Bathrooms</label>
                        <input type="number" name="bathrooms" class="form-control form-control-sm" value="{{ old('bathrooms') }}" min="0">
                    </div>
                    <div class="col-6 col-md-3">
                        <label class="form-label small fw-semibold">Balconies</label>
                        <input type="number" name="balconies" class="form-control form-control-sm" value="{{ old('balconies') }}" min="0">
                    </div>
                    <div class="col-6 col-md-3 d-flex align-items-center pt-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="parking_included" value="1" id="parkingCheck" {{ old('parking_included') ? 'checked' : '' }}>
                            <label class="form-check-label fw-semibold small" for="parkingCheck">Parking Slot Included</label>
                        </div>
                    </div>
                </div>

                {{-- Plot Specifications --}}
                <div class="row g-3 mt-2" id="plotSpecsRow" style="display: none;">
                    <div class="col-12 col-md-6">
                        <label class="form-label small fw-semibold">Road Width (ft)</label>
                        <input type="number" step="0.1" name="road_width_ft" class="form-control form-control-sm" value="{{ old('road_width_ft') }}" placeholder="e.g. 40.00">
                    </div>
                </div>

                <div class="col-12 mt-3">
                    <label class="form-label fw-semibold">Unit Description</label>
                    <textarea name="description" rows="2" class="form-control" placeholder="Key highlights, orientation or special features...">{{ old('description') }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('erp.units.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i> Save Unit
                </button>
            </div>
        </form>
    </div>
</div>

<script>
function toggleUnitFields(type) {
    const isPlot = type === 'plot';
    document.getElementById('buildingContainer').style.display = isPlot ? 'none' : 'block';
    document.getElementById('blockContainer').style.display = isPlot ? 'block' : 'none';
    document.getElementById('flatSpecsRow').style.display = isPlot ? 'none' : 'flex';
    document.getElementById('plotSpecsRow').style.display = isPlot ? 'flex' : 'none';
}

function fetchHierarchy(projectId) {
    if (!projectId) return;
    fetch(`{{ route('erp.units.hierarchy') }}?project_id=${projectId}`)
        .then(res => res.json())
        .then(data => {
            const bldgSelect = document.getElementById('buildingSelect');
            bldgSelect.innerHTML = '<option value="">-- Select Building --</option>';
            data.buildings.forEach(b => {
                bldgSelect.innerHTML += `<option value="${b.id}">${b.name} (${b.code})</option>`;
            });

            const blockSelect = document.getElementById('blockSelect');
            blockSelect.innerHTML = '<option value="">-- Select Block --</option>';
            data.blocks.forEach(blk => {
                blockSelect.innerHTML += `<option value="${blk.id}">${blk.name} (${blk.code})</option>`;
            });
        });
}

document.addEventListener('DOMContentLoaded', function() {
    toggleUnitFields(document.getElementById('unitTypeSelect').value);
});
</script>
@endsection
