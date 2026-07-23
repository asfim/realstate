@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Edit Amenity</h4>
    <a href="{{ route('admin.amenities.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Amenities List
    </a>
</div>

<div class="card p-4">
    <form action="{{ route('admin.amenities.update', $amenity->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ $amenity->is_active ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="is_active">Active & Visible on Website</label>
        </div>

        <div class="row">
            <div class="col-md-5 mb-3">
                <label class="form-label fw-bold">Amenity Name *</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $amenity->title) }}" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-5 mb-3">
                <label class="form-label fw-bold">FontAwesome Icon Class *</label>
                <input type="text" class="form-control @error('icon') is-invalid @enderror" name="icon" value="{{ old('icon', $amenity->icon) }}" required>
                <div class="form-text">e.g., <code>fa-solid fa-dumbbell</code></div>
                @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-2 mb-3">
                <label class="form-label fw-bold">Display Order</label>
                <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $amenity->sort_order) }}">
            </div>
        </div>

        <div class="text-end mt-3">
            <button type="submit" class="btn btn-gold px-4"><i class="bi bi-check-lg me-1"></i> Update Amenity</button>
        </div>
    </form>
</div>
@endsection
