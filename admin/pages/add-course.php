<?php include "header.php"; ?>

<div class="container-xxl flex-grow-1 container-p-y">

<!-- Basic Layout -->
               <div class="row">
                <div class="col-xl">
                  <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="mb-0">Create New Course</h5>
                    </div>
                    <div class="card-body">
                      <form method="POST" enctype="multipart/form-data">
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Title</label>
                          <input type="text" class="form-control" name="title" id="basic-default-fullname" placeholder="Learning loops" required//>
                        </div>
                        <div class="mb-3">
                        <select class="form-select" name="category" id="exampleFormControlSelect1" aria-label="Default select example" required/>
                          <option selected>- Select Category -</option>
                          <?php
                     $sql = "SELECT * FROM " . $siteprefix . "languages";
                     $sql2 = mysqli_query($con, $sql);
                     while ($row = mysqli_fetch_array($sql2)) {
                      echo '<option value="' . $row['s'] . '">' . $row['title'] . '</option>'; }?>
                        </select>
                        </div>
                        <div class="mb-3">
                        <select class="form-select" name="type" id="exampleFormControlSelect1" aria-label="Default select example" required/>
                          <option selected>- Select Type -</option>
                          <option value="Theory and Code">Theory and Code</option>
                          <option value="Theory">Theory</option>
                          <option value="Code">Code</option>
                        </select>
                        </div>
                        <div class="mb-3">
                        <select class="form-select" name="level" id="exampleFormControlSelect1" aria-label="Default select example" required/>
                          <option selected>- Select Level -</option>
                          <option value="Beginner">Beginner</option>
                          <option value="Intermediate">Intermediate</option>
                          <option value="Expert">Expert</option>
                        </select>
                        </div>
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-message">Description</label>
                          <textarea id="basic-default-message" name="description" class="form-control" placeholder="This course is a course for ..." required/></textarea>
                        </div>
                        <div class="mb-3">
                        <label for="formFile" class="form-label">Select featured image</label>
                        <input class="form-control" type="file" name="featured" id="formFile" accept="image/*" required/>
                      </div>
                      <div class="mb-3">
                        <select class="form-select" name="status" id="exampleFormControlSelect1" aria-label="Default select example" required/>
                          <option selected>- Course Publicity -</option>
                          <option value="publish">Publish</option>
                          <option value="draft">Draft</option>
                        </select>
                        </div>
                        <button type="submit" name="addcourse" value="course" class="btn btn-primary w-100">Create Course</button>
                      </form>
                    </div>
                  </div>
                </div>

              </div>
            </div>


            <?php include "footer.php"; ?>
