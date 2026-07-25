@extends('backend.layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.roles.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back to Roles
    </a>
    <h4 class="fw-bold mb-0">Create Security Role</h4>
    <p class="text-muted small mb-0">Define a new security role and check the permissions they should have.</p>
</div>

<div class="card p-4">
    <form action="{{ route('admin.roles.store') }}" method="POST">
        @csrf
        
        <div class="mb-4">
            <label for="name" class="form-label fw-semibold">Role Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name') }}" placeholder="e.g. Property Manager" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <h5 class="fw-bold mb-3 border-bottom pb-2">Assign Permissions</h5>

        <div class="row">
            @foreach($groupedPermissions as $module => $permissions)
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm border">
                        <div class="card-header bg-light fw-bold text-uppercase small text-dark d-flex justify-content-between align-items-center">
                            <span>{{ str_replace('-', ' ', $module) }}</span>
                            <button type="button" class="btn btn-xs btn-outline-primary select-all-module px-2 py-0" style="font-size: 0.75rem;">Select All</button>
                        </div>
                        <div class="card-body">
                            @foreach($permissions as $permission)
                                <div class="form-check mb-2">
                                    <input class="form-check-input permission-checkbox" type="checkbox" name="permissions[]" value="{{ $permission->name }}" id="perm_{{ $permission->id }}">
                                    <label class="form-check-label small text-dark" for="perm_{{ $permission->id }}">
                                        {{ str_replace($module . '.', '', $permission->name) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-4 pt-3 border-top d-flex gap-2">
            <button type="submit" class="btn btn-gold">Create Role</button>
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const selectButtons = document.querySelectorAll('.select-all-module');
    selectButtons.forEach(button => {
        button.addEventListener('click', function () {
            const card = this.closest('.card');
            const checkboxes = card.querySelectorAll('.permission-checkbox');
            const allChecked = Array.from(checkboxes).every(cb => cb.checked);
            
            checkboxes.forEach(cb => {
                cb.checked = !allChecked;
            });

            this.textContent = allChecked ? 'Select All' : 'Deselect All';
        });
    });
});
</script>
@endsection
