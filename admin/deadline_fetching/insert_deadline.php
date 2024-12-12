<?php
// update_deadline.php
include("../../database/connection.php"); // Include the database connection

// Check if the necessary data is sent via POST
if (isset($_POST['program_id']) && isset($_POST['batch_id']) && isset($_POST['module_id']) && isset($_POST['deadline'])) {
    $program_id = $_POST['program_id'];
    $batch_id = $_POST['batch_id'];
    $module_id = $_POST['module_id'];
    $deadline = $_POST['deadline'];

    // Prepare the update query
    $query = "UPDATE `module_table` 
              SET `deadline` = ? 
              WHERE `program_id` = ? AND `batch_id` = ? AND `id` = ?";

    // Prepare and bind the statement to prevent SQL injection
    if ($stmt = mysqli_prepare($conn, $query)) {
        mysqli_stmt_bind_param($stmt, 'siii', $deadline, $program_id, $batch_id, $module_id);

        // Execute the query
        if (mysqli_stmt_execute($stmt)) {
            echo 'Deadline updated successfully';
        } else {
            echo 'Error updating deadline: ' . mysqli_error($conn);
        }

        // Close the statement
        mysqli_stmt_close($stmt);
    } else {
        echo 'Error preparing statement: ' . mysqli_error($conn);
    }
} else {
    echo 'Missing parameters';
}
