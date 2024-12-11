<?php
// Include db.php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Securely hash the password

    // Check if username already exists
    $stmt = $conn->prepare("SELECT * FROM admins WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        echo "<script>alert('Username already exists!');</script>";
    } else {
        // Insert new admin into the database
        $stmt = $conn->prepare("INSERT INTO admins (username, password) VALUES (:username, :password)");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password);

        if ($stmt->execute()) {
            echo "<script>alert('Admin registered successfully!'); window.location.href = 'login.php';</script>";
        } else {
            echo "<script>alert('Error registering admin.');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Signup</title>
    <?php include 'cdn.php'?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/auth.css">
</head>

<body>
    <div class="auth_all">
        <div class="logo"></div>
        <div class="forms">
        <h2>Admin Signup</h2>
        </div>
       
        <form method="POST" action="signup.php">
           <div class="forms">
           <label for="username">Username:</label>
           <input type="text" name="username" required>
           </div>

           <div class="forms">
           <label for="password">Password:</label>
           <input type="password" name="password" required>
           </div>

            <div class="forms">
                <button  type="submit" >Signup</button>
            </div>
        </form>
        </div>
</body>

</html>