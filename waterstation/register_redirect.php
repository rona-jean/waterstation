<?php
session_start();
require 'config.php';

// Get data from form
$username = trim($_POST['username']);
$email = trim($_POST['email']);
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$address = trim($_POST['address']);

// Check if email already exists
$stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
$stmt->bind_param("s", $email);
$stmt->execute();
$stmt->store_result();

if ($stmt->num_rows > 0) {
    echo "<script>alert('Email already exists. Please use a different one.'); window.location.href='register.html';</script>";
    exit;
}
$stmt->close();

// Insert new user
$stmt = $conn->prepare("INSERT INTO users (username, email, password, address) VALUES (?, ?, ?, ?)");
$stmt->bind_param("ssss", $username, $email, $password, $address);

if ($stmt->execute()) {
    echo "<script>alert('Registration successful, you can now login.'); window.location.href='index.html';</script>";
} else {
    echo "<script>alert('Registration failed. Please try again.'); window.location.href='register.html';</script>";
}

$stmt->close();
$conn->close();
?>
