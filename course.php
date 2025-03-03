<?php include "header.php"; 

$course_id = $_GET['course'] ?? null;
if (!$course_id) {
  header("Location: courses.php");
  exit();
}


$query = "SELECT c.*, l.title AS category FROM ".$siteprefix."courses c LEFT JOIN ".$siteprefix."languages l ON c.language=l.s WHERE c.s = '$course_id'";
$result = mysqli_query($con, $query);
if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    // Accessing individual fields
    $course_id = $row['s'];
    $title = $row['title'];
    $description = $row['description'];
    $category = $row['category'];
    $category_id = $row['language'];
    $level = $row['level'];
    $type = $row['type'];
    $Dateupdated = $row['updated_date'];
    $status = $row['status'];
    $dateCreated = $row['created_date'];
    $owner = $row['updated_by'];
    $course_media = $row['featured_image'];

    $formatedupdatedate = formatDateTime2($Dateupdated);
    $formateduploaddate = formatDateTime2($dateCreated);
    $limitedDescription =$description;

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
  }
} else {
  header("Location: courses.php");
  exit();
}
//check if user is enrolled
$is_enrolled = isEnrolled($user_id, $course_id, $con, $siteprefix);
$enrolled_text = $is_enrolled ? "Continue Learning" : "Start Now";
?>
<main class="main">



<section id="clients" class="clients section">
<div class="row bg-dark p-3" style="position: relative; z-index: 1;">
  <div class="col-lg-1 col-12"></div>
  <div class="col-lg-7 col-12 d-flex align-items-center pt-3 mb-5">
    <h3 class="title text-light"><?php echo htmlspecialchars($title); ?></h3>
  </div> 
</div>


<div class="row p-5" style="position: relative; margin-top: -100px; z-index:99;">
  <div class="col-lg-8">
    <div class="card filter-container">
      <div class="card-body">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true"> <i class="bi bi-bookmark"></i> Overview</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false"><i class="bi bi-bookmark"></i> Curriculum</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false"><i class="bi bi-bookmark"></i> Reviews</button></li>
        </ul>
        <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade show active p-3" id="home" role="tabpanel" aria-labelledby="home-tab">
            <p><?php echo htmlspecialchars($description); ?></p>
          </div>
          <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
<?php 
$querys = "SELECT * FROM ".$siteprefix."theory WHERE course_id = '$course_id' ORDER BY chapter ASC";
$results = mysqli_query($con, $querys);

if (mysqli_num_rows($results) > 0) {
  while ($rows = mysqli_fetch_assoc($results)) {
    // Accessing individual fields
    $chapter = $rows['chapter'];
    $section_title = $rows['title'];
    $section_description = limitDescription($rows['subtitle']);
?>
<li class="p-2"><span class="text-bold">Section <?php echo $chapter;?> (<?php echo $section_title;?>):</span><?php echo $section_description;?></li>
<?php }}

if($course_id==1){
  $sql = "SELECT MAX(level) as last_level FROM ".$siteprefix."game_progress 
    WHERE user_id = ? AND status = 'completed'";
  $stmt = $con->prepare($sql);
  $stmt->bind_param("i", $user_id);
  $stmt->execute();
  $result = $stmt->get_result();
  $row = $result->fetch_assoc();
  $last_level = $row['last_level'] ?? 0;
  
  if($last_level >= 40) {
    echo "<li class='p-2'><span class='text-bold'>Congratulations! You have completed all levels.</span></li>";
            // Check for existing review
            $review_query = "SELECT * FROM {$siteprefix}reviews WHERE course_id = $course_id AND user = $user_id";
            $review_result = mysqli_query($con, $review_query);
            $existing_review = mysqli_fetch_assoc($review_result);
          ?>
            <div class="review-section mt-4">
              <h6 class="mb-3">Leave a Review</h6>
              <form id="reviewForm" method="POST">
                <div class="rating mb-3">
                  <?php for($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" name="rating" value="<?php echo $i; ?>" id="star<?php echo $i; ?>" 
                      <?php echo ($existing_review && $existing_review['rating'] == $i) ? 'checked' : ''; ?>>
                    <label for="star<?php echo $i; ?>">☆</label>
                  <?php endfor; ?>
                </div>
                <div class="form-group">
                  <textarea class="form-control" name="review" rows="3" placeholder="Write your review here..."><?php echo $existing_review ? $existing_review['review'] : ''; ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-3">
                  <?php echo $existing_review ? 'Update Review' : 'Submit Review'; ?>
                </button>
              </form>
            </div>

            <script>
            document.getElementById('reviewForm').onsubmit = function(e) {
              e.preventDefault();
              const formData = new FormData(this);
              formData.append('course_id', <?php echo $course_id; ?>);
              formData.append('action', '<?php echo $existing_review ? 'update' : 'create'; ?>');

              fetch('submit_review.php', {
                method: 'POST',
                body: formData
              })
              .then(response => response.json())
              .then(data => {
                if(data.success) {
                  alert('Review ' + '<?php echo $existing_review ? 'updated' : 'submitted'; ?>' + ' successfully!');
                  location.reload();
                } else {
                  alert('Error: ' + data.message);
                }
              });
            };
            </script>

            <style>
            .rating {
              display: flex;
              flex-direction: row-reverse;
              justify-content: flex-end;
            }
            .rating input {
              display: none;
            }
            .rating label {
              font-size: 24px;
              color: #ddd;
              cursor: pointer;
            }
            .rating input:checked ~ label,
            .rating label:hover,
            .rating label:hover ~ label {
              color: #1AD8FC;
            }
            </style>
          <?php  }} else { echo "<p>You can only leave a review when you have completed this course</p>"; }?>
          </div>

          <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
            <div class="card p-3">
              <div class="card-body">
                <h5 class="card-title text-bold">Reviews</h5>
                <div class="d-flex justify-content-between">
                  <div class="d-flex">
                    <div class="review-stars">
                      <?php for ($i = 1; $i <= $average_rating; $i++) { ?><i class="bi bi-star"></i><?php } ?>
                    </div>
                    <div class="ms-2"><?php echo $average_rating; ?> (<?php echo $review_count; ?> reviews)</div>
                  </div>
                 </div>
                 <div class="mt-3 mb-3">
                  <?php
                      $review_query = "SELECT r.*, u.name FROM ".$siteprefix."reviews r 
                              LEFT JOIN ".$siteprefix."users u ON r.user = u.s 
                              WHERE r.course_id = '$course_id' 
                              ORDER BY r.date DESC LIMIT 10";
                      $review_result = mysqli_query($con, $review_query);

                      while ($review = mysqli_fetch_assoc($review_result)) {
                        echo '<div class="mb-3">';
                        echo '<div class="d-flex align-items-center">';
                        echo '<strong>' . htmlspecialchars($review['name']) . '</strong>';
                        echo '<div class="ms-3">';
                        for ($i = 1; $i <= $review['rating']; $i++) {
                          echo '<i class="bi bi-star-fill text-warning"></i>';
                        }
                        echo '</div></div>';
                        echo '<p class="mt-2">' . htmlspecialchars($review['review']) . '</p>';
                        echo '<small class="text-muted">' . date('M d, Y', strtotime($review['date'])) . '</small>';
                        echo '</div>';
                      }
                      ?>
                  </div>
                </div></div></div>



        </div>
      </div>
    </div>
  </div>

  <div class="col-lg-4 mt-3">
    <div class="card p-3 filter-container">
      <img src="uploads/<?php echo htmlspecialchars($course_media); ?>" class="card-img-top rounded" alt="Course Image">
      <div class="card-body">
        <h2 class="card-title text-bold"><?php echo htmlspecialchars($title); ?></h2>
      </div>
      <ul class="list-group list-group-flush">
        <li class="list-group-item">
          <div class="d-flex bd-highlight">
            <div class="me-auto p-2 bd-highlight"><i class="bi bi-card-heading text-danger"></i> Lectures</div>
            <div class="p-2 bd-highlight"><?php echo htmlspecialchars($lesson_count); ?></div>
          </div>
        </li>
        <li class="list-group-item">
          <div class="d-flex bd-highlight">
            <div class="me-auto p-2 bd-highlight"><i class="bi bi-patch-check text-success"></i> Skill Level</div>
            <div class="p-2 bd-highlight"><?php echo htmlspecialchars($level); ?></div>
          </div>
        </li>
        <li class="list-group-item">
          <div class="d-flex bd-highlight">
            <div class="me-auto p-2 bd-highlight"><i class="bi bi-flag text-danger"></i> Expiry Period</div>
            <div class="p-2 bd-highlight">Lifetime</div>
          </div>
        </li>
        <li class="list-group-item">
          <div class="d-flex bd-highlight">
            <div class="me-auto p-2 bd-highlight"><i class="bi bi-mortarboard text-primary"></i> Certificate</div>
            <div class="p-2 bd-highlight">Yes</div>
          </div>
        </li>
        <li class="list-group-item">
          <?php if ($course_id==1){ ?>
            <p><a href="game.php" class="btn-get-started w-100 text-center">Play Game</a></p>
          <?php } else { ?>
          <p><a href="course-view.php?course=<?php echo $course_id; ?>" class="btn-get-started w-100 text-center"><?php echo $enrolled_text; ?></a></p>
          <?php } ?>
          <div class="d-flex justify-content-center">
            <?php
            $share_url = urlencode("https://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
            $share_title = urlencode($title);
            $share_text = urlencode("Check out this course: " . $title);
            ?>
            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $share_url; ?>" target="_blank"><i class="bi bi-facebook text-primary p-2"></i></a>
            <a href="https://twitter.com/intent/tweet?text=<?php echo $share_text; ?>&url=<?php echo $share_url; ?>" target="_blank"><i class="bi bi-twitter-x text-dark p-2"></i></a>
            <a href="https://api.whatsapp.com/send?text=<?php echo $share_text . ' ' . $share_url; ?>" target="_blank"><i class="bi bi-whatsapp text-success p-2"></i></a>
            <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?php echo $share_url; ?>" target="_blank"><i class="bi bi-linkedin text-primary p-2"></i></a>
          </div>
        </li>
      </ul>
    </div>
  </div>
</div>
</section>



<section id="clients" class="clients section">
<div class="row p-5">
<h3 class="text-bold">Related Courses </h3>
<?php 

$query = "SELECT c.*, l.title AS category FROM ".$siteprefix."courses c LEFT JOIN ".$siteprefix."languages l ON c.language=l.s WHERE c.language='$category_id' AND c.status='publish'";
$result = mysqli_query($con, $query);
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


$formatted_duration = formatDuration($total_duration);
$is_favorite = isFavorite($user_id, $course_id, $con, $siteprefix);

?>
              <div class="col-lg-3 col-md-6 col-12">
              <div class="course-box">
              <div class="course-image"> <img src="uploads/<?php echo htmlspecialchars($course_media); ?>" alt="Course Image">
              <div class="course-label"><?php echo $level; ?></div>
              <?php if(isset($user_id) && !empty($user_id)) { ?>
              <button class="wishlist-btn" id="favorite-btn-<?php echo $course_id; ?>" onclick="toggleFavorite(<?php echo $user_id; ?>,<?php echo $course_id; ?>)">
              <i class="bi bi-heart-fill <?php echo $is_favorite ? 'text-primary' : ''; ?>"></i>
              </button><?php } else { ?>
              <a href="signin.php" class="wishlist-btn"><i class="bi bi-heart-fill"></i></a><?php } ?>
              </div>
              <!-- Course Title -->
               <div class="course-content">
              <h6 class="course-title text-bold"><?php echo htmlspecialchars($title); ?></h6>
              <!-- Reviews and Action -->
              <div class="course-meta d-flex justify-content-between align-items-center">
              <div class="review-stars">
              <?php for ($i = 1; $i <= $average_rating; $i++) { ?><i class="bi bi-star"></i><?php } ?>
              </div>
              <a href="course.php?course=<?php echo $course_id; ?>" class="btn-get-started"><i class="bi bi-search"></i> Start Now</a></div>
              <!-- Description -->
              <p class="course-description"><?php echo $limitedDescription; ?></p>
              <hr class="separator">
              <div class="course-info d-flex justify-content-between">
              <span class="info-text"><?php echo $lesson_count; ?> Lessons</span>
              <span class="info-text"><span class="time"><i class="bi bi-alarm"></i></span> <?php echo $formatted_duration; ?></span>
              </div>
              </div>
              </div>
              </div>

<?php }}else { 
    echo "<div class='alert alert-warning' role='alert'>No related courses found.</div>"; 
}
?>
              
</div>
</section>



</main>
<?php include "footer.php"; ?>