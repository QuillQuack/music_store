<?php
require_once "config.php";

$id = $_GET['id'];
$action = $_GET['action'];

// ดึงค่าปัจจุบัน
$stmt = $conn->prepare("SELECT Quantity FROM Order_Item WHERE Order_Item_ID = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$current = $stmt->get_result()->fetch_assoc();

$qty = $current['Quantity'];

if ($action === "plus") {
    $qty++;
    $update = $conn->prepare("UPDATE Order_Item SET Quantity = ? WHERE Order_Item_ID = ?");
    $update->bind_param("ii", $qty, $id);
    $update->execute();
}

if ($action === "minus") {
    if ($qty > 1) {
        // ลดจำนวน
        $qty--;
        $update = $conn->prepare("UPDATE Order_Item SET Quantity = ? WHERE Order_Item_ID = ?");
        $update->bind_param("ii", $qty, $id);
        $update->execute();
    } else {
        // ถ้า quantity = 1 แล้วกด minus จะลบ order item ทิ้ง
        $delete = $conn->prepare("DELETE FROM Order_Item WHERE Order_Item_ID = ?");
        $delete->bind_param("i", $id);
        $delete->execute();
    }
}

header("Location: customer_dashboard.php");
exit;
?>