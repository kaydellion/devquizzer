<?php include "header.php"; ?>

<div class="container-xxl flex-grow-1 container-p-y">
    <div class="card">
        <div class="card-body">
            <form method="POST" enctype="multipart/form-data">
                <p class="text-bold text-dark">Edit Information</p>
                <div class="form-row row">

                <div>
                 <label for="profilePictureInput" class="text-light">Click to change profile picture</label>
                 <input type="file" id="profilePictureInput" name="profilePicture" accept="image/*" style="display: none;" onchange="previewProfilePicture(event)">
                 <img src="../../uploads/<?php echo htmlspecialchars($profile_picture); ?>" class="mt-3" alt="Avatar" id="profilePicturePreview" onclick="document.getElementById('profilePictureInput').click();">
                </div>
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
                <div class="form-group col-md-4 mb-3">
            <label for="password">Old Password</label>
            <div class="input-group">
            <input type="password" class="form-control" id="oldpassword" name="oldpassword" placeholder="Password">
            <div class="input-group-append">
                <span class="input-group-text" onclick="togglePasswordVisibility('oldpassword')">
                <i class="bx bx-low-vision" id="togglePasswordIcon"></i>
                </span>
            </div>
            </div>
        </div>
        <div class="form-group col-md-4 mb-3">
            <label for="password">New Password</label>
            <div class="input-group">
            <input type="password" class="form-control" id="password" name="password" placeholder="Password">
            <div class="input-group-append">
                <span class="input-group-text" onclick="togglePasswordVisibility('password')">
                <i class="bx bx-low-vision" id="togglePasswordIcon"></i>
                </span>
            </div>
            </div>
        </div>
        <div class="form-group col-md-4 mb-3">
            <label for="retypePassword">Retype Password</label>
            <div class="input-group">
            <input type="password" class="form-control" id="retypePassword" name="retypePassword" placeholder="Password">
            <div class="input-group-append">
                <span class="input-group-text" onclick="togglePasswordVisibility('retypePassword')">
                <i class="bx bx-low-vision" id="toggleRetypePasswordIcon"></i>
                </span>
            </div>
            </div>
        </div>
                   
                <p><button class="w-100 btn btn-primary" name="update-profile" value="update-user">Update Account</button></p>
                <input type="hidden" name="userid" value="<?php echo htmlspecialchars($user_id, ENT_QUOTES, 'UTF-8'); ?>">
            </form>
        </div>
    </div>
</div>

<?php include "footer.php"; ?>
