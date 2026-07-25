@extends('backend.layouts.app')

@section('content')
<div class="mb-4">
    <a href="{{ route('admin.admins.index') }}" class="btn btn-sm btn-outline-secondary mb-3">
        <i class="bi bi-arrow-left"></i> Back to Admins
    </a>
    <h4 class="fw-bold mb-0">Edit Admin User: {{ $admin->name }}</h4>
    <p class="text-muted small mb-0">Modify system administrator credentials and update role assignments.</p>
</div>

<div class="card p-4" style="max-width: 700px;">
    <form action="{{ route('admin.admins.update', $admin->id) }}" method="POST">
        @csrf
        @method('PUT')
        
        <div class="mb-3">
            <label for="name" class="form-label fw-semibold">Full Name</label>
            <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $admin->name) }}" placeholder="e.g. John Doe" required>
            @error('name')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-3">
            <label for="email" class="form-label fw-semibold">Email Address</label>
            <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $admin->email) }}" placeholder="e.g. john@example.com" required>
            @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="row mb-3 bg-light p-3 rounded border mb-4">
            <div class="col-12 mb-2">
                <span class="fw-bold small text-secondary"><i class="bi bi-info-circle me-1"></i> Update Password (Leave blank to keep current password)</span>
            </div>
            <div class="col-md-6">
                <label for="password" class="form-label fw-semibold small">New Password</label>
                <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="At least 8 characters">
                @error('password')
                    <div class="invalid-feedback">{{ $message }}</div>
                @enderror
            </div>
            <div class="col-md-6">
                <label for="password_confirmation" class="form-label fw-semibold small">Confirm New Password</label>
                <input type="password" class="form-control" id="password_confirmation" name="password_confirmation" placeholder="Confirm password">
            </div>
        </div>

        <div class="mb-4">
            <label class="form-label fw-semibold d-block">Modify Security Roles</label>
            <div class="d-flex flex-wrap gap-3">
                @foreach($roles as $role)
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="roles[]" value="{{ $role->name }}" id="role_{{ $role->id }}" {{ in_array($role->name, $adminRoles) ? 'checked' : '' }}>
                        <label class="form-check-label text-dark small" for="role_{{ $role->id }}">
                            {{ $role->name }}
                        </label>
                    </div>
                @endforeach
            </div>
            @error('roles')
                <div class="text-danger small mt-1">{{ $message }}</div>
            @enderror
        </div>

        <div class="pt-3 border-top d-flex gap-2">
            <button type="submit" class="btn btn-gold">Update Admin User</button>
            <a href="{{ route('admin.admins.index') }}" class="btn btn-secondary">Cancel</a>
        </div>
    </form>
</div>
@endsection
