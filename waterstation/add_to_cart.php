<?php
session_start();

$product = $_POST['product'];
$price = $_POST['price'];
$quantity = $_POST['quantity'];

$item = [
    'product' => $product,
    'price' => $price,
    'quantity' => $quantity
];

$_SESSION['cart'][] = $item;

header("Location: cart.php");
exit();
?>
