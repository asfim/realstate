@extends('frontend.layouts.app')
@section('content')

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="container">
    <h1 data-aos="fade-up">Contact Us</h1>
    <p data-aos="fade-up" data-aos-delay="100">Get in touch with our expert real estate advisors.</p>
  </div>
</div>
<!-- CTA -->
<section>
  <div class="container">
    <div class="cta-banner text-center" data-aos="zoom-in">
      <span class="eyebrow" style="color:var(--gold-soft);"><i></i>Limited Units Remaining</span>
      <h2>Ready To Invest In Your Future?</h2>
      <p class="mb-4" style="color:rgba(255,255,255,.8);">Book a free site visit or speak to our advisors today — no obligation, just clarity.</p>
      <div class="d-flex justify-content-center gap-3 flex-wrap">
        <a href="#contact" class="btn btn-gold">Book Site Visit</a>
        <a href="tel:+8801XXXXXXXXX" class="btn btn-outline-light-glass"><i class="fa-solid fa-phone me-2"></i>Call Now</a>
      </div>
    </div>
  </div>
</section>

<!-- CONTACT -->
<section id="contact">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Contact Us</span>
      <h2 class="section-title">Let's Talk About Your Next Property</h2>
    </div>
    <div class="row g-4">
      <div class="col-lg-5" data-aos="fade-right">
        <div class="contact-info-item"><i class="fa-solid fa-location-dot"></i><div><h6>Office Address</h6><p>House 12, Road 7, Gulshan-1, Dhaka 1212, Bangladesh</p></div></div>
        <div class="contact-info-item"><i class="fa-solid fa-phone"></i><div><h6>Phone</h6><p>+880 1XXX-XXXXXX</p></div></div>
        <div class="contact-info-item"><i class="fa-solid fa-envelope"></i><div><h6>Email</h6><p>hello@aureliaestates.example</p></div></div>
        <div class="contact-info-item"><i class="fa-solid fa-clock"></i><div><h6>Business Hours</h6><p>Sat–Thu: 10:00 AM – 7:00 PM</p></div></div>
        <div class="map-wrap mt-4">
          <iframe src="https://www.google.com/maps?q=Gulshan%201%20Dhaka&output=embed" width="100%" height="260" style="border:0;" loading="lazy" title="Office location map"></iframe>
        </div>
      </div>
      <div class="col-lg-7" data-aos="fade-left">
        <div class="glass contact-form-glass">
          <form id="contactForm" onsubmit="return handleContactSubmit(event);">
            <div class="row g-3">
              <div class="col-md-6"><input type="text" class="form-control" placeholder="Full Name" required></div>
              <div class="col-md-6"><input type="tel" class="form-control" placeholder="Phone Number" required></div>
              <div class="col-md-6"><input type="email" class="form-control" placeholder="Email Address" required></div>
              <div class="col-md-6"><select class="form-select"><option>Property Type</option><option>Apartment</option><option>Villa</option><option>Duplex</option><option>Commercial</option><option>Office</option><option>Land</option></select></div>
              <div class="col-12"><select class="form-select"><option>Budget Range</option><option>Under $200k</option><option>$200k–$500k</option><option>$500k+</option></select></div>
              <div class="col-12"><textarea class="form-control" rows="4" placeholder="Message"></textarea></div>
              <div class="col-12 form-check ps-4">
                <input class="form-check-input" type="checkbox" id="agreeCheck" required>
                <label class="form-check-label" for="agreeCheck" style="font-size:.86rem;">I agree to be contacted regarding my enquiry.</label>
              </div>
              <div class="col-12"><button class="btn btn-gold w-100" type="submit">Send Enquiry</button></div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

@endsection
