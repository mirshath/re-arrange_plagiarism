<?php
session_start();
include("../database/connection.php");
include("includes/header.php");

// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'plagiarism_checker') {
    echo '<script>window.location.href = "../login.php";</script>';
    exit();
}

if (!isset($_SESSION['id'])) {
    echo '<script>window.location.href = "../login.php";</script>';
    exit();
}

$checker_id = $_SESSION['id'];

$sql = "SELECT 
    s.id AS student_id,
    s.student_id AS student_reg_id,
    s.name AS student_name,
    s.bms_email AS student_email,
    ac.submitted_status,
    ac.created_at,
    p.program_name,
    b.batch_name
FROM 
    allocate_checker ac, 
JOIN 
    student_allocations sa ON ac.student_id = sa.student_id
JOIN 
    program_table p ON sa.program_id = p.id
JOIN 
    batch_table b ON sa.batch_id = b.id
WHERE 
    ac.checker_id = ?
ORDER BY 
    p.program_name, b.batch_name, ac.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $checker_id);
$stmt->execute();
$result = $stmt->get_result();

// Group the results by program and then by batch
$programs = [];
while ($row = $result->fetch_assoc()) {
    $program_name = $row['program_name'];
    $batch_name = $row['batch_name'];

    $programs[$program_name][$batch_name][] = $row;
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
                    <h4 class="h4 mb-0 text-gray-800">Module Management - Allocated Students</h4>
                </div>

                <!-- Allocated Students Table by Program and Batch -->
                <div class="container-fluid">
                    <?php if (!empty($programs)): ?>
                        <?php foreach ($programs as $program_name => $batches): ?>
                            <div class="card shadow mb-4">
                                <div class="card-header bg-primary text-white">
                                    <h5 class="mb-0">Program: <?php echo htmlspecialchars($program_name); ?></h5>
                                </div>
                                <div class="card-body">
                                    <?php foreach ($batches as $batch_name => $students): ?>
                                        <div class="card shadow mb-4" style="font-size: 13px;">
                                            <div class="card-header d-flex align-items-center" style="font-size: 14px;">
                                                <span class="bg-dark text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                                    <i class="fas fa-users"></i>
                                                </span>
                                                &nbsp;&nbsp;
                                                <h6 class="mb-0">Batch: <?php echo htmlspecialchars($batch_name); ?></h6>
                                            </div>
                                            <div class="card-body">
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-striped" id="dataTable_<?php echo htmlspecialchars($program_name . '_' . $batch_name); ?>" width="100%" cellspacing="0">
                                                        <thead>
                                                            <tr>
                                                                <th>#</th>
                                                                <th>Student ID</th>
                                                                <th>Student Name</th>
                                                                <th>Student BMS Email</th>
                                                                <th>Submission Status</th>
                                                                <th>Created At</th>
                                                            </tr>
                                                        </thead>
                                                        <tbody>
                                                            <?php foreach ($students as $index => $row): ?>
                                                                <tr>
                                                                    <td><?php echo $index + 1; ?></td>
                                                                    <td><?php echo htmlspecialchars($row['student_reg_id']); ?></td>
                                                                    <td><?php echo htmlspecialchars($row['student_name']); ?></td>
                                                                    <td>
                                                                        <a href="mailto:<?php echo htmlspecialchars($row['student_email']); ?>">
                                                                            <?php echo htmlspecialchars($row['student_email']); ?>
                                                                        </a>
                                                                    </td>
                                                                    <td>
                                                                        <span class="badge" style="background-color: <?php echo ($row['submitted_status'] === 'not_yet') ? 'red' : 'green'; ?>;">
                                                                            <?php echo htmlspecialchars($row['submitted_status']); ?>
                                                                        </span>
                                                                    </td>
                                                                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                                                                </tr>
                                                            <?php endforeach; ?>
                                                        </tbody>
                                                    </table>
                                                </div>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="alert alert-info text-center">No students allocated to you.</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- DataTables Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap4.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap4.min.css">

<script>
    $(document).ready(function() {
        <?php foreach ($programs as $program_name => $batches): ?>
            <?php foreach ($batches as $batch_name => $students): ?>
                $('#dataTable_<?php echo htmlspecialchars($program_name . '_' . $batch_name); ?>').DataTable({
                    "paging": true,
                    "searching": true,
                    "ordering": true,
                    "info": true,
                    "language": {
                        "search": "Search students:"
                    }
                });
            <?php endforeach; ?>
        <?php endforeach; ?>
    });
</script>
