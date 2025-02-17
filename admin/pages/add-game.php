<?php include "header.php"; ?>

<div class="container-xxl flex-grow-1 container-p-y">

<!-- Basic Layout -->
                <div class="row">
                <div class="col-xl">
                  <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="mb-0">Create New Game Task</h5>
                    </div>
                    <div class="card-body">
                      <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Title</label>
                          <input type="text" class="form-control" name="title" id="basic-default-fullname" placeholder="Learning loops v2" required/>
                        </div>
                        <div class="mb-3">
                        <select class="form-select" name="course" id="courseSelect"  onchange="updateCodeMirrorMode()"aria-label="Default select example" required>
    <option selected>- Select Course Section -</option>
    <?php
    $sql = "SELECT c.*, l.title AS language_name, l.s AS language_id 
            FROM " . $siteprefix . "courses c
            LEFT JOIN " . $siteprefix . "languages l ON c.language = l.s";
    $sql2 = mysqli_query($con, $sql);
    while ($row = mysqli_fetch_array($sql2)) {
        echo '<option value="' . $row['s'] . '" data-language-id="' . $row['language_id'] . '" data-language-name="' . $row['language_name'] . '">' . $row['title'] . '</option>';
    }
    ?>
</select>
</div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-message">Description</label>
                          <textarea id="basic-default-message" name="description" class="form-control" placeholder="Please make sure you have read all of module 2" required/></textarea>
                         </div>
                        <div class="mb-3">
                        <label for="formFile" class="form-label">Reward Points Assigned (* note that points are awarded if user passes 80% of the quiz)</label>
                        <input class="form-control" type="number" name="points" required/>
                       </div>
                       <div class="mb-3">
                        <label for="incomplete-code">Incomplete Code:</label><br>
                        <span class="text-danger">Please provide the incomplete code for the game task and for the expected gaps, just input (******) </span><br>
                        <textarea id="incomplete-code" name="incomplete_code"></textarea><br><br>

                        <label for="expected-output">Expected Output:</label><br>
                        <textarea id="expected-output" name="expected_output"></textarea><br><br>
                        </div>
                        <button type="submit" name="addgame" value="course" class="btn btn-primary w-100">Create Game</button>
                      </form>
                    </div>
                  </div>
                </div>

              </div>
            </div>


            <script>
        // Initialize CodeMirror for Incomplete Code
        const incompleteCodeEditor = CodeMirror.fromTextArea(document.getElementById('incomplete-code'), {
            lineNumbers: true,
            mode: 'javascript', // Default mode
            theme: 'dracula',
        });


        // Initialize CodeMirror for Expected Output
        const expectedOutputEditor = CodeMirror.fromTextArea(document.getElementById('expected-output'), {
            lineNumbers: false,
            mode: 'text', // Plain text mode for expected output
            theme: 'default',
        });

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
            const selectElement = document.getElementById('courseSelect');
            const selectedOption = selectElement.options[selectElement.selectedIndex];
            const language = selectedOption.getAttribute('data-language-name'); // Get the language name
            const mode = languageModes[language] || 'text'; // Default to plain text if mode not found
            incompleteCodeEditor.setOption('mode', mode);
        }
    

</script> <?php include "footer.php"; ?>
