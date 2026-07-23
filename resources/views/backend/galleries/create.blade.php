@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Upload Gallery Photo</h4>
    <a href="{{ route('admin.galleries.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Gallery List
    </a>
</div>

<div class="card p-4">
    <form action="{{ route('admin.galleries.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" checked>
            <label class="form-check-label fw-bold" for="is_active">Active & Visible on Website</label>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Photo Caption / Title *</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" placeholder="e.g. Modern Kitchen Interior" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Category (Optional)</label>
                <input type="text" class="form-control" name="category" value="{{ old('category') }}" placeholder="e.g. Interior, Exterior, Events, Drone">
            </div>

            <div class="col-md-2 mb-3">
                <label class="form-label fw-bold">Display Order</label>
                <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', 1) }}">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Upload Image File *</label>
            <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" accept="image/*" required>
            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-gold px-4"><i class="bi bi-check-lg me-1"></i> Upload Photo</button>
        </div>
    </form>
</div>
@endsection
