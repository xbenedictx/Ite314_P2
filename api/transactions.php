<?php
include '../db.php';
header('Content-Type: application/json');

$data = [];
$result = $conn->query("SELECT transactions.*, CONCAT(customer.first_name, ' ', customer.last_name) as customer_name, items.item_name FROM transactions JOIN customer ON transactions.customer_id = customer.customer_id JOIN items ON transactions.item_id = items.item_id");
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}
echo json_encode($data);
?>