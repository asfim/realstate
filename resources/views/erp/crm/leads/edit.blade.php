@extends('erp.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Edit Lead: {{ $lead->lead_code }}</h4>
        <p class="text-muted small mb-0">Update contact parameters, stage, allocation, or requirement criteria.</p>
    </div>
    <a href="{{ route('erp.leads.show', $lead) }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Dossier
    </a>
</div>

<div class="card card-custom">
    <div class="card-body p-4">
        <form action="{{ route('erp.leads.update', $lead) }}" method="POST">
            @csrf
            @method('PUT')
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-person me-2 text-warning"></i>Lead Contact Information</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Full Client Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name', $lead->name) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Primary Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" value="{{ old('phone', $lead->phone) }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Email Address</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email', $lead->email) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Secondary / WhatsApp Phone</label>
                    <input type="text" name="secondary_phone" class="form-control" value="{{ old('secondary_phone', $lead->secondary_phone) }}">
                </div>
            </div>

            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-funnel me-2 text-warning"></i>Pipeline & Assignment</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Lead Source <span class="text-danger">*</span></label>
                    <select name="source" class="form-select" required>
                        <option value="walk_in" {{ old('source', $lead->source) === 'walk_in' ? 'selected' : '' }}>Office Walk-in</option>
                        <option value="phone_call" {{ old('source', $lead->source) === 'phone_call' ? 'selected' : '' }}>Direct Phone Call</option>
                        <option value="web_form" {{ old('source', $lead->source) === 'web_form' ? 'selected' : '' }}>Website Form</option>
                        <option value="social_media" {{ old('source', $lead->source) === 'social_media' ? 'selected' : '' }}>Social Media / Meta Ads</option>
                        <option value="agent_referral" {{ old('source', $lead->source) === 'agent_referral' ? 'selected' : '' }}>Agent Referral</option>
                        <option value="newspaper_ad" {{ old('source', $lead->source) === 'newspaper_ad' ? 'selected' : '' }}>Newspaper / Billboard</option>
                        <option value="other" {{ old('source', $lead->source) === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Pipeline Stage <span class="text-danger">*</span></label>
                    <select name="stage" class="form-select" required>
                        <option value="new" {{ old('stage', $lead->stage) === 'new' ? 'selected' : '' }}>New</option>
                        <option value="contacted" {{ old('stage', $lead->stage) === 'contacted' ? 'selected' : '' }}>Contacted</option>
                        <option value="qualified" {{ old('stage', $lead->stage) === 'qualified' ? 'selected' : '' }}>Qualified</option>
                        <option value="site_visit_scheduled" {{ old('stage', $lead->stage) === 'site_visit_scheduled' ? 'selected' : '' }}>Site Visit Scheduled</option>
                        <option value="site_visit_completed" {{ old('stage', $lead->stage) === 'site_visit_completed' ? 'selected' : '' }}>Site Visit Completed</option>
                        <option value="negotiation" {{ old('stage', $lead->stage) === 'negotiation' ? 'selected' : '' }}>Negotiation</option>
                        <option value="booked" {{ old('stage', $lead->stage) === 'booked' ? 'selected' : '' }}>Won / Booked</option>
                        <option value="lost" {{ old('stage', $lead->stage) === 'lost' ? 'selected' : '' }}>Lost / Cold</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Priority Level <span class="text-danger">*</span></label>
                    <select name="priority" class="form-select" required>
                        <option value="low" {{ old('priority', $lead->priority) === 'low' ? 'selected' : '' }}>Low</option>
                        <option value="medium" {{ old('priority', $lead->priority) === 'medium' ? 'selected' : '' }}>Medium</option>
                        <option value="high" {{ old('priority', $lead->priority) === 'high' ? 'selected' : '' }}>High</option>
                        <option value="urgent" {{ old('priority', $lead->priority) === 'urgent' ? 'selected' : '' }}>Urgent</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Operating Branch</label>
                    <select name="branch_id" class="form-select">
                        <option value="">-- No Branch Allocation --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $lead->branch_id) == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Assigned Sales Agent</label>
                    <select name="assigned_agent_id" class="form-select">
                        <option value="">-- Unassigned --</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ old('assigned_agent_id', $lead->assigned_agent_id) == $agent->id ? 'selected' : '' }}>{{ $agent->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-buildings me-2 text-warning"></i>Property Interest & Budget</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Property Category / Type</label>
                    <select name="property_type_interest" class="form-select">
                        <option value="">-- Select Type --</option>
                        <option value="Apartment" {{ old('property_type_interest', $lead->property_type_interest) === 'Apartment' ? 'selected' : '' }}>Apartment / Flat</option>
                        <option value="Commercial Space" {{ old('property_type_interest', $lead->property_type_interest) === 'Commercial Space' ? 'selected' : '' }}>Commercial / Office Space</option>
                        <option value="Land / Plot" {{ old('property_type_interest', $lead->property_type_interest) === 'Land / Plot' ? 'selected' : '' }}>Land / Plot</option>
                        <option value="Duplex / Villa" {{ old('property_type_interest', $lead->property_type_interest) === 'Duplex / Villa' ? 'selected' : '' }}>Duplex / Villa</option>
                        <option value="Warehouse" {{ old('property_type_interest', $lead->property_type_interest) === 'Warehouse' ? 'selected' : '' }}>Warehouse</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Interested Project</label>
                    <select name="featured_project_id" class="form-select">
                        <option value="">-- Select Project (Optional) --</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ old('featured_project_id', $lead->featured_project_id) == $p->id ? 'selected' : '' }}>{{ $p->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Specific Property / Unit</label>
                    <select name="property_id" class="form-select">
                        <option value="">-- Select Property (Optional) --</option>
                        @foreach($properties as $prop)
                            <option value="{{ $prop->id }}" {{ old('property_id', $lead->property_id) == $prop->id ? 'selected' : '' }}>{{ $prop->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Minimum Budget (BDT)</label>
                    <input type="number" step="0.01" name="min_budget" class="form-control" value="{{ old('min_budget', $lead->min_budget) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Maximum Budget (BDT)</label>
                    <input type="number" step="0.01" name="max_budget" class="form-control" value="{{ old('max_budget', $lead->max_budget) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Preferred Location / Area</label>
                    <input type="text" name="preferred_location" class="form-control" value="{{ old('preferred_location', $lead->preferred_location) }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Preferred Size / Sq.Ft</label>
                    <input type="text" name="preferred_size_sqft" class="form-control" value="{{ old('preferred_size_sqft', $lead->preferred_size_sqft) }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Next Follow-up Scheduled Date</label>
                    <input type="datetime-local" name="next_follow_up_at" class="form-control" value="{{ old('next_follow_up_at', $lead->next_follow_up_at ? $lead->next_follow_up_at->format('Y-m-d\TH:i') : '') }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small">Detailed Client Notes / Requirements</label>
                    <textarea name="notes" rows="3" class="form-control">{{ old('notes', $lead->notes) }}</textarea>
                </div>
            </div>

            <div class="pt-3 border-top d-flex justify-content-end gap-2">
                <a href="{{ route('erp.leads.show', $lead) }}" class="btn btn-light px-4">Cancel</a>
                <button type="submit" class="btn btn-erp-gold px-4">Update Lead</button>
            </div>
        </form>
    </div>
</div>
@endsection
