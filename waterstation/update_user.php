<?php
include 'db.php';

$user_id = $_POST['user_id'];
$role = $_POST['role'];

$stmt = $conn->prepare("UPDATE users SET role = ? WHERE id = ?");
$stmt->bind_param("si", $role, $user_id);
$stmt->execute();

header("Location: admin_users.php");
exit();
?>
