@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.hr-employees.index') }}" class="text-decoration-none">Employees</a></li>
                <li class="breadcrumb-item active" aria-current="page">{{ $hrEmployee->employee_code }}</li>
            </ol>
        </nav>
        <div class="d-flex align-items-center gap-2">
            <h4 class="fw-bold mb-0 text-dark">{{ $employee->name }}</h4>
            <span class="badge bg-light text-dark border">{{ $employee->employee_code }}</span>
            <span class="badge {{ $employee->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                {{ ucfirst($employee->status) }}
            </span>
        </div>
        <p class="text-muted small mb-0">{{ $employee->designation?->name ?? 'Staff' }} &bull; {{ $employee->department?->name ?? 'General' }}</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('erp.hr-payrolls.create', ['employee_id' => $employee->id]) }}" class="btn btn-erp-gold btn-sm px-3 text-dark">
            <i class="bi bi-cash me-1"></i> Generate Payroll
        </a>
        <a href="{{ route('erp.hr-employees.edit', $employee) }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('erp.hr-employees.index') }}" class="btn btn-light btn-sm px-3">
            <i class="bi bi-arrow-left me-1"></i> Back
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Personal & Employment Details -->
    <div class="col-lg-4">
        <div class="card card-custom p-4 mb-4">
            <div class="text-center pb-3 border-bottom mb-3">
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center text-erp mb-2" style="width: 72px; height: 72px; font-size: 2rem;">
                    <i class="bi bi-person-fill"></i>
                </div>
                <h5 class="fw-bold mb-0 text-dark">{{ $hrEmployee->name }}</h5>
                <div class="text-muted small">{{ $hrEmployee->designation?->name ?? 'Designation Unassigned' }}</div>
            </div>

            <div class="mb-3">
                <div class="text-muted small">Department</div>
                <div class="fw-semibold text-dark">{{ $hrEmployee->department?->name ?? '—' }}</div>
            </div>
            <div class="mb-3">
                <div class="text-muted small">Branch</div>
                <div class="fw-semibold text-dark">{{ $hrEmployee->branch?->name ?? 'Head Office' }}</div>
            </div>
            <div class="mb-3">
                <div class="text-muted small">Phone Number</div>
                <div class="fw-semibold text-dark">{{ $hrEmployee->phone }}</div>
            </div>
            <div class="mb-3">
                <div class="text-muted small">Email Address</div>
                <div class="fw-semibold text-dark">{{ $hrEmployee->email ?? '—' }}</div>
            </div>
            <div class="mb-3">
                <div class="text-muted small">National ID (NID)</div>
                <div class="fw-semibold text-dark">{{ $hrEmployee->nid_no ?? '—' }}</div>
            </div>
            <div class="mb-3">
                <div class="text-muted small">Joining Date</div>
                <div class="fw-semibold text-dark">{{ $hrEmployee->joining_date?->format('d M, Y') }} ({{ $hrEmployee->joining_date?->diffForHumans(null, true) }})</div>
            </div>
            <div>
                <div class="text-muted small">Employment Type</div>
                <div class="fw-semibold text-dark text-capitalize">{{ $hrEmployee->employment_type }}</div>
            </div>
        </div>

        <div class="card card-custom p-4">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-bank text-primary me-2"></i> Bank Details
            </h6>
            <div class="mb-2">
                <div class="text-muted small">Bank Name</div>
                <div class="fw-semibold text-dark">{{ $hrEmployee->bank_name ?? 'Cash / Cheque' }}</div>
            </div>
            <div>
                <div class="text-muted small">Account Number</div>
                <div class="fw-semibold text-dark">{{ $hrEmployee->bank_account_no ?? '—' }}</div>
            </div>
        </div>
    </div>

    <!-- Right Column: Compensation Structure & Payroll History -->
    <div class="col-lg-8">
        <!-- Monthly Compensation Structure -->
        <div class="card card-custom p-4 mb-4">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">
                <i class="bi bi-wallet2 text-erp-gold me-2"></i> Monthly Compensation Structure
            </h6>
            <div class="row g-3">
                <div class="col-md-3 col-6">
                    <div class="p-3 bg-light rounded text-center">
                        <div class="text-muted small">Basic Salary</div>
                        <div class="fs-6 fw-bold text-dark">৳ {{ number_format($hrEmployee->basic_salary, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-3 bg-light rounded text-center">
                        <div class="text-muted small">House Rent</div>
                        <div class="fs-6 fw-bold text-dark">৳ {{ number_format($hrEmployee->house_rent_allowance, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-3 bg-light rounded text-center">
                        <div class="text-muted small">Medical Allow.</div>
                        <div class="fs-6 fw-bold text-dark">৳ {{ number_format($hrEmployee->medical_allowance, 2) }}</div>
                    </div>
                </div>
                <div class="col-md-3 col-6">
                    <div class="p-3 bg-light rounded text-center">
                        <div class="text-muted small">Conveyance</div>
                        <div class="fs-6 fw-bold text-dark">৳ {{ number_format($hrEmployee->conveyance_allowance, 2) }}</div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="alert alert-success d-flex justify-content-between align-items-center mb-0 py-2">
                        <span class="fw-semibold"><i class="bi bi-shield-check me-1"></i> Total Monthly Gross Salary:</span>
                        <span class="fs-5 fw-bold">৳ {{ number_format($hrEmployee->gross_salary, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Payroll History -->
        <div class="card card-custom">
            <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-clock-history me-1"></i> Payroll History & Salary Slips
                </h6>
                <a href="{{ route('erp.hr-payrolls.create', ['employee_id' => $hrEmployee->id]) }}" class="btn btn-outline-erp btn-sm">
                    <i class="bi bi-plus-lg me-1"></i> Process Month
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase text-muted">
                        <tr>
                            <th>Payroll Code</th>
                            <th>Month / Year</th>
                            <th class="text-end">Gross</th>
                            <th class="text-end">Net Payable</th>
                            <th class="text-center">Status</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody class="small">
                        @forelse($hrEmployee->payrolls as $pay)
                            <tr>
                                <td class="fw-bold">{{ $pay->payroll_code }}</td>
                                <td>{{ date('F', mktime(0, 0, 0, $pay->salary_month, 10)) }} {{ $pay->salary_year }}</td>
                                <td class="text-end">৳ {{ number_format($pay->gross_salary, 2) }}</td>
                                <td class="text-end fw-bold text-dark">৳ {{ number_format($pay->net_payable, 2) }}</td>
                                <td class="text-center">
                                    <span class="badge {{ $pay->payment_status === 'paid' ? 'bg-success' : 'bg-warning text-dark' }}">
                                        {{ ucfirst($pay->payment_status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('erp.hr-payrolls.show', $pay) }}" class="btn btn-outline-secondary btn-sm py-0 px-2">
                                        <i class="bi bi-receipt me-1"></i> Payslip
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">
                                    <i class="bi bi-folder2-open display-6 text-muted d-block mb-2"></i>
                                    No salary vouchers generated yet for this employee.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
