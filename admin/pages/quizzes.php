<?php include "header.php"; ?>


<div class="container-xxl flex-grow-1 container-p-y">

              <!-- Hoverable Table rows -->
              <div class="card">
                <h5 class="card-header">Quiz Management</h5>
                <div class="table-responsive text-nowrap ">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>S/N</th>
                        <th>Title</th>
                        <th>Course</th>
                        <th>Last Updated</th>
                        <th>Questions</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
<?php 
$query = "SELECT q.*, c.title AS course, (SELECT COUNT(*) FROM ".$siteprefix."quiz_questions WHERE quiz_id=q.s) as question_count FROM ".$siteprefix."quiz q LEFT JOIN ".$siteprefix."courses c ON q.course_id=c.s"; 
  $result = mysqli_query($con, $query);
  if(mysqli_num_rows($result) > 0 ) { $i=1;
  while ($row = mysqli_fetch_assoc($result)) {
      // Accessing individual fields
      $quiz_id = $row['s'];
      $title = $row['title'];
      $description = limitDescription($row['description']);
      $timer = $row['timer'];
      $course = $row['course'];
      $Dateupdated = $row['points']; 
      $updated = $row['updated_at'];
      $questions = $row['question_count'];

      $formatedupdatedate=formatDateTime2($updated);
      
      ?>
          <tr>
      <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo $i; ?></strong></td>
      <td><?php echo $title; ?></td>
      <td><?php echo $course; ?></td>
      <td><?php echo $formatedupdatedate; ?></td>
      <td><?php echo $questions; ?> questions</td>
      <td><div class="dropdown">
      <button type="button" class="btn btn-primary text-small dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
      <i class="bx bx-dots-vertical-rounded"></i>Manage</button>
          <div class="dropdown-menu">
          <a class="dropdown-item" href="edit-quiz.php?id=<?php echo $quiz_id; ?>"><i class="bx bx-edit-alt me-1"></i> Edit Quiz</a>
          <a class="dropdown-item delete" href="delete.php?action=delete&table=quiz&item=<?php echo $quiz_id; ?>&page=<?php echo $current_page; ?>"><i class="bx bx-trash me-1"></i> Delete</a>
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
