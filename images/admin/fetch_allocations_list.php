<?php
include("../database/connection.php");

if (isset($_POST['checker_id'])) {
    $checkerId = $_POST['checker_id'];

    // Query to get checker details
    $checkerQuery = "
        SELECT * 
        FROM checkers 
        WHERE id = ?
    ";
    $stmt = $conn->prepare($checkerQuery);
    $stmt->bind_param("i", $checkerId);
    $stmt->execute();
    $checkerResult = $stmt->get_result();

    // Fetch and display checker details
    if ($checkerResult->num_rows > 0) {
        $checker = $checkerResult->fetch_assoc();
        echo "<div class='card-body'>
                <p><strong>Checker Name: </strong>" . htmlspecialchars($checker['checker_name']) . "</p>
                <p><strong>Checker Email: </strong>" . htmlspecialchars($checker['checker_email']) . "</p>
                <div class='table-responsive'>
                    <table class='table table-bordered table-striped' id='dataTable' width='100%' cellspacing='0'>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Student Name</th>
                                <th>Student ID</th>
                                <th>BMS Email</th>
                                <th>Phone Number</th>
                                <th>Report Submited / Not</th>
                            </tr>
                        </thead>
                        <tbody id='allocationDetails'>";

        // Now, query to get allocated student details using INNER JOIN
        $studentQuery = "
            SELECT 
                c.*,ac.*, 
                s.name AS student_name, 
                s.student_id AS student_id, 
                s.bms_email AS student_bms_email, 
                s.phone_no AS student_phone
            FROM allocate_checker ac
            INNER JOIN old_student_db s ON ac.student_id = s.id
            INNER JOIN checkers c ON ac.checker_id = c.id
            WHERE ac.checker_id = ?
        ";

        $stmt = $conn->prepare($studentQuery);
        $stmt->bind_param("i", $checkerId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $index = 1; // Initialize row index
            while ($row = $result->fetch_assoc()) {
                echo "<tr>
                       <td style='width: 10px;'>" . $index . "</td> <!-- Display row index with fixed width -->
                        <td>" . htmlspecialchars($row['student_name']) . "</td>
                        <td>" . htmlspecialchars($row['student_id']) . "</td>
                        <td>" . htmlspecialchars($row['student_bms_email']) . "</td>
                        <td>" . htmlspecialchars($row['student_phone']) . "</td>
                        <td>";

                // Check if the submitted status is "submitted"
                if (htmlspecialchars($row['submitted_status']) == 'submitted') {
                    // If status is 'submitted', show a green badge
                    echo "<span class='badge bg-success'>" . htmlspecialchars($row['submitted_status']) . "</span>";
                } else {
                    // For any other status, show a red badge
                    echo "<span class='badge bg-danger'>" . htmlspecialchars($row['submitted_status']) . "</span>";
                }

                echo "</td>
                            </tr>";
                $index++; // Increment index for next row
            }
        } else {
            echo "<tr><td colspan='5'>No allocations found for this checker.</td></tr>";
        }

        echo "</tbody>
            </table>
        </div>
    </div>"; // Close the card-body div
    } else {
        echo "<p>No checker found with the given ID.</p>";
    }

    $stmt->close();
    $conn->close();
}
