<?php
// Include db.php
include 'db.php';
session_start();

// Check if staff is logged in
if (!isset($_SESSION['staff_email'])) {
    header('Location: staff_login.php'); // Redirect to login if not logged in
    exit;
}

// Fetch staff details from the database using their email
$email = $_SESSION['staff_email'];
$stmt = $conn->prepare("SELECT * FROM staff WHERE email = :email");
$stmt->bindParam(':email', $email);
$stmt->execute();
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

// Update profile
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $phone_number = $_POST['phone_number'];
    $password = $_POST['password'];
    
    // Handle profile image upload
    $profile_image = $staff['profile_image']; // Default to existing image
    if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] == 0) {
        $target_dir = "staff_profile/"; // Folder where images will be saved
        $target_file = $target_dir . basename($_FILES['profile_image']['name']);
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is an actual image
        $check = getimagesize($_FILES['profile_image']['tmp_name']);
        if ($check !== false) {
            // Check file size (e.g., limit to 2MB)
            if ($_FILES['profile_image']['size'] <= 2000000) {
                // Allow certain file formats (jpg, png, jpeg)
                if (in_array($imageFileType, ['jpg', 'png', 'jpeg'])) {
                    if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_file)) {
                        $profile_image = $target_file; // Save the path to the image
                    } else {
                        echo "<script>alert('Error uploading image. Please try again.');</script>";
                    }
                } else {
                    echo "<script>alert('Only JPG, JPEG, and PNG files are allowed.');</script>";
                }
            } else {
                echo "<script>alert('Image size should not exceed 2MB.');</script>";
            }
        } else {
            echo "<script>alert('File is not an image.');</script>";
        }
    }

    // Hash the new password if it was provided
    if (!empty($password)) {
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE staff SET full_name = :full_name, phone_number = :phone_number, profile_image = :profile_image, password = :password WHERE email = :email");
        $stmt->bindParam(':password', $hashed_password);
    } else {
        // Update without password change
        $stmt = $conn->prepare("UPDATE staff SET full_name = :full_name, phone_number = :phone_number, profile_image = :profile_image WHERE email = :email");
    }

    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':profile_image', $profile_image);
    $stmt->bindParam(':email', $email);

    if ($stmt->execute()) {
        echo "<script>alert('Profile updated successfully!'); window.location.href = 'staff_profile.php';</script>";
    } else {
        echo "<script>alert('Error updating profile. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Profile</title>
</head>
<body>
    <h2>Staff Profile</h2>
    <form method="POST" action="staff_profile.php" enctype="multipart/form-data">
        <label for="full_name">Full Name:</label>
        <input type="text" name="full_name" value="<?php echo $staff['full_name']; ?>" required><br><br>

        <label for="email">Email (read-only):</label>
        <input type="email" name="email" value="<?php echo $staff['email']; ?>" readonly><br><br>

        <label for="phone_number">Phone Number:</label>
        <input type="text" name="phone_number" value="<?php echo $staff['phone_number']; ?>"  required><br><br>

        <label for="department">Department:</label>
        <input type="text" name="department" value="<?php echo $staff['department']; ?>" readonly><br><br>

        <label for="profile_image">Profile Image:</label><br>
        <?php if (!empty($staff['profile_image'])): ?>
            <img src="<?php echo $staff['profile_image']; ?>" alt="Profile Image" width="100"><br>
        <?php endif; ?>
        <input type="file" name="profile_image"><br><br>

        <label for="password">New Password (leave blank to keep the current password):</label>
        <input type="password" name="password"><br><br>

        <input type="submit" value="Update Profile">
    </form>
</body>
</html>
