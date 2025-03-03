<?php include "header.php"; ?>

<main class="main">
<div class="row m-0" style="height: 100vh;">
    
<div class="col-lg-6 signup-bg d-none d-lg-block">
</div>


<div class="col-lg-6 side-padding">
<p class="pt-5"><img src="assets/img/pagelogo.png" class="logo"></p>
<form method="POST" enctype="multipart/form-data">
<p class="text-bold text-dark">Sign up to access courses, gamified learning, and more</p>
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
        <span class="input-group-text" onclick="togglePasswordVisibility('password')">
        <i class="bi bi-eye" id="togglePasswordIcon"></i>
        </span>
      </div>
      </div>
    </div>
    <div class="form-group col-md-6 mb-3">
      <label for="retypePassword">Retype Password</label>
      <div class="input-group">
      <input type="password" class="form-control" id="retypePassword" name="retypePassword" placeholder="Password" required>
      <div class="input-group-append">
        <span class="input-group-text" onclick="togglePasswordVisibility('retypePassword')">
        <i class="bi bi-eye" id="toggleRetypePasswordIcon"></i>
        </span>
      </div>
      </div>
    </div>
    </div>
    <div class="d-flex justify-space-between">
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
<p class="pt-3"><label><input type="checkbox" required> I agree to all the <a href="terms.php">Terms</a> and <a href="policies.php">Privacy Policies</a></label></p>
<p><button class="w-100 btn-get-started" name="register" value="register-user">Create Account</button></p>
<script src="https://accounts.google.com/gsi/client" async defer></script>

<div id="g_id_onload"
     data-client_id="194981606848-ll4hbu3g2tbkjco21rvepv5ii85er19n.apps.googleusercontent.com"
     data-callback="handleCredentialResponse"
     data-auto_prompt="false">
</div>

<div class="g_id_signin"
     data-type="standard"
     data-size="large"
     data-theme="outline"
     data-text="sign_in_with"
     data-shape="rectangular"
     data-logo_alignment="left">
</div>

</form>

<p class="pt-3"><hr></p>
<p>Already have an account?<a href="signin.php"> Login</a></p>
</div>


</div>





</main>
<?php include "footer.php"; ?>