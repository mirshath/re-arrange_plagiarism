<?php
include('../database/connection.php');// Make sure this file contains your database connection code

$programId = $_POST['program_id'];

// Prepare the query to fetch batches based on the program ID
$query = "SELECT * FROM batch_table WHERE programme  = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $programId);
$stmt->execute();
$result = $stmt->get_result();

$batches = [];
while ($row = $result->fetch_assoc()) {
    $batches[] = $row;
}

echo json_encode($batches);
?>
