<?php

include '../../backend/connect.php';


        // Handle delete question
        if (isset($_POST['delete_question'])) {
            $question_id = mysqli_real_escape_string($con, $_POST['question_id']);
            
            // First delete associated options
            mysqli_query($con, "DELETE FROM {$siteprefix}quiz_options WHERE question_id = '$question_id'");
            
            // Then delete the question
            mysqli_query($con, "DELETE FROM {$siteprefix}quiz_questions WHERE s = '$question_id'");
            
            header("Location: edit-quiz.php?id=" . $quiz_id);
            exit();
        }

        // Handle update question
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['delete_question'])) {
            $question_id = mysqli_real_escape_string($con, $_POST['question_id']);
            $question_text = mysqli_real_escape_string($con, $_POST['question_text']);
            
            // Update question
            $updateQuestion = "UPDATE {$siteprefix}quiz_questions 
                      SET question = '$question_text' 
                      WHERE s = '$question_id'";
            mysqli_query($con, $updateQuestion);

            // Update options
            if (isset($_POST['options'])) {
            foreach ($_POST['options'] as $option_id => $option_text) {
                $option_id = mysqli_real_escape_string($con, $option_id);
                $option_text = mysqli_real_escape_string($con, $option_text);
                $is_correct = 0;
                
                if (isset($_POST['correct_option'][$question_id]) && 
                $_POST['correct_option'][$question_id] == $option_id) {
                $is_correct = 1;
                }

                $updateOption = "UPDATE {$siteprefix}quiz_options 
                       SET option_text = '$option_text', 
                           is_correct = $is_correct 
                       WHERE s = '$option_id'";
                mysqli_query($con, $updateOption);
            }
            }

            header("Location: edit-quiz.php?id=" . $_GET['quiz']);
            exit();
        }

?>