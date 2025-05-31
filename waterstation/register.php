<?php
session_start();
include 'db.php';
require 'config.php';

$username = $_POST['username'];
$password = password_hash($_POST['password'], PASSWORD_DEFAULT);
$address = $_POST['address'];

$sql = "INSERT INTO users (username, password, address) VALUES ('$username', '$password', '$address')";

if ($conn->query($sql) === TRUE) {
    echo "<script>alert('registration successful, you can now login')</script>";
    header("Location: index.php");
    exit;
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
