<?php
session_start();
include "config.php";

if (!isset($_SESSION["artist_id"])) {
    header("Location: artist_login.php");
    exit();
}

if (!isset($_GET["album_id"])) {
    echo "Missing album_id";
    exit();
}

$album_id = intval($_GET["album_id"]);

// Correct: load genre list
$genres = $conn->query("SELECT Genre_ID, Name FROM Genre ORDER BY Name ASC");

if (isset($_POST["create"])) {
    $name     = $_POST["song_name"];
    $duration = $_POST["duration"];
    $lyrics   = $_POST["lyrics"];
    $genre_id = $_POST["genre_id"];

    $stmt = $conn->prepare("
        INSERT INTO Song (Album_ID, Song_Name, Duration, Lyrics, Genre_ID)
        VALUES (?, ?, ?, ?, ?)
    ");
    $stmt->bind_param("isssi", $album_id, $name, $duration, $lyrics, $genre_id);
    $stmt->execute();

    header("Location: artist_dashboard.php?view_songs=".$album_id);
    exit();
}
?>
<!DOCTYPE html>
<html>
<head>
<title>Create Song</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="style.css">
</head>

<body>
<div class="card">
<h2>+ Add Song</h2>

<form method="POST">

    <label>Song Name</label>
    <input type="text" name="song_name" required>

    <label>Duration (ex. 03:45)</label>
    <input type="text" name="duration" required>

    <label>Lyrics</label>
    <textarea name="lyrics" rows="6" required></textarea>

    <label>Genre</label>
    <select name="genre_id" required>
        <option value="">-- Select Genre --</option>
        <?php while ($g = $genres->fetch_assoc()) { ?>
            <option value="<?= $g['Genre_ID'] ?>"><?= htmlspecialchars($g['Name']) ?></option>
        <?php } ?>
    </select>

    <button name="create">Create Song</button>
    <button type="button" onclick="history.back()">Back</button>
</form>

</div>
</body>
</html>
