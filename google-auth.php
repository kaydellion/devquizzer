<?php
include "backend/connect.php";
$data = json_decode(file_get_contents("php://input"));

if (!$data || !isset($data->email)) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid data']);
    exit;
}

// Extract user details
$google_id = $data->sub;
$name = $data->name;
$email = $data->email;
$profile_pic = $data->picture;


// Check if user already exists
$stmt = $con->prepare("SELECT * FROM ".$siteprefix."users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // User already exists
    echo json_encode(['status' => 'exists', 'message' => 'Email already registered']);
    exit;
} else {
    // New user - Insert into database
    $date = date('Y-m-d H:i:s');
    $status = 'active';
    $options = ''; // Set default preferences if needed
    
    $stmt = $con->prepare("INSERT INTO ".$siteprefix."users (google_id, name, email, profile_pic, type, reward_points, created_date, last_login, email_verify, status, preference) 
                           VALUES (?, ?, ?, ?, 'user', 0, ?, ?, 1, ?, ?)");
    $stmt->bind_param("ssssssss", $google_id, $name, $email, $profile_pic, $date, $date, $status, $options);
    $stmt->execute();
}

// Fetch user data again after insertion
$stmt = $con->prepare("SELECT * FROM ".$siteprefix."users WHERE google_id = ?");
$stmt->bind_param("s", $google_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$id = $row["id"];

// Store user details in session
session_start(); 
$_SESSION['id']=$id;
setcookie("userID", $id, time() + (10 * 365 * 24 * 60 * 60));

echo json_encode(['status' => 'success']);
?>
