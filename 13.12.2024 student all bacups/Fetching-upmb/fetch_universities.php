<?php
// Database connection
include('../database/connection.php');

$query = "SELECT * FROM universities";
$result = mysqli_query($conn, $query);

$universities = [];
while ($row = mysqli_fetch_assoc($result)) {
    $universities[] = $row;
}

echo json_encode($universities);
