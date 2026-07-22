@extends('frontend.layouts.app')
@section('content')

<!-- PAGE HEADER -->
<div class="page-header">
  <div class="container">
    <h1 data-aos="fade-up">News & Insights</h1>
    <p data-aos="fade-up" data-aos-delay="100">Latest updates from the world of luxury real estate.</p>
  </div>
</div>
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

@endsection
