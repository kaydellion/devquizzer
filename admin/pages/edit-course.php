<?php include "header.php"; 

$course_id = $_GET['course'] ?? null;
if (!$course_id) {
  header("Location: courses.php");
  exit();
}


$query = "SELECT c.*, l.title AS category FROM ".$siteprefix."courses c LEFT JOIN ".$siteprefix."languages l ON c.language=l.s WHERE c.s = '$course_id'";
$result = mysqli_query($con, $query);
if (mysqli_num_rows($result) > 0) {
  while ($row = mysqli_fetch_assoc($result)) {
    // Accessing individual fields
    $course_id = $row['s'];
    $title = $row['title'];
    $description = $row['description'];
    $category = $row['category'];
    $category_id = $row['language'];
    $level = $row['level'];
    $type = $row['type'];
    $Dateupdated = $row['updated_date'];
    $status = $row['status'];
    $dateCreated = $row['created_date'];
    $owner = $row['updated_by'];
    $course_media = $row['featured_image'];

  }}

?>

<div class="container-xxl flex-grow-1 container-p-y">

<!-- Basic Layout -->
               <div class="row">
                <div class="col-xl">
                  <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="mb-0">Edit Course</h5>
                    </div>
                    <div class="card-body">
                      <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Title</label>
                          <input type="text" class="form-control" name="title" id="basic-default-fullname" placeholder="Learning loops" value="<?php echo $title; ?>" required/>
                        </div>
                        <div class="mb-3">
                        <select class="form-select" name="category" id="exampleFormControlSelect1" aria-label="Default select example" required>
                          <option>- Select Category -</option>
                          <?php
                     $sql = "SELECT * FROM " . $siteprefix . "languages";
                     $sql2 = mysqli_query($con, $sql);
                     while ($row = mysqli_fetch_array($sql2)) {
                        $selected = ($row['s'] == $category_id) ? 'selected' : '';
                        echo '<option value="' . $row['s'] . '" ' . $selected . '>' . $row['title'] . '</option>';
                     }?>
                        </select>
                        </div>
                        <div class="mb-3">
                        <select class="form-select" name="type" id="exampleFormControlSelect1" aria-label="Default select example" required>
                          <option>- Select Type -</option>
                          <?php
                          $types = array("Theory and Code", "Theory", "Code");
                          foreach($types as $type) {
                              $selected = ($type == $type) ? 'selected' : '';
                              echo '<option value="' . $type . '" ' . $selected . '>' . $type . '</option>';
                          }
                          ?>
                        </select>
                        </div>
                        <div class="mb-3">
                        <select class="form-select" name="level" id="exampleFormControlSelect1" aria-label="Default select example" required>
                          <option>- Select Level -</option>
                          <?php
                          $levels = array("Beginner", "Intermediate", "Expert");
                          foreach($levels as $level) {
                              $selected = ($level == $level) ? 'selected' : '';
                              echo '<option value="' . $level . '" ' . $selected . '>' . $level . '</option>';
                          }
                          ?>
                        </select>
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-message">Description</label>
                          <textarea id="basic-default-message" name="description" class="form-control" placeholder="This course is a course for ..." required><?php echo $description; ?></textarea>
                        </div>
                        <div class="mb-3">
                        <label for="formFile" class="form-label">Select New  featured image</label>
                        <input class="form-control" type="file" name="featured" id="formFile"/>
                        <small class="text-muted">Current image: <img style="width: 20%;"  src="<?php echo $siteurl;?>uploads/<?php echo htmlspecialchars($course_media); ?>" alt="Course Image"></small>
                      </div>
                      <div class="mb-3">
                        <select class="form-select" name="status" id="exampleFormControlSelect1" aria-label="Default select example" required>
                          <option>- Course Publicity -</option>
                          <?php
                          $statuses = array("publish", "draft");
                          foreach($statuses as $status) {
                              $selected = ($status == $status) ? 'selected' : '';
                              echo '<option value="' . $status . '" ' . $selected . '>' . $status . '</option>';
                          }
                          ?>
                        </select>
                        </div>
                        <input type="hidden" name="course_id" value="<?php echo $course_id; ?>">
                        <button type="submit" name="updatecourse" value="course" class="btn btn-primary w-100">Update Course</button>
                      </form>
                    </div>
                  </div>
                </div>

              </div>
            </div>


            <?php include "footer.php"; ?>
       