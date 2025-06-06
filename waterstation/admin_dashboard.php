<?php
session_start();
include 'db.php';
include 'navbar.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin' &&  $_SESSION['role'] !== 'staff') {
    echo "Access denied.";
    exit();
}

// Today's date range
$today_start = date('Y-m-d 00:00:00');
$today_end = date('Y-m-d 23:59:59');

// Total orders today
$total_orders_today = $conn->query("SELECT COUNT(*) AS total FROM orders WHERE created_at BETWEEN '$today_start' AND '$today_end'")->fetch_assoc()['total'];

// Total revenue today
$total_revenue_today = $conn->query("
    SELECT SUM(order_items.price * order_items.quantity) AS total 
    FROM orders 
    JOIN order_items ON orders.id = order_items.order_id 
    WHERE orders.created_at BETWEEN '$today_start' AND '$today_end'
")->fetch_assoc()['total'] ?? 0;

// Order status breakdown
$status_counts = [];
$statuses = ['Pending', 'Processing', 'Out for Delivery', 'Delivered', 'Cancelled'];
foreach ($statuses as $status) {
    $status_counts[$status] = $conn->query("SELECT COUNT(*) AS count FROM orders WHERE status = '$status'")->fetch_assoc()['count'];
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="form-container">
    <h2>📊 Admin Dashboard – Live Order Stats</h2>
    <ul>
        <li>Total Orders Today: <?= $total_orders_today ?></li>
        <li>Total Revenue Today: ₱<?= number_format($total_revenue_today, 2) ?></li>
        <?php foreach ($status_counts as $status => $count): ?>
            <li><?= $status ?> Orders: <?= $count ?></li>
        <?php endforeach; ?>
    </ul>
</div>
</body>
</html>
