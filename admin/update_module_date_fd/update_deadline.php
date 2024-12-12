<?php
include '../../database/connection.php';

$module_id = $_POST['module_id'];
$new_deadline = $_POST['deadline'];

$query = "UPDATE module_table SET deadline = '$new_deadline' WHERE id = $module_id";

if (mysqli_query($conn, $query)) {
    echo "Deadline updated successfully.";
} else {
    echo "Error updating deadline: " . mysqli_error($conn);
}
?>
