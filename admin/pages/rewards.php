<?php include "header.php"; ?>

<div class="container-xxl flex-grow-1 container-p-y">
              <!-- Hoverable Table rows -->
              <div class="card">
                <h5 class="card-header">Rewards Leaderboard</h5>
                <div class="table-responsive text-nowrap ">
                  <table class="table table-hover">
                    <thead>
                      <tr>
                        <th>S/N</th>
                        <th>User</th>
                        <th>Reward Points</th>
                      </tr>
                    </thead>
                    <tbody class="table-border-bottom-0">
<?php $sql = "SELECT * FROM ".$siteprefix."users WHERE type != 'admin' ORDER BY reward_points DESC";
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
                        <td><?php echo $reward_points; ?></td>
                      </tr>
                      <?php $i++; } ?>
                    </tbody>
                  </table>
                </div>
              </div>
              <!--/ Hoverable Table rows -->
            </div>

<?php include "footer.php"; ?>
