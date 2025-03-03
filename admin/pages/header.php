<?php include "../../backend/connect.php"; 

error_reporting(E_ALL); ini_set('display_errors', 1); ini_set('log_errors', 1);
$_SESSION['previous_page'] = "http://" . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
$previousPage=$_SESSION['previous_page'];
$current_page = urlencode(basename($_SERVER['PHP_SELF']) . '?' . $_SERVER['QUERY_STRING']);
 
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
        $name = $row['name'];
        $email = $row['email'];
        $password = $row['password'];
        $type = $row['type'];
        $reward_points = $row['reward_points'];
        $created_date = $row['created_date'];
        $last_login = $row['last_login'];
        $email_verify = $row['email_verify'];
        $status = $row['status'];
        $profile_picture = !empty($row['profile_picture']) ? $row['profile_picture'] : 'user.png';
        

        
        $active_log = 1;
        $user_id=$id;
        $user_reg_date=formatDateTime($created_date);
        $user_lastseen=formatDateTime($last_login);

}}

//if($active_log==0){header("location: ../index.php");}
include "actions.php"; ?>


<!DOCTYPE html>
<html
  lang="en"
  class="light-style layout-menu-fixed"
  dir="ltr"
  data-theme="theme-default"
  data-assets-path="../assets/"
  data-template="vertical-menu-template-free"
>
  <head>
    <meta charset="utf-8" />
    <meta
      name="viewport"
      content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0"
    />

    <title>Administration | <?php echo $sitename; ?></title>

    <meta name="description" content="" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../../assets/img/<?php echo $siteimg; ?>" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap" rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <link rel="stylesheet" href="../assets/vendor/libs/apex-charts/apex-charts.css" />

    <!-- Page CSS -->
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

    <!-- Helpers -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet">

    <!-- jQuery (ensure only one version) -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.2.1/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/2.2.1/js/dataTables.min.js"></script>
<script src="https://cdn.datatables.net/2.2.1/js/dataTables.bootstrap5.min.js"></script>

<script src="../assets/vendor/js/helpers.js"></script>
<script src="../assets/js/config.js"></script>
<!-- Place the first <script> tag in your HTML's <head> -->
<script src="https://cdn.tiny.cloud/1/lxphyils3mh06lqfkntl7w5kgljaoegwzfnylpr6m9g3ids6/tinymce/7/tinymce.min.js" referrerpolicy="origin"></script>

<!-- Place the following <script> and <textarea> tags your HTML's <body> -->
<script>
  tinymce.init({
    selector: '.editor',
    plugins: [
      'anchor', 'autolink', 'charmap', 'codesample', 'emoticons', 'image', 'link', 'lists', 'media', 'searchreplace', 'table', 'visualblocks', 'wordcount',
      'checklist', 'mediaembed', 'casechange', 'export', 'formatpainter', 'pageembed', 'a11ychecker', 'tinymcespellchecker', 'permanentpen', 'powerpaste', 'advtable', 'advcode', 'editimage', 'advtemplate', 'ai', 'mentions', 'tinycomments', 'tableofcontents', 'footnotes', 'mergetags', 'autocorrect', 'typography', 'inlinecss', 'markdown','importword', 'exportword', 'exportpdf'
    ],
    toolbar: 'undo redo | blocks fontfamily fontsize | bold italic underline strikethrough | link image media table mergetags | addcomment showcomments | spellcheckdialog a11ycheck typography | align lineheight | checklist numlist bullist indent outdent | emoticons charmap | removeformat',
    tinycomments_mode: 'embedded',
    tinycomments_author: 'Author name',
    mergetags_list: [
      { value: 'First.Name', title: 'First Name' },
      { value: 'Email', title: 'Email' },
    ],
    ai_request: (request, respondWith) => respondWith.string(() => Promise.reject('See docs to implement AI Assistant')),
  });
</script>
  </head>

  <body>
    <!-- Layout wrapper -->
    <div class="layout-wrapper layout-content-navbar">
      <div class="layout-container">
        <!-- Menu -->

        <aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme">
          <div class="app-brand demo">
            <a href="index.php" class="app-brand-link">
              <img src="../../assets/img/favicon.png" alt="">
            </a>

            <a href="javascript:void(0);" class="layout-menu-toggle menu-link text-large ms-auto d-block d-xl-none">
              <i class="bx bx-chevron-left bx-sm align-middle"></i>
            </a>
          </div>

          <div class="menu-inner-shadow"></div>

          <ul class="menu-inner py-1">
            <!-- Dashboard -->
            <li class="menu-item active">
              <a href="index.php" class="menu-link">
                <i class="menu-icon tf-icons bx bx-home-circle"></i>
                <div data-i18n="Analytics">Dashboard</div>
              </a>
            </li>

            <li class="menu-header small text-uppercase">
              <span class="menu-header-text">Features</span>
            </li>
            <!-- Courses -->
            <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-folder-open"></i>
                <div data-i18n="Layouts">Courses</div>
              </a>

              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="add-course.php" class="menu-link">
                    <div data-i18n="Without menu">Add New</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="courses.php" class="menu-link">
                    <div data-i18n="Without navbar">All Courses</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="pending-courses.php" class="menu-link">
                    <div data-i18n="Container">Pending Courses</div>
                  </a>
                </li>
              </ul>
            </li>

             <!-- Courses -->
             <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-notepad"></i>
                <div data-i18n="Layouts">Quiz Module</div>
              </a>

              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="add-quiz.php" class="menu-link">
                    <div data-i18n="Without menu">Add New</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="quizzes.php" class="menu-link">
                    <div data-i18n="Without navbar">All Quizzes</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="quiz-analytics.php" class="menu-link">
                    <div data-i18n="Container">Analytics & Submissions</div>
                  </a>
                </li>
              </ul>
            </li>

              <!-- Courses
              <li class="menu-item">
              <a href="javascript:void(0);" class="menu-link menu-toggle">
                <i class="menu-icon tf-icons bx bx-game"></i>
                <div data-i18n="Layouts">Gaming Module</div>
              </a>

              <ul class="menu-sub">
                <li class="menu-item">
                  <a href="add-game.php" class="menu-link">
                    <div data-i18n="Without menu">Add New Task</div>
                  </a>
                </li>
                <li class="menu-item">
                  <a href="games.php" class="menu-link">
                    <div data-i18n="Without navbar">All Tasks</div>
                  </a>
                </li>
              </ul>
            </li> -->

            <li class="menu-item"><a href="rewards.php" class="menu-link"><i class="menu-icon tf-icons bx bxs-bar-chart-alt-2"></i><div data-i18n="Spinners">Rewards Leaderboard</div></a></li>
            
            <?php if ($type == 'admin') { ?>
            <!-- Components -->           
            <li class="menu-header small text-uppercase"><span class="menu-header-text">Users</span></li>
            <li class="menu-item"><a href="adduser.php" class="menu-link"><i class="menu-icon tf-icons bx bx-user-plus"></i><div data-i18n="Spinners">Add New User</div></a></li>
            <li class="menu-item"> <a href="users.php" class="menu-link"><i class="menu-icon tf-icons bx bxs-user-account"></i> <div data-i18n="Spinners">All Users</div></a></li>
            <li class="menu-item"> <a href="send-message.php" class="menu-link"><i class="menu-icon tf-icons bx bx-mail-send"></i> <div data-i18n="Spinners">Send Message</div></a></li>

        
            <!-- Misc -->
            <li class="menu-header small text-uppercase"><span class="menu-header-text">ADMIN</span></li>
            <li class="menu-item"> <a href="notifications.php" class="menu-link"><i class="menu-icon tf-icons bx bx-bell"></i> <div data-i18n="Spinners">Notifications</div></a></li>
            <li class="menu-item"> <a href="settings.php" class="menu-link"><i class="menu-icon tf-icons bx bx-cog"></i> <div data-i18n="Spinners">Settings</div></a></li>
            <li class="menu-item"> <a href="logout.php" class="menu-link"><i class="menu-icon tf-icons bx bx-log-out"></i> <div data-i18n="Spinners">Log Out</div></a></li>
            <?php } ?>
        </aside>
        <!-- / Menu -->





        <!-- Layout container -->
        <div class="layout-page">
          <!-- Navbar -->
          <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme" id="layout-navbar">
            <div class="layout-menu-toggle navbar-nav align-items-xl-center me-3 me-xl-0 d-xl-none">
              <a class="nav-item nav-link px-0 me-xl-4" href="javascript:void(0)">
                <i class="bx bx-menu bx-sm"></i>
              </a>
            </div>

            <div class="navbar-nav-right d-flex align-items-center" id="navbar-collapse">
                <!-- Search -->
                <div class="navbar-nav align-items-center">
                <div class="nav-item d-flex align-items-center">
                  <i class="bx bx-search fs-4 lh-0"></i>
                  <input
                  type="text" 
                  id="searchInput"
                  class="form-control border-0 shadow-none"
                  placeholder="Search..."
                  aria-label="Search..."
                  />
                </div>
                </div>
                <!-- /Search -->


              <ul class="navbar-nav flex-row align-items-center ms-auto">
                <?php if ($type == 'admin') { ?>
              <li class="nav-item lh-1 me-3">
                  <?php if(isset($notification_count) && $notification_count > 0): ?>
                    <a href="notifications.php" class="position-relative">
                      <i class="bx bx-bell fs-4"></i>
                      <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        <?php echo $notification_count; ?>
                      </span>
                    </a>
                  <?php else: ?>
                    <a href="notifications.php">
                      <i class="bx bx-bell fs-4"></i>
                    </a>
                  <?php endif; ?>
                </li><?php } ?> 

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown">
                    <div class="avatar avatar-online">
                      <img src="../../uploads/<?php echo htmlspecialchars($profile_picture); ?>" alt class="w-px-40 h-auto rounded-circle" />
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="#">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-online">
                              <img src="../../uploads/<?php echo htmlspecialchars($profile_picture); ?>" alt class="w-px-40 h-auto rounded-circle" />
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <span class="fw-semibold d-block"><?php echo $name; ?></span>
                            <small class="text-muted"><?php echo $name; ?></small>
                          </div>
                        </div>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="<?php echo $siteurl; ?>" target="_blank">
                        <i class="bx bx-log-in me-2"></i>
                        <span class="align-middle">Visit Site</span>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="profile.php">
                        <i class="bx bx-user me-2"></i>
                        <span class="align-middle">My Profile</span>
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="settings.php">
                        <i class="bx bx-cog me-2"></i>
                        <span class="align-middle">Settings</span>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="logout.php">
                        <i class="bx bx-power-off me-2"></i>
                        <span class="align-middle">Log Out</span>
                      </a>
                    </li>
                  </ul>
                </li>
                <!--/ User -->
              </ul>
            </div>
          </nav> <!-- / Navbar -->

          <!-- Content wrapper -->
          <div class="content-wrapper">
            <!-- Content -->