@extends('erp.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Cost Centres & Allocation Pools</h4>
        <p class="text-muted small mb-0">Track project budgets, site overheads, and administrative cost allocations.</p>
    </div>
    <button type="button" class="btn btn-erp-gold btn-sm px-3" data-bs-toggle="modal" data-bs-target="#createCcModal">
        <i class="bi bi-plus-lg me-1"></i> Add Cost Centre
    </button>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Cost Centre Code</th>
                    <th>Name</th>
                    <th>Branch Allocation</th>
                    <th>Parent Centre</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($costCenters as $cc)
                    <tr>
                        <td>
                            <span class="badge bg-dark px-2 py-1 font-monospace">{{ $cc->code }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $cc->name }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $cc->branch?->name ?? 'Company-Wide' }}</span>
                        </td>
                        <td>
                            <small class="text-muted">{{ $cc->parent?->name ?? '—' }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $cc->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($cc->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editCcModal{{ $cc->id }}">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form action="{{ route('erp.cost-centers.destroy', $cc) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this cost center?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>

                    <!-- Edit Cost Center Modal -->
                    <div class="modal fade" id="editCcModal{{ $cc->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="{{ route('erp.cost-centers.update', $cc) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Edit Cost Centre</h5>
                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Cost Centre Code <span class="text-danger">*</span></label>
                                            <input type="text" name="code" class="form-control" value="{{ $cc->code }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Cost Centre Name <span class="text-danger">*</span></label>
                                            <input type="text" name="name" class="form-control" value="{{ $cc->name }}" required>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Branch</label>
                                            <select name="branch_id" class="form-select">
                                                <option value="">-- All / Company Wide --</option>
                                                @foreach($branches as $br)
                                                    <option value="{{ $br->id }}" {{ $cc->branch_id == $br->id ? 'selected' : '' }}>{{ $br->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Parent Cost Centre</label>
                                            <select name="parent_id" class="form-select">
                                                <option value="">-- None (Top Level) --</option>
                                                @foreach($parents as $parent)
                                                    @if($parent->id !== $cc->id)
                                                        <option value="{{ $parent->id }}" {{ $cc->parent_id == $parent->id ? 'selected' : '' }}>{{ $parent->name }} ({{ $parent->code }})</option>
                                                    @endif
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Status <span class="text-danger">*</span></label>
                                            <select name="status" class="form-select" required>
                                                <option value="active" {{ $cc->status === 'active' ? 'selected' : '' }}>Active</option>
                                                <option value="inactive" {{ $cc->status === 'inactive' ? 'selected' : '' }}>Inactive</option>
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
                        <td colspan="6" class="text-center py-4 text-muted">No cost centres created yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($costCenters->hasPages())
        <div class="p-3 border-top">
            {{ $costCenters->links() }}
        </div>
    @endif
</div>

<!-- Create Cost Center Modal -->
<div class="modal fade" id="createCcModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('erp.cost-centers.store') }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Cost Centre</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Cost Centre Code <span class="text-danger">*</span></label>
                        <input type="text" name="code" class="form-control" placeholder="e.g. CC-SITE-01" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Cost Centre Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Aurelia Green Valley Project Cost" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Branch</label>
                        <select name="branch_id" class="form-select">
                            <option value="">-- All / Company Wide --</option>
                            @foreach($branches as $br)
                                <option value="{{ $br->id }}">{{ $br->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Parent Cost Centre</label>
                        <select name="parent_id" class="form-select">
                            <option value="">-- None (Top Level) --</option>
                            @foreach($parents as $parent)
                                <option value="{{ $parent->id }}">{{ $parent->name }} ({{ $parent->code }})</option>
                            @endforeach
                        </select>
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
                    <button type="submit" class="btn btn-erp-gold">Create Cost Centre</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

