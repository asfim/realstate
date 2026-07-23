@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Edit Testimonial</h4>
    <a href="{{ route('admin.testimonials.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="card p-4">
    <form action="{{ route('admin.testimonials.update', $testimonial->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Client Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ $testimonial->name }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Role / Company <span class="text-danger">*</span></label>
                <input type="text" name="role" class="form-control" value="{{ $testimonial->role }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Rating (1-5) <span class="text-danger">*</span></label>
                <input type="number" name="rating" class="form-control" min="1" max="5" value="{{ $testimonial->rating }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Client Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                @if($testimonial->image)
                    <div class="mt-2">
                        <img src="{{ Storage::url($testimonial->image) }}" alt="Image" style="height: 60px; border-radius: 50%;">
                    </div>
                @endif
            </div>
            <div class="col-12 mb-3">
                <label class="form-label">Quote <span class="text-danger">*</span></label>
                <textarea name="quote" class="form-control" rows="3" required>{{ $testimonial->quote }}</textarea>
            </div>
        </div>

        <div class="form-check form-switch mb-4 mt-2">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ $testimonial->is_active ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="is_active">Active (Visible on Frontend)</label>
        </div>

        <button type="submit" class="btn btn-gold px-4"><i class="bi bi-save me-2"></i> Update Testimonial</button>
    </form>
</div>
@endsection
