<?php
include('../database/connection.php'); // Include your database connection

if (isset($_POST['program_id'])) {
    $programId = $_POST['program_id'];

    $batchQuery = "SELECT * FROM batch_table WHERE program_id = ?";
    $stmt = $conn->prepare($batchQuery);
    $stmt->bind_param("i", $programId);
    $stmt->execute();
    $result = $stmt->get_result();

    echo '<option value="">Select Batch</option>';
    while ($batchRow = $result->fetch_assoc()) {
        echo "<option value='{$batchRow['id']}'>{$batchRow['batch_name']}</option>";
    }

    $stmt->close();
}
?>
