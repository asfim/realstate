@extends('frontend.layouts.app')

@section('content')
<div class="property-dossier" style="background:#F7F5F0; color:#1C1B19; padding-bottom: 60px;">
<div class="crumb-strip">

  <div class="container"><a href="{{ route('home') }}">Home</a> / <a href="{{ route('properties') }}">Properties</a> / <span>Beverly Residence</span></div>
</div>

<section class="hero-collage container">
  <div class="hero-grid">
    <div class="hero-main">
      <span class="hero-tag mono">01 / ROOFTOP WALKWAY</span>
      <img id="heroMainImg" src="https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=1400&q=80" alt="Beverly Residence rooftop walkway at night">
    </div>
    <div class="hero-side">
      <div class="cell"><img src="https://images.unsplash.com/photo-1600607687644-c7171b42498b?auto=format&fit=crop&w=700&q=75" alt="Beverly Residence dining terrace"><div class="more mono">02</div></div>
      <div class="cell"><img src="https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=700&q=75" alt="Beverly Residence facade detail"><div class="more mono">+6 more</div></div>
    </div>
  </div>
  <div class="thumb-rail" id="thumbRail">
    <div class="thumb active" data-src="https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=1400&q=80"><img src="https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=200&q=60" alt=""><span>01</span></div>
    <div class="thumb" data-src="https://images.unsplash.com/photo-1600607687644-c7171b42498b?auto=format&fit=crop&w=1400&q=80"><img src="https://images.unsplash.com/photo-1600607687644-c7171b42498b?auto=format&fit=crop&w=200&q=60" alt=""><span>02</span></div>
    <div class="thumb" data-src="https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1400&q=80"><img src="https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=200&q=60" alt=""><span>03</span></div>
    <div class="thumb" data-src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1400&q=80"><img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=200&q=60" alt=""><span>04</span></div>
    <div class="thumb" data-src="https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?auto=format&fit=crop&w=1400&q=80"><img src="https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?auto=format&fit=crop&w=200&q=60" alt=""><span>05</span></div>
    <div class="thumb" data-src="https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=1400&q=80"><img src="https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=200&q=60" alt=""><span>06</span></div>
  </div>
</section>

<section class="overview-section" id="overview">
  <div class="container overview-grid">
    <div>
      <span class="idx">OVERVIEW</span>
      <h1 class="property-title">Beverly Residence</h1>
      <div class="property-loc"><i class="fa-solid fa-location-dot"></i> Plot # 17/A, Road # 126, Gulshan, Dhaka</div>
      <div class="overview-text">
        <p>Nestled in the vibrant center of Gulshan, Beverly Residence is a nine-storey address built around a double-height entrance and a furnished "meet and greet" lobby that keeps arrival private and unhurried.</p>
        <p>Each floor holds a single 3,000+ sqft, three-bedroom apartment, and the patterned rooftop turns the building's crown into its most memorable room — restaurants, hospitals and schools sit within easy reach on every side.</p>
      </div>
      <div class="status-row">
        <div class="status-item"><span class="n mono">G+9</span><span class="l">Floors</span></div>
        <div class="status-item"><span class="n mono">1</span><span class="l">Apt / Floor</span></div>
        <div class="status-item"><span class="n mono">3 / 3</span><span class="l">Bed / Bath</span></div>
        <div class="status-item"><span class="n mono">6.43</span><span class="l">Katha Land</span></div>
      </div>
    </div>

    <div class="spec-plate">
      <div class="spec-plate-head"><h5>At a Glance</h5><span class="idx" style="margin:0;">SPEC</span></div>
      <div class="spec-row"><span class="k mono"><i class="fa-solid fa-location-dot"></i>Address</span><span class="v">Plot 17/A, Rd 126</span></div>
      <div class="spec-row"><span class="k mono"><i class="fa-solid fa-ruler-combined"></i>Land Area</span><span class="v">6.43 Katha</span></div>
      <div class="spec-row"><span class="k mono"><i class="fa-solid fa-building"></i>No. of Floors</span><span class="v">G + 9</span></div>
      <div class="spec-row"><span class="k mono"><i class="fa-solid fa-table-cells"></i>Apt / Floor</span><span class="v">1</span></div>
      <div class="spec-row"><span class="k mono"><i class="fa-solid fa-vector-square"></i>Apt Size</span><span class="v">3000+ sft</span></div>
      <div class="spec-row"><span class="k mono"><i class="fa-solid fa-bed"></i>Bedroom</span><span class="v">3</span></div>
      <div class="spec-row"><span class="k mono"><i class="fa-solid fa-bath"></i>Bathroom</span><span class="v">3</span></div>
      <div class="spec-row"><span class="k mono"><i class="fa-solid fa-calendar"></i>Launch Date</span><span class="v">May 2024</span></div>
      <div class="spec-row"><span class="k mono"><i class="fa-solid fa-calendar-check"></i>Completion</span><span class="v">Mar 2027</span></div>
      <div class="spec-actions">
        <a href="{{ route('') }}" class="btn-line">Construction Status</a>
        <a href="#" class="btn-copper"><i class="fa-solid fa-download"></i>Brochure</a>
      </div>
    </div>
  </div>
</section>

<section class="plan-section" id="floorplan">
  <div class="container">
    <span class="idx">FLOOR PLAN</span>
    <h2 class="property-title" style="font-size:clamp(1.8rem,3vw,2.4rem);">Layouts By Level</h2>
    <div class="plan-tabs" id="planTabs">
      <button class="plan-tab active" data-plan="ground">Ground Floor</button>
      <button class="plan-tab" data-plan="typical">Typical Floor</button>
      <button class="plan-tab" data-plan="roof">Roof Floor</button>
      <button class="plan-tab" data-plan="unit">Unit Plan</button>
    </div>
    <div class="plan-viewer">
      <div class="plan-image" id="planImage">
        <svg viewBox="0 0 400 300"><rect x="10" y="10" width="380" height="280" fill="none" stroke="#1C1B19" stroke-width="1.5"/><rect x="40" y="40" width="140" height="100" fill="none" stroke="#1C1B19" stroke-width="1"/><rect x="200" y="40" width="160" height="100" fill="none" stroke="#1C1B19" stroke-width="1"/><rect x="40" y="160" width="320" height="100" fill="none" stroke="#1C1B19" stroke-width="1"/><text x="200" y="20" text-anchor="middle" font-family="IBM Plex Mono" font-size="10" fill="#6B675E">GROUND FLOOR — LOBBY &amp; DRIVEWAY</text><circle cx="110" cy="90" r="3" fill="#C1622F"/><text x="120" y="94" font-family="IBM Plex Mono" font-size="9" fill="#6B675E">LOBBY</text><circle cx="280" cy="90" r="3" fill="#C1622F"/><text x="290" y="94" font-family="IBM Plex Mono" font-size="9" fill="#6B675E">LOUNGE</text><circle cx="200" cy="210" r="3" fill="#C1622F"/><text x="210" y="214" font-family="IBM Plex Mono" font-size="9" fill="#6B675E">DRIVEWAY / PARKING</text></svg>
      </div>
      <div class="plan-info">
        <h5 id="planTitle">Ground Floor</h5>
        <ul id="planList">
          <li><span>Function</span><span>Lobby &amp; driveway</span></li>
          <li><span>Ceiling height</span><span>12 ft</span></li>
          <li><span>Parking bays</span><span>10</span></li>
          <li><span>Generator room</span><span>Included</span></li>
        </ul>
      </div>
    </div>
  </div>
</section>

<section class="video-section" id="video">
  <div class="container">
    <span class="idx">PROPERTY VIDEO</span>
    <h2 class="property-title" style="font-size:clamp(1.8rem,3vw,2.4rem); margin-bottom:24px;">Behind The Framework</h2>
    <div class="video-frame" id="videoTrigger">
      <img src="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1400&q=80" alt="Beverly Residence exterior still from property video">
      <div class="scrim"><div class="play-ring"><i class="fa-solid fa-play"></i></div><h5>Watch the construction story</h5></div>
    </div>
  </div>
</section>

<section class="location-section" id="location">
  <div class="container">
    <span class="idx">LOCATION</span>
    <h2 class="property-title" style="font-size:clamp(1.8rem,3vw,2.4rem); margin-bottom:24px;">Find Us In Gulshan</h2>
    <div class="location-grid">
      <div class="addr-card">
        <h5>bti Beverly Residence</h5>
        <p>Plot # 17/A, Road # 126, Gulshan, Dhaka 1212</p>
        <p class="mono" style="margin-top:16px; color:var(--copper); font-size:.8rem;">NEAREST LANDMARK</p>
        <p>AMZ Hospital — 4 min walk</p>
        <div style="display:flex; gap:10px; margin-top:20px;">
          <a href="#" class="btn-line" style="font-size:.76rem; padding:9px 16px;"><i class="fa-solid fa-directions"></i> Directions</a>
          <a href="#" class="btn-copper" style="font-size:.76rem; padding:9px 16px;"><i class="fa-solid fa-phone"></i> Call</a>
        </div>
      </div>
      <div class="map-embed">
        <iframe src="https://www.google.com/maps?q=Gulshan%201%20Road%20126%20Dhaka&output=embed" loading="lazy" title="Beverly Residence location map"></iframe>
      </div>
    </div>
  </div>
</section>

<section class="enquiry-section" id="enquiry">
  <div class="container enquiry-grid">
    <div class="enquiry-form">
      <span class="idx">ENQUIRE</span>
      <h2 class="property-title" style="font-size:clamp(1.8rem,3vw,2.4rem); margin-bottom:8px;">I'm Interested In This Property</h2>
      <p style="margin-bottom:28px;">Our team responds to every enquiry within one business day.</p>
      <form id="enquiryForm" onsubmit="return handleEnquirySubmit(event);">
        <div class="field"><label>Name</label><input type="text" required></div>
        <div class="field"><label>Email Address</label><input type="email" required></div>
        <div class="field"><label>Phone Number</label><input type="tel" required></div>
        <div class="field"><label>Message</label><textarea rows="3">I am interested in your property named "Beverly Residence".</textarea></div>
        <button type="submit" class="btn-copper">Submit <i class="fa-solid fa-arrow-right"></i></button>
      </form>
    </div>
    <div class="enquiry-note">
      <h5>Why buyers choose Beverly Residence</h5>
      <p style="margin-bottom:10px;">— A double-height entrance and furnished meet-and-greet lobby for complete privacy on arrival.</p>
      <p style="margin-bottom:10px;">— One 3,000+ sqft apartment per floor, so neighbours are never a corridor away.</p>
      <p style="margin-bottom:10px;">— A patterned rooftop walkway designed as a destination, not an afterthought.</p>
      <p>— Walking distance to Gulshan's restaurants, hospitals and schools.</p>
    </div>
  </div>
</section>



<script>
document.addEventListener('DOMContentLoaded', () => {
  /* Hero thumbnail switcher */
  const heroMainImg = document.getElementById('heroMainImg');
  document.querySelectorAll('.thumb-rail .thumb').forEach(t => {
    t.addEventListener('click', () => {
      document.querySelectorAll('.thumb-rail .thumb').forEach(x => x.classList.remove('active'));
      t.classList.add('active');
      if (heroMainImg) heroMainImg.src = t.dataset.src;
    });
  });

  /* Floor plan tabs */
  const planData = {
    ground:{ title:'Ground Floor', rows:[['Function','Lobby & driveway'],['Ceiling height','12 ft'],['Parking bays','10'],['Generator room','Included']], label:'GROUND FLOOR — LOBBY & DRIVEWAY' },
    typical:{ title:'Typical Floor', rows:[['Function','Single 3-bed apartment'],['Carpet area','3,050 sft'],['Balconies','2'],['Orientation','North-East']], label:'TYPICAL FLOOR — 1 APARTMENT' },
    roof:{ title:'Roof Floor', rows:[['Function','Rooftop walkway & lounge'],['Open area','1,800 sft'],['Features','Pool deck, garden'],['Access','Private lift']], label:'ROOF FLOOR — AMENITY DECK' },
    unit:{ title:'Unit Plan', rows:[['Bedrooms','3'],['Bathrooms','3'],['Living + dining','620 sft'],['Kitchen','180 sft']], label:'UNIT PLAN — 3 BED APARTMENT' }
  };
  document.querySelectorAll('.plan-tab').forEach(tab => {
    tab.addEventListener('click', () => {
      document.querySelectorAll('.plan-tab').forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      const data = planData[tab.dataset.plan];
      const planTitle = document.getElementById('planTitle');
      const planList = document.getElementById('planList');
      const planImageText = document.querySelector('#planImage svg text');

      if (planTitle) planTitle.textContent = data.title;
      if (planList) planList.innerHTML = data.rows.map(r => `<li><span>${r[0]}</span><span>${r[1]}</span></li>`).join('');
      if (planImageText) planImageText.textContent = data.label;
    });
  });

  /* Video trigger -> open YouTube in a lightweight lightbox */
  const videoTrigger = document.getElementById('videoTrigger');
  if (videoTrigger) {
    videoTrigger.addEventListener('click', function(){
      const overlay = document.createElement('div');
      overlay.style.cssText = 'position:fixed;inset:0;background:rgba(28,27,25,.94);z-index:500;display:flex;align-items:center;justify-content:center;padding:24px;';
      overlay.innerHTML = `<div style="width:min(900px,100%);aspect-ratio:16/9;position:relative;">
        <iframe style="width:100%;height:100%;border:0;" src="https://www.youtube.com/embed/j-3nl88O7uI?autoplay=1&rel=0" allow="autoplay; encrypted-media" allowfullscreen></iframe>
      </div>
      <span style="position:absolute;top:24px;right:32px;color:#fff;font-size:1.8rem;cursor:pointer;" id="closeVid">&times;</span>`;
      document.body.appendChild(overlay);
      overlay.addEventListener('click', (e) => { if(e.target === overlay || e.target.id==='closeVid') overlay.remove(); });
    });
  }

  const form = document.getElementById('enquiryForm');
  if (form) {
    form.addEventListener('submit', function(e) {
      e.preventDefault();
      form.innerHTML = `<div style="padding:20px 0;"><i class="fa-solid fa-circle-check" style="color:#C1622F;font-size:2rem;"></i><h5 style="font-family:var(--font-display);margin-top:12px;">Thank you</h5><p style="font-size:.9rem;">We'll be in touch within one business day.</p></div>`;
      return false;
    });
  }
});
</script>
</div>
@endsection
