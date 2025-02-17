<?php include "header.php"; ?>

<div class="container-xxl flex-grow-1 container-p-y">

<!-- Basic Layout -->
               <div class="row">
                <div class="col-xl">
                  <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="mb-0">Create New Quiz</h5>
                    </div>
                    <div class="card-body">
                      <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Title</label>
                          <input type="text" class="form-control" name="title" id="basic-default-fullname" placeholder="Learning loops v2" required//>
                        </div>
                        <div class="mb-3">
                        <select class="form-select" name="course" id="exampleFormControlSelect1" aria-label="Default select example" required/>
                        <option selected>- Select Course Section -</option>
                          <?php
                     $sql = "SELECT c.* FROM " . $siteprefix . "courses c
                     LEFT JOIN " . $siteprefix . "quiz dq ON c.s = dq.course_id
                     WHERE dq.course_id IS NULL";
             $sql2 = mysqli_query($con, $sql);
             while ($row = mysqli_fetch_array($sql2)) {
                 echo '<option value="' . $row['s'] . '">' . $row['title'] . '</option>';
             }
             ?>
                        </select>
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-message">Description</label>
                          <textarea id="basic-default-message" name="description" class="form-control" placeholder="Please make sure you have read all of module 2" required/></textarea>
                         </div>
                         <div class="mb-3">
                        <label for="formFile" class="form-label">Duration(mins)</label>
                        <input class="form-control" type="number" name="duration" required/>
                       </div>
                       <div class="mb-3">
                        <label for="formFile" class="form-label">Reward Points Assigned (* note that points are awarded if user passes 80% of the quiz)</label>
                        <input class="form-control" type="number" name="points" required/>
                       </div>
                        <button type="submit" name="addquiz" value="course" class="btn btn-primary w-100">Create Quiz</button>
                      </form>
                    </div>
                  </div>
                </div>

              </div>
            </div>


            <?php include "footer.php"; ?>
