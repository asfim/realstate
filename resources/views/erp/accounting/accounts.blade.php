@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-diagram-3 text-primary me-2"></i> Chart of Accounts (General Ledger)
        </h4>
        <div class="text-muted small">Standardized Asset, Liability, Equity, Revenue, and Expense Classifications</div>
    </div>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#accountModal">
            <i class="bi bi-plus-circle me-1"></i> New GL Account
        </button>
        <a href="{{ route('erp.accounting.journals') }}" class="btn btn-light btn-sm border">
            View Vouchers
        </a>
    </div>
</div>

<div class="row g-4">
    @foreach(['asset' => 'Assets', 'liability' => 'Liabilities', 'equity' => 'Equity', 'revenue' => 'Operating Revenue', 'expense' => 'Expenses & Construction Costs'] as $type => $label)
        <div class="col-12">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0 text-dark text-uppercase">
                        {{ $label }} ({{ $accountsByType[$type]->count() }})
                    </h6>
                    <span class="badge bg-secondary bg-opacity-10 text-dark border">{{ strtoupper($type) }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="table-light small text-uppercase">
                                <tr>
                                    <th class="w-15">Code</th>
                                    <th>Account Name</th>
                                    <th class="text-end">Opening Balance</th>
                                    <th class="text-end">Current Balance</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($accountsByType[$type] as $acc)
                                    <tr>
                                        <td class="font-monospace fw-bold text-primary">{{ $acc->code }}</td>
                                        <td>
                                            <span class="fw-semibold">{{ $acc->name }}</span>
                                            @if($acc->is_system)
                                                <span class="badge bg-light text-muted border text-xs ms-1">System</span>
                                            @endif
                                        </td>
                                        <td class="text-end text-muted font-monospace">৳ {{ number_format($acc->opening_balance, 2) }}</td>
                                        <td class="text-end font-monospace fw-bold text-dark">৳ {{ number_format($acc->calculateBalance(), 2) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center py-3 text-muted">No accounts under this category.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    @endforeach
</div>

{{-- Add Account Modal --}}
<div class="modal fade" id="accountModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('erp.accounting.accounts.store') }}" method="POST" class="modal-content">
            @csrf
            <input type="hidden" name="company_id" value="1">

            <div class="modal-header">
                <h5 class="modal-title fw-bold">Add Account to Chart of Accounts</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Account Code <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control" placeholder="e.g. 1030, 5050, 6500" required>
                    <div class="form-text text-xs">Standard range: 1xxx Assets, 2xxx Liabilities, 3xxx Equity, 4xxx Revenue, 5xxx Direct Cost, 6xxx Expense.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Account Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Dutch Bangla Bank Account" required>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Classification Type <span class="text-danger">*</span></label>
                    <select name="type" class="form-select" required>
                        <option value="asset">Asset (Debit Normal)</option>
                        <option value="liability">Liability (Credit Normal)</option>
                        <option value="equity">Equity (Credit Normal)</option>
                        <option value="revenue">Revenue (Credit Normal)</option>
                        <option value="expense">Expense / Construction Cost (Debit Normal)</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Opening Balance (BDT)</label>
                    <input type="number" step="0.01" name="opening_balance" class="form-control" value="0.00">
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Description</label>
                    <textarea name="description" rows="2" class="form-control"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Create Account</button>
            </div>
        </form>
    </div>
</div>
@endsection
