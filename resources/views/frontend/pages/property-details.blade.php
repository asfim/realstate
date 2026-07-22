@extends('frontend.layouts.app')
@section('content')

<div class="property-details-page" style="background-color: #0F172A; padding-top: 0;">
  <!-- Top Gallery -->
  <div class="pm-gallery">
    <div class="pm-main-img">
      <img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=1200&q=75" alt="Main View">
    </div>
    <div class="pm-thumbnails">
      <button class="nav-btn prev"><i class="fa-solid fa-chevron-left"></i></button>
      <div class="thumb-track">
        <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=200&q=75" class="active" alt="Thumb 1">
        <img src="https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=200&q=75" alt="Thumb 2">
        <img src="https://images.unsplash.com/photo-1600210491892-03d54c0aaf87?auto=format&fit=crop&w=200&q=75" alt="Thumb 3">
        <img src="https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=200&q=75" alt="Thumb 4">
      </div>
      <button class="nav-btn next"><i class="fa-solid fa-chevron-right"></i></button>
    </div>
  </div>

  <div class="container py-5 pm-content">
    <div class="row g-5">
      <!-- Left Column -->
      <div class="col-lg-7">
        <div class="pm-section">
          <h2 class="pm-title">Amabilia</h2>
          <p class="pm-desc">Blessed with an incredible number of conveniences, Amabilia is a love letter to refined luxury living. The corner plot home dictates amazing spaciousness, open surroundings, and charming architectural details primed with greenery. Amabilia features 2100+ sft luxurious units propped with Smart Home features.</p>
        </div>

        <div class="pm-section">
          <h3 class="pm-subtitle">Floor plan</h3>
          <div id="floorplanCarousel" class="carousel slide" data-bs-ride="carousel">
            <div class="carousel-inner">
              <div class="carousel-item active">
                <div class="row g-3">
                  <div class="col-6 col-md-3"><img src="https://images.unsplash.com/photo-1598928506311-c55dd1b6b5cb?auto=format&fit=crop&w=400&q=75" class="d-block w-100 pm-floorplan" alt="Plan 1"></div>
                  <div class="col-6 col-md-3"><img src="https://images.unsplash.com/photo-1598928506311-c55dd1b6b5cb?auto=format&fit=crop&w=400&q=75" class="d-block w-100 pm-floorplan" alt="Plan 2"></div>
                  <div class="col-6 col-md-3"><img src="https://images.unsplash.com/photo-1598928506311-c55dd1b6b5cb?auto=format&fit=crop&w=400&q=75" class="d-block w-100 pm-floorplan" alt="Plan 3"></div>
                  <div class="col-6 col-md-3"><img src="https://images.unsplash.com/photo-1598928506311-c55dd1b6b5cb?auto=format&fit=crop&w=400&q=75" class="d-block w-100 pm-floorplan" alt="Plan 4"></div>
                </div>
              </div>
              <div class="carousel-item">
                <div class="row g-3">
                  <div class="col-6 col-md-3"><img src="https://images.unsplash.com/photo-1598928506311-c55dd1b6b5cb?auto=format&fit=crop&w=400&q=75" class="d-block w-100 pm-floorplan" alt="Plan 5"></div>
                  <div class="col-6 col-md-3"><img src="https://images.unsplash.com/photo-1598928506311-c55dd1b6b5cb?auto=format&fit=crop&w=400&q=75" class="d-block w-100 pm-floorplan" alt="Plan 6"></div>
                </div>
              </div>
            </div>
            <button class="carousel-control-prev" type="button" data-bs-target="#floorplanCarousel" data-bs-slide="prev" style="width: 5%; justify-content: flex-start; margin-left: -20px;">
              <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: invert(1); background-color: rgba(255,255,255,0.8); border-radius: 50%; padding: 15px;"></span>
              <span class="visually-hidden">Previous</span>
            </button>
            <button class="carousel-control-next" type="button" data-bs-target="#floorplanCarousel" data-bs-slide="next" style="width: 5%; justify-content: flex-end; margin-right: -20px;">
              <span class="carousel-control-next-icon" aria-hidden="true" style="filter: invert(1); background-color: rgba(255,255,255,0.8); border-radius: 50%; padding: 15px;"></span>
              <span class="visually-hidden">Next</span>
            </button>
          </div>
        </div>

        <div class="pm-section">
          <h3 class="pm-subtitle">Location</h3>
          <div class="pm-map-wrap">
            <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3651.9024424301397!2d90.3910801154313!3d23.75085809467885!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjPCsDQ1JzAzLjEiTiA5MMKwMjMnMzUuNyJF!5e0!3m2!1sen!2sbd!4v1620000000000!5m2!1sen!2sbd" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
          </div>
        </div>
      </div>

      <!-- Right Column -->
      <div class="col-lg-5">
        <div class="pm-box mb-4">
          <h3 class="pm-subtitle">At a Glance</h3>
          <table class="table pm-table">
            <tbody>
              <tr><td><i class="fa-solid fa-location-dot"></i> Address</td><td>Plot-12, Abbas Garden Road, Mohakhali, Dhaka</td></tr>
              <tr><td><i class="fa-solid fa-map"></i> Land Area</td><td>8 Katha</td></tr>
              <tr><td><i class="fa-solid fa-building"></i> No. of Floors</td><td>B+G+7</td></tr>
              <tr><td><i class="fa-solid fa-layer-group"></i> Apartment/Floor</td><td>2</td></tr>
              <tr><td><i class="fa-solid fa-ruler-combined"></i> Apartment Size</td><td>2100 to 2200+ sft</td></tr>
              <tr><td><i class="fa-solid fa-bed"></i> Bedroom</td><td>3</td></tr>
              <tr><td><i class="fa-solid fa-bath"></i> Bathroom</td><td>3</td></tr>
              <tr><td><i class="fa-solid fa-calendar-plus"></i> Launch Date</td><td>October 2023</td></tr>
              <tr><td><i class="fa-solid fa-calendar-check"></i> Expected Completion</td><td>September 2026</td></tr>
            </tbody>
          </table>
          <div class="d-flex gap-2 mt-3">
            <a href="{{ route('property.construction.status') }}" class="btn btn-outline-light flex-grow-1"><i class="fa-solid fa-person-digging me-2"></i>Construction Status</a>
          </div>
        </div>

        <div class="pm-box">
          <h3 class="pm-subtitle">I am interested in this property</h3>
          <form class="pm-form">
            <input type="text" class="form-control" placeholder="Name">
            <input type="email" class="form-control" placeholder="Email Address">
            <input type="tel" class="form-control" placeholder="Phone Number">
            <textarea class="form-control" rows="3">I am interested in your property named "Amabilia".</textarea>
            <button type="submit" class="btn btn-gold w-100 mt-2">Submit <i class="fa-solid fa-arrow-right ms-2"></i></button>
          </form>
        </div>
      </div>
    </div>

    <hr class="pm-divider">
  </div>
</div>

<!-- Floating Action Buttons -->
<div class="pm-fabs">
  <a href="#" class="pm-fab"><i class="fa-solid fa-phone"></i></a>
  <a href="#" class="pm-fab"><i class="fa-brands fa-whatsapp"></i></a>
  <a href="#" class="pm-fab"><i class="fa-regular fa-message"></i></a>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const mainImg = document.querySelector('.pm-main-img img');
  const thumbs = document.querySelectorAll('.thumb-track img');
  const prevBtn = document.querySelector('.pm-thumbnails .prev');
  const nextBtn = document.querySelector('.pm-thumbnails .next');
  let currentIndex = 0;

  function updateMainImage(index) {
    if(!thumbs[index]) return;
    thumbs.forEach(t => t.classList.remove('active'));
    thumbs[index].classList.add('active');

    // Convert thumbnail resolution to full resolution
    let src = thumbs[index].src;
    src = src.replace('&w=200', '&w=1200');
    mainImg.src = src;
    currentIndex = index;
  }

  thumbs.forEach((thumb, index) => {
    thumb.addEventListener('click', () => {
      updateMainImage(index);
    });
  });

  if(prevBtn) {
    prevBtn.addEventListener('click', () => {
      let newIndex = currentIndex - 1;
      if (newIndex < 0) newIndex = thumbs.length - 1;
      updateMainImage(newIndex);
    });
  }

  if(nextBtn) {
    nextBtn.addEventListener('click', () => {
      let newIndex = currentIndex + 1;
      if (newIndex >= thumbs.length) newIndex = 0;
      updateMainImage(newIndex);
    });
  }

  // Auto-slide functionality
  let autoSlideInterval;
  function startAutoSlide() {
    autoSlideInterval = setInterval(() => {
      let newIndex = currentIndex + 1;
      if (newIndex >= thumbs.length) newIndex = 0;
      updateMainImage(newIndex);
    }, 4000); // Slides every 4 seconds
  }

  function stopAutoSlide() {
    clearInterval(autoSlideInterval);
  }

  startAutoSlide();

  // Pause auto-slide when interacting with the gallery
  const gallery = document.querySelector('.pm-gallery');
  if(gallery) {
    gallery.addEventListener('mouseenter', stopAutoSlide);
    gallery.addEventListener('mouseleave', startAutoSlide);
  }
});
</script>
@endsection
