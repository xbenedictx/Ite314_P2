<?php
include '../db.php';
header('Content-Type: application/json');

$data = [];
$result = $conn->query("SELECT * FROM items");
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);
?>