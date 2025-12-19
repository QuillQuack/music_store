<?php
session_start();
include 'config.php';

if (!isset($_SESSION["artist_id"])) {
    header("Location: artist_login.php");
    exit();
}

$artist_id = $_SESSION["artist_id"];

// Fetch all genres for dropdown
$genres = $conn->query("SELECT Genre_ID, Name FROM Genre ORDER BY Name ASC");

if (isset($_POST["create"])) {
    $name     = $_POST["album_name"];
    $desc     = $_POST["description"];
    $release  = $_POST["release_date"];
    $price    = $_POST["album_price"];
    $genre_id = $_POST["genre_id"]; // new field

    $stmt = $conn->prepare("
    INSERT INTO Album (Artist_ID, Album_Name, Description, Album_Price, Release_Date, Genre_ID)
    VALUES (?, ?, ?, ?, ?, ?)
	");
	$stmt->bind_param("issdsi", $artist_id, $name, $desc, $price, $release, $genre_id);

    $stmt->execute();

    header("Location: artist_dashboard.php");
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Create Album</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="style.css">
</head>

<body>
<div class="card">
<h2>+ Create New Album</h2>

<form method="POST">
    <label>Album Name</label>
    <input type="text" name="album_name" required>

    <label>Description</label>
    <textarea name="description" required></textarea>  

    <label>Release Date</label>
    <input type="date" name="release_date" required>

    <label>Album Price</label>
    <input type="number" step="0.01" name="album_price" required>

    <label>Genre</label>
    <select name="genre_id" required>
        <option value="">-- Select Genre --</option>
        <?php while ($g = $genres->fetch_assoc()) { ?>
            <option value="<?= $g['Genre_ID'] ?>"><?= htmlspecialchars($g['Name']) ?></option>
        <?php } ?>
    </select>

    <button name="create">Create Album</button>
    <button type="button" onclick="history.back()">Back</button> 
</form>

</div>
</body>
</html>