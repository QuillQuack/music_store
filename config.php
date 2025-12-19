<?php
// ห้ามมีช่องว่างก่อนหรือหลังโค้ดนี้

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$host = "fdb1033.awardspace.net";
$user = "4685049_table";
$pass = "a%a5Lja_8P!1vy}8";
$dbname = "4685049_table";

$conn = new mysqli($host, $user, $pass, $dbname);

if ($conn->connect_error) {
    die("Database connection failed: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");






