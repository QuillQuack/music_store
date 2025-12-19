<?php include 'config.php'; ?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Artist</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="p-5">

<div class="container" style="max-width: 500px;">
    <h2>Add New Artist</h2>

    <form method="POST" class="card p-4 shadow-sm mt-3">
        <div class="mb-3">
            <label>Artist Name</label>
            <input type="text" name="name" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>

        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>

        <button class="btn btn-success w-100" name="save">Save</button>
        <a href="artist_login.php" class="btn btn-secondary w-100 mt-2">Back to Login</a>
    </form>
</div>

<?php
if (isset($_POST['save'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $conn->prepare("INSERT INTO Artist (Name, Email, Password_Hash, Account_Balance) VALUES (?, ?, ?, 0)");
    $stmt->bind_param("sss", $name, $email, $hash);

    if ($stmt->execute()) {
        echo "<script>alert('Artist added!'); window.location='artist_login.php';</script>";
    } else {
        echo "<div class='alert alert-danger mt-3'>Error: " . $conn->error . "</div>";
    }
}
?>

</body>
</html>