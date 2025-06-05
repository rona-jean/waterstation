<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' &&  $_SESSION['role'] !== 'staff') {
    echo "Access denied.";
    exit;
}

$guest_orders = $conn->query("SELECT * FROM orders WHERE user_id IS NULL ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Guest Orders - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="page-container">
    <h2>📦 Guest Orders</h2>

    <a href="admin_orders.php" style="background-color: #0a74da; color: white; padding: 8px 16px; text-decoration: none; border-radius: 5px; margin-bottom: 15px; display: inline-block;">
         View User's Orders
    </a>

    <?php while ($order = $guest_orders->fetch_assoc()): ?>
        <div style="border: 1px solid #ccc; margin-bottom: 20px; padding: 10px; border-radius: 8px;">
            <h3>Order #<?= $order['id'] ?></h3>
            <p><strong>Guest Name:</strong> <?= htmlspecialchars($order['guest_name']) ?></p>
            <p><strong>Address:</strong> <?= htmlspecialchars($order['guest_address']) ?></p>
            <p><strong>Total:</strong> ₱<?= number_format($order['total'], 2) ?></p>
            <p><strong>Status:</strong> <?= $order['status'] ?></p>
            <p><strong>Ordered At:</strong> <?= $order['created_at'] ?></p>

            <h4>Items:</h4>
            <ul>
            <?php
                $stmt = $conn->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                $stmt->bind_param("i", $order['id']);
                $stmt->execute();
                $items = $stmt->get_result();
                while ($item = $items->fetch_assoc()):
            ?>
                <li><?= htmlspecialchars($item['name']) ?> — ₱<?= $item['price'] ?> x <?= $item['quantity'] ?></li>
            <?php endwhile; $stmt->close(); ?>
            </ul>

            <form method="POST" action="update_guest_status.php" style="margin-top: 10px;">
                <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
                <label for="status">Update Status:</label>
                <select name="status">
                    <option value="Pending" <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
                    <option value="Preparing" <?= $order['status'] == 'Preparing' ? 'selected' : '' ?>>Preparing</option>
                    <option value="Out for Delivery" <?= $order['status'] == 'Out for Delivery' ? 'selected' : '' ?>>Out for Delivery</option>
                    <option value="Delivered" <?= $order['status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
                </select>
                <button type="submit" name="update_status">Update</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
