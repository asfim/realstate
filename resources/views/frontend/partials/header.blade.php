<nav class="navbar navbar-expand-lg navbar-luxury {{ request()->routeIs('home') || request()->routeIs('property.details') ? '' : 'navbar-inner-page' }}" id="mainNav">
  <div class="container">
    <a class="navbar-brand" href="{{ route('home') }}">AURELIA<span>.</span></a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navMenu"><i class="fa-solid fa-bars"></i></button>
    <div class="collapse navbar-collapse" id="navMenu">
      <ul class="navbar-nav ms-auto align-items-lg-center gap-lg-2">
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('home') ? 'active' : '' }}" href="{{ route('home') }}">Home</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('projects') ? 'active' : '' }}" href="{{ route('projects') }}">Projects</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('properties') ? 'active' : '' }}" href="{{ route('properties') }}">Properties</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('services') ? 'active' : '' }}" href="{{ route('services') }}">Services</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('about') ? 'active' : '' }}" href="{{ route('about') }}">About</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('gallery') ? 'active' : '' }}" href="{{ route('gallery') }}">Gallery</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('blog') ? 'active' : '' }}" href="{{ route('blog') }}">Blog</a></li>
        <li class="nav-item"><a class="nav-link {{ request()->routeIs('contact') ? 'active' : '' }}" href="{{ route('contact') }}">Contact</a></li>
        <li class="nav-item ms-lg-3"><a class="btn btn-gold btn-sm" href="{{ route('contact') }}">Book a Visit</a></li>
      </ul>
    </div>
  </div>
</nav>
