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
                <img src="./images/PLGERISM.png" alt="Google Form Header Banner" class="img-fluid" style="border-radius:10px 10px 0 0">
                <div class="rounded shadow-sm" style="background-color:#ddd3d3c2; padding: 12px;">
                    <form id="studentForm" enctype="multipart/form-data">
                        <div class="card lft_border">
                            <div class="card-body shadow">
                                <div class="mb-2">
                                    <label for="student_id" class="form-label">Student BMS ID <span style="color:red;">*</span></label>
                                    <input class="form-control" type="text" id="student_id" name="student_id" required placeholder="Enter your Student ID">
                                </div>
                                <div class="mb-2">
                                    <label for="dob" class="form-label">Date of Birth <span style="color:red;">*</span></label>
                                    <input class="form-control" type="date" id="dob" name="dob" required>
                                </div>
                                <div id="student_id_alert" class="alert alert-danger mt-2" style="display: none;">
                                    <strong>Warning!</strong> Please Check Your Student ID and DOB.
                                </div>
                                <button type="button" class="btn btn-primary" id="next_button">
                                    Next <i class="fas fa-arrow-right"></i>
                                </button>
                            </div>
                        </div>
                        <div class="card mt-2 lft_border" id="d_hide" style="display: none;">
                            <div class="card-body shadow">
                                <p>Welcome! Your details are verified.</p>
                                <div class="mb-2">
                                    <label for="name_in_full" class="form-label">Name in Full <span style="color:red;">*</span></label>
                                    <input class="form-control" id="name_in_full" name="name_in_full" readonly>
                                </div>
                                <div class="mb-2">
                                    <label for="bms_email_address" class="form-label">BMS Email Address <span style="color:red;">*</span></label>
                                    <input class="form-control" id="bms_email_address" name="bms_email_address" readonly>
                                </div>
                                <div class="mb-2">
                                    <label for="phone_no" class="form-label">Phone Number</label>
                                    <input class="form-control" id="phone_no" name="phone_no" readonly>
                                </div>
                                <div class="mb-2">
                                    <label for="program_id" class="form-label">Program</label>
                                    <input class="form-control" id="program_id" name="program_id" readonly>
                                </div>
                                <div class="mb-2">
                                    <label for="batch_id" class="form-label">Batch</label>
                                    <input class="form-control" id="batch_name" name="batch_name" readonly>
                                    <input type="hidden" class="form-control" id="bacth_id_no" name="bacth_id_no" readonly>
                                </div>
                                <div class="mb-2" id="module_section" style="display: none;">
                                    <label for="module_id" class="form-label">Module</label>
                                    <select class="form-control" id="module_id" name="module_id" disabled>
                                        <option value="">Select Module</option>
                                    </select>
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
    // $("#student_id, #dob").on("blur", function() {
    //     var studentId = $("#student_id").val().trim();
    //     var dob = $("#dob").val().trim();

    //     if (!studentId || !dob) return;

    //     $.ajax({
    //         url: 'validate_student.php',
    //         type: 'POST',
    //         contentType: 'application/json',
    //         data: JSON.stringify({
    //             student_id: studentId,
    //             dob: dob
    //         }),
    //         success: function(data) {
    //             if (data.success) {
    //                 $('#d_hide').show();
    //                 $('#student_id_alert').hide();
    //                 $('#name_in_full').val(data.name);
    //                 $('#bms_email_address').val(data.email);
    //                 $('#phone_no').val(data.phone);

    //                 $('#program_id').val(data.program_name);
    //                 $('#batch_id').val(data.batch_name);



    //                 // Enable module dropdown after receiving the batch data
    //                 $('#module_id').prop('disabled', false);

    //                 // Fetch modules based on the batch_id
    //                 fetchModulesByBatch(data.batch_id);



    //             } else {
    //                 $('#student_id_alert').show();
    //                 $('#d_hide').hide();
    //                 setTimeout(function() {
    //                     location.reload();
    //                 }, 3000);
    //                 $('#next_button').prop('disabled', true);
    //                 $('#next_button').html('Refreshing... please wait');
    //             }
    //         },
    //         error: function() {
    //             alert('Error verifying student. Please try again.');
    //         }
    //     });
    // });



    // function fetchModulesByBatch(batchId) {
    //     $.ajax({
    //         url: 'get_modules_by_batch.php', // Create this new PHP file to fetch modules based on batch_id
    //         type: 'POST',
    //         data: {
    //             batch_id: batchId
    //         },
    //         success: function(response) {
    //             var modulesSelect = $('#module_id');
    //             modulesSelect.empty(); // Clear existing options
    //             modulesSelect.append('<option value="">Select Module</option>'); // Default option

    //             if (response.success) {
    //                 // Populate the module dropdown
    //                 response.modules.forEach(function(module) {
    //                     modulesSelect.append('<option value="' + module.id + '">' + module.module_name + '</option>');
    //                 });
    //             } else {
    //                 modulesSelect.append('<option value="">No modules available</option>');
    //             }
    //         },
    //         error: function() {
    //             alert('Error fetching modules. Please try again.');
    //         }
    //     });
    // }



    $("#student_id, #dob").on("blur", function() {
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
            success: function(data) {
                if (data.success) {
                    $('#d_hide').show();
                    $('#student_id_alert').hide();
                    $('#name_in_full').val(data.name);
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
                    setTimeout(function() {
                        location.reload();
                    }, 3000);
                    $('#next_button').prop('disabled', true);
                    $('#next_button').html('Refreshing... please wait');
                }
            },
            error: function() {
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
            success: function(response) {
                var modulesSelect = $('#module_id');
                modulesSelect.empty(); // Clear existing options
                modulesSelect.append('<option value="">Select Module</option>'); // Default option

                if (response.success) {
                    // Populate the module dropdown with modules fetched from the server
                    response.modules.forEach(function(module) {
                        modulesSelect.append('<option value="' + module.id + '">' + module.module_name + '</option>');
                    });
                } else {
                    modulesSelect.append('<option value="">No modules available</option>');
                }
            },
            error: function() {
                alert('Error fetching modules. Please try again.');
            }
        });
    }
</script>