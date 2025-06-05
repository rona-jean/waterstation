<?php 
session_start();

if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'user') {
        include 'navbar_user.php';
    } elseif ($_SESSION['role'] === 'admin') {
        include 'navbar_admin.php';
    } elseif ($_SESSION['role'] === 'staff') {
        include 'navbar_staff.php';
    }
} else {
    include 'navbar_guest.php';
}
?>

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
