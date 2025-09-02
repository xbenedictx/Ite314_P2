<?php
include '../db.php';
header('Content-Type: application/json');

$data = [];
try {
    $result = $conn->query("SELECT * FROM items");
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>