<?php include 'header.php'; 


// Get submission ID from URL
$submission_id = $_GET['submission'] ?? null;
if (!$submission_id) {
    die('No submission ID provided');
}

// Verify user has access to this submission
$query = "SELECT s.*, q.title as quiz_title 
          FROM {$siteprefix}submissions s
          JOIN {$siteprefix}quiz q ON s.quiz_id = q.s
          WHERE s.s = ? AND s.user_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param('ii', $submission_id, $_COOKIE['userID']);
$stmt->execute();
$submission = $stmt->get_result()->fetch_assoc();

if (!$submission) {
    die('Access denied or submission not found');
}

// Get all answers with question details
$query = "SELECT a.*, q.question, o.option_text, o.is_correct
          FROM {$siteprefix}quiz_answers a
          JOIN {$siteprefix}quiz_questions q ON a.question_id = q.s
          JOIN {$siteprefix}quiz_options o ON a.selected_option = o.s
          WHERE a.submission_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param('i', $submission_id);
$stmt->execute();
$answers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>
<main class="main">

<section>
    <h1><?php echo htmlspecialchars($submission['quiz_title']); ?> - Results</h1>
    
    <div class="score-info">
        <p>Final Score: <?php echo $submission['score']; ?></p>
        <p>Percentage: <?php echo number_format($submission['percentage'], 1); ?>%</p>
        
        <?php if ($submission['percentage'] >= 80): ?>
            <p class="certificate-earned">
                Congratulations! You've earned a certificate!
                Points Awarded: <?php echo $submission['points']; ?>
            </p>
        <?php else: ?>
            <p class="no-certificate">
                Score below 80%. No points or certificate awarded.
            </p>
        <?php endif; ?>
    </div>
    
    <h2>Your Answers:</h2>
    <?php foreach ($answers as $answer): ?>
        <div class="question <?php echo $answer['is_correct'] ? 'correct' : 'incorrect'; ?>">
            <p><strong><?php echo htmlspecialchars($answer['question']); ?></strong></p>
            <p>Your answer: <?php echo htmlspecialchars($answer['option_text']); ?></p>
        </div>
    <?php endforeach; ?>
    
    <a href="dashboard.php" class="btn btn-primary">Return to Dashboard</a>
    </section>
</main>
<?php include 'footer.php'; ?>








<!-- Button to open quiz consent modal -->
<button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quizConsentModal">
    Start Quiz
</button>

<!-- Quiz Consent Modal -->
<div class="modal fade" id="quizConsentModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quiz Consent</h5>
            </div>
            <div class="modal-body">
                <p>Important: By starting this quiz, you agree to the following conditions:</p>
                <ul>
                    <li>Once started, you cannot close or reload the quiz</li>
                    <li>If you reload the page, your quiz will auto-submit with 0 points</li>
                    <li>This attempt will be counted towards your total attempts</li>
                </ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-primary" id="startQuiz">I Agree, Start Quiz</button>
            </div>
        </div>
    </div>
</div>

<!-- Quiz Modal -->
<div class="modal fade" id="quizModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Quiz</h5>
                <div id="quizTimer" class="text-danger"></div>
            </div>
            <div class="modal-body">
                <form id="quizForm">
                    <input type="hidden" name="submission_id" id="submission_id">
                    <div id="quizQuestions"></div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="submitQuiz">Submit Quiz</button>
            </div>
        </div>
    </div>
</div>

<script>
let quizTimer;
let submissionId;

// Modify the existing quiz button click handler
if ('<?php echo $button_text; ?>' === 'Take Quiz') {
    document.querySelector('a[href*="quiz-view.php"]').addEventListener('click', function(e) {
        e.preventDefault();
        new bootstrap.Modal(document.getElementById('quizConsentModal')).show();
    });
}

document.getElementById('startQuiz').addEventListener('click', function() {
    // Hide consent modal
    bootstrap.Modal.getInstance(document.getElementById('quizConsentModal')).hide();
    
    // Start quiz
    startQuiz();
});

function startQuiz() {
    // First create submission record
    fetch('create_submission.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            course_id: <?php echo $course_id; ?>,
            quiz_id: <?php echo $quiz_id; ?>,
        })
    })
    .then(response => response.json())
    .then(data => {
        submissionId = data.submission_id;
        document.getElementById('submission_id').value = submissionId;
        loadQuizQuestions();
    });
}

function loadQuizQuestions() {
    fetch('get_quiz_questions.php?quiz_id=<?php echo $quiz_id; ?>')
    .then(response => response.json())
    .then(data => {
        const questionsHtml = data.questions.map((q, index) => `
            <div class="question mb-4">
                <p class="fw-bold">${index + 1}. ${q.question}</p>
                ${q.options.map(opt => `
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="q${q.id}" value="${opt.id}" required>
                        <label class="form-check-label">${opt.option_text}</label>
                    </div>
                `).join('')}
            </div>
        `).join('');
        
        document.getElementById('quizQuestions').innerHTML = questionsHtml;
        
        // Show quiz modal
        new bootstrap.Modal(document.getElementById('quizModal')).show();
        
        // Start timer
        startTimer(data.duration * 60); // Convert minutes to seconds
    });
}

function startTimer(duration) {
    let timer = duration;
    quizTimer = setInterval(() => {
        const minutes = parseInt(timer / 60, 10);
        const seconds = parseInt(timer % 60, 10);
        
        document.getElementById('quizTimer').textContent = 
            `${minutes}:${seconds < 10 ? '0' : ''}${seconds}`;
        
        if (--timer < 0) {
            submitQuiz();
        }
    }, 1000);
}

function submitQuiz() {
    clearInterval(quizTimer);
    const formData = new FormData(document.getElementById('quizForm'));
    
    fetch('submit_quiz.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        window.location.href = `quiz-results.php?submission=${submissionId}`;
    });
}

document.getElementById('submitQuiz').addEventListener('click', submitQuiz);

// Handle page reload/close
window.onbeforeunload = function() {
    if (submissionId) {
        submitQuiz();
        return "Are you sure you want to leave? Your quiz will be submitted automatically.";
    }
};
</script>


<script>
function loadQuizQuestions() {
    fetch('get_quiz_questions.php?quiz_id=<?php echo $quiz_id; ?>')
    .then(response => response.json())
    .then(data => {
        // Add quiz instructions at the top
        const instructionsHtml = `
            <div class="quiz-instructions mb-4">
                <h4>Quiz Instructions</h4>
                <p>${<?php echo json_encode($quiz_details['description']); ?>}</p>
                <p>Total Points: ${<?php echo $quiz_details['points']; ?>}</p>
                <hr>
            </div>
        `;
        
        const questionsHtml = data.questions.map((q, index) => `
            <div class="question mb-4">
                <p class="fw-bold">${index + 1}. ${q.question}</p>
                ${q.options.map(opt => `
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="q${q.id}" value="${opt.id}" required>
                        <label class="form-check-label">${opt.option_text}</label>
                    </div>
                `).join('')}
            </div>
        `).join('');
        
        document.getElementById('quizQuestions').innerHTML = instructionsHtml + questionsHtml;
        
        // Show quiz modal
        new bootstrap.Modal(document.getElementById('quizModal')).show();
        
        // Start timer using quiz duration from database
        startTimer(<?php echo $quiz_details['timer']; ?> * 60);
    });
}
</script>
<!DOCTYPE html>
<html>
<head>
    <title>Quiz Results</title>
    <style>
        .score-info { margin: 20px 0; padding: 15px; background: #f5f5f5; }
        .certificate-earned { color: green; }
        .no-certificate { color: #d9534f; }
    </style>
</head>
<body>
    <h1><?php echo htmlspecialchars($submission['quiz_title']); ?> - Results</h1>
    
    <div class="score-info">
        <p>Final Score: <?php echo $submission['score']; ?></p>
        <p>Percentage: <?php echo number_format($submission['percentage'], 1); ?>%</p>
        
        <?php if ($submission['percentage'] >= 80): ?>
            <p class="certificate-earned">
                Congratulations! You've earned a certificate!
                Points Awarded: <?php echo $submission['points_awarded']; ?>
            </p>
        <?php else: ?>
            <p class="no-certificate">
                Score below 80%. No points or certificate awarded.
            </p>
        <?php endif; ?>
    </div>
    
    <h2>Your Answers:</h2>
    <?php foreach ($answers as $answer): ?>
        <div class="question <?php echo $answer['is_correct'] ? 'correct' : 'incorrect'; ?>">
            <p><strong><?php echo htmlspecialchars($answer['question_text']); ?></strong></p>
            <p>Your answer: <?php echo htmlspecialchars($answer['option_text']); ?></p>
        </div>
    <?php endforeach; ?>
    
    <a href="dashboard.php">Return to Dashboard</a>
</body>
</html>



<?php
require_once 'backend/connect.php';
session_start();

// Get submission ID from URL
$submission_id = $_GET['submission'] ?? null;
if (!$submission_id) {
    die('No submission ID provided');
}

// Verify user has access to this submission
$query = "SELECT s.*, q.title as quiz_title 
          FROM {$siteprefix}quiz_submissions s
          JOIN {$siteprefix}quizzes q ON s.quiz_id = q.id
          WHERE s.id = ? AND s.user_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param('ii', $submission_id, $_SESSION['user_id']);
$stmt->execute();
$submission = $stmt->get_result()->fetch_assoc();

if (!$submission) {
    die('Access denied or submission not found');
}

// Get all answers with question details
$query = "SELECT a.*, q.question_text, o.option_text, o.is_correct
          FROM {$siteprefix}quiz_answers a
          JOIN {$siteprefix}quiz_questions q ON a.question_id = q.id
          JOIN {$siteprefix}quiz_questions_options o ON a.selected_option = o.id
          WHERE a.submission_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param('i', $submission_id);
$stmt->execute();
$answers = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quiz Results</title>
</head>
<body>
    <h1><?php echo htmlspecialchars($submission['quiz_title']); ?> - Results</h1>
    <p>Final Score: <?php echo $submission['score']; ?></p>
    
    <h2>Your Answers:</h2>
    <?php foreach ($answers as $answer): ?>
        <div class="question <?php echo $answer['is_correct'] ? 'correct' : 'incorrect'; ?>">
            <p><strong><?php echo htmlspecialchars($answer['question_text']); ?></strong></p>
            <p>Your answer: <?php echo htmlspecialchars($answer['option_text']); ?></p>
        </div>
    <?php endforeach; ?>
    
    <a href="dashboard.php">Return to Dashboard</a>
</body>
</html>