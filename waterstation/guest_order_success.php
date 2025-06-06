<?php
session_start();
$tracking_code = $_GET['tracking_code'] ?? $_SESSION['guest_tracking_code'] ?? '';
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Successful</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<?php include 'navbar_guest.php'; ?>

<div class="form-container">
    <h2>🎉 Order Placed Successfully!</h2>
    <p>Thank you for your order.</p>
    <p><strong>Your Tracking Code:</strong> <?= htmlspecialchars($tracking_code) ?></p>
    <p>Please save this tracking code to view your order status later.</p>
    <p><a href="track_order.php">Track My Order</a></p>
</div>
</body>
</html>
