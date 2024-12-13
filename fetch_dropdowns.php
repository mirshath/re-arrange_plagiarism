<?php
include('./database/connection.php');

// $program_id = $_POST['program_id'];
// $type = $_POST['type'];

// if ($type == 'module') {
//     $query = "SELECT id, module_name FROM module_table WHERE program_id = ?";
// } else if ($type == 'batch') {
//     $query = "SELECT id, batch_name FROM batch_table WHERE program_id = ?";
// }

// $stmt = $conn->prepare($query);
// $stmt->bind_param("i", $program_id);
// $stmt->execute();
// $result = $stmt->get_result();

// $options = "<option value=''>Select " . ucfirst($type) . "</option>";
// while ($row = $result->fetch_assoc()) {
//     $options .= "<option value='{$row['id']}'>{$row[$type . '_name']}</option>";
// }
// echo $options;

// $stmt->close();
// $conn->close();
?>
