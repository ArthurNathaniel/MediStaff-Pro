<?php
session_start();
include 'db.php'; // Include your database connection

// Check if staff is logged in
if (!isset($_SESSION['staff_id'])) {
    header('Location: staff_login.php'); // Redirect to login if not logged in
    exit;
}

$staff_id = $_SESSION['staff_id'];

// Get the current week's start and end dates
$start_of_week = date('Y-m-d', strtotime('last Sunday'));
$end_of_week = date('Y-m-d', strtotime('next Saturday'));

// Fetch the staff's shifts for the week
$stmt = $conn->prepare("SELECT shift_date, day_of_week, shift FROM staff_shifts WHERE staff_id = :staff_id AND shift_date BETWEEN :start_date AND :end_date ORDER BY shift_date");
$stmt->bindParam(':staff_id', $staff_id);
$stmt->bindParam(':start_date', $start_of_week);
$stmt->bindParam(':end_date', $end_of_week);
$stmt->execute();
$shifts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Create an associative array with days of the week as keys
$week_shifts = [
    'Sunday' => null,
    'Monday' => null,
    'Tuesday' => null,
    'Wednesday' => null,
    'Thursday' => null,
    'Friday' => null,
    'Saturday' => null
];

// Populate the array with shift data
foreach ($shifts as $shift) {
    $week_shifts[$shift['day_of_week']] = $shift;
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Shifts</title>
    <?php include 'cdn.php'; ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/shifts.css">
</head>

<body>
    <?php include 'staff_navbar.php'; ?>
    <div class="shifts_all">
        <h2>My Shifts for the Week</h2>
        <table border="1" cellspacing="0" cellpadding="10">
            <thead>
                <tr>
                    <th>Day</th>
                    <th>Date</th>
                    <th>Shift</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($week_shifts as $day => $shift): ?>
                    <tr>
                        <td><?php echo $day; ?></td>
                        <td><?php echo $shift ? htmlspecialchars($shift['shift_date']) : 'N/A'; ?></td>
                        <td><?php echo $shift ? htmlspecialchars($shift['shift']) : 'No Shift Assigned'; ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
