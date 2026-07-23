@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="mb-0 fw-bold">FAQ Items Management</h4>
        <p class="text-muted small mb-0">Manage frequently asked questions displayed on the /blog page</p>
    </div>
    <a href="{{ route('admin.faqs.create') }}" class="btn btn-gold">
        <i class="bi bi-plus-lg me-1"></i> Add FAQ
    </a>
</div>

<!-- Header Settings Form -->
<div class="card p-4 mb-4">
    <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark">
        <i class="bi bi-gear-fill me-2 text-warning"></i> FAQ Section Header Settings
    </h5>
    <form action="{{ route('admin.faqs.updateSettings') }}" method="POST">
        @csrf
        <div class="row g-3 mb-3">
            <div class="col-md-6">
                <label class="form-label fw-bold">FAQ Eyebrow Tag</label>
                <input type="text" class="form-control" name="faq_eyebrow" value="{{ $settings['faq_eyebrow'] ?? 'FAQ' }}">
            </div>
            <div class="col-md-6">
                <label class="form-label fw-bold">FAQ Section Title</label>
                <input type="text" class="form-control" name="faq_heading" value="{{ $settings['faq_heading'] ?? 'Frequently Asked Questions' }}">
            </div>
        </div>
        <div class="text-end">
            <button type="submit" class="btn btn-outline-primary btn-sm px-4">
                <i class="bi bi-save me-1"></i> Update FAQ Headers
            </button>
        </div>
    </form>
</div>

<!-- FAQ Table -->
<div class="card p-4">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 70px;">Order</th>
                    <th>Question</th>
                    <th>Answer</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($faqs as $faq)
                <tr>
                    <td class="fw-bold text-muted">#{{ $faq->sort_order }}</td>
                    <td>
                        <div class="fw-bold text-dark fs-6">{{ $faq->question }}</div>
                    </td>
                    <td class="small text-muted" style="max-width: 450px;">
                        {{ $faq->answer }}
                    </td>
                    <td>
                        @if($faq->is_active)
                            <span class="badge bg-success">Active</span>
                        @else
                            <span class="badge bg-secondary">Hidden</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <a href="{{ route('admin.faqs.edit', $faq->id) }}" class="btn btn-sm btn-outline-secondary me-1" title="Edit FAQ">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form action="{{ route('admin.faqs.destroy', $faq->id) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Are you sure you want to delete this FAQ?');">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-outline-danger" title="Delete FAQ">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-4 text-muted">
                        No FAQs added yet. <a href="{{ route('admin.faqs.create') }}">Add your first FAQ item now</a>.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
