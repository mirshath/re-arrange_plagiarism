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

// Initialize result to an empty variable
$result = null;
$checker_name = ''; // Variable to store the checker's name

if (isset($_POST['submit'])) {
    // Get the selected checker ID from the form
    $checker_id = $_POST['checker_id'];

    // Fetch the checker's name based on the selected checker ID
    $checker_query = "SELECT checker_name FROM checkers WHERE id = ?";
    $checker_stmt = $conn->prepare($checker_query);
    $checker_stmt->bind_param("i", $checker_id);
    $checker_stmt->execute();
    $checker_result = $checker_stmt->get_result();

    if ($checker_result && $checker_result->num_rows > 0) {
        $checker_row = $checker_result->fetch_assoc();
        $checker_name = $checker_row['checker_name']; // Store the checker's name
    }

    // Query to fetch the student and checker details, including program_name and module_name
    $query = "
    SELECT 
        ac.student_id as ac_student_id, ac.submitted_status, osd.name, osd.student_id, osd.email, bt.batch_name, 
        c.checker_name, ssf.student_id as ssf_student_id, ssf.submitted_at, ssf.Documents, 
        ssf.module_id, ssf.Documents_1, ssf.Documents_2, ssf.checker_downlaoded_at, ssf.checked_status, 
        mt.module_name, pt.program_name
    FROM allocate_checker ac
    JOIN old_student_db osd ON ac.student_id = osd.id
    JOIN checkers c ON ac.checker_id = c.id
    LEFT JOIN student_submitted_form ssf ON ac.student_reg_id = ssf.student_id
    LEFT JOIN module_table mt ON ssf.module_id = mt.id
    LEFT JOIN program_table pt ON pt.id = mt.program_id
    LEFT JOIN batch_table bt ON mt.batch_id = bt.id
    WHERE ac.checker_id = ?
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $checker_id);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!-- DataTables CSS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<!-- DataTables JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>

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
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h4 class="h4 mb-0 text-gray-800">All Records with Submitted Documents</h4>
                    <h6 class="h6 mb-0 text-gray-800">Checkers and Student's Documents</h6>
                </div>
            </div>

            <div class="container-fluid">
                <div class="card shadow mb-4" style="font-size: 13px;">
                    <div class="card-header d-flex align-items-center" style="height: 60px;">
                        <span class="bg-dark text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                            <i class="fas fa-list"></i>
                        </span> &nbsp;&nbsp;&nbsp;&nbsp;
                        <h6 class="mb-0">Student and Checker Details</h6>
                    </div>
                    <div class="card-body">
                        <form id="checkerForm" action="" method="POST" style="width: 50%;" class="d-flex align-items-center">
                            <label for="checker_id" class="mr-2">Select Checker:</label>
                            <select name="checker_id" id="checker_id" class="form-control mr-2">
                                <?php
                                // Fetch checker names from checkers table
                                $results = $conn->query("SELECT * FROM checkers");

                                // Check if there are checkers
                                if ($results->num_rows > 0) {
                                    // Populate the dropdown
                                    while ($row = $results->fetch_assoc()) {
                                        $selected = ($row['checker_name'] == $checker_name) ? 'selected' : '';
                                        echo "<option value='{$row['id']}' $selected>{$row['checker_name']}</option>";
                                    }
                                } else {
                                    echo "<option>No checkers available</option>";
                                }
                                ?>
                            </select>
                            <button type="submit" class="btn-sm btn btn-primary" name="submit">Submit</button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="container-fluid mt-4">
                <div class="card shadow mb-4" style="font-size: 13px;">
                    <div class="card-header d-flex align-items-center" style="height: 60px;">
                        <span class="bg-dark text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                            <i class="fas fa-list"></i>
                        </span> &nbsp;&nbsp;&nbsp;&nbsp;
                        <h6 class="mb-0">Current Modules</h6>
                    </div>
                    <div class="card-body">
                        <?php if ($result && $result->num_rows > 0): ?>
                            <h5>Checker: <?php echo htmlspecialchars($checker_name); ?></h5>
                            <div class="table-responsive">
                                <table id="detailsTable" class="display" style="font-size: 11px;">
                                    <?php
                                    // Fetch distinct modules for the dropdown
                                    $moduleQuery = "SELECT DISTINCT mt.module_name FROM module_table mt INNER JOIN student_submitted_form ssf ON mt.id = ssf.module_id WHERE ssf.checker_id = ?";

                                    // Using prepared statements for safety
                                    $moduleStmt = $conn->prepare($moduleQuery);
                                    $moduleStmt->bind_param("s", $checker_id);
                                    $moduleStmt->execute();
                                    $moduleResult = $moduleStmt->get_result();
                                    $modules = $moduleResult->fetch_all(MYSQLI_ASSOC);
                                    $moduleStmt->close();

                                    // ------------------------------------------------- 


                                    // ------------------------------------------------------------------ 

                                    // ------------------------------------------------------------------ 
                                    ?>
                                    <thead>
                                        <tr>
                                            <th>Student ID</th>
                                            <th>Student Name</th>
                                            <th>Student Email</th>
                                            <th>Program</th>
                                            <th>Batch</th>
                                            <!-- <th>Module</th> -->
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
                                            <th>Submitted Status</th>
                                            <th>Submitted at</th>
                                            <th>Uploaded Doc</th>
                                            <th>Uploaded Doc 1</th>
                                            <th>Uploaded Doc 2</th>
                                            <th>Checker Downloaded at</th>
                                            <th>Checked/Not</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($row = $result->fetch_assoc()): ?>


                                            <?php
                                            // Fetch the documents and display them (you can format the output as per your needs)
                                            $document = $row['Documents'];
                                            $document1 = $row['Documents_1'];
                                            $document2 = $row['Documents_2'];

                                            // Generate download links for each document
                                            $documentLink = $document ? "<a href='download_document.php?file=" . urlencode($document) . "' download>Download</a>" : '';
                                            $document1Link = $document1 ? "<a href='download_document.php?file=" . urlencode($document1) . "' download>Download</a>" : '';
                                            $document2Link = $document2 ? "<a href='download_document.php?file=" . urlencode($document2) . "' download>Download</a>" : '';

                                            ?>

                                            <tr>
                                                <td><?php echo htmlspecialchars($row['student_id']); ?></td>
                                                <td><?php echo htmlspecialchars($row['name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['email']); ?></td>
                                                <td><?php echo htmlspecialchars($row['program_name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['batch_name']); ?></td>
                                                <td><?php echo htmlspecialchars($row['module_name']); ?></td>
                                                <td><?php echo $row['submitted_status'] === 'submitted'
                                                        ? '<span class="badge badge-success">Submitted</span>'
                                                        : '<span class="badge badge-danger">Not Yet</span>'; ?></td>
                                                <td><?php echo htmlspecialchars($row['submitted_at']); ?></td>
                                                <!-- <td><?php echo $row['Documents'] ? "<a href='download_document.php?file=" . urlencode($row['Documents']) . "'>Download</a>" : ''; ?></td>
                                                <td><?php echo $row['Documents_1'] ? "<a href='download_document.php?file=" . urlencode($row['Documents_1']) . "'>Download</a>" : ''; ?></td>
                                                <td><?php echo $row['Documents_2'] ? "<a href='download_document.php?file=" . urlencode($row['Documents_2']) . "'>Download</a>" : ''; ?></td> -->
                                                <td><?= $documentLink ?></td>
                                                <td><?= $document1Link ?></td>
                                                <td><?= $document2Link ?></td>
                                                <td><?php echo htmlspecialchars($row['checker_downlaoded_at']); ?></td>
                                                <td><?php echo $row['checked_status'] === 'checked'
                                                        ? '<span class="badge badge-success">Checked</span>'
                                                        : '<span class="badge badge-danger">Not Checked</span>'; ?></td>
                                            </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php else: ?>
                            <p>No data found for the selected checker.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#detailsTable').DataTable({
            "paging": true,
            "searching": true
        });
    });
</script>


<script>
    // Initialize DataTable
    $(document).ready(function() {
        var table = $('#detailsTable').DataTable();

        // Filter by module selection
        $('#searchModule').on('change', function() {
            var selectedModule = this.value;
            table.column(5).search(selectedModule).draw(); // Apply filter on module column (index 4)
        });
    });
</script>



<?php include("includes/footer.php"); ?>