<?php
// Database configuration
$host = 'localhost'; // Database server
$username = 'root';  // Database username
$password = '';      // Database password (default is empty for localhost)
$database = 're_arrange_plagiarism'; // Your database name


// Create a connection
$conn = new mysqli($host, $username, $password, $database);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
// echo "Connected successfully";

?>
