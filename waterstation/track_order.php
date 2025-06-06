<?php
session_start();
require 'config.php';

$tracking = $_POST['code'] ?? '';
$name = $_POST['guest_name'] ?? '';
$contact = $_POST['guest_contact'] ?? '';
$order = null;
$order_items = [];

if (!empty($tracking) || !empty($name) || !empty($contact)) {
    $query = "SELECT * FROM orders WHERE 1=1";
    $params = [];
    $types = '';

    if (!empty($tracking)) {
        $query .= " AND tracking_code = ?";
        $params[] = $tracking;
        $types .= 's';
    }
    if (!empty($name)) {
        $query .= " AND guest_name LIKE ?";
        $params[] = "%$name%";
        $types .= 's';
    }
    if (!empty($contact)) {
        $query .= " AND guest_contact LIKE ?";
        $params[] = "%$contact%";
        $types .= 's';
    }

    if ($stmt = $conn->prepare($query)) {
        if (!empty($params)) {
            $stmt->bind_param($types, ...$params);
        }
        $stmt->execute();
        $result = $stmt->get_result();
        $order = $result->fetch_assoc();

        // Fetch items if order found
        if ($order) {
            $order_id = $order['id'];
            $stmt_items = $conn->prepare("
                SELECT oi.quantity, oi.price, p.name AS product_name
                FROM order_items oi
                JOIN products p ON oi.product_id = p.id
                WHERE oi.order_id = ?
            ");
            $stmt_items->bind_param("i", $order_id);
            $stmt_items->execute();
            $res_items = $stmt_items->get_result();
            $order_items = $res_items->fetch_all(MYSQLI_ASSOC);
            $stmt_items->close();
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Track My Order</title>
    <link rel="stylesheet" href="style.css" />
    <style>
        .no-bullets li {
            list-style-type: none;
            margin-bottom: 5px;
        }
    </style>
</head>
<body>

<?php include 'navbar_guest.php'; ?>

<div class="form-container">
    <h2>Track My Order</h2>

    <form method="POST" action="track_order.php">
        <input type="text" name="code" placeholder="Tracking Number" value="<?= htmlspecialchars($tracking) ?>"><br>
        <input type="text" name="guest_name" placeholder="Name" value="<?= htmlspecialchars($name) ?>"><br>
        <input type="text" name="guest_contact" placeholder="Contact Number" value="<?= htmlspecialchars($contact) ?>">
        <button type="submit">Track</button>
    </form>

    <?php if ($tracking && !$order): ?>
        <p style="color:red;">❌ No order found for this information.</p>
    <?php endif; ?>

    <?php if ($order): ?>
        <br>
        <h3>Order Details</h3>
        <p><strong>Name:</strong> <?= htmlspecialchars($order['guest_name']) ?></p>
        <p><strong>Contact:</strong> <?= htmlspecialchars($order['guest_contact']) ?></p>
        <p><strong>Address:</strong> <?= htmlspecialchars($order['guest_address']) ?></p>
        <p><strong>Tracking Number:</strong> <?= htmlspecialchars($order['tracking_code']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($order['created_at']) ?></p>
        
        <h4>Items:</h4>
        <?php if (!empty($order_items)): ?>
            <ul class="no-bullets">
            <?php 
            $total = 0;
            foreach ($order_items as $item): 
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
                <li>
                    <?= htmlspecialchars($item['product_name']) ?> × <?= $item['quantity'] ?>
                    — ₱<?= number_format($subtotal, 2) ?>
                </li>
            <?php endforeach; ?>
            </ul>
            <p><strong>Total:</strong> ₱<?= number_format($total, 2) ?></p>
        <?php else: ?>
            <p>No items found for this order.</p>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>
