@extends('frontend.layouts.app')
@section('content')

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="container">
    <h1 data-aos="fade-up">Property Categories</h1>
    <p data-aos="fade-up" data-aos-delay="100">Find the perfect property type that fits your lifestyle.</p>
  </div>
</div>
<!-- CATEGORIES -->
<section id="categories">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Property Categories</span>
      <h2 class="section-title">Browse By Property Type</h2>
    </div>
    <div class="row g-4">
      <div class="col-lg-4 col-md-6" data-aos="zoom-in"><div class="cat-card" onclick="window.location.href='{{ route('property.details') }}'" style="cursor:pointer;"><img src="https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=700&q=75" loading="lazy" alt="Apartment interior"><div class="cat-overlay"><h5><i class="fa-solid fa-building"></i>Apartment</h5></div></div></div>
      <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="80"><div class="cat-card" onclick="window.location.href='{{ route('property.details') }}'" style="cursor:pointer;"><img src="https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=700&q=75" loading="lazy" alt="Luxury villa"><div class="cat-overlay"><h5><i class="fa-solid fa-house"></i>Villa</h5></div></div></div>
      <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="160"><div class="cat-card" onclick="window.location.href='{{ route('property.details') }}'" style="cursor:pointer;"><img src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=700&q=75" loading="lazy" alt="Duplex home"><div class="cat-overlay"><h5><i class="fa-solid fa-house-chimney"></i>Duplex</h5></div></div></div>
      <div class="col-lg-4 col-md-6" data-aos="zoom-in"><div class="cat-card" onclick="window.location.href='{{ route('property.details') }}'" style="cursor:pointer;"><img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=700&q=75" loading="lazy" alt="Commercial building"><div class="cat-overlay"><h5><i class="fa-solid fa-shop"></i>Commercial</h5></div></div></div>
      <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="80"><div class="cat-card" onclick="window.location.href='{{ route('property.details') }}'" style="cursor:pointer;"><img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=700&q=75" loading="lazy" alt="Office space"><div class="cat-overlay"><h5><i class="fa-solid fa-briefcase"></i>Office</h5></div></div></div>
      <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="160"><div class="cat-card" onclick="window.location.href='{{ route('property.details') }}'" style="cursor:pointer;"><img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=700&q=75" loading="lazy" alt="Open land plot"><div class="cat-overlay"><h5><i class="fa-solid fa-map"></i>Land</h5></div></div></div>
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
