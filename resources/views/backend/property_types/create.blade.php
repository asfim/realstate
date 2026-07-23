@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Add Property Type</h4>
    <a href="{{ route('admin.property-types.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
</div>

<div class="card p-4">
    <form action="{{ route('admin.property-types.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="mb-3">
            <label class="form-label">Property Title (Type Name) <span class="text-danger">*</span></label>
            <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" required placeholder="e.g. Apartment, Villa, Duplex">
            @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-3">
            <label class="form-label">FontAwesome Icon Class (Optional)</label>
            <input type="text" class="form-control @error('icon_class') is-invalid @enderror" name="icon_class" value="{{ old('icon_class') }}" placeholder="e.g. fa-solid fa-building (defaults to fa-building)">
            @error('icon_class') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-4">
            <label class="form-label">Category Image <span class="text-danger">*</span></label>
            <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" accept="image/*" required>
            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-navy px-4">
            <i class="bi bi-save me-2"></i> Save Property Type
        </button>
    </form>
</div>
@endsection
