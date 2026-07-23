@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Edit Gallery Photo</h4>
    <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Gallery List
    </a>
</div>

<div class="card p-4">
    <form action="{{ route('admin.galleries.update', $gallery->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ $gallery->is_active ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="is_active">Active & Visible on Website</label>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Photo Caption / Title *</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $gallery->title) }}" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Category (Optional)</label>
                <input type="text" class="form-control" name="category" value="{{ old('category', $gallery->category) }}">
            </div>

            <div class="col-md-2 mb-3">
                <label class="form-label fw-bold">Display Order</label>
                <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $gallery->sort_order) }}">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Replace Image File (Optional)</label>
            <input type="file" class="form-control" name="image" accept="image/*">
            @if($gallery->image)
                <div class="mt-2">
                    <img src="{{ asset_url($gallery->image) }}" alt="Current Image" style="height: 120px; border-radius: 8px;" class="border">
                </div>
            @endif
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-gold px-4"><i class="bi bi-check-lg me-1"></i> Update Photo</button>
        </div>
    </form>
</div>
@endsection
