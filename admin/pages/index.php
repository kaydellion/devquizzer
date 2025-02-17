<?php include "header.php"; ?>

            <div class="container-xxl flex-grow-1 container-p-y">
              <div class="row">
                <div class="col-lg-12 mb-4 order-0">
                  <div class="card">
                    <div class="d-flex align-items-end row">
                      <div class="col-sm-7">
                        <div class="card-body">
                          <h5 class="card-title text-primary">Hey there,Devquizzer! 🎉</h5>
                          <p class="mb-4">Here’s the activity summary for today<br>
                          <span class="fw-bold"><?php echo $todayUsers; ?></span> new users registered, bringing fresh engagement and opportunities<br>
                          <span class="fw-bold"><?php echo $todayEnrollments; ?></span> courses were enrolled for,  showcasing growing interest in your content<br>
                          While today’s numbers might vary, they offer valuable insights into user behavior and engagement. Whether you’re seeing a surge or a steady trend, each day provides an opportunity to refine your approach.
                          </p>


                          <a href="javascript:;" class="btn btn-sm btn-outline-primary">View More</a>
                        </div>
                      </div>
                      <div class="col-sm-5 text-center text-sm-left">
                        <div class="card-body pb-0 px-0 px-md-4">
                          <img
                            src="../assets/img/illustrations/man-with-laptop-light.png"
                            height="140"
                            alt="View Badge User"
                            data-app-dark-img="illustrations/man-with-laptop-dark.png"
                            data-app-light-img="illustrations/man-with-laptop-light.png"
                          />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-lg-12 col-md-4 order-1">
                  <div class="row">

                    <div class="col-lg-4 col-md-12 col-6 mb-4">
                      <div class="card">
                        <div class="card-body">
                          <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                              <img src="../assets/img/icons/unicons/dash-icon/1.png" alt="Credit Card" class="rounded"/>
                            </div>
                          </div>
                          <span>Total Users</span>
                          <h3 class="card-title text-nowrap mb-1"><?php echo $totalUsers; ?></h3>
                          <a href="users.php"><small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i>View Users</small></a>
                        </div>
                      </div>
                    </div>
                    <div class="col-lg-4 col-md-12 col-6 mb-4">
                      <div class="card">
                        <div class="card-body">
                          <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                              <img src="../assets/img/icons/unicons/dash-icon/2.png" alt="Credit Card" class="rounded"/>
                            </div>
                          </div>
                          <span>Enrolled Courses</span>
                          <h3 class="card-title text-nowrap mb-1"><?php echo $totalEnrolled; ?></h3>
                          <a href="enrolled_courses.php"><small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i>View Courses</small></a>
                        </div>
                      </div>
                    </div>
                  
                  <div class="col-lg-4 col-md-12 col-12 mb-4">
                      <div class="card">
                        <div class="card-body">
                          <div class="card-title d-flex align-items-start justify-content-between">
                            <div class="avatar flex-shrink-0">
                              <img src="../assets/img/icons/unicons/dash-icon/3.png" alt="Credit Card" class="rounded"/>
                            </div>
                          </div>
                          <span>Draft Courses</span>
                          <h3 class="card-title text-nowrap mb-1"><?php echo $draftCourses; ?></h3>
                          <a href="pending_courses.php"><small class="text-success fw-semibold"><i class="bx bx-up-arrow-alt"></i>View Courses</small></a>
                        </div>
                      </div>
                    </div>
                
                  
                  </div>  
                </div>
            </div>
            <div class="row">
                <!-- Rewards Leaderboard -->
                <div class="col-md-12 col-lg-8 order-0 mb-4">
                  <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between">
                      <h5 class="card-title m-0 me-2">Rewards Leaderboard</h5>
                    </div>
                    <div class="card-body">
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
                </div>
                <!--/ Rewards -->
                <!-- Courses Ranking -->
                <div class="col-md-12 col-lg-4 order-0 mb-4">
                  <div class="card h-100">
                    <div class="card-header d-flex align-items-center justify-content-between pb-0">
                      <div class="card-title mb-0">
                        <h5 class="m-0 me-2">Languages Ranking</h5>
                        <small class="text-muted">This displays how many users have enrolled for courses under any of the following languages</small>
                      </div>
                    </div>
                    <div class="card-body mt-3">
                      <ul class="p-0 m-0">
                      <?php
    $sql = "SELECT l.*, COUNT(c.s) as stats FROM " . $siteprefix . "languages l LEFT JOIN " . $siteprefix . "courses c ON l.s = c.language AND c.status='publish' GROUP BY l.s order by stats DESC";
    $sql2 = mysqli_query($con, $sql);
    while ($row = mysqli_fetch_array($sql2)) { ?>
                        <li class="d-flex mb-4 pb-1">
                          <div class="avatar flex-shrink-0 me-3">
                            <span class="avatar-initial rounded bg-label-primary"
                              ><i class="bx bx-<?php echo $row['display_picture']; ?>"></i></span>
                          </div>
                          <div class="d-flex w-100 flex-wrap align-items-center justify-content-between gap-2">
                            <div class="me-2">
                              <h6 class="mb-0"><?php echo $row['title']; ?></h6>
                              <small class="text-muted"><?php echo limitDescriptionshort($row['subtext']); ?></small>
                            </div>
                            <div class="user-progress">
                              <small class="fw-semibold"><?php echo $row['stats']; ?></small>
                            </div>
                          </div>
                        </li>
<?php } ?>
                      </ul>
                    </div>
                  </div>
                </div>
                <!--/ Ranking -->
              
              </div>
            <!-- / Content -->
          <?php include "footer.php"; ?>