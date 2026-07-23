@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Add Property</h4>
    <a href="{{ route('admin.properties.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back to List
    </a>
</div>

<div class="card p-4">
    <form action="{{ route('admin.properties.store') }}" method="POST" enctype="multipart/form-data">
        @csrf
        
        <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" checked>
            <label class="form-check-label fw-bold" for="is_active">Active & Visible on Website</label>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label class="form-label">Property Type <span class="text-danger">*</span></label>
                <select name="type" class="form-select @error('type') is-invalid @enderror" required>
                    <option value="">Select Type</option>
                    @foreach($types as $typeOption)
                        <option value="{{ $typeOption->title }}" {{ old('type') == $typeOption->title ? 'selected' : '' }}>{{ $typeOption->title }}</option>
                    @endforeach
                </select>
                @error('type') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label">Cover Image <span class="text-danger">*</span></label>
            <input type="file" class="form-control @error('image') is-invalid @enderror" name="image" accept="image/*" required>
            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <button type="submit" class="btn btn-navy px-4">
            <i class="bi bi-save me-2"></i> Save Property & Continue
        </button>
    </form>
</div>
@endsection
