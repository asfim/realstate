@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Construction Site Daily Laborers</h4>
        <p class="text-muted small mb-0">Registry of daily-wage tradesmen (masons, rod binders, carpenters, helpers) and their wage rates.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('erp.hr-labor.muster') }}" class="btn btn-outline-erp btn-sm px-3">
            <i class="bi bi-calendar-check me-1"></i> Daily Attendance Muster
        </a>
        <button type="button" class="btn btn-erp btn-sm px-3" data-bs-toggle="modal" data-bs-target="#addLaborerModal">
            <i class="bi bi-plus-lg me-1"></i> Register Worker
        </button>
    </div>
</div>

<!-- Filters -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('erp.hr-labor.index') }}" class="row g-2 align-items-end">
        <div class="col-md-3">
            <label class="form-label small text-muted mb-1">Search</label>
            <input type="text" name="search" class="form-control form-control-sm" placeholder="Name, code, phone..." value="{{ request('search') }}">
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted mb-1">Trade Skill</label>
            <select name="trade" class="form-select form-select-sm">
                <option value="">All Trades</option>
                <option value="mason" {{ request('trade') == 'mason' ? 'selected' : '' }}>Mason (রাজমিস্ত্রি)</option>
                <option value="rod_binder" {{ request('trade') == 'rod_binder' ? 'selected' : '' }}>Rod Binder (রড বাইন্ডার)</option>
                <option value="carpenter" {{ request('trade') == 'carpenter' ? 'selected' : '' }}>Carpenter (কাঠমিস্ত্রি)</option>
                <option value="electrician" {{ request('trade') == 'electrician' ? 'selected' : '' }}>Electrician (ইলেকট্রিশিয়ান)</option>
                <option value="plumber" {{ request('trade') == 'plumber' ? 'selected' : '' }}>Plumber (প্লাম্বার)</option>
                <option value="helper" {{ request('trade') == 'helper' ? 'selected' : '' }}>Helper / General Labor (লেবার / হেল্পার)</option>
            </select>
        </div>
        <div class="col-md-3">
            <label class="form-label small text-muted mb-1">Assigned Project</label>
            <select name="assigned_project_id" class="form-select form-select-sm">
                <option value="">All Projects</option>
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" {{ request('assigned_project_id') == $p->id ? 'selected' : '' }}>{{ $p->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-outline-secondary btn-sm flex-fill">
                <i class="bi bi-funnel me-1"></i> Filter
            </button>
            <a href="{{ route('erp.hr-labor.index') }}" class="btn btn-light btn-sm">Reset</a>
        </div>
    </form>
</div>

<!-- Laborers Table -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase text-muted">
                <tr>
                    <th>Worker Code</th>
                    <th>Worker Name</th>
                    <th>Trade / Skill</th>
                    <th>Assigned Site</th>
                    <th>Phone</th>
                    <th class="text-end">Daily Wage (8h)</th>
                    <th class="text-end">OT Hourly</th>
                    <th class="text-center">Status</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($laborers as $laborer)
                    <tr>
                        <td class="fw-bold">{{ $laborer->laborer_code }}</td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $laborer->name }}</div>
                            <div class="text-muted small">NID: {{ $laborer->nid_no ?? 'N/A' }}</div>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border text-capitalize">
                                {{ str_replace('_', ' ', $laborer->trade) }}
                            </span>
                        </td>
                        <td>
                            @if($laborer->assignedProject)
                                <a href="{{ route('erp.dev-projects.show', $laborer->assignedProject) }}" class="text-decoration-none">
                                    {{ $laborer->assignedProject->name }}
                                </a>
                            @else
                                <span class="text-muted">Floating / Unassigned</span>
                            @endif
                        </td>
                        <td>{{ $laborer->phone ?? '—' }}</td>
                        <td class="text-end fw-bold text-dark">৳ {{ number_format($laborer->daily_wage_rate, 2) }}</td>
                        <td class="text-end text-muted">৳ {{ number_format($laborer->overtime_hourly_rate, 2) }}</td>
                        <td class="text-center">
                            <span class="badge {{ $laborer->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                {{ ucfirst($laborer->status) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-5 text-muted">
                            <i class="bi bi-people display-6 text-muted d-block mb-2"></i>
                            No construction site workers registered yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($laborers->hasPages())
        <div class="card-footer bg-transparent py-2">
            {{ $laborers->links() }}
        </div>
    @endif
</div>

<!-- Modal: Register Worker -->
<div class="modal fade" id="addLaborerModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('erp.hr-labor.laborers.store') }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Register Daily Worker</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Worker Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Md. Rafiqul Islam" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Phone Number</label>
                            <input type="text" name="phone" class="form-control form-control-sm" placeholder="017xxxxxxxx">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">National ID (NID)</label>
                            <input type="text" name="nid_no" class="form-control form-control-sm" placeholder="10/17 digits">
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Trade Skill <span class="text-danger">*</span></label>
                            <select name="trade" class="form-select form-select-sm" required>
                                <option value="mason">Mason (রাজমিস্ত্রি)</option>
                                <option value="rod_binder">Rod Binder (রড বাইন্ডার)</option>
                                <option value="carpenter">Carpenter (কাঠমিস্ত্রি)</option>
                                <option value="electrician">Electrician (ইলেকট্রিশিয়ান)</option>
                                <option value="plumber">Plumber (প্লাম্বার)</option>
                                <option value="helper" selected>Helper / General Labor (লেবার)</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Assigned Project</label>
                            <select name="assigned_project_id" class="form-select form-select-sm">
                                <option value="">-- Floating / Any --</option>
                                @foreach($projects as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Daily Wage Rate (BDT) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="daily_wage_rate" class="form-control form-control-sm" placeholder="e.g. 850.00" required>
                            <div class="form-text small text-muted">Standard 8-hour shift rate.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Overtime Hourly Rate (BDT)</label>
                            <input type="number" step="0.01" name="overtime_hourly_rate" class="form-control form-control-sm" placeholder="e.g. 150.00">
                            <div class="form-text small text-muted">Leave empty for 1.5x formula.</div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp btn-sm px-4">
                        <i class="bi bi-check-circle me-1"></i> Register Worker
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

