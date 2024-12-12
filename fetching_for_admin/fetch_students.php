<?php
include('../database/connection.php');

if (isset($_POST['program']) && isset($_POST['batch'])) {
    $programId = $_POST['program'];
    $batchId = $_POST['batch'];

    $studentQuery = "
        SELECT *
        FROM old_student_db
        WHERE program_id = ? AND batch_id = ? AND allocate=''
    ";

    $stmt = $conn->prepare($studentQuery);
    $stmt->bind_param("ii", $programId, $batchId);
    $stmt->execute();
    $result = $stmt->get_result();

    $studentCount = $result->num_rows;

    if ($studentCount > 0) {
        echo "<p>Total Students Found: <strong>{$studentCount}</strong></p>";
        // echo "<table border='1' width='100%'>
        //         <tr>
        //             <th>#</th>
        //             <th>Select</th>
        //             <th>Student ID</th>
        //             <th>Name</th>
        //             <th>Date of Birth</th>
        //             <th>BMS Email</th>
        //             <th>Phone</th>
        //             <th>Allocate</th>
        //         </tr>";

        $index = 1; // Initialize row index outside of the loop

        while ($row = $result->fetch_assoc()) {
            echo "<tr>
                    <td>{$index}</td> <!-- Display the row index -->
                    <td><input type='checkbox' class='student-checkbox' value='{$row['id']}' checked></td>
                    <td>{$row['student_id']}</td>
                    <td>{$row['name']}</td>
                    <td>{$row['DOB']}</td>
                    <td>{$row['bms_email']}</td>
                    <td>{$row['phone_no']}</td>
                    <td>{$row['allocate']}</td>
                  </tr>";
            $index++; // Increment index for next row
        }

        echo "</table>";
    } else {
        echo "<p>No students found for the selected program and batch.</p>";
    }

    $stmt->close();
}
?>