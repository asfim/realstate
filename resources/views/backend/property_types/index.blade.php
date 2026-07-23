@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Property Types Management</h4>
    <a href="{{ route('admin.property-types.create') }}" class="btn btn-navy">
        <i class="bi bi-plus-lg me-1"></i> Add Property Type
    </a>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Image</th>
                    <th>Property Title (Type)</th>
                    <th>Icon Class (Optional)</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($types as $type)
                <tr>
                    <td>
                        @if($type->image)
                            <img src="{{ Storage::url($type->image) }}" alt="Img" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center text-muted" style="width: 60px; height: 60px; border-radius: 8px;">
                                <i class="bi bi-image"></i>
                            </div>
                        @endif
                    </td>
                    <td class="fw-bold">{{ $type->title }}</td>
                    <td>
                        @if($type->icon_class)
                            <code>{{ $type->icon_class }}</code>
                        @else
                            <span class="text-muted small">Default (fa-building)</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.property-types.edit', $type->id) }}" class="btn btn-sm btn-gold text-white">
                            <i class="bi bi-pencil-square me-1"></i> Edit
                        </a>
                        <form action="{{ route('admin.property-types.destroy', $type->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this property type? Properties categorized under this type will need to be re-assigned.');">
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
                    <td colspan="4" class="text-center py-4 text-muted">No property types found. <a href="{{ route('admin.property-types.create') }}">Create one now</a></td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
