<?php
include("../../database/connection.php");

$batch_id = $_GET['batch_id'];  // Get batch_id from the query string

$query = "SELECT * FROM module_table WHERE batch_id = $batch_id";
$result = mysqli_query($conn, $query);

$modules = [];
while ($row = mysqli_fetch_assoc($result)) {
    $modules[] = $row;
}

echo json_encode($modules);
?>
