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
      <span class="eyebrow">Services & Value</span>
      <h2 class="section-title">Built On Trust, Delivered With Precision</h2>
    </div>
    <div class="row g-4">
      @forelse($services as $index => $service)
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($index % 3) * 80 }}">
        <div class="glass feature-icon-card h-100 p-4">
          @if($service->image)
            <img src="{{ asset_url($service->image) }}" alt="{{ $service->title }}" style="width: 100%; height: 160px; object-fit: cover; border-radius: 12px;" class="mb-3">
          @else
            <div class="feature-icon mb-3">
              <i class="{{ $service->icon ?? 'fa-solid fa-concierge-bell' }}"></i>
            </div>
          @endif
          <h5>{{ $service->title }}</h5>
          <p>{{ $service->short_description }}</p>
        </div>
      </div>
      @empty
      <div class="col-12 text-center text-muted py-4">
        <p>No services currently listed. Check back soon!</p>
      </div>
      @endforelse
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
    <div class="row g-3 justify-content-center text-center">
      @forelse($amenities as $index => $amenity)
      <div class="col-6 col-md-3 col-lg-2" data-aos="fade-up" data-aos-delay="{{ ($index % 6) * 40 }}">
        <div class="amenity-item p-3 h-100 d-flex flex-column align-items-center justify-content-center">
          <i class="{{ $amenity->icon ?? 'fa-solid fa-star' }}"></i>
          <span class="mt-2 fw-semibold">{{ $amenity->title }}</span>
        </div>
      </div>
      @empty
      <div class="col-12 text-center text-muted py-4">
        <p>No amenities listed currently.</p>
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
