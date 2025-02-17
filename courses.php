<?php include "header.php"; 

$language_filter = isset($_POST['language']) ? $_POST['language'] : '';
$type_filter = isset($_POST['type']) ? $_POST['type'] : '';
$level_filter = isset($_POST['level']) ? $_POST['level'] : '';
$sort_filter = isset($_POST['filter-name']) ? $_POST['filter-name'] : 'new';

$query = "SELECT c.*, l.title AS category FROM ".$siteprefix."courses c LEFT JOIN ".$siteprefix."languages l ON c.language=l.s WHERE c.status='publish'";

if ($language_filter != '') {
$query .= " AND c.language = '$language_filter'";
}
if ($type_filter != '') {
$query .= " AND c.type = '$type_filter'";
}
if ($level_filter != '') {
$query .= " AND c.level = '$level_filter'";
}
if ($sort_filter == 'new') {
$query .= " ORDER BY c.created_date DESC";
} else if ($sort_filter == 'old') {
$query .= " ORDER BY c.created_date ASC";
} 

$result = mysqli_query($con, $query);
$filter_count=mysqli_num_rows($result);
?>

<main class="main">





<div class="row bg-dark mt-3 p-3">
<div class="col-lg-2 col-12 ml-5"></div>
<div class="col-lg-6 col-12 ml-5 d-flex align-items-center">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb text-light">
            <li class="breadcrumb-item"><a href="#"><i class="fas fa-home"></i> Home</a></li>
            <li class="breadcrumb-item active text-light" aria-current="page">Courses</li>
        </ol>  <h1 class="title text-light">Courses</h1>
    </nav>
</div> 
<div class="col-lg-4 d-none d-lg-block col-12"><img class="img-fluid" src="assets/img/course.png"/></div> 
</div>

<form method="POST" action="" id="filterForm">
<section>
<div class="row p-3 justify-content-center">
<div  class="col-lg-3 col-12">
<div class="card filter-container" >
    <button class="btn btn-primary mb-3 d-block d-md-none" onclick="toggleFilterBox()">Filter Courses</button>
    <div class="filter-box">
        <h5>Language</h5>
            <div class="form-check d-flex justify-content-between">
            <div><input class="form-check-input" type="radio" name="language" id="language1" value="" <?php echo ($language_filter == '') ? 'checked' : ''; ?> onchange="document.getElementById('filterForm').submit();">
            <label class="form-check-label" for="language1">All Languages</label></div><span>(..)</span></div>
            <?php
            $sql = "SELECT l.*, COUNT(c.s) as stats FROM " . $siteprefix . "languages l LEFT JOIN " . $siteprefix . "courses c ON l.s = c.language AND c.status='publish' GROUP BY l.s";
            $sql2 = mysqli_query($con, $sql);
            while ($row = mysqli_fetch_array($sql2)) { ?>

            <div class="form-check d-flex justify-content-between">
            <div><input class="form-check-input" type="radio" name="language" id="language<?php echo $row['s']; ?>" value="<?php echo $row['s']; ?>" <?php echo ($language_filter == $row['s']) ? 'checked' : ''; ?> onchange="document.getElementById('filterForm').submit();">
            <label class="form-check-label" for="language<?php echo $row['s']; ?>"><?php echo $row['title']; ?></label></div><span>(<?php echo $row['stats']; ?>)</span></div>

            <?php } ?>

            <h5>Type</h5>
            <div class="form-check">
            <input class="form-check-input" type="radio" name="type" id="type1" value="" <?php echo ($type_filter == '') ? 'checked' : ''; ?> onchange="document.getElementById('filterForm').submit();">
            <label class="form-check-label" for="type1">All Types</label>
            </div>
            <div class="form-check">
            <input class="form-check-input" type="radio" name="type" id="type2" value="Theory and Code" <?php echo ($type_filter == 'Theory and Code') ? 'checked' : ''; ?> onchange="document.getElementById('filterForm').submit();">
            <label class="form-check-label" for="type2">Theory and Code</label>
            </div>
            <div class="form-check">
            <input class="form-check-input" type="radio" name="type" id="type3" value="Theory" <?php echo ($type_filter == 'Theory') ? 'checked' : ''; ?> onchange="document.getElementById('filterForm').submit();">
            <label class="form-check-label" for="type3">Theory</label>
            </div>
            <div class="form-check">
            <input class="form-check-input" type="radio" name="type" id="type4" value="Code" <?php echo ($type_filter == 'Code') ? 'checked' : ''; ?> onchange="document.getElementById('filterForm').submit();">
            <label class="form-check-label" for="type4">Code</label>
            </div>

            <h5>Level</h5>
            <div class="form-check">
            <input class="form-check-input" type="radio" name="level" id="level1" value="" <?php echo ($level_filter == '') ? 'checked' : ''; ?> onchange="document.getElementById('filterForm').submit();">
            <label class="form-check-label" for="level1">All Levels</label>
            </div>
            <div class="form-check">
            <input class="form-check-input" type="radio" name="level" id="level2" value="Beginner" <?php echo ($level_filter == 'Beginner') ? 'checked' : ''; ?> onchange="document.getElementById('filterForm').submit();">
            <label class="form-check-label" for="level2">Beginner</label>
            </div>
            <div class="form-check">
            <input class="form-check-input" type="radio" name="level" id="level3" value="Intermediate" <?php echo ($level_filter == 'Intermediate') ? 'checked' : ''; ?> onchange="document.getElementById('filterForm').submit();">
            <label class="form-check-label" for="level3">Intermediate</label>
            </div>
            <div class="form-check">
            <input class="form-check-input" type="radio" name="level" id="level4" value="Expert" <?php echo ($level_filter == 'Expert') ? 'checked' : ''; ?> onchange="document.getElementById('filterForm').submit();">
            <label class="form-check-label" for="level4">Expert</label>
            </div>
    </div>
    </div>
    </div>
        

        <div class="col-lg-8 col-12">
        <div class="flex-container">
        <button class="btn btn-primary btn-outline"><i class="bi bi-list-ul"></i></button>
        <a href="courses.php" class="btn btn-outline-primary btn-outline"><i class="bi bi-arrow-clockwise"></i></a>
        <span>Showing <?php echo $filter_count; ?> results</span>
        <select name="filter-name" class="kayd-form-field" onchange="document.getElementById('filterForm').submit();">
            <option value="new" <?php echo ($sort_filter == 'new') ? 'selected' : ''; ?>>Newly Published</option>
            <option value="old" <?php echo ($sort_filter == 'old') ? 'selected' : ''; ?>>Oldest to New</option>
        </select>
        </div></form>

        <?php 

       
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

        <div class="card mb-3 w-100">
            <div class="row g-0">
                <div class="col-md-4">
                    <div class="position-relative h-100">
                        <img src="uploads/<?php echo $course_media; ?>" class="h-100 w-100 course-box-img" alt="...">
                        <div class="position-absolute top-0 end-0 m-2">
                            <?php if(isset($user_id) && !empty($user_id)) { ?>
                                <button class="btn btn-light rounded-circle" id="favorite-btn-<?php echo $course_id; ?>" onclick="toggleFavorite(<?php echo $user_id; ?>,<?php echo $course_id; ?>)">
                                    <i class="bi bi-heart-fill <?php echo $is_favorite ? 'text-primary' : ''; ?>"></i>
                                </button>
                            <?php } else { ?>
                                <a href="signin.php" class="btn btn-light rounded-circle">
                                    <i class="bi bi-heart-fill"></i>
                            </a>
                            <?php } ?>
                        </div>
                        <div class="position-absolute bottom-0 end-0 mb-2">
                            <span class="badge p-3 bg-info clip-label"><?php echo $level; ?></span>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="card-body">
                        <div class="d-flex bd-highlight mb-3">
                            <div class="me-auto p-2 bd-highlight">
                                <h5 class="card-title"><?php echo $title; ?></h5>
                                <p><?php echo $average_rating; ?> <i class="bi bi-star <?php if($average_rating > 0 ) { echo '-fill text-primary'; }?>"></i>
                                (<?php echo $review_count; ?>  Reviews) <i class="bi bi-card-heading"></i> <?php echo $category; ?></p>
                            </div>
                            <div class="p-2 bd-highlight">
                                <a href="course.php?course=<?php echo $course_id; ?>" class="btn-get-started text-small text-bold">Enroll Now</a>
                            </div>
                        </div>
                        <p class="card-text"><?php echo $description; ?></p>
                        <hr>
                        <div class="d-flex bd-highlight mb-3">
                            <div class="me-auto p-2 bd-highlight text-dark text-bold"><?php echo ucfirst(str_replace('_', ' ', $type)); ?></div>


                            <div class="p-2 bd-highlight text-small"><i class="bi bi-card-heading text-primary"></i> <?php echo $lesson_count; ?> Lessons</div>
                            <div class="p-2 bd-highlight text-small"><i class="bi bi-clock text-primary"></i> <?php echo $formatted_duration; ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    <?php 
    } 
} else { 
    echo "<div class='alert alert-warning' role='alert'>No courses found matching your criteria. Please try adjusting your filters.</div>"; 
}
?>



</div>
</section>



</main>
<?php include "footer.php"; ?>