<?php
include('../database/connection.php'); // Include your database connection file

// Get program_id from the AJAX request
$program_id = isset($_POST['program_id']) ? (int)$_POST['program_id'] : 0;

if ($program_id) {
    // Query to get module names where program_id matches
    $sql = "SELECT * FROM module_table WHERE program_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $program_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Prepare response data
    $modules = [];
    while ($row = $result->fetch_assoc()) {
        $modules[] = $row;
    }

    // Return the data as JSON
    echo json_encode($modules);
    
    $stmt->close();
}

$conn->close();
?>
