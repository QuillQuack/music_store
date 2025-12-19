<?php
include 'config.php';

$id = $_GET['id'];

$sql = "DELETE FROM Artist WHERE Artist_ID=$id";

if ($conn->query($sql)) {
    echo "<script>alert('Artist deleted!'); window.location='artist_list.php';</script>";
} else {
    echo "Error deleting record.";
}
?>
