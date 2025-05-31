<?php
session_start();

$product_id = $_POST['product_id'];
$product = $_POST['product'];
$price = $_POST['price'];
$quantity = $_POST['quantity'];

$item = [
    'product_id' => $product_id,
    'name' => $product,
    'price' => $price,
    'quantity' => $quantity
];

$_SESSION['cart'][] = $item;

header("Location: cart.php");
exit();
?>
