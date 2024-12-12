<?php
include("../database/connection.php");

if (isset($_POST['checker_id'])) {
    $checker_id = intval($_POST['checker_id']);

    $query = "SELECT 
                s.id AS student_id,
                s.student_id AS student_reg_id,
                s.name AS student_name,
                p.program_name,
                b.batch_name,
                s.bms_email,
                ac.submitted_status,
                ac.created_at
              FROM 
                allocate_checker ac
              JOIN 
                old_student_db s ON ac.student_id = s.id
              JOIN 
                program_table p ON s.program_id = p.id
              JOIN 
                batch_table b ON s.batch_id = b.id
              WHERE 
                ac.checker_id = ?
              ORDER BY 
                p.program_name, b.batch_name, s.name ASC";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $checker_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $data = [];
    while ($row = $result->fetch_assoc()) {
        $data[$row['program_name']][$row['batch_name']][] = $row;
    }

    if (!empty($data)) {
        foreach ($data as $program => $batches) {
            echo "<h5 style='color:white' class=' mt-3 bg-primary p-2 rounded-top'>" . htmlspecialchars($program) . "</h5>";
            foreach ($batches as $batch => $students) {
                echo "<div class='card mb-4'>
                        <div class='card-header'><strong>Batch: " . htmlspecialchars($batch) . "</strong></div>
                        <div class='card-body'>
                            <table class='table table-bordered datatable'>
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Student ID</th>
                                        <th>Student Name</th>
                                        <th>Batch</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Created At</th>
                                    </tr>
                                </thead>
                                <tbody>";
                foreach ($students as $index => $student) {
                    echo "<tr>
                            <td>" . ($index + 1) . "</td>
                            <td>" . htmlspecialchars($student['student_reg_id']) . "</td>
                            <td>" . htmlspecialchars($student['student_name']) . "</td>
                            <td>" . htmlspecialchars($student['batch_name']) . "</td>
                            <td ><a style='text-decoration:none' href='mailto:" . htmlspecialchars($student['bms_email']) . "'>" . htmlspecialchars($student['bms_email']) . "</a></td>
                            <td>
                                <span class='badge " . ($student['submitted_status'] === 'not_yet' ? 'bg-danger' : 'bg-success') . "'>" . htmlspecialchars($student['submitted_status']) . "</span>
                            </td>
                            <td>" . htmlspecialchars($student['created_at']) . "</td>
                          </tr>";
                }
                echo "</tbody></table></div></div>";
            }
        }
    } else {
        echo "<p class='text-center text-muted'>No students found for the selected checker.</p>";
    }

    $stmt->close();
}
