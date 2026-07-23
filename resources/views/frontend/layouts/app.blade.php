<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>@yield('title', setting('meta_title', setting('site_title', 'Aurelia Estates | Find Your Dream Property')))</title>
<meta name="description" content="{{ setting('meta_description', 'Aurelia Estates — luxury real estate developer offering premium apartments, villas and commercial properties with verified listings, easy installments and legal support.') }}">
@if(setting('meta_keywords'))
<meta name="keywords" content="{{ setting('meta_keywords') }}">
@endif
@if(setting('site_favicon'))
<link rel="icon" href="{{ asset_url(setting('site_favicon')) }}">
@endif

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&family=Manrope:wght@600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">
<link rel="stylesheet" href="{{ asset('css/style.css') }}">
</head>
<body>

<div id="preloader"><div class="loader-mark">{{ strtoupper(setting('site_title', 'AURELIA ESTATES')) }}<div class="loader-bar"></div></div></div>
<div id="scrollProgress"></div>
<div class="cursor-dot" id="cursorDot"></div>
<div class="cursor-ring" id="cursorRing"></div>

@include('frontend.partials.header')

<main>
    @yield('content')
</main>

@include('frontend.partials.footer')

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  AOS.init({ duration: 700, once: true, offset: 60 });
  document.getElementById('year').textContent = new Date().getFullYear();

  /* Preloader */
  setTimeout(() => {
    const pl = document.getElementById('preloader');
    if(pl) pl.classList.add('hide');
  }, 300);

  /* Scroll progress + navbar + back to top */
  const progress = document.getElementById('scrollProgress');
  const nav = document.getElementById('mainNav');
  const backToTop = document.getElementById('backToTop');
  window.addEventListener('scroll', () => {
    const h = document.documentElement;
    const scrolled = (h.scrollTop) / (h.scrollHeight - h.clientHeight) * 100;
    if(progress) progress.style.width = scrolled + '%';
    if(nav) nav.classList.toggle('scrolled', window.scrollY > 40);
    if(backToTop) backToTop.classList.toggle('show', window.scrollY > 600);
  }, { passive: true });
  if(backToTop) backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

  /* Custom cursor */
  const dot = document.getElementById('cursorDot');
  const ring = document.getElementById('cursorRing');
  window.addEventListener('mousemove', (e) => {
    if(dot){ dot.style.left = e.clientX + 'px'; dot.style.top = e.clientY + 'px'; }
    if(ring){ ring.style.left = e.clientX + 'px'; ring.style.top = e.clientY + 'px'; }
  });
  document.querySelectorAll('a, button, .gallery-item, .project-card').forEach(el => {
    el.addEventListener('mouseenter', () => { if(ring){ ring.style.width='54px'; ring.style.height='54px'; ring.style.borderColor='rgba(212,175,55,.9)'; } });
    el.addEventListener('mouseleave', () => { if(ring){ ring.style.width='36px'; ring.style.height='36px'; ring.style.borderColor='rgba(212,175,55,.6)'; } });
  });

  /* Mouse parallax on hero floating shapes */
  const shapes = document.querySelectorAll('.floating-shape');
  if(shapes.length > 0) {
    const hero = document.querySelector('.hero');
    if(hero) {
      hero.addEventListener('mousemove', (e) => {
        const x = (e.clientX / window.innerWidth - 0.5) * 30;
        const y = (e.clientY / window.innerHeight - 0.5) * 30;
        shapes.forEach((s, i) => { s.style.transform = `translate(${x * (i+1)}px, ${y * (i+1)}px)`; });
      });
    }
  }

  /* Hero video graceful fallback */
  const video = document.getElementById('heroVideo');
  if(video) {
    video.addEventListener('canplay', () => video.classList.add('loaded'));
    video.addEventListener('error', () => video.style.display = 'none');
  }

  /* Counters */
  const counters = document.querySelectorAll('[data-count]');
  if(counters.length > 0) {
    const counterObserver = new IntersectionObserver((entries) => {
      entries.forEach(entry => {
        if (!entry.isIntersecting) return;
        const el = entry.target;
        const target = parseFloat(el.getAttribute('data-count'));
        const suffix = el.getAttribute('data-suffix') || '';
        const duration = 1600;
        const start = performance.now();
        function tick(now) {
          const progress = Math.min((now - start) / duration, 1);
          const eased = 1 - Math.pow(1 - progress, 3);
          el.textContent = Math.floor(target * eased).toLocaleString('en-US') + suffix;
          if (progress < 1) requestAnimationFrame(tick);
        }
        requestAnimationFrame(tick);
        counterObserver.unobserve(el);
      });
    }, { threshold: 0.5 });
    counters.forEach(c => counterObserver.observe(c));
  }
});

/* Lightbox functions */
function openLightbox(el){
  const overlay = document.getElementById('lightboxOverlay');
  const img = document.getElementById('lightboxImg');
  if(overlay && img) {
    img.src = el.querySelector('img').src;
    overlay.classList.add('show');
    document.body.style.overflow = 'hidden';
  }
}
function closeLightbox(){
  const overlay = document.getElementById('lightboxOverlay');
  if(overlay) {
    overlay.classList.remove('show');
    document.body.style.overflow = 'auto';
  }
}
function handleContactSubmit(e){
  e.preventDefault();
  const btn = e.target.querySelector('button[type="submit"]');
  if(!btn) return false;
  const origText = btn.textContent;
  btn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Sending...';
  btn.disabled = true;
  setTimeout(() => {
    btn.textContent = 'Enquiry Sent Successfully!';
    btn.classList.replace('btn-gold', 'btn-navy');
    e.target.reset();
    setTimeout(() => {
      btn.textContent = origText;
      btn.classList.replace('btn-navy', 'btn-gold');
      btn.disabled = false;
    }, 3000);
  }, 1200);
  return false;
}
</script>
@stack('scripts')
</body>
</html>
