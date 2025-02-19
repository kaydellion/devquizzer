<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Fetch POST data
    $name = $_POST['name'] ?? 'Name Surname';
    $course = $_POST['course'] ?? 'PHP Programming';
    $content = $_POST['content'] ?? 'Your custom message here.';
    $date = date("F j, Y");

    // Load the certificate template
    $image = imagecreatefrompng(__DIR__ . '/assets/img/certificate.png');

    // Define the text color
    $textColor = imagecolorallocate($image, 0, 0, 0); // Black

    // Define font paths
    $fontPath = __DIR__ . '/assets/vendor/Poppins Regular 400.ttf';
    $font2Path = __DIR__ . '/assets/vendor/BrushScriptOpti-Regular.otf';

    // Add text to the image
    imagettftext($image, 70, 0, 760, 680, $textColor, $font2Path, $name);
    imagettftext($image, 40, 0, 780, 800, $textColor, $fontPath, $course);
    
    // Word wrap content
    $maxWidth = 1000;
    $lines = explode("\n", wordwrap($content, 100, "\n"));
    $y = 880;
    foreach ($lines as $line) {
        // Get the size of the text box
        $bbox = imagettfbbox(20, 0, $fontPath, $line);
        // Calculate x position to center the text
        $x = (imagesx($image) - ($bbox[2] - $bbox[0])) / 2;
        imagettftext($image, 20, 0, $x, $y, $textColor, $fontPath, $line);
        $y += 30;
    }

    // Set headers for download
    header('Content-Type: image/jpeg');
    header('Content-Disposition: attachment; filename="'.$name.'-certificate.jpg"');

    // Output the image
    imagejpeg($image);

    // Free memory
    imagedestroy($image);
    exit;
}
?>
