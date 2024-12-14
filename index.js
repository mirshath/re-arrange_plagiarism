// document.addEventListener("DOMContentLoaded", function () {
//   document
//     .getElementById("studentForm")
//     .addEventListener("keypress", function (event) {
//       if (event.key === "Enter") {
//         event.preventDefault();
//       }
//     });
// });

// file upload sections
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
        errorMessage =
          "File exceeds the 50 KB size limit. Please reduce the file size.";
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

// student validation with student_id and DOB and Module fetch

$("#student_id, #dob").on("blur", function () {
  var studentId = $("#student_id").val().trim();
  var dob = $("#dob").val().trim();

  if (!studentId || !dob) return;

  $.ajax({
    url: "validate_student.php",
    type: "POST",
    contentType: "application/json",
    data: JSON.stringify({
      student_id: studentId,
      dob: dob,
    }),
    success: function (data) {
      if (data.success) {
        $("#d_hide").show();
        $("#student_id_alert").hide();
        $("#name_in_full").val(data.name);
        $("#std_auto_id").val(data.id);
        $("#bms_email_address").val(data.email);
        $("#phone_no").val(data.phone);

        $("#program_id").val(data.program_name);
        $("#batch_name").val(data.batch_name);
        $("#bacth_id_no").val(data.batch_id);

        // Enable the module dropdown and display the module section
        $("#module_section").show();
        $("#module_id").prop("disabled", false);

        // Fetch modules based on the batch_id
        fetchModulesByBatch(data.batch_id);
      } else {
        $("#student_id_alert").show();
        $("#d_hide").hide();
        setTimeout(function () {
          location.reload();
        }, 3000);
        $("#next_button").prop("disabled", true);
        $("#next_button").html("Refreshing... please wait");
      }
    },
    error: function () {
      alert("Error verifying student. Please try again.");
    },
  });
});

function fetchModulesByBatch(batchId) {
  $.ajax({
    url: "get_modules_by_batch.php", // Fetch modules by batch ID
    type: "POST",
    data: {
      batch_id: batchId,
    },
    success: function (response) {
      var modulesSelect = $("#module_id");
      modulesSelect.empty(); // Clear existing options
      modulesSelect.append('<option value="">Select Module</option>'); // Default option

      if (response.success) {
        // Populate the module dropdown with modules fetched from the server
        response.modules.forEach(function (module) {
          modulesSelect.append(
            '<option value="' +
              module.id +
              '">' +
              module.module_name +
              "</option>"
          );
        });
      } else {
        modulesSelect.append('<option value="">No modules available</option>');
      }
    },
    error: function () {
      alert("Error fetching modules. Please try again.");
    },
  });
}

$("#module_id").on("change", function () {
  var module_id = $(this).val();

  if (module_id) {
    $.ajax({
      url: "Fetching-upmb/fetch_module_deadline.php", // New PHP file to get deadline
      method: "POST",
      dataType: "json",
      data: {
        module_id: module_id,
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
              student_id: $("#student_id").val(), // Get student_id from the form
            },
            dataType: "json", // Specify JSON dataType
            success: function (response) {
              if (response.success) {
                if (response.ma_attempts >= 3) {
                  alert("You have reached the maximum number of attempts .");
                  $("#uploadSection").hide();
                  setTimeout(function () {
                    location.reload();
                  }, 1000);
                }
              } else {
                console.error(
                  "Failed to fetch module attempts:",
                  response.message
                );
              }
            },
            error: function (xhr, status, error) {
              console.error("Error fetching module attempts:", error);
            },
          });
        } else {
          // Hide module deadline section and upload section if no deadline is found
          $("#mdl_deadline").hide();
          $("#uploadSection").hide();
        }
      },
      error: function () {
        alert("Error fetching module deadline. Please try again.");
      },
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
      $("#preloader1").show();
    },
    success: function (response) {
      console.log("AJAX request completed.");
      $("#preloader1").hide();

      if (response.includes("successful")) {
        // $("#studentForm")[0].reset();
        // alert("Successfully submitted");
        // $("#successModal").modal("show");
        setTimeout(function () {
          window.location.reload();
        }, 500);
      } else if (
        response.includes("Please select a module.") ||
        response.includes("Please upload a document.")
      ) {
        $("#errorModal").find(".modal-body").html(response);
        $("#errorModal").modal("show");
      } else {
        alert(response);
      }
    },
    error: function (xhr, status, error) {
      console.log("Error in AJAX request.");
      $("#preloader1").hide();
      alert("An error occurred: " + error);
    },
  });
});
