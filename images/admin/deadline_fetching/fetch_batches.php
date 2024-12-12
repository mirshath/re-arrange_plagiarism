<?php
include("../../database/connection.php");

$program_id = $_GET['program_id'];  // Get program_id from the query string

$query = "SELECT * FROM batch_table WHERE program_id = $program_id";
$result = mysqli_query($conn, $query);

$batches = [];
while ($row = mysqli_fetch_assoc($result)) {
    $batches[] = $row;
}

echo json_encode($batches);
?>
