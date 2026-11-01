@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-person-badge text-primary me-2"></i> Staff & Employee Directory (HRM)
        </h4>
        <div class="text-muted small">Human Resource Management, Salary Structures, and Staff Profiles</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('erp.employees.create') }}" class="btn btn-primary">
            <i class="bi bi-person-plus-fill me-1"></i> Add New Employee
        </a>
        <a href="{{ route('erp.payroll.index') }}" class="btn btn-outline-primary">
            <i class="bi bi-wallet2 me-1"></i> Monthly Payroll
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('erp.employees.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-5">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by Name, Code, Designation or Phone..." value="{{ request('search') }}">
            </div>
            <div class="col-6 col-md-4">
                <select name="department_id" class="form-select form-select-sm">
                    <option value="">All Departments</option>
                    @foreach($departments as $dept)
                        <option value="{{ $dept->id }}" {{ request('department_id') == $dept->id ? 'selected' : '' }}>
                            {{ $dept->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-6 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">Filter</button>
                <a href="{{ route('erp.employees.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card shadow-sm border-0">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light small text-uppercase">
                    <tr>
                        <th>Employee Code & Name</th>
                        <th>Designation & Department</th>
                        <th>Joining Date</th>
                        <th class="text-end">Basic Salary</th>
                        <th class="text-end">Gross Salary</th>
                        <th>Status</th>
                        <th class="text-end">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($employees as $emp)
                        <tr>
                            <td>
                                <a href="{{ route('erp.employees.show', $emp) }}" class="fw-bold text-decoration-none text-dark">
                                    {{ $emp->name }}
                                </a>
                                <div class="text-muted small font-monospace">{{ $emp->employee_code }} &bull; {{ $emp->phone }}</div>
                            </td>
                            <td>
                                <div class="fw-semibold">{{ $emp->designation }}</div>
                                <div class="text-muted small">{{ $emp->department?->name ?? 'General Staff' }}</div>
                            </td>
                            <td>{{ $emp->joining_date?->format('d M Y') }}</td>
                            <td class="text-end font-monospace">৳ {{ number_format($emp->basic_salary, 2) }}</td>
                            <td class="text-end font-monospace fw-bold text-dark">৳ {{ number_format($emp->gross_salary, 2) }}</td>
                            <td>
                                <span class="badge {{ $emp->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                    {{ ucfirst($emp->status) }}
                                </span>
                            </td>
                            <td class="text-end">
                                <a href="{{ route('erp.employees.show', $emp) }}" class="btn btn-sm btn-light border" title="View Profile">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('erp.employees.edit', $emp) }}" class="btn btn-sm btn-light border" title="Edit">
                                    <i class="bi bi-pencil"></i>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">No staff members enrolled yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="mt-4">
    {{ $employees->links() }}
</div>
@endsection

