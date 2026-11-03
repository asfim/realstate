@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-dark font-monospace px-2 py-1">{{ $devProject->project_code }}</span>
            <h4 class="fw-bold mb-0 text-dark">{{ $devProject->name }}</h4>
            @php
                $pBg = match($devProject->status) {
                    'ready'              => 'success',
                    'under_construction' => 'warning',
                    'finishing'          => 'primary',
                    'sanctioned'         => 'info',
                    default              => 'secondary',
                };
            @endphp
            <span class="badge bg-{{ $pBg }}">{{ ucfirst(str_replace('_', ' ', $devProject->status)) }}</span>
        </div>
        <p class="text-muted small mb-0">
            <i class="bi bi-geo-alt-fill text-danger me-1"></i> {{ $devProject->location }} 
            &bull; {{ $devProject->total_floors }} Floors ({{ $devProject->total_basements }} Basements)
            &bull; Units: <strong>{{ $devProject->units->count() }} of {{ $devProject->total_units }} Registered</strong>
        </p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('erp.dev-projects.edit', $devProject) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit Project
        </a>
        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addUnitModal">
            <i class="bi bi-plus-lg me-1"></i> Add Unit / Flat
        </button>
        <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#addParkingModal">
            <i class="bi bi-p-square me-1"></i> Add Parking Slot
        </button>
        <a href="{{ route('erp.dev-bookings.create', ['project_id' => $devProject->id]) }}" class="btn btn-erp-gold btn-sm">
            <i class="bi bi-file-earmark-plus me-1"></i> Book Unit
        </a>
    </div>
</div>

<!-- Tabs Navigation -->
<ul class="nav nav-tabs border-bottom mb-4" id="projectTabs" role="tablist">
    <li class="nav-item">
        <button class="nav-link active fw-semibold" id="units-tab" data-bs-toggle="tab" data-bs-target="#units" type="button">
            <i class="bi bi-grid-3x3-gap-fill me-1"></i> Unit Inventory ({{ $devProject->units->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-semibold" id="overview-tab" data-bs-toggle="tab" data-bs-target="#overview" type="button">
            <i class="bi bi-info-circle me-1"></i> Project Specs & Sanctions
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-semibold" id="parking-tab" data-bs-toggle="tab" data-bs-target="#parking" type="button">
            <i class="bi bi-p-square me-1"></i> Parking Bays ({{ $devProject->parkingSlots->count() }})
        </button>
    </li>
    <li class="nav-item">
        <button class="nav-link fw-semibold" id="bookings-tab" data-bs-toggle="tab" data-bs-target="#bookings" type="button">
            <i class="bi bi-file-earmark-check me-1"></i> Sales Contracts ({{ $devProject->bookings->count() }})
        </button>
    </li>
</ul>

<div class="tab-content" id="projectTabsContent">
    <!-- Tab 1: Unit Inventory Matrix -->
    <div class="tab-pane fade show active" id="units">
        <!-- Stock Summary Ribbon -->
        <div class="row g-3 mb-4">
            <div class="col-sm-6 col-md-3">
                <div class="card card-custom p-3 bg-light">
                    <small class="text-muted fw-bold">TOTAL REGISTERED</small>
                    <h3 class="fw-bold mb-0 text-dark">{{ $devProject->units->count() }}</h3>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card card-custom p-3 bg-light">
                    <small class="text-muted fw-bold text-success">AVAILABLE FOR SALE</small>
                    <h3 class="fw-bold mb-0 text-success">{{ $devProject->availableUnitsCount() }}</h3>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card card-custom p-3 bg-light">
                    <small class="text-muted fw-bold text-danger">BOOKED / SOLD</small>
                    <h3 class="fw-bold mb-0 text-danger">{{ $devProject->soldUnitsCount() }}</h3>
                </div>
            </div>
            <div class="col-sm-6 col-md-3">
                <div class="card card-custom p-3 bg-light">
                    <small class="text-muted fw-bold text-warning">LANDOWNER SHARE</small>
                    <h3 class="fw-bold mb-0 text-warning">{{ $devProject->units->where('allocation_share', 'landowner_share')->count() }}</h3>
                </div>
            </div>
        </div>

        <div class="card card-custom">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark mb-0">Floor-by-Floor Unit Directory</h6>
                <button type="button" class="btn btn-sm btn-erp-gold" data-bs-toggle="modal" data-bs-target="#addUnitModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Unit
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Unit No. & Floor</th>
                            <th>Typology & Specs</th>
                            <th>Allocation Share</th>
                            <th>Gross Size (sft)</th>
                            <th>Price / Sq.Ft</th>
                            <th>Grand Total Price</th>
                            <th>Status</th>
                            <th class="text-end">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devProject->units as $unit)
                            <tr>
                                <td>
                                    <div class="fw-bold text-dark">{{ $unit->unit_number }}</div>
                                    <small class="text-muted">{{ $unit->floor_label }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border text-capitalize mb-1">{{ str_replace('_', ' ', $unit->unit_type) }}</span>
                                    <div class="small text-muted">
                                        @if($unit->bedrooms) {{ $unit->bedrooms }} Bed &bull; @endif
                                        @if($unit->bathrooms) {{ $unit->bathrooms }} Bath &bull; @endif
                                        Facing: {{ $unit->facing ?? 'Standard' }}
                                    </div>
                                </td>
                                <td>
                                    @php
                                        $shareColor = match($unit->allocation_share) {
                                            'developer_share' => 'success',
                                            'landowner_share' => 'warning text-dark',
                                            default           => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $shareColor }}">
                                        {{ ucfirst(str_replace('_', ' ', $unit->allocation_share)) }}
                                    </span>
                                </td>
                                <td>
                                    <div class="fw-semibold text-dark">{{ number_format($unit->gross_area_sqft) }} sft</div>
                                    @if($unit->net_carpet_area_sqft)
                                        <small class="text-muted">Carpet: {{ number_format($unit->net_carpet_area_sqft) }} sft</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="small fw-semibold text-dark">BDT {{ number_format($unit->base_price_sqft) }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">BDT {{ number_format($unit->grand_total_price) }}</div>
                                    @if($unit->utility_charges > 0)
                                        <small class="text-muted">Incl. BDT {{ number_format($unit->utility_charges) }} util</small>
                                    @endif
                                </td>
                                <td>
                                    @php
                                        $uBg = match($unit->status) {
                                            'available' => 'success',
                                            'booked'    => 'danger',
                                            'sold'      => 'dark',
                                            'reserved'  => 'warning text-dark',
                                            default     => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $uBg }}">{{ ucfirst($unit->status) }}</span>
                                </td>
                                <td class="text-end">
                                    @if($unit->status === 'available')
                                        <a href="{{ route('erp.dev-bookings.create', ['project_id' => $devProject->id, 'unit_id' => $unit->id]) }}" class="btn btn-sm btn-outline-success me-1" title="Book this Unit">
                                            Book
                                        </a>
                                    @elseif($unit->booking)
                                        <a href="{{ route('erp.dev-bookings.show', $unit->booking) }}" class="btn btn-sm btn-outline-dark me-1" title="View Booking">
                                            Contract
                                        </a>
                                    @endif
                                    <button type="button" class="btn btn-sm btn-outline-primary me-1" data-bs-toggle="modal" data-bs-target="#editUnitModal{{ $unit->id }}">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <form action="{{ route('erp.dev-units.destroy', $unit) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove unit from inventory?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger" {{ in_array($unit->status, ['booked', 'sold']) ? 'disabled' : '' }}>
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>

                            <!-- Edit Unit Modal -->
                            <div class="modal fade" id="editUnitModal{{ $unit->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="{{ route('erp.dev-units.update', $unit) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Edit Unit: {{ $unit->unit_number }}</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label small fw-semibold">Unit Number <span class="text-danger">*</span></label>
                                                        <input type="text" name="unit_number" class="form-control" value="{{ $unit->unit_number }}" required>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small fw-semibold">Floor Label <span class="text-danger">*</span></label>
                                                        <input type="text" name="floor_label" class="form-control" value="{{ $unit->floor_label }}" required>
                                                    </div>
                                                </div>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label small fw-semibold">Unit Typology</label>
                                                        <select name="unit_type" class="form-select">
                                                            <option value="apartment" {{ $unit->unit_type === 'apartment' ? 'selected' : '' }}>Apartment / Flat</option>
                                                            <option value="commercial_space" {{ $unit->unit_type === 'commercial_space' ? 'selected' : '' }}>Commercial Floor</option>
                                                            <option value="shop" {{ $unit->unit_type === 'shop' ? 'selected' : '' }}>Retail Shop</option>
                                                            <option value="duplex" {{ $unit->unit_type === 'duplex' ? 'selected' : '' }}>Duplex Suite</option>
                                                            <option value="penthouse" {{ $unit->unit_type === 'penthouse' ? 'selected' : '' }}>Penthouse</option>
                                                        </select>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small fw-semibold">Allocation Share</label>
                                                        <select name="allocation_share" class="form-select">
                                                            <option value="developer_share" {{ $unit->allocation_share === 'developer_share' ? 'selected' : '' }}>Developer Share (For Sale)</option>
                                                            <option value="landowner_share" {{ $unit->allocation_share === 'landowner_share' ? 'selected' : '' }}>Landowner Share</option>
                                                            <option value="reserved" {{ $unit->allocation_share === 'reserved' ? 'selected' : '' }}>Company Reserved</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label small fw-semibold">Gross Area (Sq.Ft) <span class="text-danger">*</span></label>
                                                        <input type="number" step="0.01" name="gross_area_sqft" class="form-control" value="{{ $unit->gross_area_sqft }}" required>
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small fw-semibold">Base Price / Sq.Ft (BDT) <span class="text-danger">*</span></label>
                                                        <input type="number" step="0.01" name="base_price_sqft" class="form-control" value="{{ $unit->base_price_sqft }}" required>
                                                    </div>
                                                </div>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-6">
                                                        <label class="form-label small fw-semibold">Utility Connection (BDT)</label>
                                                        <input type="number" step="0.01" name="utility_charges" class="form-control" value="{{ $unit->utility_charges }}">
                                                    </div>
                                                    <div class="col-6">
                                                        <label class="form-label small fw-semibold">Status</label>
                                                        <select name="status" class="form-select">
                                                            <option value="available" {{ $unit->status === 'available' ? 'selected' : '' }}>Available</option>
                                                            <option value="reserved" {{ $unit->status === 'reserved' ? 'selected' : '' }}>Reserved</option>
                                                            <option value="booked" {{ $unit->status === 'booked' ? 'selected' : '' }}>Booked</option>
                                                            <option value="sold" {{ $unit->status === 'sold' ? 'selected' : '' }}>Sold</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="row g-2 mb-3">
                                                    <div class="col-4">
                                                        <label class="form-label small fw-semibold">Bedrooms</label>
                                                        <input type="number" name="bedrooms" class="form-control" value="{{ $unit->bedrooms }}">
                                                    </div>
                                                    <div class="col-4">
                                                        <label class="form-label small fw-semibold">Bathrooms</label>
                                                        <input type="number" name="bathrooms" class="form-control" value="{{ $unit->bathrooms }}">
                                                    </div>
                                                    <div class="col-4">
                                                        <label class="form-label small fw-semibold">Facing</label>
                                                        <input type="text" name="facing" class="form-control" value="{{ $unit->facing }}">
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-erp-gold">Save Changes</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">
                                    No units configured for this project yet. Click "Add Unit" to populate the inventory.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 2: Overview & Specs -->
    <div class="tab-pane fade" id="overview">
        <div class="row g-4">
            <div class="col-lg-6">
                <div class="card card-custom p-4 mb-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Engineering & Building Specs</h6>
                    <div class="row g-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Superstructure Stories</small>
                            <div class="fw-bold text-dark">{{ $devProject->total_floors }} Floors</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Underground Basements</small>
                            <div class="fw-bold text-dark">{{ $devProject->total_basements }} Basements</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Planned Units Count</small>
                            <div class="fw-bold text-dark">{{ $devProject->total_units }} Units</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Total Parking Bays</small>
                            <div class="fw-bold text-dark">{{ $devProject->total_parking_slots }} Bays</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Sanction Approval Reference</small>
                            <div class="fw-bold text-dark font-monospace">{{ $devProject->sanction_no ?? 'Under Process' }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Cost Centre</small>
                            <span class="badge bg-light text-dark border">{{ $devProject->costCenter?->name ?? 'Head Office' }}</span>
                        </div>
                    </div>
                </div>

                <div class="card card-custom p-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Consultants & Key Dates</h6>
                    <div class="row g-3">
                        <div class="col-6">
                            <small class="text-muted d-block">Principal Architect</small>
                            <div class="fw-semibold text-dark">{{ $devProject->architect_name ?? '&mdash;' }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Structural Engineer</small>
                            <div class="fw-semibold text-dark">{{ $devProject->structural_engineer ?? '&mdash;' }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Construction Start</small>
                            <div class="fw-medium text-dark">{{ $devProject->start_date ? $devProject->start_date->format('d M Y') : 'TBD' }}</div>
                        </div>
                        <div class="col-6">
                            <small class="text-muted d-block">Target Completion</small>
                            <div class="fw-medium text-dark">{{ $devProject->expected_completion_date ? $devProject->expected_completion_date->format('d M Y') : 'TBD' }}</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="card card-custom p-4 mb-4">
                    <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Linked Land Parcel</h6>
                    @if($devProject->landParcel)
                        <div class="p-3 bg-light rounded-3 mb-3">
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <span class="fw-bold text-dark">{{ $devProject->landParcel->title }}</span>
                                <span class="badge bg-dark font-monospace">{{ $devProject->landParcel->parcel_code }}</span>
                            </div>
                            <small class="text-muted d-block mb-2">
                                Area: <strong>{{ number_format($devProject->landParcel->total_area_katha, 2) }} Katha</strong> 
                                &bull; Mouza: {{ $devProject->landParcel->mouza ?? 'N/A' }}
                            </small>
                            <div class="small fw-semibold text-secondary mb-3">
                                Landowner Ratio: {{ $devProject->landParcel->landowner_ratio }}% / Developer: {{ $devProject->landParcel->developer_ratio }}%
                            </div>
                            <a href="{{ route('erp.land-parcels.show', $devProject->landParcel) }}" class="btn btn-sm btn-outline-dark">
                                View Land Dossier & Vetting
                            </a>
                        </div>
                    @else
                        <p class="text-muted small">No land parcel linked directly to this project.</p>
                    @endif
                </div>

                @if($devProject->notes)
                    <div class="card card-custom p-4">
                        <h6 class="fw-bold text-dark border-bottom pb-2 mb-2">Notes</h6>
                        <p class="small text-muted mb-0">{{ $devProject->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Tab 3: Parking Bays -->
    <div class="tab-pane fade" id="parking">
        <div class="card card-custom">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark mb-0">Underground Parking Allotment Matrix</h6>
                <button type="button" class="btn btn-sm btn-erp-gold" data-bs-toggle="modal" data-bs-target="#addParkingModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Parking Slot
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Slot Number</th>
                            <th>Basement Level</th>
                            <th>Assigned Unit</th>
                            <th>Slot Price</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devProject->parkingSlots as $slot)
                            <tr>
                                <td>
                                    <span class="badge bg-dark font-monospace px-2 py-1">{{ $slot->slot_number }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">{{ $slot->basement_level }}</span>
                                </td>
                                <td>
                                    @if($slot->assignedUnit)
                                        <span class="fw-bold text-primary">{{ $slot->assignedUnit->unit_number }}</span>
                                    @else
                                        <span class="text-muted small">&mdash;</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="fw-semibold text-dark">BDT {{ number_format($slot->price) }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $slot->status === 'available' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($slot->status) }}
                                    </span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center py-4 text-muted">No parking slots configured yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Tab 4: Sales Bookings -->
    <div class="tab-pane fade" id="bookings">
        <div class="card card-custom">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark mb-0">Sales Contracts & Bookings</h6>
                <a href="{{ route('erp.dev-bookings.create', ['project_id' => $devProject->id]) }}" class="btn btn-sm btn-erp-gold">
                    <i class="bi bi-plus-lg me-1"></i> New Contract
                </a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Contract Code</th>
                            <th>Customer Name</th>
                            <th>Unit Purchased</th>
                            <th>Contract Total</th>
                            <th>Paid Amount</th>
                            <th>Balance Due</th>
                            <th>Status</th>
                            <th class="text-end">View</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devProject->bookings as $bkg)
                            <tr>
                                <td>
                                    <span class="badge bg-dark font-monospace">{{ $bkg->booking_code }}</span>
                                </td>
                                <td>
                                    <div class="fw-bold text-dark">{{ $bkg->customer->name }}</div>
                                    <small class="text-muted">{{ $bkg->customer->phone }}</small>
                                </td>
                                <td>
                                    <span class="fw-bold text-primary">{{ $bkg->unit->unit_number }}</span>
                                    <small class="text-muted">({{ number_format($bkg->unit->gross_area_sqft) }} sft)</small>
                                </td>
                                <td>
                                    <span class="fw-bold text-dark">BDT {{ number_format($bkg->total_contract_amount) }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-success">BDT {{ number_format($bkg->totalPaid()) }}</span>
                                </td>
                                <td>
                                    <span class="fw-semibold text-danger">BDT {{ number_format($bkg->balanceDue()) }}</span>
                                </td>
                                <td>
                                    <span class="badge {{ $bkg->status === 'active' ? 'bg-success' : 'bg-secondary' }}">
                                        {{ ucfirst($bkg->status) }}
                                    </span>
                                </td>
                                <td class="text-end">
                                    <a href="{{ route('erp.dev-bookings.show', $bkg) }}" class="btn btn-sm btn-outline-dark">
                                        Dossier
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-4 text-muted">No sales bookings signed for this project yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Add Unit -->
<div class="modal fade" id="addUnitModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('erp.dev-units.store', $devProject) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Unit to Inventory</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Unit Number <span class="text-danger">*</span></label>
                            <input type="text" name="unit_number" class="form-control" placeholder="e.g. Flat 4A" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Floor Label <span class="text-danger">*</span></label>
                            <input type="text" name="floor_label" class="form-control" placeholder="e.g. 4th Floor" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Typology <span class="text-danger">*</span></label>
                            <select name="unit_type" class="form-select" required>
                                <option value="apartment" selected>Apartment / Flat</option>
                                <option value="commercial_space">Commercial Floor</option>
                                <option value="shop">Retail Shop</option>
                                <option value="duplex">Duplex Suite</option>
                                <option value="penthouse">Penthouse</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Allocation Share <span class="text-danger">*</span></label>
                            <select name="allocation_share" class="form-select" required>
                                <option value="developer_share" selected>Developer Share (For Sale)</option>
                                <option value="landowner_share">Landowner Share</option>
                                <option value="reserved">Company Reserved</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Gross Area (Sq.Ft) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="gross_area_sqft" class="form-control" placeholder="e.g. 2150" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Base Price / Sq.Ft (BDT) <span class="text-danger">*</span></label>
                            <input type="number" step="0.01" name="base_price_sqft" class="form-control" placeholder="e.g. 8500" required>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Utility Charges (BDT)</label>
                            <input type="number" step="0.01" name="utility_charges" class="form-control" value="500000">
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Status</label>
                            <select name="status" class="form-select" required>
                                <option value="available" selected>Available</option>
                                <option value="reserved">Reserved</option>
                            </select>
                        </div>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-4">
                            <label class="form-label small fw-semibold">Bedrooms</label>
                            <input type="number" name="bedrooms" class="form-control" value="3">
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-semibold">Bathrooms</label>
                            <input type="number" name="bathrooms" class="form-control" value="3">
                        </div>
                        <div class="col-4">
                            <label class="form-label small fw-semibold">Facing</label>
                            <input type="text" name="facing" class="form-control" placeholder="e.g. South Lakeview">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp-gold">Add Unit</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Add Parking -->
<div class="modal fade" id="addParkingModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('erp.dev-parking.store', $devProject) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Add Parking Slot</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Parking Slot Number <span class="text-danger">*</span></label>
                        <input type="text" name="slot_number" class="form-control" placeholder="e.g. B1-P12" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Basement Level <span class="text-danger">*</span></label>
                        <select name="basement_level" class="form-select" required>
                            <option value="Basement 1" selected>Basement 1</option>
                            <option value="Basement 2">Basement 2</option>
                            <option value="Basement 3">Basement 3</option>
                            <option value="Ground Floor">Ground Floor Covered</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Price (BDT)</label>
                        <input type="number" step="0.01" name="price" class="form-control" value="500000">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp-gold">Add Parking</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endsection

