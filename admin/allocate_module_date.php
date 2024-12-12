<?php
session_start();
include("../database/connection.php");
include("includes/header.php");


// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    // Redirect to login page if not logged in or role is incorrect
    echo '<script>window.location.href = "../login";</script>';
    exit();
}


?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

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

            <div class="p-3" style="font-size: 12px;">

                <div class="row mb-5">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex align-items-center" style="height: 60px;">
                                <span
                                    class="bg-dark text-white rounded-circle p-2 d-flex align-items-center justify-content-center"
                                    style="width: 30px; height: 30px;">
                                    <i class="fas fa-plus-circle"></i>
                                </span> &nbsp;&nbsp;&nbsp;&nbsp;
                                <h6 class="mb-0 me-2">Dynamic Dropdown with Deadline Update</h6>
                            </div>

                            <div class="card-body">

                                <form id="dynamicForm">
                                    <!-- Program Dropdown -->
                                    <div class="mb-2">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="batch" class="form-label">Program:</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select id="program" name="program" class="form-select">
                                                    <option value="">Select Program</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="batch" class="form-label">Module:</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select id="module" name="module" class="form-select">
                                                    <option value="">Select a Module</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="batch" class="form-label">Deadline:</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="date" id="deadline" name="deadline" class="form-select" disabled>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 text-end">
                                            <button type="button" id="updateDeadline" class="btn btn-primary" disabled>Update Deadline</button>
                                        </div>
                                    </div>

                                </form>

                                <script>
                                    $(document).ready(function() {
                                        // Fetch Programs
                                        fetchPrograms();

                                        function fetchPrograms() {
                                            $.ajax({
                                                url: 'update_module_date_fd/fetch_programs.php',
                                                method: 'GET',
                                                dataType: 'json',
                                                success: function(response) {
                                                    $('#program').html('<option value="">Select a Program</option>');
                                                    response.forEach(program => {
                                                        $('#program').append(`<option value="${program.id}">${program.program_name}</option>`);
                                                    });
                                                }
                                            });
                                        }

                                        // Fetch Modules based on selected Program
                                        $('#program').on('change', function() {
                                            const programId = $(this).val();
                                            if (programId) {
                                                $.ajax({
                                                    url: 'update_module_date_fd/fetch_modules.php',
                                                    method: 'GET',
                                                    data: {
                                                        program_id: programId
                                                    },
                                                    dataType: 'json',
                                                    success: function(response) {
                                                        $('#module').prop('disabled', false).html('<option value="">Select a Module</option>');
                                                        response.forEach(module => {
                                                            $('#module').append(`<option value="${module.id}">${module.module_name}</option>`);
                                                        });
                                                    }
                                                });
                                            } else {
                                                $('#module').prop('disabled', true).html('<option value="">Select a Module</option>');
                                                $('#deadline').val('').prop('disabled', true);
                                                $('#updateDeadline').prop('disabled', true);
                                            }
                                        });

                                        // Fetch Deadline for selected Module
                                        $('#module').on('change', function() {
                                            const moduleId = $(this).val();
                                            if (moduleId) {
                                                $.ajax({
                                                    url: 'update_module_date_fd/fetch_deadline.php',
                                                    method: 'GET',
                                                    data: {
                                                        module_id: moduleId
                                                    },
                                                    dataType: 'json',
                                                    success: function(response) {
                                                        $('#deadline').val(response.deadline).prop('disabled', false);
                                                        $('#updateDeadline').prop('disabled', false);
                                                    }
                                                });
                                            } else {
                                                $('#deadline').val('').prop('disabled', true);
                                                $('#updateDeadline').prop('disabled', true);
                                            }
                                        });

                                        // Update Deadline
                                        $('#updateDeadline').on('click', function() {
                                            const moduleId = $('#module').val();
                                            const newDeadline = $('#deadline').val();
                                            if (moduleId && newDeadline) {
                                                $.ajax({
                                                    url: 'update_module_date_fd/update_deadline.php',
                                                    method: 'POST',
                                                    data: {
                                                        module_id: moduleId,
                                                        deadline: newDeadline
                                                    },
                                                    success: function(response) {
                                                        alert(response);
                                                        location.reload(); // Refresh the page
                                                    }
                                                });
                                            }
                                        });
                                    });
                                </script>
                            </div>
                        </div>
                    </div>
                </div>


                <div class="row mb-5">
                    <div class="col-md">

                        <div class="card mt-4">
                            <div class="card-header">
                                Module Details
                            </div>
                            <div class="card-body">
                                <div class="table-responsive">
                                    <!-- Add DataTable with id "dataTable" -->
                                    <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                        <thead>
                                            <tr>
                                                <th>Program Name</th>
                                                <th>Module Name</th>
                                                <th>Deadline</th>
                                                <th>Status</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <?php
                                            // Query to fetch program and module details
                                            $query = "
                                                    SELECT 
                                                        m.id AS module_id, 
                                                        p.program_name, 
                                                        m.module_name, 
                                                        m.deadline 
                                                    FROM 
                                                        module_table m 
                                                    INNER JOIN 
                                                        program_table p 
                                                    ON 
                                                        m.program_id = p.id";

                                            $result = mysqli_query($conn, $query);

                                            while ($row = mysqli_fetch_assoc($result)) {
                                                // Calculate status
                                                $deadline = new DateTime($row['deadline']);
                                                $currentDate = new DateTime();
                                                $difference = $deadline->diff($currentDate);
                                                $status = $deadline > $currentDate ? "Upcoming" : "Expired";

                                                echo "<tr>";
                                                echo "<td>" . $row['program_name'] . "</td>";
                                                echo "<td>" . $row['module_name'] . "</td>";
                                                echo "<td>" . $row['deadline'] . "</td>";

                                                // Display badge for status
                                                if ($status === "Upcoming") {
                                                    echo "<td><span class='badge bg-success'>Upcoming (" . $difference->days . " days left)</span></td>";
                                                } else {
                                                    echo "<td><span class='badge bg-danger'>Expired (" . $difference->days . " days ago)</span></td>";
                                                }

                                                echo "</tr>";
                                            }
                                            ?>
                                        </tbody>
                                    </table>

                                </div>
                            </div>
                        </div>

                        <!-- DataTable Initialization Script -->
                        <script>
                            $(document).ready(function() {
                                // Initialize DataTable
                                $('#dataTable').DataTable({
                                    "paging": true, // Enable pagination
                                    "searching": true, // Enable search
                                    "ordering": true, // Enable sorting
                                    "info": true, // Show info text
                                    "lengthChange": true, // Enable page size dropdown
                                    "language": {
                                        "search": "Filter records:", // Customize search box text
                                        "lengthMenu": "Display _MENU_ records per page"
                                    }
                                });
                            });
                        </script>

                        <!-- Include DataTables CSS -->
                        <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">

                        <!-- Include DataTables JS -->
                        <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
                        <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
                    </div>
                </div>
            </div>
        </div>
        <!-- End of Main Content -->
    </div>
    <!-- End of Content Wrapper -->
</div>
<!-- End of Page Wrapper -->



<script src="../index.js"></script>

<!-- Page level plugins -->
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<link rel="stylesheet" href="./vendor/datatables/dataTables.bootstrap4.min.css">
<!-- Page level custom scripts -->
<script src="js/demo/datatables-demo.js"></script>

</body>

</html>