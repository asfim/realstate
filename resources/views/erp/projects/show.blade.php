@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-secondary bg-opacity-10 text-dark border">{{ $project->code }}</span>
            <span class="badge bg-light text-dark border">{{ ucfirst($project->type) }}</span>
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
        </div>
        <h3 class="fw-bold text-dark mb-0">{{ $project->name }}</h3>
        <div class="text-muted small mt-1">
            <i class="bi bi-geo-alt me-1"></i> {{ $project->location ?? 'No location specified' }} &bull; 
            Company: <strong>{{ $project->company->name }}</strong>
            @if($project->branch) &bull; Branch: {{ $project->branch->name }} @endif
        </div>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        <a href="{{ route('erp.units.create', ['project_id' => $project->id]) }}" class="btn btn-primary btn-sm">
            <i class="bi bi-plus-circle me-1"></i> Add Unit
        </a>
        <a href="{{ route('erp.projects.edit', $project) }}" class="btn btn-outline-primary btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('erp.projects.index') }}" class="btn btn-light btn-sm border">
            Back to List
        </a>
    </div>
</div>

{{-- Stats Row --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card p-3 shadow-sm border-0 text-center">
            <div class="text-muted small text-uppercase fw-semibold">Total Units</div>
            <h4 class="mb-0 fw-bold mt-1">{{ $stats['total_units'] }}</h4>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card p-3 shadow-sm border-0 text-center">
            <div class="text-muted small text-uppercase fw-semibold">Available</div>
            <h4 class="mb-0 fw-bold text-success mt-1">{{ $stats['available_units'] }}</h4>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card p-3 shadow-sm border-0 text-center">
            <div class="text-muted small text-uppercase fw-semibold">Held</div>
            <h4 class="mb-0 fw-bold text-warning mt-1">{{ $stats['held_units'] }}</h4>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card p-3 shadow-sm border-0 text-center">
            <div class="text-muted small text-uppercase fw-semibold">Booked / Sold</div>
            <h4 class="mb-0 fw-bold text-danger mt-1">{{ $stats['booked_units'] + $stats['sold_units'] }}</h4>
        </div>
    </div>
    <div class="col-12 col-md-8 col-lg-4">
        <div class="card p-3 shadow-sm border-0">
            <div class="text-muted small text-uppercase fw-semibold">Total Inventory Value</div>
            <h4 class="mb-0 fw-bold text-primary mt-1">৳ {{ number_format($stats['total_inventory_value'], 0) }}</h4>
            <div class="text-muted small">Available: ৳ {{ number_format($stats['available_inventory_value'], 0) }}</div>
        </div>
    </div>
</div>

{{-- Navigation Tabs --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom p-0">
        <ul class="nav nav-tabs card-header-tabs mx-3 mt-2" id="projectTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button" role="tab">
                    <i class="bi bi-info-circle me-1"></i> Overview & Info
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="units-tab" data-bs-toggle="tab" data-bs-target="#units" type="button" role="tab">
                    <i class="bi bi-grid-3x3-gap-fill me-1"></i> Units ({{ $stats['total_units'] }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="buildings-tab" data-bs-toggle="tab" data-bs-target="#buildings" type="button" role="tab">
                    <i class="bi bi-building me-1"></i> Towers & Buildings ({{ $project->buildings->count() }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="blocks-tab" data-bs-toggle="tab" data-bs-target="#blocks" type="button" role="tab">
                    <i class="bi bi-map me-1"></i> Blocks & Roads ({{ $project->blocks->count() }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="mapping-tab" data-bs-toggle="tab" data-bs-target="#mapping" type="button" role="tab">
                    <i class="bi bi-link-45deg me-1"></i> Website Link
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-4">
        <div class="tab-content" id="projectTabContent">
            {{-- Tab 1: Overview --}}
            <div class="tab-pane fade show active" id="overview" role="tabpanel">
                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <h6 class="fw-bold text-primary mb-3">Project Details</h6>
                        <table class="table table-sm table-bordered">
                            <tr>
                                <th class="w-35 table-light">Project Code</th>
                                <td>{{ $project->code }}</td>
                            </tr>
                            <tr>
                                <th class="table-light">Project Type</th>
                                <td>{{ ucfirst($project->type) }}</td>
                            </tr>
                            <tr>
                                <th class="table-light">Ownership Model</th>
                                <td>{{ ucwords(str_replace('_', ' ', $project->ownership_type)) }}</td>
                            </tr>
                            <tr>
                                <th class="table-light">Land Area</th>
                                <td>{{ $project->land_area ? $project->land_area . ' ' . $project->land_area_unit : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th class="table-light">Address</th>
                                <td>{{ $project->address ?? 'N/A' }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-12 col-md-6">
                        <h6 class="fw-bold text-primary mb-3">Timeline & Financials</h6>
                        <table class="table table-sm table-bordered">
                            <tr>
                                <th class="w-35 table-light">Status</th>
                                <td><span class="badge {{ $badgeClass }}">{{ ucfirst($project->status) }}</span></td>
                            </tr>
                            <tr>
                                <th class="table-light">Launch Date</th>
                                <td>{{ $project->launch_date?->format('d M Y') ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th class="table-light">Target Completion</th>
                                <td>{{ $project->target_end_date?->format('d M Y') ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th class="table-light">Approved Budget</th>
                                <td>{{ $project->budget ? '৳ ' . number_format($project->budget, 2) : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th class="table-light">Avg Price / Sqft</th>
                                <td>৳ {{ number_format($stats['avg_price_per_sqft'], 2) }}</td>
                            </tr>
                        </table>
                    </div>

                    @if($project->description)
                        <div class="col-12">
                            <h6 class="fw-bold text-primary mb-2">Description & Scope</h6>
                            <div class="p-3 bg-light rounded text-muted">
                                {{ $project->description }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tab 2: Units Inventory --}}
            <div class="tab-pane fade" id="units" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Units in {{ $project->name }}</h6>
                    <a href="{{ route('erp.units.create', ['project_id' => $project->id]) }}" class="btn btn-sm btn-primary">
                        <i class="bi bi-plus-lg me-1"></i> Add Unit to Project
                    </a>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th>Unit Code</th>
                                <th>Tower / Block</th>
                                <th>Type</th>
                                <th>Specs / Area</th>
                                <th>Asking Price</th>
                                <th>Status</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($units as $u)
                                <tr>
                                    <td>
                                        <a href="{{ route('erp.units.show', $u) }}" class="fw-bold text-decoration-none">
                                            {{ $u->unit_code }}
                                        </a>
                                        @if($u->title)<div class="text-muted small">{{ $u->title }}</div>@endif
                                    </td>
                                    <td>
                                        @if($u->building)
                                            <div>{{ $u->building->name }}</div>
                                            <div class="text-muted small">{{ $u->floor?->floor_name }}</div>
                                        @elseif($u->block)
                                            <div>{{ $u->block->name }}</div>
                                            <div class="text-muted small">{{ $u->road?->road_name_or_no }}</div>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td><span class="badge bg-light text-dark border">{{ ucfirst($u->type) }}</span></td>
                                    <td>
                                        <strong>{{ number_format($u->area, 0) }}</strong> {{ $u->area_unit }}
                                        @if($u->bedrooms)
                                            <div class="text-muted small">{{ $u->bedrooms }} Bed &bull; {{ $u->bathrooms }} Bath</div>
                                        @endif
                                    </td>
                                    <td>
                                        <span class="fw-bold text-dark">৳ {{ number_format($u->asking_price, 0) }}</span>
                                    </td>
                                    <td>
                                        @php
                                            $stClass = match($u->status) {
                                                'available' => 'bg-success',
                                                'held' => 'bg-warning text-dark',
                                                'booked' => 'bg-primary',
                                                'sold' => 'bg-danger',
                                                default => 'bg-secondary'
                                            };
                                        @endphp
                                        <span class="badge {{ $stClass }}">{{ ucfirst($u->status) }}</span>
                                    </td>
                                    <td class="text-end">
                                        <a href="{{ route('erp.units.show', $u) }}" class="btn btn-sm btn-light border">
                                            <i class="bi bi-eye"></i>
                                        </a>
                                        <a href="{{ route('erp.units.edit', $u) }}" class="btn btn-sm btn-light border">
                                            <i class="bi bi-pencil"></i>
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="text-center py-4 text-muted">No units added to this project yet.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-3">
                    {{ $units->links() }}
                </div>
            </div>

            {{-- Tab 3: Buildings / Towers --}}
            <div class="tab-pane fade" id="buildings" role="tabpanel">
                <div class="row g-4">
                    <div class="col-12 col-lg-8">
                        <h6 class="fw-bold mb-3">Towers & Multi-Story Buildings</h6>
                        <div class="row g-3">
                            @forelse($project->buildings as $bldg)
                                <div class="col-12 col-md-6">
                                    <div class="card p-3 border">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold mb-0 text-dark">{{ $bldg->name }}</h6>
                                            <span class="badge bg-secondary">{{ $bldg->code }}</span>
                                        </div>
                                        <div class="text-muted small mb-2">
                                            Floors: <strong>{{ $bldg->total_floors }}</strong> &bull; Status: {{ ucfirst($bldg->status) }}
                                        </div>
                                        <div class="small">
                                            <span class="text-muted">Configured Floors:</span>
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                @foreach($bldg->floors as $floor)
                                                    <span class="badge bg-light text-dark border">{{ $floor->floor_name }} ({{ $floor->units->count() }} units)</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-muted py-3">No towers/buildings defined for this project.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Add Building Form --}}
                    <div class="col-12 col-lg-4">
                        <div class="card p-3 bg-light border">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="bi bi-plus-circle me-1"></i> Add Tower / Building
                            </h6>
                            <form action="{{ route('erp.projects.buildings.store', $project) }}" method="POST">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label small fw-semibold">Building Code <span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-control form-control-sm" placeholder="e.g. TOWER-B" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-semibold">Building Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Tower B (Park View)" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Number of Floors</label>
                                    <input type="number" name="total_floors" class="form-control form-control-sm" placeholder="e.g. 14" min="1" max="100">
                                    <div class="form-text text-xs">Floors will be automatically created.</div>
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary w-100">
                                    Save Building
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab 4: Blocks & Roads --}}
            <div class="tab-pane fade" id="blocks" role="tabpanel">
                <div class="row g-4">
                    <div class="col-12 col-lg-8">
                        <h6 class="fw-bold mb-3">Blocks & Sectors (Land Development)</h6>
                        <div class="row g-3">
                            @forelse($project->blocks as $blk)
                                <div class="col-12 col-md-6">
                                    <div class="card p-3 border">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <h6 class="fw-bold mb-0 text-dark">{{ $blk->name }}</h6>
                                            <span class="badge bg-secondary">{{ $blk->code }}</span>
                                        </div>
                                        <p class="text-muted small mb-2">{{ $blk->description ?? 'No description' }}</p>
                                        <div class="small">
                                            <span class="text-muted">Avenues & Roads:</span>
                                            <div class="d-flex flex-wrap gap-1 mt-1">
                                                @foreach($blk->roads as $rd)
                                                    <span class="badge bg-light text-dark border">{{ $rd->road_name_or_no }} ({{ $rd->width_ft }} ft)</span>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="col-12 text-muted py-3">No blocks defined for this project.</div>
                            @endforelse
                        </div>
                    </div>

                    {{-- Add Block Form --}}
                    <div class="col-12 col-lg-4">
                        <div class="card p-3 bg-light border">
                            <h6 class="fw-bold text-primary mb-3">
                                <i class="bi bi-plus-circle me-1"></i> Add Block / Sector
                            </h6>
                            <form action="{{ route('erp.projects.blocks.store', $project) }}" method="POST">
                                @csrf
                                <div class="mb-2">
                                    <label class="form-label small fw-semibold">Block Code <span class="text-danger">*</span></label>
                                    <input type="text" name="code" class="form-control form-control-sm" placeholder="e.g. BLOCK-C" required>
                                </div>
                                <div class="mb-2">
                                    <label class="form-label small fw-semibold">Block Name <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control form-control-sm" placeholder="e.g. Block C (Commercial)" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label small fw-semibold">Description</label>
                                    <input type="text" name="description" class="form-control form-control-sm" placeholder="e.g. North sector adjacent to highway">
                                </div>
                                <button type="submit" class="btn btn-sm btn-primary w-100">
                                    Save Block
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Tab 5: Website Mapping --}}
            <div class="tab-pane fade" id="mapping" role="tabpanel">
                <div class="p-4 bg-light rounded border">
                    <h6 class="fw-bold text-dark mb-2">Public Website Project Link</h6>
                    <p class="text-muted small mb-3">
                        ERP Projects maintain strict master inventory and accounting records. You can link this ERP project to an existing public website featured project for frontend synchronization.
                    </p>

                    @if($project->publicLink)
                        <div class="alert alert-success d-flex align-items-center mb-0">
                            <i class="bi bi-check-circle-fill fs-4 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-0">Linked to Website Project: {{ $project->publicLink->featuredProject?->title }}</h6>
                                <div class="small">Legacy Featured Project ID: #{{ $project->publicLink->featured_project_id }}</div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-warning mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i> This project is currently not linked to any public website project listing.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
