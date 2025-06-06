<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$orders = $conn->query("SELECT * FROM orders WHERE user_id = $user_id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>My Orders</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="page-container">
  <h2>My Orders</h2>
  <?php while($order = $orders->fetch_assoc()): ?>
    <div class="order-box">
      <p><strong>Order ID:</strong> <?= $order['id'] ?></p>
      <p><strong>Total:</strong> ₱<?= number_format($order['total'], 2) ?></p>
      <p><strong>Status:</strong> <?= $order['status'] ?></p>
      <p><strong>Date:</strong> <?= $order['created_at'] ?></p>
    </div>
  <?php endwhile; ?>
</div>
</body>
</html>