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
            } else {
                echo "Error opening the file.";
            }
        }
    } else {
        echo "Please upload a CSV file.";
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
