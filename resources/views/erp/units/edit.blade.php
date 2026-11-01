@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-pencil-square text-primary me-2"></i> Edit Unit: {{ $unit->unit_code }}
        </h4>
        <div class="text-muted small">Update specifications, area, pricing, and project hierarchy</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('erp.units.show', $unit) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-eye me-1"></i> View Unit Profile
        </a>
        <a href="{{ route('erp.units.index') }}" class="btn btn-light btn-sm border">
            Back to List
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="{{ route('erp.units.update', $unit) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-12">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">1. Hierarchy & Location</h6>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Company <span class="text-danger">*</span></label>
                    <select name="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id', $unit->company_id) == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('company_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Project <span class="text-danger">*</span></label>
                    <select name="project_id" class="form-select @error('project_id') is-invalid @enderror" required>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ old('project_id', $unit->project_id) == $p->id ? 'selected' : '' }}>
                                {{ $p->name }} ({{ strtoupper($p->type) }})
                            </option>
                        @endforeach
                    </select>
                    @error('project_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Unit Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="flat" {{ old('type', $unit->type) == 'flat' ? 'selected' : '' }}>Flat / Apartment</option>
                        <option value="plot" {{ old('type', $unit->type) == 'plot' ? 'selected' : '' }}>Residential Plot</option>
                        <option value="shop" {{ old('type', $unit->type) == 'shop' ? 'selected' : '' }}>Retail Shop</option>
                        <option value="office" {{ old('type', $unit->type) == 'office' ? 'selected' : '' }}>Corporate Office</option>
                        <option value="commercial_other" {{ old('type', $unit->type) == 'commercial_other' ? 'selected' : '' }}>Other Commercial</option>
                    </select>
                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">Unit Code <span class="text-danger">*</span></label>
                    <input type="text" name="unit_code" class="form-control @error('unit_code') is-invalid @enderror" value="{{ old('unit_code', $unit->unit_code) }}" required>
                    @error('unit_code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">Unit Title</label>
                    <input type="text" name="title" class="form-control" value="{{ old('title', $unit->title) }}">
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="draft" {{ old('status', $unit->status) == 'draft' ? 'selected' : '' }}>Draft</option>
                        <option value="available" {{ old('status', $unit->status) == 'available' ? 'selected' : '' }}>Available</option>
                        <option value="held" {{ old('status', $unit->status) == 'held' ? 'selected' : '' }}>Held</option>
                        <option value="booked" {{ old('status', $unit->status) == 'booked' ? 'selected' : '' }}>Booked</option>
                        <option value="sold" {{ old('status', $unit->status) == 'sold' ? 'selected' : '' }}>Sold</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">Facing</label>
                    <select name="facing" class="form-select">
                        <option value="">-- None --</option>
                        <option value="south" {{ old('facing', $unit->facing) == 'south' ? 'selected' : '' }}>South</option>
                        <option value="south_east" {{ old('facing', $unit->facing) == 'south_east' ? 'selected' : '' }}>South-East</option>
                        <option value="east" {{ old('facing', $unit->facing) == 'east' ? 'selected' : '' }}>East</option>
                        <option value="north" {{ old('facing', $unit->facing) == 'north' ? 'selected' : '' }}>North</option>
                        <option value="west" {{ old('facing', $unit->facing) == 'west' ? 'selected' : '' }}>West</option>
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">Tower / Building</label>
                    <select name="building_id" class="form-select">
                        <option value="">-- None --</option>
                        @foreach($buildings as $b)
                            <option value="{{ $b->id }}" {{ old('building_id', $unit->building_id) == $b->id ? 'selected' : '' }}>
                                {{ $b->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">Floor</label>
                    <select name="floor_id" class="form-select">
                        <option value="">-- None --</option>
                        @foreach($floors as $fl)
                            <option value="{{ $fl->id }}" {{ old('floor_id', $unit->floor_id) == $fl->id ? 'selected' : '' }}>
                                {{ $fl->floor_name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-12 mt-4">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">2. Pricing & Dimensions</h6>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Area / Size <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <input type="number" step="0.0001" name="area" class="form-control" value="{{ old('area', $unit->area) }}" required>
                        <select name="area_unit" class="form-select" style="max-width: 110px;">
                            <option value="sqft" {{ old('area_unit', $unit->area_unit) == 'sqft' ? 'selected' : '' }}>Sq Ft</option>
                            <option value="katha" {{ old('area_unit', $unit->area_unit) == 'katha' ? 'selected' : '' }}>Katha</option>
                            <option value="decimal" {{ old('area_unit', $unit->area_unit) == 'decimal' ? 'selected' : '' }}>Decimal</option>
                            <option value="sqm" {{ old('area_unit', $unit->area_unit) == 'sqm' ? 'selected' : '' }}>Sq M</option>
                        </select>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Asking Price (BDT) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">৳</span>
                        <input type="number" step="0.01" name="asking_price" class="form-control" value="{{ old('asking_price', $unit->asking_price) }}" required>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Minimum Approved Price (BDT)</label>
                    <div class="input-group">
                        <span class="input-group-text">৳</span>
                        <input type="number" step="0.01" name="min_price" class="form-control" value="{{ old('min_price', $unit->min_price) }}">
                    </div>
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold">Bedrooms</label>
                    <input type="number" name="bedrooms" class="form-control" value="{{ old('bedrooms', $unit->bedrooms) }}">
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold">Bathrooms</label>
                    <input type="number" name="bathrooms" class="form-control" value="{{ old('bathrooms', $unit->bathrooms) }}">
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold">Balconies</label>
                    <input type="number" name="balconies" class="form-control" value="{{ old('balconies', $unit->balconies) }}">
                </div>

                <div class="col-6 col-md-3">
                    <label class="form-label small fw-semibold">Road Width (ft)</label>
                    <input type="number" step="0.1" name="road_width_ft" class="form-control" value="{{ old('road_width_ft', $unit->road_width_ft) }}">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" rows="2" class="form-control">{{ old('description', $unit->description) }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('erp.units.show', $unit) }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> Update Unit
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

