<?php
session_start();
include("../database/connection.php");
include("includes/header.php");

// Ensure the user is logged in and has the correct role
if ($_SESSION['role'] !== 'super_admin') {
    echo '<script>window.location.href = "../login.php";</script>';
    exit();
}

// Handle form submission for adding program
if (isset($_POST['submit_program'])) {
    $program_name = mysqli_real_escape_string($conn, $_POST['program_name']);

    $query = "INSERT INTO program_table (program_name) VALUES ('$program_name')";
    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Program added successfully!');</script>";
        echo "<script>window.location.href = 'masterFile';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// Handle form submission for adding batch
if (isset($_POST['submit_batch'])) {
    $program_id = $_POST['program_id'];  // Get selected program ID
    $batch_name = mysqli_real_escape_string($conn, $_POST['batch_name']);  // Get batch name

    // Insert batch name and program ID into batch_table
    $query = "INSERT INTO batch_table (batch_name, program_id) VALUES ('$batch_name', '$program_id')";

    if (mysqli_query($conn, $query)) {
        echo "<script>alert('Batch added successfully!');</script>";
        echo "<script>window.location.href = 'masterFile';</script>";
    } else {
        echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
    }
}

// Handle form submission for adding multiple modules
if (isset($_POST['submit_module'])) {
    $program_id = $_POST['program_id'];  // Get selected program ID
    $module_names = $_POST['module_name'];  // Get array of module names
    $deadlines = $_POST['deadline'];  // Get array of deadlines

    // Loop through each module and insert into the database
    for ($i = 0; $i < count($module_names); $i++) {
        $module_name = mysqli_real_escape_string($conn, $module_names[$i]);
        $deadline = mysqli_real_escape_string($conn, $deadlines[$i]);

        $query = "INSERT INTO module_table (module_name, program_id, deadline) 
                  VALUES ('$module_name', '$program_id', '$deadline')";

        if (!mysqli_query($conn, $query)) {
            echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
            exit();
        }
    }

    echo "<script>alert('Modules added successfully!');</script>";
    echo "<script>window.location.href = 'masterFile';</script>";
}
?>

<!-- Page Wrapper -->
<div id="wrapper">
    <?php include("nav.php"); ?>
    <div id="content-wrapper" class="d-flex flex-column">
        <div id="content">
            <?php include("includes/topnav.php"); ?>

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h4 class="h4 mb-0 text-gray-800">Master File</h4>
            </div>

            <!-- Add Form for Program -->
            <div class="row mb-5 p-4">
                <!-- Program Add Section -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex align-items-center" style="height: 60px;">
                            <span class="bg-dark text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                <i class="fas fa-plus-circle"></i>
                            </span>
                            <h6 class="mb-0 me-2">&nbsp;&nbsp; Add Program</h6>
                        </div>
                        <div class="card-body">
                            <form action="" method="post" class="mb-3">
                                <div class="form-group">
                                    <label for="program_name">Program Name</label>
                                    <input type="text" class="form-control" id="program_name" name="program_name" required>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3" name="submit_program">Add Program</button>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Batch Add Section -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header d-flex align-items-center" style="height: 60px;">
                            <span class="bg-dark text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                <i class="fas fa-plus-circle"></i>
                            </span>
                            <h6 class="mb-0 me-2">&nbsp;&nbsp; Add Batch</h6>
                        </div>
                        <div class="card-body">
                            <form action="" method="post" class="mb-3">
                                <!-- Dropdown for Program Name -->
                                <div class="form-group">
                                    <label for="program_id">Select Program</label>
                                    <select class="form-control" id="program_id" name="program_id" required>
                                        <option value="">Select Program</option>
                                        <?php
                                        // Fetch programs from program_table
                                        $program_query = "SELECT * FROM program_table";
                                        $program_result = mysqli_query($conn, $program_query);
                                        while ($row = mysqli_fetch_assoc($program_result)) {
                                            echo "<option value='" . $row['id'] . "'>" . $row['program_name'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>
                                <!-- Input for Batch Name -->
                                <div class="form-group">
                                    <label for="batch_name">Batch Name</label>
                                    <input type="text" class="form-control" id="batch_name" name="batch_name" required>
                                </div>
                                <button type="submit" class="btn btn-primary mt-3" name="submit_batch">Add Batch</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>






            <!-- Add Module Section -->
            <div class="row mb-5 p-4">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex align-items-center" style="height: 60px;">
                            <span class="bg-dark text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                <i class="fas fa-plus-circle" id="addModuleRow" style="cursor: pointer;"></i>
                            </span>
                            <h6 class="mb-0 me-2">&nbsp;&nbsp; Add Module</h6>
                        </div>
                        <div class="card-body">
                            <form action="" method="post" class="mb-3" id="moduleForm">





                                <!-- Dropdown for Program Name -->
                                <div class="form-group">
                                    <label for="program_id">Select Program</label>
                                    <select class="form-control" id="program_id" name="program_id" required>
                                        <option value="">Select Program</option>
                                        <?php
                                        // Fetch programs from program_table
                                        $program_query = "SELECT * FROM program_table";
                                        $program_result = mysqli_query($conn, $program_query);
                                        while ($row = mysqli_fetch_assoc($program_result)) {
                                            echo "<option value='" . $row['id'] . "'>" . $row['program_name'] . "</option>";
                                        }
                                        ?>
                                    </select>
                                </div>


                                <div id="moduleRowsContainer" class="mb-3">
                                    <div class="module-row">
                                        <div class="row g-3 align-items-center">
                                            <!-- Module Name Input -->
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="module_name" class="form-label">Module Name</label>
                                                    <input type="text" class="form-control module_name" name="module_name[]" required>
                                                </div>
                                            </div>

                                            <!-- Module Deadline Input -->
                                            <div class="col-md-5">
                                                <div class="form-group">
                                                    <label for="deadline" class="form-label">Module Deadline</label>
                                                    <input type="date" class="form-control deadline" name="deadline[]" required>
                                                </div>
                                            </div>

                                            <!-- Add Row Button -->
                                            <div class="col-md-2 text-center">
                                                <button type="button" class="btn btn-success add-row-btn mt-4">
                                                    <i class="fas fa-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <!-- Submit Button -->
                                <button type="submit" class="btn btn-primary mt-3" name="submit_module">Add Module</button>


                                <script>
                                    $(document).ready(function() {
                                        // Add event listener for "+" button
                                        $('#moduleRowsContainer').on('click', '.add-row-btn', function() {
                                            // Clone the module row
                                            let newRow = $(this).closest('.module-row').clone();

                                            // Clear the input fields in the new row
                                            newRow.find('input').val('');

                                            // Append the new row to the container
                                            $('#moduleRowsContainer').append(newRow);
                                        });
                                    });
                                </script>

                                <!-- Submit Button -->
                                <!-- <button type="submit" class="btn btn-primary mt-3" name="submit_module">Add Module</button> -->
                            </form>
                        </div>
                    </div>
                </div>
            </div>


        </div>
    </div>
</div>


<!-- Select2 CSS -->
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/css/select2.min.css" rel="stylesheet" />

<!-- Select2 JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.1.0-rc.0/js/select2.min.js"></script>

<script>
    $(document).ready(function() {
        $('#program_id').select2({
            placeholder: "Select a Program", // Optional placeholder
            allowClear: true // Allows clearing the selection
        });
    });
</script>