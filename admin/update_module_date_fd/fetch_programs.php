<?php
include '../../database/connection.php';

$query = "SELECT * FROM program_table";
$result = mysqli_query($conn, $query);

$programs = [];
while ($row = mysqli_fetch_assoc($result)) {
    $programs[] = $row;
}

echo json_encode($programs);
?>
