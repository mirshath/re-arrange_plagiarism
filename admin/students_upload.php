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

// Check if form is submitted
if (isset($_POST['upload'])) {
    // Get dropdown values for program, batch, and module
    $program_id = isset($_POST['program_id']) ? $_POST['program_id'] : '';
    $batch_id = isset($_POST['batch_id']) ? $_POST['batch_id'] : '';
    $module_id = isset($_POST['module_id']) ? $_POST['module_id'] : '';

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

                // Prepare SQL statement for inserting into `old_student_db`
                $stmt = $conn->prepare(
                    "INSERT INTO old_student_db 
                    (student_id, name, DOB, email, bms_email, phone_no, allocate) 
                    VALUES (?, ?, ?, ?, ?, ?, ?)"
                );

                // Bind parameters
                $stmt->bind_param(
                    'sssssss',
                    $student_id,
                    $name,
                    $DOB,
                    $email,
                    $bms_email,
                    $phone_no,
                    $allocate
                );

                // Process each row in the CSV
                while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                    $student_id = $data[0];
                    $name = $data[1];
                    $DOB = $data[2];
                    $email = $data[3];
                    $bms_email = $data[4];
                    $phone_no = $data[5];
                    $allocate = ''; // Initially not allocated

                    // Execute query to insert into `old_student_db`
                    $stmt->execute();

                    // Get the last inserted student ID
                    $last_student_id = $conn->insert_id;

                    // Insert into `student_allocations` table using the last inserted student_id
                    $insertQuery = "INSERT INTO student_allocations (student_id, program_id, batch_id, module_id) 
                                    VALUES ('$last_student_id', '$program_id', '$batch_id', '$module_id')";
                    $conn->query($insertQuery);
                }

                fclose($handle);
                echo "<script>alert('CSV file data successfully imported and allocations added.');</script>";

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



// ----------------- new this is hide   here wanna unhide 


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

    // Fetch batch name using batch_id
    $batchQuery = "SELECT batch_name FROM batch_table WHERE id = ?";
    $stmt = $conn->prepare($batchQuery);
    $stmt->bind_param("i", $batch_id);
    $stmt->execute();
    $batchResult = $stmt->get_result();
    $batch = $batchResult->fetch_assoc();
    $batchName = $batch['batch_name'];

    // Fetch students for allocation based on program_id and batch_id from student_allocations table
    $studentQuery = "
        SELECT sa.*, osd.*, osd.student_id as student_reg_id 
        FROM student_allocations sa
        JOIN old_student_db osd ON sa.student_id = osd.id
        WHERE sa.program_id = ? AND sa.batch_id = ? AND osd.allocate = ''
        ";
    $stmt = $conn->prepare($studentQuery);
    $stmt->bind_param("ii", $program_id, $batch_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Fetching the results
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
            // Assuming $student['id'] is the correct student ID from old_student_db
            $checkerId = $checkers[$checkerIndex]['id'];
            $studentId = $student['id'];  // The student ID from old_student_db (check the correct field name)
            $studentRegId = $student['student_reg_id'];  // This might be different from $studentId
        
            // Insert the allocation into the allocate_checker table
            $allocateQuery = "INSERT INTO allocate_checker (student_id, student_reg_id, checker_id, batch_id, created_at) 
                                VALUES (?, ?, ?, ?, NOW())";
            $stmt = $conn->prepare($allocateQuery);
            $stmt->bind_param("iiii", $studentId, $studentRegId, $checkerId, $batch_id);
            if ($stmt->execute()) {
                // Update the student's allocation status in the old_student_db table
                $updateStudentQuery = "UPDATE old_student_db SET allocate = 'allocated' WHERE id = ?";
                $stmt = $conn->prepare($updateStudentQuery);
                $stmt->bind_param("i", $studentId); // Use the correct student ID here
                $stmt->execute();
        
                // Add student to the checker group for email
                $groupedAllocations[$checkerId][] = $student;
            }
        
            // Move to the next checker
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
                                    <div class="mb-3">
                                        <label for="program" class="form-label">Program:</label>
                                        <select id="program" name="program_id" class="form-select">
                                            <option value="">Select Program</option>
                                            <!-- Options will be dynamically populated -->
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="batch" class="form-label">Batch:</label>
                                        <select id="batch" name="batch_id" class="form-select" disabled>
                                            <option value="">Select Batch</option>
                                            <!-- Options will be dynamically populated -->
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="module" class="form-label">Module:</label>
                                        <select id="module" name="module_id" class="form-select" disabled>
                                            <option value="">Select Module</option>
                                            <!-- Options will be dynamically populated -->
                                        </select>
                                    </div>

                                    <div class="mb-3">
                                        <label for="csv_file" class="form-label">Choose CSV File:</label>
                                        <input type="file" name="csv_file" id="csv_file" class="form-control" required>
                                    </div>

                                    <button type="submit" name="upload" class="btn btn-primary">Upload</button>
                                </form>

                                <script>
                                    $(document).ready(function() {
                                        // Fetch programs
                                        $.ajax({
                                            url: "fetch-for-upload/fetch_dropdown_data.php",
                                            method: "POST",
                                            data: {
                                                type: "programs"
                                            },
                                            dataType: "json",
                                            success: function(data) {
                                                data.forEach(function(item) {
                                                    $("#program").append(new Option(item.program_name, item.id));
                                                });
                                            }
                                        });

                                        // Fetch batches when a program is selected
                                        $("#program").change(function() {
                                            let programId = $(this).val();
                                            $("#batch").prop("disabled", !programId).html('<option value="">Select Batch</option>');
                                            $("#module").prop("disabled", true).html('<option value="">Select Module</option>');

                                            if (programId) {
                                                $.ajax({
                                                    url: "fetch-for-upload/fetch_dropdown_data.php",
                                                    method: "POST",
                                                    data: {
                                                        type: "batches",
                                                        program_id: programId
                                                    },
                                                    dataType: "json",
                                                    success: function(data) {
                                                        data.forEach(function(item) {
                                                            $("#batch").append(new Option(item.batch_name, item.id));
                                                        });
                                                    }
                                                });
                                            }
                                        });

                                        // Fetch modules when a batch is selected
                                        $("#batch").change(function() {
                                            let batchId = $(this).val();
                                            $("#module").prop("disabled", !batchId).html('<option value="">Select Module</option>');

                                            if (batchId) {
                                                $.ajax({
                                                    url: "fetch-for-upload/fetch_dropdown_data.php",
                                                    method: "POST",
                                                    data: {
                                                        type: "modules",
                                                        batch_id: batchId
                                                    },
                                                    dataType: "json",
                                                    success: function(data) {
                                                        data.forEach(function(item) {
                                                            $("#module").append(new Option(item.module_name, item.id));
                                                        });
                                                    }
                                                });
                                            }
                                        });
                                    });
                                </script>
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