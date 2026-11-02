@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 page-title-row">
    <div>
        <h4 class="mb-0 fw-bold"><i class="bi bi-arrow-down-left-circle-fill text-success me-2"></i> Income & Revenues</h4>
        <p class="text-muted small mb-0">Record and manage property sales commission, booking fees, rents, and other revenues</p>
    </div>
    
    <div class="d-flex gap-2">
        @can('accounts.create')
            <a href="{{ route('admin.incomes.create') }}" class="btn btn-gold">
                <i class="bi bi-plus-circle me-1"></i> Add New Income
            </a>
        @endcan
        <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-graph-up me-1"></i> Overview
        </a>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card p-3 mb-4 border-0 shadow-sm">
    <form action="{{ route('admin.incomes.index') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-12 col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">Search Keyword</label>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Title, ref no, note..." value="{{ request('search') }}">
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">Category</label>
            <select name="category_id" class="form-select form-select-sm">
                <option value="">All Income Categories</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-12 col-sm-6 col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Payment Method</label>
            <select name="payment_method" class="form-select form-select-sm">
                <option value="">All Methods</option>
                <option value="bank_transfer" {{ request('payment_method') == 'bank_transfer' ? 'selected' : '' }}>Bank Transfer</option>
                <option value="cash" {{ request('payment_method') == 'cash' ? 'selected' : '' }}>Cash</option>
                <option value="cheque" {{ request('payment_method') == 'cheque' ? 'selected' : '' }}>Cheque</option>
                <option value="mobile_banking" {{ request('payment_method') == 'mobile_banking' ? 'selected' : '' }}>Mobile Banking (bKash/Nagad)</option>
                <option value="card" {{ request('payment_method') == 'card' ? 'selected' : '' }}>Credit/Debit Card</option>
            </select>
        </div>

        <div class="col-12 col-sm-6 col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">From Date</label>
            <input type="date" name="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
        </div>

        <div class="col-12 col-sm-6 col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">To Date</label>
            <input type="date" name="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
        </div>

        <div class="col-12 text-end mt-2">
            <button type="submit" class="btn btn-primary btn-sm px-3">
                <i class="bi bi-search me-1"></i> Apply Filters
            </button>
            <a href="{{ route('admin.incomes.index') }}" class="btn btn-outline-secondary btn-sm px-3 ms-1">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Total In Filtered Banner -->
<div class="alert alert-light border d-flex justify-content-between align-items-center py-2 px-3 mb-3">
    <div class="small text-muted">
        Showing <strong>{{ $incomes->total() }}</strong> income entries
    </div>
    <div class="fs-6">
        Total Revenue: <strong class="text-success fs-5">${{ number_format($totalFilteredAmount, 2) }}</strong>
    </div>
</div>

<!-- Incomes Table Card -->
<div class="card p-0 border-0 shadow-sm">
    @if($incomes->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Date</th>
                        <th>Income Title</th>
                        <th>Category</th>
                        <th>Property Target</th>
                        <th>Amount</th>
                        <th>Payment Method</th>
                        <th>Reference</th>
                        <th class="text-end" style="width: 140px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($incomes as $inc)
                        <tr>
                            <td class="text-muted small fw-semibold">
                                {{ $inc->transaction_date->format('M d, Y') }}
                            </td>
                            <td>
                                <div class="fw-bold text-dark">{{ $inc->title }}</div>
                                @if($inc->note)
                                    <div class="text-muted small text-truncate" style="max-width: 250px;">{{ $inc->note }}</div>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                    {{ $inc->category?->name ?? 'Uncategorized' }}
                                </span>
                            </td>
                            <td>
                                @if($inc->property)
                                    <span class="badge bg-light text-dark border">
                                        <i class="bi bi-building me-1 text-primary"></i> {{ Str::limit($inc->property->title, 20) }}
                                    </span>
                                @else
                                    <span class="text-muted small">&mdash;</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-success fs-6">+${{ number_format($inc->amount, 2) }}</span>
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border text-capitalize">
                                    {{ str_replace('_', ' ', $inc->payment_method) }}
                                </span>
                            </td>
                            <td>
                                @if($inc->reference_no)
                                    <code class="text-dark">{{ $inc->reference_no }}</code>
                                @else
                                    <span class="text-muted small">&mdash;</span>
                                @endif
                                @if($inc->attachment)
                                    <a href="{{ asset_url($inc->attachment) }}" target="_blank" class="ms-1 text-primary" title="View Attachment">
                                        <i class="bi bi-paperclip"></i>
                                    </a>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    @can('accounts.edit')
                                        <a href="{{ route('admin.incomes.edit', $inc->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit Income">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endcan
                                    @can('accounts.delete')
                                        <form action="{{ route('admin.incomes.destroy', $inc->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this income entry?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Income">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        
        @if($incomes->hasPages())
            <div class="p-3 border-top">
                {{ $incomes->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-cash-stack fs-1 mb-2 d-block opacity-40"></i>
            <p class="mb-0">No income records found matching criteria.</p>
            @can('accounts.create')
                <a href="{{ route('admin.incomes.create') }}" class="btn btn-sm btn-gold mt-2">
                    <i class="bi bi-plus-circle me-1"></i> Record Income
                </a>
            @endcan
        </div>
    @endif
</div>
@endsection

