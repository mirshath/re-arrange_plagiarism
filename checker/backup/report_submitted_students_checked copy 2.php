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
// $sql = "SELECT * FROM student_submitted_form WHERE checker_id = ? AND checked_status = 'checked'";
$sql = "SELECT ssf.*, mt.module_name FROM student_submitted_form ssf INNER JOIN module_table mt ON ssf.module_id = mt.id WHERE ssf.checker_id = ? AND ssf.checked_status = 'checked'";

$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $checker_id);  // Binding session email to the query
$stmt->execute();
$result = $stmt->get_result();

// Fetching all student data
$students = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();

// Fetch distinct modules for the dropdown
$moduleQuery = "SELECT DISTINCT mt.module_name FROM module_table mt INNER JOIN student_submitted_form ssf ON mt.id = ssf.module_id WHERE ssf.checker_id = ?";
$moduleStmt = $conn->prepare($moduleQuery);
$moduleStmt->bind_param("s", $checker_id);
$moduleStmt->execute();
$moduleResult = $moduleStmt->get_result();
$modules = $moduleResult->fetch_all(MYSQLI_ASSOC);
$moduleStmt->close();
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

            <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet">

            <!-- Table to display student data -->
            <div class="container-fluid">
                <div class="card shadow mb-4" style="font-size: 13px;">
                    <div class="card-header d-flex align-items-center" style="height: 60px;">
                        <span class="bg-dark text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                            <i class="fas fa-list"></i>
                        </span> &nbsp;&nbsp;&nbsp;&nbsp;
                        <h6 class="mb-0">Current Submitted Students Checked</h6>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">

                            <table class="table table-bordered table-striped" id="dataTable" style="font-size: 11px;" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Student ID <input type="text" style="font-size: 11px;" class="form-control" id="searchStudentID" placeholder="Search by Student ID"></th>
                                        <th>Name <input type="text" id="searchName" style="font-size: 11px;" class="form-control" placeholder="Search by Name"></th>
                                        <th>BMS Email <input type="text" id="searchEmail" style="font-size: 11px;" class="form-control" placeholder="Search by Email"></th>
                                        <th>Module
                                            <select id="searchModule" style="font-size: 11px;" class="form-control">
                                                <option value="">All Modules</option>
                                                <?php foreach ($modules as $module): ?>
                                                    <option value="<?php echo htmlspecialchars($module['module_name']); ?>">
                                                        <?php echo htmlspecialchars($module['module_name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </th>
                                        <th>Documents/Reports</th>
                                        <th>Submitted_at</th>
                                        <th>Email</th>
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
                                                echo '<td class="text-center"><a href="../uploads/documents/' . htmlspecialchars($document_path) . '" target="_blank" style="text-decoration:none;" class="download-link" data-id="' . $student['student_id'] . '">
                                                        Download &nbsp;<i class="fas fa-download"></i>
                                                        </a></td>';
                                            } else {
                                                echo '<td>No Document</td>';
                                            }
                                            echo '<td>' . htmlspecialchars($student['submitted_at']) . '</td>';
                                            // echo '<td>' . htmlspecialchars($student['checker_downlaoded_at']) . '</td>';

                                            // echo '<td><a href="mailto:' . htmlspecialchars($student['bms_email']) . '?subject=Regarding Submission Report&body=Hello ' . htmlspecialchars($student['name_full']) . ',%0A%0AYour submission has been reviewed." class="btn btn-sm btn-primary" style="text-decoration:none;">Send Email</a></td>';
                                            echo '<td class="text-center"><a href="mailto:' . htmlspecialchars($student['bms_email']) . '?subject=Regarding Submission Report&body=Hello ' . htmlspecialchars($student['name_full']) . ',%0A%0AYour submission has been reviewed." class="btn btn-sm btn-primary" style="text-decoration:none;">
                                            <i class="fas fa-envelope"></i> 
                                          </a></td>';


                                            //  ----------- submit drpdwn btn ----------- 
                                            // New Column for Pending/Checked Dropdown and Submit Button
                                            //     echo '<td>';
                                            //     echo '<select class="status-dropdown" data-student-id="' . htmlspecialchars($student['student_id']) . '">';
                                            //     echo '<option value="pending" ' . ($student['checked_status'] == 'pending' ? 'selected' : '') . '>Pending</option>';
                                            //     echo '<option value="checked" ' . ($student['checked_status'] == 'checked' ? 'selected' : '') . '>Checked</option>';
                                            //     echo '</select>';
                                            //     // echo '&nbsp;<button class="btn btn-sm btn-success submit-status" data-student-id="' . htmlspecialchars($student['student_id']) . '">Submit</button>';
                                            //     echo '&nbsp;<button class="btn btn-sm btn-success submit-status" data-student-id="' . htmlspecialchars($student['student_id']) . '">
                                            //    update
                                            //   </button>';

                                            //     echo '</td>';


                                            //  ----------- submit btn ----------- 

                                            echo '</tr>';



                                            if (!empty($student['submitted_at_2nd_time']) && !empty($student['Documents_1'])) {
                                                $document_path = $student['Documents_1'];
                                                echo '<tr>';
                                                echo '<td colspan="5" class="text-right"><strong>Second Submission Time:</strong> </td>';
                                                echo '<td class="text-center"><a href="../uploads/documents/' . htmlspecialchars($document_path) . '" target="_blank" style="text-decoration:none;" class="download-link" data-id="' . $student['student_id'] . '">
                                                        Download &nbsp;<i class="fas fa-download"></i>
                                                        </a></td>';
                                                echo '<td>' . htmlspecialchars($student['submitted_at_2nd_time']) . '</td>';
                                                echo '</tr>';
                                            }

                                            if (!empty($student['submitted_at_3rd_time']) && !empty($student['Documents_2'])) {
                                                $document_path = $student['Documents_2'];
                                                echo '<tr>';
                                                echo '<td colspan="5" class="text-right"><strong>Third Submission Time:</strong></td>';
                                                echo '<td class="text-center"><a href="../uploads/documents/' . htmlspecialchars($document_path) . '" target="_blank" style="text-decoration:none;" class="download-link" data-id="' . $student['student_id'] . '">
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


            <script>
                // Manual search function
                $(document).ready(function() {
                    // Search by student ID
                    $('#searchStudentID').on('keyup', function() {
                        var value = $(this).val().toLowerCase();
                        $("#dataTable tbody tr").filter(function() {
                            $(this).toggle($(this).find('td:nth-child(2)').text().toLowerCase().indexOf(value) > -1);
                        });
                    });

                    // Search by Name
                    $('#searchName').on('keyup', function() {
                        var value = $(this).val().toLowerCase();
                        $("#dataTable tbody tr").filter(function() {
                            $(this).toggle($(this).find('td:nth-child(3)').text().toLowerCase().indexOf(value) > -1);
                        });
                    });

                    // Search by Email
                    $('#searchEmail').on('keyup', function() {
                        var value = $(this).val().toLowerCase();
                        $("#dataTable tbody tr").filter(function() {
                            $(this).toggle($(this).find('td:nth-child(4)').text().toLowerCase().indexOf(value) > -1);
                        });
                    });
                    // Search by modle
                    $('#searchModule').on('change', function() {
                        var value = $(this).val().toLowerCase();
                        $("#dataTable tbody tr").each(function() {
                            var mainRow = $(this);
                            var studentId = mainRow.find('td:nth-child(2)').text(); // Get student ID

                            if (mainRow.find('td').length >= 5) { // Check if it's a main row
                                var isMatch = mainRow.find('td:nth-child(5)').text().toLowerCase().indexOf(value) > -1 || value === "";
                                mainRow.toggle(isMatch);

                                // Find and toggle related submission rows
                                var nextRows = mainRow.nextUntil('tr:has(td:nth-child(5))');
                                if (isMatch) {
                                    nextRows.show();
                                } else {
                                    nextRows.hide();
                                }
                            }
                        });
                    });
                });
            </script>


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

            <!-- <script>
                $(document).on('click', '.download-link', function(e) {
                    e.preventDefault(); // Prevent the default behavior of the link

                    var studentId = $(this).data('id'); // Get the student ID from the data attribute of the clicked link
                    var checkerId = <?php echo $_SESSION['id']; ?>; // Get the checker_id from the session

                    // Send the student_id and checker_id to the PHP script
                    $.ajax({
                        url: 'update_viewed_time.php', // The PHP file that will handle the update
                        type: 'POST',
                        data: {
                            student_id: studentId,
                            checker_id: checkerId
                        },
                        success: function(response) {
                            console.log('Response:', response); // Log the response for debugging
                            if (response === 'success') {
                                alert('Downloaded time updated successfully!');
                            } else {
                                alert('Error updating viewed time: ' + response); // Show the error message if not successful
                            }
                        },
                        error: function(xhr, status, error) {
                            console.log('AJAX Error:', error); // Log any AJAX errors
                        }
                    });

                    // Open the document in a new tab as before
                    window.open($(this).attr('href'), '_blank');
                });
            </script> -->

        </div>
        <!-- End of Main Content -->
    </div>
    <!-- End of Content Wrapper -->
</div>
<!-- End of Page Wrapper -->

</body>

</html>