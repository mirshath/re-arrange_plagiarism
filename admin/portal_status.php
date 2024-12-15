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



// Fetch current portal status from the database
$query = "SELECT portal_status FROM portal WHERE id = 1";
$result = $conn->query($query);
$portalStatus = '';

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $portalStatus = $row['portal_status'];
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
                                <div class="form-group">
                                    <label for="portal_status">Portal Status:</label>
                                    <div class="form-check">
                                        <input
                                            type="radio"
                                            name="portal_status"
                                            id="portal_status_on"
                                            value="on"
                                            class="form-check-input"
                                            <?php echo ($portalStatus === 'on') ? 'checked' : ''; ?>
                                            onchange="updatePortalStatus(this.value)">
                                        <label class="form-check-label" for="portal_status_on">On</label>
                                    </div>
                                    <div class="form-check">
                                        <input
                                            type="radio"
                                            name="portal_status"
                                            id="portal_status_off"
                                            value="off"
                                            class="form-check-input"
                                            <?php echo ($portalStatus === 'off') ? 'checked' : ''; ?>
                                            onchange="updatePortalStatus(this.value)">
                                        <label class="form-check-label" for="portal_status_off">Off</label>
                                    </div>
                                </div>
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

<!-- AJAX Script -->
<script>
    function updatePortalStatus(status) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "update_portal_status.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4 && xhr.status === 200) {
                console.log(xhr.responseText);
                location.reload(); // Reload to reflect changes (optional)
            }
        };
        xhr.send("portal_status=" + status);
    }
</script>

<?php
$conn->close();
?>