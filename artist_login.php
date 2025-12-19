<?php
session_start();
require_once "config.php";

if (isset($_POST["login"])) {
    $email = $_POST["email"];
    $password = $_POST["password"];

    $stmt = $conn->prepare("SELECT Artist_ID, Name, Password_Hash FROM Artist WHERE Email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($row = $result->fetch_assoc()) {
        if (password_verify($password, $row['Password_Hash'])) {
            // Success! Set session
            $_SESSION['artist_id'] = $row['Artist_ID'];
            $_SESSION['artist_name'] = $row['Name'];
            header("Location: artist_dashboard.php");
            exit;
        }
    }

    $error = "Invalid email or password.";
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Artist Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body class="d-flex justify-content-center align-items-center vh-100">

<div class="card p-4 shadow" style="width: 400px;">
    <h3 class="text-center mb-4">🎤 Artist Login</h3>

    <?php if(isset($error)): ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label>Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Password</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button type="submit" name="login" class="btn btn-dark w-100">Login</button>
    </form>

    <div class="text-center mt-3">
        <a href="artist_add.php">Register as new Artist</a> |
        <a href="select_role.php">Back</a>
    </div>
</div>

</body>
</html>