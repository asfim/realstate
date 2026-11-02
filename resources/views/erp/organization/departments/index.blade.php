@extends('erp.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Departments</h4>
        <p class="text-muted small mb-0">Organizational units for workforce, approval routing, and budgeting.</p>
    </div>
    <button type="button" class="btn btn-erp-gold btn-sm px-3" data-bs-toggle="modal" data-bs-target="#createDeptModal">
        <i class="bi bi-plus-lg me-1"></i> Add Department
    </button>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Department Code</th>
                    <th>Department Name</th>
                    <th>Designations Count</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $dept)
                    <tr>
                        <td>
                            <span class="badge bg-dark px-2 py-1 font-monospace">{{ $dept->code ?? 'N/A' }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $dept->name }}</div>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $dept->designations_count }} Roles</span>
                        </td>
                        <td>
                            <span class="badge {{ $dept->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($dept->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editDeptModal{{ $dept->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('erp.departments.destroy', $dept) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this department?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Department Modal -->
                    <div class="modal fade" id="editDeptModal{{ $dept->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="{{ route('erp.departments.update', $dept) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Department</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Department Code</label>
                                            <input type="text" name="code" class="form-control" value="{{ $dept->code }}">
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Department Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" value="{{ $dept->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Status <span class="text-danger">*</span></label>
                                            <select name="status" class="form-select" required>
                                                <option value="active" {{ $dept->status === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ $dept->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                            </select>
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
                        <td colspan="5" class="text-center py-4 text-muted">No departments created yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($departments->hasPages())
        <div class="p-3 border-top">
            {{ $departments->links() }}
        </div>
    @endif
</div>

<!-- Create Department Modal -->
<div class="modal fade" id="createDeptModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('erp.departments.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Department</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Department Code</label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. SALES">
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Department Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Sales & CRM" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Status <span class="text-danger">*</span></label>
                        <select name="status" class="form-select" required>
                            <option value="active" selected>Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp-gold">Create Department</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
