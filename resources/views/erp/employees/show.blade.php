@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-secondary bg-opacity-10 text-dark border">{{ $employee->employee_code }}</span>
            <span class="badge {{ $employee->status === 'active' ? 'bg-success' : 'bg-secondary' }}">{{ ucfirst($employee->status) }}</span>
        </div>
        <h3 class="fw-bold text-dark mb-0">{{ $employee->name }}</h3>
        <div class="text-muted small mt-1">
            <strong>{{ $employee->designation }}</strong> &bull; Department: {{ $employee->department?->name ?? 'General Staff' }}
            &bull; Joining Date: {{ $employee->joining_date?->format('d M Y') }}
        </div>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        <a href="{{ route('erp.employees.edit', $employee) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit Profile
        </a>
        <a href="{{ route('erp.employees.index') }}" class="btn btn-light btn-sm border">
            Back to Directory
        </a>
    </div>
</div>

{{-- Salary Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-primary">
            <div class="text-muted small text-uppercase fw-semibold">Basic Salary</div>
            <h4 class="mb-0 fw-bold mt-1 text-primary">৳ {{ number_format($employee->basic_salary, 2) }}</h4>
            <div class="text-muted text-xs">Contracted base</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-info">
            <div class="text-muted small text-uppercase fw-semibold">Total Allowances</div>
            <h4 class="mb-0 fw-bold mt-1 text-info">৳ {{ number_format($employee->gross_salary - $employee->basic_salary, 2) }}</h4>
            <div class="text-muted text-xs">Rent, Medical & Other</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-success">
            <div class="text-muted small text-uppercase fw-semibold">Monthly Gross Salary</div>
            <h4 class="mb-0 fw-bold mt-1 text-success">৳ {{ number_format($employee->gross_salary, 2) }}</h4>
            <div class="text-muted text-xs">Pre-deduction earnings</div>
        </div>
    </div>
    <div class="col-6 col-md-3">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-dark">
            <div class="text-muted small text-uppercase fw-semibold">Take-Home Net Salary</div>
            <h4 class="mb-0 fw-bold mt-1 text-dark">৳ {{ number_format($employee->calculateNetSalary(), 2) }}</h4>
            <div class="text-muted text-xs">After Tax & PF</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-12 col-md-4">
        <div class="card shadow-sm border-0 p-3 mb-4">
            <h6 class="fw-bold text-primary mb-3">Salary & Bank Breakdown</h6>
            <table class="table table-sm table-bordered">
                <tr><th class="table-light">House Rent</th><td class="text-end">৳ {{ number_format($employee->house_rent, 2) }}</td></tr>
                <tr><th class="table-light">Medical</th><td class="text-end">৳ {{ number_format($employee->medical_allowance, 2) }}</td></tr>
                <tr><th class="table-light">Other Allowance</th><td class="text-end">৳ {{ number_format($employee->other_allowance, 2) }}</td></tr>
                <tr><th class="table-light">Tax Deduction</th><td class="text-end text-danger">-৳ {{ number_format($employee->tax_deduction, 2) }}</td></tr>
                <tr><th class="table-light">Provident Fund</th><td class="text-end text-danger">-৳ {{ number_format($employee->provident_fund, 2) }}</td></tr>
                <tr><th class="table-light">Bank Name</th><td>{{ $employee->bank_name ?? 'Cash Disbursed' }}</td></tr>
                <tr><th class="table-light">Account Number</th><td class="font-monospace">{{ $employee->bank_account_no ?? 'N/A' }}</td></tr>
            </table>
        </div>
    </div>

    <div class="col-12 col-md-8">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white py-3 border-bottom">
                <h6 class="fw-bold mb-0 text-dark">Recent Payroll Disbursements & Payslips</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th>Payroll Period</th>
                                <th class="text-end">Basic</th>
                                <th class="text-end">Allowances</th>
                                <th class="text-end">Deductions</th>
                                <th class="text-end">Net Disbursed</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($employee->payrollItems as $item)
                                <tr>
                                    <td class="fw-bold">
                                        <a href="{{ route('erp.payroll.show', $item->payrollRun) }}" class="text-decoration-none">
                                            {{ $item->payrollRun?->title }}
                                        </a>
                                    </td>
                                    <td class="text-end font-monospace">৳ {{ number_format($item->basic_salary, 2) }}</td>
                                    <td class="text-end font-monospace text-info">+৳ {{ number_format($item->allowances, 2) }}</td>
                                    <td class="text-end font-monospace text-danger">-৳ {{ number_format($item->deductions, 2) }}</td>
                                    <td class="text-end font-monospace fw-bold text-success">৳ {{ number_format($item->net_salary, 2) }}</td>
                                    <td><span class="badge bg-success">Paid</span></td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No payslips generated for this employee yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
