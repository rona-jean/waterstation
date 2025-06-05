<?php
include 'db.php';

// Update return status if submitted
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_status'])) {
    $borrow_id = $_POST['borrow_id'];
    $status = $_POST['status'] == "returned" ? "returned" : "borrowed";
    $returned_at = $status === "returned" ? date("Y-m-d H:i:s") : NULL;

    $sql = "UPDATE borrowed_containers SET status = ?, returned_at = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssi", $status, $returned_at, $borrow_id);
    $stmt->execute();
}

// Fetch all borrowed containers
$sql = "SELECT bc.id, bc.status, bc.borrowed_at, bc.returned_at, u.username, p.name AS product_name
        FROM borrowed_containers bc
        JOIN users u ON bc.user_id = u.id
        JOIN products p ON bc.product_id = p.id
        ORDER BY bc.borrowed_at DESC";

$result = $conn->query($sql);
?>

<h2>Borrowed Containers Management</h2>
<table border="1" cellpadding="5">
    <tr>
        <th>Username</th>
        <th>Product</th>
        <th>Status</th>
        <th>Borrowed At</th>
        <th>Returned At</th>
        <th>Action</th>
    </tr>

<?php while ($row = $result->fetch_assoc()): ?>
<tr>
    <td><?= htmlspecialchars($row['username']) ?></td>
    <td><?= htmlspecialchars($row['product_name']) ?></td>
    <td><?= htmlspecialchars($row['status']) ?></td>
    <td><?= htmlspecialchars($row['borrowed_at']) ?></td>
    <td><?= htmlspecialchars($row['returned_at']) ?></td>
    <td>
        <form method="post">
            <input type="hidden" name="borrow_id" value="<?= $row['id'] ?>">
            <select name="status">
                <option value="borrowed" <?= $row['status'] === "borrowed" ? "selected" : "" ?>>Borrowed</option>
                <option value="returned" <?= $row['status'] === "returned" ? "selected" : "" ?>>Returned</option>
            </select>
            <input type="submit" name="update_status" value="Update">
        </form>
    </td>
</tr>
<?php endwhile; ?>
</table>
