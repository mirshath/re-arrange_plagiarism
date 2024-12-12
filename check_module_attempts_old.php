<?php
include('./database/connection.php'); // Database connection file

if (isset($_POST['module_id']) && isset($_POST['student_id'])) {
    $module_id = $_POST['module_id'];
    $student_id = $_POST['student_id'];

    $query = "SELECT attempts  FROM module_attempt WHERE module_id = ? AND student_id = ?";

    $stmt = mysqli_prepare($conn, $query);
    if ($stmt) {
        mysqli_stmt_bind_param($stmt, 'ii', $module_id, $student_id);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            $attempts = (int) $row['attempts'];
            echo json_encode(['success' => true, 'ma_attempts' => $attempts]);
        } else {
            echo json_encode(['success' => true, 'ma_attempts' => 0]);
        }

        mysqli_stmt_close($stmt);
    } else {
        echo json_encode(['success' => false, 'message' => 'Query preparation failed.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Missing module ID or student ID.']);
}

mysqli_close($conn);
