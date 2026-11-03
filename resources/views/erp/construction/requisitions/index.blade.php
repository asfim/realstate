@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Site Material Requisitions</h4>
        <p class="text-muted small mb-0">Project site engineer material demands, urgency levels, procurement approvals, and PO issue tracking.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.con-pos.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-receipt me-1"></i> Purchase Orders
        </a>
        <a href="{{ route('erp.con-requisitions.create') }}" class="btn btn-erp-gold btn-sm px-3">
            <i class="bi bi-plus-circle-fill me-1"></i> New Site Requisition
        </a>
    </div>
</div>

<!-- Search & Filters -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('erp.con-requisitions.index') }}" class="row g-2 align-items-center">
        <div class="col-md-5">
            <select name="project_id" class="form-select form-select-sm">
                <option value="">-- All Projects --</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-4">
            <select name="status" class="form-select form-select-sm">
                <option value="">-- Status --</option>
                <option value="submitted" {{ request('status') === 'submitted' ? 'selected' : '' }}>Submitted (Pending Approval)</option>
                <option value="approved" {{ request('status') === 'approved' ? 'selected' : '' }}>Approved (Ready for PO)</option>
                <option value="po_issued" {{ request('status') === 'po_issued' ? 'selected' : '' }}>PO Issued</option>
                <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm w-100">Filter</button>
            <a href="{{ route('erp.con-requisitions.index') }}" class="btn btn-light border btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- Requisitions Table -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Req Code</th>
                    <th>Project</th>
                    <th>Requested By</th>
                    <th>Required Date</th>
                    <th>Urgency</th>
                    <th>Items Count</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($requisitions as $req)
                    @php
                        $urgencyBadges = [
                            'normal' => 'bg-secondary',
                            'urgent' => 'bg-warning text-dark',
                            'emergency' => 'bg-danger',
                        ];
                        $statusBadges = [
                            'submitted' => 'bg-warning text-dark',
                            'approved' => 'bg-info text-dark',
                            'po_issued' => 'bg-success',
                            'rejected' => 'bg-danger',
                        ];
                    @endphp
                    <tr>
                        <td class="ps-3 fw-bold">
                            <a href="{{ route('erp.con-requisitions.show', $req) }}" class="text-decoration-none text-dark">
                                {{ $req->requisition_code }}
                            </a>
                            <div class="text-muted extra-small">{{ $req->created_at->format('M d, Y') }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $req->project->name ?? '—' }}</div>
                            <div class="text-muted extra-small">{{ $req->project->location ?? '' }}</div>
                        </td>
                        <td>
                            <div class="small fw-semibold">{{ $req->requestedByAdmin->name ?? 'Site Engr' }}</div>
                        </td>
                        <td>
                            <div class="small">{{ \Carbon\Carbon::parse($req->required_date)->format('M d, Y') }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $urgencyBadges[$req->urgency] ?? 'bg-secondary' }}">
                                {{ ucfirst($req->urgency) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ count($req->items) }} materials</span>
                        </td>
                        <td>
                            <span class="badge {{ $statusBadges[$req->status] ?? 'bg-secondary' }}">
                                {{ ucfirst(str_replace('_', ' ', $req->status)) }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('erp.con-requisitions.show', $req) }}" class="btn btn-outline-secondary btn-sm px-2 py-1">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-clipboard2-check fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No site material requisitions recorded.
                            <div class="mt-2">
                                <a href="{{ route('erp.con-requisitions.create') }}" class="btn btn-sm btn-erp-gold">Create First Requisition</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($requisitions->hasPages())
        <div class="p-3 border-top">
            {{ $requisitions->links() }}
        </div>
    @endif
</div>
@endsection

