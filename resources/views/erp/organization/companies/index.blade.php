@extends('erp.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Legal Entities & Companies</h4>
        <p class="text-muted small mb-0">Manage corporate identities, registration numbers, tax identifiers, and currencies.</p>
    </div>
    <a href="{{ route('erp.companies.create') }}" class="btn btn-erp-gold btn-sm px-3">
        <i class="bi bi-plus-lg me-1"></i> Add Legal Entity
    </a>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Entity Code</th>
                    <th>Legal Name / Trade Name</th>
                    <th>BIN / TIN</th>
                    <th>Base Currency</th>
                    <th>Branches</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($companies as $company)
                    <tr>
                        <td>
                            <span class="badge bg-dark px-2 py-1 font-monospace">{{ $company->code }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $company->legal_name }}</div>
                            @if($company->trade_name)
                                <small class="text-muted">Trading as: {{ $company->trade_name }}</small>
                            @endif
                        </td>
                        <td>
                            <small class="d-block text-muted">BIN: {{ $company->bin ?? 'N/A' }}</small>
                            <small class="d-block text-muted">TIN: {{ $company->tin ?? 'N/A' }}</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ $company->base_currency }}</span>
                        </td>
                        <td>
                            <span class="badge bg-secondary">{{ $company->branches_count }} Branches</span>
                        </td>
                        <td>
                            <span class="badge {{ $company->status === 'active' ? 'bg-success' : 'bg-danger' }}">
                                {{ ucfirst($company->status) }}
                            </span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('erp.companies.edit', $company) }}" class="btn btn-sm btn-outline-primary me-1">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('erp.companies.destroy', $company) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this company entity?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger" {{ $company->branches_count > 0 ? 'disabled' : '' }}>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-4 text-muted">No legal entities found.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($companies->hasPages())
        <div class="p-3 border-top">
            {{ $companies->links() }}
        </div>
    @endif
</div>
@endsection
