<?php

$image = imagecreatefrompng(__DIR__ . '/assets/img/certificate.png');


// Define the text color
$textColor = imagecolorallocate($image, 0, 0, 0); // Black color

// Define the font path
$fontPath = __DIR__ . '/assets/vendor/Poppins Regular 400.ttf';
$font2Path = __DIR__ . '/assets/vendor/BrushScriptOpti-Regular.otf';


// Define the text to be added
$name = $_GET['name'] ?? 'Name Surname';
$course = $_GET['course'] ?? 'PHP Programming';
$content = $_GET['content'] ?? 'Preview content goes here they offer valuable insights into user behavior and engagement.';
$date = date("F j, Y");


// Adjust the positions of the text
$nameX = 760;
$nameY = 680;
$courseX = 780;
$courseY = 800;
$contentX = 330;
$contentY = 880;
$dateX = 150;
$dateY = 300;

// Add the text to the image with new positions
imagettftext($image, 70, 0, $nameX, $nameY, $textColor, $font2Path, $name);
imagettftext($image, 40, 0, $courseX, $courseY, $textColor, $fontPath, $course);
imagettftext($image, 20, 0, $contentX, $contentY, $textColor, $fontPath, $content);


// Output the image
header('Content-Type: image/jpeg');
imagejpeg($image);

// Free up memory
imagedestroy($image);
?>
