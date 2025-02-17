<?php include "header.php";
$show="none"; $email=""; ifLoggedin($active_log);
if(isset($_GET['user_login'])){
    $user_log=$_GET['user_login'];
    $sql = "SELECT * from ".$siteprefix."users where s='$user_log'";
    $sql2 = mysqli_query($con,$sql);
    while($row = mysqli_fetch_array($sql2))
    {$email = $row['email']; $pass = $row['password']; }
     $show="block"; 
    showToast("Congratulations! Your account has been successfully created. Thank you for registering! Login now");}
   
  ?>

<main class="main">
<div class="row m-0" style="height: 100vh;">
    
<div class="col-lg-6 signin-bg d-none d-lg-block">
</div>


<div class="col-lg-6 side-padding">
<p class="pt-5"><img src="assets/img/pagelogo.png" class="logo"></p>
<form method="POST">
<p class="text-bold">Sign in to continue coding!</p>
<div class="form-group pt-3">
    <label for="exampleInputEmail1">Email address</label>
    <input type="email" class="form-control" name="email" id="exampleInputEmail1" value="<?php echo $email; ?>" aria-describedby="emailHelp" placeholder="Enter email">
  </div>
  <div class="form-group pt-3">
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
<p class="pt-3"><label><input type="checkbox"> Remember me</label></p>
<p><button class="w-100 btn-get-started" name="login" value="login">Sign In</button></p>
</form>

<p class="pt-3"><hr></p>
<p><a href="forgotpassword.php" class="w-100 btn-get-started text-center bg-dark">Forgot Password?</a></p>
<p>Haven't experienced DevQuizzer yet? <a href="signup.php">Create an account here</a></p>
</div>


</div>





</main>
<?php include "footer.php"; ?>