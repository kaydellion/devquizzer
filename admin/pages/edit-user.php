<?php include "header.php"; 

$user_id = $_GET['user'] ?? null;
if (!$user_id) {
  header("Location: users.php");
  exit();
}

$sql = "SELECT * FROM " . $siteprefix . "users WHERE s = '" .$user_id. "'";
$sql2 = mysqli_query($con, $sql);
if ($sql2 && mysqli_num_rows($sql2) > 0) {
    while ($row = mysqli_fetch_array($sql2)) {
        $userid = $row["s"];
        $name = $row['name'];
        $email = $row['email'];
        $password = $row['password'];
        $type = $row['type'];
        $reward_points = $row['reward_points'];
        $created_date = $row['created_date'];
        $last_login = $row['last_login'];
        $email_verify = $row['email_verify'];
        $status = $row['status'];

        $active_log = 1;
        $user_reg_date = formatDateTime($created_date);
        $user_lastseen = formatDateTime($last_login);
    }
} else {
    // Redirect to users page if no matching record is found
    header("Location: users.php");
    exit;
}


?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <p class="text-bold text-dark">Edit User Account</p>
                <div class="form-row row">
                    <div class="form-group col-md-6 mb-3">
                        <label for="fullName">Full Name</label>
                        <input type="text" class="form-control" id="fullName" name="fullName" value="<?php echo htmlspecialchars($name, ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="form-group col-md-6 mb-3">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                </div>
                <div class="form-row row">
                    <div class="form-group col-md-6 mb-3">
                        <label for="password">New Password (Leave blank to keep existing)</label>
                    <div class="input-group">
                   <input type="password" class="form-control" id="password" name="password" placeholder="New Password" >
                   <div class="input-group-append">
                   <span class="input-group-text p-3" onclick="togglePasswordVisibility('password')">
                   <i class="bx bx-low-vision" id="togglePasswordIcon"></i>
                   </span>
                   </div>
                    </div></div>
                    <div class="form-group col-md-6 mb-3">
                        <label for="type">User Type</label>
                        <select class="form-select p-3" name="type" id="type" required>
                            <option value="user" <?php if ($type === 'user') echo 'selected'; ?>>User</option>
                            <option value="instructor" <?php if ($type === 'instructor') echo 'selected'; ?>>Instructor</option>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label for="status">Status</label>
                    <select class="form-select" name="status" id="status" required>
                        <option value="active" <?php if ($status === 'active') echo 'selected'; ?>>Active</option>
                        <option value="inactive" <?php if ($status === 'inactive') echo 'selected'; ?>>Inactive</option>
                    </select>
                </div>
                <p><button class="w-100 btn btn-primary" name="update-user" value="update-user">Update Account</button></p>
                <input type="hidden" name="userid" value="<?php echo htmlspecialchars($userid, ENT_QUOTES, 'UTF-8'); ?>">
            </form>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
