<?php
session_start();
// insert_student.php
include('./database/connection.php'); // Include your database connection file
include('./includes/header.php'); // Include your database connection file


// Fetch the portal status from the database
$query = "SELECT portal_status FROM portal WHERE id = 1"; // Adjust the query if needed based on your database structure
$result = $conn->query($query);
$portalStatusMessage = '';

// If the portal status is found, check if it is 'off'
if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    if ($row['portal_status'] === 'off') {
        $portalStatusMessage = 'The portal is not available for use at the moment.';
    }
}




?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<style>
    .wrap-input-1 {
        position: relative;
        width: 100%;
    }

    .input {
        width: 100%;
        padding-right: 60px;
        /* Space for the button */
    }

    .btn-edit {
        position: absolute;
        top: 50%;
        right: 10px;
        /* Adjust as needed */
        transform: translateY(-50%);
        padding: 5px 10px;
        font-size: 0.9em;
        border: none;
        background-color: #04356a;
        /* Button background color */
        color: white;
        /* Text color */
        cursor: pointer;
        border-radius: 5px;
        display: flex;
        /* Flex for alignment */
        align-items: center;
        /* Center the icon vertically */
    }

    .btn-edit:hover {
        background-color: #0056b3;
        /* Darker shade on hover */
    }

    /* Tooltip styles */
    .tooltip-text {
        display: none;
        /* Hidden by default */
        position: absolute;
        bottom: 100%;
        /* Position above the button */
        left: 50%;
        /* Center the tooltip */
        transform: translateX(-50%);
        /* Center adjustment */
        background-color: #000;
        /* Tooltip background */
        color: white;
        /* Tooltip text color */
        padding: 3px 5px;
        /* Padding */
        border-radius: 4px;
        /* Rounded corners */
        font-size: 12px;
        /* Font size */
        white-space: nowrap;
        /* No wrapping */
        z-index: 10;
        /* Ensure it's above other elements */
    }

    .btn-edit:hover .tooltip-text {
        display: block;
        /* Show tooltip on hover */
    }
</style>


<!-- Display Portal Status Message if Portal is Closed -->
<?php if (!empty($portalStatusMessage)): ?>

    <div class="container  d-flex justify-content-center align-items-center" style="height: 100vh;"">

        <div class=" alert alert-danger tetx-center" role="alert">
        <h5 class="alert-heading">Portal is currently closed!</h5>
        <p><?php echo $portalStatusMessage; ?></p>
    </div>
    </div>

<?php else: ?>

    <div class="container mt-5 mb-5" id="submitting-forms">
        <div class="row justify-content-center">
            <div class="col-lg-8 col-md-12 col-12">
                <!-- <img src="https://www.bms.ac.lk/assets/images/MainBanner/data1/images/bmsbanner1.jpg"
                    alt="Google Form Header Banner" class="img-fluid" style="border-radius:10px 10px 0 0"> -->
                <img src="./images/PLGERISM.png" alt="Google Form Header Banner" class="img-fluid"
                    style="border-radius:10px 10px 0 0">
                <div class="  rounded shadow-sm" style="background-color:#ddd3d3c2; padding: 12px;">
                    <!-- <h3 class="text-center  mb-2 eb-garamond" style="font-size: 40px; font-weight: 700; "> -->
                    <!-- Plagiarism-Checker- -->
                    <!--<span style="font-size: 30px;">"Only For BMS student"</span>-->
                    <!-- </h3> -->
                    <hr
                        style="width: 50%; margin: 0 auto; height: 3px; border: none; background-image: linear-gradient(to right, blue, red);">
                    <br>

                    <form id="studentForm" enctype="multipart/form-data">
                        <div class="row">
                            <div class="col-md-12">

                                <!-- main concept  -->
                                <div class="card lft_border">
                                    <div class="card-body shadow">

                                        <!-- Student ID  -->
                                        <div class="mb-2">
                                            <!-- -----------------------------------  -->
                                            <div class="row">
                                                <p class="text-center">Please fill below form to verify you are the BMS
                                                    student.</p>
                                                <div class="col-md-4 col-sm-3">
                                                    <label for="student_id" class="form-label">Student BMS ID <span style="color:red; font-weight: 
                                                bolder;">*</span></label>
                                                </div>
                                                <div class="col-md-8 col-sm-9">
                                                    <div class="wrap-input-1">
                                                        <input class="input" type="text" placeholder="Student ID"
                                                            id="student_id" name="student_id" required>
                                                        <span class="focus-border"></span>
                                                        <div id="student_id_alert" class="alert alert-danger mt-2"
                                                            style="display: none;">
                                                            <strong>Warning!</strong> already Submitted
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- DOB  -->
                                        <div class="mb-2">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="dob" class="form-label">Date of Birth
                                                        <span style="color:red; font-weight:bolder;">*</span>
                                                    </label>
                                                </div>
                                                <div class="col">
                                                    <div class="wrap-input-1">
                                                        <input class="input" type="date" id="dob" name="dob" required
                                                            placeholder="Date Of Birth">
                                                        <span class="focus-border"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-2" id="next_button_id">
                                            <div class="row justify-content-end">
                                                <div class="col-auto">
                                                    <button type="button" class="btn btn-primary">
                                                        Next <i class="fas fa-arrow-right"></i>
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="card mt-2 lft_border" id="d_hide">
                                    <div class="card-body shadow">
                                        <div class="mb-2">

                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="name_in_full" class="form-label">Name in full
                                                        <span style="color:red; font-weight:bolder;">*</span>
                                                    </label>
                                                </div>
                                                <div class="col">
                                                    <div class="wrap-input-1 position-relative">
                                                        <input class="input" id="name_in_full" name="name_in_full"
                                                            placeholder="Name in Full" readonly>
                                                        <span class="focus-border"></span>
                                                    </div>
                                                </div>
                                            </div>

                                        </div>


                                        <div class="mb-2">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="bms_email_address" class="form-label">BMS Email Address
                                                        <span style="color:red; font-weight:bolder;">*</span>
                                                    </label>
                                                </div>
                                                <div class="col">
                                                    <div class="wrap-input-1">
                                                        <input class="input" type="email" id="bms_email_address"
                                                            name="bms_email_address" placeholder="BMS Email" readonly>
                                                        <span class="focus-border"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="phone_no" class="form-label">Phone Number</label>
                                                </div>
                                                <div class="col">
                                                    <div class="d-flex">
                                                        <div class="d-flex align-items-center">
                                                            <select class="form-select" aria-label="Country Code"
                                                                style="width: 80px;">
                                                                <option value="+94" selected>+94</option>
                                                            </select>
                                                        </div>
                                                        <div class="wrap-input-1 position-relative"
                                                            style="margin-left: 25px; flex-grow: 1;">
                                                            <input class="input" type="text" placeholder="7X XXX XXXX"
                                                                id="phone_no" name="phone_no" readonly>
                                                            <span class="focus-border"></span>
                                                            <small id="phone-error" style="color: red; display:none;">Please
                                                                enter only numbers.</small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="program" class="form-label">Program</label>
                                                </div>
                                                <div class="col">
                                                    <div class="wrap-input-1 position-relative">
                                                        <input class="input" type="text" id="std_programs"
                                                            name="std_programs" placeholder="Student Programs" readonly>
                                                        <span class="focus-border"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="mb-2">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="program" class="form-label">Batch</label>
                                                </div>
                                                <div class="col">
                                                    <div class="wrap-input-1 position-relative">
                                                        <input class="input" type="text" id="std_batch" name="std_batch"
                                                            placeholder="Student Batch" readonly>
                                                        <span class="focus-border"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="mb-2">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="module" class="form-label">Module</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <select id="module" class="form-select select2" name="module">
                                                        <option value="">Select Module</option>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>


                                        <!-- <script>
                                            // Add this to your existing module change event handler
                                            $('#module').on('change', function() {
                                                const moduleId = $(this).val();
                                                // const studentId = $('#student_id').val();

                                                if (moduleId) {
                                                    $.ajax({
                                                        url: 'check_module_attempts.php',
                                                        method: 'POST',
                                                        data: {
                                                            module_id: moduleId,
                                                            // student_id: studentId
                                                        },
                                                        success: function(response) {
                                                            const data = JSON.parse(response);
                                                            if (data.success) {
                                                                if (data.attempts < 3) {
                                                                    // Show upload section and submit button
                                                                    $('#uploadSection').show();
                                                                    $('#submitBtn').show();
                                                                } else {
                                                                    // Hide upload section and submit button
                                                                    $('#uploadSection').hide();
                                                                    $('#submitBtn').hide();
                                                                    alert('You have reached the maximum number of attempts for this module.');
                                                                }
                                                            }
                                                        },
                                                        error: function() {
                                                            alert('Error checking module attempts');
                                                        }
                                                    });
                                                }
                                            });
                                        </script> -->


                                        <!-- <script>
                                            // Handle module change event
                                            $('#module').on('change', function() {
                                                const moduleId = $(this).val(); // Get the selected module ID

                                                if (moduleId) { // Check if a module is selected
                                                    $.ajax({
                                                        url: 'check_module_attempts.php', // Backend script
                                                        method: 'POST',
                                                        data: {
                                                            module_id: moduleId // Pass the module ID
                                                        },
                                                        success: function(response) {
                                                            try {
                                                                const data = JSON.parse(response); // Parse the JSON response
                                                                if (data.success) {
                                                                    if (data.attempts < 3) {
                                                                        // Show upload section and submit button
                                                                        $('#uploadSection').show();
                                                                        $('#submitBtn').show();
                                                                        $('#submitBtn').prop('disabled', false); // Ensure submit button is enabled
                                                                    } else {
                                                                        // Hide upload section and submit button
                                                                        $('#uploadSection').hide();
                                                                        $('#submitBtn').prop('disabled', true); // Disable submit button
                                                                        $('#submitBtn').hide();
                                                                        alert('You have reached the maximum number of attempts for this module.');
                                                                    }
                                                                } else {
                                                                    alert(data.message || 'An error occurred.');
                                                                }
                                                            } catch (error) {
                                                                alert('Error parsing response from server.');
                                                            }
                                                        },
                                                        error: function() {
                                                            alert('Error checking module attempts.');
                                                        }
                                                    });
                                                } else {
                                                    // If no module selected, hide upload section and button
                                                    $('#uploadSection').hide();
                                                    $('#submitBtn').hide();
                                                    $('#submitBtn').prop('disabled', true); // Disable submit button
                                                }
                                            });
                                        </script> -->




                                        <style>
                                            #mdl_deadline {
                                                display: none;
                                            }
                                        </style>

                                        <div class="mb-2" id="mdl_deadline">
                                            <div class="row">
                                                <div class="col-md-4">
                                                    <label for="module_deadline" class="form-label">Module Deadlines</label>
                                                </div>
                                                <div class="col-md-8">
                                                    <input type="text" id="module_deadline" class="form-control"
                                                        name="module_deadline">
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



                                        <!-- Hidden fields for storing program and batch IDs -->
                                        <input type="hidden" id="program_id" name="program_id">
                                        <input type="hidden" id="batch_id" name="batch_id">


                                        <div class="p-0">
                                            <!-- <button type="submit" class="btn btn-primary mt-4 mb-4 w-100"
                                                id="submitBtn" style="display: none;" style="display: none;">Submit</button> -->

                                            <button type="submit" class="btn btn-primary mt-4 mb-4 w-100" id="submitBtn"
                                                style="display: none;">Submit</button>

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



<!-- Preloader overlay -->
<!-- Preloader overlay -->
<div id="preloader" style=" display: none; position: fixed;">
</div>






<style>
    .modal-background {
        background-image: url('https://media.istockphoto.com/id/1338579925/vector/geometric-background-of-squares-and-dots.jpg?s=612x612&w=0&k=20&c=SZJsLHWoh2rTHRJP5bSss3a7xuY6GCE3iFLUDW2t0Q0=');

    }
</style>


<?php include('All-modal.php') ?>

<!-- Bootstrap JS and dependencies (Optional) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>


<script src="./index.js"></script>

</body>

</html>