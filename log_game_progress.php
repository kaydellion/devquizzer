<?php include 'backend/connect.php'; // Include database connection file

// Get POST data from JavaScript
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$level = isset($_POST['level']) ? intval($_POST['level']) : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

if ($user_id !="" && $level > 0 && $status == 'completed') {
    try {
        // First get points for this level
        $points_stmt = $con->prepare("SELECT points FROM dv_game_levels WHERE id = ?");
        $points_stmt->bind_param("i", $level);
        $points_stmt->execute();
        $points_result = $points_stmt->get_result();
        $points = ($points_result->fetch_assoc())['points'] ?? 0;
        $points_stmt->close();
        $nextlevel = $level + 1;

        // Save game progress
        $stmt = $con->prepare("INSERT INTO dv_game_progress (user_id, level, status, points, timestamp) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("iisi", $user_id, $level, $status, $points);
        
        if (!$stmt->execute()) {
            throw new Exception("Error saving progress: " . $con->error);
        }
        $stmt->close();

        // Add to rewards history
        $query = "INSERT INTO {$siteprefix}rewards_history (s, user_id, points, type, date) VALUES (NULL, ?, ?, 'game', NOW())";
        $stmt = $con->prepare($query);
        $stmt->bind_param('ii', $user_id, $points);
        if (!$stmt->execute()) {
            throw new Exception('Query execution failed: ' . $stmt->error);
        }

        // Update user's total points
        $query = "UPDATE {$siteprefix}users SET reward_points = reward_points + ? WHERE s = ?";
        $stmt = $con->prepare($query);
        $stmt->bind_param('ii', $points, $user_id);
        if (!$stmt->execute()) {
            throw new Exception('Query execution failed: ' . $stmt->error);
        }

        echo "Code executed successfully,moving on to level $nextlevel";
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid data";
}


?>