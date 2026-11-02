@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 gap-3 page-title-row">
    <div>
        <h4 class="mb-0 fw-bold"><i class="bi bi-cash-stack text-warning me-2"></i> Staff Salary & Payroll Management</h4>
        <p class="text-muted small mb-0">Manage staff monthly payroll, allowances, deductions, and salary disbursement records</p>
    </div>
    
    <div class="d-flex gap-2">
        @can('payroll.create')
            <a href="{{ route('admin.payrolls.create') }}" class="btn btn-gold">
                <i class="bi bi-cash-coin me-1"></i> Disburse / New Payroll
            </a>
        @endcan
        <a href="{{ route('admin.accounts.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-graph-up me-1"></i> Overview
        </a>
    </div>
</div>

<!-- Search & Filter Card -->
<div class="card p-3 mb-4 border-0 shadow-sm">
    <form action="{{ route('admin.payrolls.index') }}" method="GET" class="row g-2 align-items-end">
        <div class="col-12 col-md-4">
            <label class="form-label small fw-semibold text-muted mb-1">Search Staff Name or Role</label>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Staff name, designation..." value="{{ request('search') }}">
        </div>

        <div class="col-12 col-sm-6 col-md-3">
            <label class="form-label small fw-semibold text-muted mb-1">Salary Month</label>
            <input type="text" name="salary_month" class="form-control form-control-sm" placeholder="e.g. November 2026" value="{{ request('salary_month') }}">
        </div>

        <div class="col-12 col-sm-6 col-md-2">
            <label class="form-label small fw-semibold text-muted mb-1">Payment Status</label>
            <select name="status" class="form-select form-select-sm">
                <option value="">All Statuses</option>
                <option value="paid" {{ request('status') == 'paid' ? 'selected' : '' }}>Paid</option>
                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="partial" {{ request('status') == 'partial' ? 'selected' : '' }}>Partial</option>
            </select>
        </div>

        <div class="col-12 col-md-3 text-end">
            <button type="submit" class="btn btn-primary btn-sm px-3">
                <i class="bi bi-search me-1"></i> Filter
            </button>
            <a href="{{ route('admin.payrolls.index') }}" class="btn btn-outline-secondary btn-sm px-3 ms-1">
                Reset
            </a>
        </div>
    </form>
</div>

<!-- Total In Filtered Banner -->
<div class="alert alert-light border d-flex justify-content-between align-items-center py-2 px-3 mb-3">
    <div class="small text-muted">
        Showing <strong>{{ $payrolls->total() }}</strong> staff payroll entries
    </div>
    <div class="fs-6">
        Total Salary Paid: <strong class="text-warning fs-5">${{ number_format($totalDisbursed, 2) }}</strong>
    </div>
</div>

<!-- Payroll Table Card -->
<div class="card p-0 border-0 shadow-sm">
    @if($payrolls->count() > 0)
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>Staff Member</th>
                        <th>Month</th>
                        <th>Basic Salary</th>
                        <th>Allowance</th>
                        <th>Deduction</th>
                        <th>Net Salary</th>
                        <th>Paid Date</th>
                        <th>Method</th>
                        <th>Status</th>
                        <th class="text-end" style="width: 130px;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payrolls as $pr)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="rounded-circle bg-warning-subtle text-warning fw-bold d-flex align-items-center justify-content-center" style="width: 36px; height: 36px; font-size: 0.82rem;">
                                        {{ strtoupper(substr($pr->staff_name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <div class="fw-bold text-dark">{{ $pr->staff_name }}</div>
                                        <div class="text-muted small" style="font-size: 0.72rem;">{{ $pr->staff_role ?? 'Team Staff' }}</div>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border">{{ $pr->salary_month }}</span>
                            </td>
                            <td>${{ number_format($pr->basic_salary, 2) }}</td>
                            <td>
                                @if($pr->allowance > 0)
                                    <span class="text-success">+${{ number_format($pr->allowance, 2) }}</span>
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
                            <td>
                                @if($pr->deduction > 0)
                                    <span class="text-danger">-${{ number_format($pr->deduction, 2) }}</span>
                                @else
                                    <span class="text-muted">&mdash;</span>
                                @endif
                            </td>
                            <td>
                                <span class="fw-bold text-dark fs-6">${{ number_format($pr->net_salary, 2) }}</span>
                            </td>
                            <td class="text-muted small">
                                {{ $pr->payment_date->format('M d, Y') }}
                            </td>
                            <td>
                                <span class="badge bg-light text-secondary border text-capitalize">
                                    {{ str_replace('_', ' ', $pr->payment_method) }}
                                </span>
                            </td>
                            <td>
                                @if($pr->status === 'paid')
                                    <span class="badge bg-success-subtle text-success border border-success-subtle rounded-pill">
                                        <i class="bi bi-check2-circle me-1"></i> Paid
                                    </span>
                                @elseif($pr->status === 'partial')
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle rounded-pill">
                                        Partial
                                    </span>
                                @else
                                    <span class="badge bg-secondary-subtle text-secondary border rounded-pill">
                                        Pending
                                    </span>
                                @endif
                            </td>
                            <td class="text-end">
                                <div class="d-flex justify-content-end gap-1">
                                    @can('payroll.edit')
                                        <a href="{{ route('admin.payrolls.edit', $pr->id) }}" class="btn btn-sm btn-outline-secondary" title="Edit Payroll">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    @endcan
                                    @can('payroll.delete')
                                        <form action="{{ route('admin.payrolls.destroy', $pr->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this payroll record?');" class="d-inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Payroll">
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

        @if($payrolls->hasPages())
            <div class="p-3 border-top">
                {{ $payrolls->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-cash-stack fs-1 mb-2 d-block opacity-40"></i>
            <p class="mb-0">No staff payroll records found.</p>
            @can('payroll.create')
                <a href="{{ route('admin.payrolls.create') }}" class="btn btn-sm btn-gold mt-2">
                    <i class="bi bi-cash-coin me-1"></i> Disburse Salary
                </a>
            @endcan
        </div>
    @endif
</div>
@endsection

