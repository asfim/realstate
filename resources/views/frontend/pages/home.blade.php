@extends('frontend.layouts.app')
@section('content')
<!-- HERO -->
@if(setting('hero_status', '1') == '1')
<section class="hero" id="hero">
  @if(setting('hero_bg_image'))
  <div class="hero-bg" style="background-image: url('{{ Storage::url(setting('hero_bg_image')) }}'); background-size: cover; background-position: center; filter: brightness(0.6);"></div>
  @else
  <div class="hero-bg"></div>
  <video class="hero-video" id="heroVideo" autoplay muted loop playsinline poster="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1600&q=75">
    <source src="https://assets.mixkit.co/videos/preview/mixkit-modern-apartment-with-elegant-architecture-39750-large.mp4" type="video/mp4">
  </video>
  @endif
  <div class="hero-overlay"></div>
  <div class="floating-shape fs1"></div>
  <div class="floating-shape fs2"></div>

  <div class="container hero-content">
    <div class="row">
      <div class="col-lg-9" data-aos="fade-up">
        <span class="eyebrow">Aurelia Estates · Since 2010</span>
        <h1 class="hero-title">{!! setting('hero_title', 'Find Your') !!} <span class="accent">{{ setting('hero_highlight', 'Dream Property') }}</span></h1>
        <p class="hero-sub">{{ setting('hero_subtitle', 'Verified luxury apartments, villas and commercial spaces — backed by transparent pricing, legal support and two decades of trusted delivery.') }}</p>
        <div class="hero-cta">
          <a href="{{ setting('hero_btn_url', '#projects') }}" class="btn btn-gold">{{ setting('hero_btn_text', 'View Projects') }}</a>
          <a href="#contact" class="btn btn-outline-light-glass">Book a Visit</a>
        </div>
      </div>
    </div>
    <div class="row floating-stats g-3" data-aos="fade-up" data-aos-delay="150">
      <div class="col-6 col-md-4"><div class="glass stat-card"><strong data-count="500">0</strong><span>Properties Delivered</span></div></div>
      <div class="col-6 col-md-4"><div class="glass stat-card"><strong data-count="2500">0</strong><span>Happy Clients</span></div></div>
      <div class="col-12 col-md-4"><div class="glass stat-card"><strong data-count="20">0</strong><span>Years of Experience</span></div></div>
    </div>
  </div>
  <div class="scroll-indicator">SCROLL<div class="mouse"></div></div>
</section>
@endif

<!-- SEARCH -->
@if(setting('property_search_status', '1') == '1')
<div class="container search-panel" data-aos="fade-up" style="@if(setting('search_bg_image')) background-image: url('{{ Storage::url(setting('search_bg_image')) }}'); background-size: cover; border-radius: var(--radius); @endif">
  <div class="glass" style="@if(setting('search_bg_image')) background: rgba(255,255,255,0.85); backdrop-filter: blur(10px); padding: 20px; border-radius: var(--radius); @endif">
    @if(setting('search_heading'))
      <h5 class="text-center mb-3 fw-bold">{{ setting('search_heading') }}</h5>
    @endif
    <form class="row g-3 align-items-end" onsubmit="return false;">
      <div class="col-md-3 col-6">
        <label>Location</label>
        <select class="form-select"><option>Any Location</option><option>Dhaka</option><option>Chattogram</option><option>Sylhet</option><option>Cox's Bazar</option></select>
      </div>
      <div class="col-md-3 col-6">
        <label>Property Type</label>
        <select class="form-select"><option>Any Type</option><option>Apartment</option><option>Villa</option><option>Duplex</option><option>Commercial</option><option>Office</option><option>Land</option></select>
      </div>
      <div class="col-md-2 col-6">
        <label>Budget</label>
        <select class="form-select"><option>Any Budget</option><option>Under $200k</option><option>$200k–$500k</option><option>$500k+</option></select>
      </div>
      <div class="col-md-2 col-6">
        <label>Bedrooms</label>
        <select class="form-select"><option>Any</option><option>1</option><option>2</option><option>3</option><option>4+</option></select>
      </div>
      <div class="col-md-2 col-12">
        <button class="btn btn-navy search-btn" type="submit"><i class="fa-solid fa-magnifying-glass me-2"></i>Search</button>
      </div>
    </form>
  </div>
</div>
@endif

<!-- FEATURED PROJECTS -->
<section id="projects">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Featured Projects</span>
      <h2 class="section-title">Signature Developments</h2>
      <p class="mx-auto" style="max-width:560px;">A curated selection of our most sought-after residential and commercial developments.</p>
    </div>
    <div class="row g-4">
      @forelse($featuredProperties as $index => $property)
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
        <div class="project-card" onclick="window.location.href='{{ route('project.featured') }}'" style="cursor:pointer;">
          <div class="project-img-wrap">
            <span class="project-badge">Featured</span>
            <img src="{{ $property->image ? Storage::url($property->image) : 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=75' }}" loading="lazy" alt="{{ $property->title }}">
          </div>
          <div class="project-body">
            <h4>{{ $property->title }}</h4>
            <div class="project-loc"><i class="fa-solid fa-location-dot text-primary"></i> {{ $property->location }}</div>
            <div class="project-meta">
              <div><strong>{{ number_format($property->area) }} sqft</strong>Area</div>
              <div><strong>{{ $property->beds }} Bed</strong>&nbsp;&bull;&nbsp;<strong>{{ $property->baths }} Bath</strong></div>
              <div class="project-price">{{ $property->price }}</div>
            </div>
            <a href="{{ route('project.featured') }}" class="btn btn-navy w-100 mt-3">View Details</a>
          </div>
        </div>
      </div>
      @empty
      <div class="col-12 text-center text-muted">
        <p>No featured properties available at the moment.</p>
      </div>
      @endforelse
    </div>
  </div>

  <div class="container text-center mt-5" data-aos="fade-up">
    <a href="{{ route('projects') }}" class="btn btn-navy">View More</a>
  </div>
</section>

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

  <div class="container text-center mt-5" data-aos="fade-up">
    <a href="{{ route('services') }}" class="btn btn-navy">Explore Our Services</a>
  </div>
</section>

<!-- ABOUT -->
@if(setting('why_us_status', '1') == '1')
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

  <div class="container text-center mt-5" data-aos="fade-up">
    <a href="{{ route('about') }}" class="btn btn-navy">More About Us</a>
  </div>
</section>
@endif

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

<!-- CATEGORIES -->
<section id="categories">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Property Categories</span>
      <h2 class="section-title">Browse By Property Type</h2>
    </div>
    <div class="row g-4">
      @forelse($types as $type)
      <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="{{ ($loop->index % 3) * 80 }}">
        <div class="cat-card" onclick="window.location.href='{{ route('properties') }}?type={{ urlencode($type->title) }}'" style="cursor:pointer;">
          <img src="{{ $type->image ? (\Illuminate\Support\Str::startsWith($type->image, 'http') ? $type->image : Storage::url($type->image)) : 'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=700&q=75' }}" loading="lazy" alt="{{ $type->title }}">
          <div class="cat-overlay">
            <h5>
              <i class="{{ $type->icon_class ?? 'fa-solid fa-building' }}"></i>
              {{ $type->title }}
            </h5>
          </div>
        </div>
      </div>
      @empty
      <div class="col-12 text-center text-muted">
        <p>No property categories available.</p>
      </div>
      @endforelse
    </div>
  </div>

  <div class="container text-center mt-5" data-aos="fade-up">
    <a href="{{ route('properties') }}" class="btn btn-navy">View All Properties</a>
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

<!-- VIDEO TOUR -->
<section>
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Virtual Experience</span>
      <h2 class="section-title">Take A Virtual Tour</h2>
    </div>
    <div class="video-tour" data-aos="zoom-in">
      <img src="https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1400&q=75" loading="lazy" alt="Aurelia Heights aerial view">
      <div class="overlay">
        <div class="play-btn" id="playTourBtn"><i class="fa-solid fa-play"></i></div>
        <h4 class="mb-0">Explore Aurelia Heights in 360°</h4>
      </div>
    </div>
  </div>
</section>

<!-- GALLERY -->
<section id="gallery" style="background:linear-gradient(180deg,transparent,rgba(212,175,55,.06),transparent);">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Gallery</span>
      <h2 class="section-title">A Closer Look</h2>
    </div>
    <div class="gallery-grid" data-aos="fade-up">
      <div class="gallery-item" onclick="openLightbox(this)"><img src="https://images.unsplash.com/photo-1571003123894-1f0594d2b5d9?auto=format&fit=crop&w=700&q=75" loading="lazy" alt="Aerial drone view of residential complex"><span class="zoom-tag"><i class="fa-solid fa-expand"></i></span></div>
      <div class="gallery-item" onclick="openLightbox(this)"><img src="https://images.unsplash.com/photo-1521783988139-89397d761dce?auto=format&fit=crop&w=700&q=75" loading="lazy" alt="Modern kitchen interior"><span class="zoom-tag"><i class="fa-solid fa-expand"></i></span></div>
      <div class="gallery-item" onclick="openLightbox(this)"><img src="https://images.unsplash.com/photo-1600607687939-ce8a6c25118c?auto=format&fit=crop&w=700&q=75" loading="lazy" alt="Luxury bedroom suite"><span class="zoom-tag"><i class="fa-solid fa-expand"></i></span></div>
      <div class="gallery-item" onclick="openLightbox(this)"><img src="https://images.unsplash.com/photo-1524230507669-5ff97982bb5e?auto=format&fit=crop&w=700&q=75" loading="lazy" alt="Resort-style swimming pool"><span class="zoom-tag"><i class="fa-solid fa-expand"></i></span></div>
      <div class="gallery-item" onclick="openLightbox(this)"><img src="https://images.unsplash.com/photo-1523217582562-09d0def993a6?auto=format&fit=crop&w=700&q=75" loading="lazy" alt="Handover of new home keys"><span class="zoom-tag"><i class="fa-solid fa-expand"></i></span></div>
      <div class="gallery-item" onclick="openLightbox(this)"><img src="https://images.unsplash.com/photo-1600880292203-757bb62b4baf?auto=format&fit=crop&w=700&q=75" loading="lazy" alt="Villa under construction"><span class="zoom-tag"><i class="fa-solid fa-expand"></i></span></div>
    </div>
  </div>

  <div class="container text-center mt-5" data-aos="fade-up">
    <a href="{{ route('gallery') }}" class="btn btn-navy">View Full Gallery</a>
  </div>
</section>
<div id="lightboxOverlay"><span class="lightbox-close" onclick="closeLightbox()">&times;</span><img id="lightboxImg" src="" alt="Enlarged gallery photo"></div>


<!-- TESTIMONIALS -->
@if(setting('testimonials_status', '1') == '1')
<section>
  <div class="container-fluid overflow-hidden px-0">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Testimonials</span>
      <h2 class="section-title">What Our Clients Say</h2>
    </div>

    <div class="marquee-wrapper">
      <div class="marquee-track">
        @foreach($testimonials as $testimonial)
        <div class="marquee-card">
          <div class="top-row">
            <img src="{{ $testimonial->image ? Storage::url($testimonial->image) : 'https://i.pravatar.cc/150?u='.$testimonial->id }}" alt="{{ $testimonial->name }}">
            <div class="user-info">
              <span class="name">{{ $testimonial->name }}</span>
              <span class="role">{{ $testimonial->role }}</span>
            </div>
          </div>
          <div class="stars">
            @for($i=0; $i<$testimonial->rating; $i++)<i class="fa-solid fa-star"></i>@endfor
          </div>
          <p>"{{ $testimonial->quote }}"</p>
        </div>
        @endforeach

        <!-- Duplicate for seamless scroll -->
        @foreach($testimonials as $testimonial)
        <div class="marquee-card">
          <div class="top-row">
            <img src="{{ $testimonial->image ? Storage::url($testimonial->image) : 'https://i.pravatar.cc/150?u='.$testimonial->id }}" alt="{{ $testimonial->name }}">
            <div class="user-info">
              <span class="name">{{ $testimonial->name }}</span>
              <span class="role">{{ $testimonial->role }}</span>
            </div>
          </div>
          <div class="stars">
            @for($i=0; $i<$testimonial->rating; $i++)<i class="fa-solid fa-star"></i>@endfor
          </div>
          <p>"{{ $testimonial->quote }}"</p>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</section>
@endif

<!-- TEAM -->
@if(setting('team_status', '1') == '1')
<section style="background:linear-gradient(180deg,transparent,rgba(37,99,235,.05),transparent);">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Our Team</span>
      <h2 class="section-title">The People Behind Aurelia</h2>
    </div>
    <div class="row g-4">
      @forelse($teamMembers as $index => $member)
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ $index * 80 }}">
        <div class="team-card">
          <div class="team-img-wrap">
            <img src="{{ $member->image ? Storage::url($member->image) : 'https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=500&q=75' }}" loading="lazy" alt="{{ $member->name }}">
            <div class="team-social">
              @if($member->linkedin) <a href="{{ $member->linkedin }}" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a> @endif
              @if($member->facebook) <a href="{{ $member->facebook }}" target="_blank"><i class="fa-brands fa-facebook-f"></i></a> @endif
              @if($member->email) <a href="mailto:{{ $member->email }}"><i class="fa-solid fa-envelope"></i></a> @endif
            </div>
          </div>
          <div class="team-body">
            <h5>{{ $member->name }}</h5>
            <span>{{ $member->role }}</span>
          </div>
        </div>
      </div>
      @empty
      <div class="col-12 text-center text-muted">
        <p>No team members listed at the moment.</p>
      </div>
      @endforelse
    </div>
  </div>
</section>
@endif

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

<!-- FAQ -->
<section style="background:linear-gradient(180deg,transparent,rgba(212,175,55,.06),transparent);">
  <div class="container" style="max-width:820px;">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">FAQ</span>
      <h2 class="section-title">Frequently Asked Questions</h2>
    </div>
    <div class="accordion" id="faqAccordion" data-aos="fade-up">
      <div class="accordion-item">
        <h2 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">Do you offer installment payment plans?</button></h2>
        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion"><div class="accordion-body">Yes, most properties come with flexible 3 to 5 year installment plans with a minimum 20% down payment.</div></div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">Are all listed properties legally verified?</button></h2>
        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body">Every property undergoes a full title and legal document check by our in-house legal team before listing.</div></div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">Can I book a site visit before purchasing?</button></h2>
        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body">Absolutely — you can schedule a free guided site visit using the "Book a Visit" button or the contact form below.</div></div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">What happens after I reserve a unit?</button></h2>
        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body">Our sales team contacts you within 24 hours to confirm the booking, share the agreement, and outline the payment schedule.</div></div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
@if(setting('cta_status', '1') == '1')
<section>
  <div class="container">
    <div class="cta-banner text-center" data-aos="zoom-in" style="@if(setting('cta_bg_image')) background-image: url('{{ Storage::url(setting('cta_bg_image')) }}'); background-size: cover; background-position: center; @endif">
      <span class="eyebrow" style="color:var(--gold-soft);"><i></i>Limited Units Remaining</span>
      <h2>{{ setting('cta_title', 'Ready To Invest In Your Future?') }}</h2>
      <p class="mb-4" style="color:rgba(255,255,255,.8);">{{ setting('cta_description', 'Book a free site visit or speak to our advisors today — no obligation, just clarity.') }}</p>
      <div class="d-flex justify-content-center gap-3 flex-wrap">
        @if(setting('cta_btn_text'))
        <a href="{{ setting('cta_btn_link', '#contact') }}" class="btn btn-gold">{{ setting('cta_btn_text') }}</a>
        @endif
        <a href="tel:{{ setting('footer_phone', '+8801XXXXXXXXX') }}" class="btn btn-outline-light-glass"><i class="fa-solid fa-phone me-2"></i>Call Now</a>
      </div>
    </div>
  </div>
</section>
@endif

@endsection
