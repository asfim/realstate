@extends('frontend.layouts.app')
@section('content')

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="container">
    <h1 data-aos="fade-up">Media Gallery</h1>
    <p data-aos="fade-up" data-aos-delay="100">Take a visual tour of our finest properties.</p>
  </div>
</div>
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
