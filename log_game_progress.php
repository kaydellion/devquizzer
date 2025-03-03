<?php include 'backend/connect.php'; // Include database connection file

// Get POST data from JavaScript
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$level = isset($_POST['level']) ? intval($_POST['level']) : 0;
$status = isset($_POST['status']) ? $_POST['status'] : '';

if ($user_id !="" && $level > 0 && $status == 'completed') {
    try {
        // First check if user has already completed level 40
        $check_stmt = $con->prepare("SELECT status FROM dv_game_progress WHERE user_id = ? AND level = 40 AND status = 'completed'");
        $check_stmt->bind_param("i", $user_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $already_completed = $check_result->num_rows > 0;
        $check_stmt->close();

        if ($already_completed) {
            echo "Code executed successfully. You have already completed all levels! Now go the course and leave a review,also you can download your certificate on your dashboard";
            return;
        }

        // Get points for this level
        $points_stmt = $con->prepare("SELECT points FROM dv_game_levels WHERE id = ?");
        $points_stmt->bind_param("i", $level);
        $points_stmt->execute();
        $points_result = $points_stmt->get_result();
        $points = ($points_result->fetch_assoc())['points'] ?? 0;
        $points_stmt->close();
        $nextlevel = $level + 1;

        // Rest of your existing code remains the same
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
       
        if ($level == 40) {
            // Update certificate status in enrolled course
            $query = "INSERT INTO dv_enrolled_courses (user_id, course_id, start_date, end_date, certificate) 
                      VALUES (?, 1, NOW(), NOW(), 1)";
            $stmt = $con->prepare($query);
            $stmt->bind_param('i', $user_id);
            if (!$stmt->execute()) {
                throw new Exception('Query execution failed: ' . $stmt->error);
            }
            $stmt->close();
            echo "Code executed successfully.Game course completed successfully!. Go to your enrolled courses on your dashboard to view your certificate.
            Game will restart from level 1";
        } else {
            echo "Code executed successfully. Correct solution! moving on to level $nextlevel. You have been rewarded with 25 points. Keep it up";
        }

    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid data";
}


?>