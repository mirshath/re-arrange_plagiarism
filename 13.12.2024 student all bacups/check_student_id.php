<?php
// check_student_id.php
include('./database/connection.php'); // Include your database connection file

// Check if student_id is passed in the request
if (isset($_POST['student_id'])) {
    $student_id = $_POST['student_id'];

    // Query to check if the student ID exists and retrieve the attempt count
    $checkQuery = "SELECT student_id FROM student_submitted_form WHERE student_id = '$student_id'";
    $result = mysqli_query($conn, $checkQuery);

    // if (mysqli_num_rows($result) > 0) {
    //     $row = mysqli_fetch_assoc($result);
    //     $attempt = $row['attempt'];

    //     if ($attempt < 3) {
    //         echo 'exists'; // Student ID exists and attempts are less than 3
    //     } else {
    //         echo 'attempt_exceeded'; // Attempts have reached or exceeded the limit
    //     }
    // } else {
    //     echo 'not_exists'; // Student ID not found
    // }

    
    if (mysqli_num_rows($result) > 0) {
        echo 'exists'; // Student ID already exists
    } else {
        echo 'not_exists'; // Student ID is not found
    }
}
