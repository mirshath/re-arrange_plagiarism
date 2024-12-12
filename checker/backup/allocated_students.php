<?php
session_start();
include("../database/connection.php");
include("includes/header.php");

// Ensure the user is logged in and has the correct role
if ($_SESSION['role'] !== 'plagiarism_checker') {
    echo '<script>window.location.href = "../login.php";</script>';
    exit();
}

if (!isset($_SESSION['id'])) {
    echo '<script>window.location.href = "../login.php";</script>';
    exit();
}
$checker_id = $_SESSION['id'];

// $checker_id = $_SESSION['id']; // Retrieve the logged-in checker ID
// echo "Checker ID: " . $checker_id;


$sql = "SELECT 
    s.id AS student_id,
    s.student_id AS student_reg_id,
    s.name AS student_name,
    s.bms_email AS student_email,
    s.attempt,
    ac.*,
    p.program_name, -- Assuming program_table has a column called 'program_name'
    b.batch_name
FROM 
    allocate_checker ac
JOIN 
    old_student_db s ON ac.student_id = s.id
JOIN 
    program_table p ON s.program_id = p.id

JOIN 
    batch_table b ON s.batch_id  = b.id
    
WHERE 
     ac.checker_id = ? ";

//  AND ac.display = '0'

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $checker_id);
$stmt->execute();
$result = $stmt->get_result();

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
                    <h4 class="h4 mb-0 text-gray-800">Module Management - Allocated Students</h4>
                </div>

                <!-- Allocated Students Table -->
                <div class="container-fluid">
                    <div class="card shadow mb-4" style="font-size: 13px;">
                        <div class="card-header d-flex align-items-center" style="height: 60px; font-size: 14px;">
                            <span class="bg-dark text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                <i class="fas fa-list"></i>
                            </span>
                            &nbsp;&nbsp;&nbsp;&nbsp;
                            <h6 class="mb-0">Students Allocated to You</h6>
                        </div>

                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                    <thead>
                                        <tr>
                                            <th>#</th>
                                            <th>Student ID</th>
                                            <th>Student Name</th>
                                            <th>program</th>
                                            <th>Batch</th>
                                            <th>Student BMS Email</th>
                                            <th>Submission Status</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        $index = 1; // Initialize the index variable
                                        if ($result->num_rows > 0): ?>
                                            <?php while ($row = $result->fetch_assoc()): ?>
                                                <tr>
                                                    <td style='width: 50px;'><?php echo $index; ?></td> <!-- Display row index with fixed width -->
                                                    <td><?php echo htmlspecialchars($row['student_reg_id']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['program_name']); ?></td>
                                                    <td><?php echo htmlspecialchars($row['batch_name']); ?></td>
                                                    <td><a style="text-decoration: none;" href="mailto:<?php echo htmlspecialchars($row['student_email']); ?>"><?php echo htmlspecialchars($row['student_email']); ?></a></td>
                                                    <!-- <td><?php echo htmlspecialchars($row['submitted_status']); ?></td> -->
                                                    <!-- <td style="color: <?php echo ($row['submitted_status'] == 'not_yet') ? 'red' : 'green'; ?>;">
                                                        <?php echo htmlspecialchars($row['submitted_status']); ?>
                                                    </td> -->

                                                    <td>
                                                        <span class="badge" style="background-color: <?php echo ($row['submitted_status'] == 'not_yet') ? 'red' : 'green'; ?>;">
                                                            <?php echo htmlspecialchars($row['submitted_status']); ?>
                                                        </span>
                                                        <!-- <span class="badge" style="background-color: <?php echo ($row['attempt']) ? 'green' : 'red'; ?>;">
                                                            <?php echo htmlspecialchars($row['attempt']); ?>
                                                        </span> -->

                                                        <?php if ($row['attempt'] != 0): ?>
                                                            <span class="badge" style="background-color: <?php echo ($row['attempt']) ? 'green' : 'red'; ?>;">
                                                                <?php echo htmlspecialchars($row['attempt']); ?>
                                                            </span>
                                                        <?php endif; ?>

                                                    </td>


                                                </tr>
                                                <?php $index++; // Increment the index 
                                                ?>
                                            <?php endwhile; ?>
                                        <?php else: ?>
                                            <tr>
                                                <td colspan="7" class="text-center">No students allocated to you.</td>
                                            </tr>

                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- /.container-fluid -->
            </div>
            <!-- End of Page Content -->
        </div>
        <!-- End of Main Content -->
    </div>
    <!-- End of Content Wrapper -->
</div>
<!-- End of Page Wrapper -->

<!-- DataTables Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">

<script>
    $(document).ready(function() {
        $('#dataTable').DataTable({
            "paging": true,
            "searching": true,
            "ordering": true,
            "info": true,
            "language": {
                "search": "Search students:"
            }
        });
    });
</script>

</body>

</html>

<?php
// Close connection
$stmt->close();
$conn->close();
?>