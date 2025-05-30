<?php
session_start();
require 'config.php';

// Only admin access
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}

$errors = [];
$success = "";

// Handle Add Product with image upload
if (isset($_POST['add_product'])) {
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);

    if (empty($name)) $errors[] = "Product name is required.";
    if (!is_numeric($price) || $price <= 0) $errors[] = "Price must be a positive number.";

    // Handle image upload
    $imageFileName = null;
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['image']['tmp_name'];
        $fileName = $_FILES['image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = './images/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                $imageFileName = $newFileName;
            } else {
                $errors[] = "Error uploading image file.";
            }
        } else {
            $errors[] = "Only JPG, JPEG, PNG, GIF images are allowed.";
        }
    }

    if (!$errors) {
        $stmt = $conn->prepare("INSERT INTO products (name, price, description, image) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("sdss", $name, $price, $description, $imageFileName);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $success = "Product added successfully!";
        }
        $stmt->close();
    }
}

// Handle Delete Product
if (isset($_GET['delete'])) {
    $delete_id = intval($_GET['delete']);

    // Get image to delete
    $stmt = $conn->prepare("SELECT image FROM products WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->bind_result($imgToDelete);
    $stmt->fetch();
    $stmt->close();

    if ($imgToDelete && file_exists("./images/" . $imgToDelete)) {
        unlink("./images/" . $imgToDelete);
    }

    $stmt = $conn->prepare("DELETE FROM products WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    header("Location: admin_products.php");
    exit;
}

// Handle Edit Product (update with image)
if (isset($_POST['edit_product'])) {
    $edit_id = intval($_POST['product_id']);
    $name = trim($_POST['name']);
    $price = trim($_POST['price']);
    $description = trim($_POST['description']);
    $currentImage = $_POST['current_image'];

    if (empty($name)) $errors[] = "Product name is required.";
    if (!is_numeric($price) || $price <= 0) $errors[] = "Price must be a positive number.";

    $imageFileName = $currentImage; // keep old by default

    // Handle new image upload
    if (isset($_FILES['edit_image']) && $_FILES['edit_image']['error'] === UPLOAD_ERR_OK) {
        $fileTmpPath = $_FILES['edit_image']['tmp_name'];
        $fileName = $_FILES['edit_image']['name'];
        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        $allowedfileExtensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (in_array($fileExtension, $allowedfileExtensions)) {
            $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
            $uploadFileDir = './images/';
            if (!is_dir($uploadFileDir)) {
                mkdir($uploadFileDir, 0755, true);
            }
            $dest_path = $uploadFileDir . $newFileName;

            if (move_uploaded_file($fileTmpPath, $dest_path)) {
                // Delete old image
                if ($currentImage && file_exists($uploadFileDir . $currentImage)) {
                    unlink($uploadFileDir . $currentImage);
                }
                $imageFileName = $newFileName;
            } else {
                $errors[] = "Error uploading new image file.";
            }
        } else {
            $errors[] = "Only JPG, JPEG, PNG, GIF images are allowed.";
        }
    }

    if (!$errors) {
        $stmt = $conn->prepare("UPDATE products SET name = ?, price = ?, description = ?, image = ? WHERE id = ?");
        $stmt->bind_param("sdssi", $name, $price, $description, $imageFileName, $edit_id);
        $stmt->execute();
        if ($stmt->affected_rows > 0) {
            $success = "Product updated successfully!";
        }
        $stmt->close();

        // Redirect to clear GET param to avoid resubmission
        header("Location: admin_products.php");
        exit;
    }
}

// If editing, fetch product data to populate edit form
$editProduct = null;
if (isset($_GET['edit'])) {
    $edit_id = intval($_GET['edit']);
    $stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $resultEdit = $stmt->get_result();
    $editProduct = $resultEdit->fetch_assoc();
    $stmt->close();
}

// Fetch products for display
$result = $conn->query("SELECT * FROM products ORDER BY created_at DESC");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin - Manage Products</title>
    <link rel="stylesheet" href="style.css" />
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="page-container">
  <h2>Manage Products</h2>

  <?php if ($errors): ?>
    <div style="color: red;">
      <?php foreach($errors as $error) echo "<p>$error</p>"; ?>
    </div>
  <?php endif; ?>

  <?php if ($success): ?>
    <div style="color: green;">
      <p><?php echo $success; ?></p>
    </div>
  <?php endif; ?>

  <?php if ($editProduct): ?>
  <!-- Edit Product Form -->
  <h3>Edit Product (ID: <?php echo $editProduct['id']; ?>)</h3>
  <form method="POST" action="" enctype="multipart/form-data" style="margin-bottom: 30px;">
    <input type="hidden" name="product_id" value="<?php echo $editProduct['id']; ?>" />
    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($editProduct['image']); ?>" />
    
    <label>Name:</label><br />
    <input type="text" name="name" required value="<?php echo htmlspecialchars($editProduct['name']); ?>" /><br />
    
    <label>Price (₱):</label><br />
    <input type="number" step="0.01" name="price" required value="<?php echo htmlspecialchars($editProduct['price']); ?>" /><br />
    
    <label>Description:</label><br />
    <textarea name="description" rows="3"><?php echo htmlspecialchars($editProduct['description']); ?></textarea><br />

    <label>Current Image:</label><br />
    <?php if ($editProduct['image'] && file_exists('./images/' . $editProduct['image'])): ?>
      <img src="images/<?php echo htmlspecialchars($editProduct['image']); ?>" alt="Current image" style="width: 100px; height: auto; margin-bottom: 10px;" />
    <?php else: ?>
      No image
    <?php endif; ?>
    <br />
    
    <label>Change Image:</label><br />
    <input type="file" name="edit_image" accept="image/*" /><br /><br />
    
    <button type="submit" name="edit_product">Update Product</button>
    <a href="admin_products.php" style="margin-left:10px;">Cancel</a>
  </form>
  <?php endif; ?>

  <!-- Add Product Form -->
  <h3>Add New Product</h3>
  <form method="POST" action="" enctype="multipart/form-data">
    <label>Name:</label><br />
    <input type="text" name="name" required /><br />
    
    <label>Price (₱):</label><br />
    <input type="number" step="0.01" name="price" required /><br />
    
    <label>Description:</label><br />
    <textarea name="description" rows="3"></textarea><br />

    <label>Product Image:</label><br />
    <input type="file" name="image" accept="image/*"><br /><br />
    
    <button type="submit" name="add_product">Add Product</button>
  </form>

  <hr />

  <!-- Products List with Edit/Delete -->
  <h3>Existing Products</h3>

  <table border="1" cellpadding="8" cellspacing="0" width="100%">
    <thead>
      <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Price (₱)</th>
        <th>Description</th>
        <th>Image</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
    <?php while($row = $result->fetch_assoc()): ?>
      <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo htmlspecialchars($row['name']); ?></td>
        <td><?php echo number_format($row['price'], 2); ?></td>
        <td><?php echo htmlspecialchars($row['description']); ?></td>
        <td style="text-align:center;">
          <?php if ($row['image'] && file_exists('./images/' . $row['image'])): ?>
            <img src="images/<?php echo htmlspecialchars($row['image']); ?>" alt="Product Image" style="width: 70px; height: auto;" />
          <?php else: ?>
            No image
          <?php endif; ?>
        </td>
        <td>
          <a href="admin_products.php?edit=<?php echo $row['id']; ?>">Edit</a> | 
          <a href="admin_products.php?delete=<?php echo $row['id']; ?>" onclick="return confirm('Delete this product?');" style="color: red;">Delete</a>
        </td>
      </tr>
    <?php endwhile; ?>
    </tbody>
  </table>
</div>

</body>
</html>
