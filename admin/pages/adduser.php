<?php include "header.php"; ?>

<div class="container-xxl flex-grow-1 container-p-y">
<div class="card">
<div class="card-body">
<form method="POST" enctype="multipart/form-data">
<p class="text-bold text-dark">Create a new user account here.</p>
<div class="form-row row">
    <div class="form-group col-md-6 mb-3">
      <label for="fullName">Full Name</label>
      <input type="text" class="form-control" id="fullName" name="fullName" required>
    </div>
    <div class="form-group col-md-6 mb-3">
      <label for="email">Email</label>
      <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
    </div>
    </div>
    <div class="form-row row">
    <div class="form-group col-md-6 mb-3">
      <label for="password">Password</label>
      <div class="input-group">
      <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
      <div class="input-group-append">
        <span class="input-group-text p-3" onclick="togglePasswordVisibility('password')">
        <i class="bx bx-low-vision" id="togglePasswordIcon"></i>
        </span>
      </div>
      </div>
    </div>
    <div class="form-group col-md-6 mb-3">
      <label for="retypePassword">Retype Password</label>
      <div class="input-group">
      <input type="password" class="form-control" id="retypePassword" name="retypePassword" placeholder="Password" required>
      <div class="input-group-append">
        <span class="input-group-text p-3" onclick="togglePasswordVisibility('retypePassword')">
        <i class="bx bx-low-vision" id="toggleRetypePasswordIcon"></i>
        </span>
      </div>
      </div>
    </div>
    </div>
    <div class="d-flex justify-space-between mb-3">
    <div class="radio-container m-1">
    <input class="form-check-input" type="radio" id="option1" name="options" value="Theory and Code" checked required>
    <label for="option1">Theory and Code</label>
    </div>
    <div class="radio-container m-1">
    <input class="form-check-input" type="radio" id="option1" name="options" value="Theory" required>
    <label for="option1">Theory</label>
    </div>
    <div class="radio-container m-1">
    <input class="form-check-input" type="radio" id="option2" name="options" value="Code" required>
    <label for="option2">Code</label>
    </div>
  </div>
  <div class="mb-3">
  <select class="form-select" name="type" id="exampleFormControlSelect1" aria-label="Default select example" required>
                          <option selected>- Select Type -</option>
                          <option value="user">User</option>
                          <option value="instructor">Instructor</option>
                        </select>
                        </div>
<p><button class="w-100 btn btn-primary" name="register" value="register-user">Create Account</button></p>
</form>


</div>
</div>
</div>





</main>
<?php include "footer.php"; ?>