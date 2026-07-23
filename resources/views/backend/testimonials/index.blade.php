@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Testimonials</h4>
    <a href="{{ route('admin.testimonials.create') }}" class="btn btn-navy">
        <i class="bi bi-plus-lg"></i> Add Testimonial
    </a>
</div>

<div class="card p-4">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Role</th>
                <th>Rating</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($testimonials as $testimonial)
            <tr>
                <td>
                    @if($testimonial->image)
                    <img src="{{ Storage::url($testimonial->image) }}" alt="Image" style="width:50px; height:50px; object-fit:cover; border-radius:50%;">
                    @else
                    <span class="text-muted">No Image</span>
                    @endif
                </td>
                <td class="fw-bold">{{ $testimonial->name }}</td>
                <td>{{ $testimonial->role }}</td>
                <td>
                    @for($i=0; $i<$testimonial->rating; $i++)
                        <i class="bi bi-star-fill text-warning"></i>
                    @endfor
                </td>
                <td>
                    @if($testimonial->is_active)
                    <span class="badge bg-success">Active</span>
                    @else
                    <span class="badge bg-secondary">Hidden</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.testimonials.edit', $testimonial->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('admin.testimonials.destroy', $testimonial->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this testimonial?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">No testimonials found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
