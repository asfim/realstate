@extends('erp.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Fiscal Years & Financial Periods</h4>
        <p class="text-muted small mb-0">Accounting calendars, ledger locking periods, and current financial year designation.</p>
    </div>
    <button type="button" class="btn btn-erp-gold btn-sm px-3" data-bs-toggle="modal" data-bs-target="#createFyModal">
        <i class="bi bi-plus-lg me-1"></i> Add Fiscal Year
    </button>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Period Name</th>
                    <th>Date Range</th>
                    <th>Current Active</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($fiscalYears as $fy)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $fy->name }}</div>
                        </td>
                        <td>
                            <small class="text-muted">
                                <i class="bi bi-calendar-event me-1"></i>
                                {{ $fy->start_date->format('d M Y') }} &mdash; {{ $fy->end_date->format('d M Y') }}
                            </small>
                        </td>
                        <td>
                            @if($fy->is_current)
                                <span class="badge bg-success"><i class="bi bi-check-circle-fill me-1"></i>CURRENT FY</span>
                            @else
                                <span class="text-muted small">&mdash;</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $fy->status === 'active' ? 'bg-primary' : 'bg-secondary' }}">
                                {{ ucfirst($fy->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editFyModal{{ $fy->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('erp.fiscal-years.destroy', $fy) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this fiscal year?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" {{ $fy->is_current ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Modal -->
                    <div class="modal fade" id="editFyModal{{ $fy->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="{{ route('erp.fiscal-years.update', $fy) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Fiscal Year</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Fiscal Year Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" value="{{ $fy->name }}" required>
                                        </div>
                                        <div class="row g-2 mb-3">
                                            <div class="col-6">
                                                <label class="form-label small fw-semibold">Start Date <span class="text-danger">*</span></label>
                                                <input type="date" name="start_date" class="form-control" value="{{ $fy->start_date->format('Y-m-d') }}" required>
                                            </div>
                                            <div class="col-6">
                                                <label class="form-label small fw-semibold">End Date <span class="text-danger">*</span></label>
                                                <input type="date" name="end_date" class="form-control" value="{{ $fy->end_date->format('Y-m-d') }}" required>
                                            </div>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Status <span class="text-danger">*</span></label>
                                            <select name="status" class="form-select" required>
                                                <option value="active" {{ $fy->status === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="closed" {{ $fy->status === 'closed' ? 'selected' : '' }}>Closed</option>
                                            </select>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="is_current" value="1" id="editIsCurrent{{ $fy->id }}" {{ $fy->is_current ? 'checked' : '' }}>
                                            <label class="form-check-label small fw-semibold" for="editIsCurrent{{ $fy->id }}">
                                                Set as Current Active Fiscal Year
                                            </label>
                                        </div>
                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                        <button type="submit" class="btn btn-erp-gold">Save Changes</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No fiscal periods found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($fiscalYears->hasPages())
        <div class="p-3 border-top">
            {{ $fiscalYears->links() }}
        </div>
    @endif
</div>

<!-- Create Modal -->
<div class="modal fade" id="createFyModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('erp.fiscal-years.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Fiscal Year</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Fiscal Year Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. FY 2026-2027" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" name="start_date" class="form-control" value="2026-07-01" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">End Date <span class="text-danger">*</span></label>
                            <input type="date" name="end_date" class="form-control" value="2027-06-30" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active" selected>Active</option>
                            <option value="closed">Closed</option>
                        </select>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="is_current" value="1" id="createIsCurrent">
                        <label class="form-check-label small fw-semibold" for="createIsCurrent">
                            Set as Current Active Fiscal Year
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp-gold">Create Fiscal Year</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

