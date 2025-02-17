<?php include "header.php"; ?>
<div class="container-xxl flex-grow-1 container-p-y">
              <div class="row">
                <div class="col-lg-12 mb-4 order-0">
                  <div class="card">
                <div class="card-body">
                    <p><a href="?action=read-message" class="read btn btn-primary">Mark All as Read</a></p>
                    <h5 class="card-title">Recent Notifications</h5>
                    <ul class="list-unstyled">
                        <?php
                        $sql = "SELECT * FROM dv_alerts WHERE status='0' ORDER BY s DESC LIMIT 5";
                        $sql2 = mysqli_query($con,$sql);
                        $count = mysqli_num_rows($sql2);
                        if ($count <= 0) {
                            echo "<p>No notifications found.</p>";
                        } else {
                            while ($row = mysqli_fetch_array($sql2)) {
                                $date = $row['date'];
                                $thelink = $row['link'];
                                $thealert = $row['s'];
                                $thetype = $row['type'];
                                $themessage = $row['message'];
                        ?>
                            <li class="notification-item bg-light p-3 mb-2 rounded w-100">
                                <?php if ($thelink) { ?>
                                    <a href="<?php echo htmlspecialchars($thelink); ?>" class="text-decoration-none">
                                <?php } ?>
                                <p class="text-dark mb-1"><?php echo htmlspecialchars($themessage); ?></p>
                                <small class="text-muted"><?php echo htmlspecialchars($date); ?></small>
                                <?php if ($thelink) { ?>
                                    </a>
                                <?php } ?>
                            </li>
                        <?php 
                            }
                        } 
                        ?>
                    </ul>
                </div>
                  </div>
                </div>
            </div>

<?php include "footer.php"; ?>
