@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-pencil-square text-primary me-2"></i> Edit Project: {{ $project->name }}
        </h4>
        <div class="text-muted small">Update specifications, land info, status and budget</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('erp.projects.show', $project) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-eye me-1"></i> View Project
        </a>
        <a href="{{ route('erp.projects.index') }}" class="btn btn-light btn-sm border">
            Back to List
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="{{ route('erp.projects.update', $project) }}" method="POST">
            @csrf
            @method('PUT')

            <div class="row g-3">
                <div class="col-12">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">1. Identification & Status</h6>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Company <span class="text-danger">*</span></label>
                    <select name="company_id" class="form-select @error('company_id') is-invalid @enderror" required>
                        @foreach($companies as $company)
                            <option value="{{ $company->id }}" {{ old('company_id', $project->company_id) == $company->id ? 'selected' : '' }}>
                                {{ $company->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('company_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Branch</label>
                    <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
                        <option value="">-- Optional Branch --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $project->branch_id) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Project Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code', $project->code) }}" required>
                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">Project Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $project->name) }}" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="flat" {{ old('type', $project->type) == 'flat' ? 'selected' : '' }}>Flat / Apartment</option>
                        <option value="plot" {{ old('type', $project->type) == 'plot' ? 'selected' : '' }}>Plot / Land Development</option>
                        <option value="commercial" {{ old('type', $project->type) == 'commercial' ? 'selected' : '' }}>Commercial Complex</option>
                        <option value="mixed" {{ old('type', $project->type) == 'mixed' ? 'selected' : '' }}>Mixed Use</option>
                    </select>
                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="planning" {{ old('status', $project->status) == 'planning' ? 'selected' : '' }}>Planning</option>
                        <option value="upcoming" {{ old('status', $project->status) == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="ongoing" {{ old('status', $project->status) == 'ongoing' ? 'selected' : '' }}>Ongoing Construction</option>
                        <option value="ready" {{ old('status', $project->status) == 'ready' ? 'selected' : '' }}>Ready Handover</option>
                        <option value="completed" {{ old('status', $project->status) == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="on_hold" {{ old('status', $project->status) == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 mt-4">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">2. Land & Location</h6>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Ownership Model</label>
                    <select name="ownership_type" class="form-select @error('ownership_type') is-invalid @enderror" required>
                        <option value="own_land" {{ old('ownership_type', $project->ownership_type) == 'own_land' ? 'selected' : '' }}>Company Owned Land</option>
                        <option value="joint_venture" {{ old('ownership_type', $project->ownership_type) == 'joint_venture' ? 'selected' : '' }}>Joint Venture</option>
                        <option value="developer_share" {{ old('ownership_type', $project->ownership_type) == 'developer_share' ? 'selected' : '' }}>Developer Share Only</option>
                    </select>
                    @error('ownership_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Land Area</label>
                    <div class="input-group">
                        <input type="text" name="land_area" class="form-control" value="{{ old('land_area', $project->land_area) }}">
                        <select name="land_area_unit" class="form-select" style="max-width: 120px;">
                            <option value="katha" {{ old('land_area_unit', $project->land_area_unit) == 'katha' ? 'selected' : '' }}>Katha</option>
                            <option value="decimal" {{ old('land_area_unit', $project->land_area_unit) == 'decimal' ? 'selected' : '' }}>Decimal</option>
                            <option value="bigha" {{ old('land_area_unit', $project->land_area_unit) == 'bigha' ? 'selected' : '' }}>Bigha</option>
                            <option value="sqft" {{ old('land_area_unit', $project->land_area_unit) == 'sqft' ? 'selected' : '' }}>Sq Ft</option>
                        </select>
                    </div>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">City / Location</label>
                    <input type="text" name="location" class="form-control" value="{{ old('location', $project->location) }}">
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Detailed Address</label>
                    <textarea name="address" rows="2" class="form-control">{{ old('address', $project->address) }}</textarea>
                </div>

                <div class="col-12 mt-4">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">3. Schedule & Budget</h6>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Launch Date</label>
                    <input type="date" name="launch_date" class="form-control" value="{{ old('launch_date', $project->launch_date?->format('Y-m-d')) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Target Completion Date</label>
                    <input type="date" name="target_end_date" class="form-control" value="{{ old('target_end_date', $project->target_end_date?->format('Y-m-d')) }}">
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Estimated Budget (BDT)</label>
                    <div class="input-group">
                        <span class="input-group-text">৳</span>
                        <input type="number" step="0.01" name="budget" class="form-control" value="{{ old('budget', $project->budget) }}">
                    </div>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Project Description</label>
                    <textarea name="description" rows="3" class="form-control">{{ old('description', $project->description) }}</textarea>
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('erp.projects.show', $project) }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-save me-1"></i> Update Project
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
