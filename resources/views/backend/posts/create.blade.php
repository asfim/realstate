@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Create New Blog Post</h4>
    <a href="{{ route('admin.posts.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Posts List
    </a>
</div>

<div class="card p-4">
    <form action="{{ route('admin.posts.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" checked>
            <label class="form-check-label fw-bold" for="is_active">Active & Published on Website</label>
        </div>

        <div class="row">
            <div class="col-md-7 mb-3">
                <label class="form-label fw-bold">Post Title *</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title') }}" placeholder="e.g. 5 Signs A Neighbourhood Will Appreciate In Value" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Author Name</label>
                <input type="text" class="form-control" name="author" value="{{ old('author', 'Aurelia Editorial') }}">
            </div>

            <div class="col-md-2 mb-3">
                <label class="form-label fw-bold">Publish Date</label>
                <input type="date" class="form-control" name="published_at" value="{{ old('published_at', date('Y-m-d')) }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Short Summary / Excerpt</label>
            <textarea class="form-control" name="summary" rows="2" placeholder="Brief intro text shown on blog listing cards...">{{ old('summary') }}</textarea>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Full Post Content *</label>
            <textarea class="form-control @error('content') is-invalid @enderror" name="content" rows="8" placeholder="Write complete article details..." required>{{ old('content') }}</textarea>
            @error('content') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="mb-4">
            <label class="form-label fw-bold">Featured Cover Image</label>
            <input type="file" class="form-control" name="image" accept="image/*">
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-gold px-4"><i class="bi bi-check-lg me-1"></i> Save Post</button>
        </div>
    </form>
</div>
@endsection
