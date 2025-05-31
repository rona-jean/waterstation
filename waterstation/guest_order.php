<?php
session_start();
include('db.php');
require 'config.php';

// Redirect if cart is empty
if (!isset($_SESSION['cart']) || count($_SESSION['cart']) === 0) {
    header("Location: products.php");
    exit;
}

// Check if guest submitted the form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $guest_name = trim($_POST['guest_name']);
    $guest_address = trim($_POST['guest_address']);

    // Validate required fields
    if (empty($guest_name) || empty($guest_address)) {
        echo "Please fill in all required fields.";
        exit;
    }

    // Insert order
    $stmt = $conn->prepare("INSERT INTO orders (user_id, guest_name, guest_address, status, created_at) VALUES (NULL, ?, ?, 'Pending', NOW())");
    $stmt->bind_param("ss", $guest_name, $guest_address);
    $stmt->execute();
    $order_id = $stmt->insert_id;
    $stmt->close();

    // Insert order items
    foreach ($_SESSION['cart'] as $item) {
        $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
        $stmt->execute();
        $stmt->close();
    }

    // Clear the cart after order is placed
    unset($_SESSION['cart']);

    // Redirect to thank you page
    header("Location: thank_you.php?order_id=" . $order_id);
    exit;
} else {
    echo "Invalid request.";
}
?>
