<?php 
include 'config.php';

$id = $_GET['id'];
$sql = "SELECT * FROM Artist WHERE Artist_ID=$id";
$row = $conn->query($sql)->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Artist</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body class="p-5">

<h2>Edit Artist</h2>

<form method="POST">
    <div class="mb-3">
        <label>Name</label>
        <input type="text" name="name" value="<?= $row['Name'] ?>" class="form-control">
    </div>

    <div class="mb-3">
        <label>Email</label>
        <input type="email" name="email" value="<?= $row['Email'] ?>" class="form-control">
    </div>

    <div class="mb-3">
        <label>Balance</label>
        <input type="number" step="0.01" name="balance" value="<?= $row['Account_Balance'] ?>" class="form-control">
    </div>

    <button class="btn btn-primary" name="update">Update</button>
    <a href="artist_list.php" class="btn btn-secondary">Back</a>
</form>

<?php
if (isset($_POST['update'])) {

    $name = $_POST['name'];
    $email = $_POST['email'];
    $balance = $_POST['balance'];
    $genre = $_POST['genre'];

    $sql = "UPDATE Artist 
            SET Name='$name', Email='$email', Account_Balance='$balance', Genre='$_genre'
            WHERE Artist_ID=$id";

    if ($conn->query($sql)) {
        echo "<script>alert('Updated!'); window.location='artist_list.php';</script>";
    }
}
?>

</body>
</html>

