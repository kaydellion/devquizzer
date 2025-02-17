<?php include 'header.php'; ?>
<main class="main">

<section>
<div class="row bg-dark p-5">
    <div class="col-lg-2 col-12">
        <img src="uploads/<?php echo htmlspecialchars($profile_picture); ?>" alt="Avatar" class="img-fluid rounded-circle">
    </div>
    <div class="col-lg-10 col-12 d-flex align-items-center pt-3 mb-5">
        <div class="d-flex flex-column w-100">
                <div class="d-flex">
                <?php include "links.php"; ?>
                </div>
                <h2 class="title text-primary text-bold mt-3 mb-5">Hi, <?php echo htmlspecialchars($name); ?></h2>
 </div>
</div> 
</div>

<div class="row p-5 mb-5">
<?php 
$newquery = "SELECT ec.*, c.title, c.description FROM {$siteprefix}enrolled_courses ec 
             LEFT JOIN {$siteprefix}courses c ON ec.course_id = c.s 
             WHERE ec.user_id = $user_id AND ec.certificate = 1";
$newresult = mysqli_query($con, $newquery);

if (mysqli_num_rows($newresult) > 0) {
    while ($newrow = mysqli_fetch_assoc($newresult)) {
        $coursename = $newrow['title'] ?? '';
        $course_text = $newrow['description'] ?? '';
        
        echo '<div class="col-lg-4 col-md-6 col-12 mb-4">' .
            '<div class="certificate-container">' .
            '<div class="certificate-preview" style="background-image: url(\'assets/img/certificate.png\');">' .
            '</div>' .
            '<h6 class="text-center">' . htmlspecialchars($coursename) . '</h6>' .
            '<form action="certificate.php" method="POST" class="text-center">' .
                '<input type="hidden" name="name" value="' . htmlspecialchars($name) . '">' .
                '<input type="hidden" name="course" value="' . htmlspecialchars($coursename) . '">' .
                '<input type="hidden" name="content" value="' . htmlspecialchars($course_text) . '">' .
                '<input type="hidden" name="download" value="true">' .
                '<button type="submit" class="btn btn-dark">Download Certificate</button>' .
            '</form>' .
            '</div>' .
        '</div>';
    }
} else {
    echo '<div class="col-12"><p class="mt-3">No completed courses found with certificates.</p></div>';
}

?>

</div>



</section>
</main>
<?php include 'footer.php'; ?>