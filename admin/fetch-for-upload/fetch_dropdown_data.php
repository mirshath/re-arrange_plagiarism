<?php

include '../../database/connection.php';

$type = $_POST['type'];

$response = [];

if ($type === 'programs') {
    $query = "SELECT id, program_name FROM program_table";
    $result = $conn->query($query);

    while ($row = $result->fetch_assoc()) {
        $response[] = $row;
    }
} elseif ($type === 'batches') {
    $programId = $_POST['program_id'];
    $query = "SELECT id, batch_name FROM batch_table WHERE program_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $programId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $response[] = $row;
    }
} elseif ($type === 'modules') {
    $batchId = $_POST['batch_id'];
    $query = "SELECT id, module_name FROM module_table WHERE batch_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $batchId);
    $stmt->execute();
    $result = $stmt->get_result();

    while ($row = $result->fetch_assoc()) {
        $response[] = $row;
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>
