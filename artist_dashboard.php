<?php
session_start();
include 'config.php';

if (!isset($_SESSION['artist_id'])) {
    die("Missing artist_id — กรุณาเลือก role ก่อน");
}

$artist_id = $_SESSION['artist_id'];

// ----------------------------------------------------------------------
// 1) Artist Info
// ----------------------------------------------------------------------
$stmt = $conn->prepare("SELECT Name, Email, Account_Balance FROM Artist WHERE Artist_ID = ?");
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$artist = $stmt->get_result()->fetch_assoc();

// ----------------------------------------------------------------------
// 2) Fetch Albums (with Genre)
// ----------------------------------------------------------------------
$stmt = $conn->prepare("
    SELECT a.Album_ID, a.Album_Name, a.Description, a.Release_Date, g.Name AS Genre_Name
    FROM Album a
    LEFT JOIN Genre g ON a.Genre_ID = g.Genre_ID
    WHERE a.Artist_ID = ?
");
$stmt->bind_param("i", $artist_id);
$stmt->execute();
$albums = $stmt->get_result();

// ----------------------------------------------------------------------
// 3) Fetch Songs (if viewing)
// ----------------------------------------------------------------------
$song_list = [];
$album_info = null;

if (isset($_GET["view_songs"])) {
    $album_id = intval($_GET["view_songs"]);

    // Album info with genre
    $stmt = $conn->prepare("
        SELECT a.Album_Name, a.Description, g.Name AS Genre_Name
        FROM Album a
        LEFT JOIN Genre g ON a.Genre_ID = g.Genre_ID
        WHERE a.Album_ID = ? AND a.Artist_ID = ?
    ");
    $stmt->bind_param("ii", $album_id, $artist_id);
    $stmt->execute();
    $album_info = $stmt->get_result()->fetch_assoc();

    // Songs: fetch genre from Song table
	$stmt = $conn->prepare("
  	  SELECT 
        s.Song_ID, 
        s.Song_Name, 
        s.Duration, 
        s.Lyrics, 
        g.Name AS Genre_Name
  	  FROM Song s
  	  LEFT JOIN Genre g ON s.Genre_ID = g.Genre_ID
  	  WHERE s.Album_ID = ?
		");
$stmt->bind_param("i", $album_id);
$stmt->execute();
$song_list = $stmt->get_result();

}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Artist Dashboard</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="style.css">
</head>
<body>

<!-- Back Button -->
<a href="select_role.php" class="back-btn">⬅ Back</a>

<div class="container">

    <h1>🎵 Artist Dashboard</h1>

    <!-- Artist Info -->
    <div class="card">
        <h2>👤 Artist Info</h2>
        <p><strong>Name:</strong> <?= htmlspecialchars($artist["Name"] ?? "") ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($artist["Email"] ?? "") ?></p>
        <p><strong>Balance:</strong> $<?= htmlspecialchars($artist["Account_Balance"] ?? "0") ?></p>
    </div>

    <!-- Album List -->
    <div class="card">
        <h2>📀 Your Albums</h2>
        <a href="create_album.php" class="btn">+ Create New Album</a>
        <table>
            <tr>
                <th>Album</th>
                <th>Description</th>
                <th>Release</th>
                <th>Genre</th>
                <th>Actions</th>
            </tr>
            <?php while ($a = $albums->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($a["Album_Name"] ?? "") ?></td>
                <td><?= htmlspecialchars($a["Description"] ?? "") ?></td>
                <td><?= htmlspecialchars($a["Release_Date"] ?? "") ?></td>
                <td><?= $a["Genre_Name"] ?: "N/A" ?></td>
                <td>
                    <a class="btn" href="artist_dashboard.php?view_songs=<?= $a["Album_ID"] ?>">View Songs</a>
                    <a class="btn-edit" href="edit_album.php?id=<?= $a["Album_ID"] ?>">Edit</a>
                    <a class="btn-danger" href="delete_album.php?id=<?= $a["Album_ID"] ?>" onclick="return confirm('Delete album?');">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>

    <!-- Songs in Album -->
    <?php if ($album_info) { ?>
    <div class="card">
        <h2>🎶 Songs in: <?= htmlspecialchars($album_info["Album_Name"] ?? "") ?></h2>
        <a class="btn" href="create_song.php?album_id=<?= $album_id ?>">+ Add New Song</a>
        <table>
            <tr>
                <th>Song</th>
                <th>Duration</th>
                <th>Lyrics</th>
                <th>Genre</th>
                <th>Actions</th>
            </tr>
            <?php while ($s = $song_list->fetch_assoc()) { ?>
            <tr>
                <td><?= htmlspecialchars($s["Song_Name"] ?? "") ?></td>
                <td><?= htmlspecialchars($s["Duration"] ?? "") ?></td>
                <td><div class="lyrics-box"><?= htmlspecialchars($s["Lyrics"] ?? "") ?></div></td>
                <td><?= $s["Genre_Name"] ?: "N/A" ?></td>
                <td>
                    <a class="btn-edit" href="edit_song.php?id=<?= $s["Song_ID"] ?>">Edit</a>
                    <a class="btn-danger" href="delete_song.php?id=<?= $s["Song_ID"] ?>" onclick="return confirm('Delete song?');">Delete</a>
                </td>
            </tr>
            <?php } ?>
        </table>
    </div>
    <?php } ?>

</div>
</body>
</html>