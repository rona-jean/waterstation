<?php
session_start();
require 'config.php';

// Only admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

// Update order status
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    $stmt->close();
}

// Fetch orders with user names
$orders = $conn->query("SELECT o.*, u.name AS customer_name FROM orders o JOIN users u ON o.user_id = u.id ORDER BY o.created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Orders - Admin</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="page-container">
    <h2>Manage Orders</h2>

    <?php while ($order = $orders->fetch_assoc()): ?>
        <div style="border: 1px solid #ccc; margin-bottom: 20px; padding: 10px; border-radius: 8px;">
            <h3>Order #<?php echo $order['id']; ?></h3>
            <p><strong>Customer:</strong> <?php echo htmlspecialchars($order['customer_name']); ?> (User ID: <?php echo $order['user_id']; ?>)</p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($order['address']); ?></p>
            <p><strong>Total:</strong> ₱<?php echo number_format($order['total'], 2); ?></p>
            <p><strong>Status:</strong> <?php echo $order['status']; ?></p>
            <p><strong>Ordered At:</strong> <?php echo $order['created_at']; ?></p>

            <h4>Items:</h4>
            <ul>
                <?php
                $stmt = $conn->prepare("SELECT oi.*, p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
                $stmt->bind_param("i", $order['id']);
                $stmt->execute();
                $items = $stmt->get_result();
                while ($item = $items->fetch_assoc()):
                ?>
                    <li><?php echo htmlspecialchars($item['name']); ?> — ₱<?php echo $item['price']; ?> x <?php echo $item['quantity']; ?></li>
                <?php endwhile; $stmt->close(); ?>
            </ul>

            <form method="POST" style="margin-top: 10px;">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                <label for="status">Update Status:</label>
                <select name="status">
                    <option value="Pending" <?php if ($order['status'] == 'Pending') echo 'selected'; ?>>Pending</option>
                    <option value="Preparing" <?php if ($order['status'] == 'Preparing') echo 'selected'; ?>>Preparing</option>
                    <option value="Out for Delivery" <?php if ($order['status'] == 'Out for Delivery') echo 'selected'; ?>>Out for Delivery</option>
                    <option value="Delivered" <?php if ($order['status'] == 'Delivered') echo 'selected'; ?>>Delivered</option>
                </select>
                <button type="submit" name="update_status">Update</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
