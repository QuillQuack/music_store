<?php
session_start();
include 'config.php';

// เมื่อ user เลือกศิลปิน
if (isset($_POST["artist_id"])) {
    $_SESSION["artist_id"] = $_POST["artist_id"];
    header("Location: artist_dashboard.php");
    exit;
}

// ดึงรายชื่อศิลปินทั้งหมด
$artists = $conn->query("SELECT Artist_ID, Name FROM Artist ORDER BY Name ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Select Artist</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body class="p-5">

<h3>Select Artist</h3>

<form method="POST" class="mt-3">
    <select name="artist_id" class="form-select" style="max-width:300px;" required>
        <option value="">-- Select Artist --</option>

        <?php while($a = $artists->fetch_assoc()): ?>
            <option value="<?= $a['Artist_ID'] ?>"><?= $a['Name'] ?></option>
        <?php endwhile; ?>
    </select>

    <button type="submit" class="btn btn-primary mt-3">Go to Dashboard</button>
</form>

</body>
</html>
