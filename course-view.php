<?php include "header.php";

checkActiveLog($active_log);
$course_id = $_GET['course'] ?? null;
$section_view = $_GET['section'] ?? null;
if (!$course_id) {
    header("Location: mycourses.php");
    exit();
}

//handle certificate
$certificate=0;
$newquery = "SELECT ec.*, c.title, c.description FROM {$siteprefix}enrolled_courses ec 
             LEFT JOIN {$siteprefix}courses c ON ec.course_id = c.s 
             WHERE ec.course_id = $course_id AND ec.user_id = $user_id";
$newresult = mysqli_query($con, $newquery);
if (mysqli_num_rows($newresult) > 0) {
    while ($newrow = mysqli_fetch_assoc($newresult)) {
        $certificate = $newrow['certificate'];
        $coursename = $last_entry['title'] ?? '';
        $course_text = $last_entry['description'] ?? '';
    }
}


if(!$section_view){
// Query to get the last entry from course progress
$query = "SELECT cp.*, c.title,c.description FROM {$siteprefix}course_progress cp LEFT JOIN {$siteprefix}courses c ON cp.course_id = c.s WHERE cp.course_id = $course_id AND user_id =  $user_id ORDER BY cp.s DESC LIMIT 1";
$result = mysqli_query($con, $query);
if ($result === false) {
    echo "Error: " . mysqli_error($con);
    exit();
}
$last_entry = mysqli_fetch_assoc($result);
$coursename = $last_entry['title'] ?? '';
$course_text = $last_entry['description'] ?? '';
// A user just enrolled
if ($user_id && !$last_entry) {
    enrollUser($con, $user_id, $course_id);
    $query = "SELECT s FROM {$siteprefix}theory WHERE course_id = $course_id ORDER BY chapter ASC LIMIT 1";
    $result = mysqli_query($con, $query);
    $first_section = mysqli_fetch_assoc($result);
    if (!$coursename) {
        $query = "SELECT title FROM {$siteprefix}courses WHERE s = $course_id";
        $result = mysqli_query($con, $query);
        $course = mysqli_fetch_assoc($result);
        $coursename = $course['title'] ?? 'Unknown Course';
    }
    if ($first_section) {
        $first_section_id = $first_section['s'];
        addCourseProgress($con, $user_id, $course_id, $first_section_id, $coursename);
        $last_entry['section'] = $first_section_id;
    }
}

// Collect the next section id
if ($last_entry) {
    $current_section_id = $last_entry['section'];
    $query = "SELECT s FROM {$siteprefix}theory WHERE course_id = $course_id AND s > $current_section_id ORDER BY chapter ASC LIMIT 1";
    $result = mysqli_query($con, $query);
    $next_section = mysqli_fetch_assoc($result);
    $next_section_id = $next_section['s'] ?? null;
}
// Check if there is a quiz for the course
$query = "SELECT * FROM {$siteprefix}quiz WHERE course_id = $course_id LIMIT 1";
$result = mysqli_query($con, $query);
$quiz = mysqli_fetch_assoc($result);
$quiz_id = $quiz['s'] ?? null;

// Determine the button text and link
if ($next_section_id) {
    $button_text = "Go To Next Section";
    $button_link = "next-section.php?course=$course_id&section=$next_section_id";
} elseif ($quiz_id && $certificate==0) {
    $button_text = "Take Quiz";
    $button_link = "#";
    // Add data attribute to trigger modal
    $button_link = "javascript:void(0)" . '" data-bs-toggle="modal" data-bs-target="#quizConsentModal';
} else {
    $button_text = "Course Completed"; 
    $button_link = "#";
}

// Show current progress
if ($last_entry) {
 $section = $last_entry['section'];
 }} else {$section=$section_view;}
     
    $query = "SELECT * FROM {$siteprefix}theory WHERE s = $section";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
            $chapter = $row['chapter'];
            $section_title = $row['title'];
            $section_description = limitDescription($row['subtitle']);
            $content_type = $row['content_type']; 
            $content_text= $row['content'];
            $content_media = $row['media_content'];
            if ($content_type == 'media') {
                $content = '<div class="video-container" style="width: 100%;">
                                <video controls style="width: 100%;">
                                    <source src="uploads/' . $content_media . '" type="video/mp4">
                                    Your browser does not support the video tag.
                                </video>
                            </div>';
            } else {
                $content = '<div class="text-content">' . $content_text . '</div>';
            }
            $duration = $row['duration'];
            $date_updated = formatDateTime2($row['updated_on']);  
             } 
            } else { header("Location: $previousPage");}

// Collect the next section id
    $current_section_id = $section;
    $query = "SELECT s FROM {$siteprefix}theory WHERE course_id = $course_id AND s > $current_section_id ORDER BY chapter ASC LIMIT 1";
    $result = mysqli_query($con, $query);
    $next_section = mysqli_fetch_assoc($result);
    $next_section_id = $next_section['s'] ?? null;

// Check if there is a quiz for the course
$query = "SELECT * FROM {$siteprefix}quiz WHERE course_id = $course_id LIMIT 1";
$result = mysqli_query($con, $query);
$quiz = mysqli_fetch_assoc($result);
$quiz_id = $quiz['s'] ?? null;

// Determine the button text and link
if ($next_section_id) {
    $button_text = "Go To Next Section";
    $button_link = "next-section.php?course=$course_id&section=$next_section_id";
} elseif ($quiz_id && $certificate==0){
    $button_text = "Take Quiz";
    $button_link = "#";
    // Add data attribute to trigger modal
    $button_link = "javascript:void(0)" . '" data-bs-toggle="modal" data-bs-target="#quizConsentModal';
} else {
    $button_text = "Course Completed"; 
    $button_link = "#";
}



if($certificate==1){
$show_certificate='<p class="mt-3">You’ve successfully completed the course and earned your certificate. Showcase your achievement</p>
<div class="certificate-container mt-3">
<div class="certificate-preview" style="background-image: url('.'assets/img/certificate.png'.');"></div>
</div>
<form action="certificate.php" method="POST">
    <input type="hidden" name="name" value="'.$name.'">
    <input type="hidden" name="course" value="'.$coursename.'">
    <input type="hidden" name="content" value="'.$course_text.'">
   <p class="mt-3"><button type="submit" class="btn btn-get-started">Download Certificate</button><p>
</form>';}
else{
$show_certificate='<p class="mt-3">Your course is currently still in progress. We are busy preparing your certificae. See you at the top!</p>';
}

        ?>


<main class="main">
<section>



<div class="row p-3 justify-content-center">
<div class="col-lg-6 col-12">

<div class="row">
<div class="col-lg-12 p-3 bg-dark text-light">Theory</div>
<!---
<div class="col-lg-6 p-3 text-muted"><a href="code-view.php?course=<?php //echo $course_id; ?>" class="text-muted">
<i class="bi bi-wordpress"></i> Code</a></div>-->
</div>

        <ul class="nav nav-tabs mt-3" id="myTab" role="tablist">
          <li class="nav-item" role="presentation">
            <button class="nav-link active" id="home-tab" data-bs-toggle="tab" data-bs-target="#home" type="button" role="tab" aria-controls="home" aria-selected="true">Learn</button>
          </li>
          <li class="nav-item" role="presentation">
            <button class="nav-link" id="profile-tab" data-bs-toggle="tab" data-bs-target="#profile" type="button" role="tab" aria-controls="profile" aria-selected="false">Certificate</button>
          </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="contact-tab" data-bs-toggle="tab" data-bs-target="#contact" type="button" role="tab" aria-controls="contact" aria-selected="false">Review</button>
        </ul>
        <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade show active p-3" id="home" role="tabpanel" aria-labelledby="home-tab">
          <h4 class="mt-3"><?php echo $section_title; ?>
          <br><span class="text-muted text-small">Estimated Duration: <?php echo $duration; ?> mins</span></h4>
          <p><?php echo $section_description; ?></p>
          <hr>
          <div><?php echo $content; ?></div>
          <p> <a href="<?php echo $button_link; ?>" class="btn btn-primary w-100 mt-3"><?php echo $button_text; ?></a></p></div>

<div></div>

          <div class="tab-pane fade" id="profile" role="tabpanel" aria-labelledby="profile-tab">
          <!-- show certificated -->
          <?php echo $show_certificate; ?>
          </div>

          <div class="tab-pane fade" id="contact" role="tabpanel" aria-labelledby="contact-tab">
          <!-- show reviews-->
     <?php if($certificate == 1){
            // Check for existing review
            $review_query = "SELECT * FROM {$siteprefix}reviews WHERE course_id = $course_id AND user = $user_id";
            $review_result = mysqli_query($con, $review_query);
            $existing_review = mysqli_fetch_assoc($review_result);
          ?>
            <div class="review-section mt-4">
              <h6 class="mb-3">Leave a Review</h6>
              <form id="reviewForm" method="POST">
                <div class="rating mb-3">
                  <?php for($i = 5; $i >= 1; $i--): ?>
                    <input type="radio" name="rating" value="<?php echo $i; ?>" id="star<?php echo $i; ?>" 
                      <?php echo ($existing_review && $existing_review['rating'] == $i) ? 'checked' : ''; ?>>
                    <label for="star<?php echo $i; ?>">☆</label>
                  <?php endfor; ?>
                </div>
                <div class="form-group">
                  <textarea class="form-control" name="review" rows="3" placeholder="Write your review here..."><?php echo $existing_review ? $existing_review['review'] : ''; ?></textarea>
                </div>
                <button type="submit" class="btn btn-primary mt-3">
                  <?php echo $existing_review ? 'Update Review' : 'Submit Review'; ?>
                </button>
              </form>
            </div>

            <script>
            document.getElementById('reviewForm').onsubmit = function(e) {
              e.preventDefault();
              const formData = new FormData(this);
              formData.append('course_id', <?php echo $course_id; ?>);
              formData.append('action', '<?php echo $existing_review ? 'update' : 'create'; ?>');

              fetch('submit_review.php', {
                method: 'POST',
                body: formData
              })
              .then(response => response.json())
              .then(data => {
                if(data.success) {
                  alert('Review ' + '<?php echo $existing_review ? 'updated' : 'submitted'; ?>' + ' successfully!');
                  location.reload();
                } else {
                  alert('Error: ' + data.message);
                }
              });
            };
            </script>

            <style>
            .rating {
              display: flex;
              flex-direction: row-reverse;
              justify-content: flex-end;
            }
            .rating input {
              display: none;
            }
            .rating label {
              font-size: 24px;
              color: #ddd;
              cursor: pointer;
            }
            .rating input:checked ~ label,
            .rating label:hover,
            .rating label:hover ~ label {
              color: #1AD8FC;
            }
            </style>
          <?php  } else { echo "<p>You can only leave a review when you have completed this course</p>"; }?>
          </div>

</div>
</div>
<!-- end course details -->








<div class="col-lg-4 col-12">
<div class="card p-1 filter-container min-h">
<div class="card-body">
<h3 class="text-center text-bold text-dark mb-5 mt-1">Course content</h3>
<?php
$query = "SELECT * FROM {$siteprefix}theory WHERE course_id = $course_id ORDER BY chapter ASC";
$result = mysqli_query($con, $query);

if (mysqli_num_rows($result) > 0) {
    echo "<ul class='list-group list-group-flush'>";
    while ($row = mysqli_fetch_assoc($result)) {
        $section_id = $row['s'];
        $chapter = $row['chapter'];
        $section_title = $row['title'];
        $section_description = limitDescription($row['subtitle']);
        $duration = formatDuration($row['duration']);
        $content_type = $row['content_type'];
        if ($content_type == 'media') {
        $content_icon='<i class="bi bi-file-earmark-play-fill"></i>';
        } else {
         $content_icon='<i class="bi bi-file-text-fill"></i>';
        }
        

        // Check if the section exists in course progress
        $progressQuery = "SELECT * FROM dv_course_progress WHERE user_id = $user_id AND section = $section_id";
        $progressResult = mysqli_query($con, $progressQuery);
        $isCompleted = mysqli_num_rows($progressResult) > 0;

        // Check if it's the last completed section
        $isLastCompleted = false;
        if ($isCompleted) {
            $lastCompletedQuery = "SELECT MAX(section) as last_section,end_date FROM dv_course_progress WHERE user_id = $user_id AND course_id = $course_id";
            $lastCompletedResult = mysqli_query($con, $lastCompletedQuery);
            $lastCompletedRow = mysqli_fetch_assoc($lastCompletedResult);
            $isLastCompleted = $section_id == $lastCompletedRow['last_section'];
        }

// Query to get the end_date for the current section
$endDateQuery = "SELECT end_date FROM dv_course_progress WHERE user_id = ? AND course_id = ? AND section = ?";
$stmt = $con->prepare($endDateQuery);
$stmt->bind_param("iii", $user_id, $course_id, $section_id);
$stmt->execute();
$endDateResult = $stmt->get_result();
$endDateRow = $endDateResult->fetch_assoc();

// Check if the row exists and end_date is not null
if($endDateRow && $endDateRow['end_date'] != null) { $isLastCompleted = false; }



          // Determine the icon or checkbox
          if ($certificate==1) {
            $icon = '<input type="checkbox" class="custom-checkbox" checked disabled>';
            $cardClass = 'bg-primary text-light rounded';
            $newtitle="<a class='text-light' href='course-view.php?course=$course_id&section=$section_id'>$section_title</a>";
        } 
        else if ($isCompleted && !$isLastCompleted) {
            $icon = '<input type="checkbox" class="custom-checkbox" checked disabled>';
            $cardClass = 'bg-primary text-light rounded';
            $newtitle="<a class='text-light' href='course-view.php?course=$course_id&section=$section_id'>$section_title</a>";
        } elseif ($isCompleted && $isLastCompleted) {
            $icon = '<input type="checkbox" class="custom-checkbox" disabled>';
            $cardClass = '';
            $newtitle="<a class='text-dark' href='course-view.php?course=$course_id&section=$section_id'>$section_title</a>";
        }else {
            $icon = '<i class="bi bi-lock"></i>';
            $cardClass = 'text-muted';
            $newtitle=$section_title;
        }

        // Display the card
    echo "<li class='list-group-item $cardClass'>
    <div class='d-flex bd-highlight'>
    <div class='me-auto p-2 bd-highlight'>$icon $newtitle<br> $content_icon $content_type </div>
    <div class='p-2 bd-highlight'><span class='text-small'>$duration</span></div>
    </div></li>";
    }
    echo '</ul>';
} else {
    echo "<p>No sections found for this course.</p>";
}
?>


</div>
</div>
</div> 
<!--row end -->
</div>




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
                    <li>Once started, you cannot close or reload the quiz </li>
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

document.getElementById('startQuiz').addEventListener('click', function() {
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


let isSubmitting = false;

function submitQuiz() {
    if (isSubmitting) return;
    isSubmitting = true;
    
    clearInterval(quizTimer);
    const formData = new FormData(document.getElementById('quizForm'));
    
    // Disable submit button and remove onbeforeunload
    document.getElementById('submitQuiz').disabled = true;
    window.onbeforeunload = null;

    fetch('submit_quiz.php', {
        method: 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.error) {
            showToast(data.error);
            console.error('Server error:', data.error);
            return;
        }
        
        if (data.success) {
            if (data.certificate_earned) {
                showToast(`Congratulations! You scored ${data.percentage}% and earned ${data.points_awarded} points and a certificate!`);
            } else {
                showToast(data.message || `You scored ${data.percentage}%. Try again to earn a certificate.`);
            }
            window.location.href = `quiz-results.php?submission=${submissionId}`;
        }
    })
    .catch(error => {
        showToast('An error occurred while submitting the quiz. Please try again.');
        console.error('Error:', error);
        isSubmitting = false;
        document.getElementById('submitQuiz').disabled = false;
    });
}

// Replace the onbeforeunload handler with a simpler alert
window.onbeforeunload = function(e) {
    if (submissionId) {
        e.preventDefault();
        alert("Please don't reload the page during the quiz. This will result in automatic submission.");
        return false;
    }
};


document.getElementById('submitQuiz').addEventListener('click', submitQuiz);

// Handle page reload/close
window.onbeforeunload = function() {
    if (submissionId) {
        submitQuiz();
        return "Are you sure you want to leave? Your quiz will be submitted automatically.";
    }
};

function loadQuizQuestions() {
    fetch('get_quiz_questions.php?quiz_id=<?php echo $quiz_id; ?>')
    .then(response => response.json())
    .then(data => {
        // Log the response data
        console.log('Quiz Questions Response:', data);

        // Add quiz instructions at the top
        const instructionsHtml = `
            <div class="quiz-instructions mb-4">
                <h4>Quiz Instructions</h4>
                <p>${<?php echo json_encode($quiz['description']); ?>}</p>
                <p>Total Points: ${<?php echo $quiz['points']; ?>}</p>
                <hr>
            </div>
        `;
        
        const questionsHtml = data.questions && data.questions.length ? data.questions.map((q, index) => 
            '<div class="question mb-4">' +
                '<p class="fw-bold">' + (index + 1) + '. ' + q.question + '</p>' +
                q.options.map(opt => 
                    '<div class="form-check">' +
                        '<input class="form-check-input" type="radio" name="q' + q.id + '" value="' + opt.id + '" required>' +
                        '<label class="form-check-label">' + opt.option_text + '</label>' +
                    '</div>'
                ).join('') +
            '</div>'
        ).join('') : '<p>No questions found for this quiz</p>';
        
        document.getElementById('quizQuestions').innerHTML = instructionsHtml + questionsHtml;
        
        // Show quiz modal
        new bootstrap.Modal(document.getElementById('quizModal')).show();
        
        // Start timer using quiz duration from database
        startTimer(<?php echo $quiz['timer']; ?> * 60);
    });
}

</script>
</section>
</main>
<?php  include "footer.php";  ?>