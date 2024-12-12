<?php
session_start();

include('../database/connection.php');

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$inputData = json_decode(file_get_contents("php://input"), true);
$studentIds = $inputData['student_ids'];
$batchId = $inputData['batch_id']; // Ensure batch_id is passed in the input data

$response = ['status' => 'success', 'messages' => []];
$checkerAllocations = [];

if (!empty($studentIds)) {
    // Fetch all checkers
    $queryCheckers = "SELECT * FROM checkers";
    $resultCheckers = mysqli_query($conn, $queryCheckers);

    $checkers = [];
    while ($checker = mysqli_fetch_assoc($resultCheckers)) {
        $checkers[] = $checker;
    }

    shuffle($studentIds); // Shuffle student IDs
    shuffle($checkers); // Shuffle checkers

    $checkerCount = count($checkers);
    $checkerIndex = 0;


    // Allocate students to checkers
    // Allocate students to checkers
    foreach ($studentIds as $studentId) {
        $checkerId = $checkers[$checkerIndex]['id'];

        // Fetch the student_reg_id along with student_id
        $studentRegQuery = "SELECT * FROM old_student_db WHERE id = '$studentId'";
        $studentRegResult = mysqli_query($conn, $studentRegQuery);
        $studentRegData = mysqli_fetch_assoc($studentRegResult);
        $studentRegId = $studentRegData['student_id'];

        // Check for existing allocation
        $checkQuery = "SELECT * FROM allocate_checker WHERE student_id = '$studentId' AND checker_id = '$checkerId' AND batch_id = '$batchId'";
        $checkResult = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            $response['status'] = 'error';
            $response['messages'][] = "Student $studentId is already allocated to Checker $checkerId for Batch $batchId";
        } else {
            // Allocate the checker to the student, now with student_reg_id and batch_id
            // $allocateQuery = "INSERT INTO allocate_checker (student_id, student_reg_id, checker_id, batch_id) VALUES ('$studentId', '$studentRegId', '$checkerId', '$batchId')";
            $allocateQuery = "INSERT INTO allocate_checker (student_id, student_reg_id, checker_id, batch_id, created_at) VALUES ('$studentId', '$studentRegId', '$checkerId', '$batchId', NOW())";
            if (mysqli_query($conn, $allocateQuery)) {
                $checkerAllocations[$checkerId][] = $studentId;
                $response['messages'][] = "Student $studentId allocated to Checker $checkerId for Batch $batchId";
            } else {
                $response['messages'][] = "Error allocating student $studentId: " . mysqli_error($conn);
            }


            $updateStudentQuery = "UPDATE old_student_db SET allocate = 'allocated' WHERE id = '$studentId'";
            if (mysqli_query($conn, $updateStudentQuery)) {
                $response['messages'][] = "Student $studentId allocation status updated to allocated";
            } else {
                $response['messages'][] = "Error updating allocation status for student $studentId: " . mysqli_error($conn);
            }
        }

        // Cycle through checkers
        $checkerIndex = ($checkerIndex + 1) % $checkerCount;
    }



    // PHPMailer setup
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'mail.graduatejob.lk';
        $mail->SMTPAuth = true;
        $mail->Username = 'noreply@graduatejob.lk';
        $mail->Password = 'Hasni@2024'; // app password here
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        // Send email for each checker
        foreach ($checkerAllocations as $checkerId => $studentIds) {
            $checkerQuery = "SELECT checker_name, checker_email FROM checkers WHERE id = '$checkerId'";
            $checkerResult = mysqli_query($conn, $checkerQuery);
            $checker = mysqli_fetch_assoc($checkerResult);

            if ($checker) {
                // Initialize arrays to hold programs and batches
                $programs = [];
                $batches = [];

                // Collect all student details for this checker, including their program and batch
                $studentDetails = [];
                foreach ($studentIds as $studentId) {
                    $studentQuery = "SELECT s.student_id, s.name, s.bms_email, p.program_name, b.batch_name 
                                     FROM old_student_db s 
                                     JOIN program_table p ON s.program_id = p.id 
                                     JOIN batch_table b ON s.batch_id = b.id 
                                     WHERE s.id = '$studentId'";
                    $studentResult = mysqli_query($conn, $studentQuery);
                    $student = mysqli_fetch_assoc($studentResult);
                    if ($student) {
                        // Collect programs and batches
                        $programs[$student['program_name']] = $student['program_name'];
                        $batches[$student['batch_name']] = $student['batch_name'];

                        // Store student details
                        $studentDetails[] = $student;
                    }
                }

                // Start building the email body
                $emailBody = "
                <html>
                <head>
                    <style>
                        body {
                            font-family: Arial, sans-serif;
                            line-height: 1.6;
                            margin: 20px;
                            color: #333;
                        }
                        table {
                            border-collapse: collapse;
                            width: 100%;
                            margin-top: 20px;
                        }
                        th, td {
                            border: 1px solid #dddddd;
                            text-align: left;
                            padding: 12px;
                        }
                        th {
                            background-color: #4CAF50;
                            color: white;
                        }
                        tr:nth-child(even) {
                            background-color: #f9f9f9;
                        }
                        tr:hover {
                            background-color: #f1f1f1;
                        }
                        p {
                            margin: 10px 0;
                        }
                        strong {
                            color: #333;
                        }
                    </style>
                </head>
                <body>
                    <p>Dear {$checker['checker_name']},</p>
                    <p>You have been allocated the following students:</p>
                    <p><strong>Programs:</strong> " . implode(", ", array_unique($programs)) . "</p>
                    <p><strong>Batches:</strong> " . implode(", ", array_unique($batches)) . "</p>
                    <table>
                        <tr>
                            <th>Student ID</th>
                            <th>Name</th>
                            <th>Email</th>
                        </tr>
                ";

                // Add a row for each student
                foreach ($studentDetails as $student) {
                    $emailBody .= "
                    <tr>
                        <td>{$student['student_id']}</td>
                        <td>{$student['name']}</td>
                        <td>{$student['bms_email']}</td>
                    </tr>
                    ";
                }

                // Close the table and email body
                $emailBody .= "
                    </table>
                    <p>Best regards,<br>Your Allocation System</p>
                </body>
                </html>
                ";

                // Set email headers and content
                $mail->setFrom('noreply@graduatejob.lk', 'Allocation System');
                $mail->addAddress($checker['checker_email'], $checker['checker_name']);
                $mail->Subject = 'Student Allocation Notification';
                $mail->isHTML(true);
                $mail->Body = $emailBody;

                // Try sending the email and capture any errors
                if (!$mail->send()) {
                    $response['messages'][] = "Email to {$checker['checker_name']} failed: " . $mail->ErrorInfo;
                } else {
                    $response['messages'][] = "Email sent to {$checker['checker_name']} ({$checker['checker_email']})";
                }

                // Clear any previously set recipients for the next iteration
                $mail->clearAddresses();
                $mail->clearAttachments();
            }
        }
    } catch (Exception $e) {
        $response['status'] = 'error';
        $response['messages'][] = "Mailer Error: {$mail->ErrorInfo}";
    }
} else {
    $response['status'] = 'error';
    $response['messages'][] = "No students selected for allocation.";
}

header('Content-Type: application/json');
echo json_encode($response);
