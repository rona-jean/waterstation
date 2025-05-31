<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || empty($_SESSION['cart'])) {
    header("Location: shop.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$cart = $_SESSION['cart'];

// Get address from user
$user_result = $conn->query("SELECT address FROM users WHERE id = $user_id");
$user_data = $user_result->fetch_assoc();
$address = $user_data['address'];

// Insert into orders (status and created_at now required; guest fields set to NULL)
$status = 'Pending';
$guest_name = null;
$guest_address = null;

$stmt = $conn->prepare("
    INSERT INTO orders (user_id, status, created_at, address, guest_name, guest_address) 
    VALUES (?, ?, NOW(), ?, ?, ?)
");
$stmt->bind_param("issss", $user_id, $status, $address, $guest_name, $guest_address);
$stmt->execute();
$order_id = $stmt->insert_id;
$stmt->close();

// Insert each item into order_items
foreach ($cart as $item) {
    $stmt = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    
    // ✅ Inserted here
    $stmt->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $item['price']);
    
    $stmt->execute();
    $stmt->close();
}

// Clear cart and redirect
unset($_SESSION['cart']);
echo "<script>alert('Order successfully checked out'); window.location.href='shop.php';</script>";
?>
