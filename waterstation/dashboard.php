<?php 
session_start(); // Starts the session so we can access session variables like 'role', 'name', etc.
?>

<!-- Navigation menu -->
<nav>
    <ul>
        <li><a href="shop.php">Shop</a></li>
        <li><a href="cart.php">Cart</a></li>

        <!-- Check if the user is logged in (a session role is set) -->
        <?php if (isset($_SESSION['role'])): ?>

            <!-- If the logged-in user is an admin, show admin links -->
            <?php if ($_SESSION['role'] === 'admin'): ?>
                <li><a href="admin_products.php">Product Manager</a></li>
                <li><a href="admin_users.php">User Management</a></li>
                <li><a href="admin_orders.php">Orders</a></li>

            <!-- If the logged-in user is staff, show staff links -->
            <?php elseif ($_SESSION['role'] === 'staff'): ?>
                <li><a href="staff_dashboard.php">Staff Dashboard</a></li>
                <li><a href="admin_orders.php">Orders</a></li>

            <!-- If the role is something else (like a customer), show a generic account link -->
            <?php else: ?>
                <li><a href="dashboard.php">My Account</a></li>
            <?php endif; ?>

        <!-- If no user is logged in, show login link -->
        <?php else: ?>
            <li><a href="login.php">Login</a></li>
        <?php endif; ?>

        <!-- Show logout link regardless of who is logged in -->
        <li><a href="logout.php">Logout</a></li>
    </ul>
</nav>

<!-- HTML starts here -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"> <!-- Character encoding -->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"> <!-- Makes the page responsive -->
    <title>Dashboard - Water Station</title> <!-- Page title -->
    <link rel="stylesheet" href="style.css"/> <!-- Link to your external CSS file -->
</head>
<body>
    <br>
    <h1><center>Welcome to the dashboard</center></h1> <!-- Main heading -->

    <!-- Centered paragraph to show user identity -->
    <center>
        <p>
            <?php
                // Check again if the user is logged in
                if (isset($_SESSION['role'])) {
                    // Capitalize the first letter of the role for nicer display
                    $role = ucfirst($_SESSION['role']);

                    // Try to get the user's name and ID from the session (if available)
                    $name = isset($_SESSION['name']) ? $_SESSION['name'] : '';
                    $userid = isset($_SESSION['userid']) ? $_SESSION['userid'] : '';

                    // Display role
                    echo "Logged in as: $role";

                    // If the name exists in the session, display it
                    if ($name !== '') {
                        echo " | Name: $name";
                    }
                    // If the name is not available but user ID is, display user ID
                    elseif ($userid !== '') {
                        echo " | User ID: $userid";
                    }
                } else {
                    // If no session is set, user is a guest
                    echo "You are browsing as a Guest";
                }
            ?>
        </p>
    </center>
</body>
</html>
