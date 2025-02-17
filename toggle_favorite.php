<?php 
include "backend/connect.php";

// Get user ID and course ID from request
$user_id = $_POST['user_id'];
$course_id = $_POST['course_id'];

// Check if the course is already favorited by the user
$sql = "SELECT * FROM dv_favorites WHERE user_id = ? AND course_id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("ii", $user_id, $course_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // If the course is already favorited, remove it from favorites
    $sql = "DELETE FROM dv_favorites WHERE user_id = ? AND course_id = ?";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ii", $user_id, $course_id);
    $stmt->execute();
    echo "removed";
} else {
    // If the course is not favorited, add it to favorites
    $sql = "INSERT INTO dv_favorites (user_id, course_id) VALUES (?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->bind_param("ii", $user_id, $course_id);
    $stmt->execute();
    echo "added";
}

$stmt->close();
?>
