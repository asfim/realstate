@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 page-title-row">
    <h4 class="mb-0 fw-bold">All Projects</h4>
    <a href="{{ route('admin.featured-project.create') }}" class="btn btn-gold">
        <i class="bi bi-plus-lg me-1"></i> Add Project
    </a>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Image</th>
                    <th>Title & Location</th>
                    <th>Price</th>
                    <th>Details</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $project)
                <tr>
                    <td>
                        @if($project->image)
                            <img src="{{ asset_url($project->image) }}" alt="Img" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 60px; border-radius: 8px;">
                                <i class="bi bi-image"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-bold">{{ $project->title }}</div>
                        <div class="small text-muted"><i class="bi bi-geo-alt"></i> {{ $project->location }}</div>
                    </td>
                    <td class="fw-bold text-success">{{ $project->price }}</td>
                    <td class="small text-muted">
                        <div>{{ $project->beds }} Beds &bull; {{ $project->baths }} Baths</div>
                        <div>{{ $project->area }} sqft</div>
                    </td>
                    <td>
                        @if($project->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Hidden</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.featured-project.edit', $project->id) }}" class="btn btn-sm btn-outline-secondary">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.featured-project.destroy', $project->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this project?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        No featured projects added yet.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
