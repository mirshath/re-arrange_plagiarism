<?php
session_start();
include("../database/connection.php");
include("includes/header.php");

// Ensure the user is logged in and has the correct role
// if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
//     // Redirect to login page if not logged in or role is incorrect
//     echo '<script>window.location.href = "../login";</script>';
//     exit();
// }

if (
    !isset($_SESSION['role']) ||
    ($_SESSION['role'] !== 'super_admin' && $_SESSION['role'] !== 'it_department' && $_SESSION['role'] !== 'exam_department')
) {
    echo '<script>window.location.href = "../login";</script>';
    exit();
}






// Fetch module data from the database
// $query = "SELECT * FROM module_table";
// $query = "SELECT * FROM module_table m
// INNER JOIN program_table p ON m.program_id = p.id
// INNER JOIN batch_table b ON m.bactch_id = p.id


// ";


$query = "SELECT m.*, p.program_name, b.batch_name 
          FROM module_table m
          INNER JOIN program_table p ON m.program_id = p.id
          INNER JOIN batch_table b ON m.batch_id = b.id";



$result = mysqli_query($conn, $query);

$moduleData = array();
if ($result) {
    while ($row = mysqli_fetch_assoc($result)) {
        $moduleData[] = $row;
    }
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
                                                <label for="program" class="form-label">Program:</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select id="program" name="program" class="form-select">
                                                    <!-- Program options will be populated dynamically -->
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Batch Dropdown -->
                                    <div class="mb-2">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="batch" class="form-label">Batch:</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select id="batch" name="batch" class="form-select" disabled>
                                                    <option value="">Select Batch</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Module Dropdown -->
                                    <div class="mb-2">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="module" class="form-label">Module:</label>
                                            </div>
                                            <div class="col-md-8">
                                                <select id="module" name="module" class="form-select" disabled>
                                                    <option value="">Select Module</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Deadline Date -->
                                    <div class="mb-2">
                                        <div class="row">
                                            <div class="col-md-4">
                                                <label for="deadline" class="form-label">Deadline Date:</label>
                                            </div>
                                            <div class="col-md-8">
                                                <input type="date" id="deadline" name="deadline" class="form-control" disabled />
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Insert Button -->
                                    <div class="mb-2" id="insertButtonContainer" style="display: none; text-align: right;">
                                        <button type="button" id="insertDeadlineButton" class="btn btn-primary">Insert Deadline</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Add DataTables CSS -->
                <link href="https://cdn.datatables.net/1.13.3/css/jquery.dataTables.min.css" rel="stylesheet">

                <!-- Add jQuery (required for DataTables) -->
                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

                <!-- Add DataTables JS -->
                <script src="https://cdn.datatables.net/1.13.3/js/jquery.dataTables.min.js"></script>


                <div class="card mt-5">
                    <div class="card-header">
                        <h6 class="mb-0">Module Data</h6>
                    </div>
                    <div class="card-body">
                        <table id="moduleTable" class="table table-bordered table-striped">
                            <thead>
                                <tr>
                                    <th>#</th> <!-- Index Column -->
                                    <th>Program Name</th>
                                    <th>Batch</th>
                                    <th>Module</th>
                                    <th>Deadline</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                // Initialize the index counter
                                $index = 1;
                                // Loop through the fetched module data and calculate status
                                foreach ($moduleData as $module) {
                                    $deadline = new DateTime($module['deadline']);
                                    $currentDate = new DateTime();
                                    $difference = $deadline->diff($currentDate);
                                    $status = ($deadline > $currentDate) ? "Upcoming" : "Expired";

                                    echo "<tr>";
                                    echo "<td>" . $index++ . "</td>";  // Display the index
                                    echo "<td>" . $module['program_name'] . "</td>";
                                    echo "<td>" . $module['batch_name'] . "</td>";
                                    echo "<td>" . $module['module_name'] . "</td>";
                                ?>
                                    <td>
                                        <span class="deadline-text"><?php echo $module['deadline']; ?></span> &nbsp;&nbsp;&nbsp;&nbsp;
                                        <input type="date" class="deadline-input p-1" value="<?php echo $module['deadline']; ?>" style="display:none" />
                                        <button class="btn btn-primary btn-sm edit-btn" data-id="<?php echo $module['id']; ?>" data-deadline="<?php echo $module['deadline']; ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                    </td>

                                    <?php
                                    if ($status === "Upcoming") {
                                        echo "<td><span class='badge bg-success'>Upcoming (" . $difference->days . " days left)</span></td>";
                                    } else {
                                        echo "<td><span class='badge bg-danger'>Expired (" . $difference->days . " days ago)</span></td>";
                                    }
                                    ?>

                                <?php
                                    echo "</tr>";
                                }
                                ?>
                            </tbody>
                        </table>

                    </div>
                </div>

                <script>
                    $(document).ready(function() {
                        // Initialize DataTables
                        $('#moduleTable').DataTable({
                            // Add custom settings if needed
                            paging: true, // Pagination enabled
                            searching: true, // Enable searching
                            ordering: true, // Enable sorting
                            info: true, // Show table information
                        });

                        // When the edit button is clicked
                        $('.edit-btn').click(function() {
                            const moduleId = $(this).data('id');
                            const currentDeadline = $(this).data('deadline');

                            // Find the corresponding deadline text and input field
                            const deadlineText = $(this).closest('td').find('.deadline-text');
                            const deadlineInput = $(this).closest('td').find('.deadline-input');

                            // Toggle the visibility of text and input
                            deadlineText.toggle(); // Hide the text
                            deadlineInput.toggle(); // Show the input field with current deadline value

                            // Change the button text to 'Save' once it's clicked for editing
                            $(this).text('Save').removeClass('btn-warning').addClass('btn-success');

                            // When the Save button is clicked, save the new deadline
                            $(this).click(function() {
                                const newDeadline = deadlineInput.val();

                                if (newDeadline) {
                                    // Make the AJAX request to save the new deadline
                                    $.ajax({
                                        url: 'deadline_fetching/update_deadline.php', // Your PHP script to handle the update
                                        method: 'POST',
                                        data: {
                                            module_id: moduleId,
                                            new_deadline: newDeadline
                                        },
                                        success: function(response) {
                                            // If the update is successful, update the displayed deadline
                                            deadlineText.text(newDeadline);
                                            deadlineText.toggle(); // Show the updated text
                                            deadlineInput.toggle(); // Hide the input field
                                            alert('Deadline updated successfully!');
                                            window.location.reload(); // Redirect to the same page
                                        },
                                        error: function(error) {
                                            console.error("Error updating deadline:", error);
                                            alert('Failed to update deadline.');
                                        }
                                    });
                                } else {
                                    alert('Please select a valid deadline.');
                                }
                            });
                        });
                    });
                </script>
            </div>
        </div>
        <!-- End of Main Content -->
    </div>
    <!-- End of Content Wrapper -->
</div>

<!-- Fetching and processing data with AJAX -->
<script>
    $(document).ready(function() {
        // Fetch programs on page load
        $.ajax({
            url: 'deadline_fetching/fetch_programs.php',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#program').append('<option value="">Select Program</option>');
                $.each(data, function(index, item) {
                    $('#program').append('<option value="' + item.id + '">' + item.program_name + '</option>');
                });
            },
            error: function(error) {
                console.error("Error fetching programs:", error);
            }
        });

        // Fetch batches when program is selected
        $('#program').change(function() {
            const programId = $(this).val();
            if (programId) {
                $.ajax({
                    url: 'deadline_fetching/fetch_batches.php',
                    method: 'GET',
                    data: {
                        program_id: programId
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#batch').prop('disabled', false).empty().append('<option value="">Select Batch</option>');
                        $.each(data, function(index, item) {
                            $('#batch').append('<option value="' + item.id + '">' + item.batch_name + '</option>');
                        });
                    },
                    error: function(error) {
                        console.error("Error fetching batches:", error);
                    }
                });
            } else {
                $('#batch').prop('disabled', true).empty().append('<option value="">Select Batch</option>');
                $('#module').prop('disabled', true).empty().append('<option value="">Select Module</option>');
                $('#deadline').prop('disabled', true).val('');
                $('#insertButtonContainer').hide();
            }
        });

        // Fetch modules when batch is selected
        $('#batch').change(function() {
            const batchId = $(this).val();
            if (batchId) {
                $.ajax({
                    url: 'deadline_fetching/fetch_modules.php',
                    method: 'GET',
                    data: {
                        batch_id: batchId
                    },
                    dataType: 'json',
                    success: function(data) {
                        $('#module').prop('disabled', false).empty().append('<option value="">Select Module</option>');
                        $.each(data, function(index, item) {
                            $('#module').append('<option value="' + item.id + '">' + item.module_name + '</option>');
                        });
                    },
                    error: function(error) {
                        console.error("Error fetching modules:", error);
                    }
                });
            } else {
                $('#module').prop('disabled', true).empty().append('<option value="">Select Module</option>');
                $('#deadline').prop('disabled', true).val('');
                $('#insertButtonContainer').hide();
            }
        });

        // Enable deadline date and show the insert button when a module is selected
        $('#module').change(function() {
            const moduleId = $(this).val();
            if (moduleId) {
                $('#deadline').prop('disabled', false); // Enable the deadline input
                $('#insertButtonContainer').show(); // Show the insert button
            } else {
                $('#deadline').prop('disabled', true).val(''); // Disable the deadline input
                $('#insertButtonContainer').hide(); // Hide the insert button
            }
        });

        $('#insertDeadlineButton').click(function() {
            const programId = $('#program').val();
            const batchId = $('#batch').val();
            const moduleId = $('#module').val();
            const deadline = $('#deadline').val();

            // Make sure all required values are present
            if (programId && batchId && moduleId && deadline) {
                $.ajax({
                    url: 'deadline_fetching/insert_deadline.php', // The PHP file to process the update
                    method: 'POST',
                    data: {
                        program_id: programId,
                        batch_id: batchId,
                        module_id: moduleId,
                        deadline: deadline
                    },
                    success: function(response) {
                        alert('Deadline inserted successfully!');
                        window.location.reload();
                    },
                    error: function(error) {
                        console.error("Error inserting deadline:", error);
                    }
                });
            } else {
                alert('Please fill all the fields.');
            }
        });
    });
</script>

<?php
include("includes/footer.php");
?>