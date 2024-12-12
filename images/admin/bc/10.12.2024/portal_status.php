<?php
session_start();
include("../database/connection.php");
include("includes/header.php");




// Ensure the user is logged in and has the correct role
// if ($_SESSION['role'] !== 'super_admin') {
//     // header("Location: ../login.php");
//     echo '<script>window.location.href = "../login.php";</script>';
//     exit();
// }


// Ensure the user is logged in and has the correct role
if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'super_admin') {
    // Redirect to login page if not logged in or role is incorrect
    echo '<script>window.location.href = "../login";</script>';
    exit();
}



// Fetch current portal status from the database
$query = "SELECT portal_status FROM portal WHERE id = 1";
$result = $conn->query($query);
$portalStatus = '';

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $portalStatus = $row['portal_status']; // Store the portal status
}

// Handle form submission to update portal status
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $newStatus = $_POST['portal_status'];

    // Update the portal status in the database
    $updateQuery = "UPDATE portal SET portal_status = ? WHERE id = 1";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("s", $newStatus);
    $stmt->execute();

    // Refresh the page to reflect changes
    header("Location: portal_status");
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
            <div class="container-fluid">
                <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h4 class="h4 mb-0 text-gray-800">Portal Status Management</h4>
                </div>

                <!-- Portal Status Form -->
                <div class="row mb-5">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex align-items-center" style="height: 60px;">
                                <span class="bg-dark text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                    <i class="fas fa-toggle-on"></i>
                                </span> &nbsp;&nbsp;&nbsp;&nbsp;
                                <h6 class="mb-0 me-2">Update Portal Status</h6>
                            </div>

                            <div class="card-body">
                                <form action="portal_status.php" method="POST">
                                    <div class="form-group">
                                        <label for="portal_status">Portal Status:</label>
                                        <select name="portal_status" id="portal_status" class="form-control">
                                            <option value="on" <?php echo ($portalStatus === 'on') ? 'selected' : ''; ?>>On</option>
                                            <option value="off" <?php echo ($portalStatus === 'off') ? 'selected' : ''; ?>>Off</option>
                                        </select>
                                    </div>
                                    <button type="submit" class="btn btn-primary mt-3">Update Status</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Show Portal Status -->
                <?php if ($portalStatus === 'off') : ?>
                    <div class="alert alert-danger" role="alert">
                        <h5 class="alert-heading">Portal is currently closed!</h5>
                        <p>The portal is not available for use at the moment.</p>
                    </div>
                <?php else: ?>
                    <div class="alert alert-success" role="alert">
                        <h5 class="alert-heading">Portal is Open!</h5>
                        <p>The portal is available for use.</p>
                    </div>
                <?php endif; ?>
            </div>
            <!-- End Page Content -->
        </div>
        <!-- End Main Content -->
    </div>
    <!-- End Content Wrapper -->
</div>
<!-- End Page Wrapper -->

<!-- Page level plugins -->
<script src="vendor/datatables/jquery.dataTables.min.js"></script>
<script src="vendor/datatables/dataTables.bootstrap4.min.js"></script>
<link rel="stylesheet" href="./vendor/datatables/dataTables.bootstrap4.min.css">

<!-- Page level custom scripts -->
<script src="js/demo/datatables-demo.js"></script>

</body>

</html>

<?php
$conn->close();
?>