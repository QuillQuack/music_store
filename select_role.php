<?php
session_start();

// เคลียร์ session เก่าออกทั้งหมดก่อนเลือก role ใหม่
session_unset();
session_destroy();
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<title>Select Role</title>
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="style.css">
<style>
    .card{
    width: 50%;
    }
</style>

</head>

<body>

<div class="card text-center">
    <h2>Select Your Role</h2>
    <p class="mb-4">Choose how you want to continue</p>

    <!-- CUSTOMER -->
    <a href="customer_auth.php" class="btn btn-dark role-btn">
        🧑‍💼 Continue as Customer
    </a>

    <!-- ARTIST -->
    <a href="artist_login.php" class="btn btn-dark role-btn">
        🎤 Continue as Artist
    </a>

</div>

</body>
</html>
