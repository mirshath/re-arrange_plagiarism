document.addEventListener("DOMContentLoaded", function () {
  document
    .getElementById("studentForm")
    .addEventListener("keypress", function (event) {
      if (event.key === "Enter") {
        event.preventDefault();
      }
    });
});



// Initialize Select2 for the dropdowns
$("#module").select2({
  placeholder: "Select Modules",
});

$("#d_hide").hide(); // Hide alert message
$("#d_hide2").hide(); // Hide alert message
$("#submitBtn").hide(); // Hide alert message

$("#student_id, #dob").on("blur", function () {
  var student_id = $("#student_id").val();
  var dob = $("#dob").val();

  if (student_id && dob) {
    $.ajax({
      url: "validate_student.php",
      method: "POST",
      dataType: "json", // Expect JSON response
      data: {
        student_id: student_id,
        dob: dob,
      },
      success: function (response) {
        if (response.status === "valid") {
          var studentData = response.data;

          $("#next_button_id").hide();
          $("#d_hide").show();
          $("#d_hide2").show();
          // $("#submitBtn").show();

          // Populate and enable all fields if student_id and DOB match
          $("#name_in_full").val(studentData.name);
          $("#phone_no").val(studentData.phone_no);
          $("#email_address").val(studentData.email);
          $("#std_programs").val(studentData.program_name);
          $("#std_batch").val(studentData.batch_name);
          $("#bms_email_address")
            .val(studentData.bms_email)
            .prop("readonly", true);

          // Set hidden fields for program and batch IDs to ensure they’re sent in the form
          $("#program_id").val(studentData.program_id);
          $("#batch_id").val(studentData.batch_id);

          // Add this new code to fetch modules
          var program_id = studentData.program_id;
          if (program_id) {
            $.ajax({
              url: "Fetching-upmb/fetch_modules.php",
              method: "POST",
              data: { program_id: program_id },
              dataType: "json",
              success: function (modules) {
                // Clear and populate module dropdown
                $("#module")
                  .empty()
                  .append('<option value="">Select Module</option>');
                modules.forEach(function (module) {
                  $("#module").append(
                    `<option value="${module.id}">${module.module_name}</option>`
                  );
                });
              },
            });
          }

          // $('#address, #address_2, #city, #email_address, #professional, #submitBtn').prop('disabled', false);
        } else {
          // Disable fields and trigger the modal if student_id and DOB don't match
          // $('#name_in_full, #address, #address_2, #city, #email_address, #phone_no, #professional, #submitBtn').prop('disabled', true);
          $("#errorModal").modal("show");
          setTimeout(function () {
            window.location.reload();
          }, 2000); // Refresh after 2 seconds
        }
      },
    });
  } else {
    // If either student_id or DOB is empty, disable the fields
    // $('#name_in_full, #address, #address_2, #city, #email_address, #phone_no, #professional, #submitBtn').prop('disabled', true);
  }
});

// ------------------ modlue working --------------
// Event listener for module dropdown
$("#module").on("change", function () {
  var module_id = $(this).val();

  if (module_id) {
    $.ajax({
      url: "Fetching-upmb/fetch_module_deadline.php", // New PHP file to get deadline
      method: "POST",
      dataType: "json",
      data: { module_id: module_id },

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
            $("#submitBtn").show(); // Show the upload section if a module is selected

            // -------------------------------------------------------------------------------------------------------------------
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
                  // Show remaining attempts
                  if (response.ma_attempts >= 3) {
                    alert("You have reached the maximum number of attempts.");

                    $("#uploadSection").hide();
                    $("#submitBtn").hide();
                  } else {
                    // alert(
                    //   `You have ${3 - response.ma_attempts} attempts remaining.`
                    // );
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
            // -------------------------------------------------------------------------------------------------------------------
          } else {
            // Hide the upload section if the deadline is in the future
            $("#uploadSection").hide();
            $("#submitBtn").hide(); // Show the upload section if a module is selected
          }
        } else {
          // Hide module deadline section and upload section if no deadline is found
          $("#mdl_deadline").hide();
          $("#uploadSection").hide();
          $("#submitBtn").hide();
        }
      },
    });
  } else {
    // Clear the deadline field if no module is selected
    // Hide the module deadline section if no module is selected
    $("#mdl_deadline").hide();
    $("#submitBtn").hide(); // Show the upload section if a module is selected
  }
});

// upload button showing
// Show the upload section when a module is selected
$("#module").on("change", function () {
  if ($(this).val()) {
    $("#uploadSection").show();
  } else {
    $("#uploadSection").hide(); // Hide the upload section if no module is selected
  }
});

$(document).ready(function () {
  // Show preloader on button click
  $("#submitBtn").on("click", function () {
    console.log("Submit button clicked, showing preloader.");
    $("#preloader").show();
  });

  // Handle form submission with AJAX
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
      success: function (response) {
        console.log("AJAX request complete, hiding preloader.");
        $("#preloader").hide();

        if (response.includes("successful")) {
          $("#successModal").modal("show");
          $("#studentForm")[0].reset();
          setTimeout(function () {
            window.location.reload(); // Refresh the page after a short delay
          }, 2000);
        } else if (
          response === "Please Select a module." ||
          response === "Please upload a document."
        ) {
          $("#errorModal").find(".modal-body").html(response);
          $("#errorModal").modal("show");
          setTimeout(function () {
            window.location.reload(); // Refresh the page after a short delay
          }, 2000);
        } else {
          alert(response);
        }
      },
      error: function (xhr, status, error) {
        console.log("Error in AJAX request, hiding preloader.");
        $("#preloader").hide();
        alert("An error occurred: " + error);
      },
    });
  });
});

$(document).ready(function () {
  const MAX_FILE_SIZE = 30 * 1024; // 10 KB in bytes
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
          "File exceeds the 10 KB size limit. Please reduce the file size.";
      }
    }

    // If invalid, show the error message and clear input
    if (!isValid) {
      $("#fileError").text(errorMessage).show();
      $(this).val(""); // Clear the file input
    }
  });
});
