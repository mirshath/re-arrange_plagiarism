<?php
session_start();
include('./database/connection.php');
include('./includes/header.php');


// Fetch the portal status
$query = "SELECT portal_status FROM portal WHERE id = 1";
$result = $conn->query($query);
$portalStatusMessage = '';

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['portal_status'] === 'off') {
        $portalStatusMessage = 'The portal is not available for use at the moment.';
    }
}
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">



<?php if (!empty($portalStatusMessage)): ?>
    <div class="container d-flex justify-content-center align-items-center" style="height: 100vh;">
        <div class="alert alert-danger text-center" role="alert">
            <h5 class="alert-heading">Portal is currently closed!</h5>
            <p><?php echo $portalStatusMessage; ?></p>
        </div>
    </div>
<?php else: ?>
    <div class="container mt-5 mb-5">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-12 col-12">
                <img src="./images/PLGERISM.png" alt="Google Form Header Banner" class="img-fluid"
                    style="border-radius:10px 10px 0 0">
                <div class="rounded shadow-sm" style="background-color:#ddd3d3c2; padding: 12px;">
                    <form id="studentForm" enctype="multipart/form-data">
                        <div class="card lft_border">
                            <div class="card-body shadow">

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="mb-2">
                                                            <label for="student_id" class="form-label">Student BMS ID <span
                                                                    style="color:red;">*</span></label>
                                                            <!-- <input class="form-control" type="text" id="student_id" name="student_id" required placeholder="Enter your Student ID"> -->
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="mb-2">
                                                            <!-- <label for="student_id" class="form-label">Student BMS ID <span style="color:red;">*</span></label> -->
                                                            <input class="form-control" type="text" id="student_id"
                                                                name="student_id" required
                                                                placeholder="Enter your Student ID">
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="col-md-12">
                                                <div class="row">
                                                    <div class="col-md-4">
                                                        <div class="mb-2">
                                                            <label for="dob" class="form-label">DOB <span
                                                                    style="color:red;">*</span></label>
                                                            <!-- <input class="form-control" type="date" id="dob" name="dob" required> -->
                                                        </div>
                                                    </div>
                                                    <div class="col">
                                                        <div class="mb-2">
                                                            <!-- <label for="dob" class="form-label">Date of Birth <span style="color:red;">*</span></label> -->
                                                            <input class="form-control" type="date" id="dob" name="dob"
                                                                required>
                                                        </div>
                                                        <div id="student_id_alert" class="alert alert-danger mt-2"
                                                            style="display: none;">
                                                            <strong>Warning!</strong> Please Check Your Student ID and DOB.
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>
                                    </div>
                                </div>


                                <button type="button" class="btn btn-primary float-end" id="next_button">
                                    Next <i class="fas fa-arrow-right"></i>
                                </button>

                            </div>
                        </div>



                        <div class="card mt-2 lft_border" id="d_hide" style="display: none;">
                            <div class="card-body shadow">
                                <p>Welcome! Your details are verified.</p>

                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- 1st row  -->
                                        <div class="row">
                                            <div class="col-md-4">

                                                <div class="mb-2">
                                                    <label for="name_in_full" class="form-label">Name in Full <span
                                                            style="color:red;">*</span></label>
                                                    <!-- <input class="form-control" id="name_in_full" name="name_in_full" readonly> -->
                                                </div>
                                            </div>
                                            <div class="col">

                                                <div class="mb-2">
                                                    <!-- <label for="name_in_full" class="form-label">Name in Full <span style="color:red;">*</span></label> -->
                                                    <input class="form-control" id="name_in_full" name="name_in_full"
                                                        readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 2 row  -->
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-2">
                                                    <label for="bms_email_address" class="form-label">BMS Email <span
                                                            style="color:red;">*</span></label>
                                                    <!-- <input class="form-control" id="bms_email_address" name="bms_email_address" readonly> -->
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-2">
                                                    <!-- <label for="bms_email_address" class="form-label">BMS Email Address <span style="color:red;">*</span></label> -->
                                                    <input class="form-control" id="bms_email_address"
                                                        name="bms_email_address" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 3 row  -->
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-2">
                                                    <label for="phone_no" class="form-label">Phone No</label>
                                                    <!-- <input class="form-control" id="phone_no" name="phone_no" readonly> -->
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-2">
                                                    <!-- <label for="phone_no" class="form-label">Phone Number</label> -->
                                                    <input class="form-control" id="phone_no" name="phone_no" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 4 row  -->
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-2">
                                                    <label for="program_id" class="form-label">Program</label>
                                                    <!-- <input class="form-control" id="program_id" name="program_id" readonly> -->
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-2">
                                                    <!-- <label for="program_id" class="form-label">Program</label> -->
                                                    <input class="form-control" id="program_id" name="program_id" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 5 row  -->
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-2">
                                                    <label for="batch_id" class="form-label">Batch</label>
                                                    <!-- <input class="form-control" id="batch_name" name="batch_name" readonly> -->
                                                    <!-- <input type="hidden" class="form-control" id="bacth_id_no" name="bacth_id_no" readonly> -->
                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-2">
                                                    <!-- <label for="batch_id" class="form-label">Batch</label> -->
                                                    <input class="form-control" id="batch_name" name="batch_name" readonly>
                                                    <input type="hidden" class="form-control" id="bacth_id_no"
                                                        name="bacth_id_no" readonly>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- 6 row  -->
                                        <div class="row">
                                            <div class="col-md-4">
                                                <div class="mb-2">
                                                    <label for="module_id" class="form-label">Module</label>

                                                </div>
                                            </div>
                                            <div class="col">
                                                <div class="mb-2" id="module_section" style="display: none;">

                                                    <select class="form-control" id="module_id" name="module_id" disabled>
                                                        <option value="">Select Module</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>



                                        <!-- ---------  -->
                                        <div class="mb-2" id="mdl_deadline" style="display: none;">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="module_deadline" class="form-label">Module Deadlines</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" id="module_deadline" class="form-control"
                                                        name="module_deadline" readonly>
                                                    <!-- Error message container -->
                                                    <div id="deadlineError"
                                                        style="color: red; display: none; margin-top: 5px;">The Module
                                                        deadline has been expired</div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-2" id="uploadSection" style="display: none;">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="documentUpload" class="form-label">Upload Document</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="file" id="documentUpload" name="document"
                                                        class="form-control">
                                                    <!-- Error message container -->
                                                    <div id="fileError" style="color: red; display: none; margin-top: 5px;">
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <input type="hidden" name="std_auto_id" id="std_auto_id">

                                        <div class="p-0">
                                            <button type="submit" class="btn btn-primary mt-4 mb-4 w-100"
                                                id="submitBtn">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>


<?php include('./All-modal.php');  ?>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Bootstrap JS and dependencies (Optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>



<script src="./index.js"></script>

