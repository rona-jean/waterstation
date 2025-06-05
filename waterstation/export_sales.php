<?php
session_start();
include 'db.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied.";
    exit();
}

$filter = $_GET['filter'] ?? 'day';

// Date range logic
switch ($filter) {
    case 'week':
        $start = date('Y-m-d 00:00:00', strtotime('monday this week'));
        $end = date('Y-m-d 23:59:59', strtotime('sunday this week'));
        break;
    case 'month':
        $start = date('Y-m-01 00:00:00');
        $end = date('Y-m-t 23:59:59');
        break;
    case 'day':
    default:
        $start = date('Y-m-d 00:00:00');
        $end = date('Y-m-d 23:59:59');
        break;
}

// Query data
$query = "
    SELECT o.id AS order_id, o.created_at, o.status, p.name AS product_name, oi.quantity, oi.price
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.created_at BETWEEN '$start' AND '$end'
    ORDER BY o.created_at DESC
";

$result = $conn->query($query);

// Set headers for download
header('Content-Type: text/csv');
header('Content-Disposition: attachment;filename="sales_report_' . $filter . '.csv"');

// Open output stream
$output = fopen('php://output', 'w');

// Output CSV headers
fputcsv($output, ['Order ID', 'Date', 'Status', 'Product', 'Quantity', 'Price', 'Subtotal']);

$total = 0;

// Output rows
while ($row = $result->fetch_assoc()) {
    $subtotal = $row['quantity'] * $row['price'];
    $total += $subtotal;
    fputcsv($output, [
        $row['order_id'],
        $row['created_at'],
        $row['status'],
        $row['product_name'],
        $row['quantity'],
        number_format($row['price'], 2),
        number_format($subtotal, 2)
    ]);
}

// Output total row
fputcsv($output, ['', '', '', '', '', 'Total Sales', number_format($total, 2)]);

fclose($output);
exit;
