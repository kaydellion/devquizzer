<?php
// get_game_hint.php
header('Content-Type: application/json');

// Include database connection
require_once 'backend/connect.php';

// Check if level parameter exists
if (!isset($_POST['level'])) {
  echo json_encode(['error' => 'Level parameter is required']);
  exit;
}

$level = intval($_POST['level']);

// Prepare SQL statement to prevent SQL injection
$sql = "SELECT hint FROM dv_game_levels WHERE id = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $level);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  $row = $result->fetch_assoc();
  echo json_encode(['hint' => $row['hint']]);
} else {
  echo json_encode(['hint' => 'No hint available for this level.']);
}

$stmt->close();
?>