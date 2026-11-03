@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Land Bank & Acquisition Register</h4>
        <p class="text-muted small mb-0">Land parcel tracking, Joint Venture ratios, 8-stage legal vetting, and architectural feasibility.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        <a href="{{ route('erp.land-parcels.create') }}" class="btn btn-erp-gold btn-sm px-3">
            <i class="bi bi-plus-circle-fill me-1"></i> Register Land Parcel
        </a>
    </div>
</div>

<!-- Status Filter Ribbon -->
<div class="d-flex flex-wrap gap-2 mb-4 pb-2 border-bottom">
    <a href="{{ route('erp.land-parcels.index') }}" class="btn btn-sm {{ !request('status') ? 'btn-dark' : 'btn-light border' }} rounded-pill px-3">
        All Parcels <span class="badge {{ !request('status') ? 'bg-secondary' : 'bg-light text-dark border' }} ms-1">{{ array_sum($statusCounts) }}</span>
    </a>
    @php
        $statuses = [
            'scouting'          => ['label' => 'Scouting / Prospecting', 'color' => 'secondary'],
            'legal_vetting'     => ['label' => 'Legal Vetting', 'color' => 'warning'],
            'feasibility_study' => ['label' => 'Feasibility Study', 'color' => 'info'],
            'mou_negotiation'   => ['label' => 'MOU Negotiation', 'color' => 'primary'],
            'registered'        => ['label' => 'Deed Registered', 'color' => 'dark'],
            'handed_over'       => ['label' => 'Handed Over to Construction', 'color' => 'success'],
            'dropped'           => ['label' => 'Dropped / Ineligible', 'color' => 'danger'],
        ];
    @endphp
    @foreach($statuses as $key => $meta)
        <a href="{{ route('erp.land-parcels.index', array_merge(request()->query(), ['status' => $key])) }}" 
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
    <form method="GET" action="{{ route('erp.land-parcels.index') }}" class="row g-2 align-items-center">
        @if(request('status'))
            <input type="hidden" name="status" value="{{ request('status') }}">
        @endif
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search by title, parcel code, mouza, dag no..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="acquisition_type" class="form-select form-select-sm">
                <option value="">-- All Acquisition Modes --</option>
                <option value="joint_venture" {{ request('acquisition_type') === 'joint_venture' ? 'selected' : '' }}>Joint Venture (JV)</option>
                <option value="outright_purchase" {{ request('acquisition_type') === 'outright_purchase' ? 'selected' : '' }}>Outright Purchase</option>
                <option value="leasehold" {{ request('acquisition_type') === 'leasehold' ? 'selected' : '' }}>Leasehold</option>
                <option value="power_of_attorney" {{ request('acquisition_type') === 'power_of_attorney' ? 'selected' : '' }}>Power of Attorney</option>
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm flex-grow-1">Filter</button>
            <a href="{{ route('erp.land-parcels.index') }}" class="btn btn-light btn-sm border">Reset</a>
        </div>
    </form>
</div>

<!-- Parcels Table -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Parcel Code & Title</th>
                    <th>Area (Katha / Dec)</th>
                    <th>Acquisition & Ratio</th>
                    <th>Legal Vetting Clear</th>
                    <th>Feasibility ROI</th>
                    <th>Status</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($parcels as $parcel)
                    @php
                        $vettingPct = $parcel->legalVettingClearance();
                        $latestFeas = $parcel->latestFeasibility();
                    @endphp
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-dark font-monospace" style="font-size: 0.72rem;">{{ $parcel->parcel_code }}</span>
                                <a href="{{ route('erp.land-parcels.show', $parcel) }}" class="fw-bold text-dark text-decoration-none">
                                    {{ $parcel->title }}
                                </a>
                            </div>
                            <small class="text-muted d-block mt-1">
                                <i class="bi bi-geo-alt me-1 text-danger"></i> Mouza: {{ $parcel->mouza ?? 'N/A' }} 
                                @if($parcel->dag_no) &bull; Dag: {{ $parcel->dag_no }} @endif
                            </small>
                        </td>
                        <td>
                            <div class="fw-bold text-dark">{{ number_format($parcel->total_area_katha, 2) }} Katha</div>
                            <small class="text-muted">{{ number_format($parcel->total_area_decimal ?? 0, 2) }} Decimals</small>
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border text-capitalize mb-1">
                                {{ str_replace('_', ' ', $parcel->acquisition_type) }}
                            </span>
                            @if($parcel->acquisition_type === 'joint_venture')
                                <div class="small text-secondary fw-semibold">
                                    LO: {{ $parcel->landowner_ratio }}% / Dev: {{ $parcel->developer_ratio }}%
                                </div>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                    <div class="progress-bar bg-{{ $vettingPct >= 80 ? 'success' : ($vettingPct >= 40 ? 'warning' : 'secondary') }}" 
                                         role="progressbar" style="width: {{ $vettingPct }}%"></div>
                                </div>
                                <span class="small fw-semibold">{{ $vettingPct }}%</span>
                            </div>
                            <small class="text-muted" style="font-size: 0.72rem;">8-Stage Checklist</small>
                        </td>
                        <td>
                            @if($latestFeas)
                                <div class="fw-bold text-success">{{ number_format($latestFeas->roi_percentage, 1) }}% ROI</div>
                                <small class="text-muted">{{ ucfirst($latestFeas->status) }}</small>
                            @else
                                <a href="{{ route('erp.land-feasibility.create', $parcel) }}" class="btn btn-sm btn-outline-info py-0 px-2" style="font-size: 0.75rem;">
                                    Run Feasibility
                                </a>
                            @endif
                        </td>
                        <td>
                            @php
                                $sColor = match($parcel->status) {
                                    'handed_over'   => 'success',
                                    'registered'    => 'dark',
                                    'legal_vetting' => 'warning',
                                    'mou_negotiation' => 'primary',
                                    'dropped'       => 'danger',
                                    default         => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $sColor }}">{{ ucfirst(str_replace('_', ' ', $parcel->status)) }}</span>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('erp.land-parcels.show', $parcel) }}" class="btn btn-sm btn-outline-dark me-1" title="View Dossier">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('erp.land-parcels.edit', $parcel) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit Parcel">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('erp.land-parcels.destroy', $parcel) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive this land parcel?')">
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
                            <i class="bi bi-map fs-2 d-block mb-2 text-secondary"></i>
                            No land parcels registered in this workspace yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($parcels->hasPages())
        <div class="p-3 border-top">
            {{ $parcels->links() }}
        </div>
    @endif
</div>
@endsection
