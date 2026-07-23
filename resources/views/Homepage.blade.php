<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Aurelia Estates | Find Your Dream Property</title>
<meta name="description" content="Aurelia Estates — luxury real estate developer offering premium apartments, villas and commercial properties with verified listings, easy installments and legal support.">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@500;600;700;800&family=Inter:wght@400;500;600&family=Manrope:wght@600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.css">

<style>
:root{
  --navy:#0F172A;
  --navy-soft:#1E293B;
  --gold:#D4AF37;
  --gold-soft:#F1D98B;
  --blue:#2563EB;
  --bg:#F8FAFC;
  --white:#FFFFFF;
  --ink-soft:#64748B;
  --radius:24px;
  --radius-sm:14px;
  --shadow-soft:0 20px 50px -18px rgba(15,23,42,.15);
  --shadow-lift:0 30px 70px -20px rgba(15,23,42,.25);
  --font-head:'Poppins',sans-serif;
  --font-body:'Inter',sans-serif;
  --font-btn:'Manrope',sans-serif;
  --ease:cubic-bezier(.22,1,.36,1);
}
*{ box-sizing:border-box; }
html{ scroll-behavior:smooth; }
body{ font-family:var(--font-body); background:var(--bg); color:var(--navy); overflow-x:hidden; line-height:1.7; cursor:default; }
h1,h2,h3,h4,h5{ font-family:var(--font-head); font-weight:700; letter-spacing:-.01em; }
p{ color:var(--ink-soft); }
img{ max-width:100%; }
a{ text-decoration:none; }
.btn{ font-family:var(--font-btn); font-weight:700; border-radius:100px; padding:.9rem 2rem; transition:transform .35s var(--ease), box-shadow .35s var(--ease); }
.btn-gold{ background:linear-gradient(135deg,var(--gold-soft),var(--gold)); color:var(--navy); border:none; box-shadow:0 12px 30px -10px rgba(212,175,55,.6); }
.btn-gold:hover{ transform:translateY(-3px); box-shadow:0 18px 40px -10px rgba(212,175,55,.75); color:var(--navy); }
.btn-outline-light-glass{ background:rgba(255,255,255,.08); border:1.5px solid rgba(255,255,255,.5); color:#fff; backdrop-filter:blur(8px); }
.btn-outline-light-glass:hover{ background:rgba(255,255,255,.18); transform:translateY(-3px); color:#fff; }
.btn-navy{ background:var(--navy); color:#fff; }
.btn-navy:hover{ background:var(--navy-soft); transform:translateY(-3px); color:#fff; }
section{ padding:clamp(64px,8vw,120px) 0; position:relative; }
.eyebrow{ font-family:var(--font-btn); font-size:.78rem; font-weight:700; letter-spacing:.14em; text-transform:uppercase; color:var(--blue); display:inline-flex; align-items:center; gap:8px; }
.eyebrow::before{ content:""; width:26px; height:2px; background:var(--gold); display:inline-block; }
.section-title{ font-size:clamp(1.8rem,3.4vw,2.7rem); margin-top:12px; }
.glass{ background:rgb(255 255 255 / 88%); backdrop-filter:blur(18px); -webkit-backdrop-filter:blur(18px); border:1px solid rgba(255,255,255,.5); border-radius:var(--radius); box-shadow:inset -6px -4px 18px 8px rgba(15, 23, 42, .15); }
.rounded-24{ border-radius:var(--radius) !important; }
@media (prefers-reduced-motion:reduce){ *{ animation-duration:.001ms !important; transition-duration:.001ms !important; scroll-behavior:auto !important; } }

/* Loader */
#preloader{ position:fixed; inset:0; z-index:2000; background:var(--navy); display:flex; align-items:center; justify-content:center; transition:opacity .6s var(--ease), visibility .6s; }
#preloader.hide{ opacity:0; visibility:hidden; }
.loader-mark{ font-family:var(--font-head); color:#fff; font-size:1.6rem; letter-spacing:.08em; display:flex; flex-direction:column; align-items:center; gap:14px; }
.loader-bar{ width:160px; height:2px; background:rgba(255,255,255,.15); border-radius:2px; overflow:hidden; }
.loader-bar::after{ content:""; display:block; height:100%; width:40%; background:var(--gold); animation:loaderMove 1.1s ease-in-out infinite; }
@keyframes loaderMove{ 0%{ transform:translateX(-100%);} 100%{ transform:translateX(340%);} }

/* Scroll progress */
#scrollProgress{ position:fixed; top:0; left:0; height:3px; background:linear-gradient(90deg,var(--gold),var(--blue)); z-index:1500; width:0%; transition:width .1s linear; }

/* Custom cursor */
.cursor-dot{ position:fixed; top:0; left:0; width:10px; height:10px; background:var(--gold); border-radius:50%; pointer-events:none; z-index:1600; transform:translate(-50%,-50%); transition:transform .15s ease, opacity .3s; display:none; }
.cursor-ring{ position:fixed; top:0; left:0; width:36px; height:36px; border:1.5px solid rgba(212,175,55,.6); border-radius:50%; pointer-events:none; z-index:1599; transform:translate(-50%,-50%); transition:transform .12s linear, width .25s, height .25s, border-color .25s; display:none; }
@media (hover:hover) and (pointer:fine){ .cursor-dot, .cursor-ring{ display:block; } }

/* Navbar */
.navbar-luxury{ position:fixed; inset-inline:0; top:0; z-index:200; padding:22px 0; transition:background .4s var(--ease), padding .4s var(--ease), box-shadow .4s; }
.navbar-luxury.scrolled{ background:rgba(15,23,42,.92); backdrop-filter:blur(14px); padding:12px 0; box-shadow:0 10px 30px rgba(15,23,42,.2); }
.navbar-luxury .navbar-brand{ font-family:var(--font-head); font-weight:800; font-size:1.4rem; color:#fff; }
.navbar-luxury .navbar-brand span{ color:var(--gold); }
.navbar-luxury .nav-link{ color:rgba(255,255,255,.85); font-weight:500; font-size:.94rem; margin-inline:6px; position:relative; }
.navbar-luxury .nav-link::after{ content:""; position:absolute; left:0; bottom:-2px; width:0; height:2px; background:var(--gold); transition:width .3s var(--ease); }
.navbar-luxury .nav-link:hover::after, .navbar-luxury .nav-link.active::after{ width:100%; }
.navbar-luxury .nav-link:hover, .navbar-luxury .nav-link.active{ color:#fff; }
.navbar-toggler{ border:none; color:#fff; }

/* Hero */
.hero{ min-height:100vh; position:relative; display:flex; align-items:center; overflow:hidden; }
.hero-bg{ position:absolute; inset:0; background-color:#0F172A; }
.hero-video{ position:absolute; inset:0; width:100%; height:100%; object-fit:cover; opacity:1; }
.hero-video.loaded{ opacity:1; }
.hero-overlay{ position:absolute; inset:0; background:linear-gradient(180deg, rgba(15,23,42,.75) 0%, rgba(15,23,42,.55) 45%, rgba(15,23,42,.9) 100%); }
.hero-content{ position:relative; z-index:3; color:#fff; padding-top:60px; }
.hero-content .eyebrow{ color:var(--gold-soft); }
.hero-content .eyebrow::before{ background:var(--gold-soft); }
.hero-title{ font-size:clamp(2.4rem,6vw,4.4rem); line-height:1.1; margin:18px 0 22px; }
.hero-title .accent{ background:linear-gradient(135deg,var(--gold-soft),var(--gold)); -webkit-background-clip:text; background-clip:text; color:transparent; }
.hero-sub{ color:rgba(255,255,255,.82); font-size:1.1rem; max-width:560px; margin-bottom:36px; }
.hero-cta{ display:flex; gap:18px; flex-wrap:wrap; margin-bottom:56px; }

.floating-stats{ position:relative; z-index:3; }
.stat-card{ padding:22px 24px; color:#fff; }
.stat-card strong{ display:block; font-family:var(--font-head); font-size:2rem; color:#000000; }
.stat-card span{ font-size:.86rem; color:rgb(0 0 0 / 75%); }

.floating-shape{ position:absolute; border-radius:50%; filter:blur(2px); pointer-events:none; z-index:1; }
.fs1{ width:220px; height:220px; background:radial-gradient(circle,rgba(212,175,55,.35),transparent 70%); top:10%; right:8%; }
.fs2{ width:160px; height:160px; background:radial-gradient(circle,rgba(37,99,235,.35),transparent 70%); bottom:18%; left:6%; }

.scroll-indicator{ position:absolute; bottom:28px; left:50%; transform:translateX(-50%); z-index:3; color:#fff; text-align:center; font-size:.75rem; letter-spacing:.1em; opacity:.8; }
.scroll-indicator .mouse{ width:24px; height:38px; border:2px solid rgba(255,255,255,.6); border-radius:14px; margin:8px auto 0; position:relative; }
.scroll-indicator .mouse::after{ content:""; position:absolute; top:6px; left:50%; transform:translateX(-50%); width:4px; height:8px; background:var(--gold); border-radius:2px; animation:scrollDot 1.8s ease-in-out infinite; }
@keyframes scrollDot{ 0%{ opacity:1; top:6px;} 100%{ opacity:0; top:20px;} }

/* Search bar */
.search-panel{ position:relative; z-index:10; margin-top:-72px; }
.search-panel .glass{ padding:28px; }
.search-panel label{ font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em; color:#202020; margin-bottom:6px; display:block; }
.search-panel .form-select, .search-panel .form-control{ border-radius:12px; border:1.5px solid rgb(50 12 0 / 32%); padding:.7rem .9rem; }
.search-panel .form-select:focus, .search-panel .form-control:focus{ border-color:var(--gold); box-shadow:0 0 0 4px rgba(212,175,55,.15); }
.search-btn{ width:100%; height:100%; }

/* Project cards */
.project-card{ border-radius:var(--radius); overflow:hidden; background:#e7e7e7; box-shadow:var(--shadow-soft); transition:transform .45s var(--ease), box-shadow .45s var(--ease); height:100%; }
.project-card:hover{ transform:translateY(-10px); box-shadow:var(--shadow-lift); }
.project-img-wrap{ position:relative; overflow:hidden; aspect-ratio:4/3; }
.project-img-wrap img{ width:100%; height:100%; object-fit:cover; transition:transform .6s var(--ease); }
.project-card:hover .project-img-wrap img{ transform:scale(1.08); }
.project-badge{ position:absolute; top:16px; left:16px; background:var(--gold); color:var(--navy); font-family:var(--font-btn); font-weight:700; font-size:.72rem; padding:6px 14px; border-radius:100px; }
.project-body{ padding:24px; }
.project-body h4{ font-size:1.15rem; margin-bottom:6px; }
.project-loc{ font-size:.86rem; color:var(--ink-soft); margin-bottom:14px; display:flex; align-items:center; gap:6px; }
.project-meta{ display:flex; justify-content:space-between; border-top:1px dashed rgba(15,23,42,.12); padding-top:14px; margin-top:14px; font-size:.82rem; }
.project-meta div strong{ display:block; color:var(--navy); font-size:.95rem; }
.project-price{ color:var(--blue); font-weight:800; font-size:1.15rem; font-family:var(--font-head); }

/* Why choose us */
.feature-icon-card{ padding:34px 26px; height:100%; text-align:center; transition:transform .35s var(--ease); }
.feature-icon-card:hover{ transform:translateY(-8px); }
.feature-icon{ width:64px; height:64px; margin:0 auto 18px; border-radius:18px; background:linear-gradient(135deg,var(--navy),var(--navy-soft)); color:var(--gold-soft); display:flex; align-items:center; justify-content:center; font-size:1.5rem; }
.feature-icon-card h5{ font-size:1.02rem; margin-bottom:8px; }
.feature-icon-card p{ font-size:.88rem; margin-bottom:0; }

/* About */
.about-img-wrap{ position:relative; }
.about-img-wrap img{ border-radius:var(--radius); box-shadow:var(--shadow-lift); }
.about-badge-card{ position:absolute; bottom:-24px; right:-24px; padding:22px 26px; max-width:220px; }
.about-badge-card strong{ font-family:var(--font-head); font-size:1.8rem; color:var(--gold); display:block; }
.blueprint-bg{ position:absolute; inset:0; opacity:.04; background-image:linear-gradient(var(--navy) 1px, transparent 1px), linear-gradient(90deg, var(--navy) 1px, transparent 1px); background-size:36px 36px; pointer-events:none; }

/* Timeline */
.journey-line{ position:relative; max-width:900px; margin:0 auto; }
.journey-line::before{ content:""; position:absolute; left:50%; top:0; bottom:0; width:2px; background:linear-gradient(180deg,var(--gold),var(--blue)); transform:translateX(-50%); }
.journey-item{ position:relative; width:50%; padding:0 44px 56px; }
.journey-item:nth-child(odd){ left:0; text-align:right; }
.journey-item:nth-child(even){ left:50%; text-align:left; }
.journey-dot{ position:absolute; top:4px; width:16px; height:16px; border-radius:50%; background:var(--gold); border:4px solid var(--bg); box-shadow:0 0 0 2px var(--gold); }
.journey-item:nth-child(odd) .journey-dot{ right:-9px; }
.journey-item:nth-child(even) .journey-dot{ left:-9px; }
.journey-year{ font-family:var(--font-head); color:var(--blue); font-weight:800; font-size:1.3rem; }
.journey-item h5{ margin:6px 0; font-size:1.05rem; }
.journey-item p{ font-size:.88rem; margin:0; }
@media (max-width:767px){
  .journey-line::before{ left:18px; }
  .journey-item, .journey-item:nth-child(odd), .journey-item:nth-child(even){ width:100%; left:0; text-align:left; padding:0 0 44px 46px; }
  .journey-item:nth-child(odd) .journey-dot, .journey-item:nth-child(even) .journey-dot{ left:9px; right:auto; }
}

/* Categories */
.cat-card{ position:relative; border-radius:var(--radius); overflow:hidden; aspect-ratio:1/1.1; }
.cat-card img{ width:100%; height:100%; object-fit:cover; transition:transform .6s var(--ease); }
.cat-card:hover img{ transform:scale(1.1); }
.cat-overlay{ position:absolute; inset:0; background:linear-gradient(180deg, transparent 40%, rgba(15,23,42,.9)); display:flex; align-items:flex-end; padding:20px; }
.cat-overlay h5{ color:#fff; margin:0; font-size:1.05rem; }
.cat-overlay i{ color:var(--gold); margin-right:8px; }

/* Amenities */
.amenity-item{ text-align:center; padding:22px 12px; }
.amenity-item i{ width:54px; height:54px; border-radius:50%; background:#fff; box-shadow:var(--shadow-soft); display:flex; align-items:center; justify-content:center; margin:0 auto 12px; color:var(--gold); font-size:1.2rem; }
.amenity-item span{ font-size:.84rem; font-weight:600; color:var(--navy); }

/* Video tour */
.video-tour{ position:relative; border-radius:var(--radius); overflow:hidden; box-shadow:var(--shadow-lift); }
.video-tour img{ width:100%; display:block; aspect-ratio:16/8; object-fit:cover; }
.video-tour .overlay{ position:absolute; inset:0; background:rgba(15,23,42,.4); display:flex; align-items:center; justify-content:center; flex-direction:column; color:#fff; text-align:center; }
.play-btn{ width:84px; height:84px; border-radius:50%; background:rgba(255,255,255,.15); border:1.5px solid rgba(255,255,255,.6); backdrop-filter:blur(6px); display:flex; align-items:center; justify-content:center; font-size:1.6rem; margin-bottom:20px; transition:transform .3s var(--ease); cursor:pointer; }
.play-btn:hover{ transform:scale(1.1); background:var(--gold); color:var(--navy); border-color:var(--gold); }

/* Gallery */
.gallery-grid{ column-count:3; column-gap:16px; }
.gallery-item{ break-inside:avoid; margin-bottom:16px; border-radius:var(--radius-sm); overflow:hidden; cursor:pointer; position:relative; }
.gallery-item img{ width:100%; display:block; transition:transform .5s var(--ease); }
.gallery-item:hover img{ transform:scale(1.06); }
.gallery-item .zoom-tag{ position:absolute; top:12px; right:12px; width:34px; height:34px; border-radius:50%; background:rgba(255,255,255,.85); display:flex; align-items:center; justify-content:center; color:var(--navy); opacity:0; transition:opacity .3s; }
.gallery-item:hover .zoom-tag{ opacity:1; }
@media (max-width:767px){ .gallery-grid{ column-count:2; } }
#lightboxOverlay{ position:fixed; inset:0; background:rgba(15,23,42,.94); z-index:1800; display:none; align-items:center; justify-content:center; padding:30px; }
#lightboxOverlay.show{ display:flex; }
#lightboxOverlay img{ max-width:90vw; max-height:85vh; border-radius:12px; }
.lightbox-close{ position:absolute; top:24px; right:32px; color:#fff; font-size:1.8rem; cursor:pointer; }

/* Testimonials */
.testimonial-card{ padding:32px; text-align:center; max-width:700px; margin:0 auto; }
.testimonial-avatar{ width:72px; height:72px; border-radius:50%; object-fit:cover; margin:0 auto 16px; border:3px solid var(--gold-soft); }
.testimonial-stars{ color:var(--gold); margin-bottom:14px; }
.testimonial-text{ font-family:var(--font-head); font-weight:500; font-size:1.15rem; color:var(--navy); margin-bottom:16px; }
.testimonial-name{ font-weight:700; }
.testimonial-role{ font-size:.82rem; color:var(--ink-soft); }
#testimonialCarousel .carousel-indicators [data-bs-target]{ background:var(--gold); width:8px; height:8px; border-radius:50%; }

/* Team */
.team-card{ border-radius:var(--radius); overflow:hidden; background:#fff; box-shadow:var(--shadow-soft); transition:transform .35s var(--ease); }
.team-card:hover{ transform:translateY(-8px); }
.team-img-wrap{ position:relative; aspect-ratio:3/4; overflow:hidden; }
.team-img-wrap img{ width:100%; height:100%; object-fit:cover; }
.team-social{ position:absolute; bottom:-50px; left:0; right:0; display:flex; justify-content:center; gap:10px; transition:bottom .35s var(--ease); }
.team-card:hover .team-social{ bottom:16px; }
.team-social a{ width:36px; height:36px; border-radius:50%; background:#fff; color:var(--navy); display:flex; align-items:center; justify-content:center; }
.team-body{ padding:18px; text-align:center; }
.team-body h5{ font-size:1rem; margin-bottom:2px; }
.team-body span{ font-size:.82rem; color:var(--blue); font-weight:600; }

/* Stats section */
.stats-section{ background:linear-gradient(135deg,var(--navy),var(--navy-soft)); color:#fff; }
.stats-section .stat-block{ text-align:center; }
.stats-section .stat-block strong{ font-family:var(--font-head); font-size:clamp(2rem,4vw,3rem); color:var(--gold-soft); display:block; }
.stats-section .stat-block span{ font-size:.9rem; color:rgba(255,255,255,.7); }

/* Blog */
.blog-card{ border-radius:var(--radius); overflow:hidden; background:#fff; box-shadow:var(--shadow-soft); height:100%; transition:transform .35s var(--ease); }
.blog-card:hover{ transform:translateY(-8px); }
.blog-img{ aspect-ratio:16/10; overflow:hidden; }
.blog-img img{ width:100%; height:100%; object-fit:cover; transition:transform .6s var(--ease); }
.blog-card:hover .blog-img img{ transform:scale(1.08); }
.blog-body{ padding:22px; }
.blog-date{ font-size:.76rem; color:var(--gold); font-weight:700; text-transform:uppercase; letter-spacing:.05em; }
.blog-body h5{ font-size:1.05rem; margin:8px 0 12px; }
.read-more{ color:var(--blue); font-weight:700; font-size:.88rem; }
.read-more i{ transition:transform .3s var(--ease); margin-left:4px; }
.read-more:hover i{ transform:translateX(4px); }

/* FAQ */
.accordion-item{ border:none; margin-bottom:12px; border-radius:var(--radius-sm) !important; overflow:hidden; box-shadow:var(--shadow-soft); }
.accordion-button{ font-family:var(--font-head); font-weight:600; font-size:.98rem; padding:20px 24px; }
.accordion-button:not(.collapsed){ background:var(--navy); color:#fff; }
.accordion-button:focus{ box-shadow:none; }
.accordion-button::after{ filter:none; }

/* CTA banner */
.cta-banner{ background:linear-gradient(135deg,var(--navy) 0%, var(--navy-soft) 100%); border-radius:var(--radius); color:#fff; padding:clamp(48px,6vw,80px); position:relative; overflow:hidden; }
.cta-banner::before{ content:""; position:absolute; width:340px; height:340px; background:radial-gradient(circle,rgba(212,175,55,.25),transparent 70%); top:-100px; right:-80px; }
.cta-banner h2{ font-size:clamp(1.8rem,3.6vw,2.6rem); margin-bottom:16px; }

.whatsapp-float{ position:fixed; bottom:26px; left:26px; width:56px; height:56px; border-radius:50%; background:#25D366; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.5rem; z-index:150; box-shadow:0 10px 25px rgba(37,211,102,.5); transition:transform .3s; }
.whatsapp-float:hover{ transform:scale(1.1); color:#fff; }

/* Contact */
.contact-info-item{ display:flex; gap:16px; margin-bottom:24px; }
.contact-info-item i{ width:48px; height:48px; border-radius:14px; background:var(--navy); color:var(--gold-soft); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
.contact-info-item h6{ font-family:var(--font-head); font-size:.95rem; margin-bottom:4px; }
.contact-info-item p{ font-size:.88rem; margin:0; }
.map-wrap{ border-radius:var(--radius); overflow:hidden; box-shadow:var(--shadow-soft); }
.contact-form-glass{ padding:34px; }
.contact-form-glass .form-control, .contact-form-glass .form-select{ border-radius:12px; border:1.5px solid rgba(15,23,42,.12); padding:.85rem 1rem; background:#e7e7e7; }
.contact-form-glass .form-control:focus, .contact-form-glass .form-select:focus{ border-color:var(--gold); box-shadow:0 0 0 4px rgba(212,175,55,.15); }

/* Footer */
.site-footer{ background:var(--navy); color:rgba(255,255,255,.7); padding:70px 0 24px; }
.site-footer h6{ color:#fff; font-family:var(--font-head); font-size:1rem; margin-bottom:18px; }
.site-footer li{ margin-bottom:10px; font-size:.9rem; }
.site-footer li a:hover{ color:var(--gold-soft); }
.footer-brand{ font-family:var(--font-head); font-weight:800; font-size:1.5rem; color:#fff; }
.footer-brand span{ color:var(--gold); }
.footer-social a{ width:38px; height:38px; border-radius:50%; background:rgba(255,255,255,.08); display:flex; align-items:center; justify-content:center; margin-right:8px; transition:background .3s; }
.footer-social a:hover{ background:var(--gold); color:var(--navy); }
.newsletter-form input{ border-radius:12px 0 0 12px; border:none; padding:.8rem 1rem; }
.newsletter-form button{ border-radius:0 12px 12px 0; background:var(--gold); border:none; padding:.8rem 1.2rem; color:var(--navy); font-weight:700; }
.footer-bottom{ border-top:1px solid rgba(255,255,255,.1); margin-top:44px; padding-top:22px; font-size:.82rem; }

.back-to-top{ position:fixed; bottom:26px; right:26px; width:50px; height:50px; border-radius:50%; background:var(--gold); color:var(--navy); display:flex; align-items:center; justify-content:center; z-index:150; opacity:0; pointer-events:none; transition:opacity .3s, transform .3s; box-shadow:0 10px 25px rgba(212,175,55,.5); }
.back-to-top.show{ opacity:1; pointer-events:auto; }
.back-to-top:hover{ transform:translateY(-4px); }
</style>
</head>
<body>

<div id="preloader"><div class="loader-mark">AURELIA<span style="color:#D4AF37">ESTATES</span><div class="loader-bar"></div></div></div>
<div id="scrollProgress"></div>
<div class="cursor-dot" id="cursorDot"></div>
<div class="cursor-ring" id="cursorRing"></div>

<!-- NAVBAR -->
<nav class="navbar navbar-expand-lg navbar-luxury" id="mainNav">
  <div class="container">
    <a class="navbar-brand" href="#hero">
      @if(setting('footer_logo'))
        <img src="{{ Storage::url(setting('footer_logo')) }}" alt="Logo" style="height: 40px;">
      @else
        AURELIA<span>.</span>
      @endif
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu"><i class="fa-solid fa-bars"></i></button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
        <li class="nav-item"><a class="nav-link active" href="#hero">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="#projects">Projects</a></li>
        <li class="nav-item"><a class="nav-link" href="#categories">Properties</a></li>
        <li class="nav-item"><a class="nav-link" href="#why-us">Services</a></li>
        <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
        <li class="nav-item"><a class="nav-link" href="#gallery">Gallery</a></li>
        <li class="nav-item"><a class="nav-link" href="#blog">Blog</a></li>
        <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
        <li class="nav-item ms-lg-3"><a class="btn btn-gold btn-sm" href="#contact">Book a Visit</a></li>
      </ul>
    </div>
  </div>
</nav>

<!-- HERO -->
@if(setting('hero_status', '1') == '1')
<section class="hero" id="hero">
  @if(setting('hero_bg_image'))
  <div class="hero-bg" style="background-image: url('{{ Storage::url(setting('hero_bg_image')) }}'); background-size: cover; background-position: center; filter: brightness(0.6);"></div>
  @else
  <div class="hero-bg"></div>
  <video class="hero-video" id="heroVideo" autoplay muted loop playsinline poster="https://images.unsplash.com/photo-1600585154340-be6161a56a0c?auto=format&fit=crop&w=1600&q=75">
    <source src="https://assets.mixkit.co/videos/preview/mixkit-modern-apartment-with-elegant-architecture-39750-large.mp4" type="video/mp4">
  </video>
  @endif
  <div class="hero-overlay"></div>
  <div class="floating-shape fs1"></div>
  <div class="floating-shape fs2"></div>

  <div class="container hero-content">
    <div class="row">
      <div class="col-lg-9" data-aos="fade-up">
        <span class="eyebrow">Aurelia Estates · Since 2010</span>
        <h1 class="hero-title">{!! setting('hero_title', 'Find Your') !!} <span class="accent">{{ setting('hero_highlight', 'Dream Property') }}</span></h1>
        <p class="hero-sub">{{ setting('hero_subtitle', 'Verified luxury apartments, villas and commercial spaces — backed by transparent pricing, legal support and two decades of trusted delivery.') }}</p>
        <div class="hero-cta">
          <a href="{{ setting('hero_btn_url', '#projects') }}" class="btn btn-gold">{{ setting('hero_btn_text', 'View Projects') }}</a>
          <a href="#contact" class="btn btn-outline-light-glass">Book a Visit</a>
        </div>
      </div>
    </div>
    <div class="row floating-stats g-3" data-aos="fade-up" data-aos-delay="150">
      <div class="col-6 col-md-4"><div class="glass stat-card"><strong data-count="500">0</strong><span>Properties Delivered</span></div></div>
      <div class="col-6 col-md-4"><div class="glass stat-card"><strong data-count="2500">0</strong><span>Happy Clients</span></div></div>
      <div class="col-12 col-md-4"><div class="glass stat-card"><strong data-count="20">0</strong><span>Years of Experience</span></div></div>
    </div>
  </div>
  <div class="scroll-indicator">SCROLL<div class="mouse"></div></div>
</section>
@endif

<!-- SEARCH -->
@if(setting('property_search_status', '1') == '1')
<div class="container search-panel" data-aos="fade-up" style="@if(setting('search_bg_image')) background-image: url('{{ Storage::url(setting('search_bg_image')) }}'); background-size: cover; border-radius: var(--radius); @endif">
  <div class="glass" style="@if(setting('search_bg_image')) background: rgba(255,255,255,0.85); backdrop-filter: blur(10px); padding: 20px; border-radius: var(--radius); @endif">
    @if(setting('search_heading'))
      <h5 class="text-center mb-3 fw-bold">{{ setting('search_heading') }}</h5>
    @endif
    <form class="row g-3 align-items-end" onsubmit="return false;">
      <div class="col-md-3 col-6">
        <label>Location</label>
        <select class="form-select"><option>Any Location</option><option>Dhaka</option><option>Chattogram</option><option>Sylhet</option><option>Cox's Bazar</option></select>
      </div>
      <div class="col-md-3 col-6">
        <label>Property Type</label>
        <select class="form-select"><option>Any Type</option><option>Apartment</option><option>Villa</option><option>Duplex</option><option>Commercial</option><option>Office</option><option>Land</option></select>
      </div>
      <div class="col-md-2 col-6">
        <label>Budget</label>
        <select class="form-select"><option>Any Budget</option><option>Under $200k</option><option>$200k–$500k</option><option>$500k+</option></select>
      </div>
      <div class="col-md-2 col-6">
        <label>Bedrooms</label>
        <select class="form-select"><option>Any</option><option>1</option><option>2</option><option>3</option><option>4+</option></select>
      </div>
      <div class="col-md-2 col-12">
        <button class="btn btn-navy search-btn" type="submit"><i class="fa-solid fa-magnifying-glass me-2"></i>Search</button>
      </div>
    </form>
  </div>
</div>
@endif

<!-- FEATURED PROJECTS -->
<section id="projects">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Featured Projects</span>
      <h2 class="section-title">Signature Developments</h2>
      <p class="mx-auto" style="max-width:560px;">A curated selection of our most sought-after residential and commercial developments.</p>
    </div>
    <div class="row g-4">
      <div class="col-lg-4 col-md-6" data-aos="fade-up">
        <div class="project-card" onclick="window.location.href='{{ route('property.featured.details') }}'" style="cursor:pointer;">
          <div class="project-img-wrap"><span class="project-badge">New Launch</span><img src="https://images.unsplash.com/photo-1600596542815-ffad4c1539a9?auto=format&fit=crop&w=800&q=75" loading="lazy" alt="Aurelia Heights apartment tower"></div>
          <div class="project-body">
            <h4>Aurelia Heights</h4>
            <div class="project-loc"><i class="fa-solid fa-location-dot text-primary"></i> Gulshan, Dhaka</div>
            <div class="project-meta">
              <div><strong>1,850 sqft</strong>Area</div>
              <div><strong>24 Units</strong>Available</div>
              <div class="project-price">$340k</div>
            </div>
            <a href="{{ route('property.featured.details') }}" class="btn btn-navy w-100 mt-3">View Details</a>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="project-card" onclick="window.location.href='{{ route('property.featured.details') }}'" style="cursor:pointer;">
          <div class="project-img-wrap"><span class="project-badge">Best Seller</span><img src="https://images.unsplash.com/photo-1613977257363-707ba9348227?auto=format&fit=crop&w=800&q=75" loading="lazy" alt="The Meridian villa"></div>
          <div class="project-body">
            <h4>The Meridian Villas</h4>
            <div class="project-loc"><i class="fa-solid fa-location-dot text-primary"></i> Bashundhara, Dhaka</div>
            <div class="project-meta">
              <div><strong>4,200 sqft</strong>Area</div>
              <div><strong>8 Units</strong>Available</div>
              <div class="project-price">$780k</div>
            </div>
            <a href="{{ route('property.featured.details') }}" class="btn btn-navy w-100 mt-3">View Details</a>
          </div>
        </div>
      </div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="200">
        <div class="project-card" onclick="window.location.href='{{ route('property.featured.details') }}'" style="cursor:pointer;">
          <div class="project-img-wrap"><span class="project-badge">Featured</span><img src="https://images.unsplash.com/photo-1600585154526-990dced4db0d?auto=format&fit=crop&w=800&q=75" loading="lazy" alt="Skyline Business Bay tower"></div>
          <div class="project-body">
            <h4>Skyline Business Bay</h4>
            <div class="project-loc"><i class="fa-solid fa-location-dot text-primary"></i> Motijheel, Dhaka</div>
            <div class="project-meta">
              <div><strong>2,600 sqft</strong>Area</div>
              <div><strong>16 Units</strong>Available</div>
              <div class="project-price">$510k</div>
            </div>
            <a href="{{ route('property.featured.details') }}" class="btn btn-navy w-100 mt-3">View Details</a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- WHY CHOOSE US -->
<section id="why-us" style="background:linear-gradient(180deg,transparent,rgba(37,99,235,.05),transparent);">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Why Choose Us</span>
      <h2 class="section-title">Built On Trust, Delivered With Precision</h2>
    </div>
    <div class="row g-4">
      <div class="col-lg-4 col-md-6" data-aos="fade-up"><div class="glass feature-icon-card"><div class="feature-icon"><i class="fa-solid fa-building-shield"></i></div><h5>Trusted Company</h5><p>Two decades of on-time delivery across residential and commercial developments.</p></div></div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="80"><div class="glass feature-icon-card"><div class="feature-icon"><i class="fa-solid fa-file-circle-check"></i></div><h5>Verified Property</h5><p>Every listing is title-checked and verified before it reaches our catalogue.</p></div></div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="160"><div class="glass feature-icon-card"><div class="feature-icon"><i class="fa-solid fa-hand-holding-dollar"></i></div><h5>Easy Installment</h5><p>Flexible 3 to 5 year installment plans tailored to your budget.</p></div></div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up"><div class="glass feature-icon-card"><div class="feature-icon"><i class="fa-solid fa-scale-balanced"></i></div><h5>Legal Support</h5><p>Dedicated legal team handling registration, mutation and paperwork end to end.</p></div></div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="80"><div class="glass feature-icon-card"><div class="feature-icon"><i class="fa-solid fa-city"></i></div><h5>Premium Locations</h5><p>Developments placed in the most connected and high-growth neighbourhoods.</p></div></div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="160"><div class="glass feature-icon-card"><div class="feature-icon"><i class="fa-solid fa-shield-halved"></i></div><h5>Investment Security</h5><p>Transparent contracts and escrow-backed payments protect every buyer.</p></div></div>
    </div>
  </div>
</section>

<!-- ABOUT -->
<section id="about">
  <div class="blueprint-bg"></div>
  <div class="container">
    <div class="row align-items-center g-5">
      <div class="col-lg-6" data-aos="fade-right">
        <div class="about-img-wrap">
          <img src="https://images.unsplash.com/photo-1600047509807-ba8f99d2cdde?auto=format&fit=crop&w=900&q=75" class="rounded-24" loading="lazy" alt="Aurelia Estates office and showroom">
          <div class="glass about-badge-card"><strong>20+</strong>Years shaping city skylines</div>
        </div>
      </div>
      <div class="col-lg-6" data-aos="fade-left">
        <span class="eyebrow">About Aurelia Estates</span>
        <h2 class="section-title">Building More Than Properties — Building Trust</h2>
        <p class="mt-3">Since 2010, Aurelia Estates has delivered over 500 residential and commercial projects across the country, guided by one principle: every buyer deserves clarity, quality, and a home worth the investment.</p>
        <div class="row g-3 my-3">
          <div class="col-6"><h6 class="fw-bold mb-1"><i class="fa-solid fa-bullseye text-primary me-2"></i>Our Mission</h6><p style="font-size:.9rem;">Make premium real estate accessible through transparent, verified transactions.</p></div>
          <div class="col-6"><h6 class="fw-bold mb-1"><i class="fa-solid fa-eye text-primary me-2"></i>Our Vision</h6><p style="font-size:.9rem;">To be the most trusted name in luxury real estate development nationwide.</p></div>
        </div>
        <div class="row g-3 text-center">
          <div class="col-4"><strong class="d-block fs-4 fw-bold" data-count="500">0</strong><span style="font-size:.8rem;color:var(--ink-soft);">Projects</span></div>
          <div class="col-4"><strong class="d-block fs-4 fw-bold" data-count="12000">0</strong><span style="font-size:.8rem;color:var(--ink-soft);">Customers</span></div>
          <div class="col-4"><strong class="d-block fs-4 fw-bold" data-count="98" data-suffix="%">0</strong><span style="font-size:.8rem;color:var(--ink-soft);">Satisfaction</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- JOURNEY -->
<section style="background:linear-gradient(180deg,transparent,rgba(212,175,55,.06),transparent);">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Our Journey</span>
      <h2 class="section-title">Two Decades Of Milestones</h2>
    </div>
    <div class="journey-line">
      <div class="journey-item" data-aos="fade-right"><div class="journey-dot"></div><div class="journey-year">2010</div><h5>Company Founded</h5><p>Aurelia Estates began with a single residential project in Dhaka.</p></div>
      <div class="journey-item" data-aos="fade-left"><div class="journey-dot"></div><div class="journey-year">2015</div><h5>Regional Expansion</h5><p>Expanded operations to Chattogram and Sylhet with 50+ completed units.</p></div>
      <div class="journey-item" data-aos="fade-right"><div class="journey-dot"></div><div class="journey-year">2020</div><h5>National Projects</h5><p>Launched large-scale mixed-use developments across 6 major cities.</p></div>
      <div class="journey-item" data-aos="fade-left"><div class="journey-dot"></div><div class="journey-year">2026</div><h5>Industry Leader</h5><p>Recognised as a top-3 luxury developer with 500+ properties delivered.</p></div>
    </div>
  </div>
</section>

<!-- CATEGORIES -->
<section id="categories">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Property Categories</span>
      <h2 class="section-title">Browse By Property Type</h2>
    </div>
    <div class="row g-4">
      <div class="col-lg-4 col-md-6" data-aos="zoom-in"><div class="cat-card"><img src="https://images.unsplash.com/photo-1600566753086-00f18fb6b3ea?auto=format&fit=crop&w=700&q=75" loading="lazy" alt="Apartment interior"><div class="cat-overlay"><h5><i class="fa-solid fa-building"></i>Apartment</h5></div></div></div>
      <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="80"><div class="cat-card"><img src="https://images.unsplash.com/photo-1568605114967-8130f3a36994?auto=format&fit=crop&w=700&q=75" loading="lazy" alt="Luxury villa"><div class="cat-overlay"><h5><i class="fa-solid fa-house"></i>Villa</h5></div></div></div>
      <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="160"><div class="cat-card"><img src="https://images.unsplash.com/photo-1600210492486-724fe5c67fb0?auto=format&fit=crop&w=700&q=75" loading="lazy" alt="Duplex home"><div class="cat-overlay"><h5><i class="fa-solid fa-house-chimney"></i>Duplex</h5></div></div></div>
      <div class="col-lg-4 col-md-6" data-aos="zoom-in"><div class="cat-card"><img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=700&q=75" loading="lazy" alt="Commercial building"><div class="cat-overlay"><h5><i class="fa-solid fa-shop"></i>Commercial</h5></div></div></div>
      <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="80"><div class="cat-card"><img src="https://images.unsplash.com/photo-1497366216548-37526070297c?auto=format&fit=crop&w=700&q=75" loading="lazy" alt="Office space"><div class="cat-overlay"><h5><i class="fa-solid fa-briefcase"></i>Office</h5></div></div></div>
      <div class="col-lg-4 col-md-6" data-aos="zoom-in" data-aos-delay="160"><div class="cat-card"><img src="https://images.unsplash.com/photo-1500382017468-9049fed747ef?auto=format&fit=crop&w=700&q=75" loading="lazy" alt="Open land plot"><div class="cat-overlay"><h5><i class="fa-solid fa-map"></i>Land</h5></div></div></div>
    </div>
  </div>
</section>

<!-- AMENITIES -->
<section style="background:linear-gradient(180deg,transparent,rgba(37,99,235,.05),transparent);">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Amenities</span>
      <h2 class="section-title">Everything Your New Home Needs</h2>
    </div>
    <div class="row g-2 text-center">
      <div class="col-6 col-md-2" data-aos="fade-up"><div class="amenity-item"><i class="fa-solid fa-person-swimming"></i><span>Swimming Pool</span></div></div>
      <div class="col-6 col-md-2" data-aos="fade-up" data-aos-delay="40"><div class="amenity-item"><i class="fa-solid fa-dumbbell"></i><span>Gym</span></div></div>
      <div class="col-6 col-md-2" data-aos="fade-up" data-aos-delay="80"><div class="amenity-item"><i class="fa-solid fa-child-reaching"></i><span>Kids Zone</span></div></div>
      <div class="col-6 col-md-2" data-aos="fade-up" data-aos-delay="120"><div class="amenity-item"><i class="fa-solid fa-mosque"></i><span>Mosque</span></div></div>
      <div class="col-6 col-md-2" data-aos="fade-up" data-aos-delay="160"><div class="amenity-item"><i class="fa-solid fa-tree"></i><span>Landscape Garden</span></div></div>
      <div class="col-6 col-md-2" data-aos="fade-up" data-aos-delay="200"><div class="amenity-item"><i class="fa-solid fa-square-parking"></i><span>Parking</span></div></div>
      <div class="col-6 col-md-2" data-aos="fade-up"><div class="amenity-item"><i class="fa-solid fa-elevator"></i><span>Lift</span></div></div>
      <div class="col-6 col-md-2" data-aos="fade-up" data-aos-delay="40"><div class="amenity-item"><i class="fa-solid fa-plug"></i><span>Generator</span></div></div>
      <div class="col-6 col-md-2" data-aos="fade-up" data-aos-delay="80"><div class="amenity-item"><i class="fa-solid fa-video"></i><span>CCTV</span></div></div>
      <div class="col-6 col-md-2" data-aos="fade-up" data-aos-delay="120"><div class="amenity-item"><i class="fa-solid fa-user-shield"></i><span>24/7 Security</span></div></div>
    </div>
  </div>
</section>

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

<!-- TESTIMONIALS -->
<section>
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Testimonials</span>
      <h2 class="section-title">What Our Clients Say</h2>
    </div>
    <div id="testimonialCarousel" class="carousel slide" data-bs-ride="carousel" data-aos="fade-up">
      <div class="carousel-inner">
        <div class="carousel-item active">
          <div class="glass testimonial-card">
            <img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=200&q=75" class="testimonial-avatar" loading="lazy" alt="Client Farhana Rahman">
            <div class="testimonial-stars">★★★★★</div>
            <p class="testimonial-text">"From the first site visit to key handover, the process felt effortless. The legal team handled everything transparently."</p>
            <div class="testimonial-name">Farhana Rahman</div><div class="testimonial-role">Homeowner, Aurelia Heights</div>
          </div>
        </div>
        <div class="carousel-item">
          <div class="glass testimonial-card">
            <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=200&q=75" class="testimonial-avatar" loading="lazy" alt="Client Imran Chowdhury">
            <div class="testimonial-stars">★★★★★</div>
            <p class="testimonial-text">"The installment plan made investing in a second property realistic for us. Construction quality exceeded expectations."</p>
            <div class="testimonial-name">Imran Chowdhury</div><div class="testimonial-role">Investor, Skyline Business Bay</div>
          </div>
        </div>
        <div class="carousel-item">
          <div class="glass testimonial-card">
            <img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=200&q=75" class="testimonial-avatar" loading="lazy" alt="Client Sadia Islam">
            <div class="testimonial-stars">★★★★★</div>
            <p class="testimonial-text">"Every promise on the brochure matched what we received on move-in day. Highly recommend the Meridian Villas."</p>
            <div class="testimonial-name">Sadia Islam</div><div class="testimonial-role">Homeowner, The Meridian Villas</div>
          </div>
        </div>
      </div>
      <div class="carousel-indicators position-relative mt-4">
        <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="0" class="active"></button>
        <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="1"></button>
        <button type="button" data-bs-target="#testimonialCarousel" data-bs-slide-to="2"></button>
      </div>
    </div>
  </div>
</section>

<!-- TEAM -->
<section style="background:linear-gradient(180deg,transparent,rgba(37,99,235,.05),transparent);">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Our Team</span>
      <h2 class="section-title">The People Behind Aurelia</h2>
    </div>
    <div class="row g-4">
      <div class="col-lg-3 col-md-6" data-aos="fade-up">
        <div class="team-card"><div class="team-img-wrap"><img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=500&q=75" loading="lazy" alt="CEO portrait"><div class="team-social"><a href="#"><i class="fa-brands fa-linkedin-in"></i></a><a href="#"><i class="fa-solid fa-envelope"></i></a></div></div><div class="team-body"><h5>Rashed Karim</h5><span>Chief Executive Officer</span></div></div>
      </div>
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="80">
        <div class="team-card"><div class="team-img-wrap"><img src="https://images.unsplash.com/photo-1580489944761-15a19d654956?auto=format&fit=crop&w=500&q=75" loading="lazy" alt="Sales Manager portrait"><div class="team-social"><a href="#"><i class="fa-brands fa-linkedin-in"></i></a><a href="#"><i class="fa-solid fa-envelope"></i></a></div></div><div class="team-body"><h5>Nusrat Jahan</h5><span>Sales Manager</span></div></div>
      </div>
      <div class="col-lg-3 col-md-6" data-aos="fade-up" data-aos-delay="160">
        <div class="team-card"><div class="team-img-wrap"><img src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=500&q=75" loading="lazy" alt="Architect portrait"><div class="team-social"><a href="#"><i class="fa-brands fa-linkedin-in"></i></a><a href="#"><i class="fa-solid fa-envelope"></i></a></div></div><div class="team-body"><h5>Tanvir Ahmed</h5><span>Lead Architect</span></div></div>
      </div>
      <div class="col-lg-3 col-md-6" data-aos="fade-up">
        <div class="team-card"><div class="team-img-wrap"><img src="https://images.unsplash.com/photo-1544005313-94ddf0286df2?auto=format&fit=crop&w=500&q=75" loading="lazy" alt="Engineer portrait"><div class="team-social"><a href="#"><i class="fa-brands fa-linkedin-in"></i></a><a href="#"><i class="fa-solid fa-envelope"></i></a></div></div><div class="team-body"><h5>Farhana Rahman</h5><span>Civil Engineer</span></div></div>
      </div>
    </div>
  </div>
</section>

<!-- STATS -->
<section class="stats-section">
  <div class="container">
    <div class="row g-4">
      <div class="col-6 col-md-3" data-aos="fade-up"><div class="stat-block"><strong data-count="500" data-suffix="+">0</strong><span>Projects</span></div></div>
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="80"><div class="stat-block"><strong data-count="12000" data-suffix="+">0</strong><span>Customers</span></div></div>
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="160"><div class="stat-block"><strong data-count="350" data-suffix="+">0</strong><span>Employees</span></div></div>
      <div class="col-6 col-md-3" data-aos="fade-up" data-aos-delay="240"><div class="stat-block"><strong data-count="98" data-suffix="%">0</strong><span>Client Satisfaction</span></div></div>
    </div>
  </div>
</section>

<!-- BLOG -->
<section id="blog">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Latest Insights</span>
      <h2 class="section-title">From Our Blog</h2>
    </div>
    <div class="row g-4">
      <div class="col-lg-4 col-md-6" data-aos="fade-up">
        <div class="blog-card"><div class="blog-img"><img src="https://images.unsplash.com/photo-1560518883-ce09059eeffa?auto=format&fit=crop&w=700&q=75" loading="lazy" alt="Real estate investment article"></div><div class="blog-body"><span class="blog-date">Jul 12, 2026</span><h5>5 Signs A Neighbourhood Will Appreciate In Value</h5><a href="#" class="read-more">Read More <i class="fa-solid fa-arrow-right"></i></a></div></div>
      </div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="80">
        <div class="blog-card"><div class="blog-img"><img src="https://images.unsplash.com/photo-1600607687644-c7171b42498b?auto=format&fit=crop&w=700&q=75" loading="lazy" alt="Home loan guidance article"></div><div class="blog-body"><span class="blog-date">Jul 02, 2026</span><h5>A First-Time Buyer's Guide To Installment Plans</h5><a href="#" class="read-more">Read More <i class="fa-solid fa-arrow-right"></i></a></div></div>
      </div>
      <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="160">
        <div class="blog-card"><div class="blog-img"><img src="https://images.unsplash.com/photo-1512918728675-ed5a9ecdebfd?auto=format&fit=crop&w=700&q=75" loading="lazy" alt="Commercial property article"></div><div class="blog-body"><span class="blog-date">Jun 20, 2026</span><h5>Why Commercial Spaces Are The New Growth Bet</h5><a href="#" class="read-more">Read More <i class="fa-solid fa-arrow-right"></i></a></div></div>
      </div>
    </div>
  </div>
</section>

<!-- FAQ -->
<section style="background:linear-gradient(180deg,transparent,rgba(212,175,55,.06),transparent);">
  <div class="container" style="max-width:820px;">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">FAQ</span>
      <h2 class="section-title">Frequently Asked Questions</h2>
    </div>
    <div class="accordion" id="faqAccordion" data-aos="fade-up">
      <div class="accordion-item">
        <h2 class="accordion-header"><button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">Do you offer installment payment plans?</button></h2>
        <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion"><div class="accordion-body">Yes, most properties come with flexible 3 to 5 year installment plans with a minimum 20% down payment.</div></div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">Are all listed properties legally verified?</button></h2>
        <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body">Every property undergoes a full title and legal document check by our in-house legal team before listing.</div></div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">Can I book a site visit before purchasing?</button></h2>
        <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body">Absolutely — you can schedule a free guided site visit using the "Book a Visit" button or the contact form below.</div></div>
      </div>
      <div class="accordion-item">
        <h2 class="accordion-header"><button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">What happens after I reserve a unit?</button></h2>
        <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion"><div class="accordion-body">Our sales team contacts you within 24 hours to confirm the booking, share the agreement, and outline the payment schedule.</div></div>
      </div>
    </div>
  </div>
</section>

<!-- CTA -->
@if(setting('cta_status', '1') == '1')
<section>
  <div class="container">
    <div class="cta-banner text-center" data-aos="zoom-in" style="@if(setting('cta_bg_image')) background-image: url('{{ Storage::url(setting('cta_bg_image')) }}'); background-size: cover; background-position: center; @endif">
      <span class="eyebrow" style="color:var(--gold-soft);"><i></i>Limited Units Remaining</span>
      <h2>{{ setting('cta_title', 'Ready To Invest In Your Future?') }}</h2>
      <p class="mb-4" style="color:rgba(255,255,255,.8);">{{ setting('cta_description', 'Book a free site visit or speak to our advisors today — no obligation, just clarity.') }}</p>
      <div class="d-flex justify-content-center gap-3 flex-wrap">
        <a href="{{ setting('cta_btn_link', '#contact') }}" class="btn btn-gold">{{ setting('cta_btn_text', 'Book Site Visit') }}</a>
        <a href="tel:+8801XXXXXXXXX" class="btn btn-outline-light-glass"><i class="fa-solid fa-phone me-2"></i>Call Now</a>
      </div>
    </div>
  </div>
</section>
@endif

<!-- CONTACT -->
<section id="contact">
  <div class="container">
    <div class="text-center mb-5" data-aos="fade-up">
      <span class="eyebrow">Contact Us</span>
      <h2 class="section-title">Let's Talk About Your Next Property</h2>
    </div>
    <div class="row g-4">
      <div class="col-lg-5" data-aos="fade-right">
        <div class="contact-info-item"><i class="fa-solid fa-location-dot"></i><div><h6>Office Address</h6><p>{{ setting('footer_address', 'House 12, Road 7, Gulshan-1, Dhaka 1212, Bangladesh') }}</p></div></div>
        <div class="contact-info-item"><i class="fa-solid fa-phone"></i><div><h6>Phone</h6><p>{{ setting('footer_phone', '+880 1XXX-XXXXXX') }}</p></div></div>
        <div class="contact-info-item"><i class="fa-solid fa-envelope"></i><div><h6>Email</h6><p>{{ setting('footer_email', 'hello@aureliaestates.example') }}</p></div></div>
        <div class="contact-info-item"><i class="fa-solid fa-clock"></i><div><h6>Business Hours</h6><p>Sat–Thu: 10:00 AM – 7:00 PM</p></div></div>
        <div class="map-wrap mt-4">
          <iframe src="https://www.google.com/maps?q=Gulshan%201%20Dhaka&output=embed" width="100%" height="260" style="border:0;" loading="lazy" title="Office location map"></iframe>
        </div>
      </div>
      <div class="col-lg-7" data-aos="fade-left">
        <div class="glass contact-form-glass">
          <form id="contactForm" onsubmit="return handleContactSubmit(event);">
            <div class="row g-3">
              <div class="col-md-6"><input type="text" class="form-control" placeholder="Full Name" required></div>
              <div class="col-md-6"><input type="tel" class="form-control" placeholder="Phone Number" required></div>
              <div class="col-md-6"><input type="email" class="form-control" placeholder="Email Address" required></div>
              <div class="col-md-6"><select class="form-select"><option>Property Type</option><option>Apartment</option><option>Villa</option><option>Duplex</option><option>Commercial</option><option>Office</option><option>Land</option></select></div>
              <div class="col-12"><select class="form-select"><option>Budget Range</option><option>Under $200k</option><option>$200k–$500k</option><option>$500k+</option></select></div>
              <div class="col-12"><textarea class="form-control" rows="4" placeholder="Message"></textarea></div>
              <div class="col-12 form-check ps-4">
                <input class="form-check-input" type="checkbox" id="agreeCheck" required>
                <label class="form-check-label" for="agreeCheck" style="font-size:.86rem;">I agree to be contacted regarding my enquiry.</label>
              </div>
              <div class="col-12"><button class="btn btn-gold w-100" type="submit">Send Enquiry</button></div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- FOOTER -->
<footer class="site-footer">
  <div class="container">
    <div class="row g-4 mb-4">
      <div class="col-lg-4">
        @if(setting('footer_logo'))
          <img src="{{ Storage::url(setting('footer_logo')) }}" alt="Logo" style="height: 50px; margin-bottom: 15px;">
        @else
          <div class="footer-brand mb-3">AURELIA<span>.</span></div>
        @endif
        <p style="font-size:.9rem; max-width:300px;">{{ setting('footer_description', 'Two decades of building verified, premium properties people are proud to call home.') }}</p>
        <div class="footer-social mt-3">
          @if(setting('footer_facebook'))<a href="{{ setting('footer_facebook') }}"><i class="fa-brands fa-facebook-f"></i></a>@endif
          @if(setting('footer_instagram'))<a href="{{ setting('footer_instagram') }}"><i class="fa-brands fa-instagram"></i></a>@endif
          @if(setting('footer_linkedin'))<a href="{{ setting('footer_linkedin') }}"><i class="fa-brands fa-linkedin-in"></i></a>@endif
          @if(setting('footer_youtube'))<a href="{{ setting('footer_youtube') }}"><i class="fa-brands fa-youtube"></i></a>@endif
        </div>
      </div>
      <div class="col-lg-2 col-6">
        <h6>Quick Links</h6>
        <ul class="list-unstyled"><li><a href="#about">About Us</a></li><li><a href="#projects">Projects</a></li><li><a href="#gallery">Gallery</a></li><li><a href="#contact">Contact</a></li></ul>
      </div>
      <div class="col-lg-2 col-6">
        <h6>Services</h6>
        <ul class="list-unstyled"><li><a href="#">Buying</a></li><li><a href="#">Installments</a></li><li><a href="#">Legal Support</a></li><li><a href="#">Site Visits</a></li></ul>
      </div>
      <div class="col-lg-4">
        <h6>Newsletter</h6>
        <p style="font-size:.88rem;">Get new launches and offers in your inbox.</p>
        <form class="d-flex newsletter-form" onsubmit="event.preventDefault(); this.reset(); alert('Subscribed successfully.');">
          <input type="email" placeholder="Your email" required class="form-control">
          <button type="submit">Join</button>
        </form>
      </div>
    </div>
    <div class="footer-bottom text-center">&copy; <span id="year"></span> {{ setting('footer_copyright', 'Aurelia Estates. All rights reserved.') }}</div>
  </div>
</footer>

<a href="https://wa.me/8801XXXXXXXXX" class="whatsapp-float" target="_blank" rel="noopener" aria-label="Chat on WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
<button class="back-to-top" id="backToTop" aria-label="Back to top"><i class="fa-solid fa-arrow-up"></i></button>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/aos@2.3.4/dist/aos.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  AOS.init({ duration: 700, once: true, offset: 60 });
  document.getElementById('year').textContent = new Date().getFullYear();

  /* Preloader */
  window.addEventListener('load', () => {
    setTimeout(() => document.getElementById('preloader').classList.add('hide'), 300);
  });

  /* Scroll progress + navbar + back to top */
  const progress = document.getElementById('scrollProgress');
  const nav = document.getElementById('mainNav');
  const backToTop = document.getElementById('backToTop');
  window.addEventListener('scroll', () => {
    const h = document.documentElement;
    const scrolled = (h.scrollTop) / (h.scrollHeight - h.clientHeight) * 100;
    progress.style.width = scrolled + '%';
    nav.classList.toggle('scrolled', window.scrollY > 40);
    backToTop.classList.toggle('show', window.scrollY > 600);
  }, { passive: true });
  backToTop.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

  /* Custom cursor */
  const dot = document.getElementById('cursorDot');
  const ring = document.getElementById('cursorRing');
  window.addEventListener('mousemove', (e) => {
    dot.style.left = e.clientX + 'px'; dot.style.top = e.clientY + 'px';
    ring.style.left = e.clientX + 'px'; ring.style.top = e.clientY + 'px';
  });
  document.querySelectorAll('a, button, .gallery-item, .project-card').forEach(el => {
    el.addEventListener('mouseenter', () => { ring.style.width='54px'; ring.style.height='54px'; ring.style.borderColor='rgba(212,175,55,.9)'; });
    el.addEventListener('mouseleave', () => { ring.style.width='36px'; ring.style.height='36px'; ring.style.borderColor='rgba(212,175,55,.6)'; });
  });

  /* Mouse parallax on hero floating shapes */
  const shapes = document.querySelectorAll('.floating-shape');
  document.querySelector('.hero').addEventListener('mousemove', (e) => {
    const x = (e.clientX / window.innerWidth - 0.5) * 30;
    const y = (e.clientY / window.innerHeight - 0.5) * 30;
    shapes.forEach((s, i) => { s.style.transform = `translate(${x * (i+1)}px, ${y * (i+1)}px)`; });
  });

  /* Hero video graceful fallback */
  const video = document.getElementById('heroVideo');
  video.addEventListener('canplay', () => video.classList.add('loaded'));
  video.addEventListener('error', () => video.style.display = 'none');

  /* Counters */
  const counters = document.querySelectorAll('[data-count]');
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
        else el.textContent = target.toLocaleString('en-US') + suffix;
      }
      requestAnimationFrame(tick);
      counterObserver.unobserve(el);
    });
  }, { threshold: 0.4 });
  counters.forEach(c => counterObserver.observe(c));

  /* Video tour play button */
  document.getElementById('playTourBtn').addEventListener('click', function(){
    this.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
    setTimeout(() => alert('Virtual tour would open here — connect your video provider (YouTube/Vimeo) embed.'), 500);
  });
});

/* Lightbox */
function openLightbox(el){
  const src = el.querySelector('img').src;
  document.getElementById('lightboxImg').src = src;
  document.getElementById('lightboxOverlay').classList.add('show');
}
function closeLightbox(){ document.getElementById('lightboxOverlay').classList.remove('show'); }
document.getElementById('lightboxOverlay').addEventListener('click', (e) => { if(e.target.id === 'lightboxOverlay') closeLightbox(); });

/* Contact form demo submit */
function handleContactSubmit(e){
  e.preventDefault();
  const form = e.target;
  const btn = form.querySelector('button[type="submit"]');
  btn.disabled = true; btn.textContent = 'Sending...';
  setTimeout(() => {
    form.innerHTML = '<div class="text-center py-4"><i class="fa-solid fa-circle-check fa-3x mb-3" style="color:var(--gold);"></i><h5>Enquiry Sent!</h5><p style="font-size:.9rem;">Our team will reach out within 24 hours.</p></div>';
  }, 800);
  return false;
}
</script>
</body>
</html>

