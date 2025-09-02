<?php
include '../db.php';
header('Content-Type: application/json');

$data = [];
try {
    $result = $conn->query("SELECT transactions.*, CONCAT(customer.first_name, ' ', customer.last_name) as customer_name, items.item_name FROM transactions JOIN customer ON transactions.customer_id = customer.customer_id JOIN items ON transactions.item_id = items.item_id");
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
    echo json_encode($data);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>