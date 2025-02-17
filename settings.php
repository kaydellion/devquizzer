<?php include 'header.php'; ?>
<main class="main">

<section>
<form method="POST" enctype="multipart/form-data">
<div class="row bg-dark p-5">
    <div class="col-lg-2 col-12">
        <img src="uploads/<?php echo htmlspecialchars($profile_picture); ?>" alt="Avatar" class="img-fluid rounded-circle">
    </div>
    <div class="col-lg-10 col-12 d-flex align-items-center pt-3 mb-5">
        <div class="d-flex flex-column w-100">
                <div class="d-flex">
                <?php include "links.php"; ?>
                </div>
                <h2 class="title text-primary text-bold mt-3 mb-5">Hi, <?php echo htmlspecialchars($name); ?></h2>
 
        <label for="profilePictureInput" class="text-light">Click to change profile picture</label>
        <input type="file" id="profilePictureInput" name="profilePicture" accept="image/*" style="display: none;" onchange="previewProfilePicture(event)">
        <img src="uploads/<?php echo htmlspecialchars($profile_picture); ?>" class="mt-3" alt="Avatar" id="profilePicturePreview" onclick="document.getElementById('profilePictureInput').click();">
        </div>
    </div> 
</div>

<div class="row p-5">
<div class="col-lg-7 col-12">
<div class="form-row row">
        <div class="form-group col-md-6 mb-3">
            <label for="fullName">Full Name</label>
            <input type="text" class="form-control" id="fullName" name="fullName" value="<?php echo htmlspecialchars($name); ?>" required>
        </div>
        <div class="form-group col-md-6 mb-3">
            <label for="email">Email</label>
            <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email); ?>" placeholder="Email" readonly required>
        </div>
        </div>
        <div class="form-row row">
        <div class="form-group col-md-4 mb-3">
            <label for="password">Old Password</label>
            <div class="input-group">
            <input type="password" class="form-control" id="oldpassword" name="oldpassword" placeholder="Password">
            <div class="input-group-append">
                <span class="input-group-text" onclick="togglePasswordVisibility('oldpassword')">
                <i class="bi bi-eye" id="togglePasswordIcon"></i>
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
                <i class="bi bi-eye" id="togglePasswordIcon"></i>
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
                <i class="bi bi-eye" id="toggleRetypePasswordIcon"></i>
                </span>
            </div>
            </div>
        </div>
        </div>
        <div class="d-flex justify-space-between">
        <div class="radio-container m-1">
        <input class="form-check-input" type="radio" id="option1" name="options" value="Theory and Code" <?php echo ($preference == 'Theory and Code') ? 'checked' : ''; ?> required>
        <label for="option1">Theory and Code</label>
        </div>
        <div class="radio-container m-1">
        <input class="form-check-input" type="radio" id="option2" name="options" value="Theory" <?php echo ($preference == 'Theory') ? 'checked' : ''; ?> required>
        <label for="option2">Theory</label>
        </div>
        <div class="radio-container m-1">
        <input class="form-check-input" type="radio" id="option3" name="options" value="Code" <?php echo ($preference == 'Code') ? 'checked' : ''; ?> required>
        <label for="option3">Code</label>
        </div>
    </div>   
    <p class="mt-3"><button class="w-100 btn-get-started" name="update-profile" value="register-user">Update Account</button></p>   
</div>
</div>


</form>
</section>
</main>
<?php include 'footer.php'; ?>