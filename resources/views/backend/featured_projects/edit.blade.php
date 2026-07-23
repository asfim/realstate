@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Edit Featured Project</h4>
    <a href="{{ route('admin.featured-project.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
</div>

<div class="card p-4">
    <form action="{{ route('admin.featured-project.update', $featuredProject->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ $featuredProject->is_active ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="is_active">Active & Visible</label>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Project Title *</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $featuredProject->title) }}" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Location *</label>
                <input type="text" class="form-control @error('location') is-invalid @enderror" name="location" value="{{ old('location', $featuredProject->location) }}" required>
                @error('location') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label">Price *</label>
                <input type="text" class="form-control @error('price') is-invalid @enderror" name="price" value="{{ old('price', $featuredProject->price) }}" required>
                @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Bedrooms</label>
                <input type="number" class="form-control" name="beds" value="{{ old('beds', $featuredProject->beds) }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Bathrooms</label>
                <input type="number" class="form-control" name="baths" value="{{ old('baths', $featuredProject->baths) }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label">Area (sqft)</label>
                <input type="number" class="form-control" name="area" value="{{ old('area', $featuredProject->area) }}">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Project Image</label>
            <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" accept="image/*">
            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
            @if($featuredProject->image)
                <div class="mt-2">
                    <img src="{{ asset_url($featuredProject->image) }}" alt="Current Image" style="height: 100px; border-radius: 8px;">
                </div>
            @endif
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-gold px-4">Update Project</button>
        </div>
    </form>
</div>
@endsection
