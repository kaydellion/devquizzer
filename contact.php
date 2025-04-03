<?php include 'header.php'; ?>
<main class="main">
 <!-- Contact Section -->
 <section id="contact" class="contact section">

<!-- Section Title -->
<div class="container section-title" data-aos="fade-up">
  <h2>Contact</h2>
  <p>Study any topic, anytime. explore thousands of coding courses all in one place.</p>
</div><!-- End Section Title -->

<div class="container" data-aos="fade-up" data-aos-delay="100">

  <div class="row gy-4">
    <div class="col-lg-6 ">
      <div class="row gy-4">

        <div class="col-lg-12">
          <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="200">
            <i class="bi bi-geo-alt"></i>
            <h3>Address</h3>
            <p>South Africa</p>
          </div>
        </div><!-- End Info Item -->

        <div class="col-md-6">
          <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="300">
            <i class="bi bi-telephone"></i>
            <h3>Call Us</h3>
            <p><?php echo $sitenumber; ?></p>
          </div>
        </div><!-- End Info Item -->

        <div class="col-md-6">
          <div class="info-item d-flex flex-column justify-content-center align-items-center" data-aos="fade-up" data-aos-delay="400">
            <i class="bi bi-envelope"></i>
            <h3>Email Us</h3>
            <p><?php echo $sitemail; ?></p>
          </div>
        </div><!-- End Info Item -->

      </div>
    </div>

    <div class="col-lg-6">
      <form action="" method="post" class="php-email-form" data-aos="fade-up" data-aos-delay="500">
        <div class="row gy-4">

          <div class="col-md-6">
            <input type="text" name="name" class="form-control" placeholder="Your Name" required="">
          </div>

          <div class="col-md-6 ">
            <input type="email" class="form-control" name="email" placeholder="Your Email" required="">
          </div>

          <div class="col-md-12">
            <input type="text" class="form-control" name="subject" placeholder="Subject" required="">
          </div>

          <div class="col-md-12">
            <textarea class="form-control" name="message" rows="4" placeholder="Message" required=""></textarea>
          </div>

          <div class="col-md-12 text-center">
            <button type="submit" name="contact" value="message">Send Message</button>
          </div>

        </div>
      </form>
    </div><!-- End Contact Form -->

  </div>

</div>

</section><!-- /Contact Section -->
</main>
<?php include 'footer.php'; ?>