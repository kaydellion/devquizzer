// Edit course
if (isset($_POST['editcourse'])) {
    $course_id = mysqli_real_escape_string($con, $_POST['course_id']);
    $title = mysqli_real_escape_string($con, $_POST['title']);
    $description = mysqli_real_escape_string($con, $_POST['description']);
    $category = $_POST['category'];
    $type = $_POST['type'];
    $level = $_POST['level'];
    $status = $_POST['status'];
    $error = "";

    // Handle file upload only if new file is selected
    if (!empty($_FILES['featured']['name'])) {
        $uploadDir = '../../uploads/';
        $fileKey = 'featured';
        global $fileName;
        $picture = handleFileUpload($fileKey, $uploadDir, $fileName);
        
        $query = "UPDATE `".$siteprefix."courses` SET 
            title = '$title',
            description = '$description',
            language = '$category',
            type = '$type',
            level = '$level',
            featured_image = '$picture',
            updated_date = NOW(),
            updated_by = '$name',
            status = '$status'
            WHERE s = '$course_id'";
    } else {
        $query = "UPDATE `".$siteprefix."courses` SET 
            title = '$title',
            description = '$description',
            language = '$category',
            type = '$type',
            level = '$level',
            updated_date = NOW(),
            updated_by = '$name',
            status = '$status'
            WHERE s = '$course_id'";
    }

    $submit = mysqli_query($con, $query);
    if ($submit) {
        $statusAction = "Successful";
        $statusMessage = "Course updated successfully! $error";
        showSuccessModal($statusAction, $statusMessage);
        header("refresh:1; url=courses.php");
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
    $error = "";

    // Handle file upload only if new file is selected and content type is not text
    if (!empty($_FILES['media']['name']) && $type != "text") {
        $uploadDir = '../../uploads/';
        $fileKey = 'media';
        global $fileName;
        $media = handleFileUpload($fileKey, $uploadDir, $fileName);
        
        $query = "UPDATE `".$siteprefix."theory` SET 
            title = '$title',
            subtitle = '$subtitle',
            duration = '$duration',
            content_type = '$type',
            content = '$content',
            media_content = '$media',
            updated_on = NOW()
            WHERE s = '$section_id'";
    } else {
        $query = "UPDATE `".$siteprefix."theory` SET 
            title = '$title',
            subtitle = '$subtitle',
            duration = '$duration',
            content_type = '$type',
            content = '$content',
            updated_on = NOW()
            WHERE s = '$section_id'";
    }

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




	<form action="" method="post">
              <div class="form-group">
                <input type="text" class="form-control" placeholder="Your Name">
              </div>
              <div class="form-group">
                <input type="text" class="form-control" placeholder="Your Email">
              </div>
              <div class="form-group">
                <input type="text" class="form-control" placeholder="Subject">
              </div>
              <div class="form-group">
                <textarea name="" id="" cols="30" rows="7" class="form-control" placeholder="Message"></textarea>
              </div>
              <div class="form-group">
                <input type="submit" value="Send Message" class="btn btn-primary py-3 px-5">
              </div>
            </form>