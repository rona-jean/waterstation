<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: shop.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'];
$total = 0;
foreach ($cart as $item) {
    $total += $item['price'] * $item['quantity'];
}

// Fetch address from user table
$user_result = $conn->query("SELECT address FROM users WHERE id = $user_id");
$user_data = $user_result->fetch_assoc();
$address = $user_data['address'];

// Insert into orders
$stmt = $conn->prepare("INSERT INTO orders (user_id, total, address) VALUES (?, ?, ?)");
$stmt->bind_param("ids", $user_id, $total, $address);
$stmt->execute();
$order_id = $stmt->insert_id;
$stmt->close();

// Insert each item into order_items
foreach ($cart as $item) {
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("iiid", $order_id, $item['id'], $item['quantity'], $item['price']);
    $stmt->execute();
    $stmt->close();
}

unset($_SESSION['cart']);
echo "<script>alert('Order successfully checked out'); window.location.href='shop.php';</script>";
?>