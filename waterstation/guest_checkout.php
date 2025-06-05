<?php
session_start();
require 'config.php';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guest_name = $_POST['guest_name'] ?? '';
    $guest_contact = $_POST['guest_contact'] ?? '';
    $guest_address = $_POST['guest_address'] ?? '';

    // Simple validation
    if (empty($guest_name) || empty($guest_contact) || empty($guest_address)) {
        $error = "Please fill in all the required fields.";
    } elseif (empty($_SESSION['cart'])) {
        $error = "Your cart is empty.";
    } else {
        // Generate a unique tracking code
        $tracking_code = 'GUEST' . strtoupper(bin2hex(random_bytes(4)));

        // Prepare insert for order
        $stmt = $conn->prepare("INSERT INTO orders (user_id, guest_name, guest_contact, guest_address, tracking_code, status, created_at) VALUES (NULL, ?, ?, ?, ?, 'Pending', NOW())");
        
        if (!$stmt) {
            die("SQL error: " . $conn->error);
        }

        $stmt->bind_param("ssss", $guest_name, $guest_contact, $guest_address, $tracking_code);
        $stmt->execute();
        $order_id = $stmt->insert_id;
        $stmt->close();

        // Insert order items
        $stmt_items = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        if (!$stmt_items) {
            die("SQL error (items): " . $conn->error);
        }

        foreach ($_SESSION['cart'] as $item) {
            $stmt_items->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
            $stmt_items->execute();
        }
        $stmt_items->close();

        // Store guest tracking code in session so guest can retrieve their order
        $_SESSION['guest_tracking_code'] = $tracking_code;

        // Clear cart
        unset($_SESSION['cart']);

        // Redirect to order confirmation
        header("Location: guest_order_success.php?tracking_code=$tracking_code");
        exit;
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Guest Checkout - Water Refilling Station</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar_guest.php'; ?>

<div class="form-container">
    <h2>Guest Checkout</h2>

    <?php if (isset($error)): ?>
        <p style="color: red;"><strong><?= $error ?></strong></p>
    <?php endif; ?>

    <form method="POST">
        <label for="guest_name">Name:</label>
        <input type="text" name="guest_name" id="guest_name" required>

        <label for="guest_contact">Contact Number:</label>
        <input type="text" name="guest_contact" id="guest_contact" required>

        <label for="guest_address">Address:</label>
        <textarea name="guest_address" id="guest_address" rows="4" required></textarea>

        <button type="submit">✅ Confirm Order</button>
    </form>

    <p><a href="cart.php">⬅ Back to Cart</a></p>
</div>
</body>
</html>
