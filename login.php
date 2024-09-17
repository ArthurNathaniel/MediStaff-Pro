<?php
// Include db.php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Fetch the admin data from the database
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();
    $admin = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify the password
    if ($admin && password_verify($password, $admin['password'])) {
        $_SESSION['admin_username'] = $admin['username'];
        echo "<script>alert('Login successful!'); window.location.href = 'admin_dashboard.php';</script>";
        exit;
    } else {
        echo "<script>alert('Invalid username or password!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/auth.css">
</head>
<body>
<div class="auth_all">
<div class="forms">
<h2>Medistaff Pro - Admin Login</h2>
</div>
    <form method="POST" action="login.php">
      <div class="forms">
      <label for="username">Username:</label>
      <input type="text" name="username" required>
      </div>
        
     <div class="forms">
     <label for="password">Password:</label>
     <input type="password" name="password" required>
     </div>
        <div class="forms">
            <button type="submit">Login</button>
        </div>
    </form>
</div>
</body>
</html>
