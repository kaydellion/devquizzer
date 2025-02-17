<?php
// Database connection
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "game_db"; // Change to your database name

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get POST data from JavaScript
$level = isset($_POST['level']) ? intval($_POST['level']) : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';
$clearedRows = isset($_POST['clearedRows']) ? intval($_POST['clearedRows']) : 0;

if ($level > 0 && ($status == 'win' || $status == 'loss')) {
    // Save to database
    $stmt = $conn->prepare("INSERT INTO dv_game_results (level, status, cleared_rows, timestamp) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("isi", $level, $status, $clearedRows);
    
    if ($stmt->execute()) {
        echo "Game result saved!";
    } else {
        echo "Error saving result: " . $conn->error;
    }
    $stmt->close();
} else {
    echo "Invalid data";
}

$conn->close();
?>
