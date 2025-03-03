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
    // User exists - log them in
    $row = $result->fetch_assoc();
    $id = $row["id"];
    
    // Update last login time
    $date = date('Y-m-d H:i:s');
    $stmt = $con->prepare("UPDATE ".$siteprefix."users SET last_login = ? WHERE id = ?");
    $stmt->bind_param("si", $date, $id);
    $stmt->execute();

    // Start session and set cookie
    session_start();
    $_SESSION['id'] = $id;
    setcookie("userID", $id, time() + (10 * 365 * 24 * 60 * 60));
    
    echo json_encode(['status' => 'success']);
    exit;
} else {
    // New user - Insert into database
    $date = date('Y-m-d H:i:s');
    $status = 'active';
    $options = '';
    
    $stmt = $con->prepare("INSERT INTO ".$siteprefix."users (google_id, name, email, profile_picture, type, reward_points, created_date, last_login, email_verify, status, preference) 
                           VALUES (?, ?, ?, ?, 'user', 0, ?, ?, 1, ?, ?)");
    $stmt->bind_param("ssssssss", $google_id, $name, $email, $profile_pic, $date, $date, $status, $options);
    $stmt->execute();
    
    $id = $con->insert_id;
    
    // Start session and set cookie
    setcookie("userID", $id, time() + (10 * 365 * 24 * 60 * 60));
}

echo json_encode(['status' => 'success']);
?>
