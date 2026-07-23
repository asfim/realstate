@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Manage Property Details & Construction</h4>
    <span class="text-muted small">Select a property below to edit its description, gallery, floor plans, and construction status.</span>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Image</th>
                    <th>Property Title</th>
                    <th>Type</th>
                    <th>Details Status</th>
                    <th>Construction Status</th>
                    <th class="text-end">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($properties as $property)
                <tr>
                    <td>
                        @if($property->image)
                            <img src="{{ Storage::url($property->image) }}" alt="Img" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 60px; border-radius: 8px;">
                                <i class="bi bi-image"></i>
                            </div>
                        @endif
                    </td>
                    <td class="fw-bold">{{ $property->title }}</td>
                    <td>
                        <span class="badge bg-navy text-dark">{{ $property->type }}</span>
                    </td>
                    <td>
                        @if($property->detail && !empty($property->detail->description))
                            <span class="badge bg-success">Added</span>
                            <span class="small text-muted ms-2">
                                ({{ count($property->detail->gallery ?? []) }} gallery, {{ count($property->detail->floor_plans ?? []) }} plans)
                            </span>
                        @else
                            <span class="badge bg-warning text-dark">Missing Details</span>
                        @endif
                    </td>
                    <td>
                        @if($property->constructionStatus && $property->constructionStatus->status_updated_at)
                            <span class="badge bg-navy text-white">{{ $property->constructionStatus->status_updated_at }}</span>
                            <span class="small text-muted ms-2">({{ count($property->constructionStatus->progress_items ?? []) }} tasks)</span>
                        @else
                            <span class="badge bg-secondary">Not updated</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.properties.manage.edit', $property->id) }}" class="btn btn-sm btn-gold text-white">
                            <i class="bi bi-gear-fill me-1"></i> Manage Details
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">No properties found. Please add a property first.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
