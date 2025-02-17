<?php
require_once 'backend/connect.php';

// Function to log errors
function logError($message) {
    error_log(date('[Y-m-d H:i:s] ') . $message . "\n", 3, 'quiz_errors.log');
    return ['error' => $message];
}

try {
    // Get quiz ID from request
    $quiz_id = $_GET['quiz_id'] ?? 0;

    // Validate quiz ID
    if (!is_numeric($quiz_id)) {
        throw new Exception('Invalid quiz ID');
    }

    // Query to get questions and their options
    $query = "
        SELECT q.s as id, q.question,
               o.s as option_id, o.option_text
        FROM {$siteprefix}quiz_questions q
        LEFT JOIN {$siteprefix}quiz_options o ON q.s = o.question_id
        WHERE q.quiz_id = ?
        ORDER BY q.s, o.s";

    $stmt = $con->prepare($query);
    if (!$stmt) {
        throw new Exception('Query preparation failed: ' . $con->error);
    }

    $stmt->bind_param("i", $quiz_id);
    if (!$stmt->execute()) {
        throw new Exception('Query execution failed: ' . $stmt->error);
    }

    $result = $stmt->get_result();
    
    $questions = [];
    while ($row = $result->fetch_assoc()) {
        $question_id = $row['id'];
        if (!isset($questions[$question_id])) {
            $questions[$question_id] = [
                'id' => $question_id,
                'question' => $row['question'],
                'points' => $row['points'],
                'options' => []
            ];
        }
        if ($row['option_id']) {
            $questions[$question_id]['options'][] = [
                'id' => $row['option_id'],
                'option_text' => $row['option_text']
            ];
        }
    }

    // Convert to indexed array
    $questions = array_values($questions);
    $response = ['questions' => $questions];

} catch (Exception $e) {
    $response = logError($e->getMessage());
}

header('Content-Type: application/json');
echo json_encode($response);
?>