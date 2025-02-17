<?php include "header.php"; ?>


<div class="container-xxl flex-grow-1 container-p-y">

              <!-- Hoverable Table rows -->
              <div class="card">
                <h5 class="card-header">Pending Courses</h5>
                <div class="table-responsive text-nowrap ">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>S/N</th>
                        <th>Course Title</th>
                        <th>Category</th>
                        <th>Uploaded by</th>
                        <th>Last Updated</th>
                        <th>Status</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
<?php $query = "SELECT c.*, l.title AS category FROM ".$siteprefix."courses c LEFT JOIN ".$siteprefix."languages l ON c.language=l.s WHERE c.status='draft'"; 
        $result = mysqli_query($con, $query);
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
            $course_media = $row['featured_image'];

            $formatedupdatedate=formatDateTime2($Dateupdated);
            $formateduploaddate=formatDateTime2($dateCreated);
            
            ?>
                      <tr>
                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo $i; ?></strong></td>
                        <td><?php echo $title; ?></td>
                        <td><?php echo $category; ?></td>
                        <td><?php echo $owner; ?></td>
                        <td><?php echo $formatedupdatedate; ?></td>
                        <td><span class="badge bg-label-<?php echo getBadgeColor($status); ?> me-1"><?php echo $status; ?></span></td>
                        <td><div class="dropdown">
                        <button type="button" class="btn btn-primary text-small dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>Manage</button>
                            <div class="dropdown-menu">
                            <a class="dropdown-item" href="edit-course.php?course=<?php echo $course_id; ?>"><i class="bx bx-edit-alt me-1"></i> Edit Course</a>
                            <a class="dropdown-item" href="sections.php?course=<?php echo $course_id; ?>"><i class="bx bx-edit-alt me-1"></i> Manage Sections</a>
                            <a class="dropdown-item" href="?action=delete&table=courses&item=<?php echo $course_id; ?>"><i class="bx bx-trash me-1"></i> Delete</a>
                            </div>
                          </div>
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
