<?php
session_start();
include 'db.php';
include 'navbar_user.php';

// Check if user is logged in or not
$is_guest = !isset($_SESSION['user_id']);

if ($is_guest) {
    // Handle guest orders (based on session or cookies if needed)
    echo "<h2 style='text-align:center;'>Guest orders are not yet supported here. Please login to view registered orders.</h2>";
    exit;
} else {
    $user_id = $_SESSION['user_id'];

    // Handle cancel request
    if (isset($_GET['cancel_order_id'])) {
        $cancel_order_id = intval($_GET['cancel_order_id']);
        // Only cancel if still pending and belongs to user
        $stmt = $conn->prepare("UPDATE orders SET status = 'Cancelled' WHERE id = ? AND user_id = ? AND status = 'Pending'");
        $stmt->bind_param("ii", $cancel_order_id, $user_id);
        $stmt->execute();
        $stmt->close();
    }

    // Fetch user orders
    $sql = "SELECT o.id AS order_id, o.status, o.created_at,
                   GROUP_CONCAT(CONCAT(p.name, ' (x', oi.quantity, ')') SEPARATOR ', ') AS items,
                   SUM(oi.quantity * oi.price) AS total
            FROM orders o
            JOIN order_items oi ON o.id = oi.order_id
            JOIN products p ON oi.product_id = p.id
            WHERE o.user_id = ?
            GROUP BY o.id
            ORDER BY o.created_at DESC";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Orders</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .orders-container {
            max-width: 800px;
            margin: 20px auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background-color: white;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 12px;
        }
        th {
            background-color: rgb(15, 101, 180);
            color: white;
        }
        .cancel-btn {
            color: red;
            text-decoration: none;
            font-weight: bold;
        }

        .order-tracking {
            display: flex;
            justify-content: space-between;
            margin-top: 10px;
            font-size: 13px;
            position: relative;
        }

        .order-tracking::before {
            content: '';
            position: absolute;
            top: 16px;
            left: 0;
            right: 0;
            height: 4px;
            background: #ddd;
            z-index: 0;
        }

        .order-tracking .step {
            flex: 1;
            text-align: center;
            z-index: 1;
            color: #aaa;
            position: relative;
        }

        .order-tracking .step.active {
            color: green;
            font-weight: bold;
        }

        .order-tracking .step::before {
            content: "●";
            font-size: 18px;
            display: block;
            margin-bottom: 5px;
        }

        .order-tracking .step.active::before {
            color: green;
        }

    </style>
</head>
<body>
<div class="orders-container">
    <h2>📦 My Orders</h2>

    <?php if ($result->num_rows > 0): ?>
    <table>
        <tr>
            <th>Order ID</th>
            <th>Date</th>
            <th>Items</th>
            <th>Total (₱)</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= $row['order_id'] ?></td>
            <td><?= date("M d, Y h:i A", strtotime($row['created_at'])) ?></td>
            <td><?= htmlspecialchars($row['items']) ?></td>
            <td>₱<?= number_format($row['total'], 2) ?></td>
            <td><?= htmlspecialchars($row['status']) ?></td>
            <td>
                <?php if ($row['status'] === 'Pending'): ?>
                    <a href="user_orders.php?cancel_order_id=<?= $row['order_id'] ?>" class="cancel-btn" onclick="return confirm('Cancel this order?')">Cancel</a>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
        </tr>

        <tr>
            <td colspan="6">
                <?php $status = strtolower($row['status']); ?>
                <div class="order-tracking">
                    <div class="step <?= $status == 'pending' || $status != 'pending' ? 'active' : '' ?>">Order Placed</div>
                    <div class="step <?= $status == 'preparing' || in_array($status, ['out for delivery', 'paid', 'delivered']) ? 'active' : '' ?>">Preparing</div>
                    <div class="step <?= $status == 'out for delivery' || in_array($status, ['paid', 'delivered']) ? 'active' : '' ?>">Out for Delivery</div>
                    <div class="step <?= $status == 'paid' || $status == 'delivered' ? 'active' : '' ?>">Paid</div>
                    <div class="step <?= $status == 'delivered' ? 'active' : '' ?>">Delivered</div>
                </div>
            </td>
        </tr>

        <?php endwhile; ?>
    </table>
    <?php else: ?>
        <p>You haven't placed any orders yet.</p>
    <?php endif; ?>
</div>
</body>
</html>
