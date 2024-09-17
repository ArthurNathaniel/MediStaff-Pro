<?php
// Include db.php
include 'db.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch the staff data from the database based on email
    $stmt = $conn->prepare("SELECT * FROM staff WHERE email = :email");
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    $staff = $stmt->fetch(PDO::FETCH_ASSOC);

    // Verify the password
    if ($staff && password_verify($password, $staff['password'])) {
        $_SESSION['staff_email'] = $staff['email'];
        $_SESSION['staff_name'] = $staff['full_name'];
        header('Location: staff_dashboard.php'); // Redirect to staff dashboard after login
        exit;
    } else {
        echo "<script>alert('Invalid email or password!');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Login</title>
</head>
<body>
    <h2>Staff Login</h2>
    <form method="POST" action="staff_login.php">
        <label for="email">Email:</label>
        <input type="email" name="email" required><br><br>

        <label for="password">Password:</label>
        <input type="password" name="password" required><br><br>

        <input type="submit" value="Login">
    </form>
</body>
</html>
