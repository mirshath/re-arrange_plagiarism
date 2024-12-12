<?php
include '../../database/connection.php';

$program_id = $_GET['program_id'];

$query = "SELECT * FROM module_table WHERE program_id = $program_id";
$result = mysqli_query($conn, $query);

$modules = [];
while ($row = mysqli_fetch_assoc($result)) {
    $modules[] = $row;
}

echo json_encode($modules);
?>
