<?php
session_start();
include("../database/connection.php");
include("includes/header.php");

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    // Redirect to login page if not logged in or role is incorrect
    echo '<script>window.location.href = "../login";</script>';
    exit();
}

// Include PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require '../vendor/autoload.php';

// Initialize variables
$students = [];

if (isset($_POST['upload'])) {
    // Check if file is uploaded
    if (isset($_FILES['csv_file']) && $_FILES['csv_file']['error'] == 0) {
        $fileName = $_FILES['csv_file']['name'];
        $fileTmpName = $_FILES['csv_file']['tmp_name'];
        $fileExtension = pathinfo($fileName, PATHINFO_EXTENSION);

        // Validate file type
        if ($fileExtension !== 'csv') {
            echo "Please upload a valid CSV file.";
        } else {
            if (($handle = fopen($fileTmpName, 'r')) !== false) {
                // Skip the header row
                fgetcsv($handle);

                // Prepare SQL statement
                $stmt = $conn->prepare(
                    "INSERT INTO old_student_db 
                    (student_id, name, DOB, program_id, batch_id, email, bms_email, phone_no, allocate) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)"
                );

                // Bind parameters
                $stmt->bind_param(
                    'sssssssss',
                    $student_id,
                    $name,
                    $DOB,
                    $program_id,
                    $batch_id,
                    $email,
                    $bms_email,
                    $phone_no,
                    $allocate
                );

                // Process each row
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $student_id = $data[0];
                    $name = $data[1];
                    $DOB = $data[2];
                    $program_id = $data[3];
                    $batch_id = $data[4];
                    $email = $data[5];
                    $bms_email = $data[6];
                    $phone_no = $data[7];
                    $allocate = ''; // Initially not allocated

                    // Execute query
                    $stmt->execute();
                }

                fclose($handle);
                echo "<script>alert('CSV file data successfully imported into the database.');</script>";

                // After successful upload, start allocation
                allocateStudents($program_id, $batch_id); // Call allocation function
            } else {
                echo "Error opening the file.";
            }
        }
    } else {
        echo "Please upload a CSV file.";
    }
}

// Function to allocate students automatically and group them by checker
// function allocateStudents($program_id, $batch_id)
// {
//     global $conn;

//     // Fetch students for allocation based on program_id and batch_id
//     $studentQuery = "SELECT * FROM old_student_db WHERE program_id = ? AND batch_id = ? AND allocate = ''";
//     $stmt = $conn->prepare($studentQuery);
//     $stmt->bind_param("ii", $program_id, $batch_id);
//     $stmt->execute();
//     $result = $stmt->get_result();

//     $students = [];
//     while ($row = $result->fetch_assoc()) {
//         $students[] = $row;
//     }

//     // Proceed if students are available for allocation
//     if (!empty($students)) {
//         // Fetch checkers
//         $checkerQuery = "SELECT * FROM checkers";
//         $checkerResult = mysqli_query($conn, $checkerQuery);
//         $checkers = [];
//         while ($checker = mysqli_fetch_assoc($checkerResult)) {
//             $checkers[] = $checker;
//         }

//         shuffle($students); // Shuffle student order
//         shuffle($checkers); // Shuffle checker order

//         // Allocate students to checkers and group them
//         $checkerIndex = 0;
//         $groupedAllocations = [];

//         foreach ($students as $student) {
//             $checkerId = $checkers[$checkerIndex]['id'];
//             $studentId = $student['id'];
//             $studentRegId = $student['student_id'];

//             // Insert the allocation into allocate_checker table
//             $allocateQuery = "INSERT INTO allocate_checker (student_id, student_reg_id, checker_id, batch_id, created_at) 
//                                 VALUES ('$studentId', '$studentRegId', '$checkerId', '$batch_id', NOW())";
//             if (mysqli_query($conn, $allocateQuery)) {
//                 // Update student's allocation status
//                 $updateStudentQuery = "UPDATE old_student_db SET allocate = 'allocated' WHERE id = '$studentId'";
//                 mysqli_query($conn, $updateStudentQuery);

//                 // Add student to the checker group for email
//                 $groupedAllocations[$checkerId][] = $student;
//             }

//             // Move to next checker
//             $checkerIndex = ($checkerIndex + 1) % count($checkers);
//         }

//         // Now, send one email per checker with the list of students assigned to them
//         foreach ($groupedAllocations as $checkerId => $students) {
//             sendGroupedAllocationEmail($checkerId, $students);
//         }

//         echo "<script>alert('Students successfully allocated to checkers and emails sent.');</script>";
//     } else {
//         echo "<script>alert('No unallocated students found for the selected program and batch.');</script>";
//     }
// }

// Function to send grouped email to checker
// function sendGroupedAllocationEmail($checkerId, $students)
// {
//     global $conn;

//     // Fetch checker details
//     $checkerQuery = "SELECT checker_name, checker_email FROM checkers WHERE id = ?";
//     $stmt = $conn->prepare($checkerQuery);
//     $stmt->bind_param("i", $checkerId);
//     $stmt->execute();
//     $checker = $stmt->get_result()->fetch_assoc();

//     // Prepare student details for email
//     $studentList = "";
//     foreach ($students as $student) {
//         $studentList .= "<tr>
//                             <td>{$student['student_id']}</td>
//                             <td>{$student['name']}</td>
//                             <td>{$student['bms_email']}</td>
//                           </tr>";
//     }

//     // Send email using PHPMailer
//     $mail = new PHPMailer(true);
//     try {
//         $mail->isSMTP();
//         $mail->Host = 'mail.graduatejob.lk';
//         $mail->SMTPAuth = true;
//         $mail->Username = 'noreply@graduatejob.lk';
//         $mail->Password = 'Hasni@2024'; // Use your app password here
//         $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
//         $mail->Port = 587;

//         $mail->setFrom('noreply@graduatejob.lk', 'GraduateJob');
//         $mail->addAddress($checker['checker_email'], $checker['checker_name']);
//         $mail->isHTML(true);
//         $mail->Subject = "Student Allocation Notification";
//         $mail->Body = "
//         <html>
//         <head>
//             <style>
//                 body {
//                     font-family: Arial, sans-serif;
//                     line-height: 1.6;
//                     margin: 20px;
//                     color: #333;
//                 }
//                 table {
//                     border-collapse: collapse;
//                     width: 100%;
//                     margin-top: 20px;
//                 }
//                 th, td {
//                     border: 1px solid #dddddd;
//                     text-align: left;
//                     padding: 12px;
//                 }
//                 th {
//                     background-color: #4CAF50;
//                     color: white;
//                 }
//                 tr:nth-child(even) {
//                     background-color: #f9f9f9;
//                 }
//                 tr:hover {
//                     background-color: #f1f1f1;
//                 }
//                 p {
//                     margin: 10px 0;
//                 }
//                 strong {
//                     color: #333;
//                 }
//             </style>
//         </head>
//         <body>
//             <p>Dear {$checker['checker_name']},</p>
//             <p>You have been allocated the following students:</p>
//             <table>
//                 <tr>
//                     <th>Student ID</th>
//                     <th>Name</th>
//                     <th>BMS Email</th>
//                 </tr>
//                 $studentList
//             </table>

//             <p>Best regards,</p>
//             <p>GraduateJob Team</p>
//         </body>
//         </html>
//         ";
//         $mail->send();
//     } catch (Exception $e) {
//         echo "Error sending email: " . $mail->ErrorInfo;
//     }
// }




// Function to allocate students automatically and group them by checker
function allocateStudents($program_id, $batch_id)
{
    global $conn;

    // Fetch program and module names using program_id and batch_id
    $programQuery = "SELECT program_name FROM program_table WHERE id = ?";
    $stmt = $conn->prepare($programQuery);
    $stmt->bind_param("i", $program_id);
    $stmt->execute();
    $programResult = $stmt->get_result();
    $program = $programResult->fetch_assoc();
    $programName = $program['program_name'];

    // Fetch module name (assumed to be the batch name)
    $batchQuery = "SELECT batch_name FROM batch_table WHERE id = ?";
    $stmt = $conn->prepare($batchQuery);
    $stmt->bind_param("i", $batch_id);
    $stmt->execute();
    $batchResult = $stmt->get_result();
    $batch = $batchResult->fetch_assoc();
    $batchName = $batch['batch_name'];

    // Fetch students for allocation based on program_id and batch_id
    $studentQuery = "SELECT * FROM old_student_db WHERE program_id = ? AND batch_id = ? AND allocate = ''";
    $stmt = $conn->prepare($studentQuery);
    $stmt->bind_param("ii", $program_id, $batch_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $students = [];
    while ($row = $result->fetch_assoc()) {
        $students[] = $row;
    }

    // Proceed if students are available for allocation
    if (!empty($students)) {
        // Fetch checkers
        $checkerQuery = "SELECT * FROM checkers";
        $checkerResult = mysqli_query($conn, $checkerQuery);
        $checkers = [];
        while ($checker = mysqli_fetch_assoc($checkerResult)) {
            $checkers[] = $checker;
        }

        shuffle($students); // Shuffle student order
        shuffle($checkers); // Shuffle checker order

        // Allocate students to checkers and group them
        $checkerIndex = 0;
        $groupedAllocations = [];

        foreach ($students as $student) {
            $checkerId = $checkers[$checkerIndex]['id'];
            $studentId = $student['id'];
            $studentRegId = $student['student_id'];

            // Insert the allocation into allocate_checker table
            $allocateQuery = "INSERT INTO allocate_checker (student_id, student_reg_id, checker_id, batch_id, created_at) 
                                VALUES ('$studentId', '$studentRegId', '$checkerId', '$batch_id', NOW())";
            if (mysqli_query($conn, $allocateQuery)) {
                // Update student's allocation status
                $updateStudentQuery = "UPDATE old_student_db SET allocate = 'allocated' WHERE id = '$studentId'";
                mysqli_query($conn, $updateStudentQuery);

                // Add student to the checker group for email
                $groupedAllocations[$checkerId][] = $student;
            }

            // Move to next checker
            $checkerIndex = ($checkerIndex + 1) % count($checkers);
        }

        // Now, send one email per checker with the list of students assigned to them
        foreach ($groupedAllocations as $checkerId => $students) {
            sendGroupedAllocationEmail($checkerId, $students, $programName, $batchName); // Pass program and batch names
        }

        echo "<script>alert('Students successfully allocated to checkers and emails sent.');</script>";
    } else {
        echo "<script>alert('No unallocated students found for the selected program and batch.');</script>";
    }
}

// Function to send grouped email to checker
function sendGroupedAllocationEmail($checkerId, $students, $programName, $batchName)
{
    global $conn;

    // Fetch checker details
    $checkerQuery = "SELECT checker_name, checker_email FROM checkers WHERE id = ?";
    $stmt = $conn->prepare($checkerQuery);
    $stmt->bind_param("i", $checkerId);
    $stmt->execute();
    $checker = $stmt->get_result()->fetch_assoc();

    // Prepare student details for email
    $studentList = "";
    foreach ($students as $student) {
        $studentList .= "<tr>
                            <td>{$student['student_id']}</td>
                            <td>{$student['name']}</td>
                            <td>{$student['bms_email']}</td>
                          </tr>";
    }

    // Send email using PHPMailer
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'mail.graduatejob.lk';
        $mail->SMTPAuth = true;
        $mail->Username = 'noreply@graduatejob.lk';
        $mail->Password = 'Hasni@2024'; // Use your app password here
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom('noreply@graduatejob.lk', 'GraduateJob');
        $mail->addAddress($checker['checker_email'], $checker['checker_name']);
        $mail->isHTML(true);
        $mail->Subject = "Student Allocation Notification";
        $mail->Body = "
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
            <p>You have been allocated the following students in the program <strong>{$programName}</strong> under the batch <strong>{$batchName}</strong>:</p>
            <table>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>BMS Email</th>
                </tr>
                $studentList
            </table>
            <p>Please complete the allocation process as soon as possible.</p>
            <p>Best regards,</p>
            <p>GraduateJob Team</p>
        </body>
        </html>
        ";
        $mail->send();
    } catch (Exception $e) {
        echo "Error sending email: " . $mail->ErrorInfo;
    }
}
?>




<!-- Page Wrapper -->
<div id="wrapper">
    <!-- Sidebar -->
    <?php include("nav.php"); ?>
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <!-- Main Content -->
        <div id="content">
            <!-- Topbar -->
            <?php include("includes/topnav.php"); ?>
            <!-- Begin Page Content -->
            <div class="p-3">
                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h4 class="h4 mb-0 text-gray-800">Module Management</h4>
                </div>

                <!-- Add Form -->
                <div class="row mb-5">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Upload CSV File</h6>
                            </div>
                            <div class="card-body">
                                <form action="" method="POST" enctype="multipart/form-data">
                                    <div class="form-group">
                                        <label for="csv_file">Choose CSV File:</label>
                                        <input type="file" name="csv_file" id="csv_file" required>
                                    </div>
                                    <button type="submit" name="upload" class="btn btn-primary mt-3">Upload</button>
                                </form>
                            </div>
                        </div>
                    </div>



                    <div class="col-md-4">
                        <div class="card">
                            <div class="card-header">
                                <h6 class="mb-0">Download the format</h6>
                            </div>
                            <div class="card-body">
                            <a href="old_student_db upload.csv" download style="text-decoration: none;">
                                    <div class="d-flex align-items-center justify-content-end" download>
                                        <div class="flex-shrink-0">
                                            <p class="mb-0 fw-bolder">DOWNLOAD EXCEL FORMAT</p>
                                        </div>
                                        <div class="flex-grow-1 ms-3">
                                            <img src="./img/pngegg.png" alt="Excel Image" class="img-fluid w-25">
                                        </div>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</div>