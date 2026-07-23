@extends('frontend.layouts.app')
@section('content')

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="container">
    <h1 data-aos="fade-up">{{ setting('about_title', 'About ' . setting('site_title', 'Aurelia Estates')) }}</h1>
    <p data-aos="fade-up" data-aos-delay="100">{{ setting('about_subtitle', 'Two decades of excellence in luxury real estate.') }}</p>
  </div>
</div>

<!-- ABOUT MAIN SECTION -->
<section id="about">
  <div class="blueprint-bg"></div>
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6" data-aos="fade-right">
        <div class="about-img-wrap">
          <img src="{{ asset_url(setting('about_image'), 'https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?auto=format&fit=crop&w=900&q=75') }}" class="rounded-24" loading="lazy" alt="{{ setting('about_heading') }}">
          <div class="glass about-badge-card"><strong>{{ setting('about_badge_number', '20+') }}</strong> {{ setting('about_badge_text', 'Years shaping city skylines') }}</div>
        </div>
      </div>
      <div class="col-lg-6" data-aos="fade-left">
        <span class="eyebrow">{{ setting('about_eyebrow', 'About ' . setting('site_title', 'Aurelia Estates')) }}</span>
        <h2 class="section-title">{{ setting('about_heading', 'Building More Than Properties — Building Trust') }}</h2>
        <p class="mt-3">{{ setting('about_description', 'Since 2010, ' . setting('site_title', 'Aurelia Estates') . ' has delivered over 500 residential and commercial projects across the country, guided by one principle: every buyer deserves clarity, quality, and a home worth the investment.') }}</p>
        
        <div class="row g-3 my-3">
          <div class="col-6">
            <h6 class="fw-bold mb-1"><i class="fa-solid fa-bullseye text-primary me-2"></i>{{ setting('about_mission_title', 'Our Mission') }}</h6>
            <p style="font-size:.9rem;">{{ setting('about_mission_text', 'Make premium real estate accessible through transparent, verified transactions.') }}</p>
          </div>
          <div class="col-6">
            <h6 class="fw-bold mb-1"><i class="fa-solid fa-eye text-primary me-2"></i>{{ setting('about_vision_title', 'Our Vision') }}</h6>
            <p style="font-size:.9rem;">{{ setting('about_vision_text', 'To be the most trusted name in luxury real estate development nationwide.') }}</p>
          </div>
        </div>

        <div class="row g-3 text-center">
          <div class="col-4">
            <strong class="d-block fs-4 fw-bold" data-count="{{ setting('about_stat1_number', 500) }}" data-suffix="{{ setting('about_stat1_suffix', '+') }}">0</strong>
            <span style="font-size:.8rem;color:var(--ink-soft);">{{ setting('about_stat1_label', 'Projects') }}</span>
          </div>
          <div class="col-4">
            <strong class="d-block fs-4 fw-bold" data-count="{{ setting('about_stat2_number', 12000) }}" data-suffix="{{ setting('about_stat2_suffix', '+') }}">0</strong>
            <span style="font-size:.8rem;color:var(--ink-soft);">{{ setting('about_stat2_label', 'Customers') }}</span>
          </div>
          <div class="col-4">
            <strong class="d-block fs-4 fw-bold" data-count="{{ setting('about_stat4_number', 98) }}" data-suffix="{{ setting('about_stat4_suffix', '%') }}">0</strong>
            <span style="font-size:.8rem;color:var(--ink-soft);">{{ setting('about_stat4_label', 'Satisfaction') }}</span>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- JOURNEY / TIMELINE -->
<section style="background:linear-gradient(180deg,transparent,rgba(212,175,55,.06),transparent);">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Our Journey</span>
      <h2 class="section-title">Two Decades Of Milestones</h2>
    </div>
    <div class="journey-line">
      @forelse($journeys as $index => $journey)
      <div class="journey-item" data-aos="{{ $index % 2 == 0 ? 'fade-right' : 'fade-left' }}">
        <div class="journey-dot"></div>
        <div class="journey-year">{{ $journey->year }}</div>
        <h5>{{ $journey->title }}</h5>
        <p>{{ $journey->description }}</p>
      </div>
      @empty
      <div class="text-center text-muted py-4">No milestone timeline added yet.</div>
      @endforelse
    </div>
  </div>
</section>

<!-- TEAM -->
<section style="background:linear-gradient(180deg,transparent,rgba(37,99,235,.05),transparent);">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Our Team</span>
      <h2 class="section-title">The People Behind {{ setting('site_title', 'Aurelia') }}</h2>
    </div>
    <div class="row g-4">
      @forelse($teamMembers as $index => $member)
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($index % 4) * 80 }}">
        <div class="team-card">
          <div class="team-img-wrap">
            <img src="{{ asset_url($member->image) }}" loading="lazy" alt="{{ $member->name }}">
            <div class="team-social">
              @if($member->linkedin)<a href="{{ $member->linkedin }}" target="_blank"><i class="fa-brands fa-linkedin-in"></i></a>@endif
              @if($member->email)<a href="mailto:{{ $member->email }}"><i class="fa-solid fa-envelope"></i></a>@endif
            </div>
          </div>
          <div class="team-body">
            <h5>{{ $member->name }}</h5>
            <span>{{ $member->designation }}</span>
          </div>
        </div>
      </div>
      @empty
      <div class="col-12 text-center text-muted py-4">No team members listed yet.</div>
      @endforelse
    </div>
  </div>
</section>

<!-- STATS COUNTER -->
<section class="stats-section">
  <div class="container">
    <div class="row g-4">
      <div class="col-6 col-md-3" data-aos="fade-up">
        <div class="stat-block">
          <strong data-count="{{ setting('about_stat1_number', 500) }}" data-suffix="{{ setting('about_stat1_suffix', '+') }}">0</strong>
          <span>{{ setting('about_stat1_label', 'Projects') }}</span>
        </div>
      </div>
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="80">
        <div class="stat-block">
          <strong data-count="{{ setting('about_stat2_number', 12000) }}" data-suffix="{{ setting('about_stat2_suffix', '+') }}">0</strong>
          <span>{{ setting('about_stat2_label', 'Customers') }}</span>
        </div>
      </div>
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="160">
        <div class="stat-block">
          <strong data-count="{{ setting('about_stat3_number', 350) }}" data-suffix="{{ setting('about_stat3_suffix', '+') }}">0</strong>
          <span>{{ setting('about_stat3_label', 'Employees') }}</span>
        </div>
      </div>
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="240">
        <div class="stat-block">
          <strong data-count="{{ setting('about_stat4_number', 98) }}" data-suffix="{{ setting('about_stat4_suffix', '%') }}">0</strong>
          <span>{{ setting('about_stat4_label', 'Client Satisfaction') }}</span>
        </div>
      </div>
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
