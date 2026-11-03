@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Double-Entry Journal Vouchers</h4>
        <p class="text-muted small mb-0">General journals, cash receipts/payments, bank transfers, cost center allocations, and ledger postings.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.acc-reports.trial-balance') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-file-earmark-bar-graph me-1"></i> Trial Balance
        </a>
        <a href="{{ route('erp.acc-journals.create') }}" class="btn btn-erp-gold btn-sm px-3">
            <i class="bi bi-plus-circle me-1"></i> Post Journal Voucher
        </a>
    </div>
</div>

<!-- Search & Filters -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('erp.acc-journals.index') }}" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search voucher code, narration..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="voucher_type" class="form-select form-select-sm">
                <option value="">-- All Voucher Types --</option>
                <option value="JV" {{ request('voucher_type') === 'JV' ? 'selected' : '' }}>Journal Voucher (JV)</option>
                <option value="CPV" {{ request('voucher_type') === 'CPV' ? 'selected' : '' }}>Cash Payment (CPV)</option>
                <option value="CRV" {{ request('voucher_type') === 'CRV' ? 'selected' : '' }}>Cash Receipt (CRV)</option>
                <option value="BPV" {{ request('voucher_type') === 'BPV' ? 'selected' : '' }}>Bank Payment (BPV)</option>
                <option value="BRV" {{ request('voucher_type') === 'BRV' ? 'selected' : '' }}>Bank Receipt (BRV)</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">-- Status --</option>
                <option value="posted" {{ request('status') === 'posted' ? 'selected' : '' }}>Posted to Ledger</option>
                <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                <option value="reversed" {{ request('status') === 'reversed' ? 'selected' : '' }}>Reversed</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm w-100">Filter</button>
            <a href="{{ route('erp.acc-journals.index') }}" class="btn btn-light border btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- Vouchers Table -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Voucher # / Date</th>
                    <th>Type</th>
                    <th>Narration / Purpose</th>
                    <th>Total Amount</th>
                    <th>Entries</th>
                    <th>Created By</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($journals as $j)
                    <tr>
                        <td class="ps-3 fw-bold">
                            <a href="{{ route('erp.acc-journals.show', $j) }}" class="text-decoration-none text-dark">
                                {{ $j->voucher_code }}
                            </a>
                            <div class="text-muted extra-small">{{ \Carbon\Carbon::parse($j->entry_date)->format('M d, Y') }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border font-monospace">{{ $j->voucher_type }}</span>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ Str::limit($j->narration, 70) }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">৳ {{ number_format($j->total_amount, 2) }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border">{{ count($j->entries) }} GL lines</span>
                        </td>
                        <td>
                            <div class="small">{{ $j->creator->name ?? 'Accountant' }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $j->status === 'posted' ? 'bg-success' : ($j->status === 'reversed' ? 'bg-danger' : 'bg-secondary') }}">
                                {{ ucfirst($j->status) }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('erp.acc-journals.show', $j) }}" class="btn btn-outline-secondary btn-sm px-2 py-1">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-journal-bookmark fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No journal vouchers posted yet.
                            <div class="mt-2">
                                <a href="{{ route('erp.acc-journals.create') }}" class="btn btn-sm btn-erp-gold">Post First Journal Entry</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($journals->hasPages())
        <div class="p-3 border-top">
            {{ $journals->links() }}
        </div>
    @endif
</div>
@endsection

