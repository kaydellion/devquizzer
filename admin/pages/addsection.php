<?php include "header.php";
if(isset($_GET['course'])){ $course=$_GET['course']; }else {header("courses.php");} ?>
<script>
$(document).ready(function(){
  $('#contentType').change(function() {
    var selectedType = $(this).val();
    
    // Hide both sections by default
    $('#contentSection').hide();
    $('#mediaSection').hide();

    // Show the relevant section based on selection
    if (selectedType === 'text') {
      $('#contentSection').show();
      $('#mediaSection').hide();
    } else if (selectedType === 'media') {
      $('#mediaSection').show();
      $('#contentSection').hide();
    }
  });
});
</script>
<div class="container-xxl flex-grow-1 container-p-y">
               <div class="row">
                <div class="col-xl">
                  <div class="card mb-4">
                    <div class="card-header d-flex justify-content-between align-items-center">
                      <h5 class="mb-0">Add Section</h5>
                    </div>
                    <div class="card-body">
                      <form method="POST" enctype="multipart/form-data">
                        <input value="<?php echo $course; ?>" name="course" hidden />
                        <div class="mb-3">
                          <label class="form-label" for="basic-default-fullname">Section Title</label>
                          <input type="text" class="form-control" name="title" id="basic-default-fullname" placeholder="Learning loops beginning"  required/>
                        </div>
                        <div class="mb-3">
                          <label class="form-label">Section subtitle</label>
                          <textarea name="subtitle" class="form-control" placeholder="This course is a course for ..." required></textarea>
                        </div>
                        <div class="mb-3">
                        <select class="form-select" name="type" id="contentType" required>
                          <option selected>- Select Content Type -</option>
                          <option value="media">Media</option>
                          <option value="text">Text</option>
                        </select>
                        </div>
                        <div class="mb-3" id="mediaSection" style="display:none;">
                        <label for="formFile" class="form-label">Section Media</label>
                        <input class="form-control" type="file" name="media" id="formFile" />
                       </div>
                        <div class="mb-3" id="contentSection" style="display:none;">
                          <label class="form-label" for="basic-default-message">Section Content</label>
                          <textarea id="basic-default-message" name="content" class="editor form-control" placeholder="This course is a course for ..." ></textarea>
                        </div>
                       <div class="mb-3">
                        <label for="formFile" class="form-label">Estimated Duration (mins) </label>
                        <input class="form-control" type="number" name="duration" required/>
                       </div>
                       <!-- <div class="mb-3">
                        <label for="formFile" class="form-label">Section Arrangement Number ( 1 - **)</label>
                        <input class="form-control" type="number" name="section" required/>
                       </div> -->
                        <button type="submit" name="addsection" value="course" class="btn btn-primary w-100">Add Section</button>
                      </form>
                    </div>
                  </div>
                </div>

              </div>
            </div>


            <?php include "footer.php"; ?>
