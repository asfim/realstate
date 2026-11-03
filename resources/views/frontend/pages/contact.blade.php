@extends('frontend.layouts.app')
@section('content')

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="container">
    <h1 data-aos="fade-up">{{ setting('contact_header_title', 'Contact Us') }}</h1>
    <p data-aos="fade-up" data-aos-delay="100">{{ setting('contact_header_subtitle', 'Get in touch with our expert real estate advisors.') }}</p>
  </div>
</div>

<!-- CTA -->
@if(setting('contact_cta_status', '1') == '1')
<section>
  <div class="container">
    <div class="cta-banner text-center" data-aos="zoom-in">
      @if(setting('contact_cta_eyebrow'))
        <span class="eyebrow" style="color:var(--gold-soft);"><i></i>{{ setting('contact_cta_eyebrow', 'Limited Units Remaining') }}</span>
      @endif
      <h2>{{ setting('contact_cta_title', 'Ready To Invest In Your Future?') }}</h2>
      <p class="mb-4" style="color:rgba(255,255,255,.8);">{{ setting('contact_cta_description', 'Book a free site visit or speak to our advisors today — no obligation, just clarity.') }}</p>
      <div class="d-flex justify-content-center gap-3 flex-wrap">
        @if(setting('contact_cta_btn_text'))
          <a href="{{ setting('contact_cta_btn_link', '#contact') }}" class="btn btn-gold">{{ setting('contact_cta_btn_text', 'Book Site Visit') }}</a>
        @endif
        @if(setting('contact_cta_phone_text'))
          <a href="tel:{{ setting('contact_cta_phone', '+8801XXXXXXXXX') }}" class="btn btn-outline-light-glass"><i class="fa-solid fa-phone me-2"></i>{{ setting('contact_cta_phone_text', 'Call Now') }}</a>
        @endif
      </div>
    </div>
  </div>
</section>
@endif

<!-- CONTACT -->
<section id="contact">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      @if(setting('contact_sec_eyebrow'))
        <span class="eyebrow">{{ setting('contact_sec_eyebrow', 'Contact Us') }}</span>
      @endif
      <h2 class="section-title">{{ setting('contact_sec_title', "Let's Talk About Your Next Property") }}</h2>
    </div>
    <div class="row g-4">
      <div class="col-lg-5" data-aos="fade-right">
        @if(setting('contact_address'))
          <div class="contact-info-item"><i class="fa-solid fa-location-dot"></i><div><h6>{{ setting('contact_address_label', 'Office Address') }}</h6><p>{{ setting('contact_address', 'House 12, Road 7, Gulshan-1, Dhaka 1212, Bangladesh') }}</p></div></div>
        @endif
        @if(setting('contact_phone'))
          <div class="contact-info-item"><i class="fa-solid fa-phone"></i><div><h6>{{ setting('contact_phone_label', 'Phone') }}</h6><p><a href="tel:{{ setting('contact_phone', '+880 1XXX-XXXXXX') }}" style="color:inherit;text-decoration:none;">{{ setting('contact_phone', '+880 1XXX-XXXXXX') }}</a></p></div></div>
        @endif
        @if(setting('contact_email'))
          <div class="contact-info-item"><i class="fa-solid fa-envelope"></i><div><h6>{{ setting('contact_email_label', 'Email') }}</h6><p><a href="mailto:{{ setting('contact_email', 'hello@aureliaestates.example') }}" style="color:inherit;text-decoration:none;">{{ setting('contact_email', 'hello@aureliaestates.example') }}</a></p></div></div>
        @endif
        @if(setting('contact_hours'))
          <div class="contact-info-item"><i class="fa-solid fa-clock"></i><div><h6>{{ setting('contact_hours_label', 'Business Hours') }}</h6><p>{{ setting('contact_hours', 'Sat–Thu: 10:00 AM – 7:00 PM') }}</p></div></div>
        @endif
        @if(setting('contact_map_iframe'))
          <div class="map-wrap mt-4">
            <iframe src="{{ setting('contact_map_iframe', 'https://www.google.com/maps?q=Gulshan%201%20Dhaka&output=embed') }}" width="100%" height="260" style="border:0;" loading="lazy" title="Office location map"></iframe>
          </div>
        @endif
      </div>
      <div class="col-lg-7" data-aos="fade-left">
        <div class="glass contact-form-glass">
          <div id="contactAlert" class="alert d-none mb-3"></div>
          <form id="contactForm">
            @csrf
            <div class="row g-3">
              <div class="col-md-6"><input type="text" name="name" class="form-control" placeholder="Full Name *" required></div>
              <div class="col-md-6"><input type="tel" name="phone" class="form-control" placeholder="Phone Number *" required></div>
              <div class="col-md-6"><input type="email" name="email" class="form-control" placeholder="Email Address *" required></div>
              <div class="col-md-6">
                <select name="property_type" class="form-select">
                  <option value="">Property Type</option>
                  @if(isset($propertyTypes) && count($propertyTypes) > 0)
                    @foreach($propertyTypes as $type)
                      <option value="{{ $type->title ?? $type->name }}">{{ $type->title ?? $type->name }}</option>
                    @endforeach
                  @else
                    <option value="Apartment">Apartment</option>
                    <option value="Villa">Villa</option>
                    <option value="Duplex">Duplex</option>
                    <option value="Commercial">Commercial</option>
                    <option value="Office">Office</option>
                    <option value="Land">Land</option>
                  @endif
                </select>
              </div>
              <div class="col-12">
                <select name="budget" class="form-select">
                  <option value="">Budget Range</option>
                  <option value="Under $200k">Under $200k</option>
                  <option value="$200k–$500k">$200k–$500k</option>
                  <option value="$500k+">$500k+</option>
                </select>
              </div>
              <div class="col-12"><textarea name="message" class="form-control" rows="4" placeholder="Message"></textarea></div>
              <div class="col-12 form-check ps-4">
                <input class="form-check-input" type="checkbox" id="agreeCheck" required>
                <label class="form-check-label" for="agreeCheck" style="font-size:.86rem;">I agree to be contacted regarding my enquiry.</label>
              </div>
              <div class="col-12"><button class="btn btn-gold w-100" type="submit" id="submitBtn">Send Enquiry</button></div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection

@push('scripts')
<script>
document.getElementById('contactForm')?.addEventListener('submit', function(e) {
  e.preventDefault();
  const form = this;
  const btn = document.getElementById('submitBtn');
  const alertBox = document.getElementById('contactAlert');
  
  const origText = btn.innerHTML;
  btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin me-2"></i> Sending...';
  btn.disabled = true;
  alertBox.className = 'alert d-none mb-3';

  const formData = new FormData(form);
  let extraInfo = [];
  if (formData.get('property_type')) extraInfo.push('Property Type: ' + formData.get('property_type'));
  if (formData.get('budget')) extraInfo.push('Budget: ' + formData.get('budget'));
  
  let msg = formData.get('message') || '';
  if (extraInfo.length > 0) {
    msg = (extraInfo.join(' | ')) + (msg ? '\nDetails: ' + msg : '');
  }
  formData.set('message', msg);

  fetch('{{ route("enquiry.submit") }}', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    },
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    btn.innerHTML = origText;
    btn.disabled = false;
    if (data.success) {
      alertBox.className = 'alert alert-success mb-3';
      alertBox.textContent = data.message || 'Thank you! Your enquiry has been submitted successfully.';
      form.reset();
    } else {
      alertBox.className = 'alert alert-danger mb-3';
      alertBox.textContent = data.message || 'Something went wrong. Please try again.';
    }
  })
  .catch(err => {
    btn.innerHTML = origText;
    btn.disabled = false;
    alertBox.className = 'alert alert-danger mb-3';
    alertBox.textContent = 'Submission failed. Please check your connection and try again.';
  });
});
</script>
@endpush
