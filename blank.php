// Edit course
if (isset($_POST['editcourse'])) {
    $course_id = mysqli_real_escape_string($con, $_POST['course_id']);
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $category = $_POST['category'];
    $type = $_POST['type'];
    $level = $_POST['level'];
    $status = $_POST['status'];

    $picture = '';
    if (!empty($_FILES['featured']['name'])) {
        $uploadDir = '../../uploads/';
        $fileKey = 'featured';
        global $fileName;
        $picture = handleFileUpload($fileKey, $uploadDir, $fileName);
        $picture_query = ", `featured_image` = '$picture'";
    } else {
        $picture_query = "";
    }

    $query = "UPDATE `".$siteprefix."courses` SET 
        `title` = '$title',
        `description` = '$description',
        `language` = '$category',
        `type` = '$type',
        `level` = '$level',
        `status` = '$status',
        `updated_date` = NOW(),
        `updated_by` = '$name'
        $picture_query
        WHERE `s` = '$course_id'";

    $submit = mysqli_query($con, $query);
    if ($submit) {
        $statusAction = "Successful";
        $statusMessage = "Course updated successfully!";
        showSuccessModal($statusAction, $statusMessage);
    } else {
        $statusAction = "Oops!";
        $statusMessage = "An error has occurred!";
        showErrorModal($statusAction, $statusMessage);
    }
}

// Edit section
if (isset($_POST['editsection'])) {
    $section_id = mysqli_real_escape_string($con, $_POST['section_id']);
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $subtitle = mysqli_real_escape_string($con, $_POST['subtitle']);
    $content = mysqli_real_escape_string($con, $_POST['content']);
    $type = $_POST['type'];
    $duration = $_POST['duration'];

    $media = '';
    if (!empty($_FILES['media']['name'])) {
        $uploadDir = '../../uploads/';
        $fileKey = 'media';
        global $fileName;
        $media = handleFileUpload($fileKey, $uploadDir, $fileName);
        $media_query = ", `media_content` = '$media'";
    } else {
        $media_query = "";
    }

    if($type == "text") {
        $media_query = ", `media_content` = ''";
    }

    $query = "UPDATE `".$siteprefix."theory` SET 
        `title` = '$title',
        `subtitle` = '$subtitle',
        `duration` = '$duration',
        `content_type` = '$type',
        `content` = '$content',
        `updated_on` = NOW()
        $media_query
        WHERE `s` = '$section_id'";

    $submit = mysqli_query($con, $query);
    if ($submit) {
        $statusAction = "Successful";
        $statusMessage = "Section updated successfully!";
        showSuccessModal($statusAction, $statusMessage);
    } else {
        $statusAction = "Oops!";
        $statusMessage = "An error has occurred!";
        showErrorModal($statusAction, $statusMessage);
    }
}


function loadLevel(levelId) {
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "get_game_level.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.send(`level_id=${levelId}`);

  xhr.onload = function() {
    if (xhr.status === 200) {
      const level = JSON.parse(xhr.responseText);
      if (level) {
        document.getElementById('levelTitle').textContent = level.title;
        document.getElementById('levelDescription').textContent = level.description;
        incompleteCodeEditor.setValue(level.java_code);
        currentLevel = levelId;
      }
    }
  };
}

// Add a function to show hints for current level
function showHint() {
  const xhr = new XMLHttpRequest();
  xhr.open("POST", "get_game_hint.php", true);
  xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
  xhr.send(`level=${currentLevel}`);

  xhr.onload = function() {
    if (xhr.status === 200) {
      const response = JSON.parse(xhr.responseText);
      const hint = response.hint || "No hint available for this level.";
      displayModal("Hint: " + hint, "Got it!", null);
    }
  };
}