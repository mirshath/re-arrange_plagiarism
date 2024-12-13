<?php
// Database connection
include('./database/connection.php');

// Get the form data
// $program_id = $_POST['program_id'];
// $module_id = $_POST['module_id'];
// $batch_id = $_POST['batch_id'];

// // Query to fetch student details based on selected program, module, and batch
// $sql_students = "
//     SELECT osd.*, pt.*, mt.*, bt.*
//     FROM std_crs_details scd
//     JOIN old_student_db osd ON scd.student_id = osd.id
//     JOIN program_table pt ON scd.program_id = pt.id
//     JOIN module_table mt ON scd.module_id = mt.id
//     JOIN batch_table bt ON scd.batch_id = bt.id
//     WHERE scd.program_id = ? AND scd.module_id = ? AND scd.batch_id = ?
// ";

// $stmt_students = $conn->prepare($sql_students);
// $stmt_students->bind_param("iii", $program_id, $module_id, $batch_id);
// $stmt_students->execute();
// $result_students = $stmt_students->get_result();

// // Get the student count
// $student_count = $result_students->num_rows;

// // Query to fetch the checker count
// $sql_checkers = "SELECT COUNT(*) as checker_count FROM ckeckers";
// $result_checkers = $conn->query($sql_checkers);
// $checker_data = $result_checkers->fetch_assoc();
// $checker_count = $checker_data['checker_count'];

// // Output the counts
// echo "<div class='student-checker-counts'>";
// echo "<p>Total Students: $student_count</p>";
// echo "<p>Total Checkers: $checker_count</p>";
// echo "</div>";

// // Display the student details in an HTML table
// if ($student_count > 0) {
//     echo "<table class='table'>
//             <thead>
//                 <tr>
//                     <th>Student ID</th>
//                     <th>Name</th>
//                     <th>Date of Birth</th>
//                     <th>Email</th>
//                     <th>Phone</th>
//                     <th>Program</th>
//                     <th>Module</th>
//                     <th>Batch</th>
//                 </tr>
//             </thead>
//             <tbody>";
//     while ($row = $result_students->fetch_assoc()) {
//         echo "<tr>
//                 <td>{$row['student_id']}</td>
//                 <td>{$row['name']}</td>
//                 <td>{$row['DOB']}</td>
//                 <td>{$row['email']}</td>
//                 <td>{$row['phone_no']}</td>
//                 <td>{$row['program_name']}</td>
//                 <td>{$row['module_name']}</td>
//                 <td>{$row['batch_name']}</td>
//               </tr>";
//     }
//     echo "</tbody></table>";
// } else {
//     echo "<div>No students found for the selected program, module, and batch.</div>";
// }

// // Close the statements and database connection
// $stmt_students->close();
// $conn->close();
?>
