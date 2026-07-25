@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-0 fw-bold">Services Management</h4>
        <p class="text-muted small mb-0">Manage services displayed on the /services page and homepage</p>
    </div>
    <a href="{{ route('admin.services.create') }}" class="btn btn-gold">
        <i class="bi bi-plus-lg me-1"></i> Add Service
    </a>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 60px;">Order</th>
                    <th style="width: 70px;">Icon</th>
                    <th>Service Title</th>
                    <th>Short Description</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($services as $service)
                <tr>
                    <td class="fw-bold text-muted">#{{ $service->sort_order }}</td>
                    <td class="text-center">
                        @if($service->image)
                            <img src="{{ asset_url($service->image) }}" alt="Img" style="width: 45px; height: 45px; object-fit: cover; border-radius: 8px;" class="border">
                        @else
                            <div class="bg-light p-2 rounded text-warning fs-4 border d-inline-flex align-items-center justify-content-center" style="width: 45px; height: 45px;">
                                <i class="{{ $service->icon ?? 'fa-solid fa-concierge-bell' }}"></i>
                            </div>
                        @endif
                    </td>
                    <td>
                        <div class="fw-bold text-dark fs-6">{{ $service->title }}</div>
                    </td>
                    <td class="small text-muted" style="max-width: 350px;">
                        {{ $service->short_description }}
                    </td>
                    <td>
                        @if($service->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Hidden</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.services.edit', $service->id) }}" class="btn btn-sm btn-outline-secondary me-1" title="Edit Service">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.services.destroy', $service->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this service?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete Service">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        No services added yet. <a href="{{ route('admin.services.create') }}">Add your first service now</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
