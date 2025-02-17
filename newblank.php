
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
