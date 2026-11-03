@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Material Vendors & Contractors</h4>
        <p class="text-muted small mb-0">Approved construction material suppliers, sub-contractors, trade license records, and payment profiles.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.con-pos.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-receipt me-1"></i> Purchase Orders
        </a>
        <a href="{{ route('erp.con-vendors.create') }}" class="btn btn-erp-gold btn-sm px-3">
            <i class="bi bi-person-plus-fill me-1"></i> Register Vendor
        </a>
    </div>
</div>

<!-- Search & Filters -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('erp.con-vendors.index') }}" class="row g-2 align-items-center">
        <div class="col-md-7">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search by vendor name, code, contact person, phone..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="status" class="form-select form-select-sm">
                <option value="">-- All Statuses --</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Approved</option>
                <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                <option value="blacklisted" {{ request('status') === 'blacklisted' ? 'selected' : '' }}>Blacklisted</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm w-100">Filter</button>
            <a href="{{ route('erp.con-vendors.index') }}" class="btn btn-light border btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- Vendors Table -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Code / Vendor Name</th>
                    <th>Contact Person</th>
                    <th>Phone & Email</th>
                    <th>Trade License & TIN</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($vendors as $v)
                    <tr>
                        <td class="ps-3">
                            <div class="fw-bold text-dark">{{ $v->name }}</div>
                            <span class="badge bg-light text-dark border extra-small">{{ $v->vendor_code }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $v->contact_person ?? '—' }}</div>
                        </td>
                        <td>
                            <div><i class="bi bi-telephone me-1 text-muted"></i>{{ $v->phone }}</div>
                            @if($v->email)
                                <div class="text-muted extra-small"><i class="bi bi-envelope me-1"></i>{{ $v->email }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="small">TL: {{ $v->trade_license_no ?? '—' }}</div>
                            <div class="text-muted extra-small">TIN: {{ $v->tin_no ?? '—' }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $v->status === 'active' ? 'bg-success' : ($v->status === 'blacklisted' ? 'bg-danger' : 'bg-secondary') }}">
                                {{ ucfirst($v->status) }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('erp.con-vendors.edit', $v) }}" class="btn btn-outline-secondary btn-sm px-2 py-1">
                                <i class="bi bi-pencil"></i> Edit
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">
                            <i class="bi bi-truck fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No suppliers or contractors registered yet.
                            <div class="mt-2">
                                <a href="{{ route('erp.con-vendors.create') }}" class="btn btn-sm btn-erp-gold">Register First Supplier</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($vendors->hasPages())
        <div class="p-3 border-top">
            {{ $vendors->links() }}
        </div>
    @endif
</div>
@endsection

