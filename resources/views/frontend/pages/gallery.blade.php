@extends('frontend.layouts.app')
@section('content')

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="container">
    <h1 data-aos="fade-up">{{ setting('gallery_title', 'Media Gallery') }}</h1>
    <p data-aos="fade-up" data-aos-delay="100">{{ setting('gallery_subtitle', 'Take a visual tour of our finest properties.') }}</p>
  </div>
</div>

<!-- VIRTUAL VIDEO TOUR -->
<section>
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">{{ setting('gallery_tour_eyebrow', 'Virtual Experience') }}</span>
      <h2 class="section-title">{{ setting('gallery_tour_title', 'Take A Virtual Tour') }}</h2>
    </div>
    <div class="video-tour" data-aos="zoom-in">
      <img src="{{ asset_url(setting('gallery_tour_image'), 'https://images.unsplash.com/photo-1512917774080-9991f1c4c750?auto=format&fit=crop&w=1400&q=75') }}" loading="lazy" alt="{{ setting('gallery_tour_video_title') }}">
      <div class="overlay">
        <a href="{{ setting('gallery_tour_video_url', 'https://www.youtube.com/watch?v=dQw4w9WgXcQ') }}" target="_blank" class="play-btn" id="playTourBtn" style="text-decoration:none;">
          <i class="fa-solid fa-play"></i>
        </a>
        <h4 class="mb-0">{{ setting('gallery_tour_video_title', 'Explore Aurelia Heights in 360°') }}</h4>
      </div>
    </div>
  </div>
</section>

<!-- PHOTO GALLERY -->
<section id="gallery" style="background:linear-gradient(180deg,transparent,rgba(212,175,55,.06),transparent);">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">{{ setting('gallery_section_eyebrow', 'Gallery') }}</span>
      <h2 class="section-title">{{ setting('gallery_section_title', 'A Closer Look') }}</h2>
    </div>
    <div class="gallery-grid" data-aos="fade-up">
      @forelse($galleries as $photo)
      <div class="gallery-item" onclick="openLightbox(this)">
        <img src="{{ asset_url($photo->image) }}" loading="lazy" alt="{{ $photo->title }}">
        <span class="zoom-tag"><i class="fa-solid fa-expand"></i></span>
      </div>
      @empty
      <div class="col-12 text-center text-muted py-4">
        <p>No photos uploaded to the gallery yet.</p>
      </div>
      @endforelse
    </div>
  </div>
</section>
<div id="lightboxOverlay"><span class="lightbox-close" onclick="closeLightbox()">&times;</span><img id="lightboxImg" src="" alt="Enlarged gallery photo"></div>

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
