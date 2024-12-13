<?php
// validate_student.php
include('./database/connection.php'); // Include your database connection file

if (isset($_POST['student_id']) && isset($_POST['dob'])) {
    $student_id = $_POST['student_id'];
    $dob = $_POST['dob'];

    // Prepare the SQL statement with JOINs to fetch related data from other tables
    $stmt = $conn->prepare("
        SELECT 
            old_student_db.*, 
            batch_table.*,
            program_table.*,
            module_table.*
        FROM 
            old_student_db
        INNER JOIN 
            batch_table ON old_student_db.batch_id = batch_table.id
        INNER JOIN 
            program_table ON old_student_db.program_id = program_table.id
        LEFT JOIN 
            module_table ON module_table.program_id = program_table.id
        WHERE 
            old_student_db.student_id = ? 
            AND old_student_db.DOB = ?
    ");
    
    $stmt->bind_param("ss", $student_id, $dob); // Bind parameters
    $stmt->execute(); // Execute the statement
    $result = $stmt->get_result(); // Get the result

    if ($result->num_rows > 0) {
        $studentData = $result->fetch_assoc(); // Fetch the student data
        echo json_encode(['status' => 'valid', 'data' => $studentData]); // Return as JSON
    } else {
        echo json_encode(['status' => 'invalid']);
    }
    $stmt->close(); // Close the statement
} else {
    echo json_encode(['status' => 'invalid']);
}

$conn->close();
?>
