<?php
// update_customer_profile.php
require_once 'config.php'; // include DB connection

session_start();

if (!isset($_SESSION['customer_id'])) {
    header('Location: login.php');
    exit();
}

$customer_id = $_SESSION['customer_id'];
$message = "";

// Fetch current customer data
$sql = "SELECT Name, Email FROM Customer WHERE Customer_ID = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $customer_id);
$stmt->execute();
$result = $stmt->get_result();
$customer = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    if (!empty($password)) {
        $password_hash = password_hash($password, PASSWORD_BCRYPT);
        $update_sql = "UPDATE Customer SET Name = ?, Email = ?, Password_Hash = ? WHERE Customer_ID = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("sssi", $name, $email, $password_hash, $customer_id);
    } else {
        $update_sql = "UPDATE Customer SET Name = ?, Email = ? WHERE Customer_ID = ?";
        $update_stmt = $conn->prepare($update_sql);
        $update_stmt->bind_param("ssi", $name, $email, $customer_id);
    }

    if ($update_stmt->execute()) {
        $message = "Profile updated successfully!";
    } else {
        $message = "Error updating profile: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Update Customer Profile</title>
</head>
<body>
    <h2>Update Profile</h2>

    <?php if ($message): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Name:</label><br>
        <input type="text" name="name" value="<?php echo $customer['Name']; ?>" required><br><br>

        <label>Email:</label><br>
        <input type="email" name="email" value="<?php echo $customer['Email']; ?>" required><br><br>

        <label>New Password (leave blank to keep current):</label><br>
        <input type="password" name="password"><br><br>

        <button type="submit">Update Profile</button>
    </form>
</body>
</html>
