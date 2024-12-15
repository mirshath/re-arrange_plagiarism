<?php
// Start session if needed
session_start();

// Include the database connection
include("../database/connection.php");

// Ensure the user is logged in and has the correct role
// if ($_SESSION['role'] !== 'super_admin') {
//     echo '<script>window.location.href = "../login.php";</script>';
//     exit();
// }


// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    // Redirect to login page if not logged in or role is incorrect
    echo '<script>window.location.href = "../login";</script>';
    exit();
}



// Get the file name from the GET request
if (isset($_GET['file']) && !empty($_GET['file'])) {
    $file_name = basename($_GET['file']);  // Extract file name from the request to prevent path traversal
    $file_path = "../uploads/documents/" . $file_name;  // Specify the path where your documents are stored

    // Ensure the file exists before proceeding
    if (file_exists($file_path)) {
        // Set headers to force the file download
        header("Content-Type: application/octet-stream");
        header("Content-Disposition: attachment; filename=\"" . $file_name . "\"");
        header("Content-Length: " . filesize($file_path));
        header("Cache-Control: no-cache, no-store, must-revalidate");
        header("Pragma: no-cache");
        header("Expires: 0");

        // Clear output buffer and read the file
        ob_clean();
        flush();
        readfile($file_path);
        exit;
    } else {
        // If the file doesn't exist, display an error
        die("Error: File not found.");
    }
} else {
    // If no file parameter is provided in the URL
    die("Error: No file specified.");
}
