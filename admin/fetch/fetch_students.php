<?php
include("../../database/connection.php");

// Fetch students based on the selected checker
if (isset($_POST['checker_id'])) {
    $checker_id = $_POST['checker_id'];

    // Query to fetch student details, program name, and module name
    $query = "
        SELECT s.student_id, s.student_name, p.program_name, m.module_name
        FROM students s
        JOIN programs p ON s.program_id = p.program_id
        JOIN modules m ON s.module_id = m.module_id
        WHERE s.checker_id = '$checker_id'"; // Assuming 'students' table has checker_id, program_id, and module_id
    $result = mysqli_query($conn, $query);

    // Output student data in a table row format
    if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>
                    <td>".$row['student_name']."</td>
                    <td>".$row['program_name']."</td>
                    <td>".$row['module_name']."</td>
                  </tr>";
        }
    } else {
        echo "<tr><td colspan='3'>No students found for this checker.</td></tr>";
    }
}
?>
