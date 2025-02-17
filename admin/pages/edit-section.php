<?php include "header.php";
if(isset($_GET['id'])){ 
    $section = $_GET['id'];
    $query = "SELECT * FROM {$siteprefix}theory WHERE s = $section";
    $result = mysqli_query($con, $query);
    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $section_title = $row['title'];
        $course = $row['course_id'];
        $section_description = $row['subtitle'];
        $content_type = $row['content_type'];
        $content_text = $row['content'];
        $content_media = $row['media_content'];
        $duration = $row['duration'];
        $chapter = $row['chapter'];

        $content = '<div class="video-container" style="width: 20%;">
        <video controls style="width: 100%;">
            <source src="../../uploads/' . $content_media . '" type="video/mp4">
            Your browser does not support the video tag.
        </video>
    </div>';
    }
} else {header($previousPage);}
?>
<!-- Rest of your JavaScript remains the same -->

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-xl">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Edit Section</h5>
                </div>
                <div class="card-body">
                    <form method="POST" enctype="multipart/form-data">
                        <input value="<?php echo $course; ?>" name="course" hidden />
                        <input value="<?php echo $section; ?>" name="section" hidden />
                        <input value="<?php echo $previousPage; ?>" name="previous" hidden />
                        <div class="mb-3">
                            <label class="form-label" for="basic-default-fullname">Section Title</label>
                            <input type="text" class="form-control" name="title" value="<?php echo $section_title; ?>" id="basic-default-fullname" placeholder="Learning loops beginning" required/>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Section subtitle</label>
                            <textarea name="subtitle" class="form-control" placeholder="This course is a course for ..." required><?php echo $section_description; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <select class="form-select" name="type" id="contentType" required>
                                <option>- Select Content Type -</option>
                                <option value="media" <?php echo ($content_type == 'media') ? 'selected' : ''; ?>>Media</option>
                                <option value="text" <?php echo ($content_type == 'text') ? 'selected' : ''; ?>>Text</option>
                            </select>
                        </div>
                        <div class="mb-3" id="mediaSection" style="display:<?php echo ($content_type == 'media') ? 'block' : 'none'; ?>">
                            <label for="formFile" class="form-label">Section Media</label>
                            <input class="form-control" type="file" name="media" id="formFile" />
                            <?php if($content_media) { echo $content; } ?>
                        </div>
                        <div class="mb-3" id="contentSection" style="display:<?php echo ($content_type == 'text') ? 'block' : 'none'; ?>">
                            <label class="form-label" for="basic-default-message">Section Content</label>
                            <textarea id="basic-default-message" name="content" class="editor form-control" placeholder="This course is a course for ..."><?php echo $content_text; ?></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="formFile" class="form-label">Estimated Duration (mins)</label>
                            <input class="form-control" type="number" name="duration" value="<?php echo $duration; ?>" required/>
                        </div>

                        <div class="mb-3">
                            <label for="formFile" class="form-label">Chapter Arrangement</label>
                            <input class="form-control" type="number" name="chapter" value="<?php echo $chapter; ?>" required/>
                        </div>
                        <button type="submit" name="updatesection" value="course" class="btn btn-primary w-100">Update Section</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
