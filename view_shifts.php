<?php
// Include db.php
include 'db.php';
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header('Location: admin_login.php'); // Redirect to admin login if not logged in
    exit;
}

// Fetch all staff members
$stmt = $conn->prepare("SELECT s.full_name, ss.shift_date, ss.day_of_week, ss.shift 
                        FROM staff_shifts ss 
                        JOIN staff s ON ss.staff_id = s.id 
                        ORDER BY ss.shift_date ASC");
$stmt->execute();
$assigned_shifts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Assigned Shifts</title>
    <?php include 'cdn.php' ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/shifts.css">
    <style>
      
    </style>
</head>
<body>
<?php include 'admin_navbar.php' ?>
    <div class="view_all_shift">
   <div class="forms">
   <h2>Assigned Shifts</h2>
   </div>

<table>
    <thead>
        <tr>
            <th>Staff Name</th>
            <th>Shift Date</th>
            <th>Day of the Week</th>
            <th>Shift</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($assigned_shifts as $shift): ?>
            <tr>
                <td><?php echo htmlspecialchars($shift['full_name']); ?></td>
                <td><?php echo htmlspecialchars($shift['shift_date']); ?></td>
                <td><?php echo htmlspecialchars($shift['day_of_week']); ?></td>
                <td><?php echo htmlspecialchars($shift['shift']); ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
    </div>
</body>
</html>
