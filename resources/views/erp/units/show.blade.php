@extends('backend.layouts.app')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-secondary bg-opacity-10 text-dark border">{{ $unit->unit_code }}</span>
            <span class="badge bg-light text-dark border">{{ ucfirst($unit->type) }}</span>
            @php
                $statusColor = match($unit->status) {
                    'available' => 'success',
                    'held' => 'warning',
                    'booked' => 'primary',
                    'sold' => 'danger',
                    default => 'secondary'
                };
            @endphp
            <span class="badge bg-{{ $statusColor }} text-{{ $statusColor === 'warning' ? 'dark' : 'white' }}">
                {{ ucfirst($unit->status) }}
            </span>
        </div>
        <h3 class="fw-bold text-dark mb-0">{{ $unit->title ?? $unit->unit_code }}</h3>
        <div class="text-muted small mt-1">
            Project: <a href="{{ route('erp.projects.show', $unit->project) }}" class="fw-bold text-decoration-none">{{ $unit->project->name }}</a>
            &bull; Company: {{ $unit->company->name }}
            @if($unit->building) &bull; {{ $unit->building->name }} ({{ $unit->floor?->floor_name }}) @endif
            @if($unit->block) &bull; {{ $unit->block->name }} ({{ $unit->road?->road_name_or_no }}) @endif
        </div>
    </div>
    <div class="d-flex gap-2 mt-3 mt-md-0">
        {{-- Quick Status Transition Trigger --}}
        <button type="button" class="btn btn-warning btn-sm" data-bs-toggle="modal" data-bs-target="#statusModal">
            <i class="bi bi-arrow-left-right me-1"></i> Transition Status
        </button>

        {{-- Quick Price Revision Trigger --}}
        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#priceModal">
            <i class="bi bi-tag me-1"></i> Revise Price
        </button>

        <a href="{{ route('erp.units.edit', $unit) }}" class="btn btn-light btn-sm border">
            <i class="bi bi-pencil me-1"></i> Edit
        </a>
        <a href="{{ route('erp.units.index') }}" class="btn btn-light btn-sm border">
            Back to Inventory
        </a>
    </div>
</div>

{{-- Top Specs Cards --}}
<div class="row g-3 mb-4">
    <div class="col-6 col-md-3">
        <div class="card p-3 shadow-sm border-0">
            <div class="text-muted small text-uppercase fw-semibold">Authoritative Price</div>
            <h4 class="mb-0 fw-bold text-primary mt-1">৳ {{ number_format($unit->asking_price, 2) }}</h4>
            @if($unit->min_price)
                <div class="text-muted text-xs">Min Approved: ৳ {{ number_format($unit->min_price, 2) }}</div>
            @endif
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card p-3 shadow-sm border-0">
            <div class="text-muted small text-uppercase fw-semibold">Area / Dimensions</div>
            <h4 class="mb-0 fw-bold text-dark mt-1">{{ number_format($unit->area, 2) }} <span class="fs-6 fw-normal text-muted">{{ $unit->area_unit }}</span></h4>
            <div class="text-muted text-xs">
                ৳ {{ $unit->area > 0 ? number_format($unit->asking_price / $unit->area, 0) : 0 }} / {{ $unit->area_unit }}
            </div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card p-3 shadow-sm border-0">
            <div class="text-muted small text-uppercase fw-semibold">Orientation & Facing</div>
            <h5 class="mb-0 fw-bold text-dark mt-1">{{ $unit->facing ? ucwords(str_replace('_', ' ', $unit->facing)) : 'N/A' }}</h5>
            <div class="text-muted text-xs">Lock Version: v{{ $unit->lock_version }}</div>
        </div>
    </div>

    <div class="col-6 col-md-3">
        <div class="card p-3 shadow-sm border-0">
            <div class="text-muted small text-uppercase fw-semibold">Website Sync</div>
            @if($unit->publicLink)
                <h6 class="mb-0 fw-bold text-success mt-1">
                    <i class="bi bi-link-45deg"></i> Linked to Web
                </h6>
                <div class="text-muted text-xs">Property #{{ $unit->publicLink->property_id }}</div>
            @else
                <h6 class="mb-0 fw-bold text-muted mt-1">Unlinked</h6>
                <div class="text-muted text-xs">Inventory only</div>
            @endif
        </div>
    </div>
</div>

{{-- Detail & Audit Tabs --}}
<div class="card shadow-sm border-0">
    <div class="card-header bg-white border-bottom p-0">
        <ul class="nav nav-tabs card-header-tabs mx-3 mt-2" id="unitTab" role="tablist">
            <li class="nav-item">
                <button class="nav-link active" id="details-tab" data-bs-toggle="tab" data-bs-target="#details" type="button" role="tab">
                    <i class="bi bi-info-circle me-1"></i> Specifications
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="price-history-tab" data-bs-toggle="tab" data-bs-target="#priceHistory" type="button" role="tab">
                    <i class="bi bi-clock-history me-1"></i> Price Revision History ({{ $unit->priceHistories->count() }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="status-events-tab" data-bs-toggle="tab" data-bs-target="#statusEvents" type="button" role="tab">
                    <i class="bi bi-journal-check me-1"></i> Status Audit Log ({{ $unit->statusEvents->count() }})
                </button>
            </li>
            <li class="nav-item">
                <button class="nav-link" id="website-link-tab" data-bs-toggle="tab" data-bs-target="#websiteLink" type="button" role="tab">
                    <i class="bi bi-globe me-1"></i> Website Property Mapping
                </button>
            </li>
        </ul>
    </div>

    <div class="card-body p-4">
        <div class="tab-content" id="unitTabContent">
            {{-- Specifications Tab --}}
            <div class="tab-pane fade show active" id="details" role="tabpanel">
                <div class="row g-4">
                    <div class="col-12 col-md-6">
                        <h6 class="fw-bold text-primary mb-3">Unit Configuration</h6>
                        <table class="table table-sm table-bordered">
                            <tr>
                                <th class="w-35 table-light">Unit Code</th>
                                <td class="fw-bold">{{ $unit->unit_code }}</td>
                            </tr>
                            <tr>
                                <th class="table-light">Project</th>
                                <td>{{ $unit->project->name }}</td>
                            </tr>
                            <tr>
                                <th class="table-light">Type</th>
                                <td>{{ ucfirst($unit->type) }}</td>
                            </tr>
                            <tr>
                                <th class="table-light">Building / Floor</th>
                                <td>{{ $unit->building?->name ?? '—' }} ({{ $unit->floor?->floor_name ?? '—' }})</td>
                            </tr>
                            <tr>
                                <th class="table-light">Block / Road</th>
                                <td>{{ $unit->block?->name ?? '—' }} ({{ $unit->road?->road_name_or_no ?? '—' }})</td>
                            </tr>
                        </table>
                    </div>

                    <div class="col-12 col-md-6">
                        <h6 class="fw-bold text-primary mb-3">Physical Characteristics</h6>
                        <table class="table table-sm table-bordered">
                            <tr>
                                <th class="w-35 table-light">Bedrooms</th>
                                <td>{{ $unit->bedrooms ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th class="table-light">Bathrooms</th>
                                <td>{{ $unit->bathrooms ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th class="table-light">Balconies</th>
                                <td>{{ $unit->balconies ?? 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th class="table-light">Road Width</th>
                                <td>{{ $unit->road_width_ft ? $unit->road_width_ft . ' ft' : 'N/A' }}</td>
                            </tr>
                            <tr>
                                <th class="table-light">Parking</th>
                                <td>{{ $unit->parking_included ? 'Yes, Included' : 'No' }}</td>
                            </tr>
                        </table>
                    </div>

                    @if($unit->description)
                        <div class="col-12">
                            <h6 class="fw-bold text-primary mb-2">Description</h6>
                            <div class="p-3 bg-light rounded text-muted">
                                {{ $unit->description }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Price Revision History Tab --}}
            <div class="tab-pane fade" id="priceHistory" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Historical Price Changes</h6>
                    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#priceModal">
                        <i class="bi bi-plus-lg me-1"></i> Revise Price
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th>Date & Time</th>
                                <th>Old Price</th>
                                <th>New Price</th>
                                <th>Variance</th>
                                <th>Changed By</th>
                                <th>Reason</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($unit->priceHistories as $ph)
                                <tr>
                                    <td>{{ $ph->created_at?->format('d M Y, h:i A') }}</td>
                                    <td class="text-muted">৳ {{ number_format($ph->old_price, 2) }}</td>
                                    <td class="fw-bold text-success">৳ {{ number_format($ph->new_price, 2) }}</td>
                                    <td>
                                        @php $diff = $ph->new_price - $ph->old_price; @endphp
                                        @if($diff > 0)
                                            <span class="text-success">+৳ {{ number_format($diff, 2) }}</span>
                                        @elseif($diff < 0)
                                            <span class="text-danger">-৳ {{ number_format(abs($diff), 2) }}</span>
                                        @else
                                            <span class="text-muted">—</span>
                                        @endif
                                    </td>
                                    <td>{{ $ph->changedBy?->name ?? 'System' }}</td>
                                    <td class="small">{{ $ph->reason ?? 'No reason noted' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="text-center py-4 text-muted">No price revisions on record.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Status Audit Log Tab --}}
            <div class="tab-pane fade" id="statusEvents" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h6 class="fw-bold mb-0">Unit Lifecycle Event Log</h6>
                    <button type="button" class="btn btn-sm btn-warning" data-bs-toggle="modal" data-bs-target="#statusModal">
                        <i class="bi bi-arrow-left-right me-1"></i> Transition Status
                    </button>
                </div>

                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead class="table-light small text-uppercase">
                            <tr>
                                <th>Timestamp</th>
                                <th>Previous Status</th>
                                <th>New Status</th>
                                <th>Authorized By</th>
                                <th>Notes</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($unit->statusEvents as $se)
                                <tr>
                                    <td>{{ $se->created_at?->format('d M Y, h:i A') }}</td>
                                    <td><span class="badge bg-secondary">{{ ucfirst($se->from_status) }}</span></td>
                                    <td><span class="badge bg-primary">{{ ucfirst($se->to_status) }}</span></td>
                                    <td>{{ $se->actor?->name ?? 'System' }}</td>
                                    <td class="small">{{ $se->notes ?? '—' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-4 text-muted">No status events on record.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Website Mapping Tab --}}
            <div class="tab-pane fade" id="websiteLink" role="tabpanel">
                <div class="p-4 bg-light rounded border">
                    <h6 class="fw-bold text-dark mb-2">Connect to Website Public Property</h6>
                    <p class="text-muted small mb-3">
                        Connect this authoritative ERP Unit to an existing website property listing. The public listing will serve frontend visitors while this ERP record maintains legal ownership and financial pricing.
                    </p>

                    @if($unit->publicLink)
                        <div class="alert alert-success d-flex align-items-center mb-3">
                            <i class="bi bi-check-circle-fill fs-3 me-3"></i>
                            <div>
                                <h6 class="fw-bold mb-0">Currently Linked to: {{ $unit->publicLink->property?->title }}</h6>
                                <div class="small">Property ID: #{{ $unit->publicLink->property_id }} &bull; Sync Status: {{ ucfirst($unit->publicLink->sync_status) }}</div>
                            </div>
                        </div>
                    @endif

                    <form action="{{ route('erp.units.link-property', $unit) }}" method="POST" class="row g-2 align-items-center">
                        @csrf
                        <div class="col-12 col-md-8">
                            <select name="property_id" class="form-select" required>
                                <option value="">-- Choose Existing Website Property --</option>
                                @foreach($availableProperties as $prop)
                                    <option value="{{ $prop->id }}" {{ ($unit->publicLink?->property_id == $prop->id) ? 'selected' : '' }}>
                                        #{{ $prop->id }} - {{ $prop->title }} ({{ $prop->location }})
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12 col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-link-45deg me-1"></i> {{ $unit->publicLink ? 'Update Website Link' : 'Link Property' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Modal 1: Status Transition --}}
<div class="modal fade" id="statusModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('erp.units.transition-status', $unit) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Transition Lifecycle Status</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Current Status</label>
                    <div><span class="badge bg-secondary fs-6">{{ ucfirst($unit->status) }}</span></div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">New Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-select" required>
                        <option value="available" {{ $unit->status === 'available' ? 'disabled' : '' }}>Available</option>
                        <option value="held" {{ $unit->status === 'held' ? 'disabled' : '' }}>Held / Reserved</option>
                        <option value="booked" {{ $unit->status === 'booked' ? 'disabled' : '' }}>Booked</option>
                        <option value="sold" {{ $unit->status === 'sold' ? 'disabled' : '' }}>Sold</option>
                        <option value="cancelled" {{ $unit->status === 'cancelled' ? 'disabled' : '' }}>Cancelled</option>
                        <option value="draft" {{ $unit->status === 'draft' ? 'disabled' : '' }}>Draft</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Transition Reason / Notes</label>
                    <textarea name="notes" rows="2" class="form-control" placeholder="Audit remarks for this lifecycle change..."></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Confirm Transition</button>
            </div>
        </form>
    </div>
</div>

{{-- Modal 2: Price Revision --}}
<div class="modal fade" id="priceModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('erp.units.revise-price', $unit) }}" method="POST" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title fw-bold">Revise Unit Asking Price</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-semibold">Current Asking Price</label>
                    <div class="fw-bold text-dark fs-5">৳ {{ number_format($unit->asking_price, 2) }}</div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">New Asking Price (BDT) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">৳</span>
                        <input type="number" step="0.01" name="new_price" class="form-control" value="{{ $unit->asking_price }}" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">New Minimum Approved Price (BDT)</label>
                    <div class="input-group">
                        <span class="input-group-text">৳</span>
                        <input type="number" step="0.01" name="new_min_price" class="form-control" value="{{ $unit->min_price }}">
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-semibold">Revision Justification / Reason</label>
                    <input type="text" name="reason" class="form-control" placeholder="e.g. Market escalation, floor premium, festival offer">
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-primary">Commit Price Revision</button>
            </div>
        </form>
    </div>
</div>
@endsection
