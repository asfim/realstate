@extends('frontend.layouts.app')
@section('content')

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="container">
    <h1 data-aos="fade-up">Signature Projects</h1>
    <p data-aos="fade-up" data-aos-delay="100">Explore our portfolio of premium real estate developments.</p>
  </div>
</div>
<!-- FEATURED PROJECTS -->
<section id="projects">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Featured Projects</span>
      <h2 class="section-title">Signature Developments</h2>
      <p class="mx-auto" style="max-width:560px;">A curated selection of our most sought-after residential and commercial developments.</p>
    </div>
    <div class="row g-4">
      <div class="col-lg-4 col-md-6" data-aos="fade-up">
        <div class="project-card">
          <div class="project-img-wrap"><span class="project-badge">New Launch</span><img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=75" loading="lazy" alt="Aurelia Heights apartment tower"></div>
          <div class="project-body">
            <h4>Aurelia Heights</h4>
            <div class="project-loc"><i class="fa-solid fa-location-dot text-primary"></i> Gulshan, Dhaka</div>
            <div class="project-meta">
              <div><strong>1,850 sqft</strong>Area</div>
              <div><strong>24 Units</strong>Available</div>
              <div class="project-price">$340k</div>
            </div>
            <a href="#contact" class="btn btn-navy w-100 mt-3">View Details</a>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="project-card">
          <div class="project-img-wrap"><span class="project-badge">Best Seller</span><img src="https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=800&q=75" loading="lazy" alt="The Meridian villa"></div>
          <div class="project-body">
            <h4>The Meridian Villas</h4>
            <div class="project-loc"><i class="fa-solid fa-location-dot text-primary"></i> Bashundhara, Dhaka</div>
            <div class="project-meta">
              <div><strong>4,200 sqft</strong>Area</div>
              <div><strong>8 Units</strong>Available</div>
              <div class="project-price">$780k</div>
            </div>
            <a href="#contact" class="btn btn-navy w-100 mt-3">View Details</a>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="project-card">
          <div class="project-img-wrap"><span class="project-badge">Featured</span><img src="https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=800&q=75" loading="lazy" alt="Skyline Business Bay tower"></div>
          <div class="project-body">
            <h4>Skyline Business Bay</h4>
            <div class="project-loc"><i class="fa-solid fa-location-dot text-primary"></i> Motijheel, Dhaka</div>
            <div class="project-meta">
              <div><strong>2,600 sqft</strong>Area</div>
              <div><strong>16 Units</strong>Available</div>
              <div class="project-price">$510k</div>
            </div>
            <a href="#contact" class="btn btn-navy w-100 mt-3">View Details</a>
          </div>
        </div>
      </div>
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
