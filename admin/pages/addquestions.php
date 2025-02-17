<?php include "header.php";
if(isset($_GET['quiz'])){ $quiz=$_GET['quiz']; }else {header("quizzes.php");} 
?>



<div class="container-xxl flex-grow-1 container-p-y">

            <div class="row">
                <div class="col-xl">
                    <div class="card mb-4">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="mb-0">Add Questions</h5>
                        </div>
                        <div class="card-body">
                            <form id="quizForm" method="POST">
                                <input name="quiz" value="<?php echo $quiz; ?>" hidden />
                                <div id="questionsContainer">
                                    <div class="question-template mb-4">
                                        <div class="form-group">
                                            <label class="text-black"><b>Question <span class="question-number">1</span></b></label>
                                            <input type="text" class="form-control" name="questions[]" required>
                                        </div>
                                        <div class="form-group mt-3">
                                            <label class="mb-1">Option 1</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="options[]" required>
                                                <div class="input-group-append">
                                                    <div class="input-group-text p-3">
                                                        <input type="radio" name="correct[0]" value="0" title="If clicked, this will be the correct answer" checked>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-1">
                                            <label class="mb-1">Option 2</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="options[]" required>
                                                <div class="input-group-append">
                                                    <div class="input-group-text p-3">
                                                        <input type="radio" name="correct[0]" value="1" title="If clicked, this will be the correct answer">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-1">
                                            <label class="mb-1">Option 3</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="options[]" required>
                                                <div class="input-group-append">
                                                    <div class="input-group-text p-3">
                                                        <input type="radio" name="correct[0]" value="2" title="If clicked, this will be the correct answer">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="form-group mt-1">
                                            <label class="mb-1">Option 4</label>
                                            <div class="input-group">
                                                <input type="text" class="form-control" name="options[]" required>
                                                <div class="input-group-append">
                                                    <div class="input-group-text p-3">
                                                        <input type="radio" name="correct[0]" value="3" title="If clicked, this will be the correct answer">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <button type="submit"  value="question" name="addquestions" class="btn btn-primary">Submit</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="floating-btn" id="addQuestionBtn">+</div>
            <script>
                document.getElementById('addQuestionBtn').addEventListener('click', function() {
                    var questionTemplate = document.querySelector('.question-template').cloneNode(true);
                    var questionNumber = document.querySelectorAll('.question-template').length;
                    questionTemplate.querySelector('.question-number').textContent = questionNumber + 1;
                    
                    // Clear all input fields in the new template
                    questionTemplate.querySelectorAll('input[type="text"]').forEach(function(input) {
                        input.value = '';
                    });
                    
                    // Update radio button names for the new question
                    questionTemplate.querySelectorAll('input[type="radio"]').forEach(function(radio) {
                        radio.name = 'correct[' + questionNumber + ']';
                    });
                    
                    document.getElementById('questionsContainer').appendChild(questionTemplate);
                });
            </script>


</div>
<?php include "footer.php"; ?>