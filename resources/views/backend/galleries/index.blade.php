@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-0 fw-bold">Media Gallery Management</h4>
        <p class="text-muted small mb-0">Manage photo gallery, virtual video tour, and page headers</p>
    </div>
    <a href="{{ route('admin.galleries.create') }}" class="btn btn-gold">
        <i class="bi bi-plus-lg me-1"></i> Upload Photo
    </a>
</div>

<!-- Virtual Video Tour & Header Settings Form -->
<div class="card p-4 mb-4">
    <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark">
        <i class="bi bi-camera-reel-fill me-2 text-warning"></i> Page Header & Virtual Video Tour Settings
    </h5>
    <form action="{{ route('admin.galleries.updateSettings') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Page Header Title</label>
                <input type="text" class="form-control" name="gallery_title" value="{{ $settings['gallery_title'] ?? 'Media Gallery' }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Page Header Subtitle</label>
                <input type="text" class="form-control" name="gallery_subtitle" value="{{ $settings['gallery_subtitle'] ?? 'Take a visual tour of our finest properties.' }}" required>
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-4">
                <label class="form-label fw-bold">Virtual Tour Eyebrow</label>
                <input type="text" class="form-control" name="gallery_tour_eyebrow" value="{{ $settings['gallery_tour_eyebrow'] ?? 'Virtual Experience' }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Virtual Tour Section Title</label>
                <input type="text" class="form-control" name="gallery_tour_title" value="{{ $settings['gallery_tour_title'] ?? 'Take A Virtual Tour' }}">
            </div>
            <div class="col-md-4">
                <label class="form-label fw-bold">Tour Video Card Caption</label>
                <input type="text" class="form-control" name="gallery_tour_video_title" value="{{ $settings['gallery_tour_video_title'] ?? 'Explore Aurelia Heights in 360°' }}">
            </div>
        </div>

        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Video Tour Cover / Poster Image</label>
                <input type="file" class="form-control mb-2" name="gallery_tour_image" accept="image/*">
                @if(setting('gallery_tour_image'))
                    <img src="{{ asset_url(setting('gallery_tour_image')) }}" alt="Cover" style="height: 90px; border-radius: 8px;" class="border">
                @endif
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Video Tour Link / Embed URL</label>
                <input type="text" class="form-control" name="gallery_tour_video_url" value="{{ $settings['gallery_tour_video_url'] ?? 'https://www.youtube.com/watch?v=dQw4w9WgXcQ' }}" placeholder="https://www.youtube.com/embed/... or MP4 link">
            </div>
        </div>

        <div class="text-end">
            <button type="submit" class="btn btn-outline-primary btn-sm px-4">
                <i class="bi bi-save me-1"></i> Update Tour & Settings
            </button>
        </div>
    </form>
</div>

<!-- Photo Gallery List -->
<div class="card p-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold mb-0 text-dark"><i class="bi bi-images me-2 text-warning"></i> Photo Gallery List</h5>
    </div>

    <div class="row g-3">
        @forelse($galleries as $photo)
        <div class="col-md-4 col-lg-3">
            <div class="card h-100 border p-2 text-center position-relative">
                <img src="{{ asset_url($photo->image) }}" alt="{{ $photo->title }}" style="height: 160px; object-fit: cover; border-radius: 8px;" class="mb-2">
                <div class="fw-bold text-dark small text-truncate" title="{{ $photo->title }}">{{ $photo->title }}</div>
                @if($photo->category)
                    <div><span class="badge bg-light text-dark border mt-1">{{ $photo->category }}</span></div>
                @endif
                <div class="mt-2 d-flex justify-content-between align-items-center border-top pt-2">
                    <span class="small text-muted">Order: #{{ $photo->sort_order }}</span>
                    <div>
                        <a href="{{ route('admin.galleries.edit', $photo->id) }}" class="btn btn-xs btn-outline-secondary p-1 px-2 me-1" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.galleries.destroy', $photo->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Delete this photo?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-xs btn-outline-danger p-1 px-2" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center text-muted py-4">
            No gallery photos uploaded yet. Click <strong>Upload Photo</strong> above to add photos.
        </div>
        @endforelse
    </div>
</div>
@endsection
