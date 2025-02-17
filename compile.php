<?php

error_reporting(E_ALL);
ini_set('display_errors', 0); // Turn off direct output of errors
ini_set('log_errors', 1);

header('Content-Type: application/json'); // Ensure JSON response

try {
    // Get JSON data from request body
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        throw new Exception('Invalid request data');
    }

    // Process the data
    $completed_code = $data['code'] ?? '';
    $language = $data['language'] ?? '';
    $expected = $data['expected'] ?? '';

    if (empty($completed_code) || empty($language)) {
        throw new Exception('Missing code or language');
    }

    
    try{
        switch ($language) {
            case 'PHP':
                // Execute PHP code
                ob_start();
                eval($completed_code);
                $output = ob_get_clean(); // Capture output
                break;
        
            case 'Wordpress':
                // WordPress uses PHP, so treat it the same as PHP
                ob_start();
                eval($completed_code);
                $output = ob_get_clean();
                break;
        
            case 'HTML':
                // HTML is static and doesn't need execution
                $output = $completed_code;
                break;
        
            case 'CSS':
                // CSS is static and doesn't need execution
                $output = $completed_code;
                break;
        
            case 'Bootstrap':
                // Bootstrap is CSS-based, so treat it the same as CSS
                $output = $completed_code;
                break;
        
            case 'javascript':
                // Execute JavaScript using Node.js
                $escaped_code = escapeshellarg($completed_code);
                $output = shell_exec("node -e " . $escaped_code);
                break;
        
            case 'Python':
                // Execute Python code
                $output = shell_exec("python3 -c '$completed_code'");
                break;
        
            case 'Java':
                // Execute Java code
                $tempFile = tempnam(sys_get_temp_dir(), 'java');
                file_put_contents($tempFile . '.java', $completed_code);
                $output = shell_exec("javac $tempFile.java && java -cp " . dirname($tempFile) . " " . basename($tempFile));
                unlink($tempFile . '.java');
                unlink($tempFile . '.class');
                break;
        
            case 'C++':
                // Execute C++ code
                $tempFile = tempnam(sys_get_temp_dir(), 'cpp');
                file_put_contents($tempFile . '.cpp', $completed_code);
                $output = shell_exec("g++ $tempFile.cpp -o $tempFile && $tempFile");
                unlink($tempFile . '.cpp');
                unlink($tempFile);
                break;

            case 'React JS':
                // React JS uses JavaScript, so treat it the same as JavaScript
                $escaped_code = escapeshellarg($completed_code);
                $output = shell_exec("node -e " . $escaped_code);
                break;
                break;
        
            case 'Laravel':
                // Laravel uses PHP, so treat it the same as PHP
                ob_start();
                eval($completed_code);
                $output = ob_get_clean();
                break;
        
            case 'Swift':
                // Execute Swift code
                $tempFile = tempnam(sys_get_temp_dir(), 'swift');
                file_put_contents($tempFile . '.swift', $completed_code);
                $output = shell_exec("swift $tempFile.swift");
                unlink($tempFile . '.swift');
                break;
        
            case 'Kotlin':
                // Execute Kotlin code
                $tempFile = tempnam(sys_get_temp_dir(), 'kt');
                file_put_contents($tempFile . '.kt', $completed_code);
                $output = shell_exec("kotlinc $tempFile.kt -include-runtime -d $tempFile.jar && java -jar $tempFile.jar");
                unlink($tempFile . '.kt');
                unlink($tempFile . '.jar');
                break;
        
            case 'SQL':
                               
                                $result = $con->query($completed_code);
                                if ($result) {
                                    $output = print_r($result->fetch_all(MYSQLI_ASSOC), true);
                                } else {
                                    $output = $conn->error;
                                }
                                $con->close();
                                break;
        
            case 'Ruby':
                // Execute Ruby code
                $output = shell_exec("ruby -e '$completed_code'");
                break;
        
            case 'R':
                // Execute R code
                $output = shell_exec("Rscript -e '$completed_code'");
                break;
        
            case 'C#':
                // Execute C# code
                $tempFile = tempnam(sys_get_temp_dir(), 'cs');
                file_put_contents($tempFile . '.cs', $completed_code);
                $output = shell_exec("csc $tempFile.cs && $tempFile.exe");
                unlink($tempFile . '.cs');
                unlink($tempFile . '.exe');
                break;
        
            default:
                $output = "Unsupported language: $language";
    }} catch (Exception $e) {
        echo json_encode(['error' => 'Execution error: ' . $e->getMessage()]);
        exit;
    }
    
    echo json_encode(['success' => true, 'output' => $output, 'expected' => $expected]);

} catch (Exception $e) {
    http_response_code(400); // Set error status code
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
?>

