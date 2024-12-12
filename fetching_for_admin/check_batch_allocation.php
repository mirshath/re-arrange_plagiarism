<?php
include('../database/connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['batch_id'])) {
    $batchId = mysqli_real_escape_string($conn, $_POST['batch_id']);

    // Check if batch is allocated
    $query = "SELECT COUNT(*) AS allocation_count FROM allocate_checker WHERE batch_id = '$batchId'";
    $result = mysqli_query($conn, $query);

    if ($result) {
        $data = mysqli_fetch_assoc($result);
        if ($data['allocation_count'] > 0) {
            echo json_encode([
                'status' => 'allocated',
                'message' => 'This batch has already been allocated!'
            ]);
        } else {
            echo json_encode([
                'status' => 'available'
            ]);
        }
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Error checking allocation.'
        ]);
    }
}
?>
