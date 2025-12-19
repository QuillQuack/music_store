<?php
include "config.php";

$orderID = $_GET['order_id'];

$sql = "
SELECT 
    oi.Order_ID,
    oi.Product_ID,
    oi.Quantity,
    oi.Price,
    (oi.Quantity * oi.Price) AS Item_Total
FROM Order_Item oi
WHERE oi.Order_ID = $orderID
";

$items = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Order Items</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body class="p-5">

<h2>Items in Order #<?= $orderID ?></h2>

<table class="table table-bordered table-striped mt-4">
    <thead>
        <tr>
            <th>Product ID</th>
            <th>Qty</th>
            <th>Price</th>
            <th>Total</th>
        </tr>
    </thead>

    <tbody>
        <?php while($it = $items->fetch_assoc()) { ?>
        <tr>
            <td><?= $it['Product_ID'] ?></td>
            <td><?= $it['Quantity'] ?></td>
            <td><?= number_format($it['Price'],2) ?></td>
            <td><?= number_format($it['Item_Total'],2) ?></td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<a href="javascript:history.back()" class="btn btn-secondary mt-3">Back</a>

</body>
</html>
