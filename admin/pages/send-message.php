<?php include "header.php"; ?>

<div class="container-xxl flex-grow-1 container-p-y">

<!-- Basic Layout -->
               <div class="row">
                <div class="col-xl">
                  <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="mb-0">Send Message</h5>
                    </div>
                    <div class="card-body">
                      <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Message Subject</label>
                          <input type="text" class="form-control" name="title" id="basic-default-fullname" placeholder="Learning loops" required>
                        </div>
                        <div class="mb-3">
                        <label class="form-label" for="exampleFormControlSelect1">Select Recipient(s)</label>
                        <select class="form-select select-multiple" name="user" id="exampleFormControlSelect1" aria-label="Default select example" multiple="multiple">
                          <option value="all">All Users</option>
                          <option value="instructor">All Instructors</option>
                          <option value="user">All Users</option>
                          <?php
                     $sql = "SELECT * FROM " . $siteprefix . "users where type !='admin'";
                     $sql2 = mysqli_query($con, $sql);
                     while ($row = mysqli_fetch_array($sql2)) {
                      echo '<option value="' . $row['email'] . '">' . $row['name'] . ' (' . $row['type'] . ')</option>'; }?>
                        </select>
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-message">Message Content</label>
                          <textarea id="basic-default-message" name="content" class="form-control  editor" placeholder="This course is a course for ..." required/></textarea>
                        </div>
                        <button type="submit" name="sendmessage" value="send" class="btn btn-primary w-100">Send Message</button>
                      </form>
                    </div>
                  </div>
                </div>

              </div>
            </div>


            <?php include "footer.php"; ?>
