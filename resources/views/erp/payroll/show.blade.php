@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-secondary bg-opacity-10 text-dark border">Payroll #{{ $payrollRun->id }}</span>
            <span class="badge bg-success">Approved & Disbursed</span>
        </div>
        <h3 class="fw-bold text-dark mb-0">{{ $payrollRun->title }}</h3>
        <div class="text-muted small mt-1">
            Year: {{ $payrollRun->year }} &bull; Month: {{ date('F', mktime(0, 0, 0, $payrollRun->month, 10)) }}
            @if($payrollRun->journal)
                &bull; Linked GL Voucher: <a href="{{ route('erp.accounting.journals') }}" class="fw-bold text-decoration-none">{{ $payrollRun->journal->journal_no }}</a>
            @endif
        </div>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        <button type="button" class="btn btn-outline-secondary btn-sm" onclick="window.print()">
            <i class="bi bi-printer me-1"></i> Print Payslip Sheet
        </button>
        <a href="{{ route('erp.payroll.index') }}" class="btn btn-light btn-sm border">
            Back to Payroll
        </a>
    </div>
</div>

{{-- Summary Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-info">
            <div class="text-muted small text-uppercase fw-semibold">Total Gross Salary</div>
            <h4 class="mb-0 fw-bold mt-1 text-info">৳ {{ number_format($payrollRun->total_gross, 2) }}</h4>
            <div class="text-muted text-xs">Basic + All allowances</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-danger">
            <div class="text-muted small text-uppercase fw-semibold">Total Deductions</div>
            <h4 class="mb-0 fw-bold mt-1 text-danger">-৳ {{ number_format($payrollRun->total_deductions, 2) }}</h4>
            <div class="text-muted text-xs">Income Tax & PF</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-success">
            <div class="text-muted small text-uppercase fw-semibold">Net Disbursed</div>
            <h4 class="mb-0 fw-bold mt-1 text-success">৳ {{ number_format($payrollRun->total_net, 2) }}</h4>
            <div class="text-muted text-xs">Bank transfer total</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-primary">
            <div class="text-muted small text-uppercase fw-semibold">Employees Processed</div>
            <h4 class="mb-0 fw-bold mt-1 text-primary">{{ $payrollRun->items->count() }} Staff</h4>
            <div class="text-muted text-xs">All active employees</div>
        </div>
    </div>
</div>

{{-- Staff Payslips Table --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
        <h6 class="fw-bold mb-0 text-dark">Staff Individual Payslip Breakdown</h6>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th>Employee</th>
                        <th>Designation & Dept</th>
                        <th class="text-end">Basic Salary</th>
                        <th class="text-end">Allowances</th>
                        <th class="text-end">Deductions</th>
                        <th class="text-end">Net Payable</th>
                        <th>Payment Mode</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($payrollRun->items as $item)
                        <tr>
                            <td>
                                <a href="{{ route('erp.employees.show', $item->employee) }}" class="fw-bold text-decoration-none text-dark">
                                    {{ $item->employee->name }}
                                </a>
                                <div class="text-muted small font-monospace">{{ $item->employee->employee_code }}</div>
                            </td>
                            <td>
                                <div>{{ $item->employee->designation }}</div>
                                <div class="text-muted small">{{ $item->employee->department?->name ?? 'General' }}</div>
                            </td>
                            <td class="text-end font-monospace">৳ {{ number_format($item->basic_salary, 2) }}</td>
                            <td class="text-end font-monospace text-info">+৳ {{ number_format($item->allowances, 2) }}</td>
                            <td class="text-end font-monospace text-danger">-৳ {{ number_format($item->deductions, 2) }}</td>
                            <td class="text-end font-monospace fw-bold text-success fs-6">৳ {{ number_format($item->net_salary, 2) }}</td>
                            <td>
                                <span class="badge bg-light text-dark border text-uppercase">
                                    {{ str_replace('_', ' ', $item->payment_method) }}
                                </span>
                            </td>
                            <td><span class="badge bg-success">Paid</span></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light border-top border-dark fw-bold fs-6">
                    <tr>
                        <td colspan="2" class="text-uppercase ps-3">Total Payroll Disbursement</td>
                        <td class="text-end font-monospace">৳ {{ number_format($payrollRun->items->sum('basic_salary'), 2) }}</td>
                        <td class="text-end font-monospace text-info">+৳ {{ number_format($payrollRun->items->sum('allowances'), 2) }}</td>
                        <td class="text-end font-monospace text-danger">-৳ {{ number_format($payrollRun->items->sum('deductions'), 2) }}</td>
                        <td class="text-end font-monospace text-success fs-5">৳ {{ number_format($payrollRun->total_net, 2) }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>
@endsection
