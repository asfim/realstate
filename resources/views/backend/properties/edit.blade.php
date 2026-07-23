@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Edit Basic Info: <span class="text-primary">{{ $property->title }}</span></h4>
        <div class="text-muted small">ID: {{ $property->id }} &bull; Type: {{ $property->type }}</div>
    </div>
    <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
</div>

<div class="card p-4">
    <form action="{{ route('admin.properties.update', $property->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <h5 class="fw-bold border-bottom pb-2 mb-3">Basic Information</h5>

        <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ $property->is_active ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="is_active">Active & Visible on Website</label>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Property Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                    <option value="">Select Type</option>
                    @foreach($types as $typeOption)
                        <option value="{{ $typeOption->title }}" {{ old('type', $property->type) == $typeOption->title ? 'selected' : '' }}>{{ $typeOption->title }}</option>
                    @endforeach
                </select>
                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Cover Image</label>
            <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" accept="image/*">
            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
            @if($property->image)
                <div class="mt-2">
                    <img src="{{ Storage::url($property->image) }}" alt="Cover" style="height: 100px; border-radius: 8px;">
                </div>
            @endif
        </div>

        <button type="submit" class="btn btn-navy"><i class="bi bi-save me-1"></i> Update Basic Info</button>
    </form>
</div>
@endsection
