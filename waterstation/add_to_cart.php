<?php
session_start();
require 'config.php'; // ✅ Needed to access the database

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    $productId = intval($_POST['product_id']);
    $productName = $_POST['product'];
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);

    // ✅ Fetch if the product is borrowable from the database
    $stmt = $conn->prepare("SELECT is_borrowable FROM products WHERE id = ?");
    $stmt->bind_param("i", $productId);
    $stmt->execute();
    $stmt->bind_result($isBorrowable);
    $stmt->fetch();
    $stmt->close();

    // ✅ Block guests or non-'user' roles from borrowing items
    if ($isBorrowable && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user')) {
        $_SESSION['flash'] = "Borrowing this item is only available to registered users.";
        header('Location: shop.php');
        exit();
    }

    // ✅ Add to cart
    $item = [
        'product_id' => $productId,
        'product' => $productName,
        'price' => $price,
        'quantity' => $quantity
    ];

    if (!isset($_SESSION['cart'])) {
        $_SESSION['cart'] = [];
    }

    $_SESSION['cart'][] = $item;

    $_SESSION['flash'] = "$productName has been added to your cart.";
    header('Location: shop.php');
    exit();
} else {
    header('Location: shop.php');
    exit();
}
