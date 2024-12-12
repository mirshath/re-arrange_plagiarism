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
                    <h4 class="h4 mb-0 text-gray-800">Allocate Checkers</h4>
                </div>

                <div class="row mb-5">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex align-items-center" style="height: 60px;">
                                <span
                                    class="bg-dark text-white rounded-circle p-2 d-flex align-items-center justify-content-center"
                                    style="width: 30px; height: 30px;">
                                    <i class="fas fa-plus-circle"></i>
                                </span> &nbsp;&nbsp;&nbsp;&nbsp;
                                <h6 class="mb-0 me-2">Allocate Checker</h6>
                            </div>

                            <div class="card-body">
                                <form id="programBatchForm">
                                    <div class="mb-2">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="program" class="form-label">Program:</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select id="program" name="program" class="form-select">
                                                    <option value="">Select Program</option>
                                                    <?php
                                                    // Fetch programs from the program_table
                                                    $programQuery = "SELECT * FROM program_table";
                                                    $programResult = $conn->query($programQuery);
                                                    while ($programRow = $programResult->fetch_assoc()) {
                                                        echo "<option value='{$programRow['id']}'>{$programRow['program_name']}</option>";
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="mb-2">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="batch" class="form-label">Batch:</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select id="batch" name="batch" class="form-select">
                                                    <option value="">Select Batch</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="row">
                                        <div class="col-md-12 text-end">
                                            <button type="button" class="btn btn-primary"
                                                onclick="fetchStudentList()">Submit</button>
                                            <button type="button" id="allocateButton" class="btn btn-secondary">Allocate
                                                Checkers
                                            </button>
                                        </div>
                                    </div>

                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="container-fluid">
                <div class="card shadow mb-4" style="font-size: 13px;">
                    <div class="card-header d-flex align-items-center" style="height: 60px;">
                        <span
                            class="bg-dark text-white rounded-circle p-2 d-flex align-items-center justify-content-center"
                            style="width: 30px; height: 30px;">
                            <i class="fas fa-list"></i>
                        </span> &nbsp;&nbsp;&nbsp;&nbsp;
                        <h6 class="mb-0">Students</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table id="result" class="table table-bordered table-striped display" width="100%"
                                cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <!-- <th>Select</th> -->
                                        <th><input type="checkbox" id="select-all"></th>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>Date of Birth</th>
                                        <th>BMS Email</th>
                                        <th>Phone</th>
                                        <th>Allocate</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <!-- Data will be dynamically fetched here -->
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <script>
                $(document).ready(function () {
                    // When the "Select All" checkbox is clicked
                    $('#select-all').on('click', function () {
                        var isChecked = $(this).prop('checked'); // Get the state of the "Select All" checkbox

                        // Check or uncheck all checkboxes with the class "student-checkbox"
                        $('.student-checkbox').prop('checked', isChecked);
                    });

                    // When any student checkbox is clicked
                    $('.student-checkbox').on('click', function () {
                        // If all student checkboxes are checked, check the "Select All" checkbox, otherwise uncheck it
                        var allChecked = $('.student-checkbox:checked').length === $('.student-checkbox').length;
                        $('#select-all').prop('checked', allChecked);
                    });
                });
            </script>

            <!-- Include DataTable Libraries -->
            <link rel="stylesheet" href="https://cdn.datatables.net/1.13.5/css/jquery.dataTables.min.css">
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.datatables.net/1.13.5/js/jquery.dataTables.min.js"></script>

            <!-- /.container-fluid -->
        </div>
        <!-- End of Main Content -->
    </div>
    <!-- End of Content Wrapper -->
</div>
<!-- End of Page Wrapper -->

<script>
    function fetchStudentList() {
        const formData = new FormData(document.getElementById('programBatchForm'));

        fetch('../fetching_for_admin/fetch_students.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.text())
            .then(data => {
                const table = $('#result').DataTable();
                table.clear().destroy(); // Destroy existing DataTable instance
                $('#result tbody').html(data); // Populate the table body
                $('#result').DataTable({ // Reinitialize DataTable
                    paging: true,
                    searching: true,
                    ordering: true,
                    responsive: true
                });
            })
            .catch(error => console.error('Error:', error));
    }

    $(document).ready(function () {
        // Initialize DataTable without data on page load
        $('#result').DataTable({
            paging: true,
            searching: true,
            ordering: true,
            responsive: true
        });
    });


    $(document).ready(function () {
        $('#program').on('change', function () {
            var programId = $(this).val();

            if (programId) {
                $.ajax({
                    url: '../fetching_for_admin/fetch_batches.php',
                    type: 'POST',
                    data: {
                        program_id: programId
                    },
                    success: function (data) {
                        $('#batch').html(data);
                    }
                });
            } else {
                $('#batch').html('<option value="">Select Batch</option>');
            }
        });


        // ================================================ 
        $('#batch').on('change', function () {
            const batchId = $(this).val();
            const alertMessage = '<div id="batchAlert" class="text-danger mt-2">This batch has already been allocated!</div>';

            // Remove any previous alert messages
            $('#batchAlert').remove();

            if (batchId) {
                $.ajax({
                    url: '../fetching_for_admin/check_batch_allocation.php', // PHP file to check batch allocation
                    type: 'POST',
                    data: {
                        batch_id: batchId
                    },
                    success: function (response) {
                        const result = JSON.parse(response);
                        if (result.status === 'allocated') {
                            $('#batch').after(alertMessage);
                        }
                    },
                    error: function () {
                        console.error('Error checking batch allocation.');
                    }
                });
            }
        });

        // ================================================
    });


    document.getElementById('allocateButton').addEventListener('click', function () {
        const allocateButton = document.getElementById('allocateButton');
        allocateButton.disabled = true; // Disable the button
        allocateButton.textContent = 'Please wait...'; // Change button text

        const selectedStudents = [];
        document.querySelectorAll('.student-checkbox:checked').forEach((checkbox) => {
            selectedStudents.push(checkbox.value);
        });

        const batchId = document.getElementById('batch').value; // Get batch ID

        if (!batchId) {
            alert('Please select a batch.');
            allocateButton.disabled = false; // Re-enable the button
            allocateButton.textContent = 'Allocate Checkers'; // Reset button text
            return;
        }

        if (selectedStudents.length === 0) {
            alert('Please select at least one student to allocate.');
            allocateButton.disabled = false; // Re-enable the button
            allocateButton.textContent = 'Allocate Checkers'; // Reset button text
            return;
        }

        fetch('allocate_checkers.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({
                student_ids: selectedStudents,
                batch_id: batchId // Include batch_id
            })
        })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'error') {
                    alert('Errors occurred:\n' + data.messages.join('\n'));
                    allocateButton.disabled = false; // Re-enable the button
                    allocateButton.textContent = 'Allocate Checkers'; // Reset button text
                } else {
                    alert('Allocation successful and emails sent!');
                    window.location.reload(); // Refresh the page on success
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
                allocateButton.disabled = false; // Re-enable the button
                allocateButton.textContent = 'Allocate Checkers'; // Reset button text
            });
    });
</script>

<script src="../index.js"></script>

<!-- Page level plugins -->
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>

<link rel="stylesheet" href="./vendor/datatables/dataTables.bootstrap4.min.css">
<!-- Page level custom scripts -->
<script src="js/demo/datatables-demo.js"></script>


</body>

</html>