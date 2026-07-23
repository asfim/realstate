@extends('frontend.layouts.app')

@section('content')
<div class="property-dossier" style="background:#F7F5F0; color:#1C1B19; padding-bottom: 60px;">
<div class="crumb-strip">
  <div class="container"><a href="{{ route('home') }}">Home</a> / <a href="{{ route('projects') }}">Projects</a> / <span>{{ $project->title ?? 'Featured Project' }}</span></div>
</div>

@php
    $gallery = $project->gallery ?? [
        'https://images.unsplash.com/photo-1600607687920-4e2a09cf159d?auto=format&fit=crop&w=1400&q=80',
        'https://images.unsplash.com/photo-1600607687644-c7171b42498b?auto=format&fit=crop&w=1400&q=80',
        'https://images.unsplash.com/photo-1600566753190-17f0baa2a6c3?auto=format&fit=crop&w=1400&q=80',
        'https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1400&q=80',
        'https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?auto=format&fit=crop&w=1400&q=80',
        'https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=1400&q=80'
    ];
    if($project->image && !in_array($project->image, $gallery)) {
        array_unshift($gallery, $project->image);
    }
@endphp

<section class="hero-collage container">
  <div class="hero-grid">
    <div class="hero-main">
      <span class="hero-tag mono">01 / OVERVIEW</span>
      <img id="heroMainImg" src="{{ asset_url($gallery[0]) }}" alt="{{ $project->title }}">
    </div>
    <div class="hero-side">
      @if(isset($gallery[1]))
      <div class="cell">
          <img src="{{ asset_url($gallery[1]) }}" alt="{{ $project->title }}">
          <div class="more mono">02</div>
      </div>
      @endif
      @if(isset($gallery[2]))
      <div class="cell">
          <img src="{{ asset_url($gallery[2]) }}" alt="{{ $project->title }}">
          <div class="more mono">+{{ count($gallery) > 2 ? count($gallery) - 2 : 1 }} more</div>
      </div>
      @endif
    </div>
  </div>
  <div class="thumb-rail" id="thumbRail">
    @foreach($gallery as $index => $imgUrl)
      @php $fullUrl = asset_url($imgUrl); @endphp
      <div class="thumb {{ $loop->first ? 'active' : '' }}" data-src="{{ $fullUrl }}">
        <img src="{{ $fullUrl }}" alt="Thumb">
        <span>0{{ $index + 1 }}</span>
      </div>
    @endforeach
  </div>
</section>

<section class="overview-section" id="overview">
  <div class="container overview-grid">
    <div>
      <span class="idx">OVERVIEW</span>
      <h1 class="property-title">{{ $project->title ?? 'Beverly Residence' }}</h1>
      <div class="property-loc"><i class="fa-solid fa-location-dot"></i> {{ $project->address ?? $project->location ?? 'Plot # 17/A, Road # 126, Gulshan, Dhaka' }}</div>
      <div class="overview-text">
        @if($project->description)
            <p>{{ $project->description }}</p>
        @else
            <p>Nestled in the vibrant center of Gulshan, {{ $project->title }} is a nine-storey address built around a double-height entrance and a furnished "meet and greet" lobby that keeps arrival private and unhurried.</p>
            <p>Each floor holds a single 3,000+ sqft, three-bedroom apartment, and the patterned rooftop turns the building's crown into its most memorable room — restaurants, hospitals and schools sit within easy reach on every side.</p>
        @endif
      </div>
      <div class="status-row">
        <div class="status-item"><span class="n mono">{{ $project->no_of_floors ?? 'G+9' }}</span><span class="l">Floors</span></div>
        <div class="status-item"><span class="n mono">{{ $project->apartments_per_floor ?? '1' }}</span><span class="l">Apt / Floor</span></div>
        <div class="status-item"><span class="n mono">{{ $project->beds ?? 3 }} / {{ $project->baths ?? 3 }}</span><span class="l">Bed / Bath</span></div>
        <div class="status-item"><span class="n mono">{{ $project->land_area ?? '6.43' }}</span><span class="l">Land Area</span></div>
      </div>
    </div>

    <div class="spec-plate">
      <div class="spec-plate-head"><h5>At a Glance</h5><span class="idx" style="margin:0;">SPEC</span></div>
      <div class="spec-row"><span class="k mono"><i class="fa-solid fa-location-dot"></i>Address</span><span class="v">{{ $project->address ?? $project->location }}</span></div>
      <div class="spec-row"><span class="k mono"><i class="fa-solid fa-ruler-combined"></i>Land Area</span><span class="v">{{ $project->land_area ?? '6.43 Katha' }}</span></div>
      <div class="spec-row"><span class="k mono"><i class="fa-solid fa-building"></i>No. of Floors</span><span class="v">{{ $project->no_of_floors ?? 'G + 9' }}</span></div>
      <div class="spec-row"><span class="k mono"><i class="fa-solid fa-table-cells"></i>Apt / Floor</span><span class="v">{{ $project->apartments_per_floor ?? '1' }}</span></div>
      <div class="spec-row"><span class="k mono"><i class="fa-solid fa-vector-square"></i>Apt Size</span><span class="v">{{ $project->area ? $project->area . ' sqft' : '3000+ sft' }}</span></div>
      <div class="spec-row"><span class="k mono"><i class="fa-solid fa-bed"></i>Bedroom</span><span class="v">{{ $project->beds ?? 3 }}</span></div>
      <div class="spec-row"><span class="k mono"><i class="fa-solid fa-bath"></i>Bathroom</span><span class="v">{{ $project->baths ?? 3 }}</span></div>
      <div class="spec-row"><span class="k mono"><i class="fa-solid fa-calendar"></i>Launch Date</span><span class="v">{{ $project->launch_date ?? 'May 2024' }}</span></div>
      <div class="spec-row"><span class="k mono"><i class="fa-solid fa-calendar-check"></i>Completion</span><span class="v">{{ $project->expected_completion ?? 'Mar 2027' }}</span></div>
      <div class="spec-actions">
        @if($project->brochure)
            <a href="{{ Storage::url($project->brochure) }}" target="_blank" class="btn-copper"><i class="fa-solid fa-download"></i>Download Brochure</a>
        @else
            <a href="#enquiry" class="btn-copper"><i class="fa-solid fa-envelope"></i>Enquire Now</a>
        @endif
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
        @php
          $gImg = isset($project->floor_plans['ground']['image']) ? (\Illuminate\Support\Str::startsWith($project->floor_plans['ground']['image'], ['http://', 'https://']) ? $project->floor_plans['ground']['image'] : Storage::url($project->floor_plans['ground']['image'])) : null;
        @endphp
        @if($gImg)
          <img src="{{ $gImg }}" alt="Ground Floor Plan" style="width: 100%; max-height: 320px; object-fit: contain; border-radius: 8px;">
        @else
          <svg viewBox="0 0 400 300"><rect x="10" y="10" width="380" height="280" fill="none" stroke="#1C1B19" stroke-width="1.5"/><rect x="40" y="40" width="140" height="100" fill="none" stroke="#1C1B19" stroke-width="1"/><rect x="200" y="40" width="160" height="100" fill="none" stroke="#1C1B19" stroke-width="1"/><rect x="40" y="160" width="320" height="100" fill="none" stroke="#1C1B19" stroke-width="1"/><text x="200" y="20" text-anchor="middle" font-family="IBM Plex Mono" font-size="10" fill="#6B675E">GROUND FLOOR — LOBBY &amp; DRIVEWAY</text><circle cx="110" cy="90" r="3" fill="#C1622F"/><text x="120" y="94" font-family="IBM Plex Mono" font-size="9" fill="#6B675E">LOBBY</text><circle cx="280" cy="90" r="3" fill="#C1622F"/><text x="290" y="94" font-family="IBM Plex Mono" font-size="9" fill="#6B675E">LOUNGE</text><circle cx="200" cy="210" r="3" fill="#C1622F"/><text x="210" y="214" font-family="IBM Plex Mono" font-size="9" fill="#6B675E">DRIVEWAY / PARKING</text></svg>
        @endif
      </div>
      <div class="plan-info">
        <h5 id="planTitle">Ground Floor</h5>
        @php
            $initialGroundRows = $project->floor_plans['ground']['info'] ?? [
                ['key' => 'Function', 'value' => 'Lobby & driveway'],
                ['key' => 'Ceiling height', 'value' => '12 ft'],
                ['key' => 'Parking bays', 'value' => '10'],
                ['key' => 'Generator room', 'value' => 'Included'],
            ];
        @endphp
        <ul id="planList">
          @foreach($initialGroundRows as $r)
            <li><span>{{ is_array($r) && isset($r['key']) ? $r['key'] : $r[0] }}</span><span>{{ is_array($r) && isset($r['value']) ? $r['value'] : $r[1] }}</span></li>
          @endforeach
        </ul>
      </div>
    </div>
  </div>
</section>

<section class="video-section" id="video">
  <div class="container">
    <span class="idx">PROPERTY VIDEO</span>
    <h2 class="property-title" style="font-size:clamp(1.8rem,3vw,2.4rem); margin-bottom:24px;">Behind The Framework</h2>
    @php
        $hasCustomPoster = !empty($project->video_thumbnail);
        $hasVideoFile = !empty($project->video_file);
        $youtubeThumbUrl = $project->youtube_thumbnail_url;
        $videoUrl = $project->formatted_video_url ?? 'https://www.youtube.com/embed/L_LUpnjgPso?autoplay=1&rel=0';
        $isFile = $hasVideoFile ? '1' : '0';

        $posterImageUrl = null;
        if ($hasCustomPoster) {
            $posterImageUrl = Storage::url($project->video_thumbnail);
        } elseif (!$hasVideoFile && $youtubeThumbUrl) {
            $posterImageUrl = $youtubeThumbUrl;
        } elseif (!$hasVideoFile) {
            $posterImageUrl = \Illuminate\Support\Str::startsWith($gallery[0], ['http://', 'https://']) ? $gallery[0] : Storage::url($gallery[0]);
        }
    @endphp
    <div class="video-frame" id="videoTrigger" data-video-url="{{ $videoUrl }}" data-is-file="{{ $isFile }}">
      @if(!$hasCustomPoster && $hasVideoFile)
        <video src="{{ Storage::url($project->video_file) }}#t=0.1" preload="metadata" muted style="width: 100%; height: 100%; object-fit: cover; display: block;"></video>
      @else
        <img src="{{ $posterImageUrl }}" alt="{{ $project->title }} video preview">
      @endif
      <div class="scrim"><div class="play-ring"><i class="fa-solid fa-play"></i></div><h5>Watch the project story</h5></div>
    </div>
  </div>
</section>

<section class="location-section" id="location">
  <div class="container">
    <span class="idx">LOCATION</span>
    <h2 class="property-title" style="font-size:clamp(1.8rem,3vw,2.4rem); margin-bottom:24px;">Find Us In {{ $project->location }}</h2>
    <div class="location-grid">
      <div class="addr-card">
        <h5>{{ $project->title }}</h5>
        <p>{{ $project->address ?? $project->location }}</p>
        <p class="mono" style="margin-top:16px; color:var(--copper); font-size:.8rem;">NEAREST LANDMARK</p>
        <p>{{ $project->nearest_landmark ?? 'AMZ Hospital — 4 min walk' }}</p>
        <div style="display:flex; gap:10px; margin-top:20px;">
          <a href="{{ $project->map_url ?? '#' }}" target="_blank" class="btn-line" style="font-size:.76rem; padding:9px 16px;"><i class="fa-solid fa-directions"></i> Directions</a>
          <a href="#enquiry" class="btn-copper" style="font-size:.76rem; padding:9px 16px;"><i class="fa-solid fa-phone"></i> Contact Us</a>
        </div>
      </div>
      <div class="map-embed">
        <iframe src="{{ $project->map_url && \Illuminate\Support\Str::contains($project->map_url, 'embed') ? $project->map_url : 'https://www.google.com/maps?q=' . urlencode($project->address ?? $project->location) . '&output=embed' }}" loading="lazy" title="{{ $project->title }} location map"></iframe>
      </div>
    </div>
  </div>
</section>

<section class="enquiry-section" id="enquiry">
  <div class="container enquiry-grid">
    <div class="enquiry-form">
      <span class="idx">ENQUIRE</span>
      <h2 class="property-title" style="font-size:clamp(1.8rem,3vw,2.4rem); margin-bottom:8px;">I'm Interested In This Project</h2>
      <p style="margin-bottom:28px;">Our team responds to every enquiry within one business day.</p>
      <form id="enquiryForm" onsubmit="return handleEnquirySubmit(event);">
        <div class="field"><label>Name</label><input type="text" required></div>
        <div class="field"><label>Email Address</label><input type="email" required></div>
        <div class="field"><label>Phone Number</label><input type="tel" required></div>
        <div class="field"><label>Message</label><textarea rows="3">I am interested in your project named "{{ $project->title }}".</textarea></div>
        <button type="submit" class="btn-copper">Submit <i class="fa-solid fa-arrow-right"></i></button>
      </form>
    </div>
    <div class="enquiry-note">
      <h5>Why buyers choose {{ $project->title }}</h5>
      <p style="margin-bottom:10px;">— Double-height entrance and luxury furnished lobby.</p>
      <p style="margin-bottom:10px;">— Spaciously designed {{ $project->beds }}-bedroom luxury units ({{ $project->area }} sqft).</p>
      <p style="margin-bottom:10px;">— Prime location at {{ $project->location }}.</p>
      <p>— Modern amenities and architectural refinement.</p>
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

  /* Category-wise Floor Plan images & Info Rows */
  @php
    $plansMap = [];
    $plansInfoMap = [];
    $cats = ['ground', 'typical', 'roof', 'unit'];
    foreach($cats as $c) {
        if(isset($project->floor_plans[$c]['image'])) {
            $img = $project->floor_plans[$c]['image'];
            $plansMap[$c] = \Illuminate\Support\Str::startsWith($img, ['http://', 'https://']) ? $img : Storage::url($img);
        }
        if(isset($project->floor_plans[$c]['info']) && is_array($project->floor_plans[$c]['info'])) {
            $rows = [];
            foreach($project->floor_plans[$c]['info'] as $row) {
                if(!empty($row['key'])) {
                    $rows[] = [$row['key'], $row['value'] ?? ''];
                }
            }
            if(!empty($rows)) {
                $plansInfoMap[$c] = $rows;
            }
        }
    }
  @endphp
  const categoryImages = @json($plansMap);
  const categoryInfoMap = @json($plansInfoMap);

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
      const catKey = tab.dataset.plan;
      const data = planData[catKey];
      const planTitle = document.getElementById('planTitle');
      const planList = document.getElementById('planList');
      const planImageContainer = document.getElementById('planImage');

      if (planTitle) planTitle.textContent = data.title;

      const activeRows = (categoryInfoMap && categoryInfoMap[catKey]) ? categoryInfoMap[catKey] : data.rows;
      if (planList) planList.innerHTML = activeRows.map(r => `<li><span>${r[0]}</span><span>${r[1]}</span></li>`).join('');

      if(categoryImages && categoryImages[catKey]) {
        planImageContainer.innerHTML = `<img src="${categoryImages[catKey]}" alt="${data.title}" style="width: 100%; max-height: 340px; object-fit: contain; border-radius: 8px;">`;
      } else {
        planImageContainer.innerHTML = `<svg viewBox="0 0 400 300"><rect x="10" y="10" width="380" height="280" fill="none" stroke="#1C1B19" stroke-width="1.5"/><rect x="40" y="40" width="140" height="100" fill="none" stroke="#1C1B19" stroke-width="1"/><rect x="200" y="40" width="160" height="100" fill="none" stroke="#1C1B19" stroke-width="1"/><rect x="40" y="160" width="320" height="100" fill="none" stroke="#1C1B19" stroke-width="1"/><text x="200" y="20" text-anchor="middle" font-family="IBM Plex Mono" font-size="10" fill="#6B675E">${data.label}</text><circle cx="110" cy="90" r="3" fill="#C1622F"/><text x="120" y="94" font-family="IBM Plex Mono" font-size="9" fill="#6B675E">LOBBY</text><circle cx="280" cy="90" r="3" fill="#C1622F"/><text x="290" y="94" font-family="IBM Plex Mono" font-size="9" fill="#6B675E">LOUNGE</text><circle cx="200" cy="210" r="3" fill="#C1622F"/><text x="210" y="214" font-family="IBM Plex Mono" font-size="9" fill="#6B675E">DRIVEWAY / PARKING</text></svg>`;
      }
    });
  });

  /* Video trigger */
  const videoTrigger = document.getElementById('videoTrigger');
  if (videoTrigger) {
    videoTrigger.addEventListener('click', function(){
      let videoSrc = this.getAttribute('data-video-url') || 'https://www.youtube.com/embed/L_LUpnjgPso?autoplay=1&rel=0';
      const isFile = this.getAttribute('data-is-file') === '1';

      if (!isFile && videoSrc) {
        if (videoSrc.includes('youtube.com/watch')) {
          try {
            const urlObj = new URL(videoSrc);
            const v = urlObj.searchParams.get('v');
            if (v) videoSrc = `https://www.youtube.com/embed/${v}?autoplay=1&rel=0`;
          } catch(e) {}
        } else if (videoSrc.includes('youtu.be/')) {
          const id = videoSrc.split('youtu.be/')[1].split('?')[0].split('/')[0];
          if (id) videoSrc = `https://www.youtube.com/embed/${id}?autoplay=1&rel=0`;
        }
      }

      const overlay = document.createElement('div');
      overlay.style.cssText = 'position:fixed;inset:0;background:rgba(0,0,0,.92);z-index:9999;display:flex;align-items:center;justify-content:center;padding:20px;';
      
      let playerContent = '';
      if(isFile) {
        playerContent = `<video style="width:100%;height:100%;max-height:85vh;border-radius:12px;outline:none;background:#000;" src="${videoSrc}" controls autoplay></video>`;
      } else {
        playerContent = `<iframe style="width:100%;height:100%;border:0;border-radius:12px;background:#000;" src="${videoSrc}" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" allowfullscreen></iframe>`;
      }

      overlay.innerHTML = `<div style="width:min(920px,95%);aspect-ratio:16/9;position:relative;background:#000;border-radius:12px;box-shadow:0 20px 50px rgba(0,0,0,0.8);">
        ${playerContent}
        <button type="button" id="closeVid" style="position:absolute;top:-45px;right:0;background:none;border:none;color:#fff;font-size:2.2rem;cursor:pointer;line-height:1;padding:4px;">&times;</button>
      </div>`;

      document.body.appendChild(overlay);

      overlay.addEventListener('click', (e) => {
        if(e.target === overlay || e.target.id === 'closeVid' || e.target.closest('#closeVid')) {
          overlay.remove();
        }
      });
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
