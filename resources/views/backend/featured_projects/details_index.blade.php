@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Manage Project Details</h4>
    <a href="{{ route('admin.featured-project.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-list-ul me-1"></i> All Projects List
    </a>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Image</th>
                    <th>Title & Location</th>
                    <th>Price & Size</th>
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
                    <td>
                        <div class="fw-bold text-success">{{ $project->price }}</div>
                        <div class="small text-muted">{{ $project->beds }} Beds &bull; {{ $project->baths }} Baths &bull; {{ $project->area }} sqft</div>
                    </td>
                    <td>
                        @if($project->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Hidden</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('project.featured', $project->id) }}" target="_blank" class="btn btn-sm btn-outline-info me-1" title="Preview Frontend Page">
                            <i class="bi bi-eye"></i> View Page
                        </a>
                        <a href="{{ route('admin.featured-project.details.edit', $project->id) }}" class="btn btn-sm btn-gold">
                            <i class="bi bi-pencil-square me-1"></i> Edit Details
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        No projects found. Please add a project first.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
