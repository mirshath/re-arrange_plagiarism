<?php
include("../../database/connection.php");

// Get the data from the request
$moduleId = $_POST['module_id'];
$newDeadline = $_POST['new_deadline'];

// Update the deadline in the database
$query = "UPDATE module_table SET deadline = ? WHERE id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("si", $newDeadline, $moduleId);

if ($stmt->execute()) {
    echo 'Deadline updated successfully.';
} else {
    echo 'Error updating deadline.';
}

$stmt->close();
$conn->close();
