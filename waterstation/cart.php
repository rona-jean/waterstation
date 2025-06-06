<?php 
session_start();
require 'config.php';

// ✅ Dynamic navbar inclusion
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        include 'navbar_admin.php';
    } elseif ($_SESSION['role'] === 'staff') {
        include 'navbar_staff.php';
    } elseif ($_SESSION['role'] === 'user') {
        include 'navbar_user.php';
    }
} else {
    include 'navbar_guest.php';
}

// ✅ Handle adding to cart
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'], $_POST['quantity'])) {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];

    // Fetch product info from DB
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $product = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if ($product && $quantity > 0) {
        if (!isset($_SESSION['cart'])) {
            $_SESSION['cart'] = [];
        }

        // Check if product already in cart
        $found = false;
        foreach ($_SESSION['cart'] as &$item) {
            if ($item['product_id'] == $product_id) {
                $item['quantity'] += $quantity;
                $found = true;
                break;
            }
        }
        unset($item); // Break reference

        // If not found, add new item
        if (!$found) {
            $_SESSION['cart'][] = [
                'product_id' => $product_id,
                'product_name' => $product['name'],
                'price' => $product['price'],
                'quantity' => $quantity
            ];
        }

        header("Location: cart.php"); // Prevent resubmission
        exit;
    }
}

// ✅ Handle clearing cart
if (isset($_POST['clear_cart'])) {
    unset($_SESSION['cart']);
    header("Location: cart.php");
    exit;
}

// ✅ Determine checkout page
$checkoutPage = isset($_SESSION['role']) ? 'checkout.php' : 'guest_checkout.php';
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Your Cart - Water Refilling Station</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="form-container">
    <h2>Your Cart</h2>

    <?php if (!empty($_SESSION['cart'])): ?>
        <ul class="no-bullets">
            <?php
            $total = 0;
            foreach ($_SESSION['cart'] as $item):
                $subtotal = $item['price'] * $item['quantity'];
                $total += $subtotal;
            ?>
                <li>
                    <?= htmlspecialchars($item['product']); ?> — 
                    ₱<?= number_format($item['price'], 2); ?> × <?= $item['quantity']; ?> =
                    <strong>₱<?= number_format($subtotal, 2); ?></strong>
                </li>
            <?php endforeach; ?>
        </ul>
        <br>
        <h3>Total: ₱<?= number_format($total, 2); ?></h3>
        <br>
        <!-- Checkout button -->
        <form action="<?= $checkoutPage ?>" method="POST">
            <button type="submit">✅ Checkout</button>
        </form>

        <!-- Clear cart -->
        <form action="cart.php" method="POST" style="margin-top: 10px;">
            <button type="submit" name="clear_cart">🗑️ Clear Cart</button>
        </form>
    <?php else: ?>
        <p>Your cart is empty.</p>
    <?php endif; ?>
    
    <br>
    <p><a href="shop.php">⬅ Back to Shop</a></p>
</div>

</body>
</html>
