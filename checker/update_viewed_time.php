<?php
session_start();
include("../database/connection.php");

// Update the condition to check for module_id
if (isset($_POST['student_id']) && isset($_POST['checker_id']) && isset($_POST['module_id'])) {
    $student_id = $_POST['student_id'];
    $checker_id = $_POST['checker_id'];
    $module_id = $_POST['module_id'];

    date_default_timezone_set('Asia/Colombo');
    $current_date = date('Y-m-d H:i:s');

    $sql = "UPDATE student_submitted_form 
            SET checker_downlaoded_at = ? 
            WHERE student_id = ? 
            AND checker_id = ? 
            AND module_id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("ssss", $current_date, $student_id, $checker_id, $module_id);

        if ($stmt->execute()) {
            echo 'success';
        } else {
            echo 'SQL Error: ' . $stmt->error;
        }

        $stmt->close();
    } else {
        echo 'Query preparation failed: ' . $conn->error;
    }
} else {
    echo 'invalid_request';
}

$conn->close();
