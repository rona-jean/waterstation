<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
<nav>
    <ul>
        <li><a href="admin_dashboard.php">Admin Dashboard</a></li>
        <li><a href="admin_products.php">Product Manager</a></li>
        <li><a href="admin_users.php">User Management</a></li>
        <li><a href="admin_orders.php">Orders</a></li>
        <li><a href="manage_borrowed.php">Borrowed Containers</a></li>
        <li><a href="sales_report.php">Sales Report</a></li>
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>
<?php endif; ?>
