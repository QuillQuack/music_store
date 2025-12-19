<?php
session_start();
include "config.php";

if (!isset($_SESSION['artist_id'])) {
    die("Access denied.");
}

if (isset($_GET['id'])) {
    $album_id = intval($_GET['id']);

    // 1. Delete order items directly linked to album
    $conn->query("
        DELETE FROM Order_Item
        WHERE Album_ID = $album_id
    ");

    // 2. Delete order items linked to songs in this album
    $conn->query("
        DELETE oi
        FROM Order_Item oi
        INNER JOIN Song s ON oi.Song_ID = s.Song_ID
        WHERE s.Album_ID = $album_id
    ");

    // 3. Delete songs under this album
    $conn->query("
        DELETE FROM Song
        WHERE Album_ID = $album_id
    ");

    // 4. Delete the album
    $conn->query("
        DELETE FROM Album
        WHERE Album_ID = $album_id
    ");
}

header("Location: artist_dashboard.php");
exit;
?>
s