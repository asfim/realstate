@extends('erp.layouts.app')

@section('content')
<div class="d-flex align-items-center justify-content-between mb-4">
    <div>
        <h4 class="fw-bold mb-1 text-dark">Register New Lead</h4>
        <p class="text-muted small mb-0">Direct customer inquiry intake, requirement capture, and sales agent allocation.</p>
    </div>
    <a href="{{ route('erp.leads.index') }}" class="btn btn-outline-secondary btn-sm">
        <i class="bi bi-arrow-left me-1"></i> Back to Pipeline
    </a>
</div>

<div class="card card-custom">
    <div class="card-body p-4">
        <form action="{{ route('erp.leads.store') }}" method="POST">
            @csrf
            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-person me-2 text-warning"></i>Lead Contact Information</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Full Client Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Mahfuzur Rahman" value="{{ old('name') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Primary Phone <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" placeholder="e.g. +880 1711 000000" value="{{ old('phone') }}" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Email Address</label>
                    <input type="email" name="email" class="form-control" placeholder="e.g. client@example.com" value="{{ old('email') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Secondary / WhatsApp Phone</label>
                    <input type="text" name="secondary_phone" class="form-control" placeholder="e.g. +880 1819 000000" value="{{ old('secondary_phone') }}">
                </div>
            </div>

            <h6 class="fw-bold text-dark border-bottom pb-2 mb-3"><i class="bi bi-funnel me-2 text-warning"></i>Pipeline & Assignment</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Lead Source <span class="text-danger">*</span></label>
                    <select name="source" class="form-select" required>
                        <option value="walk_in" {{ old('source') === 'walk_in' ? 'selected' : '' }}>Office Walk-in</option>
                        <option value="phone_call" {{ old('source') === 'phone_call' ? 'selected' : '' }}>Direct Phone Call</option>
                        <option value="web_form" {{ old('source') === 'web_form' ? 'selected' : '' }}>Website Form</option>
                        <option value="social_media" {{ old('source') === 'social_media' ? 'selected' : '' }}>Social Media / Meta Ads</option>
                        <option value="agent_referral" {{ old('source') === 'agent_referral' ? 'selected' : '' }}>Agent Referral</option>
                        <option value="newspaper_ad" {{ old('source') === 'newspaper_ad' ? 'selected' : '' }}>Newspaper / Billboard</option>
                        <option value="other" {{ old('source') === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Initial Stage <span class="text-danger">*</span></label>
                    <select name="stage" class="form-select" required>
                        <option value="new" selected>New</option>
                        <option value="contacted">Contacted</option>
                        <option value="qualified">Qualified</option>
                        <option value="site_visit_scheduled">Site Visit Scheduled</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Priority Level <span class="text-danger">*</span></label>
                    <select name="priority" class="form-select" required>
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                        <option value="urgent">Urgent</option>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Operating Branch</label>
                    <select name="branch_id" class="form-select">
                        <option value="">-- Auto Company Context --</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Assigned Sales Agent</label>
                    <select name="assigned_agent_id" class="form-select">
                        <option value="">-- Auto Assign (Round-Robin) --</option>
                        @foreach($agents as $agent)
                            <option value="{{ $agent->id }}" {{ old('assigned_agent_id') == $agent->id ? 'selected' : '' }}>{{ $agent->name }} ({{ $agent->email }})</option>
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
                        <option value="Apartment">Apartment / Flat</option>
                        <option value="Commercial Space">Commercial / Office Space</option>
                        <option value="Land / Plot">Land / Plot</option>
                        <option value="Duplex / Villa">Duplex / Villa</option>
                        <option value="Warehouse">Warehouse</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Interested Project</label>
                    <select name="featured_project_id" class="form-select">
                        <option value="">-- Select Project (Optional) --</option>
                        @foreach($projects as $p)
                            <option value="{{ $p->id }}" {{ old('featured_project_id') == $p->id ? 'selected' : '' }}>{{ $p->title }} ({{ $p->location }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold small">Specific Property / Unit</label>
                    <select name="property_id" class="form-select">
                        <option value="">-- Select Property (Optional) --</option>
                        @foreach($properties as $prop)
                            <option value="{{ $prop->id }}" {{ old('property_id') == $prop->id ? 'selected' : '' }}>{{ $prop->title }} - BDT {{ number_format($prop->price) }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Minimum Budget (BDT)</label>
                    <input type="number" step="0.01" name="min_budget" class="form-control" placeholder="e.g. 10000000" value="{{ old('min_budget') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Maximum Budget (BDT)</label>
                    <input type="number" step="0.01" name="max_budget" class="form-control" placeholder="e.g. 25000000" value="{{ old('max_budget') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Preferred Location / Area</label>
                    <input type="text" name="preferred_location" class="form-control" placeholder="e.g. Gulshan, Banani, Uttara" value="{{ old('preferred_location') }}">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold small">Preferred Size / Sq.Ft</label>
                    <input type="text" name="preferred_size_sqft" class="form-control" placeholder="e.g. 1800 - 2400 sq.ft." value="{{ old('preferred_size_sqft') }}">
                </div>

                <div class="col-md-6">
                    <label class="form-label fw-semibold small">First Follow-up Scheduled Date</label>
                    <input type="datetime-local" name="next_follow_up_at" class="form-control" value="{{ old('next_follow_up_at') }}">
                </div>
                <div class="col-12">
                    <label class="form-label fw-semibold small">Detailed Client Notes / Requirements</label>
                    <textarea name="notes" rows="3" class="form-control" placeholder="Specific requirements, financing plan, family details...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <div class="pt-3 border-top d-flex justify-content-end gap-2">
                <a href="{{ route('erp.leads.index') }}" class="btn btn-light px-4">Cancel</a>
                <button type="submit" class="btn btn-erp-gold px-4">Register Lead</button>
            </div>
        </form>
    </div>
</div>
@endsection

