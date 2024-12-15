<?php
// Include database connection
include("../database/connection.php");

// Query to fetch module deadlines
$sql = "SELECT m.module_name, m.deadline, p.program_name 
        FROM module_table m
        JOIN program_table p ON m.program_id = p.id";
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
            'title' =>  $row['module_name'], // Program and module name
            'start' => $row['deadline'],                                  // Deadline date
            'description' => "Deadline in: " . $daysRemaining,             // Difference description
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
?>
