<?php include "header.php";

checkActiveLog($active_log);
$course_id = $_GET['course'] ?? null;
$section_view = $_GET['section'] ?? null;
if (!$course_id) {
    header("Location: mycourses.php");
    exit();
}


if(isset($_GET['task'])){ 
    $task = $_GET['task'];
    $query = "SELECT * FROM ".$siteprefix."game_tasks WHERE s = '$task' AND course_id = '$course_id'";
} else {
    // Check last progress entry
    $progress_query = "SELECT level FROM ".$siteprefix."game_progress 
                      WHERE user_id = '$user_id' AND course_id = '$course_id' 
                      ORDER BY start_date DESC LIMIT 1";
    $progress_result = mysqli_query($con, $progress_query);
    
    if(mysqli_num_rows($progress_result) > 0) {
        $progress = mysqli_fetch_assoc($progress_result);
        $query = "SELECT * FROM ".$siteprefix."game_tasks 
                 WHERE level = '{$progress['level']}' AND course_id = '$course_id'";
    } else {
        // Get first level task
        $query = "SELECT * FROM ".$siteprefix."game_tasks 
                 WHERE level = 1 AND course_id = '$course_id' LIMIT 1";
        
        // Insert initial progress record
        $insert_query = "INSERT INTO ".$siteprefix."game_progress 
                        (user_id, level, course_id, start_date) 
                        VALUES ('$user_id', 1, '$course_id', NOW())";
        mysqli_query($con, $insert_query);
    }
}

$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $task_id = $row['s'];
    $title = $row['title'];
    $description = $row['description'];
    $points = $row['points'];
    $incomplete_code = $row['incomplete_code'];
    $expected_output = $row['expected_output'];
    $level = $row['level'];
    $language_id = $row['language_id'];     
}

//get languae name
$query = "SELECT * FROM ".$siteprefix."languages WHERE s = '$language_id' ";
$result = mysqli_query($con, $query);
while ($row = mysqli_fetch_assoc($result)) {
    $language_name = $row['title']; }
?>


<main class="main">
<section>



<div class="row p-3 justify-content-center">
<div class="col-12 p-3">

<div class="row">
<div class="col-lg-6 p-3 text-muted"><a href="course-view.php?course=<?php echo $course_id; ?>" class="text-dark">Theory</a></div>
<div class="col-lg-6 p-3 bg-dark text-light"><i class="bi bi-wordpress"></i> Code </div>
</div>


<!-- Code View Tasks-->
<div class="row">
<div class="d-flex flex-wrap gap-3 align-items-center rounded col-lg-12 bg-dark p-1 mt-3">
<?php 
 $query = "SELECT t.*, CASE WHEN p.level IS NOT NULL THEN 1 ELSE 0 END as completed 
          FROM ".$siteprefix."game_tasks t 
          LEFT JOIN ".$siteprefix."game_progress p 
          ON p.course_id = t.course_id 
          AND p.user_id = '$user_id' 
          AND p.level >= t.level 
          WHERE t.course_id = '$course_id' 
          ORDER BY t.level";
 $result = mysqli_query($con, $query);
 while ($row = mysqli_fetch_assoc($result)) {
    $isCompleted = $row['completed'] == 1;
    $isCurrent = ($row['s'] == ($task_id ?? ''));
    $divClass = $isCurrent ? 'bg-primary rounded p-2' : '';
?>
    <div class="d-flex gap-3 <?php echo $divClass; ?>">
        <input class="form-check-input" type="checkbox" 
               style="width: 25px; height: 25px;"
               <?php echo $isCompleted ? 'checked' : 'disabled'; ?>>
        <?php if ($isCompleted): ?>
            <a href="?task=<?php echo $row['s']; ?>&course=<?php echo $course_id; ?>" 
               class="text-decoration-none">
        <?php else: ?>
            <span>
        <?php endif; ?>
            <h5 class="mb-0 text-light">
                <?php echo $row['title']; ?>
            </h5>
            <small class="text-light">
                Level <?php echo $row['level']; ?>
            </small>
        <?php echo $isCompleted ? '</a>' : '</span>'; ?>
    </div>
<?php } ?>
</div>
</div>

<!-- Code View Board -->
<div class="row mt-4 p-5" style="background-image: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url('assets/img/code.jpeg'); background-size: cover; background-position: center; border-radius: 10px;">
    <div class="col-lg-12 position-relative">
        <!-- Top Bar -->
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="h4 mb-0 text-light"><h5 class="text-light">Level <?php echo $level ?></h5>
            <p class="text-small"><?php echo $description;?> </p></div>
            <div class="d-flex gap-3 align-items-center">
                <div class="border rounded p-2 bg-transparent text-light" data-bs-toggle="tooltip" data-bs-placement="top" title="Points to be earned when the level is completed">
                    <i class="bi bi-gem text-warning"></i>
                    <span class="text-primary"><?php echo $points; ?></span>
                </div>
            </div>
        </div>
        
        <!-- Code Editor -->
        <div class="bg-dark rounded">
            <textarea  id="incomplete-code" class="form-control" rows="10" style="font-family: monospace;"><?php echo $incomplete_code; ?> </textarea>
        </div>
        
        <!-- Compile Button -->
        <div class="text-center mt-3">
            <button class="btn btn-primary compile-code">
                <i class="bi bi-play-fill"></i> Compile & Run
            </button>
        </div>
    </div>
</div>

</div>



</div>
</div> 


</section>
</main>
<script>
        // Initialize CodeMirror for Incomplete Code
        const incompleteCodeEditor = CodeMirror.fromTextArea(document.getElementById('incomplete-code'), {
            lineNumbers: true,
            mode: 'javascript', // Default mode
            theme: 'dracula',
        });


      
        // Compile and Run functionality
        document.querySelector('.compile-code').addEventListener('click', function() {
                const code = incompleteCodeEditor.getValue() || ''; 

            
            // Basic code validation
            if (!code.trim()) {
                showResultModal('Error', 'Please enter some code', false);
                return;
            }

            console.log(code);

            // Send code to server for compilation
            fetch('compile.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    code: code,
                    language: '<?php echo $language_name; ?>',
                    expected: '<?php echo addslashes($expected_output); ?>'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.error) {
                    showResultModal('Error', data.error, false);
                    return;
                }
                
                console.log(data);
                // Compare outputs after trimming whitespace
                if (data.output.trim() === data.expected.trim()) {
                    showResultModal('Success!', 'Great job! Moving to next task...', true);
                    
                    // Update progress
                    fetch('update_progress.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            course_id: '<?php echo $course_id; ?>',
                            task_id: '<?php echo $task_id; ?>'
                        })
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.next_task) {
                            setTimeout(() => {
                                window.location.href = `?task=${data.next_task}&course=<?php echo $course_id; ?>`;
                            }, 2000);
                        } else {
                            showResultModal('Success!', 'You have completed all tasks!', true);
                        }
                    })
                    .catch(error => {
                        showResultModal('Error', 'Failed to update progress: ' + error.message, false);
                    });
                } else {
                    showResultModal('Try Again', `Output does not match expected result.\nExpected: ${data.expected.trim()}\nGot: ${data.output.trim()}`, false);
                }
            })
            .catch(error => {
                showResultModal('Error', `Compilation error: ${error.message}`, false);
            });
        });
       
        // Function to show result modal
        function showResultModal(title, message, success) {
            const modalHtml = `
                <div class="modal fade" id="resultModal" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header ${success ? 'bg-success' : 'bg-danger'} text-white">
                                <h5 class="modal-title">${title}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <p>${message}</p>
                            </div>
                            <div class="modal-footer">
                                ${success ? 
                                    '<button type="button" class="btn btn-success" data-bs-dismiss="modal">Continue</button>' :
                                    '<button type="button" class="btn btn-primary" onclick="location.reload()">Try Again</button>'
                                }
                            </div>
                        </div>
                    </div>
                </div>
            `;

            // Remove existing modal if any
            const existingModal = document.getElementById('resultModal');
            if (existingModal) {
                existingModal.remove();
            }

            // Add new modal to body
            document.body.insertAdjacentHTML('beforeend', modalHtml);
            
            // Show the modal
            const modal = new bootstrap.Modal(document.getElementById('resultModal'));
            modal.show();
        }


        // Map languages to CodeMirror modes
        const languageModes = {
            'PHP': 'php',
            'Wordpress': 'php',
            'HTML': 'htmlmixed',
            'CSS': 'css',
            'Bootstrap': 'css',
            'JavaScript': 'javascript',
            'Python': 'python',
            'Java': 'text/x-java',
            'C++': 'text/x-c++src',
            'React JS': 'javascript',
            'Laravel': 'php',
            'Swift': 'swift',
            'Kotlin': 'text/x-kotlin',
            'SQL': 'sql',
            'Ruby': 'ruby',
            'R': 'r',
            'C#': 'text/x-csharp'
        };

        function updateCodeMirrorMode() {
            const language = "<?php echo $language_name; ?>"; // Get the language name
            const mode = languageModes[language] || 'text'; // Default to plain text if mode not found
            incompleteCodeEditor.setOption('mode', mode);
        }

        updateCodeMirrorMode(); // Set the initial mode

</script>
<?php  include "footer.php";  ?>
