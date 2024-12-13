<?php
include('../database/connection.php');

$universityId = $_POST['university_id'];

$query = "SELECT * FROM program_table WHERE university_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $universityId);
$stmt->execute();
$result = $stmt->get_result();

$programs = [];
while ($row = $result->fetch_assoc()) {
    $programs[] = $row;
}

echo json_encode($programs);
?>
