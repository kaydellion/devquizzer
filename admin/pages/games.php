<?php include "header.php"; ?>


<div class="container-xxl flex-grow-1 container-p-y">

              <!-- Hoverable Table rows -->
              <div class="card">
                <h5 class="card-header">Gaming Module Management</h5>
                <div class="table-responsive text-nowrap ">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>S/N</th>
                        <th>Lanuage</th>
                        <th>Course</th>
                        <th>Levels</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
<?php 

$query = "SELECT g.*, c.title AS course, l.title AS language_name, (SELECT COUNT(*) FROM ".$siteprefix."game_tasks WHERE course_id=g.course_id) as tasks_count FROM ".$siteprefix."game_tasks g 
LEFT JOIN ".$siteprefix."courses c ON g.course_id=c.s LEFT JOIN ".$siteprefix."languages l ON c.language=l.s GROUP BY g.course_id"; 
 $result = mysqli_query($con, $query);
  if(mysqli_num_rows($result) > 0 ) { $i=1;
  while ($row = mysqli_fetch_assoc($result)) {
      // Accessing individual fields
      $course_id = $row['course_id'];
      $title = $row['title'];
      $description = limitDescription($row['description']);
      $course = $row['course'];
      $Dateupdated = $row['points'];
      $language = $row['language_name'];
      $tasks = $row['tasks_count'];
      ?>
          <tr>
      <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo $i; ?></strong></td>
      <td><?php echo $language; ?></td>
      <td><?php echo $course; ?></td>
      <td><?php echo $tasks; ?> levels</td>
      <td><a class="btn btn-primary text-small" href="game-tasks.php?id=<?php echo $course_id; ?>"><i class="bx bx-edit-alt me-1"></i> Manage Tasks</a>
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
