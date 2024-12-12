<?php
include('../database/connection.php');

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';



$inputData = json_decode(file_get_contents("php://input"), true);
$studentIds = $inputData['student_ids'];

$response = ['status' => 'success', 'messages' => []];
$checkerAllocations = [];

if (!empty($studentIds)) {
    $queryCheckers = "SELECT * FROM checkers";
    $resultCheckers = mysqli_query($conn, $queryCheckers);

    $checkers = [];
    while ($checker = mysqli_fetch_assoc($resultCheckers)) {
        $checkers[] = $checker;
    }

    shuffle($studentIds);
    shuffle($checkers);

    $checkerCount = count($checkers);
    $checkerIndex = 0;

    foreach ($studentIds as $studentId) {
        $checkerId = $checkers[$checkerIndex]['id'];

        $checkQuery = "SELECT * FROM allocate_checker WHERE student_id = '$studentId' AND checker_id = '$checkerId'";
        $checkResult = mysqli_query($conn, $checkQuery);

        if (mysqli_num_rows($checkResult) > 0) {
            $response['status'] = 'error';
            $response['messages'][] = "Student $studentId is already allocated to Checker $checkerId";
        } else {
            $allocateQuery = "INSERT INTO allocate_checker (student_id, checker_id) VALUES ('$studentId', '$checkerId')";
            if (mysqli_query($conn, $allocateQuery)) {
                $checkerAllocations[$checkerId][] = $studentId;
                $response['messages'][] = "Student $studentId allocated to Checker $checkerId";
            } else {
                $response['messages'][] = "Error allocating student $studentId: " . mysqli_error($conn);
            }
        }

        $checkerIndex = ($checkerIndex + 1) % $checkerCount;
    }

    foreach ($checkerAllocations as $checkerId => $studentIds) {
        $checkerQuery = "SELECT * FROM checkers WHERE id = '$checkerId'";
        $checkerResult = mysqli_query($conn, $checkerQuery);
        $checker = mysqli_fetch_assoc($checkerResult);

        if ($checker) {
            $emailBody = "Dear {$checker['checker_name']},\n\nYou have been allocated the following students:\n";
            foreach ($studentIds as $studentId) {
                $studentQuery = "SELECT * FROM old_student_db WHERE id = '$studentId'";
                $studentResult = mysqli_query($conn, $studentQuery);
                $student = mysqli_fetch_assoc($studentResult);
                if ($student) {
                    $emailBody .= "- {$student['name']} (ID: {$student['bms_email']})\n";
                }
            }
            $emailBody .= "\nBest regards,\nYour Allocation System";

            $mail = new PHPMailer();
            $mail->isSMTP();
            $mail->Host = 'smtp.office365.com';
            $mail->SMTPAuth = true;
            $mail->Username = 'noreply@graduatejob.lk';
            $mail->Password = 'Bms@202';
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $mail->Port = 587;

            $mail->setFrom('noreply@graduatejob.lk', 'Allocation System');
            $mail->addAddress($checker['checker_email'], $checker['checker_name']);
            $mail->Subject = 'Student Allocation Notification';
            $mail->Body = $emailBody;

            if (!$mail->send()) {
                $response['messages'][] = "Email to {$checker['checker_name']} failed: " . $mail->ErrorInfo;
            } else {
                $response['messages'][] = "Email sent to {$checker['checker_name']} ({$checker['checker_email']})";
            }
        }
    }
} else {
    $response['status'] = 'error';
    $response['messages'][] = "No students selected for allocation.";
}

header('Content-Type: application/json');
echo json_encode($response);
?>
