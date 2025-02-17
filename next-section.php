<?php include "header.php";

$course_id = $_GET['course'] ?? null;
$section_id = $_GET['section'] ?? null;
if (!$course_id) {
    header("Location: $previousPage");
    exit();
}

$coursename="";
addCourseProgress($con, $user_id, $course_id, $section_id, $coursename);
header("Location: course-view.php?course=$course_id");
exit;
?>
