@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">CRM & Lead Pipeline</h4>
        <p class="text-muted small mb-0">Multi-source lead capture, sales agent assignment, scoring & conversion tracker.</p>
    </div>
    <div class="d-flex align-items-center gap-2">
        @if($unimportedSubmissionsCount > 0)
            <form action="{{ route('erp.leads.import-submissions') }}" method="POST">
                @csrf
                <button type="submit" class="btn btn-outline-primary btn-sm px-3">
                    <i class="bi bi-cloud-arrow-down-fill me-1"></i> Ingest Web Inquiries ({{ $unimportedSubmissionsCount }})
                </button>
            </form>
        @endif
        <a href="{{ route('erp.leads.create') }}" class="btn btn-erp-gold btn-sm px-3">
            <i class="bi bi-person-plus-fill me-1"></i> New Lead
        </a>
    </div>
</div>

<!-- Pipeline Stage Filter Ribbon -->
<div class="d-flex flex-wrap gap-2 mb-4 pb-2 border-bottom">
    <a href="{{ route('erp.leads.index') }}" class="btn btn-sm {{ !request('stage') ? 'btn-dark' : 'btn-light border' }} rounded-pill px-3">
        All Leads <span class="badge {{ !request('stage') ? 'bg-secondary' : 'bg-light text-dark border' }} ms-1">{{ array_sum($stageCounts) }}</span>
    </a>
    @php
        $stages = [
            'new'                  => ['label' => 'New', 'color' => 'info'],
            'contacted'            => ['label' => 'Contacted', 'color' => 'primary'],
            'qualified'            => ['label' => 'Qualified', 'color' => 'secondary'],
            'site_visit_scheduled' => ['label' => 'Site Visit Scheduled', 'color' => 'warning'],
            'site_visit_completed' => ['label' => 'Site Visit Done', 'color' => 'info'],
            'negotiation'          => ['label' => 'Negotiation', 'color' => 'dark'],
            'booked'               => ['label' => 'Won / Booked', 'color' => 'success'],
            'lost'                 => ['label' => 'Lost / Cold', 'color' => 'danger'],
        ];
    @endphp
    @foreach($stages as $key => $meta)
        <a href="{{ route('erp.leads.index', array_merge(request()->query(), ['stage' => $key])) }}" 
           class="btn btn-sm {{ request('stage') === $key ? 'btn-' . $meta['color'] : 'btn-light border' }} rounded-pill px-3">
            {{ $meta['label'] }}
            <span class="badge {{ request('stage') === $key ? 'bg-light text-dark' : 'bg-secondary' }} ms-1">
                {{ $stageCounts[$key] ?? 0 }}
            </span>
        </a>
    @endforeach
</div>

<!-- Search & Filter Bar -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('erp.leads.index') }}" class="row g-2 align-items-center">
        @if(request('stage'))
            <input type="hidden" name="stage" value="{{ request('stage') }}">
        @endif
        <div class="col-md-5">
            <div class="input-group input-group-sm">
                <span class="input-group-text bg-white"><i class="bi bi-search text-muted"></i></span>
                <input type="text" name="search" class="form-control" placeholder="Search by name, phone, email, lead code..." value="{{ request('search') }}">
            </div>
        </div>
        <div class="col-md-3">
            <select name="source" class="form-select form-select-sm">
                <option value="">-- All Sources --</option>
                <option value="web_form" {{ request('source') === 'web_form' ? 'selected' : '' }}>Website Inquiry</option>
                <option value="walk_in" {{ request('source') === 'walk_in' ? 'selected' : '' }}>Walk-in</option>
                <option value="social_media" {{ request('source') === 'social_media' ? 'selected' : '' }}>Social Media / Meta</option>
                <option value="phone_call" {{ request('source') === 'phone_call' ? 'selected' : '' }}>Direct Phone Call</option>
                <option value="agent_referral" {{ request('source') === 'agent_referral' ? 'selected' : '' }}>Agent Referral</option>
            </select>
        </div>
        <div class="col-md-2">
            <select name="agent_id" class="form-select form-select-sm">
                <option value="">-- All Agents --</option>
                @foreach($agents as $agent)
                    <option value="{{ $agent->id }}" {{ request('agent_id') == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                @endforeach
            </select>
        </div>
        <div class="col-md-2 d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm flex-grow-1">Filter</button>
            <a href="{{ route('erp.leads.index') }}" class="btn btn-light btn-sm border">Reset</a>
        </div>
    </form>
</div>

<!-- Leads List -->
<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Lead Info</th>
                    <th>Interest & Budget</th>
                    <th>Source & Priority</th>
                    <th>Assigned Agent</th>
                    <th>Lead Score</th>
                    <th>Stage Progression</th>
                    <th class="text-end">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($leads as $lead)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="badge bg-dark font-monospace" style="font-size: 0.72rem;">{{ $lead->lead_code }}</span>
                                <span class="fw-bold text-dark">{{ $lead->name }}</span>
                            </div>
                            <div class="small text-muted mt-1">
                                <i class="bi bi-telephone me-1 text-primary"></i> {{ $lead->phone }}
                                @if($lead->email)
                                    <span class="mx-1">&bull;</span>
                                    <i class="bi bi-envelope me-1 text-secondary"></i> {{ $lead->email }}
                                @endif
                            </div>
                            <small class="text-muted d-block" style="font-size: 0.75rem;">
                                Added {{ $lead->created_at->diffForHumans() }}
                            </small>
                        </td>
                        <td>
                            @if($lead->featuredProject)
                                <span class="badge bg-primary bg-opacity-10 text-primary d-inline-block mb-1">
                                    <i class="bi bi-building me-1"></i> {{ $lead->featuredProject->title }}
                                </span>
                            @elseif($lead->property)
                                <span class="badge bg-info bg-opacity-10 text-info d-inline-block mb-1">
                                    <i class="bi bi-house me-1"></i> {{ $lead->property->title }}
                                </span>
                            @elseif($lead->property_type_interest)
                                <span class="badge bg-light text-dark border d-inline-block mb-1">{{ $lead->property_type_interest }}</span>
                            @else
                                <span class="text-muted small">&mdash;</span>
                            @endif

                            @if($lead->min_budget || $lead->max_budget)
                                <div class="small text-muted">
                                    BDT {{ number_format($lead->min_budget ?? 0) }} - {{ number_format($lead->max_budget ?? 0) }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="badge bg-light text-dark border text-capitalize mb-1">
                                {{ str_replace('_', ' ', $lead->source) }}
                            </span>
                            <div>
                                @php
                                    $pColor = match($lead->priority) {
                                        'urgent' => 'danger',
                                        'high'   => 'warning',
                                        'medium' => 'primary',
                                        default  => 'secondary',
                                    };
                                @endphp
                                <span class="badge bg-{{ $pColor }}" style="font-size: 0.7rem;">{{ ucfirst($lead->priority) }}</span>
                            </div>
                        </td>
                        <td>
                            @if($lead->assignedAgent)
                                <div class="d-flex align-items-center gap-1">
                                    <img src="https://ui-avatars.com/api/?name={{ urlencode($lead->assignedAgent->name) }}&background=e2e8f0&color=090e1a" class="rounded-circle" width="22" height="22">
                                    <span class="small fw-semibold text-dark">{{ $lead->assignedAgent->name }}</span>
                                </div>
                            @else
                                <span class="badge bg-warning text-dark"><i class="bi bi-exclamation-circle me-1"></i>Unassigned</span>
                            @endif
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <div class="progress flex-grow-1" style="height: 6px; width: 60px;">
                                    <div class="progress-bar bg-{{ $lead->score >= 70 ? 'success' : ($lead->score >= 40 ? 'warning' : 'secondary') }}" 
                                         role="progressbar" style="width: {{ $lead->score }}%"></div>
                                </div>
                                <span class="small fw-bold">{{ $lead->score }}%</span>
                            </div>
                        </td>
                        <td>
                            <form action="{{ route('erp.leads.update-stage', $lead) }}" method="POST" class="d-inline">
                                @csrf
                                <select name="stage" class="form-select form-select-sm" style="font-size: 0.8rem; width: auto;" onchange="this.form.submit()">
                                    <option value="new" {{ $lead->stage === 'new' ? 'selected' : '' }}>New</option>
                                    <option value="contacted" {{ $lead->stage === 'contacted' ? 'selected' : '' }}>Contacted</option>
                                    <option value="qualified" {{ $lead->stage === 'qualified' ? 'selected' : '' }}>Qualified</option>
                                    <option value="site_visit_scheduled" {{ $lead->stage === 'site_visit_scheduled' ? 'selected' : '' }}>Site Visit Scheduled</option>
                                    <option value="site_visit_completed" {{ $lead->stage === 'site_visit_completed' ? 'selected' : '' }}>Site Visit Completed</option>
                                    <option value="negotiation" {{ $lead->stage === 'negotiation' ? 'selected' : '' }}>Negotiation</option>
                                    <option value="booked" {{ $lead->stage === 'booked' ? 'selected' : '' }}>Won / Booked</option>
                                    <option value="lost" {{ $lead->stage === 'lost' ? 'selected' : '' }}>Lost / Cold</option>
                                </select>
                            </form>
                        </td>
                        <td class="text-end">
                            <a href="{{ route('erp.leads.show', $lead) }}" class="btn btn-sm btn-outline-dark me-1" title="View Dossier & Timeline">
                                <i class="bi bi-eye"></i>
                            </a>
                            <a href="{{ route('erp.leads.edit', $lead) }}" class="btn btn-sm btn-outline-primary me-1" title="Edit Lead">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('erp.leads.destroy', $lead) }}" method="POST" class="d-inline" onsubmit="return confirm('Archive this lead record?')">
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
                            <i class="bi bi-inbox fs-2 d-block mb-2 text-secondary"></i>
                            No leads found matching your criteria.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($leads->hasPages())
        <div class="p-3 border-top">
            {{ $leads->links() }}
        </div>
    @endif
</div>
@endsection
