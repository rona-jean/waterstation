<?php 
session_start();
require 'config.php';

// 🔒 Only allow admins
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin' &&  $_SESSION['role'] !== 'staff') {
    header("Location: login.php");
    exit;
}

// ✅ Handle status update
if (isset($_POST['update_status'])) {
    $order_id = intval($_POST['order_id']);
    $new_status = $_POST['status'];

    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $new_status, $order_id);
    $stmt->execute();
    $stmt->close();
}

// 📦 Get all orders (including guest orders where user_id IS NULL)
$orders = $conn->query("SELECT * FROM orders ORDER BY created_at DESC");
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

    <a href="admin_guest_orders.php" style="background-color: #0a74da; color: white; padding: 8px 16px; text-decoration: none; border-radius: 5px; margin-bottom: 15px; display: inline-block;">
        📦 View Guest Orders
    </a>

    <?php while ($order = $orders->fetch_assoc()): ?>
        <div class="order-card" style="border: 1px solid #ccc; padding: 10px; margin-bottom: 20px; border-radius: 8px;">
            <h3>Order #<?php echo $order['id']; ?></h3>

            <?php if ($order['user_id']): ?>
                <?php
                // 🧑‍💼 Get the username from users table
                $stmt = $conn->prepare("SELECT username FROM users WHERE id = ?");
                $stmt->bind_param("i", $order['user_id']);
                $stmt->execute();
                $user_result = $stmt->get_result()->fetch_assoc();
                $username = $user_result['username'] ?? 'Unknown';
                $stmt->close();
                ?>
                <p><strong>Customer:</strong> <?php echo htmlspecialchars($username); ?> (User ID: <?php echo $order['user_id']; ?>)</p>
            <?php else: ?>
                <p><strong>Guest:</strong> <?php echo htmlspecialchars($order['guest_name']); ?></p>
            <?php endif; ?>

            <p><strong>Guest Contact:</strong> <?php echo htmlspecialchars($order['guest_contact'] ?? $order['guest_contact']); ?></p>
            <p><strong>Address:</strong> <?php echo htmlspecialchars($order['guest_address'] ?? $order['address']); ?></p>
            <p><strong>Tracking Number:</strong> <?php echo htmlspecialchars($order['tracking_code'] ?? $order['tracking_code']); ?></p>
            
            <?php
            // 💰 Get total amount
            $stmt = $conn->prepare("SELECT SUM(price * quantity) AS total FROM order_items WHERE order_id = ?");
            $stmt->bind_param("i", $order['id']);
            $stmt->execute();
            $total_result = $stmt->get_result()->fetch_assoc();
            $order_total = $total_result['total'] ?? 0;
            $stmt->close();
            ?>

            <p><strong>Total:</strong> ₱<?php echo number_format($order_total, 2); ?></p>
            <p><strong>Status:</strong> <?php echo $order['status']; ?></p>
            <p><strong>Ordered At:</strong> <?php echo $order['created_at']; ?></p>

            <h4>Items:</h4>
                <ul style="list-style-type: none; padding-left: 0;">
                    <?php
                    $stmt = $conn->prepare("SELECT oi.*, p.name 
                        FROM order_items oi 
                        LEFT JOIN products p ON oi.product_id = p.id 
                        WHERE oi.order_id = ?
                    ");
                    $stmt->bind_param("i", $order['id']);
                    $stmt->execute();
                    $items = $stmt->get_result();

                    if ($items->num_rows > 0):
                        while ($item = $items->fetch_assoc()):
                            $product_name = $item['name'] ?? 'Unknown Product';
                    ?>
                        <li><?php echo htmlspecialchars($product_name); ?> — ₱<?php echo $item['price']; ?> x <?php echo $item['quantity']; ?></li>
                    <?php
                        endwhile;
                    else:
                    ?>
                        <li><em>No items found for this order.</em></li>
                    <?php endif; $stmt->close(); ?>
                </ul>


            <!-- 📝 Status Update Form -->
            <form method="POST" style="margin-top: 10px;">
                <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                <label for="status">Update Status:</label>
                <select name="status">
                    <?php foreach (['Pending', 'Preparing', 'Out for Delivery', 'Delivered'] as $status): ?>
                        <option value="<?php echo $status; ?>" <?php if ($order['status'] === $status) echo 'selected'; ?>>
                            <?php echo $status; ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <button type="submit" name="update_status">Update</button>
            </form>
        </div>
    <?php endwhile; ?>
</div>

</body>
</html>
