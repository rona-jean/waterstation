<?php
session_start();
require 'config.php';

// ✅ Flash message for add-to-cart
$flash = '';
if (isset($_SESSION['flash'])) {
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
}

// ✅ Navbar by role
if (isset($_SESSION['role'])) {
    if ($_SESSION['role'] === 'admin') {
        include 'navbar_admin.php';
    } elseif ($_SESSION['role'] === 'staff') {
        include 'navbar_staff.php';
    } elseif ($_SESSION['role'] === 'user') {
        include 'navbar_user.php';
    }
} else {
    include 'navbar_guest.php';
}

// ✅ Fetch all products
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8" />
<title>Shop - Water Refilling Station</title>
<link rel="stylesheet" href="style.css" />
</head>
<body>

<div class="page-container">
  <br>
  <h2>Shop Products</h2>

  <?php if ($flash): ?>
    <div style="padding: 10px; background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; margin-bottom: 15px; border-radius: 5px;">
        <?= $flash ?>
    </div>
  <?php endif; ?>
  
  <div class="product-list" style="display: flex; flex-wrap: wrap; gap: 30px; justify-content: center;">
  <?php while($product = $result->fetch_assoc()): ?>
  <div class="product-card" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; width: 220px; background: #fff;">
    <h3><?= htmlspecialchars($product['name']); ?></h3>
    <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image" style="width: 100%; height: auto;">
    <p style="font-weight: 600;">₱<?= number_format($product['price'], 2); ?></p>
    <p style="font-size: 0.9em; color: #555;"><?= nl2br(htmlspecialchars($product['description'])); ?></p>

    <?php
    // If this is a borrowable product and the user is a guest (not logged in or not role = user)
    if ($product['is_borrowable'] == 1 && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'user')): ?>
      <div style="margin-top: 10px; padding: 10px; background-color: #fff3cd; border: 1px solid #ffeeba; color: #856404; border-radius: 5px;">
        🔒 <strong>This item is available for borrowing by registered users only.</strong><br>
        <a href="register.html" style="color: #007bff;">Register now to borrow this container</a>
      </div>
    <?php else: ?>
      <form method="POST" action="add_to_cart.php" style="margin-top: 10px;">
        <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
        <input type="hidden" name="product" value="<?= htmlspecialchars($product['name']); ?>">
        <input type="hidden" name="price" value="<?= $product['price']; ?>">

        <label>Qty:</label>
        <input type="number" name="quantity" min="1" value="1" style="width: 60px;" />
        <button type="submit" name="add_to_cart" style="margin-left: 10px;">Add to Cart</button>
      </form>
    <?php endif; ?>
  </div>
<?php endwhile; ?>
  </div>
</div>

</body>
</html>
