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
      @forelse($projects as $index => $property)
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ $index * 100 }}">
        <div class="project-card" onclick="window.location.href='{{ route('property.featured.details') }}'" style="cursor:pointer;">
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
            <a href="{{ route('property.featured.details') }}" class="btn btn-navy w-100 mt-3">View Details</a>
          </div>
        </div>
      </div>
      @empty
      <div class="col-12 text-center text-muted">
        <p>No projects available at the moment.</p>
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
