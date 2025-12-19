<?php
session_start();
include "config.php";

if (!isset($_SESSION['artist_id'])) {
    die("Missing artist_id — กรุณา login ก่อน");
}

$id = $_GET['id'];

$album = $conn->query("SELECT * FROM Album WHERE Album_ID=$id")->fetch_assoc();

if (isset($_POST['update'])) {

    $name = $_POST['name'];
    $desc = $_POST['description'];
    $price = $_POST['price'];

    $stmt = $conn->prepare("
        UPDATE Album
        SET Album_Name=?, Description=?, Album_Price=?
        WHERE Album_ID=?
    ");
    $stmt->bind_param("ssdi", $name, $desc, $price, $id);
    $stmt->execute();

    header("Location: artist_dashboard.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Album</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body class="p-4">

<h2>Edit Album</h2>

<form method="POST">
    <div class="mb-3">
        <label>Album Name</label>
        <input name="name" class="form-control" value="<?= $album['Album_Name'] ?>">
    </div>

    <div class="mb-3">
        <label>Description</label>
        <textarea name="description" class="form-control"><?= $album['Description'] ?></textarea>
    </div>

    <div class="mb-3">
        <label>Price</label>
        <input type="number" step="0.01" name="price" class="form-control" value="<?= $album['Album_Price'] ?>">
    </div>

    <button name="update" class="btn btn-primary">Save</button>
    <a href="artist_dashboard.php" class="btn btn-secondary">Cancel</a>
</form>

</body>
</html>
