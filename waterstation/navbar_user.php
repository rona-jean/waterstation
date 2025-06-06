<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>

<style>
    .navbar {
        background-color: rgb(15, 101, 180);
        padding: 10px;
        color: white;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .navbar a {
        color: white;
        text-decoration: none;
        margin: 0 10px;
        font-weight: bold;
    }

    .navbar a:hover {
        text-decoration: underline;
    }

    .navbar .right {
        display: flex;
        align-items: center;
    }

    .navbar .welcome {
        margin-right: 10px;
    }
</style>

<div class="navbar">
    <div class="left">
        <a href="dashboard.php">🏠 Home</a>
        <a href="shop.php">🛒 Shop</a>
        <a href="cart.php">🧺 Cart</a>
        <a href="order_history.php">Order History</a>
        <a href="user_orders.php">📦 My Orders</a>
    </div>
    <div class="right">
        <?php if (isset($_SESSION['username'])): ?>
            <span class="welcome">Welcome, <?= htmlspecialchars($_SESSION['username']) ?>!</span>
            <a href="logout.php">🚪 Logout</a>
        <?php else: ?>
            <a href="index.html">🔑 Login</a>
        <?php endif; ?>
    </div>
</div>
