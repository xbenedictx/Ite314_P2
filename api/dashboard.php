<?php
include '../db.php';
header('Content-Type: application/json');

$data = [];

$data['totals'] = [
    'items' => $conn->query("SELECT COUNT(*) AS total_items FROM items")->fetch_assoc()['total_items'],
    'sales' => $conn->query("SELECT COUNT(*) AS total_sales FROM transactions")->fetch_assoc()['total_sales'],
    'revenue' => $conn->query("SELECT SUM(total_amount) AS revenue FROM transactions")->fetch_assoc()['revenue'],
    'customers' => $conn->query("SELECT COUNT(*) AS total_customers FROM customer")->fetch_assoc()['total_customers']
];

$rangeRes = $conn->query("SELECT MIN(date_added) AS min_date, MAX(date_added) AS max_date FROM transactions");
$range = $rangeRes->fetch_assoc();
$startDate = new DateTime($range['min_date']);
$endDate   = new DateTime($range['max_date']);

$dateMap = [];
$period = new DatePeriod($startDate, new DateInterval('P1D'), $endDate->modify('+1 day'));
foreach ($period as $date) {
    $dateMap[$date->format("Y-m-d")] = ['date'=>$date->format("Y-m-d"), 'num_transactions'=>0, 'revenue'=>0];
}

$res = $conn->query("SELECT date_added, SUM(quantity) AS total_quantity 
                     FROM transactions 
                     GROUP BY date_added 
                     ORDER BY date_added");
$quantities = [];
while ($row = $res->fetch_assoc()) {
    $quantities[$row['date_added']] = (int)$row['total_quantity'];
}

$rangeRes = $conn->query("SELECT MIN(date_added) AS min_date, MAX(date_added) AS max_date FROM transactions");
$range = $rangeRes->fetch_assoc();
$startDate = new DateTime($range['min_date']);
$endDate   = new DateTime($range['max_date']);

$dateMap = [];
$period = new DatePeriod($startDate, new DateInterval('P1D'), $endDate->modify('+1 day'));
foreach ($period as $date) {
    $d = $date->format("Y-m-d");
    $dateMap[] = [
        'date' => date("M d, Y", strtotime($d)),
        'total_quantity' => $quantities[$d] ?? 0
    ];
}
$data['transactions_over_time'] = $dateMap;

$res = $conn->query("SELECT SUM(stock_quantity) AS total_stock FROM items");
$total_stock = $res->fetch_assoc()['total_stock'];

$res = $conn->query("SELECT SUM(quantity) AS total_sold FROM transactions");
$total_sold = $res->fetch_assoc()['total_sold'];

$data['stock_vs_sold'] = [
    'stock' => (int)$total_stock,
    'sold' => (int)$total_sold
];

$res = $conn->query("SELECT i.item_name, SUM(t.quantity) AS total_sold 
                     FROM transactions t 
                     JOIN items i ON t.item_id = i.item_id 
                     GROUP BY t.item_id 
                     ORDER BY total_sold DESC 
                     LIMIT 5");
$hot_items = [];
while ($row = $res->fetch_assoc()) {
    $hot_items[] = $row;
}
$data['hot_items'] = $hot_items;

$res = $conn->query("SELECT c.address, SUM(t.total_amount) AS total_sales 
                     FROM transactions t 
                     JOIN customer c ON t.customer_id = c.customer_id 
                     GROUP BY c.address 
                     ORDER BY total_sales DESC");
$addr_sales = [];
while ($row = $res->fetch_assoc()) {
    $addr_sales[] = $row;
}
$data['sales_by_address'] = $addr_sales;

echo json_encode($data);
?>