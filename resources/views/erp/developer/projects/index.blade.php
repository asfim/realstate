@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Developer Projects & Towers</h4>
        <p class="text-muted small mb-0">Own building projects, floor-by-floor unit inventories, parking allotments, and sales tracking.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.dev-bookings.create') }}" class="btn btn-outline-primary btn-sm px-3">
            <i class="bi bi-file-earmark-text-fill me-1"></i> New Booking Contract
        </a>
        <a href="{{ route('erp.dev-projects.create') }}" class="btn btn-erp-gold btn-sm px-3">
            <i class="bi bi-building-add me-1"></i> Register Project
        </a>
    </div>
</div>

<!-- Status Filter Ribbon -->
<div class="d-flex flex-wrap gap-2 mb-4 pb-2 border-bottom">
    <a href="{{ route('erp.dev-projects.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-light border' }} rounded-pill px-3">
        All Projects <span class="badge {{ !request('status') ? 'bg-secondary' : 'bg-light text-dark border' }} ms-1">{{ array_sum($statusCounts) }}</span>
    </a>
    @php
        $statuses = [
            'planning'           => ['label' => 'Planning & Design', 'color' => 'secondary'],
            'sanctioned'         => ['label' => 'Sanction Approved', 'color' => 'info'],
            'under_construction' => ['label' => 'Under Construction', 'color' => 'warning'],
            'finishing'          => ['label' => 'Finishing Phase', 'color' => 'primary'],
            'ready'              => ['label' => 'Ready / Handover Ready', 'color' => 'success'],
            'handed_over'        => ['label' => 'Handed Over to Association', 'color' => 'dark'],
        ];
    @endphp
    @foreach($statuses as $key => $meta)
        <a href="{{ route('erp.dev-projects.index', array_merge(request()->query(), ['status' => $key])) }}" 
           class="btn btn-sm {{ request('status') === $key ? 'btn-' . $meta['color'] : 'btn-light border' }} rounded-pill px-3">
            {{ $meta['label'] }}
            <span class="badge {{ request('status') === $key ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">
                {{ $statusCounts[$key] ?? 0 }}
            </span>
        </a>
    @endforeach
</div>

<!-- Search & Filters -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('erp.dev-projects.index') }}" class="row g-2 align-items-center">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <div class="col-md-6">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search by project name, project code, location..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="project_type" class="form-select form-select-sm">
                <option value="">-- All Typologies --</option>
                <option value="residential" {{ request('project_type') === 'residential' ? 'selected' : '' }}>Residential Luxury</option>
                <option value="commercial" {{ request('project_type') === 'commercial' ? 'selected' : '' }}>Commercial Office</option>
                <option value="mixed_use" {{ request('project_type') === 'mixed_use' ? 'selected' : '' }}>Mixed-Use</option>
                <option value="land_plotted" {{ request('project_type') === 'land_plotted' ? 'selected' : '' }}>Land / Plotted</option>
                <option value="warehouse" {{ request('project_type') === 'warehouse' ? 'selected' : '' }}>Warehouse</option>
            </select>
        </div>
        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm flex-grow-1">Filter</button>
            <a href="{{ route('erp.dev-projects.index') }}" class="btn btn-light btn-sm border">Reset</a>
        </div>
    </form>
</div>

<!-- Projects Grid / Table -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Project Code & Name</th>
                    <th>Typology & Location</th>
                    <th>Floors / Basements</th>
                    <th>Unit Inventory</th>
                    <th>Target Completion</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($projects as $p)
                    @php
                        $totalUnits = $p->units->count();
                        $availUnits = $p->units->where('status', 'available')->count();
                        $soldUnits = $p->units->whereIn('status', ['booked', 'sold', 'handed_over'])->count();
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-dark font-monospace" style="font-size: 0.72rem;">{{ $p->project_code }}</span>
                                <a href="{{ route('erp.dev-projects.show', $p) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $p->name }}
                                </a>
                            </div>
                            @if($p->landParcel)
                                <small class="text-muted d-block mt-1">
                                    <i class="bi bi-map me-1 text-warning"></i> Land Parcel: {{ $p->landParcel->parcel_code }} ({{ number_format($p->landParcel->total_area_katha, 1) }} Katha)
                                </small>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border text-capitalize mb-1">{{ str_replace('_', ' ', $p->project_type) }}</span>
                            <small class="d-block text-muted"><i class="bi bi-geo-alt me-1 text-danger"></i> {{ $p->location }}</small>
                        </td>
                        <td>
                            <div class="fw-semibold text-dark">{{ $p->total_floors }} Floors</div>
                            <small class="text-muted">{{ $p->total_basements }} Basements</small>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <span class="badge bg-success" title="Available for sale">{{ $availUnits }} Avail</span>
                                <span class="badge bg-dark" title="Booked or sold">{{ $soldUnits }} Sold</span>
                                <span class="small text-muted">/ {{ $totalUnits }} Total</span>
                            </div>
                            @if($totalUnits > 0)
                                <div class="progress" style="height: 5px; width: 80px;">
                                    <div class="progress-bar bg-success" style="width: {{ ($soldUnits / $totalUnits) * 100 }}%"></div>
                                </div>
                            @endif
                        </td>
                        <td>
                            <small class="fw-medium text-dark">{{ $p->expected_completion_date ? $p->expected_completion_date->format('M Y') : 'TBD' }}</small>
                        </td>
                        <td>
                            @php
                                $pBg = match($p->status) {
                                    'ready'              => 'success',
                                    'under_construction' => 'warning',
                                    'finishing'          => 'primary',
                                    'sanctioned'         => 'info',
                                    default              => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $pBg }}">{{ ucfirst(str_replace('_', ' ', $p->status)) }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('erp.dev-projects.show', $p) }}" class="btn btn-sm btn-outline-dark me-1" title="View Project Dossier & Units">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('erp.dev-projects.edit', $p) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit Project">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('erp.dev-projects.destroy', $p) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive this project?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-building fs-2 d-block mb-2 text-secondary"></i>
                            No developer projects registered in this workspace yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($projects->hasPages())
        <div class="p-3 border-top">
            {{ $projects->links() }}
        </div>
    @endif
</div>
@endsection
