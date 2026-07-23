@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Company Journey & Milestones</h4>
        <p class="text-muted small mb-0">Manage timeline milestones displayed on the About Us page</p>
    </div>
    <a href="{{ route('admin.journeys.create') }}" class="btn btn-gold">
        <i class="bi bi-plus-lg me-1"></i> Add Milestone
    </a>
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 70px;">Sl</th>
                    <th style="width: 100px;">Year</th>
                    <th>Milestone Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($journeys as $item)
                <tr>
                    <td class="fw-bold text-muted">{{ $loop->iteration }}</td>
                    <td>
                        <span class="badge bg-gold text-dark fs-6">{{ $item->year }}</span>
                    </td>
                    <td>
                        <div class="fw-bold text-dark fs-6">{{ $item->title }}</div>
                    </td>
                    <td class="small text-muted" style="max-width: 400px;">
                        {{ $item->description }}
                    </td>
                    <td>
                        @if($item->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Hidden</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.journeys.edit', $item->id) }}" class="btn btn-sm btn-outline-secondary me-1" title="Edit Milestone">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.journeys.destroy', $item->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this milestone?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete Milestone">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">
                        No journey milestones added yet. <a href="{{ route('admin.journeys.create') }}">Add your first milestone now</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
