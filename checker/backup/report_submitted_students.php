<?php
session_start();
include("../database/connection.php");
include("includes/header.php");

// Ensure the user is logged in and has the correct role
// if ($_SESSION['role'] !== 'plagiarism_checker') {
//     echo '<script>window.location.href = "../login.php";</script>';
//     exit();
// }


// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'plagiarism_checker') {
    // Redirect to login page if not logged in or role is incorrect
    echo '<script>window.location.href = "../login.php";</script>';
    exit();
}

if (!isset($_SESSION['id'])) {
    echo '<script>window.location.href = "../login.php";</script>';
    exit();
}



// Get the session email
$checker_id = $_SESSION['id'];



// Query to fetch student data based on checker_id from the student_submitted_form table
// $sql = "SELECT * FROM student_submitted_form WHERE checker_id = ?  AND checked_status = 'pending'";
$sql = "SELECT ssf.*, mt.module_name FROM student_submitted_form ssf INNER JOIN module_table mt ON ssf.module_id = mt.id WHERE ssf.checker_id = ? AND ssf.checked_status = 'pending'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $checker_id);  // Binding session email to the query
$stmt->execute();
$result = $stmt->get_result();

// Fetching all student data
$students = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

?>

<!-- Page Wrapper -->
<div id="wrapper">
    <!-- Sidebar -->
    <?php include("nav.php"); ?>
    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">
        <!-- Main Content -->
        <div id="content">
            <!-- Topbar ssss -->
            <?php include("includes/topnav.php"); ?>

            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

            <!-- Table to display student data -->
            <div class="container-fluid">
                <div class="card shadow mb-4" style="font-size: 13px;">
                    <div class="card-header d-flex align-items-center" style="height: 60px;">
                        <span class="bg-dark text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                            <i class="fas fa-list"></i>
                        </span> &nbsp;&nbsp;&nbsp;&nbsp;
                        <h6 class="mb-0">Current Submitted Students and Documents</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Student ID</th>
                                        <th>Name</th>
                                        <th>BMS Email</th>
                                        <th>Module Name</th>
                                        <th>Documents/Reports</th>
                                        <th>Submitted_at</th>
                                        <th>Downloaded_at</th>
                                        <!-- <th>Action</th> -->
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Displaying student data in table rows
                                    if ($students) {
                                        $index = 1;
                                        foreach ($students as $student) {


                                            // Apply yellow background color if checked_status is 'checked'
                                            $rowClass = ($student['checked_status'] == 'checked') ? 'highlight-yellow' : '';


                                            echo '<tr class="' . $rowClass . '">';
                                            echo '<td>' . $index . '</td>';
                                            echo '<td>' . htmlspecialchars($student['student_id']) . '</td>';
                                            echo '<td>' . htmlspecialchars($student['name_full']) . '</td>';
                                            echo '<td>' . htmlspecialchars($student['bms_email']) . '</td>';
                                            echo '<td>' . htmlspecialchars($student['module_name']) . '</td>';

                                            if (!empty($student['Documents'])) {
                                                $document_path = $student['Documents'];
                                                echo '<td class="text-center"><a href="../uploads/documents/' . htmlspecialchars($document_path) . '" target="_blank" style="text-decoration:none;" class="download-link" data-id="' . $student['student_id'] . '" data-module-id="' . htmlspecialchars($student['module_id']) . '">
                                                        Download &nbsp;<i class="fas fa-download"></i>
                                                        </a></td>';
                                            } else {
                                                echo '<td>No Document</td>';
                                            }

                                            echo '<td>' . htmlspecialchars($student['submitted_at']) . '</td>';
                                            echo '<td>' . htmlspecialchars($student['checker_downlaoded_at']) . '</td>';



                                    ?>

                                            <td>
                                                <select class="status-dropdown" data-student-id="<?php echo htmlspecialchars($student['student_id']); ?>">
                                                    <option disabled value="pending" <?php echo ($student['checked_status'] == 'pending' ? 'selected' : ''); ?>>Pending</option>
                                                    <option value="checked" <?php echo ($student['checked_status'] == 'checked' ? 'selected' : ''); ?>>Checked</option>
                                                </select>

                                                <!-- Update Button (Initially Hidden) -->
                                                <button class="btn btn-sm submit-status" data-student-id="<?php echo htmlspecialchars($student['student_id']); ?>" style="display: none;">
                                                    <a href="mailto:<?php echo htmlspecialchars($student['bms_email']); ?>?subject=Regarding Submission Report&body=Hello <?php echo htmlspecialchars($student['name_full']); ?>,%0A%0AYour submission has been reviewed.&bcc=ccemail@example.com"
                                                        class="btn btn-sm btn-primary" style="text-decoration:none;">Update</a>
                                                </button>
                                            </td>

                                            <script>
                                                $(document).ready(function() {
                                                    // When dropdown value changes
                                                    $('.status-dropdown').change(function() {
                                                        var studentId = $(this).data('student-id');
                                                        var selectedStatus = $(this).val(); // Get the selected status

                                                        // Check if the selected status is 'checked'
                                                        if (selectedStatus === 'checked') {
                                                            // Show the update button for this student
                                                            $(this).closest('td').find('.submit-status').show();
                                                        } else {
                                                            // Hide the update button if not 'checked'
                                                            $(this).closest('td').find('.submit-status').hide();
                                                        }
                                                    });
                                                });
                                            </script>


                                    <?php
                                            //  ----------- submit btn ----------- 



                                            echo '</tr>';


                                            if (!empty($student['submitted_at_2nd_time']) && !empty($student['Documents_1'])) {
                                                $document_path = $student['Documents_1'];
                                                echo '<tr>';
                                                echo '<td colspan="5" class="text-right"><strong>Second Submission Time:</strong> </td>';
                                                echo '<td class="text-center"><a href="../uploads/documents/' . htmlspecialchars($document_path) . '" target="_blank" style="text-decoration:none;" class="download-link" data-id="' . $student['student_id'] . '" data-module-id="' . htmlspecialchars($student['module_id']) . '">
                                                        Download &nbsp;<i class="fas fa-download"></i>
                                                        </a></td>';
                                                echo '<td>' . htmlspecialchars($student['submitted_at_2nd_time']) . '</td>';
                                                echo '</tr>';
                                            }

                                            if (!empty($student['submitted_at_3rd_time']) && !empty($student['Documents_2'])) {
                                                $document_path = $student['Documents_2'];
                                                echo '<tr>';
                                                echo '<td colspan="5" class="text-right"><strong>Third Submission Time:</strong></td>';
                                                echo '<td class="text-center"><a href="../uploads/documents/' . htmlspecialchars($document_path) . '" target="_blank" style="text-decoration:none;" class="download-link" data-id="' . $student['student_id'] . '" data-module-id="' . htmlspecialchars($student['module_id']) . '">
                                                        Download &nbsp;<i class="fas fa-download"></i>
                                                        </a></td>';
                                                echo '<td>' . htmlspecialchars($student['submitted_at_3rd_time']) . '</td>';
                                                echo '</tr>';
                                            }

                                            $index++;
                                        }
                                    } else {
                                        echo '<tr><td colspan="6">No students found.</td></tr>';
                                    }
                                    ?>
                                </tbody>

                            </table>



                        </div>
                    </div>
                </div>
            </div>

            <style>
                .highlight-yellow {
                    background-color: yellow;
                }
            </style>

            <!-- DataTables Scripts -->
            <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
            <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
            <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
            <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">

            <script>
                $(document).on('click', '.submit-status', function() {
                    var studentId = $(this).data('student-id'); // Get the student ID
                    var status = $(this).closest('tr').find('.status-dropdown').val(); // Get the selected value from the dropdown

                    $.ajax({
                        url: 'update_student_status.php', // PHP file to handle the update
                        type: 'POST',
                        data: {
                            student_id: studentId,
                            status: status
                        },
                        success: function(response) {
                            if (response === 'success') {
                                alert('Status updated successfully!');
                                // window.location.href = document.referrer;    {here refreshing and move theat suces page }
                                location.reload();
                            } else {
                                alert('Error updating status.');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log('AJAX Error:', error);
                        }
                    });
                });




                $(document).ready(function() {
                    $('#dataTable').DataTable({
                        "paging": true,
                        "searching": true,
                        "ordering": true,
                        "info": true,
                        "lengthMenu": [5, 10, 25, 50, 100], // Allow the user to select how many entries to show
                        "language": {
                            "search": "Searchss students:" // Change the search label
                        }
                    });
                });
            </script>

            <script>
                $(document).on('click', '.download-link', function(e) {
                    e.preventDefault();

                    var studentId = $(this).data('id');
                    var checkerId = <?php echo $_SESSION['id']; ?>;
                    var moduleId = $(this).data('module-id');

                    // Send the student_id and checker_id to the PHP script
                    $.ajax({
                        url: 'update_viewed_time.php',
                        type: 'POST',
                        data: {
                            student_id: studentId,
                            checker_id: checkerId,
                            module_id: moduleId
                        },
                        success: function(response) {
                            console.log('Response:', response);
                            if (response === 'success') {
                                alert('Downloaded time updated successfully!');
                            } else {
                                alert('Error updating viewed time: ' + response);
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log('AJAX Error:', error);
                        }
                    });

                    // Open the document in a new tab as before
                    window.open($(this).attr('href'), '_blank');
                });
            </script>

        </div>
        <!-- End of Main Content -->
    </div>
    <!-- End of Content Wrapper -->
</div>
<!-- End of Page Wrapper -->

</body>

</html>