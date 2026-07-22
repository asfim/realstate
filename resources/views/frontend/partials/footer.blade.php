<footer class="site-footer">
  <div class="container">
    <div class="row g-4 mb-4">
      <div class="col-lg-4">
        <div class="footer-brand mb-3">AURELIA<span>.</span></div>
        <p style="font-size:.9rem; max-width:300px;">Two decades of building verified, premium properties people are proud to call home.</p>
        <div class="footer-social mt-3">
          <a href="#"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="#"><i class="fa-brands fa-instagram"></i></a>
          <a href="#"><i class="fa-brands fa-linkedin-in"></i></a>
          <a href="#"><i class="fa-brands fa-youtube"></i></a>
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
    <div class="footer-bottom text-center">&copy; <span id="year"></span> Aurelia Estates. All rights reserved.</div>
  </div>
</footer>

<a href="https://wa.me/8801XXXXXXXXX" class="whatsapp-float" target="_blank" rel="noopener" aria-label="Chat on WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
<button class="back-to-top" id="backToTop" aria-label="Back to top"><i class="fa-solid fa-arrow-up"></i></button>
