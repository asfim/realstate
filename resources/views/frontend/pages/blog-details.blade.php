@extends('frontend.layouts.app')
@section('content')

<!-- PAGE HEADER -->
<div class="page-header" style="padding: 60px 0 40px;">
  <div class="container">
    <div class="mb-2">
      <a href="{{ route('blog') }}" class="text-gold text-decoration-none small fw-bold">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Blog
      </a>
    </div>
    <h1 data-aos="fade-up" style="font-size:2.2rem;">{{ $post->title }}</h1>
    <div class="d-flex align-items-center gap-3 text-muted small mt-3" data-aos="fade-up" data-aos-delay="100">
      <span><i class="fa-regular fa-user me-1 text-gold"></i> {{ $post->author }}</span>
      <span><i class="fa-regular fa-calendar-alt me-1 text-gold"></i> {{ optional($post->published_at)->format('M d, Y') }}</span>
    </div>
  </div>
</div>

<section style="padding: 50px 0;">
  <div class="container">
    <div class="row g-5">
      <div class="col-lg-8">
        @if($post->image)
        <div class="mb-4 rounded-24 overflow-hidden shadow-sm" data-aos="fade-up">
          <img src="{{ asset_url($post->image) }}" alt="{{ $post->title }}" style="width: 100%; max-height: 450px; object-fit: cover;">
        </div>
        @endif

        @if($post->summary)
        <div class="p-4 rounded-3 mb-4 border-start border-4 border-gold bg-light fs-5 fst-italic text-secondary" data-aos="fade-up">
          "{{ $post->summary }}"
        </div>
        @endif

        <div class="blog-content fs-6 text-dark" style="line-height: 1.8;" data-aos="fade-up">
          {!! nl2br(e($post->content)) !!}
        </div>
      </div>

      <div class="col-lg-4">
        <div class="sticky-top" style="top: 100px;">
          <div class="card p-4 border-0 shadow-sm rounded-24 mb-4">
            <h5 class="fw-bold mb-3">Recent Insights</h5>
            <div class="d-flex flex-column gap-3">
              @foreach($recentPosts as $recent)
              <a href="{{ route('blog.details', $recent->slug) }}" class="text-decoration-none d-flex gap-3 align-items-center text-dark">
                @if($recent->image)
                  <img src="{{ asset_url($recent->image) }}" alt="Img" style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;">
                @endif
                <div>
                  <h6 class="mb-1 fw-semibold small lh-base text-truncate-2">{{ $recent->title }}</h6>
                  <span class="text-muted" style="font-size:0.75rem;">{{ optional($recent->published_at)->format('M d, Y') }}</span>
                </div>
              </a>
              @endforeach
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
@endsection
