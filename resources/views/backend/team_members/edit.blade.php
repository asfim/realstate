@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Edit Team Member</h4>
    <a href="{{ route('admin.team-members.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Back
    </a>
</div>

<div class="card p-4">
    <form action="{{ route('admin.team-members.update', $teamMember->id) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label class="form-label">Name <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="{{ $teamMember->name }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Role <span class="text-danger">*</span></label>
                <input type="text" name="role" class="form-control" value="{{ $teamMember->role }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="{{ $teamMember->email }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Facebook URL</label>
                <input type="url" name="facebook" class="form-control" value="{{ $teamMember->facebook }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">LinkedIn URL</label>
                <input type="url" name="linkedin" class="form-control" value="{{ $teamMember->linkedin }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label">Image</label>
                <input type="file" name="image" class="form-control" accept="image/*">
                @if($teamMember->image)
                    <div class="mt-2">
                        <img src="{{ Storage::url($teamMember->image) }}" alt="Image" style="height: 60px; border-radius: 8px;">
                    </div>
                @endif
            </div>
        </div>

        <div class="form-check form-switch mb-4 mt-2">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ $teamMember->is_active ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="is_active">Active (Visible on Frontend)</label>
        </div>

        <button type="submit" class="btn btn-gold px-4"><i class="bi bi-save me-2"></i> Update Team Member</button>
    </form>
</div>
@endsection
