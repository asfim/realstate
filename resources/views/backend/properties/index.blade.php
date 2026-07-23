@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Properties Management</h4>
    <a href="{{ route('admin.properties.create') }}" class="btn btn-navy">
        <i class="bi bi-plus-lg me-1"></i> Add Property
    </a>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Image</th>
                    <th>Title</th>
                    <th>Type</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
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
                    <td>
                        <div class="fw-bold">{{ $property->title }}</div>
                    </td>
                    <td>
                        <span class="badge bg-navy text-dark">{{ $property->type }}</span>
                    </td>
                    <td>
                        @if($property->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Hidden</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.properties.edit', $property->id) }}" class="btn btn-sm btn-gold text-white">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </a>
                        <form action="{{ route('admin.properties.destroy', $property->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this property? This will delete all details, floor plans, and construction status.');">
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
                    <td colspan="5" class="text-center py-4 text-muted">No properties found. <a href="{{ route('admin.properties.create') }}">Create one now</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
