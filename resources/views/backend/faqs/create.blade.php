@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="mb-0 fw-bold">Add New FAQ</h4>
    <a href="{{ route('admin.faqs.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to FAQ List
    </a>
</div>

<div class="card p-4">
    <form action="{{ route('admin.faqs.store') }}" method="POST">
        @csrf

        <div class="form-check form-switch mb-4">
            <input class="form-check-input" type="checkbox" role="switch" id="is_active" name="is_active" value="1" checked>
            <label class="form-check-label fw-bold" for="is_active">Active & Visible on Website</label>
        </div>

        <div class="row">
            <div class="col-md-10 mb-3">
                <label class="form-label fw-bold">Question *</label>
                <input type="text" class="form-control @error('question') is-invalid @enderror" name="question" value="{{ old('question') }}" placeholder="e.g. Do you offer installment payment plans?" required>
                @error('question') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>

            <div class="col-md-2 mb-3">
                <label class="form-label fw-bold">Display Order</label>
                <input type="number" class="form-control" name="sort_order" value="{{ old('sort_order', 1) }}">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label fw-bold">Answer *</label>
            <textarea class="form-control @error('answer') is-invalid @enderror" name="answer" rows="4" placeholder="Detailed explanation or answer..." required>{{ old('answer') }}</textarea>
            @error('answer') <div class="invalid-feedback">{{ $message }}</div> @enderror
        </div>

        <div class="text-end mt-3">
            <button type="submit" class="btn btn-gold px-4"><i class="bi bi-check-lg me-1"></i> Save FAQ</button>
        </div>
    </form>
</div>
@endsection
