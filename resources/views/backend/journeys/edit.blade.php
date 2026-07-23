@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Edit Milestone</h4>
    <a href="{{ route('admin.journeys.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Journey Milestones
    </a>
</div>

<div class="card p-4">
    <form action="{{ route('admin.journeys.update', $journey->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" {{ $journey->is_active ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="is_active">Active & Visible on Website</label>
        </div>

        <div class="row">
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Year / Period *</label>
                <input type="text" class="form-control @error('year') is-invalid @enderror" name="year" value="{{ old('year', $journey->year) }}" required>
                @error('year') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-7 mb-3">
                <label class="form-label fw-bold">Milestone Title *</label>
                <input type="text" class="form-control @error('title') is-invalid @enderror" name="title" value="{{ old('title', $journey->title) }}" required>
                @error('title') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-2 mb-3">
                <label class="form-label fw-bold">Display Order</label>
                <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', $journey->sort_order) }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Description *</label>
            <textarea class="form-control @error('description') is-invalid @enderror" name="description" rows="3" required>{{ old('description', $journey->description) }}</textarea>
            @error('description') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="text-end mt-3">
            <button type="submit" class="btn btn-gold px-4"><i class="bi bi-check-lg me-1"></i> Update Milestone</button>
        </div>
    </form>
</div>
@endsection
