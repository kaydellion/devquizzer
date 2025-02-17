<?php session_start(); ob_start();

error_reporting(E_ALL); ini_set('display_errors', 1); ini_set('log_errors', 1);
$db_host = "localhost"; 
$db_username = "root"; 
$db_pass = ""; 
$db_name = "devquizzer";
$con = mysqli_connect ("$db_host","$db_username","$db_pass","$db_name");
$siteprefix="dv_";
date_default_timezone_set('Africa/Lagos');
$currentdate=date("Y-m-d");
$currentdatetime=date("Y-m-d H:i:s");


$sql = "SELECT * from ".$siteprefix."site_settings";
$sql2 = mysqli_query($con,$sql);
while($row = mysqli_fetch_array($sql2)){
$sitemail = $row["site_mail"];
$sitenumber = $row["site_number"];
$sitename = $row["site_name"]; 
$siteimg= $row["site_logo"];
$siteurl= $row["site_url"];
$sitedescription= $row["site_description"];
$sitekeywords= $row["site_keywords"];} 
$adminlink=$siteurl.'/admin';


include "functions.php"; 
?>