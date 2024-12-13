<?php
session_start();
include('./database/connection.php');

header('Content-Type: application/json');

$batch_id = $_POST['batch_id'] ?? '';

$response = ['success' => false, 'modules' => []];

if (empty($batch_id)) {
    echo json_encode($response);
    exit;
}



// Query to fetch modules for the selected batch with deadlines after today's date
$query = "
    SELECT *
    FROM module_table 
    WHERE batch_id = ? 
      AND deadline >= CURDATE()
";

$stmt = $conn->prepare($query);

if ($stmt) {
    $stmt->bind_param('s', $batch_id);  // Assuming batch_id is a string
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $modules = [];
        while ($module = $result->fetch_assoc()) {
            $modules[] = $module;
        }
        $response['success'] = true;
        $response['modules'] = $modules;
    } else {
        $response['message'] = 'No modules found for the selected batch.';
    }

    $stmt->close();
} else {
    $response['message'] = 'Database query error.';
}

echo json_encode($response);
