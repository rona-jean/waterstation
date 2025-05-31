<?php
session_start();
require 'config.php';

// Fetch all products from DB
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
<?php include 'navbar.php'; ?>

<div class="page-container">
  <h2>Shop Products</h2>

  <div class="product-list" style="display: flex; flex-wrap: wrap; gap: 30px;">
  <?php while($product = $result->fetch_assoc()): ?>
    <div class="product-card" style="border: 1px solid #ddd; border-radius: 8px; padding: 15px; width: 220px; background: #fff;">
      <h3><?php echo htmlspecialchars($product['name']); ?></h3>
      <p style="font-weight: 600;">₱<?php echo number_format($product['price'], 2); ?></p>
      <p style="font-size: 0.9em; color: #555;"><?php echo nl2br(htmlspecialchars($product['description'])); ?></p>

    <form method="POST" action="add_to_cart.php" style="margin-top: 10px;">
    <img src="images/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="product-image">

    <!-- ✅ Pass necessary info -->
    <input type="hidden" name="product_id" value="<?= $product['id']; ?>">
    <input type="hidden" name="product" value="<?= htmlspecialchars($product['name']); ?>">
    <input type="hidden" name="price" value="<?= $product['price']; ?>">

    <label>Quantity:</label>
    <input type="number" name="quantity" min="1" value="1" style="width: 60px;" />

    <button type="submit" name="add_to_cart" style="margin-left: 10px;">Add to Cart</button>
</form>


    </div>
  <?php endwhile; ?>
  </div>
</div>

</body>
</html>
