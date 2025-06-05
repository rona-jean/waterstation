<?php
session_start();
require 'config.php';

// Allow only admin and staff
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], ['admin', 'staff'])) {
    header("Location: login.php");
    exit;
}

// Handle return update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['borrow_id'])) {
    $borrow_id = intval($_POST['borrow_id']);
    $stmt = $conn->prepare("UPDATE borrowed_containers SET returned = 1, returned_at = NOW() WHERE id = ?");
    $stmt->bind_param("i", $borrow_id);
    $stmt->execute();
    $stmt->close();
}

// ✅ Fetch borrowed container records with returned status
$query = "SELECT b.id, u.username AS user_name, p.name AS product, b.created_at AS borrowed_at, b.returned, b.returned_at
    FROM borrowed_containers b
    JOIN users u ON b.user_id = u.id
    LEFT JOIN products p ON b.product_id = p.id
    ORDER BY b.created_at DESC
";


$result = $conn->query($query);
if (!$result) {
    echo "Query Error: " . $conn->error;
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Manage Borrowed Containers</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="page-container">
    <h2>Borrowed Containers Management</h2>
    <table border="1" cellpadding="8" cellspacing="0">
        <tr>
            <th>User</th>
            <th>Product</th>
            <th>Borrowed At</th>
            <th>Status</th>
            <th>Action</th>
        </tr>

        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?= htmlspecialchars($row['user_name']) ?></td>
            <td><?= htmlspecialchars($row['product']) ?></td>
            <td><?= $row['borrowed_at'] ?></td>
            <td>
                <?= $row['returned'] ? "✅ Returned at " . $row['returned_at'] : "❌ Not Returned" ?>
            </td>
            <td>
                <?php if (!$row['returned']): ?>
                    <form method="POST">
                        <input type="hidden" name="borrow_id" value="<?= $row['id'] ?>">
                        <button type="submit">Mark as Returned</button>
                    </form>
                <?php else: ?>
                    —
                <?php endif; ?>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</div>
</body>
</html>
