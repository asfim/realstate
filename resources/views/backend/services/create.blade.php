@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Add New Service</h4>
    <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Services List
    </a>
</div>

<div class="card p-4">
    <form action="{{ route('admin.services.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" checked>
            <label class="form-check-label fw-bold" for="is_active">Active & Visible on Website</label>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Service Title *</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" placeholder="e.g. Trusted Company, Legal Support" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">FontAwesome Icon Class</label>
                <input type="text" class="form-control @error('icon') is-invalid @enderror" name="icon" value="{{ old('icon', 'fa-solid fa-building-shield') }}" placeholder="fa-solid fa-building-shield">
                <div class="form-text">e.g., <code>fa-solid fa-shield-halved</code>, <code>fa-solid fa-city</code></div>
                @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Display Order</label>
                <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', 1) }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Short Description *</label>
            <textarea class="form-control @error('short_description') is-invalid @enderror" name="short_description" rows="3" placeholder="Brief summary displayed on service cards..." required>{{ old('short_description') }}</textarea>
            @error('short_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Detailed Description (Optional)</label>
            <textarea class="form-control" name="description" rows="5" placeholder="Full service details...">{{ old('description') }}</textarea>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Service Image (Optional)</label>
            <input type="file" class="form-control" name="image" accept="image/*">
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-gold px-4"><i class="bi bi-check-lg me-1"></i> Save Service</button>
        </div>
    </form>
</div>
@endsection
