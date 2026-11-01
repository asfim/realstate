@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-1 fw-bold text-dark">
            <i class="bi bi-buildings-fill text-primary me-2"></i> Real Estate Projects
        </h4>
        <div class="text-muted small">Manage residential towers, land plots & commercial complexes</div>
    </div>
    <div>
        <a href="{{ route('erp.projects.create') }}" class="btn btn-primary">
            <i class="bi bi-plus-circle me-1"></i> Add New Project
        </a>
    </div>
</div>

{{-- Filters --}}
<div class="card shadow-sm border-0 mb-4">
    <div class="card-body p-3">
        <form method="GET" action="{{ route('erp.projects.index') }}" class="row g-2 align-items-center">
            <div class="col-12 col-md-3">
                <input type="text" name="search" class="form-control form-control-sm" placeholder="Search by name, code or location..." value="{{ request('search') }}">
            </div>
            <div class="col-6 col-md-3">
                <select name="type" class="form-select form-select-sm">
                    <option value="">All Project Types</option>
                    <option value="flat" {{ request('type') === 'flat' ? 'selected' : '' }}>Flat / Apartment</option>
                    <option value="plot" {{ request('type') === 'plot' ? 'selected' : '' }}>Plot / Land Development</option>
                    <option value="commercial" {{ request('type') === 'commercial' ? 'selected' : '' }}>Commercial Complex</option>
                    <option value="mixed" {{ request('type') === 'mixed' ? 'selected' : '' }}>Mixed Use</option>
                </select>
            </div>
            <div class="col-6 col-md-3">
                <select name="status" class="form-select form-select-sm">
                    <option value="">All Lifecycle Statuses</option>
                    <option value="planning" {{ request('status') === 'planning' ? 'selected' : '' }}>Planning</option>
                    <option value="upcoming" {{ request('status') === 'upcoming' ? 'selected' : '' }}>Upcoming</option>
                    <option value="ongoing" {{ request('status') === 'ongoing' ? 'selected' : '' }}>Ongoing Construction</option>
                    <option value="ready" {{ request('status') === 'ready' ? 'selected' : '' }}>Ready Handover</option>
                    <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                    <option value="on_hold" {{ request('status') === 'on_hold' ? 'selected' : '' }}>On Hold</option>
                </select>
            </div>
            <div class="col-12 col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-sm btn-primary flex-fill">
                    <i class="bi bi-filter me-1"></i> Filter
                </button>
                <a href="{{ route('erp.projects.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            </div>
        </form>
    </div>
</div>

{{-- Projects Grid --}}
<div class="row g-4">
    @forelse($projects as $project)
        <div class="col-12 col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 position-relative">
                <div class="card-body d-flex flex-column p-4">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <span class="badge bg-secondary bg-opacity-10 text-dark border me-1">{{ $project->code }}</span>
                            <span class="badge bg-light text-dark border">{{ ucfirst($project->type) }}</span>
                        </div>
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

                    <h5 class="card-title fw-bold text-dark mt-2 mb-1">
                        <a href="{{ route('erp.projects.show', $project) }}" class="text-decoration-none text-dark">
                            {{ $project->name }}
                        </a>
                    </h5>
                    <div class="text-muted small mb-3">
                        <i class="bi bi-geo-alt me-1"></i> {{ $project->location ?? 'Location not set' }}
                    </div>

                    <div class="bg-light rounded p-3 mb-3 small">
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Total Units:</span>
                            <span class="fw-bold">{{ $project->units_count }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Available:</span>
                            <span class="text-success fw-bold">{{ $project->available_units_count }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-1">
                            <span class="text-muted">Booked / Sold:</span>
                            <span class="text-danger fw-bold">{{ $project->sold_units_count }}</span>
                        </div>
                        @if($project->budget)
                            <div class="d-flex justify-content-between border-top pt-1 mt-1">
                                <span class="text-muted">Project Budget:</span>
                                <span class="fw-semibold">৳ {{ number_format($project->budget, 0) }}</span>
                            </div>
                        @endif
                    </div>

                    <div class="mt-auto d-flex justify-content-between align-items-center pt-2 border-top">
                        <a href="{{ route('erp.projects.show', $project) }}" class="btn btn-sm btn-primary">
                            <i class="bi bi-eye me-1"></i> View Details
                        </a>
                        <div class="dropdown">
                            <button class="btn btn-sm btn-light border dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                Actions
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li>
                                    <a class="dropdown-item" href="{{ route('erp.projects.edit', $project) }}">
                                        <i class="bi bi-pencil me-2"></i> Edit Project
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('erp.units.create', ['project_id' => $project->id]) }}">
                                        <i class="bi bi-plus-lg me-2"></i> Add Unit
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('erp.projects.destroy', $project) }}" method="POST" onsubmit="return confirm('Delete this project? (Cannot delete if units exist)');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-trash me-2"></i> Delete
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12">
            <div class="card p-5 text-center shadow-sm border-0">
                <i class="bi bi-building-x fs-1 text-muted mb-3"></i>
                <h5>No Projects Found</h5>
                <p class="text-muted small">No real estate projects match your search or filter criteria.</p>
                <div>
                    <a href="{{ route('erp.projects.create') }}" class="btn btn-primary btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Add First Project
                    </a>
                </div>
            </div>
        </div>
    @endforelse
</div>

<div class="mt-4">
    {{ $projects->links() }}
</div>
@endsection
