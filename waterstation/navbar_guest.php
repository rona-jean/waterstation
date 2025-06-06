<?php
if (session_status() === PHP_SESSION_NONE) session_start();

if (!isset($_SESSION['role'])): ?>
<nav>
    <ul>
        <li><a href="shop.php">Shop</a></li>
        <li><a href="track_order.php">Track Order</a></li>
        <li><a href="index.html">Login</a></li>
        <li><a href="register.html">Register</a></li>
    </ul>
</nav>
<?php endif; ?>
