<?php
session_start();
include 'db.php'; // Include your database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch staff details from the database
    $stmt = $conn->prepare("SELECT id, full_name, profile_image, password FROM staff WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($staff && password_verify($password, $staff['password'])) {
        // Set session variables
        $_SESSION['staff_id'] = $staff['id'];
        $_SESSION['staff_name'] = $staff['full_name'];
        $_SESSION['staff_email'] = $email;
        $_SESSION['profile_image'] = $staff['profile_image'];

        // Redirect to dashboard
        header('Location: staff_dashboard.php');
        exit;
    } else {
        // Handle login error
        echo "<script>alert('Invalid email or password');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login</title>
    <?php include 'cdn.php' ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/auth.css">
</head>

<body>
<div class="auth_all">
<div class="logo"></div>
   <div class="forms">
   <h2>Staff Login</h2>
   </div>
    <form method="POST" action="staff_login.php">

        <div class="forms">
            <label for="email">Email:</label>
            <input type="email" placeholder="Enter your email address" name="email" required>
        </div>

        <div class="forms">
            <label for="password">Password:</label>
            <input type="password" placeholder="Enter your password" name="password" required>
        </div>

        <div class="forms">
            <button type="submit">Login</button>
        </div>

    </form>
</div>
</body>

</html>