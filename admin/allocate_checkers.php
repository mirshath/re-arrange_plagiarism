<?php
session_start();

include('../database/connection.php');

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

$inputData = json_decode(file_get_contents("php://input"), true);
// Get input data
$program_id = $_POST['program_id'];
$batch_id = $_POST['batch_id'];

// Prepare response
$response = ['status' => 'success', 'messages' => []];

// Fetch students for allocation based on program_id and batch_id
$query = "SELECT * FROM old_student_db WHERE program_id = '$program_id' AND batch_id = '$batch_id' AND allocate = ''";
$result = mysqli_query($conn, $query);
$students = [];
while ($student = mysqli_fetch_assoc($result)) {
    $students[] = $student['id'];
}

// Proceed if students are available
if (!empty($students)) {
    $studentIds = $students;
    $checkerQuery = "SELECT * FROM checkers";
    $checkerResult = mysqli_query($conn, $checkerQuery);
    $checkers = [];
    while ($checker = mysqli_fetch_assoc($checkerResult)) {
        $checkers[] = $checker;
    }

    shuffle($studentIds);  // Shuffle student IDs
    shuffle($checkers);    // Shuffle checkers

    $checkerCount = count($checkers);
    $checkerIndex = 0;

    // Allocate students to checkers
    $checkerAllocations = []; // Array to store checker allocations
    foreach ($studentIds as $studentId) {
        $checkerId = $checkers[$checkerIndex]['id'];

        // Fetch the student reg_id and other details
        $studentQuery = "SELECT * FROM old_student_db WHERE id = '$studentId' AND allocate=''";
        $studentResult = mysqli_query($conn, $studentQuery);
        $studentData = mysqli_fetch_assoc($studentResult);
        $studentRegId = $studentData['student_id'];

        // Check if already allocated
        $allocationCheckQuery = "SELECT * FROM allocate_checker WHERE student_id = '$studentId' AND checker_id = '$checkerId' AND batch_id = '$batch_id'";
        $allocationCheckResult = mysqli_query($conn, $allocationCheckQuery);

        if (mysqli_num_rows($allocationCheckResult) > 0) {
            $response['status'] = 'error';
            $response['messages'][] = "Student $studentId is already allocated.";
        } else {
            // Allocate student to checker
            $allocateQuery = "INSERT INTO allocate_checker (student_id, student_reg_id, checker_id, batch_id, created_at) VALUES ('$studentId', '$studentRegId', '$checkerId', '$batch_id', NOW())";
            if (mysqli_query($conn, $allocateQuery)) {
                // Update student allocation status
                $updateStudentQuery = "UPDATE old_student_db SET allocate = 'allocated' WHERE id = '$studentId'";
                mysqli_query($conn, $updateStudentQuery);
                $response['messages'][] = "Student $studentId allocated to Checker $checkerId.";

                // Store allocation for email
                if (!isset($checkerAllocations[$checkerId])) {
                    $checkerAllocations[$checkerId] = [];
                }
                $checkerAllocations[$checkerId][] = $studentId;
            } else {
                $response['messages'][] = "Error allocating student $studentId: " . mysqli_error($conn);
            }
        }

        $checkerIndex = ($checkerIndex + 1) % $checkerCount;
    }
} else {
    $response['status'] = 'error';
    $response['messages'][] = "No unallocated students found.";
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

header('Content-Type: application/json');
echo json_encode($response);
?>
