@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Team Members</h4>
    <a href="{{ route('admin.team-members.create') }}" class="btn btn-navy">
        <i class="bi bi-plus-lg"></i> Add Team Member
    </a>
</div>

<div class="card p-4">
    <table class="table table-hover align-middle">
        <thead class="table-light">
            <tr>
                <th>Image</th>
                <th>Name</th>
                <th>Role</th>
                <th>Email</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($members as $member)
            <tr>
                <td>
                    @if($member->image)
                    <img src="{{ Storage::url($member->image) }}" alt="Image" style="width:50px; height:50px; object-fit:cover; border-radius:8px;">
                    @else
                    <span class="text-muted">No Image</span>
                    @endif
                </td>
                <td class="fw-bold">{{ $member->name }}</td>
                <td>{{ $member->role }}</td>
                <td>{{ $member->email }}</td>
                <td>
                    @if($member->is_active)
                    <span class="badge bg-success">Active</span>
                    @else
                    <span class="badge bg-secondary">Hidden</span>
                    @endif
                </td>
                <td>
                    <a href="{{ route('admin.team-members.edit', $member->id) }}" class="btn btn-sm btn-outline-primary"><i class="bi bi-pencil"></i></a>
                    <form action="{{ route('admin.team-members.destroy', $member->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this team member?');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-outline-danger"><i class="bi bi-trash"></i></button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">No team members found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
