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
                    <h4 class="h4 mb-0 text-gray-800">Checker Allocations</h4>
                </div>

                <!-- Checker Dropdown and Allocation Table -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <div class="card">
                            <div class="card-header d-flex align-items-center" style="height: 60px;">
                                <span class="bg-dark text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                    <i class="fas fa-user-check"></i>
                                </span> &nbsp;&nbsp;&nbsp;&nbsp;
                                <h6 class="mb-0">Select Checker</h6>
                            </div>
                            <div class="card-body">
                                <!-- Checker Selection Dropdown -->
                                <form action="" method="post">
                                    <div class="form-group">
                                        <label for="checkerSelect">Checker Name</label>
                                        <select id="checkerSelect" class="form-control">
                                            <option value="">Select a Checker</option>
                                            <?php
                                            $checkersQuery = "SELECT id, checker_name FROM checkers";
                                            $checkersResult = $conn->query($checkersQuery);
                                            while ($row = $checkersResult->fetch_assoc()) {
                                                echo "<option value='" . $row['id'] . "'>" . htmlspecialchars($row['checker_name']) . "</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Allocation Details Table -->
                <div class="">
                    <div class="card shadow mb-4" style="font-size: 13px;">
                        <div class="card-header d-flex align-items-center" style="height: 60px;">
                            <span class="bg-dark text-white rounded-circle p-2 d-flex align-items-center justify-content-center" style="width: 30px; height: 30px;">
                                <i class="fas fa-list"></i>
                            </span> &nbsp;&nbsp;&nbsp;&nbsp;
                            <h6 class="mb-0">Allocation Details</h6>
                        </div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped" id="dataTable" width="100%" cellspacing="0">
                                    <tbody id="allocationDetails">

                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
            <!-- /.container-fluid -->
        </div>
        <!-- End of Main Content -->
    </div>
    <!-- End of Content Wrapper -->
</div>
<!-- End of Page Wrapper -->


<!-- AJAX to Fetch Checker Allocations in Real-Time -->
<script>
    $(document).ready(function() {
        $('#checkerSelect').change(function() {
            var checkerId = $(this).val();
            if (checkerId) {
                $.ajax({
                    url: 'fetch_allocations_list.php',
                    type: 'POST',
                    data: {
                        checker_id: checkerId
                    },
                    success: function(response) {
                        $('#allocationDetails').html(response);
                    }
                });
            } else {
                $('#allocationDetails').html('');
            }
        });
    });
</script>


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