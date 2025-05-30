<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<nav>
    <ul>
        <li><a href="shop.php">Shop</a></li>
        <li><a href="cart.php">Cart</a></li>

        <?php if (isset($_SESSION['role'])): ?>
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li><a href="admin_products.php">Product Manager</a></li>
                <li><a href="admin_users.php">User Management</a></li>
                <li><a href="admin_orders.php">Orders</a></li>
            <?php elseif ($_SESSION['role'] === 'staff'): ?>
                <li><a href="staff_dashboard.php">Staff Dashboard</a></li>
                <li><a href="admin_orders.php">Orders</a></li>
            <?php else: ?>
                <li><a href="dashboard.php">My Account</a></li>
            <?php endif; ?>
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
        <?php endif; ?>

        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>
