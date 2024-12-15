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
    ($_SESSION['role'] !== 'super_admin' && $_SESSION['role'] !== 'it_department')
) {
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

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4 p-4">
                <h4 class="h4 mb-0 text-gray-800">Checkers and Allocated Students </h4>
            </div>

            <div class="container">
                <!-- Dropdown to select checker -->
                <div class="form-group">
                    <label for="checker">Select Checker</label>
                    <select class="form-control" id="checker" name="checker">
                        <option value="">-- Select Checker --</option>
                        <?php
                        $query = "SELECT DISTINCT ac.checker_id, c.* 
                                  FROM allocate_checker ac
                                  JOIN checkers c ON ac.checker_id = c.id";
                        $result = mysqli_query($conn, $query);
                        while ($row = mysqli_fetch_assoc($result)) {
                            echo "<option value='" . $row['checker_id'] . "'>" . htmlspecialchars($row['checker_name']) . "</option>";
                        }
                        ?>
                    </select>
                </div>

                <!-- Container for student tables -->
                <div id="studentsContainer" class="mt-4" style="font-size: 13px;">
                    <p class="text-muted">Select a checker to view allocated students.</p>
                </div>
            </div>
        </div>
        <!-- End of Main Content -->
    </div>
    <!-- End of Content Wrapper -->
</div>
<!-- End of Page Wrapper -->

<!-- DataTables CSS and JS CDN -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
    $(document).ready(function () {
        $('#checker').change(function () {
            var checkerId = $(this).val();

            if (checkerId != "") {
                $.ajax({
                    url: "fetch_students.php",
                    method: "POST",
                    data: { checker_id: checkerId },
                    success: function (response) {
                        $('#studentsContainer').html(response);

                        // Initialize DataTables for each dynamically created table
                        $('.datatable').DataTable({
                            responsive: true,
                            dom: 'Bfrtip',
                            buttons: ['copy', 'csv', 'excel', 'pdf', 'print']
                        });
                    },
                    error: function (xhr, status, error) {
                        console.error("Error: " + error);
                        $('#studentsContainer').html('<p class="text-danger">Failed to load students. Please try again.</p>');
                    }
                });
            } else {
                $('#studentsContainer').html('<p class="text-muted">Select a checker to view allocated students.</p>');
            }
        });
    });
</script>
</body>
</html>
