<?php
session_start();
include "config.php";

if (!isset($_SESSION["artist_id"])) {
    header("Location: artist_login.php");
    exit();
}

if (!isset($_GET["id"])) {
    echo "Missing Song_ID";
    exit();
}

$song_id = intval($_GET["id"]);

$stmt = $conn->prepare("SELECT * FROM Song WHERE Song_ID = ?");
$stmt->bind_param("i", $song_id);
$stmt->execute();
$song = $stmt->get_result()->fetch_assoc();

if (!$song) {
    echo "Song not found!";
    exit();
}

$album_id = $song["Album_ID"];

if (isset($_POST["update"])) {
    $name = $_POST["song_name"];
    $duration = $_POST["duration"];
    $lyrics = $_POST["lyrics"];

    $stmt = $conn->prepare("
        UPDATE Song 
        SET Song_Name=?, Duration=?, Lyrics=?
        WHERE Song_ID=?
    ");
    $stmt->bind_param("sssi", $name, $duration, $lyrics, $song_id);
    $stmt->execute();

    header("Location: artist_dashboard.php?view_songs=".$album_id);
    exit();
}
?>

<!DOCTYPE html>
<html>
<head>
<title>Edit Song</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="style.css">
</style>
</head>

<body>
<div class="card">
<h2>Edit Song</h2>

<form method="POST">

    <label>Song Name</label>
    <input type="text" name="song_name" value="<?= $song['Song_Name'] ?>" required>

    <label>Duration</label>
    <input type="text" name="duration" value="<?= $song['Duration'] ?>" required>

    <label>Lyrics</label>
    <textarea name="lyrics" rows="6"><?= $song['Lyrics'] ?></textarea>

    <button name="update">Save Changes</button>
    <button type="button" onclick="history.back()">Cancel</button>

</form>

</div>
</body>
</html>
