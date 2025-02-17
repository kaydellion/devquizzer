<?php

include "../backend/connect.php"; 

//login user
if (isset($_POST['login'])){
    $code= $_POST['username'];
    $password = $_POST['password'];
          
    $sql = "SELECT * from ".$siteprefix."users where email='$code'";
    $sql2 = mysqli_query($con,$sql);
    if (mysqli_affected_rows($con) == 0){
    echo'Invalid Email address!'; 
    }
                
    else {  
    while($row = mysqli_fetch_array($sql2)){
    $id = $row["s"];   					
    $hashedPassword = $row['password'];
    $status = $row['status'];
    $type = $row['type'];
    }

    if (!checkPassword($password, $hashedPassword)) {
    echo'Incorrect Password for this account! <a href="forgetpassword.php" style="color:red;">Forgot password? Recover here</a>';
    }
                
   else if($status == "active" && $type=="admin" || $type =="instructor"){
    $date=date('Y-m-d H:i:s');
    $insert = mysqli_query($con,"UPDATE ".$siteprefix."users SET last_login='$date' where s='$id'") or die ('Could not connect: ' .mysqli_error($con)); 
                  
    session_start(); 
    $_SESSION['id']=$id;
    setcookie("userID", $id, time() + (10 * 365 * 24 * 60 * 60));
    $message = "Logged In Successfully";
                 
                 
                 
    //redirection
    if (isset($_SESSION['previous_page'])) {
    $previousPage = $_SESSION['previous_page'];
    header("location: $previousPage");
    } else {
    header("location: pages/");
    }} 
    }}

    
    ?>