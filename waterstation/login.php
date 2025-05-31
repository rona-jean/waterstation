<?php
session_start();
include 'db.php';
require 'config.php';

$username = $_POST['username'];
$password = $_POST['password'];

// Protect against SQL injection
$username = $conn->real_escape_string($username);

$sql = "SELECT * FROM users WHERE username='$username'";
$result = $conn->query($sql);

if ($result->num_rows == 1) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['username'] = $username;
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role']; // ✅ Save role
        header("Location: dashboard.php");
        exit();
    } else {
        echo "<script>alert('Invalid password.'); window.location.href='login.html';</script>";
    }
} else {
    echo "<script>alert('User not found. Please register first.'); window.location.href='register.html';</script>";
}

$conn->close();
?>
