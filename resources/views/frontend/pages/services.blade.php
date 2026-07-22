@extends('frontend.layouts.app')
@section('content')

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="container">
    <h1 data-aos="fade-up">Our Services</h1>
    <p data-aos="fade-up" data-aos-delay="100">Comprehensive real estate services tailored for you.</p>
  </div>
</div>
<!-- WHY CHOOSE US -->
<section id="why-us" style="background:linear-gradient(180deg,transparent,rgba(37,99,235,.05),transparent);">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Why Choose Us</span>
      <h2 class="section-title">Built On Trust, Delivered With Precision</h2>
    </div>
    <div class="row g-4">
      <div class="col-lg-4 col-md-6" data-aos="fade-up"><div class="glass feature-icon-card"><div class="feature-icon"><i class="fa-solid fa-building-shield"></i></div><h5>Trusted Company</h5><p>Two decades of on-time delivery across residential and commercial developments.</p></div></div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="80"><div class="glass feature-icon-card"><div class="feature-icon"><i class="fa-solid fa-file-circle-check"></i></div><h5>Verified Property</h5><p>Every listing is title-checked and verified before it reaches our catalogue.</p></div></div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="160"><div class="glass feature-icon-card"><div class="feature-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div><h5>Easy Installment</h5><p>Flexible 3 to 5 year installment plans tailored to your budget.</p></div></div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up"><div class="glass feature-icon-card"><div class="feature-icon"><i class="fa-solid fa-scale-balanced"></i></div><h5>Legal Support</h5><p>Dedicated legal team handling registration, mutation and paperwork end to end.</p></div></div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="80"><div class="glass feature-icon-card"><div class="feature-icon"><i class="fa-solid fa-city"></i></div><h5>Premium Locations</h5><p>Developments placed in the most connected and high-growth neighbourhoods.</p></div></div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="160"><div class="glass feature-icon-card"><div class="feature-icon"><i class="fa-solid fa-shield-halved"></i></div><h5>Investment Security</h5><p>Transparent contracts and escrow-backed payments protect every buyer.</p></div></div>
    </div>
  </div>
</section>

<!-- AMENITIES -->
<section style="background:linear-gradient(180deg,transparent,rgba(37,99,235,.05),transparent);">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Amenities</span>
      <h2 class="section-title">Everything Your New Home Needs</h2>
    </div>
    <div class="row g-2 text-center">
      <div class="col-6 col-md-2" data-aos="fade-up"><div class="amenity-item"><i class="fa-solid fa-person-swimming"></i><span>Swimming Pool</span></div></div>
      <div class="col-6 col-md-2" data-aos="fade-up" data-aos-delay="40"><div class="amenity-item"><i class="fa-solid fa-dumbbell"></i><span>Gym</span></div></div>
      <div class="col-6 col-md-2" data-aos="fade-up" data-aos-delay="80"><div class="amenity-item"><i class="fa-solid fa-child-reaching"></i><span>Kids Zone</span></div></div>
      <div class="col-6 col-md-2" data-aos="fade-up" data-aos-delay="120"><div class="amenity-item"><i class="fa-solid fa-mosque"></i><span>Mosque</span></div></div>
      <div class="col-6 col-md-2" data-aos="fade-up" data-aos-delay="160"><div class="amenity-item"><i class="fa-solid fa-tree"></i><span>Landscape Garden</span></div></div>
      <div class="col-6 col-md-2" data-aos="fade-up" data-aos-delay="200"><div class="amenity-item"><i class="fa-solid fa-square-parking"></i><span>Parking</span></div></div>
      <div class="col-6 col-md-2" data-aos="fade-up"><div class="amenity-item"><i class="fa-solid fa-elevator"></i><span>Lift</span></div></div>
      <div class="col-6 col-md-2" data-aos="fade-up" data-aos-delay="40"><div class="amenity-item"><i class="fa-solid fa-plug"></i><span>Generator</span></div></div>
      <div class="col-6 col-md-2" data-aos="fade-up" data-aos-delay="80"><div class="amenity-item"><i class="fa-solid fa-video"></i><span>CCTV</span></div></div>
      <div class="col-6 col-md-2" data-aos="fade-up" data-aos-delay="120"><div class="amenity-item"><i class="fa-solid fa-user-shield"></i><span>24/7 Security</span></div></div>
    </div>
  </div>
</section>


<!-- GENERIC CTA -->
<section style="padding:60px 0;">
  <div class="container">
    <div class="cta-banner" data-aos="zoom-in">
      <h2>Ready to find your dream property?</h2>
      <p style="font-size:1.1rem; margin-bottom:30px;">Our real estate experts are here to guide you every step of the way.</p>
      <a href="{{ route('contact') }}" class="btn btn-gold">Contact Us Today</a>
    </div>
  </div>
</section>
@endsection
