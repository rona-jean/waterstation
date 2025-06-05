<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['role']) && $_SESSION['role'] === 'staff'): ?>
<nav>
    <ul>
        <li><a href="staff_dashboard.php">Staff Dashboard</a></li>
        <li><a href="admin_orders.php">Orders</a></li>
        <li><a href="admin_dashboard.php">Order Details</a></li>
        <li><a href="admin_guest_orders.php">Guest Orders</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>
<?php endif; ?>
