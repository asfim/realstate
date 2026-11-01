@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-building-gear text-primary me-2"></i> Companies & Multi-Branch Architecture
        </h4>
        <div class="text-muted small">Manage enterprise entities, regional branches, and functional departments</div>
    </div>
</div>

<div class="row g-4">
    @foreach($companies as $company)
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="fw-bold mb-0 text-dark">{{ $company->name }}</h5>
                        <div class="text-muted small">Company Code: <strong>{{ $company->code }}</strong> &bull; Currency: <strong>{{ $company->currency }}</strong> &bull; Tax ID: {{ $company->tax_id ?? 'N/A' }}</div>
                    </div>
                    <span class="badge {{ $company->is_active ? 'bg-success' : 'bg-secondary' }}">
                        {{ $company->is_active ? 'Active Entity' : 'Inactive' }}
                    </span>
                </div>
                <div class="card-body p-4">
                    <div class="row g-4">
                        {{-- Branches --}}
                        <div class="col-12 col-md-7">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h6 class="fw-bold mb-0">Operational Branches ({{ $company->branches->count() }})</h6>
                                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#branchModal{{ $company->id }}">
                                    <i class="bi bi-plus-lg me-1"></i> Add Branch
                                </button>
                            </div>
                            <div class="table-responsive">
                                <table class="table table-sm table-hover align-middle mb-0">
                                    <thead class="table-light small">
                                        <tr>
                                            <th>Branch Code</th>
                                            <th>Branch Name</th>
                                            <th>Contact</th>
                                            <th>Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($company->branches as $branch)
                                            <tr>
                                                <td><span class="badge bg-light text-dark border">{{ $branch->code }}</span></td>
                                                <td class="fw-bold">{{ $branch->name }}</td>
                                                <td class="small text-muted">{{ $branch->phone ?? 'N/A' }} &bull; {{ $branch->address ?? 'N/A' }}</td>
                                                <td><span class="badge bg-success">Active</span></td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="text-center py-3 text-muted">No branches added.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        {{-- Departments --}}
                        <div class="col-12 col-md-5">
                            <h6 class="fw-bold mb-3">Departments</h6>
                            <ul class="list-group list-group-flush small">
                                @forelse($company->departments as $dept)
                                    <li class="list-group-item px-0 py-2 d-flex justify-content-between align-items-center">
                                        <span><i class="bi bi-diagram-2 text-primary me-2"></i> {{ $dept->name }}</span>
                                        <span class="badge bg-secondary">{{ $dept->code ?? 'DEP' }}</span>
                                    </li>
                                @empty
                                    <li class="list-group-item px-0 py-2 text-muted">No departments configured.</li>
                                @endforelse
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Add Branch Modal --}}
        <div class="modal fade" id="branchModal{{ $company->id }}" tabindex="-1">
            <div class="modal-dialog">
                <form action="{{ route('erp.companies.branches.store', $company) }}" method="POST" class="modal-content">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title fw-bold">Add Branch for {{ $company->name }}</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Branch Code <span class="text-danger">*</span></label>
                            <input type="text" name="code" class="form-control" placeholder="e.g. DHK-NORTH" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Branch Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="e.g. Uttara Sales Center" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Phone</label>
                            <input type="text" name="phone" class="form-control" placeholder="+880 1...">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Address</label>
                            <textarea name="address" rows="2" class="form-control"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-primary">Save Branch</button>
                    </div>
                </form>
            </div>
        </div>
    @endforeach
</div>
@endsection

