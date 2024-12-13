<?php
include('./database/connection.php');

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get POST data and sanitize inputs
    $student_id = mysqli_real_escape_string($conn, trim($_POST['student_id']));
    $dob = mysqli_real_escape_string($conn, $_POST['dob']);
    $name_in_full = mysqli_real_escape_string($conn, trim($_POST['name_in_full']));
    $bms_email_address = mysqli_real_escape_string($conn, trim($_POST['bms_email_address']));
    $phone_no = mysqli_real_escape_string($conn, trim($_POST['phone_no']));
    $program_id = mysqli_real_escape_string($conn, $_POST['program_id']);
    $batch_id = mysqli_real_escape_string($conn, $_POST['batch_id']);

    // Check if 'module' is selected
    if (isset($_POST['module']) && !empty($_POST['module'])) {
        $module_id = mysqli_real_escape_string($conn, $_POST['module']);
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
        WHERE ac.student_reg_id = ?
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

    // Check if the student has already submitted the form
    // $checkQuery = "SELECT * FROM student_submitted_form WHERE student_id = ?";
    // $stmt = mysqli_prepare($conn, $checkQuery);
    // mysqli_stmt_bind_param($stmt, 's', $student_id);
    // mysqli_stmt_execute($stmt);
    // $result = mysqli_stmt_get_result($stmt);



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


    // Send email to student
    $mail = new PHPMailer;
    $mail->isSMTP();
    $mail->Host = 'mail.graduatejob.lk';
    $mail->SMTPAuth = true;
    $mail->Username = 'noreply@graduatejob.lk';
    $mail->Password = 'Hasni@2024';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('noreply@graduatejob.lk', 'GraduateJob.lk');
    $mail->addAddress($bms_email_address);
    $mail->isHTML(true);
    $mail->Subject = 'Document Submission Confirmation';

    $mail->Body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px; color: #333; }
            table { border-collapse: collapse; width: 100%; margin-top: 20px; }
            th, td { border: 1px solid #dddddd; text-align: left; padding: 12px; }
            th { background-color: #4CAF50; color: white; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            tr:hover { background-color: #f1f1f1; }
        </style>
        </head>
        <body>
        <p>Dear $name_in_full,</p>
        <p>Thank you for submitting your document. Your submission will be forwarded to the checker for review.</p>
        <p><strong>Your allocated checker is:</strong></p>
        <table>
            <tr><th>Checker Email</th></tr>
            <tr><td>$checkerEmail</td></tr>
        </table>
        <p>You will receive a notification once your submission has been reviewed.</p>
        <p>Best regards,<br>Your Team</p>
        </body>
        </html>
    ";

    $mail->addAttachment($uploadDir . $filePath);

    if ($mail->send()) {
        echo 'Email sent to student successfully.';
    }

    // Send email to checker with student details
    $mailChecker = new PHPMailer;
    $mailChecker->isSMTP();
    $mailChecker->Host = 'mail.graduatejob.lk';
    $mailChecker->SMTPAuth = true;
    $mailChecker->Username = 'noreply@graduatejob.lk';
    $mailChecker->Password = 'Hasni@2024';
    $mailChecker->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mailChecker->Port = 587;

    $mailChecker->setFrom('noreply@graduatejob.lk', 'GraduateJob.lk');
    $mailChecker->addAddress($checkerEmail);
    $mailChecker->isHTML(true);
    $mailChecker->Subject = 'New Student Document Submitted for Review';

    $mailChecker->Body = "
    <html>
    <head>
        <style>
            body { font-family: Arial, sans-serif; line-height: 1.6; margin: 20px; color: #333; }
            table { border-collapse: collapse; width: 100%; margin-top: 20px; }
            th, td { border: 1px solid #dddddd; text-align: left; padding: 12px; }
            th { background-color: #4CAF50; color: white; }
            tr:nth-child(even) { background-color: #f9f9f9; }
            tr:hover { background-color: #f1f1f1; }
        </style>
        </head>
        <body>
        <p>Dear $checkerName,</p>
        <p>A new document has been submitted by the student with the following details:</p>
        <table>
            <tr><th>Student Name</th><td>$studentName</td></tr>
            <tr><th>Student ID</th><td>$student_id</td></tr>
            <tr><th>Date of Birth</th><td>$dob</td></tr>
            <tr><th>Email</th><td>$bms_email_address</td></tr>
            <tr><th>Document</th><td><a href='http://localhost/plagiarism-checker/login.php'>View Document</a></td></tr>
        </table>
        <p>Please review and approve or reject the document submission.</p>
        <p>Best regards,<br>Your Team</p>
        </body>
        </html>
    ";

    if ($mailChecker->send()) {
        echo 'Email sent to checker successfully.';
    } else {
        echo 'Failed to send email to checker.';
    }
}
