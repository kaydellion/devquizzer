<?php include 'header.php';

// Fetch quiz details if ID is provided
if (isset($_GET['id'])) {
    $quiz_id = mysqli_real_escape_string($con, $_GET['id']);
    $query = "SELECT * FROM {$siteprefix}quiz WHERE s = '$quiz_id'";
    $result = mysqli_query($con, $query);
    $quiz = mysqli_fetch_assoc($result);

    // Fetch questions and options
    $questionsQuery = "SELECT * FROM {$siteprefix}quiz_questions WHERE quiz_id = '$quiz_id'";
    $questionsResult = mysqli_query($con, $questionsQuery);
    $questions = [];
    while ($question = mysqli_fetch_assoc($questionsResult)) {
        $question_id = $question['s'];
        $optionsQuery = "SELECT * FROM dv_quiz_options WHERE question_id = '$question_id'";
        $optionsResult = mysqli_query($con, $optionsQuery);
        $options = [];
        while ($option = mysqli_fetch_assoc($optionsResult)) {
            $options[] = $option;
        }
        $question['options'] = $options;
        $questions[] = $question;
    }
}else{
    header("Location: quizzes.php");
    exit();
  }
?>

<div class="container-xxl flex-grow-1 container-p-y">
<div class="row">
<div class="col-xl">
<div class="card mb-4">
 <div class="card-header d-flex justify-content-between align-items-center">
        <h2>Edit Quiz</h2></div> <div class="card-body">
        <form method="POST" action="">
            <input type="hidden" name="quiz_id" value="<?php echo $quiz['s']; ?>">
            
            <div class="mb-3">
                <label>Title</label>
                <input type="text" name="title" class="form-control" value="<?php echo $quiz['title']; ?>" required>
            </div>

            <div class="mb-3">
                <label>Description</label>
                <textarea name="description" class="form-control" required><?php echo $quiz['description']; ?></textarea>
            </div>

            <div class="mb-3">
                <label>Course</label>
                <select name="course" class="form-control" required>
                    <?php
                    $courses = mysqli_query($con, "SELECT * FROM dv_courses");
                    while ($course = mysqli_fetch_array($courses)) {
                        $selected = ($course['s'] == $quiz['course_id']) ? 'selected' : '';
                        echo "<option value='" . $course['s'] . "' $selected>" . $course['title'] . "</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="mb-3">
                <label>Duration (minutes)</label>
                <input type="number" name="duration" class="form-control" value="<?php echo $quiz['timer']; ?>" required>
            </div>

            <div class="mb-3">
                <label>Points</label>
                <input type="number" name="points" class="form-control" value="<?php echo $quiz['points']; ?>" required>
            </div>

            <button type="submit" name="updatequiz" class="btn btn-primary">Update Quiz Details</button>
            <a href="quizzes.php" class="btn btn-secondary">Cancel</a>
        </form>

        <h3 class="mt-4">Questions</h3>
        <?php

        // Display questions and options with delete button
        $question_number = 1; // Initialize counter
        foreach ($questions as $question) {
            echo '<div class="card mb-3">';
            echo '<div class="card-body">';
            echo '<form method="POST" class="mb-3">';
            echo '<input type="hidden" name="quiz_id" value="' . $quiz_id . '">';
            echo '<input type="hidden" name="question_id" value="' . $question['s'] . '">';
            echo '<div class="mb-3">';
            echo '<label>Question ' . $question_number . ':</label>';
            echo '<input type="text" name="question_text" class="form-control" value="' . htmlspecialchars($question['question']) . '" required>';
            echo '</div>';
            
            foreach ($question['options'] as $option) {
            echo '<div class="mb-2 form-group"><div class="input-group">';
            echo '<input type="text" name="options[' . $option['s'] . ']" class="form-control" value="' . htmlspecialchars($option['option_text']) . '" required>';
            echo '<div class="input-group-append"><div class="input-group-text p-3"><input type="radio" name="correct_option[' . $question['s'] . ']" value="' . $option['s'] . '" ' . ($option['is_correct'] ? 'checked' : '') . '>';
            echo '</div></div></div></div>';
            }
            
            echo '<button type="submit" name="update_question" class="btn btn-primary">Update Question</button>';
            echo '</form>';
            
            echo '<form method="POST" class="d-inline">';
            echo '<input type="hidden" name="quiz_id" value="' . $quiz_id . '">';
            echo '<input type="hidden" name="question_id" value="' . $question['s'] . '">';
            echo '<button type="submit" name="delete_question" class="btn btn-danger" onclick="return confirm(\'Are you sure you want to delete this question?\')">Delete Question</button>';
            echo '</form>';
            
            echo '</div></div>';
            $question_number++; // Increment counter
        }
        ?>

        
        <a href="addquestions.php?quiz=<?php echo $quiz_id; ?>" class="btn btn-success">Add More Questions</a>
</div></div></div>  </div>
</div>
<?php include 'footer.php'; ?>