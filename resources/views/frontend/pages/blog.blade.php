@extends('frontend.layouts.app')
@section('content')

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="container">
    <h1 data-aos="fade-up">{{ setting('blog_title', 'News & Insights') }}</h1>
    <p data-aos="fade-up" data-aos-delay="100">{{ setting('blog_subtitle', 'Latest updates from the world of luxury real estate.') }}</p>
  </div>
</div>

<!-- BLOG SECTION -->
<section id="blog">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">{{ setting('blog_eyebrow', 'Latest Insights') }}</span>
      <h2 class="section-title">{{ setting('blog_heading', 'From Our Blog') }}</h2>
    </div>
    <div class="row g-4">
      @forelse($posts as $index => $post)
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="{{ ($index % 3) * 80 }}">
        <div class="blog-card h-100 d-flex flex-column">
          <div class="blog-img">
            <img src="{{ asset_url($post->image, 'https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=700&q=75') }}" loading="lazy" alt="{{ $post->title }}">
          </div>
          <div class="blog-body flex-grow-1 d-flex flex-column justify-content-between p-4">
            <div>
              <span class="blog-date text-muted small d-block mb-2">
                <i class="fa-regular fa-calendar-alt me-1"></i> {{ optional($post->published_at)->format('M d, Y') }}
              </span>
              <h5 class="fw-bold text-dark">{{ $post->title }}</h5>
              <p class="text-muted small mt-2 mb-3">{{ Str::limit($post->summary, 110) }}</p>
            </div>
            <a href="{{ route('blog.details', $post->slug) }}" class="read-more fw-bold text-gold">
              Read More <i class="fa-solid fa-arrow-right ms-1"></i>
            </a>
          </div>
        </div>
      </div>
      @empty
      <div class="col-12 text-center text-muted py-5">
        <p>No blog posts published yet.</p>
      </div>
      @endforelse
    </div>

    @if($posts->hasPages())
      <div class="d-flex justify-content-center mt-5">
        {{ $posts->links() }}
      </div>
    @endif
  </div>
</section>

<!-- FAQ SECTION -->
<section style="background:linear-gradient(180deg,transparent,rgba(212,175,55,.06),transparent);">
  <div class="container" style="max-width:820px;">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">{{ setting('faq_eyebrow', 'FAQ') }}</span>
      <h2 class="section-title">{{ setting('faq_heading', 'Frequently Asked Questions') }}</h2>
    </div>
    <div class="accordion" id="faqAccordion" data-aos="fade-up">
      @forelse($faqs as $index => $faq)
      <div class="accordion-item mb-3 border rounded-3 overflow-hidden">
        <h2 class="accordion-header" id="heading{{ $faq->id }}">
          <button class="accordion-button {{ $index !== 0 ? 'collapsed' : '' }} fw-bold" type="button" data-bs-toggle="collapse" data-bs-target="#faq{{ $faq->id }}" aria-expanded="{{ $index === 0 ? 'true' : 'false' }}" aria-controls="faq{{ $faq->id }}">
            {{ $faq->question }}
          </button>
        </h2>
        <div id="faq{{ $faq->id }}" class="accordion-collapse collapse {{ $index === 0 ? 'show' : '' }}" aria-labelledby="heading{{ $faq->id }}" data-bs-parent="#faqAccordion">
          <div class="accordion-body text-secondary" style="line-height:1.7;">
            {{ $faq->answer }}
          </div>
        </div>
      </div>
      @empty
      <div class="text-center text-muted py-4">No FAQs added yet.</div>
      @endforelse
    </div>
  </div>
</section>

@endsection
