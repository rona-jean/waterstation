<?php
session_start();
include('db.php');
require 'config.php';

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    header("Location: products.php"); // Adjust this if needed
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Guest Checkout</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h2>Guest Checkout</h2>

<form method="POST" action="guest_order.php">
    <input type="text" name="guest_name" placeholder="Name" required>

    <label for="guest_address">Address:</label>
    <textarea name="guest_address" required></textarea>

    <h3>Order Summary</h3>
    <ul>
        <?php
        $total = 0;
        foreach ($_SESSION['cart'] as $item):
            $subtotal = $item['price'] * $item['quantity'];
            $total += $subtotal;
        ?>
            <li>
                <?php echo htmlspecialchars($item['name'] ?? 'Product'); ?>
                — ₱<?php echo number_format($item['price'], 2); ?>
                × <?php echo $item['quantity']; ?>
                = ₱<?php echo number_format($subtotal, 2); ?>
            </li>
        <?php endforeach; ?>
    </ul>

    <p><strong>Total: ₱<?php echo number_format($total, 2); ?></strong></p>

    <button type="submit">Place Order</button>
</form>
</body>
</html>
