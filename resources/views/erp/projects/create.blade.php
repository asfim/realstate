@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-plus-circle text-primary me-2"></i> Create Real Estate Project
        </h4>
        <div class="text-muted small">Setup project information, location, land details and budget</div>
    </div>
    <div>
        <a href="{{ route('erp.projects.index') }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-arrow-left me-1"></i> Back to Projects
        </a>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-4">
        <form action="{{ route('erp.projects.store') }}" method="POST">
            @csrf

            <div class="row g-3">
                {{-- Basic Info --}}
                <div class="col-12">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">1. Basic Identification</h6>
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
                    <label class="form-label fw-semibold">Branch</label>
                    <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
                        <option value="">-- Optional Branch --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }} ({{ $branch->code }})
                            </option>
                        @endforeach
                    </select>
                    @error('branch_id') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Project Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control @error('code') is-invalid @enderror" value="{{ old('code') }}" placeholder="e.g. PRJ-SKYLINE" required>
                    <div class="form-text">Unique code identifier for ERP records.</div>
                    @error('code') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-6">
                    <label class="form-label fw-semibold">Project Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name') }}" placeholder="e.g. Aurelia Skyline Tower" required>
                    @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">Project Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                        <option value="flat" {{ old('type') == 'flat' ? 'selected' : '' }}>Flat / Apartment</option>
                        <option value="plot" {{ old('type') == 'plot' ? 'selected' : '' }}>Plot / Land Development</option>
                        <option value="commercial" {{ old('type') == 'commercial' ? 'selected' : '' }}>Commercial Complex</option>
                        <option value="mixed" {{ old('type') == 'mixed' ? 'selected' : '' }}>Mixed Use</option>
                    </select>
                    @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-3">
                    <label class="form-label fw-semibold">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select @error('status') is-invalid @enderror" required>
                        <option value="planning" {{ old('status') == 'planning' ? 'selected' : '' }}>Planning</option>
                        <option value="upcoming" {{ old('status') == 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                        <option value="ongoing" {{ old('status', 'ongoing') == 'ongoing' ? 'selected' : '' }}>Ongoing Construction</option>
                        <option value="ready" {{ old('status') == 'ready' ? 'selected' : '' }}>Ready Handover</option>
                        <option value="completed" {{ old('status') == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="on_hold" {{ old('status') == 'on_hold' ? 'selected' : '' }}>On Hold</option>
                    </select>
                    @error('status') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Land & Ownership Details --}}
                <div class="col-12 mt-4">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">2. Land, Ownership & Location</h6>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Ownership Model <span class="text-danger">*</span></label>
                    <select name="ownership_type" class="form-select @error('ownership_type') is-invalid @enderror" required>
                        <option value="own_land" {{ old('ownership_type') == 'own_land' ? 'selected' : '' }}>Company Owned Land</option>
                        <option value="joint_venture" {{ old('ownership_type') == 'joint_venture' ? 'selected' : '' }}>Joint Venture (Landowner + Developer)</option>
                        <option value="developer_share" {{ old('ownership_type') == 'developer_share' ? 'selected' : '' }}>Developer Share Only</option>
                    </select>
                    @error('ownership_type') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Land Area</label>
                    <div class="input-group">
                        <input type="text" name="land_area" class="form-control @error('land_area') is-invalid @enderror" value="{{ old('land_area') }}" placeholder="e.g. 15.5">
                        <select name="land_area_unit" class="form-select" style="max-width: 120px;">
                            <option value="katha" {{ old('land_area_unit') == 'katha' ? 'selected' : '' }}>Katha</option>
                            <option value="decimal" {{ old('land_area_unit') == 'decimal' ? 'selected' : '' }}>Decimal</option>
                            <option value="bigha" {{ old('land_area_unit') == 'bigha' ? 'selected' : '' }}>Bigha</option>
                            <option value="sqft" {{ old('land_area_unit') == 'sqft' ? 'selected' : '' }}>Sq Ft</option>
                        </select>
                    </div>
                    @error('land_area') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">City / Location</label>
                    <input type="text" name="location" class="form-control @error('location') is-invalid @enderror" value="{{ old('location') }}" placeholder="e.g. Gulshan-2, Dhaka">
                    @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Detailed Site Address</label>
                    <textarea name="address" rows="2" class="form-control @error('address') is-invalid @enderror" placeholder="Complete road, plot, sector or holding address...">{{ old('address') }}</textarea>
                    @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                {{-- Schedule & Financial Budget --}}
                <div class="col-12 mt-4">
                    <h6 class="fw-bold text-primary border-bottom pb-2 mb-3">3. Schedule & Project Budget</h6>
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Launch Date</label>
                    <input type="date" name="launch_date" class="form-control @error('launch_date') is-invalid @enderror" value="{{ old('launch_date') }}">
                    @error('launch_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Target Completion Date</label>
                    <input type="date" name="target_end_date" class="form-control @error('target_end_date') is-invalid @enderror" value="{{ old('target_end_date') }}">
                    @error('target_end_date') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12 col-md-4">
                    <label class="form-label fw-semibold">Estimated Budget (BDT)</label>
                    <div class="input-group">
                        <span class="input-group-text">৳</span>
                        <input type="number" step="0.01" name="budget" class="form-control @error('budget') is-invalid @enderror" value="{{ old('budget') }}" placeholder="e.g. 250000000">
                    </div>
                    @error('budget') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold">Project Description & Scope</label>
                    <textarea name="description" rows="3" class="form-control @error('description') is-invalid @enderror" placeholder="Describe the structural and marketing scope...">{{ old('description') }}</textarea>
                    @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="{{ route('erp.projects.index') }}" class="btn btn-outline-secondary">Cancel</a>
                <button type="submit" class="btn btn-primary px-4">
                    <i class="bi bi-check-circle me-1"></i> Save & Continue
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

