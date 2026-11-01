@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-diagram-3-fill text-warning me-2"></i> Real Estate ERP Dashboard
        </h4>
        <div class="text-muted small">
            <span>{{ $company?->name ?? 'Enterprise ERP' }}</span> &bull; 
            <span>Currency: <strong>{{ $company?->currency ?? 'BDT' }}</strong></span> &bull; 
            <i class="bi bi-clock me-1"></i> {{ now()->timezone('Asia/Dhaka')->format('d M Y, h:i A') }} (Asia/Dhaka)
        </div>
    </div>
    <div class="d-flex gap-2 mt-2 mt-md-0">
        <a href="{{ route('erp.projects.create') }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> New Project
        </a>
        <a href="{{ route('erp.units.create') }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-building-add me-1"></i> New Unit
        </a>
    </div>
</div>

{{-- Top KPI Cards --}}
<div class="row g-3 mb-4">
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card h-100 shadow-sm border-0 border-start border-primary border-4 p-3">
            <div class="d-flex align-items-center">
                <div class="widget-icon bg-primary bg-opacity-10 text-primary p-3 rounded-3 me-3 fs-4">
                    <i class="bi bi-buildings-fill"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Active Projects</div>
                    <h3 class="mb-0 fw-bold text-dark">{{ $projectsCount }}</h3>
                    <div class="text-muted small mt-1">Total Developments</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card h-100 shadow-sm border-0 border-start border-success border-4 p-3">
            <div class="d-flex align-items-center">
                <div class="widget-icon bg-success bg-opacity-10 text-success p-3 rounded-3 me-3 fs-4">
                    <i class="bi bi-door-open-fill"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Available Units</div>
                    <h3 class="mb-0 fw-bold text-success">{{ $availableUnits }} <span class="fs-6 text-muted fw-normal">/ {{ $totalUnits }}</span></h3>
                    <div class="text-muted small mt-1">{{ $totalUnits > 0 ? round(($availableUnits / $totalUnits) * 100, 1) : 0 }}% of inventory</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card h-100 shadow-sm border-0 border-start border-info border-4 p-3">
            <div class="d-flex align-items-center">
                <div class="widget-icon bg-info bg-opacity-10 text-info p-3 rounded-3 me-3 fs-4">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Total Inventory Value</div>
                    <h4 class="mb-0 fw-bold text-dark">৳ {{ number_format($totalInventoryValue, 0) }}</h4>
                    <div class="text-muted small mt-1">All flats, plots & shops</div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-12 col-sm-6 col-lg-3">
        <div class="card h-100 shadow-sm border-0 border-start border-warning border-4 p-3">
            <div class="d-flex align-items-center">
                <div class="widget-icon bg-warning bg-opacity-10 text-warning p-3 rounded-3 me-3 fs-4">
                    <i class="bi bi-bag-check-fill"></i>
                </div>
                <div>
                    <div class="text-muted small text-uppercase fw-semibold">Booked & Sold Value</div>
                    <h4 class="mb-0 fw-bold text-dark">৳ {{ number_format($soldInventoryValue, 0) }}</h4>
                    <div class="text-muted small mt-1">{{ $bookedUnits + $soldUnits }} units locked/sold</div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Inventory Status Breakdown Progress --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h6 class="fw-bold mb-0 text-dark">
                <i class="bi bi-pie-chart-fill text-primary me-2"></i> Unit Inventory Lifecycle Distribution
            </h6>
            <a href="{{ route('erp.units.index') }}" class="btn btn-sm btn-link text-decoration-none">
                View Full Inventory <i class="bi bi-arrow-right"></i>
            </a>
        </div>

        <div class="progress mb-3" style="height: 14px;">
            @php
                $availPct = $totalUnits > 0 ? ($availableUnits / $totalUnits) * 100 : 0;
                $heldPct = $totalUnits > 0 ? ($heldUnits / $totalUnits) * 100 : 0;
                $bookedPct = $totalUnits > 0 ? ($bookedUnits / $totalUnits) * 100 : 0;
                $soldPct = $totalUnits > 0 ? ($soldUnits / $totalUnits) * 100 : 0;
            @endphp
            <div class="progress-bar bg-success" style="width: {{ $availPct }}%" title="Available: {{ $availableUnits }}"></div>
            <div class="progress-bar bg-warning" style="width: {{ $heldPct }}%" title="Held: {{ $heldUnits }}"></div>
            <div class="progress-bar bg-primary" style="width: {{ $bookedPct }}%" title="Booked: {{ $bookedUnits }}"></div>
            <div class="progress-bar bg-danger" style="width: {{ $soldPct }}%" title="Sold: {{ $soldUnits }}"></div>
        </div>

        <div class="d-flex flex-wrap gap-4 text-sm small">
            <div><span class="badge bg-success me-1">&bull;</span> <strong>{{ $availableUnits }}</strong> Available ({{ round($availPct, 1) }}%)</div>
            <div><span class="badge bg-warning text-dark me-1">&bull;</span> <strong>{{ $heldUnits }}</strong> Held ({{ round($heldPct, 1) }}%)</div>
            <div><span class="badge bg-primary me-1">&bull;</span> <strong>{{ $bookedUnits }}</strong> Booked ({{ round($bookedPct, 1) }}%)</div>
            <div><span class="badge bg-danger me-1">&bull;</span> <strong>{{ $soldUnits }}</strong> Sold ({{ round($soldPct, 1) }}%)</div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Recent Projects List --}}
    <div class="col-12 col-lg-7">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0 text-dark">
                    <i class="bi bi-kanban-fill text-primary me-2"></i> Active Projects Overview
                </h6>
                <a href="{{ route('erp.projects.index') }}" class="btn btn-sm btn-outline-secondary">All Projects</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th>Code & Name</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Units</th>
                                <th class="text-end">Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentProjects as $project)
                                <tr>
                                    <td>
                                        <a href="{{ route('erp.projects.show', $project) }}" class="fw-bold text-decoration-none text-dark">
                                            {{ $project->name }}
                                        </a>
                                        <div class="text-muted small">{{ $project->code }} &bull; {{ $project->location ?? 'N/A' }}</div>
                                    </td>
                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            {{ ucfirst($project->type) }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $badgeClass = match($project->status) {
                                                'planning' => 'bg-secondary',
                                                'upcoming' => 'bg-info text-dark',
                                                'ongoing' => 'bg-primary',
                                                'ready' => 'bg-success',
                                                'completed' => 'bg-dark',
                                                default => 'bg-light text-dark'
                                            };
                                        @endphp
                                        <span class="badge {{ $badgeClass }}">{{ ucfirst($project->status) }}</span>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary bg-opacity-10 text-dark fw-bold">
                                            {{ $project->units_count }}
                                        </span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('erp.projects.show', $project) }}" class="btn btn-sm btn-light border">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No projects found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Recent Audit Logs & Price Changes --}}
    <div class="col-12 col-lg-5">
        <div class="card shadow-sm border-0 h-100">
            <div class="card-header bg-white py-3 border-bottom">
                <ul class="nav nav-pills card-header-pills" id="activityTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active btn-sm" id="status-tab" data-bs-toggle="tab" data-bs-target="#status-panel" type="button" role="tab">
                            <i class="bi bi-arrow-left-right me-1"></i> Status Events
                        </button>
                    </li>
                    <li class="nav-item">
                        <button class="nav-link btn-sm" id="price-tab" data-bs-toggle="tab" data-bs-target="#price-panel" type="button" role="tab">
                            <i class="bi bi-tag-fill me-1"></i> Price Revisions
                        </button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-3">
                <div class="tab-content" id="activityTabContent">
                    {{-- Status Events Tab --}}
                    <div class="tab-pane fade show active" id="status-panel" role="tabpanel">
                        <ul class="list-group list-group-flush small">
                            @forelse($recentStatusEvents as $event)
                                <li class="list-group-item px-0 py-2 border-0 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">{{ $event->unit?->unit_code }} ({{ $event->unit?->project?->name }})</span>
                                        <span class="text-muted text-xs">{{ $event->created_at?->diffForHumans() }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="badge bg-secondary">{{ $event->from_status }}</span>
                                        <i class="bi bi-arrow-right text-muted"></i>
                                        <span class="badge bg-primary">{{ $event->to_status }}</span>
                                        @if($event->actor)
                                            <span class="text-muted text-xs ms-auto">by {{ $event->actor->name }}</span>
                                        @endif
                                    </div>
                                    @if($event->notes)
                                        <div class="text-muted text-xs mt-1 fst-italic">"{{ $event->notes }}"</div>
                                    @endif
                                </li>
                            @empty
                                <div class="text-center py-4 text-muted">No recent status events recorded.</div>
                            @endforelse
                        </ul>
                    </div>

                    {{-- Price Revisions Tab --}}
                    <div class="tab-pane fade" id="price-panel" role="tabpanel">
                        <ul class="list-group list-group-flush small">
                            @forelse($recentPriceHistories as $ph)
                                <li class="list-group-item px-0 py-2 border-0 border-bottom">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="fw-bold">{{ $ph->unit?->unit_code }}</span>
                                        <span class="text-muted text-xs">{{ $ph->created_at?->diffForHumans() }}</span>
                                    </div>
                                    <div class="d-flex align-items-center gap-2 mt-1">
                                        <span class="text-muted text-decoration-line-through">৳ {{ number_format($ph->old_price, 0) }}</span>
                                        <i class="bi bi-arrow-right text-success"></i>
                                        <span class="text-success fw-bold">৳ {{ number_format($ph->new_price, 0) }}</span>
                                    </div>
                                    @if($ph->reason)
                                        <div class="text-muted text-xs mt-1">Reason: {{ $ph->reason }}</div>
                                    @endif
                                </li>
                            @empty
                                <div class="text-center py-4 text-muted">No price revisions logged.</div>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
