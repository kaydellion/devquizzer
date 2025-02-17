<?php

//register user
if(isset($_POST['register'])){
    $fullName = $_POST['fullName'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $retypePassword = $_POST['retypePassword'];
    $options = $_POST['options'];
    
    
     //status
     $status='active';
     $date=date('Y-m-d H:i:s');

//error for same email,password errors
$checkEmail = mysqli_query($con, "SELECT * FROM ".$siteprefix."users WHERE email='$email'");
if(mysqli_num_rows($checkEmail) >= 1 ) {
$statusAction="Ooops!";
$statusMessage="This email has already been registered. Please try registering another email.";
showErrorModal($statusAction, $statusMessage); } 					
else if (strlen($password) < 6){
    $statusAction="Try Again";
    $statusMessage="Password must have 8 or more characters";
    showErrorModal($statusAction, $statusMessage);
}										
else if ($password !== $retypePassword ){
    $statusAction="Ooops!";
    $statusMessage="Password do not match!";
    showErrorModal($statusAction, $statusMessage);
}
else {
    $password=hashPassword($password);
    
    $submit = mysqli_query($con, " INSERT INTO `".$siteprefix."users` (`name`, `email`, `password`, `type`, `reward_points`, `created_date`, `last_login`, `email_verify`, `status`,`profile_picture`,`preference`)
     VALUES ('$fullName', '$email', '$password', 'user', 0, '$date', '$date', 1, '$status','','$options')")
    or die('Could not connect: ' . mysqli_error($con));
    $user_id = mysqli_insert_id($con);
    

    $emailSubject="Registration Successful";
    $emailMessage="<p>Thank you for registering on our website. Your registration is now complete. 
    You can now log in using your email and password.</p>";
    $adminmessage = "A new user has been registered($fullName)";
    $link="users.php";
    $msgtype='New User';
    $message_status=0;
    $emailMessage_admin="<p>Hello Dear Admin,a new user has been successfully registered!</p>";
    $emailSubject_admin="New User Registeration";
    insertadminAlert($con, $adminmessage, $link, $date, $msgtype, $message_status); 
    //sendEmail($email, $name, $siteName, $siteMail, $emailMessage, $emailSubject);
    //sendEmail($siteMail, $adminName, $siteName, $siteMail, $emailMessage_admin, $emailSubject_admin);
    echo header("location:signin.php?user_login=$user_id");	
    }
}




if(isset($_POST['contact'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    
    $emailMessage = "From: " . $name . "\nEmail: " . $email . "\nMessage:\n" . $message;
    
    if(sendEmail($sitemail, $sitename, $sitename, $sitemail, $emailMessage, $subject)) {
        $message='Message sent successfully. We will get back to you soon.';
        showSuccessModal('Success', $message);
    } else {
        $message='Failed to send message';
        showErrorModal('Error', $message);
    }
}


if(isset($_POST['update-profile'])){
    $fullName = htmlspecialchars($_POST['fullName']);
    $email = htmlspecialchars($_POST['email']);
    $password = !empty($_POST['password']) ? password_hash($_POST['password'], PASSWORD_DEFAULT) : null;
    $retypePassword = !empty($_POST['retypePassword']) ? $_POST['retypePassword'] : null;
    $oldPassword = htmlspecialchars($_POST['oldpassword']);
    $options = htmlspecialchars($_POST['options']);
    $profilePicture = $_FILES['profilePicture']['name'];

    // Validate passwords match
    if ($password && $password !== $retypePassword) {
        $message= "Passwords do not match.";
    }

    // Validate old password
    $stmt = $con->prepare("SELECT password FROM ".$siteprefix."users WHERE s = ?");
    if ($stmt === false) {
        $message = "Error preparing statement: " . $con->error;
    } else {
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();
        if ($user === null || !checkPassword($oldPassword, $user['password'])) {
            $message = "Old password is incorrect.";
        }
    }

    $uploadDir = 'uploads/';
    $fileKey='profilePicture';
    global $fileName;

    // Update profile picture if a new one is uploaded
    if (!empty($profilePicture)) {
        $profilePicture = handleFileUpload($fileKey, $uploadDir, $fileName);
    } else {
        $profilePicture = $profile_picture; // Use the current profile picture if no new one is uploaded
    }

    // Update user information in the database
    $query = "UPDATE ".$siteprefix."users SET name = ?, email = ?, preference = ?, profile_picture = ?";
    $params = [$fullName, $email, $options, $profilePicture];

    if ($password) {
        $query .= ", password = ?";
        $params[] = $password;
    }

    $query .= " WHERE s = ?";
    $params[] = $user_id;

    $stmt = $con->prepare($query);
    $stmt->bind_param(str_repeat('s', count($params)), ...$params);
    if ($stmt->execute()) {
        $message= "Profile updated successfully.";
    } else {
        $message= "Error updating profile.";
    }
    showToast($message); 
    echo "<meta http-equiv='refresh' content='2'>";
}





//login user
if (isset( $_POST['login'])){
    $code= $_POST['email'];
    $password = $_POST['password'];
          
    $sql = "SELECT * from ".$siteprefix."users where email='$code'";
    $sql2 = mysqli_query($con,$sql);
    if (mysqli_affected_rows($con) == 0){
    $statusAction="Try Again!";
    $statusMessage='Invalid Email address!';
    showErrorModal($statusAction, $statusMessage);  
    }
                
    else {  
    while($row = mysqli_fetch_array($sql2)){
    $id = $row["s"]; 
    $hashedPassword = $row['password'];
    $status = $row['status'];
    $type = $row['type'];
    }
     
    if($type!='user'){
        $statusAction="Ooops!";
        $statusMessage='Invalid Credentials!';
        showErrorModal($statusAction, $statusMessage);  
    }

     else if (!checkPassword($password, $hashedPassword)) {
     $statusAction="Ooops!";
     $statusMessage='Incorrect Password for this account! <a href="forgetpassword.php" style="color:red;">Forgot password? Recover here</a>';
     showErrorModal($statusAction, $statusMessage);  
    }
     
    
    else if($verify == "0"){
        $statusAction="Ooops!";
        $statusMessage=' Email Address have not been verified. we have sent you a mail which contains verification link. kindly check your email and verify your email address.';
        showErrorModal($statusAction, $statusMessage);  
    }
    
    else if($status == "active"){
    $date=date('Y-m-d H:i:s');
    $insert = mysqli_query($con,"UPDATE ".$siteprefix."users SET last_login='$date' where s='$id'") or die ('Could not connect: ' .mysqli_error($con)); 
                  
    session_start(); 
    $_SESSION['id']=$id;
    setcookie("userID", $id, time() + (10 * 365 * 24 * 60 * 60));
    $message = "Logged In Successfully";
                 
                 
    showToast($message);          
    //redirection
    if (isset($_SESSION['previous_page'])) {
      $previousPage = $_SESSION['previous_page'];
      header("location: $previousPage");
    } else {
      header("location: dashboard.php");
    }} 
    }}



?>