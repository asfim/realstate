@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Amenities Management</h4>
        <p class="text-muted small mb-0">Manage amenities displayed in the "Everything Your New Home Needs" section</p>
    </div>
    <a href="{{ route('admin.amenities.create') }}" class="btn btn-gold">
        <i class="bi bi-plus-lg me-1"></i> Add Amenity
    </a>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 70px;">Order</th>
                    <th style="width: 80px;">Icon</th>
                    <th>Amenity Name</th>
                    <th>FontAwesome Class</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($amenities as $amenity)
                <tr>
                    <td class="fw-bold text-muted">#{{ $amenity->sort_order }}</td>
                    <td class="text-center">
                        <div class="bg-light p-2 rounded text-warning fs-4 border d-inline-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                            <i class="{{ $amenity->icon ?? 'fa-solid fa-star' }}"></i>
                        </div>
                    </td>
                    <td>
                        <div class="fw-bold text-dark fs-6">{{ $amenity->title }}</div>
                    </td>
                    <td>
                        <code class="small text-primary">{{ $amenity->icon }}</code>
                    </td>
                    <td>
                        @if($amenity->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Hidden</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.amenities.edit', $amenity->id) }}" class="btn btn-sm btn-outline-secondary me-1" title="Edit Amenity">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.amenities.destroy', $amenity->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this amenity?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete Amenity">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        No amenities added yet. <a href="{{ route('admin.amenities.create') }}">Add your first amenity now</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
