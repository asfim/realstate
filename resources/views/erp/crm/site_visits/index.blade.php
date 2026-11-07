@extends('erp.layouts.app')

@section('content')
<div class="erp-header-row mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Site Visits & Property Inspections</h4>
        <p class="text-muted small mb-0">Scheduled client site tours, agent accompaniment logs, client feedback, and ratings.</p>
    </div>
    <a href="{{ route('erp.leads.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-person-lines-fill me-1"></i> Leads Pipeline
    </a>
</div>

<!-- Filters -->
<div class="card card-custom p-3 mb-4">
    <form method="GET" action="{{ route('erp.site-visits.index') }}" class="row g-2 align-items-center">
        <div class="col-12 col-md-4">
            <select name="status" class="form-select form-select-sm">
                <option value="">-- All Visit Statuses --</option>
                <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
                <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
                <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                <option value="no_show" {{ request('status') === 'no_show' ? 'selected' : '' }}>Client No-Show</option>
            </select>
        </div>
        <div class="col-12 col-md-auto d-flex gap-2">
            <button type="submit" class="btn btn-dark btn-sm px-3">Filter</button>
            <a href="{{ route('erp.site-visits.index') }}" class="btn btn-light btn-sm border px-3">Reset</a>
        </div>
    </form>
</div>

<div class="card card-custom">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light small text-uppercase">
                <tr>
                    <th>Scheduled Date & Time</th>
                    <th>Lead / Prospect</th>
                    <th>Project / Property</th>
                    <th>Accompaniment Agent</th>
                    <th>Status</th>
                    <th>Rating & Client Feedback</th>
                    <th class="text-end">Update</th>
                </tr>
            </thead>
            <tbody>
                @forelse($siteVisits as $visit)
                    <tr>
                        <td>
                            <div class="fw-bold text-dark">{{ $visit->scheduled_at->format('d M Y') }}</div>
                            <small class="text-muted"><i class="bi bi-clock me-1"></i>{{ $visit->scheduled_at->format('h:i A') }}</small>
                        </td>
                        <td>
                            <a href="{{ route('erp.leads.show', $visit->lead) }}" class="fw-semibold text-dark text-decoration-none">
                                {{ $visit->lead->name }}
                            </a>
                            <small class="d-block text-muted">{{ $visit->lead->phone }}</small>
                        </td>
                        <td>
                            @if($visit->featuredProject)
                                <span class="badge bg-primary bg-opacity-10 text-primary">
                                    <i class="bi bi-building me-1"></i> {{ $visit->featuredProject->title }}
                                </span>
                            @elseif($visit->property)
                                <span class="badge bg-info bg-opacity-10 text-info">
                                    <i class="bi bi-house me-1"></i> {{ $visit->property->title }}
                                </span>
                            @else
                                <span class="text-muted small">&mdash;</span>
                            @endif
                        </td>
                        <td>
                            <small class="fw-medium text-dark">{{ $visit->assignedAgent?->name ?? 'Unassigned' }}</small>
                        </td>
                        <td>
                            @php
                                $col = match($visit->status) {
                                    'completed' => 'success',
                                    'scheduled' => 'warning',
                                    'cancelled' => 'danger',
                                    default     => 'secondary',
                                };
                            @endphp
                            <span class="badge bg-{{ $col }}">{{ ucfirst($visit->status) }}</span>
                        </td>
                        <td>
                            @if($visit->client_rating)
                                <div class="text-warning small">
                                    @for($i = 1; $i <= 5; $i++)
                                        <i class="bi bi-star{{ $i <= $visit->client_rating ? '-fill' : '' }}"></i>
                                    @endfor
                                </div>
                            @endif
                            <small class="text-muted">{{ Str::limit($visit->client_feedback ?? $visit->agent_remarks, 30) ?? '&mdash;' }}</small>
                        </td>
                        <td class="text-end">
                            <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#visitModal{{ $visit->id }}">
                                Outcome
                            </button>
                        </td>
                    </tr>

                    <!-- Modal: Update Visit -->
                    <div class="modal fade" id="visitModal{{ $visit->id }}" tabindex="-1">
                        <div class="modal-dialog">
                            <form action="{{ route('erp.site-visits.update', $visit) }}" method="POST">
                                @csrf
                                @method('PUT')
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h5 class="modal-title fw-bold">Site Visit Outcome: {{ $visit->lead->name }}</h5>
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
                                            <label class="form-label small fw-semibold">Client Impression Rating</label>
                                            <select name="client_rating" class="form-select">
                                                <option value="">-- No Rating --</option>
                                                <option value="5" {{ $visit->client_rating == 5 ? 'selected' : '' }}>5 Stars - Very High Intent</option>
                                                <option value="4" {{ $visit->client_rating == 4 ? 'selected' : '' }}>4 Stars - Good</option>
                                                <option value="3" {{ $visit->client_rating == 3 ? 'selected' : '' }}>3 Stars - Moderate</option>
                                                <option value="2" {{ $visit->client_rating == 2 ? 'selected' : '' }}>2 Stars - Skeptical</option>
                                                <option value="1" {{ $visit->client_rating == 1 ? 'selected' : '' }}>1 Star - Rejected</option>
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Client Feedback</label>
                                            <textarea name="client_feedback" rows="2" class="form-control">{{ $visit->client_feedback }}</textarea>
                                        </div>
                                        <div class="mb-3">
                                            <label class="form-label small fw-semibold">Agent Remarks</label>
                                            <textarea name="agent_remarks" rows="2" class="form-control">{{ $visit->agent_remarks }}</textarea>
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
                        <td colspan="7" class="text-center py-5 text-muted">
                            <i class="bi bi-geo-alt fs-2 d-block mb-2 text-secondary"></i>
                            No site visits recorded yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($siteVisits->hasPages())
        <div class="p-3 border-top">
            {{ $siteVisits->links() }}
        </div>
    @endif
</div>
@endsection

