@extends('frontend.layouts.app')
@section('content')

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="container">
    <h1 data-aos="fade-up">About Aurelia Estates</h1>
    <p data-aos="fade-up" data-aos-delay="100">Two decades of excellence in luxury real estate.</p>
  </div>
</div>
<!-- ABOUT -->
<section id="about">
  <div class="blueprint-bg"></div>
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6" data-aos="fade-right">
        <div class="about-img-wrap">
          <img src="https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?auto=format&fit=crop&w=900&q=75" class="rounded-24" loading="lazy" alt="Aurelia Estates office and showroom">
          <div class="glass about-badge-card"><strong>20+</strong>Years shaping city skylines</div>
        </div>
      </div>
      <div class="col-lg-6" data-aos="fade-left">
        <span class="eyebrow">About Aurelia Estates</span>
        <h2 class="section-title">Building More Than Properties — Building Trust</h2>
        <p class="mt-3">Since 2010, Aurelia Estates has delivered over 500 residential and commercial projects across the country, guided by one principle: every buyer deserves clarity, quality, and a home worth the investment.</p>
        <div class="row g-3 my-3">
          <div class="col-6"><h6 class="fw-bold mb-1"><i class="fa-solid fa-bullseye text-primary me-2"></i>Our Mission</h6><p style="font-size:.9rem;">Make premium real estate accessible through transparent, verified transactions.</p></div>
          <div class="col-6"><h6 class="fw-bold mb-1"><i class="fa-solid fa-eye text-primary me-2"></i>Our Vision</h6><p style="font-size:.9rem;">To be the most trusted name in luxury real estate development nationwide.</p></div>
        </div>
        <div class="row g-3 text-center">
          <div class="col-4"><strong class="d-block fs-4 fw-bold" data-count="500">0</strong><span style="font-size:.8rem;color:var(--ink-soft);">Projects</span></div>
          <div class="col-4"><strong class="d-block fs-4 fw-bold" data-count="12000">0</strong><span style="font-size:.8rem;color:var(--ink-soft);">Customers</span></div>
          <div class="col-4"><strong class="d-block fs-4 fw-bold" data-count="98" data-suffix="%">0</strong><span style="font-size:.8rem;color:var(--ink-soft);">Satisfaction</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- JOURNEY -->
<section style="background:linear-gradient(180deg,transparent,rgba(212,175,55,.06),transparent);">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Our Journey</span>
      <h2 class="section-title">Two Decades Of Milestones</h2>
    </div>
    <div class="journey-line">
      <div class="journey-item" data-aos="fade-right"><div class="journey-dot"></div><div class="journey-year">2010</div><h5>Company Founded</h5><p>Aurelia Estates began with a single residential project in Dhaka.</p></div>
      <div class="journey-item" data-aos="fade-left"><div class="journey-dot"></div><div class="journey-year">2015</div><h5>Regional Expansion</h5><p>Expanded operations to Chattogram and Sylhet with 50+ completed units.</p></div>
      <div class="journey-item" data-aos="fade-right"><div class="journey-dot"></div><div class="journey-year">2020</div><h5>National Projects</h5><p>Launched large-scale mixed-use developments across 6 major cities.</p></div>
      <div class="journey-item" data-aos="fade-left"><div class="journey-dot"></div><div class="journey-year">2026</div><h5>Industry Leader</h5><p>Recognised as a top-3 luxury developer with 500+ properties delivered.</p></div>
    </div>
  </div>
</section>

<!-- TEAM -->
<section style="background:linear-gradient(180deg,transparent,rgba(37,99,235,.05),transparent);">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Our Team</span>
      <h2 class="section-title">The People Behind Aurelia</h2>
    </div>
    <div class="row g-4">
      <div class="col-lg-3 col-md-6" data-aos="fade-up">
        <div class="team-card"><div class="team-img-wrap"><img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=500&q=75" loading="lazy" alt="CEO portrait"><div class="team-social"><a href="#"><i class="fa-brands fa-linkedin-in"></i></a><a href="#"><i class="fa-solid fa-envelope"></i></a></div></div><div class="team-body"><h5>Rashed Karim</h5><span>Chief Executive Officer</span></div></div>
      </div>
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="80">
        <div class="team-card"><div class="team-img-wrap"><img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=500&q=75" loading="lazy" alt="Sales Manager portrait"><div class="team-social"><a href="#"><i class="fa-brands fa-linkedin-in"></i></a><a href="#"><i class="fa-solid fa-envelope"></i></a></div></div><div class="team-body"><h5>Nusrat Jahan</h5><span>Sales Manager</span></div></div>
      </div>
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="160">
        <div class="team-card"><div class="team-img-wrap"><img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=500&q=75" loading="lazy" alt="Architect portrait"><div class="team-social"><a href="#"><i class="fa-brands fa-linkedin-in"></i></a><a href="#"><i class="fa-solid fa-envelope"></i></a></div></div><div class="team-body"><h5>Tanvir Ahmed</h5><span>Lead Architect</span></div></div>
      </div>
      <div class="col-lg-3 col-md-6" data-aos="fade-up">
        <div class="team-card"><div class="team-img-wrap"><img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=500&q=75" loading="lazy" alt="Engineer portrait"><div class="team-social"><a href="#"><i class="fa-brands fa-linkedin-in"></i></a><a href="#"><i class="fa-solid fa-envelope"></i></a></div></div><div class="team-body"><h5>Farhana Rahman</h5><span>Civil Engineer</span></div></div>
      </div>
    </div>
  </div>
</section>

<!-- STATS -->
<section class="stats-section">
  <div class="container">
    <div class="row g-4">
      <div class="col-6 col-md-3" data-aos="fade-up"><div class="stat-block"><strong data-count="500" data-suffix="+">0</strong><span>Projects</span></div></div>
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="80"><div class="stat-block"><strong data-count="12000" data-suffix="+">0</strong><span>Customers</span></div></div>
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="160"><div class="stat-block"><strong data-count="350" data-suffix="+">0</strong><span>Employees</span></div></div>
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="240"><div class="stat-block"><strong data-count="98" data-suffix="%">0</strong><span>Client Satisfaction</span></div></div>
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
