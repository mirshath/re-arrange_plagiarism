<?php
include('./database/connection.php'); // Include your database connection
include('./includes/header.php'); // Include header if needed
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Student List by Program and Batch</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>

<body>
    <div class="container mt-5">
        <form id="programBatchForm">
            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label for="program" class="form-label">Program:</label>
                    </div>
                    <div class="col-md-8">
                        <select id="program" name="program" class="form-select">
                            <option value="">Select Program</option>
                            <?php
                            // Fetch programs from the program_table
                            $programQuery = "SELECT * FROM program_table";
                            $programResult = $conn->query($programQuery);
                            while ($programRow = $programResult->fetch_assoc()) {
                                echo "<option value='{$programRow['id']}'>{$programRow['program_name']}</option>";
                            }
                            ?>
                        </select>
                    </div>
                </div>
            </div>

            <div class="mb-2">
                <div class="row">
                    <div class="col-md-4">
                        <label for="batch" class="form-label">Batch:</label>
                    </div>
                    <div class="col-md-8">
                        <select id="batch" name="batch" class="form-select">
                            <option value="">Select Batch</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-md-12 text-end">
                    <button type="button" class="btn btn-primary" onclick="fetchStudentList()">Submit</button>
                </div>
            </div>
        </form>

        <button type="button" id="allocateButton" class="btn btn-secondary mt-3">Allocate Checkers</button>

        <div id="result" class="mt-4"></div>
    </div>

    <script>
        $(document).ready(function() {
            $('#program').on('change', function() {
                var programId = $(this).val();

                if (programId) {
                    $.ajax({
                        url: 'fetching_for_admin/fetch_batches.php',
                        type: 'POST',
                        data: { program_id: programId },
                        success: function(data) {
                            $('#batch').html(data);
                        }
                    });
                } else {
                    $('#batch').html('<option value="">Select Batch</option>');
                }
            });
        });

        function fetchStudentList() {
            const formData = new FormData(document.getElementById('programBatchForm'));

            fetch('fetching_for_admin/fetch_students.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.text())
            .then(data => {
                document.getElementById('result').innerHTML = data;
            })
            .catch(error => console.error('Error:', error));
        }

        document.getElementById('allocateButton').addEventListener('click', function() {
            const selectedStudents = [];
            document.querySelectorAll('.student-checkbox:checked').forEach((checkbox) => {
                selectedStudents.push(checkbox.value);
            });

            if (selectedStudents.length === 0) {
                alert('Please select at least one student to allocate.');
                return;
            }

            fetch('allocate_checkers.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ student_ids: selectedStudents })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'error') {
                    alert('Errors occurred:\n' + data.messages.join('\n'));
                } else {
                    alert('Allocation successful and emails sent!');
                }
            })
            .catch(error => console.error('Error:', error));
        });
    </script>
</body>
</html>
