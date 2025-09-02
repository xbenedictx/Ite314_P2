<?php
include '../db.php';
header('Content-Type: application/json');

$data = [];

try {
    // Total Items
    $result = $conn->query("SELECT COUNT(*) as count FROM items");
    $data['total_items'] = $result->fetch_assoc();
    $result_stock = $conn->query("SELECT SUM(stock_quantity) as total_stock FROM items");
    $data['total_items']['total_stock'] = $result_stock->fetch_assoc()['total_stock'];

    // Number of Sales
    $result = $conn->query("SELECT COUNT(*) as count FROM transactions");
    $data['num_sales'] = $result->fetch_assoc();
    $result_rev = $conn->query("SELECT SUM(total_amount) as total_rev FROM transactions");
    $data['num_sales']['total_rev'] = $result_rev->fetch_assoc()['total_rev'];

    // Hot Items
    $hot_items = [];
    $result = $conn->query("SELECT items.item_name, SUM(transactions.quantity) as total_sold FROM transactions JOIN items ON transactions.item_id = items.item_id GROUP BY transactions.item_id ORDER BY total_sold DESC LIMIT 5");
    while ($row = $result->fetch_assoc()) {
        $hot_items[] = $row;
    }
    $data['hot_items'] = $hot_items;

    // Number of Customers
    $result = $conn->query("SELECT COUNT(*) as count FROM customer");
    $data['num_customers'] = $result->fetch_assoc();
    $result_unique = $conn->query("SELECT COUNT(DISTINCT customer_id) as unique_sales FROM transactions");
    $data['num_customers']['unique_sales'] = $result_unique->fetch_assoc()['unique_sales'];

    echo json_encode($data);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>