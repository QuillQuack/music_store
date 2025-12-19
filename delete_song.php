<?php
session_start();
include "config.php";

if (!isset($_SESSION['artist_id'])) {
    die("Access denied.");
}

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Get Album ID first so we can redirect back to the list
    $q = $conn->query("SELECT Album_ID FROM Song WHERE Song_ID = $id");
    $row = $q->fetch_assoc();
    $album_id = $row['Album_ID'];

    $conn->query("DELETE FROM Song WHERE Song_ID = $id");

    header("Location: artist_dashboard.php?view_songs=" . $album_id);
} else {
    header("Location: artist_dashboard.php");
}
exit;
?>