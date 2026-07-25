@extends('backend.layouts.app')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4 page-title-row">
    <div>
        <h4 class="mb-0 fw-bold">Contact Page Settings</h4>
        <p class="text-muted small mb-0">Manage Page Header, Call-To-Action Banner, Contact Information, & Map Embed</p>
    </div>
</div>

<div class="card p-4">
    <form action="{{ route('admin.contact_settings.update') }}" method="POST">
        @csrf

        <!-- PAGE HEADER -->
        <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark"><i class="bi bi-card-heading me-2 text-warning"></i> Page Header Section</h5>
        <div class="row mb-4">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Header Title</label>
                <input type="text" class="form-control" name="contact_header_title" value="{{ $settings['contact_header_title'] ?? 'Contact Us' }}" required>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Header Subtitle</label>
                <input type="text" class="form-control" name="contact_header_subtitle" value="{{ $settings['contact_header_subtitle'] ?? 'Get in touch with our expert real estate advisors.' }}" required>
            </div>
        </div>

        <!-- CTA BANNER SECTION -->
        <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark mt-2"><i class="bi bi-megaphone me-2 text-warning"></i> Call To Action (CTA) Banner</h5>
        <div class="form-check form-switch mb-3">
            <input class="form-check-input" type="checkbox" id="contact_cta_status" name="contact_cta_status" value="1" {{ ($settings['contact_cta_status'] ?? '1') == '1' ? 'checked' : '' }}>
            <label class="form-check-label fw-bold" for="contact_cta_status">Show CTA Banner on Contact Page</label>
        </div>
        <div class="row mb-4">
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">CTA Eyebrow Tag</label>
                <input type="text" class="form-control" name="contact_cta_eyebrow" value="{{ $settings['contact_cta_eyebrow'] ?? 'Limited Units Remaining' }}">
            </div>
            <div class="col-md-8 mb-3">
                <label class="form-label fw-bold">CTA Main Title</label>
                <input type="text" class="form-control" name="contact_cta_title" value="{{ $settings['contact_cta_title'] ?? 'Ready To Invest In Your Future?' }}">
            </div>
            <div class="col-12 mb-3">
                <label class="form-label fw-bold">CTA Description / Subtitle</label>
                <textarea class="form-control" name="contact_cta_description" rows="2">{{ $settings['contact_cta_description'] ?? 'Book a free site visit or speak to our advisors today — no obligation, just clarity.' }}</textarea>
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Primary Button Text</label>
                <input type="text" class="form-control" name="contact_cta_btn_text" value="{{ $settings['contact_cta_btn_text'] ?? 'Book Site Visit' }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Primary Button Link</label>
                <input type="text" class="form-control" name="contact_cta_btn_link" value="{{ $settings['contact_cta_btn_link'] ?? '#contact' }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Call Button Text</label>
                <input type="text" class="form-control" name="contact_cta_phone_text" value="{{ $settings['contact_cta_phone_text'] ?? 'Call Now' }}">
            </div>
            <div class="col-md-3 mb-3">
                <label class="form-label fw-bold">Call Phone Number</label>
                <input type="text" class="form-control" name="contact_cta_phone" value="{{ $settings['contact_cta_phone'] ?? '+8801XXXXXXXXX' }}">
            </div>
        </div>

        <!-- CONTACT DETAILS & MAP -->
        <h5 class="fw-bold mb-3 border-bottom pb-2 text-dark mt-2"><i class="bi bi-geo-alt me-2 text-warning"></i> Contact Details & Location Map</h5>
        <div class="row mb-3">
            <div class="col-md-4 mb-3">
                <label class="form-label fw-bold">Section Eyebrow</label>
                <input type="text" class="form-control" name="contact_sec_eyebrow" value="{{ $settings['contact_sec_eyebrow'] ?? 'Contact Us' }}">
            </div>
            <div class="col-md-8 mb-3">
                <label class="form-label fw-bold">Section Main Title</label>
                <input type="text" class="form-control" name="contact_sec_title" value="{{ $settings['contact_sec_title'] ?? 'Let\'s Talk About Your Next Property' }}">
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Office Address Label</label>
                <input type="text" class="form-control mb-2" name="contact_address_label" value="{{ $settings['contact_address_label'] ?? 'Office Address' }}">
                <label class="form-label fw-bold">Office Address Value</label>
                <textarea class="form-control" name="contact_address" rows="2">{{ $settings['contact_address'] ?? 'House 12, Road 7, Gulshan-1, Dhaka 1212, Bangladesh' }}</textarea>
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Phone Number Label</label>
                <input type="text" class="form-control mb-2" name="contact_phone_label" value="{{ $settings['contact_phone_label'] ?? 'Phone' }}">
                <label class="form-label fw-bold">Phone Number Value</label>
                <input type="text" class="form-control" name="contact_phone" value="{{ $settings['contact_phone'] ?? '+880 1XXX-XXXXXX' }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Email Address Label</label>
                <input type="text" class="form-control mb-2" name="contact_email_label" value="{{ $settings['contact_email_label'] ?? 'Email' }}">
                <label class="form-label fw-bold">Email Address Value</label>
                <input type="email" class="form-control" name="contact_email" value="{{ $settings['contact_email'] ?? 'hello@aureliaestates.example' }}">
            </div>
            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Business Hours Label</label>
                <input type="text" class="form-control mb-2" name="contact_hours_label" value="{{ $settings['contact_hours_label'] ?? 'Business Hours' }}">
                <label class="form-label fw-bold">Business Hours Value</label>
                <input type="text" class="form-control" name="contact_hours" value="{{ $settings['contact_hours'] ?? 'Sat–Thu: 10:00 AM – 7:00 PM' }}">
            </div>
            <div class="col-12 mb-3">
                <label class="form-label fw-bold">Google Maps Embed Iframe URL</label>
                <input type="text" class="form-control" name="contact_map_iframe" value="{{ $settings['contact_map_iframe'] ?? 'https://www.google.com/maps?q=Gulshan%201%20Dhaka&output=embed' }}">
                <small class="text-muted">Enter the embed URL (e.g. https://www.google.com/maps?q=...) for Google Maps iframe.</small>
            </div>
        </div>

        <div class="text-end mt-4">
            <button type="submit" class="btn btn-gold px-4"><i class="bi bi-check-lg me-1"></i> Save Contact Settings</button>
        </div>
    </form>
</div>
@endsection
