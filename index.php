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


<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    $(document).ready(function () {
        $("#submitBtn").hide();
        const MAX_FILE_SIZE = 50 * 1024; // 50 KB in bytes
        const ALLOWED_EXTENSIONS = ["doc", "docx"]; // Allowed file extensions

        // Check file size and type on file input change
        $("#documentUpload").on("change", function () {
            let file = this.files[0];
            let isValid = true;
            let errorMessage = "";

            // Clear any previous error messages
            $("#fileError").hide().text("");

            if (file) {
                let fileSize = file.size;
                let fileName = file.name;
                let fileExtension = fileName.split(".").pop().toLowerCase(); // Get the file extension

                // Check if file type is allowed
                if (!ALLOWED_EXTENSIONS.includes(fileExtension)) {
                    isValid = false;
                    errorMessage = "Only Word documents (.doc, .docx) are allowed.";
                }

                // Check if file size exceeds the limit
                if (fileSize > MAX_FILE_SIZE) {
                    isValid = false;
                    errorMessage = "File exceeds the 50 KB size limit. Please reduce the file size.";
                }
            }

            // If invalid, show the error message and clear input
            if (!isValid) {
                $("#fileError").text(errorMessage).show(); // Show error message
                $(this).val(""); // Clear the file input
                $("#submitBtn").hide(); // Hide submit button
            } else {
                // If no errors, hide error message and show the submit button
                $("#fileError").hide();
                $("#submitBtn").show();
            }
        });
    });
</script>


<script>
    $("#student_id, #dob").on("blur", function () {
        var studentId = $("#student_id").val().trim();
        var dob = $("#dob").val().trim();

        if (!studentId || !dob) return;

        $.ajax({
            url: 'validate_student.php',
            type: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({
                student_id: studentId,
                dob: dob
            }),
            success: function (data) {
                if (data.success) {
                    $('#d_hide').show();
                    $('#student_id_alert').hide();
                    $('#name_in_full').val(data.name);
                    $('#std_auto_id').val(data.id);
                    $('#bms_email_address').val(data.email);
                    $('#phone_no').val(data.phone);

                    $('#program_id').val(data.program_name);
                    $('#batch_name').val(data.batch_name);
                    $('#bacth_id_no').val(data.batch_id);

                    // Enable the module dropdown and display the module section
                    $('#module_section').show();
                    $('#module_id').prop('disabled', false);

                    // Fetch modules based on the batch_id
                    fetchModulesByBatch(data.batch_id);

                } else {
                    $('#student_id_alert').show();
                    $('#d_hide').hide();
                    setTimeout(function () {
                        location.reload();
                    }, 3000);
                    $('#next_button').prop('disabled', true);
                    $('#next_button').html('Refreshing... please wait');
                }
            },
            error: function () {
                alert('Error verifying student. Please try again.');
            }
        });
    });

    function fetchModulesByBatch(batchId) {
        $.ajax({
            url: 'get_modules_by_batch.php', // Fetch modules by batch ID
            type: 'POST',
            data: {
                batch_id: batchId
            },
            success: function (response) {
                var modulesSelect = $('#module_id');
                modulesSelect.empty(); // Clear existing options
                modulesSelect.append('<option value="">Select Module</option>'); // Default option

                if (response.success) {
                    // Populate the module dropdown with modules fetched from the server
                    response.modules.forEach(function (module) {
                        modulesSelect.append('<option value="' + module.id + '">' + module.module_name + '</option>');
                    });
                } else {
                    modulesSelect.append('<option value="">No modules available</option>');
                }
            },
            error: function () {
                alert('Error fetching modules. Please try again.');
            }
        });
    }

    // ---------------------------------------------------------------------- 

    // Event listener for module dropdown
    $("#module_id").on("change", function () {
        var module_id = $(this).val();

        if (module_id) {
            $.ajax({
                url: "Fetching-upmb/fetch_module_deadline.php", // New PHP file to get deadline
                method: "POST",
                dataType: "json",
                data: {
                    module_id: module_id
                },

                success: function (response) {
                    if (response.status === "success") {
                        // Set deadline and make the input read-only
                        $("#module_deadline").val(response.deadline).prop("readonly", true);

                        // Show the module deadline section
                        $("#mdl_deadline").show();

                        // Parse the deadline date and get today’s date
                        var deadlineDate = new Date(response.deadline);
                        var today = new Date();

                        // Check if the deadline is before or on today's date
                        if (deadlineDate >= today) {
                            // Show the upload section if the deadline is in the past or today
                            $("#uploadSection").show();
                        } else {
                            // Hide the upload section if the deadline is in the future
                            $("#uploadSection").hide();
                            $("#deadlineError").show();

                        }

                        // Fetch the number of attempts made for the module
                        $.ajax({
                            url: "Fetching-upmb/check_module_attempts.php",
                            method: "POST",
                            data: {
                                module_id: module_id,
                                student_id: $("#student_id").val() // Get student_id from the form
                            },
                            dataType: "json", // Specify JSON dataType
                            success: function (response) {
                                if (response.success) {
                                    if (response.ma_attempts >= 3) {
                                        alert("You have reached the maximum number of attempts .");
                                        $("#uploadSection").hide();
                                        setTimeout(function () {
                                            location.reload();
                                        }, 000);

                                    }
                                } else {
                                    console.error("Failed to fetch module attempts:", response.message);
                                }
                            },
                            error: function (xhr, status, error) {
                                console.error("Error fetching module attempts:", error);
                            }
                        });
                    } else {
                        // Hide module deadline section and upload section if no deadline is found
                        $("#mdl_deadline").hide();
                        $("#uploadSection").hide();
                    }
                },
                error: function () {
                    alert("Error fetching module deadline. Please try again.");
                }
            });
        } else {
            // Clear the deadline field if no module is selected
            $("#mdl_deadline").hide();
            $("#uploadSection").hide();
        }
    });




    // -------- new insert student ------------
    $("#studentForm").on("submit", function (e) {
        e.preventDefault();

        console.log("Form submission started.");

        var formData = new FormData(this);

        $.ajax({
            url: "insert_student.php",
            method: "POST",
            data: formData,
            contentType: false,
            processData: false,
            beforeSend: function () {
                console.log("Showing preloader...");
                $("#preloader").show();
            },
            success: function (response) {
                console.log("AJAX request completed.");
                $("#preloader").hide();

                if (response.includes("successful")) {
                    $("#successModal").modal("show");
                    $("#studentForm")[0].reset();
                    setTimeout(function () {
                        window.location.reload();
                    }, 2000);
                } else if (response.includes("Please select a module.") || response.includes("Please upload a document.")) {
                    $("#errorModal").find(".modal-body").html(response);
                    $("#errorModal").modal("show");
                } else {
                    alert(response);
                }
            },
            error: function (xhr, status, error) {
                console.log("Error in AJAX request.");
                $("#preloader").hide();
                alert("An error occurred: " + error);
            },
        });
    });


</script>