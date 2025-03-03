<?php include "backend/connect.php"; 
 error_reporting(E_ALL); ini_set('display_errors', 1); ini_set('log_errors', 1);

//previous page
$_SESSION['previous_page'] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$previousPage = $_SESSION['previous_page'] ?? 'index.php';
$imagePath= "uploads/";

$code = "";
if (isset($_COOKIE['userID'])) {$code = $_COOKIE['userID'];}
$check = "SELECT * FROM ".$siteprefix."users WHERE s = '" . $code . "'";
$query = mysqli_query($con, $check);
if (mysqli_affected_rows($con) == 0) {
    $active_log = 0;
} else {
    $sql = "SELECT * FROM ".$siteprefix."users  WHERE s  = '".$code."'";
    $sql2 = mysqli_query($con, $sql);
    while ($row = mysqli_fetch_array($sql2)) {
        $id = $row["s"];
        $googleid = $row["google_id"];
        $name = $row['name'];
        $email = $row['email'];
        $password = $row['password'];
        $type = $row['type'];
        $reward_points = $row['reward_points'];
        $created_date = $row['created_date'];
        $last_login = $row['last_login'];
        $email_verify = $row['email_verify'];
        $status = $row['status'];
        $preference = $row['preference'];
        $profile_picture = !empty($row['profile_picture']) ? $row['profile_picture'] : 'user.png';
        

        

        $active_log = 1;
        $user_id=$id;
        $username=getFirstWord($name);
        $user_reg_date=formatDateTime($created_date);
        $user_lastseen=formatDateTime($last_login);

        if($googleid=""){$profile_picture=$imagePath.$profile_picture;}else{$profile_picture=$profile_picture;}



// Fetch notifications count
$sql = "SELECT COUNT(*) as count FROM ".$siteprefix."notifications WHERE user = ? AND status = 0 ";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$notification_count = $row['count'];

// Fetch current game level
$sql = "SELECT level FROM dv_game_progress WHERE user_id = ? ORDER BY timestamp DESC LIMIT 1";
$stmt = $con->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$currentgamelevel = $row['level'] ?? 1;
}}

//if($active_log==0){header("location: signup.php");}
//$adminlink=$siteurl.'/admin';
include "backend/actions.php"; ?>

<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="utf-8">
  <meta content="width=device-width, initial-scale=1.0" name="viewport">
  <title><?php echo $sitename; ?></title>
  <meta name="description" content="<?php echo $sitedescription; ?>">
  <meta name="keywords" content="<?php echo $sitekeywords; ?>">
  <meta name="title" content="ForestGigs - Login" />


<link type="image/x-icon" href="assets/img/<?php echo $siteimg; ?>" rel="shortcut icon" />

<link href="assets/img/<?php echo $siteimg; ?>" rel="apple-touch-icon" />
<meta name="apple-mobile-web-app-capable" content="yes" />
<meta name="apple-mobile-web-app-status-bar-style" content="black" />
<meta name="apple-mobile-web-app-title" content="<?php echo $sitename; ?>" />

<meta itemprop="name" content="<?php echo $sitename; ?>" />
<meta itemprop="description" content="<?php echo $sitedescription; ?>" />

<meta property="og:type" content="website" />
<meta property="og:title" content="<?php echo $sitename; ?>"/>
<meta property="og:description" content="" />
<meta property="og:image" content="assets/img/<?php echo $siteimg; ?>" />
<meta property="og:image:type" content="png" />
<meta property="og:image:width" content="1180" />
<meta property="og:image:height" content="600" />
<meta property="og:url" content="#" />
<meta name="twitter:card" content="summary_large_image" />


  <!-- Favicons -->
  <link href="assets/img/<?php echo $siteimg; ?>" rel="icon">
  <link href="assets/img/<?php echo $siteimg; ?>" rel="apple-touch-icon">

  <!-- Fonts -->
  <link href="https://fonts.googleapis.com" rel="preconnect">
  <link href="https://fonts.gstatic.com" rel="preconnect" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@0,100;0,300;0,400;0,500;0,700;0,900;1,100;1,300;1,400;1,500;1,700;1,900&family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&family=Raleway:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet">

  <!-- Vendor CSS Files -->
  <link href="assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
  <link href="assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">
  <link href="assets/vendor/aos/aos.css" rel="stylesheet">
  <link href="assets/vendor/glightbox/css/glightbox.min.css" rel="stylesheet">
  <link href="assets/vendor/swiper/swiper-bundle.min.css" rel="stylesheet">

  <!-- Main CSS File -->
  <link href="assets/css/main.css" rel="stylesheet">

      <!-- Include CodeMirror CSS & Theme -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/codemirror.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/theme/dracula.min.css">

<!-- CodeMirror JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/codemirror.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/python/python.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/javascript/javascript.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/php/php.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/clike/clike.min.js"></script> <!-- For Java, C++, C# -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/xml/xml.min.js"></script> <!-- For HTML -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/css/css.min.js"></script> <!-- For CSS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/sql/sql.min.js"></script> <!-- For SQL -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/ruby/ruby.min.js"></script> <!-- For Ruby -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/r/r.min.js"></script> <!-- For R -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/swift/swift.min.js"></script> <!-- For Swift -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.0/mode/kotlin/kotlin.min.js"></script> <!-- For Kotlin -->
    <!-- Page CSS -->


  <!-- =======================================================
  * Template Name: Presento
  * Template URL: https://bootstrapmade.com/presento-bootstrap-corporate-template/
  * Updated: Aug 07 2024 with Bootstrap v5.3.3
  * Author: BootstrapMade.com
  * License: https://bootstrapmade.com/license/
  ======================================================== -->
</head>

<body class="index-page">

  <header id="header" class="header d-flex align-items-center sticky-top" style="z-index: 100;">
    <div class="container-fluid container-xl position-relative d-flex align-items-center">

      <a href="index.php" class="logo d-flex align-items-center me-auto">
        <!-- Uncomment the line below if you also wish to use an image logo -->
      <img src="assets/img/favicon.png" alt="">
       <!-- <h1 class="sitename">Presento</h1> 
        <span>.</span>-->
      </a>

      <nav id="navmenu" class="navmenu">
      <ul><form action="search.php" method="get">
      <li class="d-lg-block d-md-none w-100"><div class="input-group">
      <button class="btn-get-started" type="button" id="label-button"><i class="bi bi-search"></i> Courses</button>
      <input type="text" class="form-control" name="term" placeholder="Search Course..." aria-label="Search" aria-describedby="label-button">
      </div></li></form>
          <li><a href="index.php" class="active">Home<br></a></li>
          <li><a href="courses.php">All Courses</a></li>
          <li><a href="game.php">Learn With Game</a></li>
          <li><a href="index.php#faq">About Us</a></li>
          <li><a href="contact.php">Contact</a></li>
          <li><a href="leaderboard.php">Leaderboard</a></li>
          <?php if($active_log==1){ ?>
          <li class="dropdown"><a href="#"><span>My Account</span> <i class="bi bi-chevron-down toggle-dropdown"></i></a>
            <ul>
              <li><a href="dashboard.php">Dashboard</a></li>
              <li><a href="mycourses.php">Enrolled Courses</a></li>
              <li><a href="saved-courses.php">Saved Courses</a></li>
            </ul>
          </li>
          <?php } ?>
          <li><div class="language-switcher">
            <img src="https://upload.wikimedia.org/wikipedia/en/a/ae/Flag_of_the_United_Kingdom.svg" alt="UK" class="flag">
          </div></li>
        </ul>
        <i class="mobile-nav-toggle d-xl-none bi bi-list"></i>
      </nav>
      <?php if($active_log==0){ ?><a class="btn-getstarted" href="signin.php">Sign In</a><?php } else { ?>
      <a class="btn-getstarted" href="logout.php">Logout</a><?php } ?>

    </div>
  </header>

  <main class="main">