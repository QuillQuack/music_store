<?php
session_start();
require_once "config.php";

// Check if user is logged in
if (!isset($_SESSION['customer_id'])) {
    header("Location: customer_auth.php");
    exit;
}

// Initialize cart
if (!isset($_SESSION["cart"])) {
    $_SESSION["cart"] = [];
}

// Handle Add to Cart
if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["add_to_cart"])) {
    $type = $_POST["type"];
    $item_id = intval($_POST["item_id"]);
    $quantity = max(1, intval($_POST["quantity"]));

    // Fetch item data using prepared statement (FIXED: SQL injection)
    if ($type === "song") {
        $stmt = $conn->prepare("SELECT Song_ID, Song_Name AS Title, Song_Price AS Price FROM Song WHERE Song_ID = ?");
        $stmt->bind_param("i", $item_id);
    } else {
        $stmt = $conn->prepare("SELECT Album_ID, Album_Name AS Title, Album_Price AS Price FROM Album WHERE Album_ID = ?");
        $stmt->bind_param("i", $item_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if (!$data) {
        $_SESSION["error_message"] = "Invalid item selected";
        header("Location: music_store.php");
        exit;
    }

    $unit_price = floatval($data["Price"]);
    $total = $unit_price * $quantity;

    $_SESSION["cart"][] = [
        "item_type"  => $type,
        "item_id"    => $item_id,
        "item_name"  => $data["Title"],
        "quantity"   => $quantity,
        "unit_price" => $unit_price,
        "total"      => $total
    ];

    // FIXED: Redirect to correct filename
}

// Get Albums
$album_sql = "
SELECT 
    a.Album_ID,
    a.Album_Name,
    a.Album_Price,
    a.Release_Date,
    ar.Name AS Artist_Name
FROM Album a
LEFT JOIN Artist ar ON a.Artist_ID = ar.Artist_ID
ORDER BY a.Album_Name ASC
";
$albums = $conn->query($album_sql);

// Get Songs
$songs_sql = "
SELECT 
    s.Song_ID,
    s.Song_Name,
    s.Duration,
    s.Song_Price,
    a.Album_Name,
    ar.Name AS Artist_Name
FROM Song s
LEFT JOIN Album a ON s.Album_ID = a.Album_ID
LEFT JOIN Artist ar ON a.Artist_ID = ar.Artist_ID
ORDER BY s.Song_Name ASC
";
$songs = $conn->query($songs_sql);
?>
<!DOCTYPE html>
<html>
<head>
    <title>Music Store</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width,initial-scale=1" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="container mt-4">
    <h2 class="fw-bold">🎵 Music Store</h2>
    <a href="customer_dashboard.php" class="btn btn-secondary btn-sm mb-3">← Back</a>

    <?php if (!empty($_SESSION["success_message"])): ?>
    <div class="alert alert-success">
        <?= htmlspecialchars($_SESSION["success_message"]) ?>
        <?php unset($_SESSION["success_message"]); ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($_SESSION["error_message"])): ?>
    <div class="alert alert-danger">
        <?= htmlspecialchars($_SESSION["error_message"]) ?>
        <?php unset($_SESSION["error_message"]); ?>
    </div>
    <?php endif; ?>

    <ul class="nav nav-tabs mb-4" id="tabs">
        <li class="nav-item"><button class="nav-link active" data-bs-toggle="tab" data-bs-target="#albums">Albums</button></li>
        <li class="nav-item"><button class="nav-link" data-bs-toggle="tab" data-bs-target="#songs">Songs</button></li>
    </ul>

    <div class="tab-content">
        <!-- ALBUMS TAB -->
        <div class="tab-pane fade show active" id="albums">
            <div class="row g-3">
                <?php if ($albums && $albums->num_rows > 0): ?>
                    <?php while ($a = $albums->fetch_assoc()): ?>
                        <div class="col-md-4">
                            <div class="album-card">
                                <div>
                                    <h5 class="fw-bold"><?= htmlspecialchars($a["Album_Name"] ?? 'Untitled') ?></h5>
                                    <p class="text-muted mb-1">Artist: <?= htmlspecialchars($a["Artist_Name"] ?? 'Unknown') ?></p>
                                    <p class="text-muted mb-1">Release: <?= htmlspecialchars($a["Release_Date"] ?? 'N/A') ?></p>
                                </div>
                                <div class="mt-auto">
                                    <p class="price-tag">$<?= number_format(floatval($a["Album_Price"] ?? 0), 2) ?></p>
                                    <form method="POST" class="d-flex gap-2 align-items-center">
                                        <input type="hidden" name="add_to_cart" value="1">
                                        <input type="hidden" name="type" value="album">
                                        <input type="hidden" name="item_id" value="<?= intval($a["Album_ID"]) ?>">
                                        <input type="number" name="quantity" value="1" min="1" class="form-control form-control-sm" style="width: 80px;">
                                        <button class="btn btn-primary btn-sm flex-grow-1">🛒 Add</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info">No albums available.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- SONGS TAB -->
        <div class="tab-pane fade" id="songs">
            <div class="row g-3">
                <?php if ($songs && $songs->num_rows > 0): ?>
                    <?php while ($s = $songs->fetch_assoc()): ?>
                        <div class="col-md-4">
                            <div class="song-card">
                                <div>
                                    <h5 class="fw-bold"><?= htmlspecialchars($s["Song_Name"] ?? 'Untitled') ?></h5>
                                    <p class="text-muted mb-1">Artist: <?= htmlspecialchars($s["Artist_Name"] ?? 'Unknown') ?></p>
                                    <p class="text-muted mb-1">Album: <?= htmlspecialchars($s["Album_Name"] ?? '—') ?></p>
                                    <p class="text-muted mb-1">Duration: <?= htmlspecialchars($s["Duration"] ?? 'N/A') ?></p>
                                </div>
                                <div class="mt-auto">
                                    <p class="price-tag">$<?= number_format(floatval($s["Song_Price"] ?? 0), 2) ?></p>
                                    <form method="POST" class="d-flex gap-2 align-items-center">
                                        <input type="hidden" name="add_to_cart" value="1">
                                        <input type="hidden" name="type" value="song">
                                        <input type="hidden" name="item_id" value="<?= intval($s["Song_ID"]) ?>">
                                        <input type="number" name="quantity" value="1" min="1" class="form-control form-control-sm" style="width: 80px;">
                                        <button class="btn btn-success btn-sm flex-grow-1">🛒 Add</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; ?>
                <?php else: ?>
                    <div class="col-12">
                        <div class="alert alert-info">No songs available.</div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Floating Cart Button -->
    <div id="floating-cart">
        <a href="cart_view.php" class="btn btn-warning btn-lg shadow">
            🛒 Cart (<?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?>)
        </a>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>