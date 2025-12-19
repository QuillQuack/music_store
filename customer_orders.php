<?php
include "config.php";
$customerID = $_GET['id'];

// ดึงข้อมูลลูกค้าคนนั้น
$cust = $conn->query("SELECT * FROM Customer WHERE Customer_ID=$customerID")->fetch_assoc();

// ดึงออเดอร์ของลูกค้าคนนี้
$sql = "SELECT * FROM Orders WHERE Customer_ID=$customerID ORDER BY Order_ID DESC";
$orders = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Customer Orders</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body class="p-5">

<h2>Orders of <?= $cust['Name'] ?></h2>

<table class="table table-bordered table-striped mt-4">
    <thead>
        <tr>
            <th>Order ID</th>
            <th>Date</th>
            <th>Total</th>
            <th>Action</th>
        </tr>
    </thead>

    <tbody>
        <?php while($o = $orders->fetch_assoc()) { ?>
        <tr>
            <td><?= $o['Order_ID'] ?></td>
            <td><?= $o['Order_Date'] ?></td>
            <td><?= number_format($o['Total_Amount'],2) ?></td>

            <td>
                <a href="order_items.php?order_id=<?= $o['Order_ID'] ?>" 
                   class="btn btn-success btn-sm">
                   View Items
                </a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<a href="customer_dashboard.php" class="btn btn-secondary mt-3">Back</a>

</body>
</html>
