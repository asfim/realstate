@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-1 small">
                <li class="breadcrumb-item"><a href="{{ route('erp.hr-labor.index') }}" class="text-decoration-none">Laborers</a></li>
                <li class="breadcrumb-item active" aria-current="page">Daily Muster Roll</li>
            </ol>
        </nav>
        <h4 class="fw-bold mb-1 text-dark">Site Daily Labor Attendance & Overtime Muster Roll</h4>
        <p class="text-muted small mb-0">Record daily site attendance, regular hours (standard 8h shift), overtime hours, and automatic wage accruals.</p>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('erp.hr-labor.index') }}" class="btn btn-outline-secondary btn-sm px-3">
            <i class="bi bi-people me-1"></i> Laborer Registry
        </a>
    </div>
</div>

<!-- Date & Project Selector Form -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('erp.hr-labor.muster') }}" class="row g-2 align-items-end">
        <div class="col-md-5">
            <label class="form-label small text-muted mb-1">Select Construction Project</label>
            <select name="project_id" class="form-select form-select-sm" onchange="this.form.submit()">
                @forelse($projects as $p)
                    <option value="{{ $p->id }}" {{ $selectedProjectId == $p->id ? 'selected' : '' }}>{{ $p->name }} ({{ $p->project_code }})</option>
                @empty
                    <option value="">No Active Projects</option>
                @endforelse
            </select>
        </div>
        <div class="col-md-4">
            <label class="form-label small text-muted mb-1">Attendance Work Date</label>
            <input type="date" name="work_date" class="form-control form-control-sm" value="{{ $workDate }}" onchange="this.form.submit()">
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-erp btn-sm flex-fill">
                <i class="bi bi-arrow-repeat me-1"></i> Load Muster Sheet
            </button>
        </div>
    </form>
</div>

<!-- Daily Summary Metrics -->
@php
    $presentCount = $musterRolls->where('is_present', true)->count();
    $totalHours = $musterRolls->sum('regular_hours');
    $totalOt = $musterRolls->sum('overtime_hours');
    $totalSpend = $musterRolls->sum('total_wage');
@endphp

<div class="row g-3 mb-4">
    <div class="col-md-3 col-6">
        <div class="card card-custom p-3 border-start border-4 border-primary">
            <div class="text-muted small">Total Present Workers</div>
            <div class="fs-4 fw-bold text-dark">{{ $presentCount }}</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-custom p-3 border-start border-4 border-info">
            <div class="text-muted small">Regular Hours Worked</div>
            <div class="fs-4 fw-bold text-dark">{{ number_format($totalHours, 1) }} hrs</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-custom p-3 border-start border-4 border-warning">
            <div class="text-muted small">Total Overtime Hours</div>
            <div class="fs-4 fw-bold text-dark">{{ number_format($totalOt, 1) }} hrs</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="card card-custom p-3 border-start border-4 border-success">
            <div class="text-muted small">Accrued Site Labor Wages</div>
            <div class="fs-4 fw-bold text-success">৳ {{ number_format($totalSpend, 2) }}</div>
        </div>
    </div>
</div>

<!-- Muster Roll Log Sheet -->
<div class="card card-custom">
    <div class="card-header bg-transparent d-flex justify-content-between align-items-center py-3">
        <h6 class="fw-bold mb-0 text-dark">
            <i class="bi bi-card-checklist me-1"></i> Daily Attendance Log Sheet &bull; {{ \Carbon\Carbon::parse($workDate)->format('d M, Y (l)') }}
        </h6>
        <span class="badge bg-light text-dark border">{{ $musterRolls->count() }} Recorded Today</span>
    </div>
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase text-muted">
                <tr>
                    <th>Worker ID</th>
                    <th>Worker Name</th>
                    <th>Trade</th>
                    <th class="text-end">Base Wage (8h)</th>
                    <th class="text-center">Present</th>
                    <th class="text-center" style="width: 120px;">Regular Hrs</th>
                    <th class="text-center" style="width: 120px;">OT Hrs</th>
                    <th class="text-end">Computed Wage</th>
                    <th class="text-end" style="width: 130px;">Action</th>
                </tr>
            </thead>
            <tbody class="small">
                @forelse($projectLaborers as $laborer)
                    @php
                        $existingRoll = $musterRolls->firstWhere('laborer_id', $laborer->id);
                    @endphp
                    <tr>
                        <form action="{{ route('erp.hr-labor.muster.store') }}" method="POST">
                            @csrf
                            <input type="hidden" name="project_id" value="{{ $selectedProjectId }}">
                            <input type="hidden" name="work_date" value="{{ $workDate }}">
                            <input type="hidden" name="laborer_id" value="{{ $laborer->id }}">

                            <td class="fw-bold">{{ $laborer->laborer_code }}</td>
                            <td>
                                <div class="fw-semibold text-dark">{{ $laborer->name }}</div>
                                <div class="text-muted small">{{ $laborer->phone ?? 'No Phone' }}</div>
                            </td>
                            <td>
                                <span class="badge bg-light text-dark border text-capitalize">
                                    {{ str_replace('_', ' ', $laborer->trade) }}
                                </span>
                            </td>
                            <td class="text-end">৳ {{ number_format($laborer->daily_wage_rate, 2) }}</td>
                            <td class="text-center">
                                <input type="hidden" name="is_present" value="0">
                                <input type="checkbox" name="is_present" value="1" class="form-check-input" {{ ($existingRoll ? $existingRoll->is_present : true) ? 'checked' : '' }}>
                            </td>
                            <td class="text-center">
                                <input type="number" step="0.5" name="regular_hours" class="form-control form-control-sm text-center" value="{{ $existingRoll ? $existingRoll->regular_hours : 8 }}" min="0" max="12">
                            </td>
                            <td class="text-center">
                                <input type="number" step="0.5" name="overtime_hours" class="form-control form-control-sm text-center" value="{{ $existingRoll ? $existingRoll->overtime_hours : 0 }}" min="0" max="12">
                            </td>
                            <td class="text-end fw-bold">
                                @if($existingRoll)
                                    <span class="text-success">৳ {{ number_format($existingRoll->total_wage, 2) }}</span>
                                @else
                                    <span class="text-muted">—</span>
                                @endif
                            </td>
                            <td class="text-end">
                                <button type="submit" class="btn {{ $existingRoll ? 'btn-outline-secondary' : 'btn-erp' }} btn-sm px-2">
                                    <i class="bi bi-save me-1"></i> {{ $existingRoll ? 'Update' : 'Save' }}
                                </button>
                            </td>
                        </form>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-person-x display-6 text-muted d-block mb-2"></i>
                            No laborers assigned to this project yet. Register laborers and assign them to this site first.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection

