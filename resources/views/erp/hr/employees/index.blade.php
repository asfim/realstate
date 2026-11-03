@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Permanent Staff & Human Resources</h4>
        <p class="text-muted small mb-0">Corporate employees, engineers, architects, sales relationship managers, and executive leadership.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.hr-payrolls.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-cash-stack me-1"></i> Monthly Payroll
        </a>
        <a href="{{ route('erp.hr-labor.index') }}" class="btn btn-outline-info btn-sm px-3">
            <i class="bi bi-people-fill me-1"></i> Site Daily Laborers
        </a>
        <a href="{{ route('erp.hr-employees.create') }}" class="btn btn-erp-gold btn-sm px-3">
            <i class="bi bi-person-plus-fill me-1"></i> Onboard Employee
        </a>
    </div>
</div>

<!-- Search & Filters -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('erp.hr-employees.index') }}" class="row g-2 align-items-center">
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search employee code, name, phone..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="department_id" class="form-select form-select-sm">
                <option value="">-- All Departments --</option>
                @foreach($departments as $d)
                    <option value="{{ $d->id }}" {{ request('department_id') == $d->id ? 'selected' : '' }}>{{ $d->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2">
            <select name="status" class="form-select form-select-sm">
                <option value="">-- Status --</option>
                <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active Staff</option>
                <option value="resigned" {{ request('status') === 'resigned' ? 'selected' : '' }}>Resigned</option>
                <option value="terminated" {{ request('status') === 'terminated' ? 'selected' : '' }}>Terminated</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm w-100">Filter</button>
            <a href="{{ route('erp.hr-employees.index') }}" class="btn btn-light border btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- Employees Table -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th class="ps-3">Code / Employee Name</th>
                    <th>Department & Role</th>
                    <th>Contact Phone</th>
                    <th>Gross Salary</th>
                    <th>Joining Date</th>
                    <th>Status</th>
                    <th class="text-end pe-3">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($employees as $emp)
                    <tr>
                        <td class="ps-3 fw-bold">
                            <a href="{{ route('erp.hr-employees.show', $emp) }}" class="text-decoration-none text-dark">
                                {{ $emp->name }}
                            </a>
                            <div class="text-muted extra-small font-monospace">{{ $emp->employee_code }} • {{ ucfirst($emp->employment_type) }}</div>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $emp->designation->name ?? 'Staff' }}</div>
                            <div class="text-muted extra-small">{{ $emp->department->name ?? 'General' }}</div>
                        </td>
                        <td>
                            <div><i class="bi bi-telephone me-1 text-muted"></i>{{ $emp->phone }}</div>
                            @if($emp->email)
                                <div class="text-muted extra-small"><i class="bi bi-envelope me-1"></i>{{ $emp->email }}</div>
                            @endif
                        </td>
                        <td>
                            <div class="fw-bold text-dark">৳ {{ number_format($emp->gross_salary, 2) }}</div>
                            <div class="text-muted extra-small">Basic: ৳{{ number_format($emp->basic_salary, 2) }}</div>
                        </td>
                        <td>
                            <div class="small">{{ \Carbon\Carbon::parse($emp->joining_date)->format('M d, Y') }}</div>
                        </td>
                        <td>
                            <span class="badge {{ $emp->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($emp->status) }}
                            </span>
                        </td>
                        <td class="text-end pe-3">
                            <a href="{{ route('erp.hr-employees.show', $emp) }}" class="btn btn-outline-secondary btn-sm px-2 py-1">
                                <i class="bi bi-eye"></i> View
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2 text-secondary opacity-50"></i>
                            No permanent staff registered yet.
                            <div class="mt-2">
                                <a href="{{ route('erp.hr-employees.create') }}" class="btn btn-sm btn-erp-gold">Onboard First Employee</a>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($employees->hasPages())
        <div class="p-3 border-top">
            {{ $employees->links() }}
        </div>
    @endif
</div>
@endsection

