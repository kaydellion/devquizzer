<?php include "header.php"; ?>


<div class="container-xxl flex-grow-1 container-p-y">

              <!-- Hoverable Table rows -->
              <div class="card">
                <h5 class="card-header">Enrolled Courses</h5>
                <div class="table-responsive text-nowrap ">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>S/N</th>
                        <th>Course Title</th>
                        <th>Category</th>
                        <th>Students</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
<?php  $query = "SELECT c.*, l.title AS category, COUNT(e.`s`) AS enrolled_students 
                  FROM `{$siteprefix}courses` c 
                  LEFT JOIN `{$siteprefix}languages` l ON c.language=l.`s` 
                  LEFT JOIN `{$siteprefix}enrolled_courses` e ON c.`s`=e.course_id 
                  GROUP BY c.`s` 
                  HAVING enrolled_students > 0
                  ORDER BY enrolled_students DESC";
        $result = mysqli_query($con, $query);
        if (!$result) {
            die("Query failed: " . mysqli_error($con));
        }
        if(mysqli_num_rows($result) > 0 ) { $i=1;
        while ($row = mysqli_fetch_assoc($result)) {
            // Accessing individual fields
            $course_id = $row['s'];
            $title = $row['title'];
            $description = limitDescription($row['description']);
            $category = $row['category'];
            $Dateupdated = $row['updated_date'];
            $status = $row['status'];
            $dateCreated = $row['created_date']; 
            $owner = $row['updated_by'];
            $students = $row['enrolled_students'];
            
            ?>
                      <tr>
                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo $i; ?></strong></td>
                        <td><?php echo $title; ?></td>
                        <td><?php echo $category; ?></td>
                        <td><?php echo $students; ?></td>
                        <td><a class="btn btn-primary text-small" target="_blank" href="<?php echo $siteurl;?>course-view.php?course=<?php echo $course_id; ?>"><i class="bx bx-eye me-1"></i>View Course</a>
                        </td>
                      </tr>
                      <?php $i++; }} ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <!--/ Hoverable Table rows -->

            

            </div>




<?php include "footer.php"; ?>
