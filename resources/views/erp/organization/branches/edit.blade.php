@extends('erp.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Edit Branch</h4>
        <p class="text-muted small mb-0">Update branch details for {{ $branch->name }}.</p>
    </div>
    <a href="{{ route('erp.branches.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Branches
    </a>
</div>

<div class="card card-custom">
    <div class="card-body p-4">
        <form action="{{ route('erp.branches.update', $branch) }}" method="POST">
            @csrf
            @method('PUT')
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Branch Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control" value="{{ old('code', $branch->code) }}" required>
                </div>
                <div class="col-md-8">
                    <label class="form-label fw-semibold small">Branch Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $branch->name) }}" required>
                </div>

                <div class="col-12">
                    <label class="form-label fw-semibold small">Address</label>
                    <textarea name="address" rows="2" class="form-control">{{ old('address', $branch->address) }}</textarea>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Phone Number</label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $branch->phone) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $branch->email) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Branch Manager</label>
                    <select name="manager_admin_id" class="form-select">
                        <option value="">-- None Assigned --</option>
                        @foreach($admins as $admin)
                            <option value="{{ $admin->id }}" {{ old('manager_admin_id', $branch->manager_admin_id) == $admin->id ? 'selected' : '' }}>
                                {{ $admin->name }} ({{ $admin->email }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-3">
                    <label class="form-label fw-semibold small">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="active" {{ old('status', $branch->status) === 'active' ? 'selected' : '' }}>Active</option>
                        <option value="inactive" {{ old('status', $branch->status) === 'inactive' ? 'selected' : '' }}>Inactive</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-center mt-md-4">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="is_head_office" value="1" id="isHeadOffice" {{ old('is_head_office', $branch->is_head_office) ? 'checked' : '' }}>
                        <label class="form-check-label fw-semibold small" for="isHeadOffice">
                            Designate as Head Office
                        </label>
                    </div>
                </div>
            </div>

            <div class="mt-4 pt-3 border-top d-flex justify-content-end gap-2">
                <a href="{{ route('erp.branches.index') }}" class="btn btn-light px-4">Cancel</a>
                <button type="submit" class="btn btn-erp-gold px-4">Update Branch</button>
            </div>
        </form>
    </div>
</div>
@endsection

