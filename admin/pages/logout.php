<?php session_start();

$_SESSION = array();

if (isset($_COOKIE['userID'])) {
    setcookie("userID", "", time()-3600);
}

unset($_SESSION['id']);
unset($_SESSION['email']);
unset($_SESSION['password']);
unset($_SESSION['name']);

$message = "Logged Out Successfully";


session_destroy();

if($_COOKIE['userID']){
header("Location: ../");
} else {
print "<script>alert('Could not log you out, sorry the system encountered an error.');</script>";
header("refresh:0; url=index.php");
exit();
} 

?> 

