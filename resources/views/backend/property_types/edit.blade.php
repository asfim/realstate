@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Edit Property Type: {{ $propertyType->title }}</h4>
    <a href="{{ route('admin.property-types.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
</div>

<div class="card p-4">
    <form action="{{ route('admin.property-types.update', $propertyType->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label class="form-label">Property Title (Type Name) <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $propertyType->title) }}" required>
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">FontAwesome Icon Class (Optional)</label>
            <input type="text" class="form-control @error('icon_class') is-invalid @enderror" name="icon_class" value="{{ old('icon_class', $propertyType->icon_class) }}" placeholder="e.g. fa-solid fa-building">
            @error('icon_class') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-4">
            <label class="form-label">Category Image</label>
            <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" accept="image/*">
            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
            @if($propertyType->image)
                <div class="mt-2">
                    <img src="{{ Storage::url($propertyType->image) }}" alt="Preview" style="height: 100px; border-radius: 8px;">
                </div>
            @endif
        </div>

        <button type="submit" class="btn btn-navy px-4">
            <i class="bi bi-save me-2"></i> Update Property Type
        </button>
    </form>
</div>
@endsection
