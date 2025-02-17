<?php
require_once 'backend/connect.php';

// Get JSON data
$data = json_decode(file_get_contents('php://input'), true);
$course_id = $data['course_id'];
$quiz_id = $data['quiz_id'];
$user_id = $_COOKIE['userID'];

// Create submission record with default/initial values
$query = "INSERT INTO dv_submissions (`user_id`, `quiz_id`, `type`, `course_id`, `score`, `start_date`, `submission_date`, `points`) 
         VALUES ('$user_id', '$quiz_id', 'quiz', '$course_id', 0, NOW(), '', 0)";

$result = mysqli_query($con, $query);

if (!$result) {
    error_log("Database Error: " . mysqli_error($con));
    die("Query failed: " . mysqli_error($con));
}

$submission_id = mysqli_insert_id($con);
echo json_encode(['submission_id' => $submission_id]);
?>
