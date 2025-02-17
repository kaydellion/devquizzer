<?php include "header.php"; ?>


<div class="container-xxl flex-grow-1 container-p-y">

              <!-- Hoverable Table rows -->
              <div class="card">
                <h5 class="card-header">All Users</h5>
                <div class="table-responsive text-nowrap ">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>S/N</th>
                        <th>User</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Registered_Date</th>
                        <th>Last Login</th>
                        <th>Actions</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
<?php $sql = "SELECT * FROM ".$siteprefix."users  WHERE type  != 'admin'";
      $sql2 = mysqli_query($con, $sql);
      $i=1;
      while ($row = mysqli_fetch_array($sql2)) {
        $userid = $row["s"];
        $name = $row['name'];
        $email = $row['email'];
        $password = $row['password'];
        $type = $row['type'];
        $reward_points = $row['reward_points'];
        $created_date = $row['created_date'];
        $last_login = $row['last_login'];
        $email_verify = $row['email_verify'];
        $status = $row['status'];

            $formatedupdatedate=formatDateTime($last_login);
            $formateduploaddate=formatDateTime($created_date);
            
            ?>
                      <tr>
                        <td><i class="fab fa-angular fa-lg text-danger me-3"></i> <strong><?php echo $i; ?></strong></td>
                        <td><?php echo $name; ?></td>
                        <td><?php echo $email; ?></td>
                        <td><span class="badge bg-label-<?php echo getUserColor($type); ?> me-1"><?php echo $type; ?></span></td>
                        <td><?php echo $formateduploaddate; ?></td>
                        <td><?php echo $formatedupdatedate; ?></td>
                        <td><div class="dropdown">
                        <button type="button" class="btn btn-primary text-small dropdown-toggle hide-arrow" data-bs-toggle="dropdown">
                        <i class="bx bx-dots-vertical-rounded"></i>Manage</button>
                            <div class="dropdown-menu">
                            <a class="dropdown-item" href="edit-user.php?user=<?php echo $userid; ?>"><i class="bx bx-edit-alt me-1"></i> Edit </a>
                            <a class="dropdown-item delete" href="delete.php?action=delete&table=users&item=<?php echo $userid; ?>&page=<?php echo $current_page; ?>"><i class="bx bx-trash me-1"></i> Delete</a>
                            </div>
                          </div>
                        </td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <!--/ Hoverable Table rows -->

            

            </div>




<?php include "footer.php"; ?>
