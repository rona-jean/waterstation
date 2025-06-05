<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch orders for this user, newest first
$orders = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");

// Fetch borrowed containers for this user, newest first
$borrowed = $conn->query("
    SELECT b.*, p.name AS product_name
    FROM borrowed_containers b
    JOIN products p ON b.product_id = p.id
    WHERE b.user_id = $user_id
    ORDER BY b.borrowed_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Order History</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar_user.php'; ?>
<div class="page-container">

    <h2>My Orders</h2>
    <?php if ($orders && $orders->num_rows > 0): ?>
        <?php while ($order = $orders->fetch_assoc()): ?>
            <div style="border:1px solid #ccc; padding: 10px; margin-bottom: 15px;">
                <p><strong>Order #<?= htmlspecialchars($order['id']) ?></strong></p>
                <p>Status: <?= htmlspecialchars($order['status']) ?></p>
                <p>Date: <?= htmlspecialchars($order['created_at']) ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>You have no orders yet.</p>
    <?php endif; ?>

    <h2>Borrowed Containers</h2>
    <?php if ($borrowed && $borrowed->num_rows > 0): ?>
        <?php while ($row = $borrowed->fetch_assoc()): ?>
            <div style="border:1px solid #ccc; padding: 10px; margin-bottom: 10px;">
                <p><strong><?= htmlspecialchars($row['product_name']) ?></strong></p>
                <p>Borrowed At: <?= htmlspecialchars($row['borrowed_at']) ?></p>
                <p>Status: <?= ($row['returned'] ? "Returned on " . htmlspecialchars($row['returned_at']) : "Not Returned") ?></p>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p>You have no borrowed containers currently.</p>
    <?php endif; ?>

</div>
</body>
</html>
