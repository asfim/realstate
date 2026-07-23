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
      @forelse($projects as $proj)
        @php
            $projImg = asset_url($proj->image);
        @endphp
        <div class="col-lg-4 col-md-6" data-aos="fade-up">
          <div class="project-card" onclick="window.location.href='{{ route('project.featured', $proj->id) }}'" style="cursor:pointer;">
            <div class="project-img-wrap">
              <span class="project-badge">Featured</span>
              <img src="{{ $projImg }}" loading="lazy" alt="{{ $proj->title }}" style="height: 240px; width: 100%; object-fit: cover;">
            </div>
            <div class="project-body">
              <h4>{{ $proj->title }}</h4>
              <div class="project-loc"><i class="fa-solid fa-location-dot text-primary"></i> {{ $proj->location }}</div>
              <div class="project-meta">
                <div><strong>{{ $proj->area ?? 2100 }} sqft</strong>Area</div>
                <div><strong>{{ $proj->beds ?? 3 }} Beds</strong>Layout</div>
                <div class="project-price">{{ $proj->price }}</div>
              </div>
              <a href="{{ route('project.featured', $proj->id) }}" class="btn btn-navy w-100 mt-3">View Details</a>
            </div>
          </div>
        </div>
      @empty
        <div class="col-12 text-center py-5 text-muted">
          No projects available.
        </div>
      @endforelse
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
