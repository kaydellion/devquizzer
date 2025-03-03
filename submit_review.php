<?php include"backend/connect.php";
    

            // Return JSON response
            header('Content-Type: application/json');

            // Check if user is logged in
            if (!isset($_SESSION['user_id'])) {
                echo json_encode(['success' => false, 'message' => 'User not logged in']);
                exit;
            }

            $user_id = $_SESSION['user_id'];
            $course_id = $_POST['course_id'] ?? null;
            $rating = $_POST['rating'] ?? null;
            $review = $_POST['review'] ?? '';
            $action = $_POST['action'] ?? 'create';

            // Validate inputs
            if (!$course_id || !$rating) {
                echo json_encode(['success' => false, 'message' => 'Missing required fields']);
                exit;
            }

            // Sanitize inputs
            $course_id = mysqli_real_escape_string($con, $course_id);
            $rating = intval($rating);
            $review = mysqli_real_escape_string($con, $review);

            if ($action === 'create') {
                $query = "INSERT INTO {$siteprefix}reviews (course_id, user, rating, review, date) 
                          VALUES (?, ?, ?, ?, NOW())";
            } else {
                $query = "UPDATE {$siteprefix}reviews 
                          SET rating = ?, review = ?, date = NOW() 
                          WHERE course_id = ? AND user = ?";
            }

            $stmt = mysqli_prepare($con, $query);
            if ($stmt === false) {
                echo json_encode(['success' => false, 'message' => 'Prepare failed: ' . mysqli_error($con)]);
                exit;
            }

            if ($action === 'create') {
                mysqli_stmt_bind_param($stmt, "iiis", $course_id, $user_id, $rating, $review);
            } else {
                mysqli_stmt_bind_param($stmt, "isii", $rating, $review, $course_id, $user_id);
            }

            if (mysqli_stmt_execute($stmt)) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Database error: ' . mysqli_error($con)]);
            }

            mysqli_stmt_close($stmt);


            ?>