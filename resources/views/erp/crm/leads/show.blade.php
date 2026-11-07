@extends('erp.layouts.app')

@section('content')
<div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-4">
    <div>
        <div class="d-flex align-items-center gap-2 mb-1">
            <span class="badge bg-dark font-monospace px-2 py-1">{{ $lead->lead_code }}</span>
            <h4 class="fw-bold mb-0 text-dark">{{ $lead->name }}</h4>
            @if($lead->customer)
                <span class="badge bg-success"><i class="bi bi-check2-all me-1"></i>Converted Customer: {{ $lead->customer->customer_code }}</span>
            @endif
        </div>
        <p class="text-muted small mb-0">Lead dossier, interaction timeline, site visits, and booking readiness.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <a href="{{ route('erp.leads.edit', $lead) }}" class="btn btn-outline-secondary btn-sm">
            <i class="bi bi-pencil me-1"></i> Edit Lead
        </a>
        <button type="button" class="btn btn-outline-primary btn-sm" data-bs-toggle="modal" data-bs-target="#logActivityModal">
            <i class="bi bi-telephone-plus me-1"></i> Log Activity
        </button>
        <button type="button" class="btn btn-outline-warning btn-sm" data-bs-toggle="modal" data-bs-target="#scheduleVisitModal">
            <i class="bi bi-geo-alt-fill me-1"></i> Schedule Site Visit
        </button>
        @if(!$lead->customer)
            <button type="button" class="btn btn-erp-gold btn-sm" data-bs-toggle="modal" data-bs-target="#convertCustomerModal">
                <i class="bi bi-person-check-fill me-1"></i> Convert to Customer
            </button>
        @else
            <a href="{{ route('erp.customers.show', $lead->customer) }}" class="btn btn-success btn-sm">
                <i class="bi bi-person-badge me-1"></i> View Customer File
            </a>
        @endif
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Lead Profile & Details -->
    <div class="col-lg-4">
        <!-- Lead Score & Stage Card -->
        <div class="card card-custom p-4 mb-4">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <span class="text-muted small fw-semibold text-uppercase">Lead Score</span>
                <span class="badge bg-{{ $lead->score >= 70 ? 'success' : ($lead->score >= 40 ? 'warning' : 'secondary') }} px-2 py-1">
                    {{ $lead->score }} / 100
                </span>
            </div>
            <div class="progress mb-3" style="height: 10px;">
                <div class="progress-bar bg-{{ $lead->score >= 70 ? 'success' : ($lead->score >= 40 ? 'warning' : 'secondary') }}" 
                     role="progressbar" style="width: {{ $lead->score }}%"></div>
            </div>

            <!-- Current Stage & Quick Update -->
            <label class="form-label small fw-semibold text-muted text-uppercase mb-1">Pipeline Stage</label>
            <form action="{{ route('erp.leads.update-stage', $lead) }}" method="POST">
                @csrf
                <div class="input-group input-group-sm">
                    <select name="stage" class="form-select">
                        <option value="new" {{ $lead->stage === 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ $lead->stage === 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="qualified" {{ $lead->stage === 'qualified' ? 'selected' : '' }}>Qualified</option>
                        <option value="site_visit_scheduled" {{ $lead->stage === 'site_visit_scheduled' ? 'selected' : '' }}>Site Visit Scheduled</option>
                        <option value="site_visit_completed" {{ $lead->stage === 'site_visit_completed' ? 'selected' : '' }}>Site Visit Completed</option>
                        <option value="negotiation" {{ $lead->stage === 'negotiation' ? 'selected' : '' }}>Negotiation</option>
                        <option value="booked" {{ $lead->stage === 'booked' ? 'selected' : '' }}>Won / Booked</option>
                        <option value="lost" {{ $lead->stage === 'lost' ? 'selected' : '' }}>Lost / Cold</option>
                    </select>
                    <button type="submit" class="btn btn-dark">Update</button>
                </div>
            </form>
        </div>

        <!-- Contact Profile -->
        <div class="card card-custom p-4 mb-4">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Client Information</h6>
            <div class="mb-3">
                <small class="text-muted d-block">Phone Number</small>
                <div class="d-flex align-items-center justify-content-between mt-1">
                    <span class="fw-bold text-dark"><i class="bi bi-telephone-fill text-primary me-2"></i>{{ $lead->phone }}</span>
                    <a href="{{ $lead->whatsAppUrl() }}" target="_blank" class="btn btn-sm btn-outline-success">
                        <i class="bi bi-whatsapp me-1"></i> WhatsApp
                    </a>
                </div>
                @if($lead->secondary_phone)
                    <div class="small text-muted mt-1"><i class="bi bi-telephone text-secondary me-2"></i>{{ $lead->secondary_phone }}</div>
                @endif
            </div>

            <div class="mb-3">
                <small class="text-muted d-block">Email Address</small>
                <div class="fw-medium text-dark"><i class="bi bi-envelope-fill text-secondary me-2"></i>{{ $lead->email ?? 'No email provided' }}</div>
            </div>

            <div class="mb-3">
                <small class="text-muted d-block">Source & Priority</small>
                @if($lead->source === 'facebook_lead_ad')
                    <span class="badge bg-primary text-white me-1"><i class="bi bi-facebook me-1"></i> Facebook Ad</span>
                @elseif($lead->source === 'whatsapp_ad' || $lead->source === 'whatsapp')
                    <span class="badge bg-success text-white me-1"><i class="bi bi-whatsapp me-1"></i> WhatsApp Ad</span>
                @else
                    <span class="badge bg-light text-dark border me-1">{{ ucfirst(str_replace('_', ' ', $lead->source)) }}</span>
                @endif
                <span class="badge bg-secondary">{{ ucfirst($lead->priority) }} Priority</span>
            </div>

            @if($lead->campaign_name || $lead->ad_name)
                <div class="mb-3 p-2 bg-light rounded-2 border">
                    <small class="text-muted d-block fw-semibold" style="font-size: 0.72rem;">Meta Ad Attribution</small>
                    <div class="small fw-bold text-dark"><i class="bi bi-megaphone-fill text-primary me-1"></i> {{ $lead->campaign_name ?? 'Facebook Ad' }}</div>
                    @if($lead->ad_name)
                        <div class="text-muted" style="font-size: 0.75rem;">{{ $lead->ad_name }}</div>
                    @endif
                </div>
            @endif

            <div class="mb-3">
                <small class="text-muted d-block">Assigned Agent</small>
                <div class="fw-bold text-dark mt-1">
                    @if($lead->assignedAgent)
                        <i class="bi bi-person-badge-fill text-warning me-1"></i> {{ $lead->assignedAgent->name }}
                    @else
                        <span class="text-danger small">Unassigned</span>
                    @endif
                </div>
            </div>

            <div class="mb-2">
                <small class="text-muted d-block">Operating Branch</small>
                <div class="small fw-semibold text-secondary mt-1">
                    <i class="bi bi-geo-alt-fill me-1"></i> {{ $lead->branch?->name ?? 'Company Head Office' }}
                </div>
            </div>
        </div>

        <!-- Property Interest & Budget -->
        <div class="card card-custom p-4">
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3">Property Requirements</h6>
            <div class="mb-3">
                <small class="text-muted d-block">Interested Project</small>
                <div class="fw-semibold text-primary mt-1">
                    {{ $lead->featuredProject?->title ?? 'Open / Any Project' }}
                </div>
            </div>

            <div class="mb-3">
                <small class="text-muted d-block">Property Type</small>
                <div class="fw-semibold text-dark mt-1">
                    {{ $lead->property_type_interest ?? 'Apartment / Residential' }}
                </div>
            </div>

            <div class="mb-3">
                <small class="text-muted d-block">Budget Range</small>
                <div class="fw-bold text-success mt-1">
                    BDT {{ number_format((float)($lead->min_budget ?? 0)) }} &mdash; {{ number_format((float)($lead->max_budget ?? 0)) }}
                </div>
            </div>

            <div class="mb-3">
                <small class="text-muted d-block">Preferred Location & Size</small>
                <div class="small text-dark mt-1">
                    {{ $lead->preferred_location ?? 'Not specified' }} 
                    @if($lead->preferred_size_sqft) ({{ $lead->preferred_size_sqft }}) @endif
                </div>
            </div>

            @if($lead->notes)
                <div class="mt-3 p-3 bg-light rounded-3 small">
                    <strong class="d-block mb-1 text-secondary">Notes:</strong>
                    {{ $lead->notes }}
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Site Visits & Activity Timeline -->
    <div class="col-lg-8">
        <!-- Site Visits Scheduled/Completed Section -->
        <div class="card card-custom mb-4">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-geo-alt-fill text-warning me-2"></i>Site Visits & Property Inspection</h6>
                <button type="button" class="btn btn-sm btn-outline-warning" data-bs-toggle="modal" data-bs-target="#scheduleVisitModal">
                    <i class="bi bi-plus-lg me-1"></i> New Site Visit
                </button>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light small text-uppercase">
                        <tr>
                            <th>Scheduled Time</th>
                            <th>Project / Property</th>
                            <th>Agent</th>
                            <th>Status</th>
                            <th>Rating & Feedback</th>
                            <th class="text-end">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($lead->siteVisits as $visit)
                            <tr>
                                <td>
                                    <div class="fw-semibold text-dark">{{ $visit->scheduled_at->format('d M Y') }}</div>
                                    <small class="text-muted">{{ $visit->scheduled_at->format('h:i A') }}</small>
                                </td>
                                <td>
                                    <span class="small fw-semibold text-dark">{{ $visit->featuredProject?->title ?? $visit->property?->title ?? 'General Site Visit' }}</span>
                                </td>
                                <td>
                                    <small class="text-muted">{{ $visit->assignedAgent?->name ?? 'Unassigned' }}</small>
                                </td>
                                <td>
                                    @php
                                        $vColor = match($visit->status) {
                                            'completed' => 'success',
                                            'scheduled' => 'warning',
                                            'cancelled' => 'danger',
                                            default     => 'secondary',
                                        };
                                    @endphp
                                    <span class="badge bg-{{ $vColor }}">{{ ucfirst($visit->status) }}</span>
                                </td>
                                <td>
                                    @if($visit->client_rating)
                                        <div class="text-warning small mb-1">
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="bi bi-star{{ $i <= $visit->client_rating ? '-fill' : '' }}"></i>
                                            @endfor
                                        </div>
                                    @endif
                                    <small class="text-muted">{{ Str::limit($visit->client_feedback ?? $visit->agent_remarks, 35) ?? '&mdash;' }}</small>
                                </td>
                                <td class="text-end">
                                    <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#editVisitModal{{ $visit->id }}">
                                        Outcome
                                    </button>
                                </td>
                            </tr>

                            <!-- Edit Site Visit Modal -->
                            <div class="modal fade" id="editVisitModal{{ $visit->id }}" tabindex="-1">
                                <div class="modal-dialog">
                                    <form action="{{ route('erp.site-visits.update', $visit) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title fw-bold">Update Site Visit Outcome</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                            </div>
                                            <div class="modal-body">
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Status <span class="text-danger">*</span></label>
                                                    <select name="status" class="form-select" required>
                                                        <option value="scheduled" {{ $visit->status === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                                                        <option value="completed" {{ $visit->status === 'completed' ? 'selected' : '' }}>Completed</option>
                                                        <option value="cancelled" {{ $visit->status === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                                        <option value="no_show" {{ $visit->status === 'no_show' ? 'selected' : '' }}>Client No-Show</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Client Rating (1 to 5 Stars)</label>
                                                    <select name="client_rating" class="form-select">
                                                        <option value="">-- No Rating --</option>
                                                        <option value="5" {{ $visit->client_rating == 5 ? 'selected' : '' }}>5 Stars - Highly Interested</option>
                                                        <option value="4" {{ $visit->client_rating == 4 ? 'selected' : '' }}>4 Stars - Good Impression</option>
                                                        <option value="3" {{ $visit->client_rating == 3 ? 'selected' : '' }}>3 Stars - Neutral / Exploring</option>
                                                        <option value="2" {{ $visit->client_rating == 2 ? 'selected' : '' }}>2 Stars - Issues with Price/Layout</option>
                                                        <option value="1" {{ $visit->client_rating == 1 ? 'selected' : '' }}>1 Star - Not Interested</option>
                                                    </select>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Client Feedback</label>
                                                    <textarea name="client_feedback" rows="2" class="form-control" placeholder="What the client liked or disliked...">{{ $visit->client_feedback }}</textarea>
                                                </div>
                                                <div class="mb-3">
                                                    <label class="form-label small fw-semibold">Agent Remarks & Next Action</label>
                                                    <textarea name="agent_remarks" rows="2" class="form-control" placeholder="Internal sales remarks...">{{ $visit->agent_remarks }}</textarea>
                                                </div>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                                                <button type="submit" class="btn btn-erp-gold">Save Outcome</button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No site visits logged for this client yet.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Activity Timeline -->
        <div class="card card-custom">
            <div class="card-header bg-white py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-bold text-dark mb-0"><i class="bi bi-clock-history text-primary me-2"></i>Interaction Timeline & Call Logs</h6>
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#logActivityModal">
                    <i class="bi bi-plus-lg me-1"></i> Add Log
                </button>
            </div>
            <div class="card-body p-4">
                <div class="timeline">
                    @forelse($lead->activities as $act)
                        <div class="d-flex gap-3 mb-4">
                            <div>
                                @php
                                    $icon = match($act->type) {
                                        'call'          => 'telephone-fill bg-primary',
                                        'meeting'       => 'people-fill bg-success',
                                        'site_visit'    => 'geo-alt-fill bg-warning',
                                        'email'         => 'envelope-fill bg-info',
                                        'whatsapp'      => 'whatsapp bg-success',
                                        'status_change' => 'arrow-repeat bg-dark',
                                        default         => 'chat-left-text-fill bg-secondary',
                                    };
                                @endphp
                                <div class="rounded-circle text-white d-flex align-items-center justify-content-center {{ explode(' ', $icon)[1] }}" style="width: 38px; height: 38px;">
                                    <i class="bi bi-{{ explode(' ', $icon)[0] }} small"></i>
                                </div>
                            </div>
                            <div class="flex-grow-1 bg-light p-3 rounded-3">
                                <div class="d-flex align-items-center justify-content-between mb-1">
                                    <span class="fw-bold text-dark">{{ $act->subject }}</span>
                                    <small class="text-muted">{{ $act->activity_date->format('d M Y, h:i A') }}</small>
                                </div>
                                @if($act->description)
                                    <p class="small text-secondary mb-2">{{ $act->description }}</p>
                                @endif
                                <div class="d-flex align-items-center gap-3 small text-muted">
                                    <span><i class="bi bi-person me-1"></i> {{ $act->admin?->name ?? 'System' }}</span>
                                    @if($act->outcome)
                                        <span><i class="bi bi-check2-circle me-1 text-success"></i> Outcome: {{ ucfirst($act->outcome) }}</span>
                                    @endif
                                    @if($act->next_follow_up_date)
                                        <span class="text-warning fw-semibold"><i class="bi bi-calendar-event me-1"></i> Follow-up: {{ $act->next_follow_up_date->format('d M Y, h:i A') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-4 text-muted">
                            <i class="bi bi-chat-square-dots fs-3 d-block mb-2"></i>
                            No interaction activities recorded yet. Click "Add Log" to record phone calls or notes.
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal: Log Activity -->
<div class="modal fade" id="logActivityModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('erp.leads.activities.store', $lead) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Log Interaction / Follow-up</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Interaction Type <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="call">Phone Call</option>
                                <option value="meeting">Physical Meeting</option>
                                <option value="whatsapp">WhatsApp Chat</option>
                                <option value="email">Email</option>
                                <option value="note">Internal Memo / Note</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Outcome</label>
                            <select name="outcome" class="form-select">
                                <option value="connected">Connected / Spoke</option>
                                <option value="busy">Client Busy</option>
                                <option value="no_answer">No Answer / Switched Off</option>
                                <option value="interested">Very Interested</option>
                                <option value="rescheduled">Follow-up Requested</option>
                                <option value="not_interested">Not Interested</option>
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Subject / Title <span class="text-danger">*</span></label>
                        <input type="text" name="subject" class="form-control" placeholder="e.g. Discussed 3BHK pricing with buyer" required>
                    </div>
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Interaction Date & Time <span class="text-danger">*</span></label>
                            <input type="datetime-local" name="activity_date" class="form-control" value="{{ now()->format('Y-m-d\TH:i') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label small fw-semibold">Next Follow-up Date</label>
                            <input type="datetime-local" name="next_follow_up_date" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Discussion Summary / Notes</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="What was agreed or discussed..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp-gold">Save Interaction</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Schedule Site Visit -->
<div class="modal fade" id="scheduleVisitModal" tabindex="-1">
    <div class="modal-dialog">
        <form action="{{ route('erp.leads.site-visits.store', $lead) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Schedule Project Site Visit</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Scheduled Date & Time <span class="text-danger">*</span></label>
                        <input type="datetime-local" name="scheduled_at" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Project Location</label>
                        <select name="featured_project_id" class="form-select">
                            <option value="">-- Choose Project --</option>
                            @foreach($projects as $p)
                                <option value="{{ $p->id }}" {{ $lead->featured_project_id == $p->id ? 'selected' : '' }}>{{ $p->title }} ({{ $p->location }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Assigned Accompaniment Agent</label>
                        <select name="assigned_agent_id" class="form-select">
                            <option value="">-- Auto Use Lead Agent --</option>
                            @foreach($agents as $agent)
                                <option value="{{ $agent->id }}" {{ $lead->assigned_agent_id == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Visit Preparation Notes</label>
                        <textarea name="agent_remarks" rows="2" class="form-control" placeholder="e.g. Client requested to see 7th floor duplex and parking space..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-erp-gold">Confirm Schedule</button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Modal: Convert to Customer -->
@if(!$lead->customer)
<div class="modal fade" id="convertCustomerModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form action="{{ route('erp.leads.convert', $lead) }}" method="POST">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title fw-bold">Convert Lead to Permanent Customer Profile</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info py-2 small mb-3">
                        <i class="bi bi-info-circle me-1"></i> Converting this lead generates a permanent Customer Code (e.g. <code>CUST-2026-00001</code>) and opens eligibility for Unit Bookings, Deeds, and Account Billing.
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Customer Full Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" value="{{ $lead->name }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Primary Phone <span class="text-danger">*</span></label>
                            <input type="text" name="phone" class="form-control" value="{{ $lead->phone }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Email Address</label>
                            <input type="email" name="email" class="form-control" value="{{ $lead->email }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">National ID (NID) Number</label>
                            <input type="text" name="nid_number" class="form-control" placeholder="e.g. 1984251092837">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Tax Identification No. (TIN)</label>
                            <input type="text" name="tin_number" class="form-control" placeholder="e.g. 482910482910">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Present Address</label>
                            <input type="text" name="present_address" class="form-control" placeholder="Current residential address">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nominee Name</label>
                            <input type="text" name="nominee_name" class="form-control" placeholder="e.g. Nominee Full Name">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label small fw-semibold">Nominee Relation</label>
                            <input type="text" name="nominee_relation" class="form-control" placeholder="e.g. Spouse / Son / Daughter">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success px-4"><i class="bi bi-check-circle me-1"></i> Confirm Conversion</button>
                </div>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

