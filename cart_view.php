<?php
// Start output buffering to prevent header issues
ob_start();

ini_set('session.save_path', '/tmp');
session_start();
require_once "config.php";

// Check authentication
if (!isset($_SESSION["customer_id"])) {
    header("Location: customer_auth.php");
    exit;
}

// Initialize cart
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

// REMOVE ITEM from cart
if (isset($_GET["remove"])) {
    $index = intval($_GET["remove"]);
    if (isset($_SESSION["cart"][$index])) {
        unset($_SESSION["cart"][$index]);
        $_SESSION["cart"] = array_values($_SESSION["cart"]); // Re-index array
    }
    header("Location: cart_view.php");
    exit;
}

// CLEAR entire cart
if (isset($_GET["clear"])) {
    $_SESSION["cart"] = [];
    header("Location: cart_view.php");
    exit;
}

// CONFIRM ORDER
if (isset($_POST["confirm"])) {
    if (!empty($_SESSION["cart"])) {

        // Start transaction
        $conn->begin_transaction();

        try {
            // Create order
            $stmt = $conn->prepare("INSERT INTO Orders (Customer_ID, Order_Date, Total_Paid_Amount, Status)
                                    VALUES (?, NOW(), 0, 'Processing')");
            $stmt->bind_param("i", $_SESSION["customer_id"]);
            $stmt->execute();
            $order_id = $conn->insert_id;

            $grand_total = 0;

            // Add order items
            foreach ($_SESSION["cart"] as $c) {
                $item_type = $c["item_type"] ?? 'song';
                $item_id   = intval($c["item_id"] ?? 0);
                $quantity  = intval($c["quantity"] ?? 1);
                $unit_price = floatval($c["unit_price"] ?? 0);
                $line_total = $unit_price * $quantity;

                $albumID = ($item_type === "album") ? $item_id : NULL;
                $songID  = ($item_type === "song") ? $item_id : NULL;

                // Insert into Order_Item
                $sql = $conn->prepare("
                    INSERT INTO Order_Item (Order_ID, Album_ID, Song_ID, Item_Type, Quantity, Unit_Price)
                    VALUES (?, ?, ?, ?, ?, ?)
                ");

                $sql->bind_param("iiisid",
                    $order_id, $albumID, $songID, $item_type, $quantity, $unit_price
                );
                $sql->execute();

                $grand_total += $line_total;

                $artist_id = 0;

                if ($item_type === "album") {
                    // For Albums: Direct lookup
                    $q = $conn->prepare("SELECT Artist_ID FROM Album WHERE Album_ID = ?");
                    $q->bind_param("i", $item_id);
                    $q->execute();
                    $res = $q->get_result()->fetch_assoc();
                    if ($res) $artist_id = $res['Artist_ID'];
                } else {
                    // For Songs: Join to find Album's Artist
                    $q = $conn->prepare("
                        SELECT a.Artist_ID 
                        FROM Song s
                        JOIN Album a ON s.Album_ID = a.Album_ID
                        WHERE s.Song_ID = ?
                    ");
                    $q->bind_param("i", $item_id);
                    $q->execute();
                    $res = $q->get_result()->fetch_assoc();
                    if ($res) $artist_id = $res['Artist_ID'];
                }

                if ($artist_id > 0) {
                    // Update the Artist's balance
                    $update_bal = $conn->prepare("UPDATE Artist SET Account_Balance = Account_Balance + ? WHERE Artist_ID = ?");
                    $update_bal->bind_param("di", $line_total, $artist_id);
                    $update_bal->execute();
                }
                // ---------------------------------------------------------
            }

            // Update final order total
            $up = $conn->prepare("UPDATE Orders SET Total_Paid_Amount=? WHERE Order_ID=?");
            $up->bind_param("di", $grand_total, $order_id);
            $up->execute();

            // Commit transaction
            $conn->commit();

            // Clear cart and redirect
            $_SESSION["cart"] = [];
            $_SESSION["success_message"] = "Order #$order_id confirmed! Money sent to artists.";
            header("Location: customer_dashboard.php");
            exit;

        } catch (Exception $e) {
            // Rollback on error
            $conn->rollback();
            $_SESSION["error_message"] = "Order failed: " . $e->getMessage();
            header("Location: cart_view.php");
            exit;
        }
    }
}

// Calculate cart total
$cart_total = 0;
if (!empty($_SESSION["cart"])) {
    foreach ($_SESSION["cart"] as $item) {
        $cart_total += floatval($item["total"] ?? 0);
    }
}

// Clean output buffer
ob_end_flush();
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Your Cart</title>
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container mt-4">
    <h2 class="fw-bold mb-4">🛒 Your Cart</h2>

    <?php if (!empty($_SESSION["error_message"])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($_SESSION["error_message"]) ?>
        <?php unset($_SESSION["error_message"]); ?>
    </div>
    <?php endif; ?>

    <?php if (empty($_SESSION["cart"])): ?>
        <div class="alert alert-info">Your cart is empty. Start shopping!</div>
        <a href="music_store.php" class="btn btn-primary">← Go to Music Store</a>
    <?php else: ?>

    <a href="music_store.php" class="btn btn-secondary btn-sm mb-3">← Continue Shopping</a>

    <div class="card">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Item Name</th>
                    <th>Quantity</th>
                    <th>Unit Price</th>
                    <th>Total</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($_SESSION["cart"] as $index => $c): ?>
                <?php
                    $item_type = $c["item_type"] ?? 'song';
                    $item_name = $c["item_name"] ?? 'Unknown Item';
                    $quantity = intval($c["quantity"] ?? 1);
                    $unit_price = floatval($c["unit_price"] ?? 0);
                    $total = floatval($c["total"] ?? 0);
                ?>
                <tr>
                    <td>
                        <span class="badge bg-<?= $item_type === 'album' ? 'primary' : 'success' ?>">
                            <?= ucfirst(htmlspecialchars($item_type)) ?>
                        </span>
                    </td>
                    <td><?= htmlspecialchars($item_name) ?></td>
                    <td><?= $quantity ?></td>
                    <td>$<?= number_format($unit_price, 2) ?></td>
                    <td>$<?= number_format($total, 2) ?></td>
                    <td>
                        <a href="cart_view.php?remove=<?= $index ?>"
                           class="btn  btn-rm"
                           onclick="return confirm('Remove this item?')">Remove</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="4" class="text-end fw-bold" style="color: var(--success);">Grand Total:</td>
                    <td colspan="2" class="fw-bold fs-5" style="color: var(--success);">
                        $<?= number_format($cart_total, 2) ?>
                    </td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="d-flex gap-2 mt-4 justify-content-end">
        <a href="cart_view.php?clear=1"
           class="btn btn-warning"
           onclick="return confirm('Clear entire cart?')">Clear Cart</a>

        <form method="POST">
            <button name="confirm"
                    class="btn btn-success btn-lg"
                    onclick="return confirm('Confirm this order?')">
                ✓ Confirm Order
            </button>
        </form>
    </div>

    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>