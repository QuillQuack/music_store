<?php
session_start();
require_once "config.php";

// -------------------------------------------
// 1) ตรวจว่าลูกค้าเข้าสู่ระบบหรือยัง
// -------------------------------------------
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_auth.php");
    exit;
}

$customer_id = $_SESSION['customer_id'];

// -------------------------------------------
// 2) ดึงข้อมูลลูกค้า
// -------------------------------------------
$stmt = $conn->prepare("SELECT Customer_ID, Name, Email, Country FROM Customer WHERE Customer_ID = ?");
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();

// -------------------------------------------
// 3) ดึงข้อมูล Orders + Order_Item + Prices
//    เฉพาะ order ที่มี item อยู่เท่านั้น
// -------------------------------------------
$order_sql = "
SELECT 
    o.Order_ID, 
    o.Order_Date,
    oi.Order_Item_ID,
    oi.Quantity,
    oi.Album_ID,
    oi.Song_ID,
    a.Album_Name,
    a.Album_Price,
    s.Song_Name,
    s.Song_Price
FROM Orders o
INNER JOIN Order_Item oi ON o.Order_ID = oi.Order_ID
LEFT JOIN Album a ON oi.Album_ID = a.Album_ID
LEFT JOIN Song s ON oi.Song_ID = s.Song_ID
WHERE o.Customer_ID = ?
ORDER BY o.Order_Date DESC
";

$stmt2 = $conn->prepare($order_sql);
$stmt2->bind_param("i", $customer_id);
$stmt2->execute();
$order_items = $stmt2->get_result();

// Calculate total
$total_price = 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Customer Profile</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>



<!-- Back Button (Top Left) -->
<a href="select_role.php" class="back-btn">⬅ Back</a>

<!-- Music Store Button (Top Right) -->
<div style="position: fixed; top: 20px; right: 20px; z-index: 1000;">
    <a href="music_store.php" class="btn btn-primary shadow">
        🎵 Music Store
    </a>
</div>

<div class="container py-5">

    <!-- TITLE -->
    <h2 class="mb-4 text-center fw-bold">Customer Profile</h2>

    <!-- CUSTOMER INFORMATION CARD -->
    <div class="card shadow-sm mb-5">
        <div class="card-body">
            <h4 class="mb-3">Your Profile</h4>
            <p><strong>Name:</strong> <?= htmlspecialchars($customer['Name']) ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($customer['Email']) ?></p>
            <p><strong>Country:</strong> <?= htmlspecialchars($customer['Country']) ?></p>
        </div>
    </div>

    <!-- ORDER HISTORY -->
    <h4 class="fw-bold mb-3">Your Orders</h4>

    <?php if ($order_items->num_rows === 0): ?>
        <div class="alert alert-warning">No orders found.</div>
    <?php else: ?>

    <div class="card"> 
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Product</th>
                    <th>Unit Price</th>
                    <th>Quantity</th>
                    <th>Subtotal</th>
                </tr>
            </thead>
            <tbody>

            <?php while ($row = $order_items->fetch_assoc()): 
                // Determine price and product name
                $unit_price = 0;
                $product_name = "";
                
                if ($row['Album_ID']) {
                    $unit_price = $row['Album_Price'];
                    $product_name = "Album: " . $row['Album_Name'];
                }
                if ($row['Song_ID']) {
                    $unit_price = $row['Song_Price'];
                    $product_name = "Song: " . $row['Song_Name'];
                }
                
                $subtotal = $unit_price * $row['Quantity'];
                $total_price += $subtotal;
            ?>
                <tr>
                    <td><?= $row['Order_ID'] ?></td>
                    <td><?= $row['Order_Date'] ?></td>
                    <td><?= $product_name ?></td>
                    <td>$<?= number_format($unit_price, 2) ?></td>
                    <td><?= $row['Quantity'] ?></td>
                    <td>$<?= number_format($subtotal, 2) ?></td>
                </tr>
            <?php endwhile; ?>

            </tbody>
            <tfoot>
                <tr>
                    <td colspan="5" class="text-end fw-bold" style="color: var(--success);">Total:</td>
                    <td class="fw-bold" style="color: var(--success);">$<?= number_format($total_price, 2) ?></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <?php endif; ?>

</div>

</body>
</html>