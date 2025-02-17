<?php include "header.php"; ?>

<main class="main">

<!-- Hero Section -->
<section id="hero" class="hero section">
  <img src="assets/img/Background.png" alt="" data-aos="fade-in">
  <div class="container">
    <div class="row">
      <div class="col-lg-12 text-center">
        <p class="spaced-text">Welcome to Devquizzer</p>
        <h2 data-aos="fade-up" data-aos-delay="100">Learn Coding and <br> Programming the <span> Fun Way! </span></h2>
        <p data-aos="fade-up" data-aos-delay="200">Master theory and practical skills through engaging courses and gamified learning experiences.</p>
        <div class="d-flex mt-4" style="justify-content:center;" data-aos="fade-up" data-aos-delay="300">
          <a href="game.php" class="btn-get-started bg-secondary m-1">Play a game <i class="bi bi-arrow-right"></i></a>
          <a href="courses.php" class="btn-get-started m-1">Popular Courses <i class="bi bi-arrow-right"></i></a>
        </div>

      </div>
    </div>
  </div>

</section><!-- /Hero Section -->


 <!-- Icon Box Section -->
 <section id="clients" class="clients section">
    <div class="cut-off-container">
    
    <div class="row">
    
    <div class="col-lg-4">
    <div class="icon-box">
        <div class="icon">
        <i class="bi bi-briefcase"></i>
        </div>
        <div class="content">
            <h3>Expert Learning</h3>
            <p>Find the right course for you</p>
        </div>
    </div></div>

    <div class="col-lg-4">
    <div class="icon-box">
        <div class="icon">
        <i class="bi bi-mortarboard-fill"></i>
        </div>
        <div class="content">
            <h3>16+ Progamming Courses</h3>
            <p>Explore a variety of fresh topics</p>
        </div>
    </div></div>

    <div class="col-lg-4">
    <div class="icon-box">
        <div class="icon">
        <i class="bi bi-key"></i>
        </div>
        <div class="content">
            <h3>Access for free</h3>
            <p>Learn on your schedule</p>
        </div>
    </div></div>

</div>
</div>
</section>



 <!-- Upcoming Courses Section -->
<section id="clients" class="clients section">
<div class="row p-5">
<div class="col-lg-6">
<h3 class="text-bold">Explore our upcoming courses </h3>
</div>

<div class="col-lg-6">
<blockquote class="custom-blockquote">
<p>Discover a world of learning opportunities through our upcoming
courses, where industry experts and thought leaders will guide you
in acquiring new expertise, expanding your horizons, and reaching
your full potential.</p>
</blockquote>
</div>
</div>

<div class="row side-padding">

<?php 

$query = "SELECT c.*, l.title AS category FROM ".$siteprefix."courses c LEFT JOIN ".$siteprefix."languages l ON c.language=l.s WHERE c.status='publish'";       
$result = mysqli_query($con, "SELECT c.*, l.title AS category, COUNT(e.course_id) as enrolled_count 
FROM ".$siteprefix."courses c 
LEFT JOIN ".$siteprefix."languages l ON c.language=l.s 
LEFT JOIN ".$siteprefix."enrolled_courses e ON c.s=e.course_id 
WHERE c.status='publish' 
GROUP BY c.s 
ORDER BY enrolled_count DESC 
LIMIT 6");
if(mysqli_num_rows($result) > 0 ) { 
while ($row = mysqli_fetch_assoc($result)) {
    // Accessing individual fields
    $course_id = $row['s'];
    $title = $row['title'];
    $description = limitDescription($row['description']);
    $category = $row['category'];
    $level = $row['level'];
    $type = $row['type'];
    $Dateupdated = $row['updated_date'];
    $status = $row['status'];
    $dateCreated = $row['created_date']; 
    $owner = $row['updated_by'];
    $course_media = $row['featured_image'];

    $formatedupdatedate = formatDateTime2($Dateupdated);
    $formateduploaddate = formatDateTime2($dateCreated);

$rating_data = calculateRating($course_id, $con, $siteprefix);
$average_rating = $rating_data['average_rating'];
$review_count = $rating_data['review_count'];

$lesson_query = "SELECT COUNT(*) as lesson_count, SUM(duration) as total_duration FROM " . $siteprefix . "theory WHERE course_id = '$course_id'";
$lesson_result = mysqli_query($con, $lesson_query);
$lesson_data = mysqli_fetch_assoc($lesson_result);

$lesson_count = $lesson_data['lesson_count'];
$total_duration = $lesson_data['total_duration'];

$hours = floor($total_duration / 60);
$minutes = $total_duration % 60;
$formatted_duration = sprintf("%02d:%02d", $hours, $minutes);

$is_favorite = isFavorite($user_id, $course_id, $con, $siteprefix);

?>
<div class="col-lg-4 col-md-6 mb-3">
<div class="card card-custom" style="background-image: url('uploads/<?php echo $course_media; ?>')">
<div class="card-label"><?php echo $category; ?></div>
 <!-- Bottom text box -->
<div class="card-content">
<h5><a href="course.php?course=<?php echo $course_id; ?>" class="text-dark"><?php echo $title; ?></a></h5>
<p>Released on : <?php echo $formateduploaddate; ?></p>
</div>
</div>
</div> 
<?php }} ?>

</div>
</section>


  <!-- Top courses Title -->
  <section id="testimonials" class="testimonials section dark-background">
  <div class="container" data-aos="fade-up">
  <h2>Top Courses</h2>
  <p>These are the most popular courses among listen courses learners worldwide</p>
</div><!-- End Section Title -->
      <div class="container" data-aos="fade-up" data-aos-delay="100">

        <div class="swiper init-swiper">
        <script type="application/json" class="swiper-config">
{
  "loop": true,
  "speed": 600,
  "autoplay": {
    "delay": 5000
  },
  "slidesPerView": "auto",
  "pagination": {
    "el": ".swiper-pagination",
    "type": "bullets",
    "clickable": true
  },
  "navigation": {
    "nextEl": ".swiper-button-next",
    "prevEl": ".swiper-button-prev"
  },
  "breakpoints": {
    "320": {
      "slidesPerView": 1,
      "spaceBetween": 40
    },
    "1200": {
      "slidesPerView": 3,
      "spaceBetween": 10
    }
  }
}
</script>

          <div class="swiper-wrapper">
             
          <?php 

$query = "SELECT c.*, l.title AS category FROM ".$siteprefix."courses c LEFT JOIN ".$siteprefix."languages l ON c.language=l.s WHERE c.status='publish'";       
$result = mysqli_query($con, "SELECT c.*, l.title AS category, COUNT(e.course_id) as enrolled_count 
FROM ".$siteprefix."courses c 
LEFT JOIN ".$siteprefix."languages l ON c.language=l.s 
LEFT JOIN ".$siteprefix."enrolled_courses e ON c.s=e.course_id 
WHERE c.status='publish' 
GROUP BY c.s 
ORDER BY enrolled_count DESC 
LIMIT 6");
if(mysqli_num_rows($result) > 0 ) { 
while ($row = mysqli_fetch_assoc($result)) {
    // Accessing individual fields
    $course_id = $row['s'];
    $title = $row['title'];
    $description = limitDescription($row['description']);
    $category = $row['category'];
    $level = $row['level'];
    $type = $row['type'];
    $Dateupdated = $row['updated_date'];
    $status = $row['status'];
    $dateCreated = $row['created_date']; 
    $owner = $row['updated_by'];
    $course_media = $row['featured_image'];

    $formatedupdatedate = formatDateTime2($Dateupdated);
    $formateduploaddate = formatDateTime2($dateCreated);

$rating_data = calculateRating($course_id, $con, $siteprefix);
$average_rating = $rating_data['average_rating'];
$review_count = $rating_data['review_count'];

$lesson_query = "SELECT COUNT(*) as lesson_count, SUM(duration) as total_duration FROM " . $siteprefix . "theory WHERE course_id = '$course_id'";
$lesson_result = mysqli_query($con, $lesson_query);
$lesson_data = mysqli_fetch_assoc($lesson_result);

$lesson_count = $lesson_data['lesson_count'];
$total_duration = $lesson_data['total_duration'];

$hours = floor($total_duration / 60);
$minutes = $total_duration % 60;
$formatted_duration = sprintf("%02d:%02d", $hours, $minutes);

$is_favorite = isFavorite($user_id, $course_id, $con, $siteprefix);

?>
              <div class="swiper-slide">
              <div class="course-box">
              <div class="course-image"> <img src="uploads/<?php echo $course_media;?>" alt="<?php echo $title; ?>">
              <div class="course-label"><?php echo $level; ?></div>
              <button class="wishlist-btn"><i class="bi bi-heart-fill"></i></button>
              </div>
              <!-- Course Title -->
               <div class="course-content">
              <h6 class="course-title text-bold"><?php echo $title; ?></h6>
              <!-- Reviews and Action -->
              <div class="course-meta d-flex justify-content-between align-items-center">
              <div class="review-stars">
              <?php for ($i = 1; $i <= $average_rating; $i++) { ?><i class="bi bi-star"></i><?php } ?>
              </div>
              <a href="course.php?course=<?php echo $course_id; ?>" class="btn-get-started"><i class="bi bi-search"></i> Start Now</a></div>
              <!-- Description -->
              <p class="course-description"><?php echo $description; ?></p>
              <hr class="separator">
              <div class="course-info d-flex justify-content-between">
              <span class="info-text"><?php echo $lesson_count; ?> Lessons</span>
              <span class="info-text"><span class="time"><i class="bi bi-alarm"></i></span>  <?php echo $formatted_duration;?></span>
              </div>
              </div>
              </div>
            </div><!-- End testimonial item -->
<?php }} ?>
 

      

          </div>
          <div class="swiper-pagination"></div>
          <div class="swiper-button-prev"></div>
          <div class="swiper-button-next"></div>
          </div>

      </div>
    </section>




     <!-- Categories Section -->
 <section id="clients" class="clients section">
  <!-- Section Title -->
  <div class="container section-title" data-aos="fade-up">
        <h2>Top Categories</h2>
        <p>These are the most popular courses among listen courses learners worldwide</p>
      </div><!-- End Section Title -->
    
    <div class="row side-padding">
    <?php
    $sql = "SELECT l.*, COUNT(c.s) as stats FROM " . $siteprefix . "languages l LEFT JOIN " . $siteprefix . "courses c ON l.s = c.language AND c.status='publish' GROUP BY l.s order by stats DESC";
    $sql2 = mysqli_query($con, $sql);
    while ($row = mysqli_fetch_array($sql2)) { ?>
    <div class="col-lg-4"><a href="category.php?item=<?php echo $row['s']; ?>">
    <div class="icon-box light-blue">
        <div class="icon circled">
        <i class="bi bi-<?php echo $row['display_picture']; ?>"></i>
        </div>
        <div class="content">
        <h3 class="text-dark"><?php echo $row['title']; ?></h3>
        <p><?php echo $row['stats']; ?> Courses</p>
        </div>
    </div></a></div>
    <?php } ?>


</div>
</div>
</section>



 <!-- Faq Section -->
<section id="faq" class="faq section">
<div class="container section-title" data-aos="fade-up">
  <h2>Frequently Asked Questions</h2>
  <p>Have something to know? Check here if you have any questions about us.</p>
</div><!-- End Section Title -->

<div class="container">

  <div class="row justify-content-center">

    <div class="col-lg-10" data-aos="fade-up" data-aos-delay="100">

      <div class="faq-container">

        <div class="faq-item faq-active">
          <h3>What is DevQuizzer?</h3>
          <div class="faq-content">
            <p>Dev Quizzer is an innovative learning platform where you can master coding and programming skills through theory-based courses and gamified learning experiences. Whether you're a beginner or looking to enhance your skills, we’ve got you covered.</p>
          </div>
          <i class="faq-toggle bi bi-chevron-right"></i>
        </div><!-- End Faq item-->

        <div class="faq-item">
          <h3>Feugiat scelerisque varius morbi enim nunc faucibus?</h3>
          <div class="faq-content">
            <p>Dolor sit amet consectetur adipiscing elit pellentesque habitant morbi. Id interdum velit laoreet id donec ultrices. Fringilla phasellus faucibus scelerisque eleifend donec pretium. Est pellentesque elit ullamcorper dignissim. Mauris ultrices eros in cursus turpis massa tincidunt dui.</p>
          </div>
          <i class="faq-toggle bi bi-chevron-right"></i>
        </div><!-- End Faq item-->

        <div class="faq-item">
          <h3>Dolor sit amet consectetur adipiscing elit pellentesque?</h3>
          <div class="faq-content">
            <p>Eleifend mi in nulla posuere sollicitudin aliquam ultrices sagittis orci. Faucibus pulvinar elementum integer enim. Sem nulla pharetra diam sit amet nisl suscipit. Rutrum tellus pellentesque eu tincidunt. Lectus urna duis convallis convallis tellus. Urna molestie at elementum eu facilisis sed odio morbi quis</p>
          </div>
          <i class="faq-toggle bi bi-chevron-right"></i>
        </div><!-- End Faq item-->

        <div class="faq-item">
          <h3>Ac odio tempor orci dapibus. Aliquam eleifend mi in nulla?</h3>
          <div class="faq-content">
            <p>Dolor sit amet consectetur adipiscing elit pellentesque habitant morbi. Id interdum velit laoreet id donec ultrices. Fringilla phasellus faucibus scelerisque eleifend donec pretium. Est pellentesque elit ullamcorper dignissim. Mauris ultrices eros in cursus turpis massa tincidunt dui.</p>
          </div>
          <i class="faq-toggle bi bi-chevron-right"></i>
        </div><!-- End Faq item-->

        <div class="faq-item">
          <h3>Tempus quam pellentesque nec nam aliquam sem et tortor?</h3>
          <div class="faq-content">
            <p>Molestie a iaculis at erat pellentesque adipiscing commodo. Dignissim suspendisse in est ante in. Nunc vel risus commodo viverra maecenas accumsan. Sit amet nisl suscipit adipiscing bibendum est. Purus gravida quis blandit turpis cursus in</p>
          </div>
          <i class="faq-toggle bi bi-chevron-right"></i>
        </div><!-- End Faq item-->

        <div class="faq-item">
          <h3>Perspiciatis quod quo quos nulla quo illum ullam?</h3>
          <div class="faq-content">
            <p>Enim ea facilis quaerat voluptas quidem et dolorem. Quis et consequatur non sed in suscipit sequi. Distinctio ipsam dolore et.</p>
          </div>
          <i class="faq-toggle bi bi-chevron-right"></i>
        </div><!-- End Faq item-->

      </div>

    </div><!-- End Faq Column-->

  </div>

</div>
</section><!-- /Faq Section -->



 <!-- Faq Section -->
<section id="faq" class="faq section">
<div class="container section-title" data-aos="fade-up">
  <h2>Why Choose DevQuizzer?</h2>
  <p>Gather your thoughts, and make your decisions clearly</p>
</div><!-- End Section Title -->

<div class="container">
   <div class="row justify-content-center">
  <div class="col-lg-10" data-aos="fade-up" data-aos-delay="100">

  <div class="workflow-container">
    <div class="workflow-step">
        <div class="step-number">
            <span class="number">1</span>
            <div class="vertical-line"></div>
        </div>
        <div class="step-content">
            <h3 class="step-title">Interactive Learning</h3>
            <p class="step-description">Gamify your coding journey with levels, and challenges. Learn by doing and keep track of your progress in a fun and engaging way! </p>
        </div>
        <div class="step-image">
            <img src="assets/img/workflow.jpeg" alt="Workflow Step Image">
        </div>
    </div>
</div>

<p><hr class="divider"></p>

<div class="workflow-container">
    <div class="workflow-step">
        <div class="step-number">
            <span class="number">2</span>
            <div class="vertical-line"></div>
        </div>
        <div class="step-content">
            <h3 class="step-title">Theory / Gamified Based Courses</h3>
            <p class="step-description">Build a strong foundation with expertly crafted theory-based lessons. Master the concepts before jumping into coding </p>
        </div>
        <div class="step-image">
            <img src="assets/img/workflow.jpeg" alt="Workflow Step Image">
        </div>
    </div>
</div>

<p><hr class="divider"></p>

<div class="workflow-container">
    <div class="workflow-step">
        <div class="step-number">
            <span class="number">3</span>
            <div class="vertical-line"></div>
        </div>
        <div class="step-content">
            <h3 class="step-title">Coding in Practice</h3>
            <p class="step-description">Work on hands-on projects to apply your skills and build real-world applications as you learn. </p>
        </div>
        <div class="step-image">
            <img src="assets/img/workflow.jpeg" alt="Workflow Step Image">
        </div>
    </div>
</div>

 

</div><!-- End Faq Column-->
</div>

</div>
</section><!-- /Faq Section -->


</main>
<?php include "footer.php"; ?>