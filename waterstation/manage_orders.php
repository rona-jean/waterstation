<?php
session_start();
require 'config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['order_id'], $_POST['status'])) {
    $order_id = intval($_POST['order_id']);
    $status = $_POST['status'];
    $stmt = $conn->prepare("UPDATE orders SET status = ? WHERE id = ?");
    $stmt->bind_param("si", $status, $order_id);
    $stmt->execute();
    $stmt->close();
}

$orders = $conn->query("SELECT orders.*, users.username FROM orders JOIN users ON orders.user_id = users.id ORDER BY created_at DESC");
?>
<!DOCTYPE html>
<html>
<head>
  <title>Manage Orders</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="page-container">
  <h2>Manage Orders</h2>
  <?php while($order = $orders->fetch_assoc()): ?>
    <div class="order-box">
      <p><strong>Order ID:</strong> <?= $order['id'] ?></p>
      <p><strong>User:</strong> <?= htmlspecialchars($order['username']) ?></p>
      <p><strong>Total:</strong> ₱<?= number_format($order['total'], 2) ?></p>
      <p><strong>Status:</strong> <?= $order['status'] ?></p>
      <form method="POST" style="margin-top: 10px;">
        <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
        <select name="status">
          <option <?= $order['status'] == 'Pending' ? 'selected' : '' ?>>Pending</option>
          <option <?= $order['status'] == 'Preparing' ? 'selected' : '' ?>>Preparing</option>
          <option <?= $order['status'] == 'Out for Delivery' ? 'selected' : '' ?>>Out for Delivery</option>
          <option <?= $order['status'] == 'Delivered' ? 'selected' : '' ?>>Delivered</option>
          <option <?= $order['status'] == 'Cancelled' ? 'selected' : '' ?>>Cancelled</option>
        </select>
        <button type="submit">Update</button>
      </form>
    </div>
  <?php endwhile; ?>
</div>
</body>
</html>
