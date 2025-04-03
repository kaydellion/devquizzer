</main>

  <footer id="footer" class="footer dark-background">

    <div class="container footer-top">
      <div class="row gy-4">
        <div class="col-lg-4 col-md-6 footer-about">
          <a href="index.html" class="logo d-flex align-items-center">
            <span class="sitename"><img src="assets/img/footer.png" class="logo"></span>
          </a>
          <div class="footer-contact mt-0">
            <p class="text-small">Study any topic, anytime. explore thousands of courses for
            the lowest price ever!</p>
            <div class="footer-newsletter">
          <p class="text-small">Subscribe to our newsletter and receive the latest news about our products and services!</p>
          <form method="post" class="php-email-form">
            <div class="newsletter-form"><input type="email" name="email"><input type="submit" name="subscribe" value="Subscribe"></div>
          </form>
        </div>
          </div>
        </div>

        <div class="col-lg-3 col-md-3 footer-links">
          <h4>Top Categories</h4>
          <ul>
          <?php
            $sql = "SELECT l.*, COUNT(c.s) as stats FROM " . $siteprefix . "languages l LEFT JOIN " . $siteprefix . "courses c ON l.s = c.language AND c.status='publish' GROUP BY l.s order by stats DESC LIMIT 5";
            $sql2 = mysqli_query($con, $sql);
            while ($row = mysqli_fetch_array($sql2)) { ?>
           <li><a href="category.php?item=<?php echo $row['s']; ?>"><?php echo $row['title']; ?></a></li>
            <?php } ?>
          </ul>
        </div>

        <div class="col-lg-3 col-md-3 footer-links">
          <h4>Useful Links</h4>
          <ul>
            <li><a href="index.php">Home</a></li>
            <li><a href="index.php#faq">About us</a></li>
            <li><a href="terms.php">Terms of service</a></li>
            <li><a href="privacy.php">Privacy policy</a></li>
          </ul>
        </div>

        <div class="col-lg-2 col-md-3 footer-links">
          <h4>Help</h4>
          <ul>
            <li><a href="courses.php">Courses</a></li>
            <li><a href="rewards.php">Leaderboard</a></li>
            <li><a href="index.php#faq">FAQ</a></li>
            <li><a href="contact.php">Contact</a></li>
          </ul>
        </div>



      </div>
    </div>

    <div class="container copyright text-center mt-4">
      <p>© <span>Copyright</span> <strong class="px-1 sitename">DevQuizzer</strong> <span>All Rights Reserved</span></p>
      <div class="credits">
       <!--  Designed by <a href="#" class="text-dark">Kayd</a> -->
      </div>
    </div>

  </footer>

  <!-- Scroll Top -->
  <a href="#" id="scroll-top" class="scroll-top d-flex align-items-center justify-content-center"><i class="bi bi-arrow-up-short"></i></a>

  <!-- Vendor JS Files -->
  <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
  <script src="assets/vendor/php-email-form/validate.js"></script>
  <script src="assets/vendor/aos/aos.js"></script>
  <script src="assets/vendor/glightbox/js/glightbox.min.js"></script>
  <script src="assets/vendor/swiper/swiper-bundle.min.js"></script>
  <script src="assets/vendor/purecounter/purecounter_vanilla.js"></script>
  <script src="assets/vendor/imagesloaded/imagesloaded.pkgd.min.js"></script>
  <script src="assets/vendor/isotope-layout/isotope.pkgd.min.js"></script>

  <!-- Main JS File -->
  <script src="assets/js/main.js"></script>

</body>

</html>