<?php
session_start();
include 'db.php';
include 'navbar.php';

if (!isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied.";
    exit();
}

$filter = $_GET['filter'] ?? 'day';
$start = $end = "";

// Determine date range based on filter
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

// Fetch sales data
$query = "
    SELECT o.id AS order_id, o.created_at, o.status, oi.product_id, p.name, oi.quantity, oi.price 
    FROM orders o
    JOIN order_items oi ON o.id = oi.order_id
    JOIN products p ON oi.product_id = p.id
    WHERE o.created_at BETWEEN '$start' AND '$end'
    ORDER BY o.created_at DESC
";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Sales Report</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background-color: #2196F3; color: white; }
        select { padding: 5px; margin-bottom: 10px; }
    </style>
</head>
<body>

<div class="form-container">
    <h2>📊 Sales Report</h2>

    <form method="GET">
        <label for="filter">Filter by:</label>
        <select name="filter" onchange="this.form.submit()">
            <option value="day" <?= $filter === 'day' ? 'selected' : '' ?>>Today</option>
            <option value="week" <?= $filter === 'week' ? 'selected' : '' ?>>This Week</option>
            <option value="month" <?= $filter === 'month' ? 'selected' : '' ?>>This Month</option>
        </select>
    </form>

    <form method="GET" action="export_sales.php" style="margin-bottom: 10px;">
        <input type="hidden" name="filter" value="<?= $filter ?>">
        <button type="submit">📥 Export CSV</button>
    </form>


    <table>
        <tr>
            <th>Order ID</th><th>Date</th><th>Status</th><th>Product</th><th>Qty</th><th>Price</th><th>Subtotal</th>
        </tr>
        <?php
        $total = 0;
        while ($row = $result->fetch_assoc()):
            $subtotal = $row['quantity'] * $row['price'];
            $total += $subtotal;
        ?>
        <tr>
            <td><?= $row['order_id'] ?></td>
            <td><?= $row['created_at'] ?></td>
            <td><?= $row['status'] ?></td>
            <td><?= htmlspecialchars($row['name']) ?></td>
            <td><?= $row['quantity'] ?></td>
            <td>₱<?= number_format($row['price'], 2) ?></td>
            <td>₱<?= number_format($subtotal, 2) ?></td>
        </tr>
        <?php endwhile; ?>
        <tr>
            <td colspan="6"><strong>Total Sales</strong></td>
            <td><strong>₱<?= number_format($total, 2) ?></strong></td>
        </tr>
    </table>
</div>
</body>
</html>
