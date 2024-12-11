<?php
session_start();
if (!isset($_SESSION['staff_email']) || !isset($_SESSION['staff_id'])) {
    header('Location: staff_login.php'); // Redirect if not logged in
    exit;
}

include 'db.php'; // Include the database connection

// Fetch staff profile image and today's shift
$staff_id = $_SESSION['staff_id'];
$today_date = date('Y-m-d');

// Get staff profile image
$stmt = $conn->prepare("SELECT profile_image FROM staff WHERE id = :staff_id");
$stmt->bindParam(':staff_id', $staff_id);
$stmt->execute();
$staff = $stmt->fetch(PDO::FETCH_ASSOC);

// Get today's shift
$stmt = $conn->prepare("SELECT shift FROM staff_shifts WHERE staff_id = :staff_id AND shift_date = :shift_date");
$stmt->bindParam(':staff_id', $staff_id);
$stmt->bindParam(':shift_date', $today_date);
$stmt->execute();
$today_shift = $stmt->fetch(PDO::FETCH_ASSOC);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Dashboard</title>
    <?php include 'cdn.php'?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/dashboard.css">
</head>
<body>
<?php include 'staff_navbar.php'?>
<div class="dashboard_all">
<div class="dashboard_title">
            <p>Welcome back, <?php echo htmlspecialchars($_SESSION['staff_name']); ?>!</p>
</div>
            <div class="profile">
                <?php if (!empty($staff['profile_image'])): ?>
                    <img src="staff_profile/<?php echo htmlspecialchars($staff['profile_image']); ?>" alt="Profile Image">
                <?php else: ?>
                    <img src="images/default_profile.png" alt="Default Profile Image">
                <?php endif; ?>
            </div>

            <h3>View Today's Shift</h3>
            <p>
                <?php if (!empty($today_shift)): ?>
                    You are scheduled for the <?php echo htmlspecialchars($today_shift['shift']); ?> shift today.
                <?php else: ?>
                    You have no shift assigned for today.
                <?php endif; ?>
            </p>
        </div>
    </div>
                </div>
</body>
</html>
