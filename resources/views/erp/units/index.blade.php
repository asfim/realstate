@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-grid-3x3-gap-fill text-primary me-2"></i> Unit Inventory Master Desk
        </h4>
        <div class="text-muted small">Flat, Residential Plot & Commercial Space Inventory Management</div>
    </div>
    <div class="d-flex gap-2">
        <a href="{{ route('erp.units.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add New Unit
        </a>
    </div>
</div>

{{-- Inventory Status Summary Counters --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-dark">
            <div class="text-muted small text-uppercase fw-semibold">Total Inventory</div>
            <h3 class="mb-0 fw-bold mt-1 text-dark">{{ $summary['total'] }}</h3>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-success">
            <div class="text-muted small text-uppercase fw-semibold">Available</div>
            <h3 class="mb-0 fw-bold mt-1 text-success">{{ $summary['available'] }}</h3>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-warning">
            <div class="text-muted small text-uppercase fw-semibold">Held</div>
            <h3 class="mb-0 fw-bold mt-1 text-warning">{{ $summary['held'] }}</h3>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-primary">
            <div class="text-muted small text-uppercase fw-semibold">Booked</div>
            <h3 class="mb-0 fw-bold mt-1 text-primary">{{ $summary['booked'] }}</h3>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2">
        <div class="card p-3 shadow-sm border-0 border-start border-4 border-danger">
            <div class="text-muted small text-uppercase fw-semibold">Sold</div>
            <h3 class="mb-0 fw-bold mt-1 text-danger">{{ $summary['sold'] }}</h3>
        </div>
    </div>
    <div class="col-6 col-md-4 col-lg-2 d-flex align-items-center">
        <div class="btn-group w-100" role="group">
            <a href="{{ request()->fullUrlWithQuery(['view' => 'table']) }}" class="btn btn-sm {{ $viewMode === 'table' ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="bi bi-list-ul me-1"></i> Table
            </a>
            <a href="{{ request()->fullUrlWithQuery(['view' => 'grid']) }}" class="btn btn-sm {{ $viewMode === 'grid' ? 'btn-primary' : 'btn-outline-secondary' }}">
                <i class="bi bi-grid-fill me-1"></i> Grid
            </a>
        </div>
    </div>
</div>

{{-- Filters --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('erp.units.index') }}" class="row g-2 align-items-center">
            <input type="hidden" name="view" value="{{ $viewMode }}">

            <div class="col-12 col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by Unit Code or Title..." value="{{ request('search') }}">
            </div>

            <div class="col-6 col-md-3">
                <select name="project_id" class="form-select form-select-sm">
                    <option value="">All Projects</option>
                    @foreach($projects as $p)
                        <option value="{{ $p->id }}" {{ request('project_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->name }} ({{ $p->code }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Types</option>
                    <option value="flat" {{ request('type') === 'flat' ? 'selected' : '' }}>Flat</option>
                    <option value="plot" {{ request('type') === 'plot' ? 'selected' : '' }}>Plot</option>
                    <option value="shop" {{ request('type') === 'shop' ? 'selected' : '' }}>Shop</option>
                    <option value="office" {{ request('type') === 'office' ? 'selected' : '' }}>Office</option>
                </select>
            </div>

            <div class="col-6 col-md-2">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Statuses</option>
                    <option value="available" {{ request('status') === 'available' ? 'selected' : '' }}>Available</option>
                    <option value="held" {{ request('status') === 'held' ? 'selected' : '' }}>Held</option>
                    <option value="booked" {{ request('status') === 'booked' ? 'selected' : '' }}>Booked</option>
                    <option value="sold" {{ request('status') === 'sold' ? 'selected' : '' }}>Sold</option>
                    <option value="draft" {{ request('status') === 'draft' ? 'selected' : '' }}>Draft</option>
                </select>
            </div>

            <div class="col-6 col-md-2 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">
                    <i class="bi bi-filter"></i> Apply
                </button>
                <a href="{{ route('erp.units.index', ['view' => $viewMode]) }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

@if($viewMode === 'grid')
    {{-- Card Grid View --}}
    <div class="row g-3">
        @forelse($units as $u)
            @php
                $statusColor = match($u->status) {
                    'available' => 'success',
                    'held' => 'warning',
                    'booked' => 'primary',
                    'sold' => 'danger',
                    default => 'secondary'
                };
            @endphp
            <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                <div class="card h-100 shadow-sm border-0 border-top border-4 border-{{ $statusColor }}">
                    <div class="card-body p-3 d-flex flex-column">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <a href="{{ route('erp.units.show', $u) }}" class="fw-bold fs-5 text-decoration-none text-dark">
                                {{ $u->unit_code }}
                            </a>
                            <span class="badge bg-{{ $statusColor }} text-{{ $statusColor === 'warning' ? 'dark' : 'white' }}">
                                {{ ucfirst($u->status) }}
                            </span>
                        </div>

                        <div class="text-muted small mb-2 text-truncate">
                            <i class="bi bi-buildings me-1"></i> {{ $u->project?->name }}
                        </div>

                        @if($u->building)
                            <div class="small text-muted mb-2">
                                {{ $u->building->name }} &bull; {{ $u->floor?->floor_name }}
                            </div>
                        @elseif($u->block)
                            <div class="small text-muted mb-2">
                                {{ $u->block->name }} &bull; {{ $u->road?->road_name_or_no }}
                            </div>
                        @endif

                        <div class="bg-light p-2 rounded small mb-3">
                            <div class="d-flex justify-content-between">
                                <span class="text-muted">Type / Area:</span>
                                <strong>{{ ucfirst($u->type) }} &bull; {{ number_format($u->area, 0) }} {{ $u->area_unit }}</strong>
                            </div>
                            @if($u->bedrooms)
                                <div class="d-flex justify-content-between mt-1 text-muted">
                                    <span>Specs:</span>
                                    <span>{{ $u->bedrooms }}B / {{ $u->bathrooms }}B</span>
                                </div>
                            @endif
                            @if($u->facing)
                                <div class="d-flex justify-content-between mt-1 text-muted">
                                    <span>Facing:</span>
                                    <span>{{ ucwords(str_replace('_', ' ', $u->facing)) }}</span>
                                </div>
                            @endif
                        </div>

                        <div class="mt-auto pt-2 border-top d-flex justify-content-between align-items-center">
                            <div>
                                <div class="text-xs text-muted">Asking Price</div>
                                <div class="fw-bold text-dark">৳ {{ number_format($u->asking_price, 0) }}</div>
                            </div>
                            <a href="{{ route('erp.units.show', $u) }}" class="btn btn-sm btn-light border">
                                <i class="bi bi-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="card p-5 text-center shadow-sm border-0">
                    <p class="text-muted mb-0">No units match the filter criteria.</p>
                </div>
            </div>
        @endforelse
    </div>
@else
    {{-- Table View --}}
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Unit Code & Project</th>
                            <th>Hierarchy Location</th>
                            <th>Type</th>
                            <th>Size / Area</th>
                            <th>Asking Price (BDT)</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($units as $u)
                            <tr>
                                <td>
                                    <a href="{{ route('erp.units.show', $u) }}" class="fw-bold text-decoration-none text-dark">
                                        {{ $u->unit_code }}
                                    </a>
                                    <div class="text-muted small">{{ $u->project?->name }}</div>
                                </td>
                                <td>
                                    @if($u->building)
                                        <div>{{ $u->building->name }}</div>
                                        <div class="text-muted small">{{ $u->floor?->floor_name ?? 'Floor N/A' }}</div>
                                    @elseif($u->block)
                                        <div>{{ $u->block->name }}</div>
                                        <div class="text-muted small">{{ $u->road?->road_name_or_no ?? 'Road N/A' }}</div>
                                    @else
                                        <span class="text-muted">—</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border">{{ ucfirst($u->type) }}</span>
                                </td>
                                <td>
                                    <strong>{{ number_format($u->area, 0) }}</strong> {{ $u->area_unit }}
                                    @if($u->bedrooms)
                                        <div class="text-muted small">{{ $u->bedrooms }} Bed &bull; {{ $u->bathrooms }} Bath</div>
                                    @elseif($u->road_width_ft)
                                        <div class="text-muted small">{{ $u->road_width_ft }} ft road</div>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">৳ {{ number_format($u->asking_price, 0) }}</span>
                                    @if($u->min_price)
                                        <div class="text-muted text-xs">Min: ৳ {{ number_format($u->min_price, 0) }}</div>
                                    @endif
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
                                    <a href="{{ route('erp.units.show', $u) }}" class="btn btn-sm btn-light border" title="View Profile">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('erp.units.edit', $u) }}" class="btn btn-sm btn-light border" title="Edit Unit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    @if(!in_array($u->status, ['booked', 'sold', 'registered']))
                                        <form action="{{ route('erp.units.destroy', $u) }}" method="POST" class="d-inline" onsubmit="return confirm('Delete this unit?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-light border text-danger" title="Delete">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">No units found matching criteria.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endif

<div class="mt-4">
    {{ $units->links() }}
</div>
@endsection

