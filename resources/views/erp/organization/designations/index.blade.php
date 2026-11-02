@extends('erp.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Designations & Job Titles</h4>
        <p class="text-muted small mb-0">Role titles and grading mapped to departments.</p>
    </div>
    <button type="button" class="btn btn-erp-gold btn-sm px-3" data-bs-toggle="modal" data-bs-target="#createDesModal">
        <i class="bi bi-plus-lg me-1"></i> Add Designation
    </button>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Designation / Job Title</th>
                    <th>Department</th>
                    <th>Grade / Pay Band</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($designations as $des)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $des->name }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $des->department?->name ?? 'General' }}</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $des->grade ?? 'Standard' }}</span>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editDesModal{{ $des->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('erp.designations.destroy', $des) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this designation?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Designation Modal -->
                    <div class="modal fade" id="editDesModal{{ $des->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="{{ route('erp.designations.update', $des) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Designation</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Designation Title <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" value="{{ $des->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Department</label>
                                            <select name="department_id" class="form-select">
                                                <option value="">-- No Specific Department --</option>
                                                @foreach($departments as $dept)
                                                    <option value="{{ $dept->id }}" {{ $des->department_id == $dept->id ? 'selected' : '' }}>
                                                        {{ $dept->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Grade / Level</label>
                                            <input type="text" name="grade" class="form-control" value="{{ $des->grade }}">
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
                        <td colspan="4" class="text-center py-4 text-muted">No designations registered.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($designations->hasPages())
        <div class="p-3 border-top">
            {{ $designations->links() }}
        </div>
    @endif
</div>

<!-- Create Designation Modal -->
<div class="modal fade" id="createDesModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('erp.designations.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add New Designation</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Designation Title <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Chief Structural Engineer" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Department</label>
                        <select name="department_id" class="form-select">
                            <option value="">-- No Specific Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Grade / Level</label>
                        <input type="text" name="grade" class="form-control" placeholder="e.g. Senior / Level 4">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp-gold">Create Designation</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
