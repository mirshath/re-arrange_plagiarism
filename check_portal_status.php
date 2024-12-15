<?php
include('./database/connection.php');

$query = "SELECT portal_status FROM portal WHERE id = 1";
$result = $conn->query($query);
$response = ['status' => 'on'];

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['portal_status'] === 'off') {
        $response = [
            'status' => 'off',
            'message' => 'The portal is not available for use at the moment.'
        ];
    }
    else {
        $response = [
            'status' => 'on',
            'message' => 'The portal is available for use.'
        ];
    }
}

header('Content-Type: application/json');
echo json_encode($response);
?>