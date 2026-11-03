@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Real Estate Chart of Accounts (COA)</h4>
        <p class="text-muted small mb-0">General ledger account structure: Assets (1000s), Liabilities (2000s), Equity (3000s), Revenue (4000s), and Expenses (5000s).</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.acc-journals.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-journal-text me-1"></i> Journal Vouchers
        </a>
        <button type="button" class="btn btn-erp-gold btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addAccountModal">
            <i class="bi bi-plus-circle me-1"></i> Add Account Head
        </button>
    </div>
</div>

@if ($errors->any())
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Five Core Account Classes Ribbon -->
<div class="row g-3 mb-4">
    @php
        $typeMeta = [
            'asset'     => ['label' => 'Assets (1000s)', 'color' => 'primary', 'desc' => 'Cash, Bank, WIP Inventory, Receivables'],
            'liability' => ['label' => 'Liabilities (2000s)', 'color' => 'danger', 'desc' => 'Payables, Customer Advances, Caution Deposits'],
            'equity'    => ['label' => 'Equity (3000s)', 'color' => 'info', 'desc' => 'Share Capital, Retained Earnings'],
            'revenue'   => ['label' => 'Revenue (4000s)', 'color' => 'success', 'desc' => 'Apartment Sales, Rent, Brokerage Commission'],
            'expense'   => ['label' => 'Expenses (5000s)', 'color' => 'warning', 'desc' => 'Construction Materials, Labor, Legal, Overhead'],
        ];
    @endphp
    @foreach($typeMeta as $type => $meta)
        <div class="col">
            <div class="card card-custom p-3 border-start border-4 border-{{ $meta['color'] }} h-100">
                <div class="text-muted extra-small text-uppercase fw-semibold">{{ $meta['label'] }}</div>
                <div class="fs-4 fw-bold text-dark mt-1">{{ count($accountsByType[$type] ?? []) }}</div>
                <div class="text-muted extra-small mt-1">{{ $meta['desc'] }}</div>
            </div>
        </div>
    @endforeach
</div>

<!-- Chart of Accounts Master Table -->
<div class="card card-custom">
    <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-diagram-3-fill text-erp-gold me-2"></i> General Ledger Account Catalog
        </h6>
        <span class="badge bg-light text-dark border">Total Accounts: {{ count($accounts) }}</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3" style="width: 15%;">Account Code</th>
                    <th style="width: 35%;">Account Name & Title</th>
                    <th style="width: 15%;">Class / Type</th>
                    <th style="width: 15%;">Normal Balance</th>
                    <th style="width: 10%;">Status</th>
                    <th class="text-end pe-3" style="width: 10%;">System Lock</th>
                </tr>
            </thead>
            <tbody>
                @forelse($accounts as $acc)
                    @php
                        $classBadges = [
                            'asset' => 'bg-primary',
                            'liability' => 'bg-danger',
                            'equity' => 'bg-info text-dark',
                            'revenue' => 'bg-success',
                            'expense' => 'bg-warning text-dark',
                        ];
                    @endphp
                    <tr>
                        <td class="ps-3">
                            <span class="badge bg-light text-dark border font-monospace fs-6">{{ $acc->account_code }}</span>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ $acc->account_name }}</div>
                            @if($acc->description)
                                <div class="text-muted extra-small">{{ $acc->description }}</div>
                            @endif
                        </td>
                        <td>
                            <span class="badge {{ $classBadges[$acc->account_type] ?? 'bg-secondary' }}">
                                {{ ucfirst($acc->account_type) }}
                            </span>
                        </td>
                        <td>
                            <span class="text-capitalize fw-semibold text-muted">{{ $acc->normal_balance }}</span>
                        </td>
                        <td>
                            <span class="badge bg-success">Active</span>
                        </td>
                        <td class="text-end pe-3">
                            @if($acc->is_system_locked)
                                <span class="badge bg-light text-muted border" title="Standard System Account"><i class="bi bi-lock-fill"></i> Locked</span>
                            @else
                                <span class="badge bg-light text-dark border">Custom</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">No accounts registered in Chart of Accounts.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Account Modal -->
<div class="modal fade" id="addAccountModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('erp.acc-coa.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Account Head to COA</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Account Code <span class="text-danger">*</span></label>
                        <input type="text" name="account_code" class="form-control form-control-sm" placeholder="e.g. 1025 or 5060" required>
                        <div class="form-text">Follow numbering: 1xxx (Asset), 2xxx (Liability), 3xxx (Equity), 4xxx (Revenue), 5xxx (Expense).</div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Account Name <span class="text-danger">*</span></label>
                        <input type="text" name="account_name" class="form-control form-control-sm" placeholder="e.g. City Bank Escrow Account" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label fw-semibold">Account Type <span class="text-danger">*</span></label>
                            <select name="account_type" class="form-select form-select-sm" required>
                                <option value="asset">Asset (1xxx)</option>
                                <option value="liability">Liability (2xxx)</option>
                                <option value="equity">Equity (3xxx)</option>
                                <option value="revenue">Revenue (4xxx)</option>
                                <option value="expense">Expense (5xxx)</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label fw-semibold">Normal Balance <span class="text-danger">*</span></label>
                            <select name="normal_balance" class="form-select form-select-sm" required>
                                <option value="debit">Debit</option>
                                <option value="credit">Credit</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Description / Purpose</label>
                        <textarea name="description" class="form-control form-control-sm" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light border" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp-gold px-4">Create Account</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
