<?php
// Include database connection
include("../database/connection.php");

// Query to fetch module deadlines with program, batch details, and student count
$sql = "
    SELECT 
        m.module_name, 
        m.deadline, 
        p.program_name, 
        b.batch_name, 
        COUNT(DISTINCT sa.student_id) AS student_count
    FROM 
        module_table m
    JOIN 
        program_table p ON m.program_id = p.id
    JOIN 
        batch_table b ON m.batch_id = b.id
    LEFT JOIN 
        student_allocations sa ON sa.module_id = m.id AND sa.batch_id = b.id
    GROUP BY 
        m.module_name, m.deadline, p.program_name, b.batch_name;
"; // Query that includes program, batch, and student count

$result = $conn->query($sql);

$events = [];
$currentDate = new DateTime(); // Get the current date

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $deadline = new DateTime($row['deadline']);
        $difference = $currentDate->diff($deadline); // Calculate the difference

        // Format the difference as days
        $daysRemaining = $difference->format('%R%a days');

        // Create the event object
        $events[] = [
            'title' => $row['program_name'], // Initially show the program name
            'start' => $row['deadline'],      // Deadline date
            'description' => "Deadline in: " . $daysRemaining, // Difference description
            'program_name' => $row['program_name'],
            'batch_name' => $row['batch_name'],
            'module_name' => $row['module_name'],
            'student_count' => $row['student_count'] // Add student count for the batch/module
        ];
    }
} else {
    echo "No events found.";
    exit();
}

// Return events as JSON
header('Content-Type: application/json');
echo json_encode($events);

// Close database connection
$conn->close();
