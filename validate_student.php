<?php
session_start();
include('./database/connection.php');

header('Content-Type: application/json');

$input = json_decode(file_get_contents('php://input'), true);

$student_id = $input['student_id'] ?? '';
$dob = $input['dob'] ?? '';

$response = ['success' => false, 'message' => '', 'name' => '', 'email' => '', 'phone' => '', 'program_name' => '', 'batch_name' => '', 'module_name' => ''];

if (empty($student_id) || empty($dob)) {
    $response['message'] = 'Student ID and Date of Birth are required.';
    echo json_encode($response);
    exit;
}

// Query to get all student details including program, batch, and module details
$query = "
    SELECT 
        st.id, 
        st.name, 
        st.bms_email, 
        st.phone_no, 
        pr.program_name, 
        ba.batch_name, 
        ba.id as batch_id, 
        mo.module_name
    FROM 
        old_student_db AS st
    LEFT JOIN 
        student_allocations AS sa ON sa.student_id = st.id
    LEFT JOIN 
        program_table AS pr ON sa.program_id = pr.id
    LEFT JOIN 
        batch_table AS ba ON sa.batch_id = ba.id
    LEFT JOIN 
        module_table AS mo ON sa.module_id = mo.id
    WHERE 
        st.student_id = ? AND st.DOB = ?
";

$stmt = $conn->prepare($query);

if ($stmt) {
    $stmt->bind_param('ss', $student_id, $dob);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $student = $result->fetch_assoc();
        $response['success'] = true;
        $response['id'] = $student['id'];
        $response['name'] = $student['name'];
        $response['email'] = $student['bms_email'];
        $response['phone'] = $student['phone_no'];
        $response['program_name'] = $student['program_name'];
        $response['batch_id'] = $student['batch_id'];
        $response['batch_name'] = $student['batch_name'];
        $response['module_name'] = $student['module_name'];
    } else {
        $response['message'] = 'Student not found or already submitted.';
    }

    $stmt->close();
} else {
    $response['message'] = 'Database query error.';
}

echo json_encode($response);
?>
