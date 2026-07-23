@extends('frontend.layouts.app')
@section('content')

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="container">
    @if(request('type'))
      <h1 data-aos="fade-up">{{ request('type') }} Listings</h1>
      <p data-aos="fade-up" data-aos-delay="100">Explore premium {{ strtolower(request('type')) }} units in choice locations.</p>
    @else
      <h1 data-aos="fade-up">Property Categories</h1>
      <p data-aos="fade-up" data-aos-delay="100">Find the perfect property type that fits your lifestyle.</p>
    @endif
  </div>
</div>

@if(request('type'))
<!-- PROPERTIES LIST BY TYPE -->
<section id="categories">
  <div class="container">
    <div class="mb-4" data-aos="fade-up">
      <a href="{{ route('properties') }}" class="btn btn-navy btn-sm px-3">
        <i class="fa-solid fa-arrow-left me-2"></i>Back to Categories
      </a>
    </div>

    <div class="row g-4">
      @forelse($properties as $index => $property)
      <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="{{ $index * 100 }}">
        <div class="cat-card" onclick="window.location.href='{{ route('property.details', $property->id) }}'" style="cursor:pointer;">
          <img src="{{ $property->image ? Storage::url($property->image) : 'https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=75' }}" loading="lazy" alt="{{ $property->title }}">
          <div class="cat-overlay">
            <h5><i class="fa-solid fa-building"></i>{{ $property->title }}</h5>
          </div>
        </div>
      </div>
      @empty
      <div class="col-12 text-center text-muted py-5">
        <p>No {{ strtolower(request('type')) }} listings available at the moment. Please check back later.</p>
      </div>
      @endforelse
    </div>
  </div>
</section>
@else
<!-- CATEGORIES GRID -->
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
</section>
@endif

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
