<?php include 'config.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Artist List</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>

<body class="p-5">

<h2>Artist List</h2>

<a href="artist_add.php" class="btn btn-success mb-3">+ Add Artist</a>
<a href="artist_dashboard.php" class="btn btn-secondary mb-3">Back</a>

<table class="table table-bordered table-striped">
    <tr>
        <th>ID</th>
        <th>Name</th>
        <th>Email</th>
        <th>Balance</th>
        <th>Action</th>
    </tr>

<?php
$sql = "SELECT * FROM Artist";
$result = $conn->query($sql);

while($row = $result->fetch_assoc()):
?>
    <tr>
        <td><?= $row['Artist_ID'] ?></td>
        <td><?= $row['Name'] ?></td>
        <td><?= $row['Email'] ?></td>
        <td><?= $row['Account_Balance'] ?></td>
        <td>
            <a href="artist_edit.php?id=<?= $row['Artist_ID'] ?>" class="btn btn-warning btn-sm">Edit</a>
            <a href="artist_delete.php?id=<?= $row['Artist_ID'] ?>" class="btn btn-danger btn-sm"
                onclick="return confirm('Delete this artist?');">Delete</a>
        </td>
    </tr>
<?php endwhile; ?>

</table>

</body>
</html>
