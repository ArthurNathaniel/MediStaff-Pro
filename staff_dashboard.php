<?php
session_start();
if (!isset($_SESSION['staff_email'])) {
    header('Location: staff_login.php'); // Redirect if not logged in
    exit;
}

echo "Welcome, " . $_SESSION['staff_name'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
</head>
<body>
    <h2>Staff Dashboard</h2>
    <p>Welcome, <?php echo $_SESSION['staff_name']; ?>!</p>
    <!-- Add staff dashboard content here -->
</body>
</html>
