@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-0 fw-bold">Blog Posts Management</h4>
        <p class="text-muted small mb-0">Manage articles, news, and insights for the /blog page</p>
    </div>
    <a href="{{ route('admin.posts.create') }}" class="btn btn-gold">
        <i class="bi bi-plus-lg me-1"></i> Add New Post
    </a>
</div>

<!-- Header & Section Settings Form -->
<div class="card p-4 mb-4">
    <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark">
        <i class="bi bi-gear-fill me-2 text-warning"></i> Blog Page Header & Section Title Settings
    </h5>
    <form action="{{ route('admin.posts.updateSettings') }}" method="POST">
        @csrf
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Page Header Title</label>
                <input type="text" class="form-control" name="blog_title" value="{{ $settings['blog_title'] ?? 'News & Insights' }}" required>
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Page Header Subtitle</label>
                <input type="text" class="form-control" name="blog_subtitle" value="{{ $settings['blog_subtitle'] ?? 'Latest updates from the world of luxury real estate.' }}" required>
            </div>
        </div>
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">Section Eyebrow</label>
                <input type="text" class="form-control" name="blog_eyebrow" value="{{ $settings['blog_eyebrow'] ?? 'Latest Insights' }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">Section Main Heading</label>
                <input type="text" class="form-control" name="blog_heading" value="{{ $settings['blog_heading'] ?? 'From Our Blog' }}">
            </div>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-outline-primary btn-sm px-4">
                <i class="bi bi-save me-1"></i> Update Header Settings
            </button>
        </div>
    </form>
</div>

<!-- Posts List Table -->
<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 70px;">Image</th>
                    <th>Post Title</th>
                    <th>Author</th>
                    <th>Published Date</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($posts as $post)
                <tr>
                    <td>
                        @if($post->image)
                            <img src="{{ asset_url($post->image) }}" alt="Img" style="width: 60px; height: 45px; object-fit: cover; border-radius: 6px;" class="border">
                        @else
                            <div class="bg-light border text-muted d-flex align-items-center justify-content-center rounded" style="width: 60px; height: 45px;">
                                <i class="bi bi-image"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-bold text-dark fs-6">{{ $post->title }}</div>
                        <div class="small text-muted">{{ Str::limit($post->summary, 80) }}</div>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $post->author }}</span></td>
                    <td class="small text-muted">{{ optional($post->published_at)->format('M d, Y') }}</td>
                    <td>
                        @if($post->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Hidden</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.posts.edit', $post->id) }}" class="btn btn-sm btn-outline-secondary me-1" title="Edit Post">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.posts.destroy', $post->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this blog post?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete Post">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        No blog posts published yet. <a href="{{ route('admin.posts.create') }}">Create your first post now</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
        <div class="mt-3">
            {{ $posts->links() }}
        </div>
    </div>
</div>
@endsection
