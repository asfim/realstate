@extends('frontend.layouts.app')
@section('content')

<div class="property-details-page" style="background-color: #0F172A; padding-top: 0;">
  <!-- Top Gallery -->
  <div class="pm-gallery">
    <div class="pm-main-img">
      <img src="{{ asset_url($property->image, !empty($property->detail->gallery) ? asset_url($property->detail->gallery[0]) : null) }}" alt="{{ $property->title }}">
    </div>
    @if($property->image || !empty($property->detail->gallery))
    <div class="pm-thumbnails">
      <button class="nav-btn prev" type="button"><i class="fa-solid fa-chevron-left"></i></button>
      <div class="thumb-track-wrap">
        <div class="thumb-track">
          @if($property->image)
            <img src="{{ asset_url($property->image) }}" class="active" alt="Cover Image">
          @endif
          @if(!empty($property->detail->gallery))
            @foreach($property->detail->gallery as $imgPath)
              <img src="{{ asset_url($imgPath) }}" class="{{ !$property->image && $loop->first ? 'active' : '' }}" alt="Thumb {{ $loop->iteration }}">
            @endforeach
          @endif
        </div>
      </div>
      <button class="nav-btn next" type="button"><i class="fa-solid fa-chevron-right"></i></button>
    </div>
    @endif
  </div>

  <div class="container py-5 pm-content">
    <div class="row g-5">
      <!-- Left Column -->
      <div class="col-lg-7">
        <div class="pm-section">
          <h2 class="pm-title">{{ $property->title }}</h2>
          <p class="pm-desc">{!! nl2br(e($property->detail->description ?? 'No description available yet.')) !!}</p>
        </div>

        <div class="pm-section">
          <h3 class="pm-subtitle">Floor plan</h3>
          @if(!empty($property->detail->floor_plans))
            <div id="floorplanCarousel" class="carousel slide" data-bs-ride="carousel">
              <div class="carousel-inner">
                @foreach(array_chunk($property->detail->floor_plans, 4) as $chunkIndex => $chunk)
                  <div class="carousel-item {{ $chunkIndex === 0 ? 'active' : '' }}">
                    <div class="row g-3">
                      @foreach($chunk as $planPath)
                        <div class="col-6 col-md-3">
                          <a href="{{ Storage::url($planPath) }}" target="_blank">
                            <img src="{{ Storage::url($planPath) }}" class="d-block w-100 pm-floorplan" alt="Plan">
                          </a>
                        </div>
                      @endforeach
                    </div>
                  </div>
                @endforeach
              </div>
              @if(count($property->detail->floor_plans) > 4)
                <button class="carousel-control-prev" type="button" data-bs-target="#floorplanCarousel" data-bs-slide="prev" style="width: 5%; justify-content: flex-start; margin-left: -20px;">
                  <span class="carousel-control-prev-icon" aria-hidden="true" style="filter: invert(1); background-color: rgba(255,255,255,0.8); border-radius: 50%; padding: 15px;"></span>
                  <span class="visually-hidden">Previous</span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#floorplanCarousel" data-bs-slide="next" style="width: 5%; justify-content: flex-end; margin-right: -20px;">
                  <span class="carousel-control-next-icon" aria-hidden="true" style="filter: invert(1); background-color: rgba(255,255,255,0.8); border-radius: 50%; padding: 15px;"></span>
                  <span class="visually-hidden">Next</span>
                </button>
              @endif
            </div>
          @else
            <p class="text-muted">No floor plans uploaded.</p>
          @endif
        </div>

        <div class="pm-section">
          <h3 class="pm-subtitle">Location</h3>
          <div class="pm-map-wrap">
            @if(!empty($property->detail->map_url))
              <iframe src="{{ $property->detail->map_url }}" width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            @else
              <p class="text-muted">Map location not set.</p>
            @endif
          </div>
        </div>
      </div>

      <!-- Right Column -->
      <div class="col-lg-5">
        <div class="pm-box mb-4">
          <h3 class="pm-subtitle">At a Glance</h3>
          <table class="table pm-table">
            <tbody>
              <tr><td><i class="fa-solid fa-location-dot"></i> Address</td><td>{{ $property->detail->address ?? $property->location }}</td></tr>
              <tr><td><i class="fa-solid fa-map"></i> Land Area</td><td>{{ $property->detail->land_area ?? 'N/A' }}</td></tr>
              <tr><td><i class="fa-solid fa-building"></i> No. of Floors</td><td>{{ $property->detail->no_of_floors ?? 'N/A' }}</td></tr>
              <tr><td><i class="fa-solid fa-layer-group"></i> Apartment/Floor</td><td>{{ $property->detail->apartments_per_floor ?? 'N/A' }}</td></tr>
              <tr><td><i class="fa-solid fa-ruler-combined"></i> Apartment Size</td><td>{{ $property->area ? number_format($property->area) . ' sqft' : 'N/A' }}</td></tr>
              <tr><td><i class="fa-solid fa-bed"></i> Bedroom</td><td>{{ $property->beds ?? 'N/A' }}</td></tr>
              <tr><td><i class="fa-solid fa-bath"></i> Bathroom</td><td>{{ $property->baths ?? 'N/A' }}</td></tr>
              <tr><td><i class="fa-solid fa-calendar-plus"></i> Launch Date</td><td>{{ $property->detail->launch_date ?? 'N/A' }}</td></tr>
              <tr><td><i class="fa-solid fa-calendar-check"></i> Expected Completion</td><td>{{ $property->detail->expected_completion ?? 'N/A' }}</td></tr>
            </tbody>
          </table>
          <div class="d-flex gap-2 mt-3">
            <a href="{{ route('property.construction.status', $property->id) }}" class="btn btn-outline-light flex-grow-1"><i class="fa-solid fa-person-digging me-2"></i>Construction Status</a>
          </div>
        </div>

        <div class="pm-box">
          <h3 class="pm-subtitle">I am interested in this property</h3>
          <form class="pm-form" action="{{ route('enquiry.submit') }}" method="POST" onsubmit="return handlePropertyEnquiry(event, this);">
            @csrf
            <input type="hidden" name="property_id" value="{{ $property->id }}">
            <div class="prop-alert alert alert-success d-none mb-3" role="alert"></div>
            <input type="text" name="name" class="form-control" placeholder="Name" required>
            <input type="email" name="email" class="form-control" placeholder="Email Address" required>
            <input type="tel" name="phone" class="form-control" placeholder="Phone Number" required>
            <textarea name="message" class="form-control" rows="3" required>I am interested in your property named "{{ $property->title }}".</textarea>
            <button type="submit" class="btn btn-gold w-100 mt-2">Submit <i class="fa-solid fa-arrow-right ms-2"></i></button>
          </form>
        </div>
      </div>
    </div>

    <hr class="pm-divider">
  </div>
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

    const trackWrap = document.querySelector('.thumb-track-wrap');
    if (trackWrap && thumbs[index]) {
      const thumb = thumbs[index];
      const targetLeft = thumb.offsetLeft - (trackWrap.clientWidth / 2) + (thumb.clientWidth / 2);
      trackWrap.scrollTo({
        left: Math.max(0, targetLeft),
        behavior: 'smooth'
      });
    }
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

function handlePropertyEnquiry(e, form) {
  e.preventDefault();
  const alertBox = form.querySelector('.prop-alert');
  const submitBtn = form.querySelector('button[type="submit"]');

  submitBtn.disabled = true;
  submitBtn.innerHTML = 'Submitting... <i class="fa-solid fa-spinner fa-spin ms-2"></i>';

  const formData = new FormData(form);

  fetch('{{ route("enquiry.submit") }}', {
    method: 'POST',
    headers: {
      'X-CSRF-TOKEN': '{{ csrf_token() }}',
      'Accept': 'application/json'
    },
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if(data.success) {
      alertBox.classList.remove('d-none');
      alertBox.className = 'prop-alert alert d-flex align-items-center mb-3 border-0 p-3 text-white';
      alertBox.style.background = 'linear-gradient(135deg, #10B981, #059669)';
      alertBox.style.borderRadius = '12px';
      alertBox.style.boxShadow = '0 10px 25px rgba(16,185,129,0.3)';
      alertBox.innerHTML = '<i class="fa-solid fa-circle-check me-3 fs-3"></i> <div><strong class="d-block mb-1">Enquiry Submitted!</strong><span style="font-size:0.9rem; opacity:0.95;">' + data.message + '</span></div>';
      form.reset();
    } else {
      alertBox.classList.remove('d-none');
      alertBox.className = 'alert alert-danger mb-3';
      alertBox.innerText = 'Something went wrong. Please try again.';
    }
  })
  .catch(err => {
    alertBox.classList.remove('d-none');
    alertBox.className = 'alert alert-danger mb-3';
    alertBox.innerText = 'Server error. Please try again.';
  })
  .finally(() => {
    submitBtn.disabled = false;
    submitBtn.innerHTML = 'Submit <i class="fa-solid fa-arrow-right ms-2"></i>';
  });

  return false;
}
</script>
@endsection
