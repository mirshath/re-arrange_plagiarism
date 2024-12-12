<?php
session_start();
include("../database/connection.php");
include("includes/header.php");

// Ensure the user is logged in and has the correct role
if ($_SESSION['role'] !== 'super_admin') {
    echo '<script>window.location.href = "../login.php";</script>';
    exit();
}

// Check if form is submitted
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
            // Open the file
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
                    $allocate = $data[8];

                    // Execute query
                    $stmt->execute();
                }

                fclose($handle);
                echo "<script>alert('CSV file data successfully imported into the database.'); 
                window.location.href = 'students_upload.php';</script>";
                
            } else {
                echo "Error opening the file.";
            }
        }
    } else {
        echo "Please upload a CSV file.";
    }
}

// Close connection
$conn->close();
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
                            <div class="card-header d-flex align-items-center" style="height: 60px;">
                                <span class="bg-dark text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                    <i class="fas fa-plus-circle"></i>
                                </span> &nbsp;&nbsp;&nbsp;&nbsp;
                                <h6 class="mb-0 me-2">Add Modules</h6>
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
                </div>

            </div>

            <!-- /.container-fluid -->
        </div>
        <!-- End of Main Content -->
    </div>
    <!-- End of Content Wrapper -->
</div>
<!-- End of Page Wrapper -->



<!-- Page level plugins -->
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<link rel="stylesheet" href="./vendor/datatables/dataTables.bootstrap4.min.css">
<!-- Page level custom scripts -->
<script src="js/demo/datatables-demo.js"></script>

</body>
</html>
