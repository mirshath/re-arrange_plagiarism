<?php
include '../../database/connection.php';

$module_id = $_GET['module_id'];

$query = "SELECT deadline FROM module_table WHERE id = $module_id";
$result = mysqli_query($conn, $query);

$deadline = mysqli_fetch_assoc($result);
echo json_encode($deadline);
