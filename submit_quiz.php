<?php 
// submit_quiz.php
header('Content-Type: application/json');
try {
    require_once 'backend/connect.php';

// Get user ID from session
$user_id = $_COOKIE['userID'] ?? null;
if (!$user_id) {
    echo json_encode(['error' => 'User not authenticated']);
    exit;
}

// Get submission data
$submission_id = $_POST['submission_id'] ?? null;
if (!$submission_id) {
    echo json_encode(['error' => 'No submission ID provided']);
    exit;
}

// Initialize score
$total_score = 0;

// Get all answers from the form
foreach ($_POST as $question_key => $selected_option) {
    // Skip non-question fields
    if ($question_key === 'submission_id') continue;
    
    // Extract question ID from the input name (q{id})
    $question_id = substr($question_key, 1);
    
    // Get correct answer and points for this question
    $query = "SELECT o.is_correct 
              FROM {$siteprefix}quiz_options o
              INNER JOIN {$siteprefix}quiz_questions q ON o.question_id = q.s
              WHERE q.s = ? AND o.s = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('ii', $question_id, $selected_option);
     if (!$stmt->execute()) {
        throw new Exception('Query execution failed: ' . $stmt->error);
    }
    $result = $stmt->get_result();
    
    if ($row = $result->fetch_assoc()) {
        if ($row['is_correct'] == 1) {
            $total_score += 1;
        }
    }
    
    // Record the answer
    $query = "INSERT INTO {$siteprefix}quiz_answers 
              (submission_id, question_id, selected_option) 
              VALUES (?, ?, ?)";
    $stmt = $con->prepare($query);
    $stmt->bind_param('iii', $submission_id, $question_id, $selected_option);
     if (!$stmt->execute()) {
        throw new Exception('Query execution failed: ' . $stmt->error);
    }
}


// Get total possible points for the quiz
$query = "SELECT COUNT(*) as total_possible 
          FROM {$siteprefix}quiz_questions 
          WHERE quiz_id = (SELECT quiz_id FROM {$siteprefix}submissions WHERE s = ?)";
$stmt = $con->prepare($query);
$stmt->bind_param('i', $submission_id);
 if (!$stmt->execute()) {
        throw new Exception('Query execution failed: ' . $stmt->error);
    }
$result = $stmt->get_result();
$total_possible = $result->fetch_assoc()['total_possible'];

// Calculate percentage
$score_percentage = ($total_score / $total_possible) * 100;


// Update submission with score and percentage
$query = "UPDATE {$siteprefix}submissions 
          SET score = ?, percentage = ?, submission_date = NOW() 
          WHERE s = ?";
$stmt = $con->prepare($query);
$stmt->bind_param('ddi', $total_score, $score_percentage, $submission_id);
if (!$stmt->execute()) {
    throw new Exception('Query execution failed: ' . $stmt->error);
}


// If score is 80% or higher
if ($score_percentage >= 70) {
    // Get quiz and course information
    $query = "SELECT quiz_id, course_id FROM {$siteprefix}submissions WHERE s = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $submission_id);
     if (!$stmt->execute()) {
        throw new Exception('Query execution failed: ' . $stmt->error);
    }
    $result = $stmt->get_result();
    $quiz_info = $result->fetch_assoc();
    
    // Award points and log reward

  
    // Get points value from quiz details
    $query = "SELECT points FROM {$siteprefix}quiz WHERE s = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('i', $quiz_info['quiz_id']);
     if (!$stmt->execute()) {
        throw new Exception('Query execution failed: ' . $stmt->error);
    }
    $result = $stmt->get_result();
    $quiz = $result->fetch_assoc();
    $points_awarded = $quiz['points'] ?? 100; // Default to 100 if not set

    // Update submission with points only
    $query = "UPDATE {$siteprefix}submissions 
              SET points = ?
              WHERE s = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('di', $points_awarded, $submission_id);
    if (!$stmt->execute()) {
        throw new Exception('Query execution failed: ' . $stmt->error);
    }

    $query = "INSERT INTO {$siteprefix}rewards_history 
              (s, user_id, points, type, date) 
              VALUES (NULL, ?, ?, 'quiz_completion', NOW())";
    $stmt = $con->prepare($query);
    $stmt->bind_param('ii', $user_id, $points_awarded);
     if (!$stmt->execute()) {
        throw new Exception('Query execution failed: ' . $stmt->error);
    }
    
    // Update user's total points
    $query = "UPDATE {$siteprefix}users 
              SET reward_points = reward_points + ? 
              WHERE s = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('ii', $points_awarded, $user_id);
     if (!$stmt->execute()) {
        throw new Exception('Query execution failed: ' . $stmt->error);
    }
    
    // Update certificate status in enrolled course
    $query = "UPDATE {$siteprefix}enrolled_courses 
              SET certificate = 1, 
                  end_date = NOW() 
              WHERE user_id = ? AND course_id = ?";
    $stmt = $con->prepare($query);
    $stmt->bind_param('ii', $user_id, $quiz_info['course_id']);
     if (!$stmt->execute()) {
        throw new Exception('Query execution failed: ' . $stmt->error);
    }
    
    echo json_encode([
        'success' => true, 
        'score' => $total_score,
        'percentage' => $score_percentage,
        'points_awarded' => $points_awarded,
        'certificate_earned' => true
    ]);
} else {
    echo json_encode([
        'success' => true, 
        'score' => $total_score,
        'percentage' => $score_percentage,
        'message' => 'Score below 70%. No points or certificate awarded.'
    ]); }} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => $e->getMessage()]);
} 
?>



