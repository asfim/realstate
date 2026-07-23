<footer class="site-footer">
  <div class="container">
    <div class="row g-4 mb-4">
      <div class="col-lg-4">
        @if(setting('footer_logo'))
          <img src="{{ asset_url(setting('footer_logo')) }}" alt="Logo" style="height: 50px; margin-bottom: 15px;">
        @elseif(setting('site_logo'))
          <img src="{{ asset_url(setting('site_logo')) }}" alt="Logo" style="height: 50px; margin-bottom: 15px;">
        @else
          <div class="footer-brand mb-3">{{ setting('site_title', 'AURELIA') }}<span>.</span></div>
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
        <ul class="list-unstyled"><li><a href="{{ route('about') }}">About Us</a></li><li><a href="{{ route('projects') }}">Projects</a></li><li><a href="{{ route('gallery') }}">Gallery</a></li><li><a href="{{ route('contact') }}">Contact</a></li></ul>
      </div>
      <div class="col-lg-2 col-6">
        <h6>Services</h6>
        <ul class="list-unstyled"><li><a href="{{ route('services') }}">Buying</a></li><li><a href="{{ route('services') }}">Installments</a></li><li><a href="{{ route('services') }}">Legal Support</a></li><li><a href="{{ route('services') }}">Site Visits</a></li></ul>
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
