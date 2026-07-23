@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Edit Service</h4>
    <a href="{{ route('admin.services.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Services List
    </a>
</div>

<div class="card p-4">
    <form action="{{ route('admin.services.update', $service->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ $service->is_active ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="is_active">Active & Visible on Website</label>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Service Title *</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $service->title) }}" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">FontAwesome Icon Class</label>
                <input type="text" class="form-control @error('icon') is-invalid @enderror" name="icon" value="{{ old('icon', $service->icon) }}" placeholder="fa-solid fa-building-shield">
                <div class="form-text">e.g., <code>fa-solid fa-shield-halved</code></div>
                @error('icon') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Display Order</label>
                <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $service->sort_order) }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Short Description *</label>
            <textarea class="form-control @error('short_description') is-invalid @enderror" name="short_description" rows="3" required>{{ old('short_description', $service->short_description) }}</textarea>
            @error('short_description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Detailed Description (Optional)</label>
            <textarea class="form-control" name="description" rows="5">{{ old('description', $service->description) }}</textarea>
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Service Image (Optional)</label>
            <input type="file" class="form-control" name="image" accept="image/*">
            @if($service->image)
                <div class="mt-2">
                    <img src="{{ asset_url($service->image) }}" alt="Service Image" style="height: 100px; border-radius: 8px;" class="border">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="delete_image" value="1" id="del_serv_img">
                        <label class="form-check-label text-danger small fw-bold" for="del_serv_img">
                            <i class="bi bi-trash me-1"></i> Remove / Delete Uploaded Image
                        </label>
                    </div>
                </div>
            @endif
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-gold px-4"><i class="bi bi-check-lg me-1"></i> Update Service</button>
        </div>
    </form>
</div>
@endsection
