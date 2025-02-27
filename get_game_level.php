<?php
// Database connection
require_once 'backend/connect.php';

// Check if level_id is set
if (isset($_POST['level_id'])) {
  $level_id = intval($_POST['level_id']);
  
  // Prepare SQL query with site prefix from config
  $sql = "SELECT id, title, description, java_code, hint FROM dv_game_levels WHERE id = ?";
  
  // Prepare and execute statement
  if ($stmt = $con->prepare($sql)) {
    $stmt->bind_param("i", $level_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
      // Return level data as JSON
      echo json_encode([
        'id' => $row['id'],
        'title' => $row['title'],
        'description' => $row['description'],
        'java_code' => $row['java_code'],
        'hint' => $row['hint']
      ]);
    } else {
      echo json_encode(['error' => 'Level not found']);
    }
    
    $stmt->close();
  } else {
    echo json_encode(['error' => 'Database error']);
  }
} else {
  echo json_encode(['error' => 'No level ID provided']);
}

?>