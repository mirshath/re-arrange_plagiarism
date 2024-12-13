<?php
include('../database/connection.php'); // Include your database connection file

// Get module_id from the AJAX request
$module_id = isset($_POST['module_id']) ? (int)$_POST['module_id'] : 0;

if ($module_id) {
    // Query to get the module deadline
    $sql = "SELECT deadline FROM module_table WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $module_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetch the deadline if the module exists
    if ($row = $result->fetch_assoc()) {
        $response = [
            "status" => "success",
            "deadline" => $row['deadline']
        ];
    } else {
        $response = ["status" => "error", "message" => "Deadline not found"];
    }

    echo json_encode($response);

    $stmt->close();
}

$conn->close();
?>
