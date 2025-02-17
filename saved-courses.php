<?php include 'header.php'; 
$query = "SELECT c.*, l.title AS category FROM ".$siteprefix."favorites f LEFT JOIN ".$siteprefix."courses c ON f.course_id=c.s LEFT JOIN ".$siteprefix."languages l ON c.language=l.s WHERE f.user_id='$user_id' AND c.status='publish'";
$result = mysqli_query($con, $query);
$item_count=mysqli_num_rows($result);
?>

<section id="clients" class="clients section">
<div class="row p-5">
<h3 class="text-bold">Saved Courses (<?php echo $item_count;?>)</h3>
<?php 
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

$hours = floor($total_duration / 60);
$minutes = $total_duration % 60;
$formatted_duration = sprintf("%02d:%02d", $hours, $minutes);

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
              <p class="course-description"><?php echo $description; ?></p>
              <hr class="separator">
              <div class="course-info d-flex justify-content-between">
              <span class="info-text"><?php echo $lesson_count; ?> Lessons</span>
              <span class="info-text"><span class="time"><i class="bi bi-alarm"></i></span> <?php echo $formatted_duration; ?></span>
              </div>
              </div>
              </div>
              </div>

<?php }}else { 
    echo "<div class='alert alert-warning' role='alert'>No courses found for <a href='courses.php'>View More Courses</a></div>"; 
}
?>
              
</div>
</section>

<?php include 'footer.php'; ?>