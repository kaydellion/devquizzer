<?php include 'header.php'; 


// Get submission ID from URL
$submission_id = $_GET['submission'] ?? null;
if (!$submission_id) {
    die('No submission ID provided');
}

// Verify user has access to this submission and get attempts count
$query = "SELECT s.*, q.title as quiz_title,
          (SELECT COUNT(*) 
           FROM {$siteprefix}submissions 
           WHERE user_id = ? AND quiz_id = s.quiz_id) as attempts
          FROM {$siteprefix}submissions s
          JOIN {$siteprefix}quiz q ON s.quiz_id = q.s
          WHERE s.s = ? AND s.user_id = ?";
$stmt = $con->prepare($query);
$stmt->bind_param('iii', $_COOKIE['userID'], $submission_id, $_COOKIE['userID']);
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
<div class="vh-100"></div>
</main>
<?php include 'footer.php'; ?>
<style>
.quiz-modal {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.7);
    z-index: 1000;
}

.modal-content {
    background-color: #fff;
    border-radius: 10px !important;
    margin: 15% auto;
    padding: 40px;
    width: 80%;
    max-width: 500px;
    text-align: center;
}

.success-image {
    max-width: 200px;
    margin: 20px auto;
}
</style>
<div id="quizModal" class="quiz-modal">
    <div class="modal-content">
        <?php if ($submission['percentage'] >= 80): ?>
            <h2><?php echo number_format($submission['percentage'], 1); ?>%</h2>
            <p>You beat the quiz in <?php echo $submission['attempts'] ?? 1; ?> attempts!</p>
            <img src="assets/img/success.png" alt="Success" class="success-image">
            <a href="certificates.php" class="btn btn-primary">Go to Certificates</a>
        <?php else: ?>
            <h2><?php echo number_format($submission['percentage'], 1); ?>%</h2>
            <p>Keep going! You're on the right track.<br>
            With a little more study, you'll master this material. Every attempt brings you closer to success!</p>
            <img src="assets/img/retry.gif" alt="Try Again" class="success-image">
            <p><?php echo getRandomMotivationalQuote(); ?></p>
            <a href="course-view.php?course=<?php echo $submission['course_id']; ?>" class="btn btn-primary">Try Again</a>
        <?php endif; ?>
    </div>
</div>




<script>
document.addEventListener('DOMContentLoaded', function() {
    document.getElementById('quizModal').style.display = 'block';
});
</script>