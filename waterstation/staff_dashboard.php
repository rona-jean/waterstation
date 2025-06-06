<?php
session_start();
include('navbar_staff.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'staff') {
    echo "Access denied.";
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Staff Dashboard</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<div class="form-container">
    <h2>👷 Staff Dashboard</h2>
    <p>Welcome, <?php echo $_SESSION['username']; ?>!</p>
    <p>Your role: <?php echo $_SESSION['role']; ?></p>
<!--
    <ul>
        <li><a href="admin_orders.php">📦 View Customer Orders</a></li>
        <li><a href="admin_dashboard.php">Order Details</a></li>
        <li><a href="admin_guest_orders.php">Guest Orders</a></li>
        <li><a href="logout.php">🚪 Logout</a></li>
    </ul>
-->
</div>
</body>
</html>
