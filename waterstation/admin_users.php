<?php
session_start();
include 'db.php';
include('navbar.php');

if (!isset($_SESSION['username']) || $_SESSION['role'] !== 'admin') {
    echo "Access denied.";
    exit();
}

$result = $conn->query("SELECT id, username, address, role FROM users");
?>

<!DOCTYPE html>
<html>
<head>
    <title>User Management</title>
    <link rel="stylesheet" href="style.css">
    <style>
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { padding: 10px; border: 1px solid #ccc; }
        th { background-color:rgb(15, 101, 180); }
    </style>
</head>
<body>
<div class="form-container">
    <h2>👤 Manage Users</h2>
    <table>
        <tr>
            <th>ID</th>
            <th>Username</th>
            <th>Address</th>
            <th>Role</th>
            <th>Actions</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <form action="update_user.php" method="POST">
                <td><?= $row['id'] ?></td>
                <td><?= htmlspecialchars($row['username']) ?></td>
                <td><?= htmlspecialchars($row['address']) ?></td>
                <td>
                    <select name="role">
                        <option value="user" <?= $row['role'] == 'user' ? 'selected' : '' ?>>user</option>
                        <option value="staff" <?= $row['role'] == 'staff' ? 'selected' : '' ?>>staff</option>
                        <option value="admin" <?= $row['role'] == 'admin' ? 'selected' : '' ?>>admin</option>
                    </select>
                </td>
                <td>
                    <input type="hidden" name="user_id" value="<?= $row['id'] ?>">
                    <button type="submit">Update</button>
                    <a href="delete_user.php?id=<?= $row['id'] ?>" onclick="return confirm('Delete this user?')">🗑️ Delete</a>
                </td>
            </form>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
