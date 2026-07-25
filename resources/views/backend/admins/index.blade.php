@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-0 fw-bold">Admin Users Management</h4>
        <p class="text-muted small mb-0">Manage system administrators and assign access roles to them</p>
    </div>
    @can('admins.create')
    <a href="{{ route('admin.admins.create') }}" class="btn btn-gold">
        <i class="bi bi-plus-lg me-1"></i> Add Admin User
    </a>
    @endcan
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th>Name</th>
                    <th>Email Address</th>
                    <th>Assigned Roles</th>
                    <th class="text-end" style="width: 150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($admins as $admin)
                <tr>
                    <td>
                        <div class="fw-bold text-dark fs-6">{{ $admin->name }}</div>
                    </td>
                    <td>
                        <span class="text-secondary small">{{ $admin->email }}</span>
                    </td>
                    <td>
                        <div class="d-flex flex-wrap gap-1">
                            @forelse($admin->roles as $role)
                                <span class="badge bg-primary text-light small">{{ $role->name }}</span>
                            @empty
                                <span class="text-muted small">No roles assigned.</span>
                            @endforelse
                        </div>
                    </td>
                    <td class="text-end">
                        @can('admins.edit')
                        <a href="{{ route('admin.admins.edit', $admin->id) }}" class="btn btn-sm btn-outline-secondary me-1" title="Edit Admin">
                            <i class="bi bi-pencil"></i>
                        </a>
                        @endcan
                        
                        @can('admins.delete')
                            @if($admin->id !== 1 && $admin->email !== 'admin@example.com' && auth('admin')->id() !== $admin->id)
                                <form action="{{ route('admin.admins.destroy', $admin->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this administrator account?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger" title="Delete Admin">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            @endif
                        @endcan
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4" class="text-center py-4 text-muted">
                        No admin accounts configured.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
