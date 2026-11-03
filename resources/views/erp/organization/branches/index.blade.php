@extends('erp.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Operating Branches</h4>
        <p class="text-muted small mb-0">Multi-branch network for {{ $currentCompany->legal_name ?? 'Current Company' }}.</p>
    </div>
    <a href="{{ route('erp.branches.create') }}" class="btn btn-erp-gold btn-sm px-3">
        <i class="bi bi-plus-lg me-1"></i> Add Branch
    </a>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Branch Code</th>
                    <th>Branch Name</th>
                    <th>Type</th>
                    <th>Branch Manager</th>
                    <th>Contact Info</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($branches as $branch)
                    <tr>
                        <td>
                            <span class="badge bg-dark px-2 py-1 font-monospace">{{ $branch->code }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $branch->name }}</div>
                            <small class="text-muted">{{ Str::limit($branch->address, 45) ?? 'No physical address' }}</small>
                        </td>
                        <td>
                            @if($branch->is_head_office)
                                <span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>Head Office</span>
                            @else
                                <span class="badge bg-light text-secondary border">Regional Branch</span>
                            @endif
                        </td>
                        <td>
                            <small class="fw-medium text-dark">{{ $branch->manager?->name ?? 'Unassigned' }}</small>
                        </td>
                        <td>
                            <small class="d-block text-muted"><i class="bi bi-telephone me-1"></i>{{ $branch->phone ?? 'N/A' }}</small>
                            <small class="d-block text-muted"><i class="bi bi-envelope me-1"></i>{{ $branch->email ?? 'N/A' }}</small>
                        </td>
                        <td>
                            <span class="badge {{ $branch->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($branch->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('erp.branches.edit', $branch) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('erp.branches.destroy', $branch) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this branch?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" {{ $branch->is_head_office ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No branches found for this entity.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($branches->hasPages())
        <div class="p-3 border-top">
            {{ $branches->links() }}
        </div>
    @endif
</div>
@endsection

