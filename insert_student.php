<?php
include('./database/connection.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Sanitize inputs
    // Get POST data and sanitize inputs
    $id = mysqli_real_escape_string($conn, trim($_POST['std_auto_id']));
    $student_id = mysqli_real_escape_string($conn, trim($_POST['student_id']));
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $name_in_full = mysqli_real_escape_string($conn, trim($_POST['name_in_full']));
    $bms_email_address = mysqli_real_escape_string($conn, trim($_POST['bms_email_address']));
    $phone_no = mysqli_real_escape_string($conn, trim($_POST['phone_no']));
    $program_id = mysqli_real_escape_string($conn, $_POST['program_id']);
    $batch_id = mysqli_real_escape_string($conn, $_POST['batch_name']);

    // Check if 'module' is selected
    if (isset($_POST['module_id']) && !empty($_POST['module_id'])) {
        $module_id = mysqli_real_escape_string($conn, $_POST['module_id']);
    } else {
        echo 'Please Select a module.';
        exit;
    }

    $uploadDir = 'uploads/documents/';
    $filePath = null;

    // Check if file is uploaded
    if (empty($_FILES['document']['name'])) {
        echo 'Please upload a document.';
        exit;
    } else {
        $fileName = basename($_FILES['document']['name']);
        $fileTmpName = $_FILES['document']['tmp_name'];
        $filePath = $fileName;

        // Validate file type (only allow PDFs, DOCX)
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);
        $allowedExtensions = ['pdf', 'docx'];

        if (!in_array(strtolower($fileExtension), $allowedExtensions)) {
            echo 'Invalid file type. Please upload a PDF or DOCX file.';
            exit;
        }

        // Create upload directory if it doesn't exist
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }

        // Move the uploaded file to the server
        if (!move_uploaded_file($fileTmpName, $uploadDir . $filePath)) {
            echo 'File upload failed.';
            $filePath = null;
            exit;
        }
    }

    // Check if the student record exists
    $checkerQuery = "
         SELECT ac.*, ch.*, osd.name AS student_name 
         FROM allocate_checker ac
         INNER JOIN checkers ch ON ac.checker_id = ch.id
         INNER JOIN old_student_db osd ON ac.student_id = osd.id
         WHERE ac.student_id  = ?
         LIMIT 1
         ";
    $stmt = mysqli_prepare($conn, $checkerQuery);
    mysqli_stmt_bind_param($stmt, 's', $student_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $checker = mysqli_fetch_assoc($result);

    if ($checker) {
        $checkerID = $checker['id'];
        $checkerName = $checker['checker_name'];
        $checkerEmail = $checker['checker_email'];
        $studentName = $checker['student_name'];
    } else {
        $checkerName = "N/A";
        $checkerEmail = "N/A";
        $studentName = "N/A";
    }


    // Check if the student and module record exists
    $checkQuery = "SELECT * FROM student_submitted_form WHERE student_id = ? AND module_id = ?";
    $stmt = mysqli_prepare($conn, $checkQuery);
    mysqli_stmt_bind_param($stmt, 'ss', $student_id, $module_id);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if the record exists
    if (mysqli_num_rows($result) > 0) {
        // Fetch the record and determine the attempt count
        $row = mysqli_fetch_assoc($result);
        $attemptCount = $row['attempt'];

        // Initialize the update query
        if ($attemptCount == 1) {
            $updateQuery = "
             UPDATE student_submitted_form 
             SET date_of_birth = ?, 
                 name_full = ?, 
                 bms_email = ?, 
                 phone_number = ?, 
                 program_id = ?, 
                 batch_id = ?, 
                 module_id = ?, 
                 Documents_1 = ?, 
                 checker_id = ?, 
                 submitted_at_2nd_time = NOW(), 
                 checked_status = 'pending', 
                 attempt = attempt + 1 
             WHERE student_id = ? AND module_id = ?
         ";
        } elseif ($attemptCount == 2) {
            $updateQuery = "
             UPDATE student_submitted_form 
             SET date_of_birth = ?, 
                 name_full = ?, 
                 bms_email = ?, 
                 phone_number = ?, 
                 program_id = ?, 
                 batch_id = ?, 
                 module_id = ?, 
                 Documents_2 = ?, 
                 checker_id = ?, 
                 submitted_at_3rd_time = NOW(), 
                 checked_status = 'pending', 
                 attempt = attempt + 1 
             WHERE student_id = ? AND module_id = ?
         ";
        } else {
            echo "Maximum attempts reached.";
            exit;
        }

        // Prepare and execute the update query
        $stmt = mysqli_prepare($conn, $updateQuery);
        if ($stmt) {
            // Bind parameters based on the query
            mysqli_stmt_bind_param(
                $stmt,
                'sssssssssss',
                $dob,
                $name_in_full,
                $bms_email_address,
                $phone_no,
                $program_id,
                $batch_id,
                $module_id,
                $filePath,
                $checkerID,
                $student_id,
                $module_id
            );
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            echo 'Update successful';

            // Update the attempt count in the module_attempt table
            $updateAttemptQuery = "
             UPDATE module_attempt 
             SET attempts = attempts + 1 
             WHERE student_id = ? AND module_id = ?
         ";
            $stmt = mysqli_prepare($conn, $updateAttemptQuery);
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, 'ss', $student_id, $module_id);
                mysqli_stmt_execute($stmt);
                mysqli_stmt_close($stmt);
                echo 'Attempt count updated in module_attempt';
            } else {
                echo 'Failed to update attempt count in module_attempt';
            }
        } else {
            echo 'Error updating student record';
        }
    } else {
        // Insert a new record for the student
        $insertQuery = "
             INSERT INTO student_submitted_form (student_id, date_of_birth, name_full, bms_email, phone_number, program_id, batch_id, module_id, Documents, checker_id, submitted_at, attempt) 
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 1)
         ";
        $stmt = mysqli_prepare($conn, $insertQuery);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ssssssssss', $student_id, $dob, $name_in_full, $bms_email_address, $phone_no, $program_id, $batch_id, $module_id, $filePath, $checkerID);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            echo 'Registration successful';
        }


        // Update the attempt count in the old_student_db table
        $insertModuleAttemptQuery = "INSERT INTO module_attempt (student_id, module_id, attempts, created_at) VALUES (?, ?, 1, NOW())";
        $stmt = mysqli_prepare($conn, $insertModuleAttemptQuery);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, 'ss', $student_id, $module_id);
            mysqli_stmt_execute($stmt);
            mysqli_stmt_close($stmt);
            echo 'Module attempt record inserted successfully';
        } else {
            echo 'Failed to insert module attempt record';
        }


        // ------------------------------ update the allocate table ----------- 
        $updateSubmitted_status = "UPDATE allocate_checker SET submitted_status = 'submitted' WHERE student_reg_id = ?";
        $stmts = mysqli_prepare($conn, $updateSubmitted_status);

        if ($stmts) {
            // Bind the student_id parameter to the prepared statement
            mysqli_stmt_bind_param($stmts, 's', $student_id);

            // Execute the statement
            mysqli_stmt_execute($stmts);

            // Close the statement
            mysqli_stmt_close($stmts);

            echo 'Submitted status updated successfully in allocate_checker table';
        } else {
            echo 'Failed to update submitted status in allocate_checker table';
        }
    }

}
?>