<?php
session_start();
include("../database/connection.php");

// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    http_response_code(403);
    echo "Unauthorized access";
    exit();
}

// Update the portal status if `portal_status` is sent via POST
if (isset($_POST['portal_status'])) {
    $newStatus = $_POST['portal_status'];
    $updateQuery = "UPDATE portal SET portal_status = ? WHERE id = 1";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("s", $newStatus);

    if ($stmt->execute()) {
        echo "Portal status updated to: $newStatus";
    } else {
        echo "Failed to update portal status";
    }
    $stmt->close();
}

$conn->close();
