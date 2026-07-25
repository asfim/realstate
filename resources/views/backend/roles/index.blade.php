@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">Roles & Permissions Management</h4>
        <p class="text-muted small mb-0">Define and manage security roles and their associated system privileges</p>
    </div>
    @can('roles.create')
    <a href="{{ route('admin.roles.create') }}" class="btn btn-gold">
        <i class="bi bi-plus-lg me-1"></i> Add Role
    </a>
    @endcan
</div>

<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 150px;">Role Name</th>
                    <th>Permissions</th>
                    <th class="text-end" style="width: 150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($roles as $role)
                <tr>
                    <td>
                        <div class="fw-bold text-dark fs-6">{{ $role->name }}</div>
                    </td>
                    <td>
                        @if($role->name === 'Super Admin')
                            <span class="badge bg-gold">ALL PRIVILEGES (Gate Bypass)</span>
                        @else
                            <div class="d-flex flex-wrap gap-1">
                                @forelse($role->permissions as $permission)
                                    <span class="badge bg-secondary text-light small">{{ $permission->name }}</span>
                                @empty
                                    <span class="text-muted small">No permissions assigned.</span>
                                @endforelse
                            </div>
                        @endif
                    </td>
                    <td class="text-end">
                        @if($role->name !== 'Super Admin')
                            @can('roles.edit')
                            <a href="{{ route('admin.roles.edit', $role->id) }}" class="btn btn-sm btn-outline-secondary me-1" title="Edit Role">
                                <i class="bi bi-pencil"></i>
                            </a>
                            @endcan
                            @can('roles.delete')
                            <form action="{{ route('admin.roles.destroy', $role->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this role?');">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-sm btn-outline-danger" title="Delete Role">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                            @endcan
                        @else
                            <span class="text-muted small italic">System Protected</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="text-center py-4 text-muted">
                        No roles added yet. <a href="{{ route('admin.roles.create') }}">Create your first role now</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
