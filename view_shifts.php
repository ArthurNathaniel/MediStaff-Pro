<?php
// Include db.php
include 'db.php';
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header('Location: admin_login.php'); // Redirect to admin login if not logged in
    exit;
}

// Define default values for filters
$start_date = isset($_POST['start_date']) ? $_POST['start_date'] : '';
$end_date = isset($_POST['end_date']) ? $_POST['end_date'] : '';
$day_of_week = isset($_POST['day_of_week']) ? $_POST['day_of_week'] : '';

// Fetch all staff members
$stmt = $conn->prepare("SELECT id, full_name FROM staff");
$stmt->execute();
$staff_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Build SQL query based on filters
$sql = "
    SELECT s.id AS staff_id, s.full_name, sh.shift_date, sh.day_of_week, sh.shift 
    FROM staff s 
    LEFT JOIN staff_shifts sh ON s.id = sh.staff_id 
    WHERE 1
";

if (!empty($start_date) && !empty($end_date)) {
    $sql .= " AND sh.shift_date BETWEEN :start_date AND :end_date";
}

if (!empty($day_of_week)) {
    $sql .= " AND sh.day_of_week = :day_of_week";
}

$sql .= " ORDER BY s.full_name, sh.shift_date";

$stmt = $conn->prepare($sql);

if (!empty($start_date) && !empty($end_date)) {
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
}

if (!empty($day_of_week)) {
    $stmt->bindParam(':day_of_week', $day_of_week);
}

$stmt->execute();
$staff_shifts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Staff Shifts</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        table, th, td {
            border: 1px solid black;
        }

        th, td {
            padding: 10px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>View Staff Shifts</h2>

    <form method="POST" action="view_shifts.php">
        <label for="start_date">Start Date:</label>
        <input type="date" name="start_date" id="start_date" value="<?php echo htmlspecialchars($start_date); ?>"><br><br>

        <label for="end_date">End Date:</label>
        <input type="date" name="end_date" id="end_date" value="<?php echo htmlspecialchars($end_date); ?>"><br><br>

        <label for="day_of_week">Day of the Week:</label>
        <select name="day_of_week" id="day_of_week">
            <option value="">Select Day</option>
            <option value="Monday" <?php echo ($day_of_week == 'Monday') ? 'selected' : ''; ?>>Monday</option>
            <option value="Tuesday" <?php echo ($day_of_week == 'Tuesday') ? 'selected' : ''; ?>>Tuesday</option>
            <option value="Wednesday" <?php echo ($day_of_week == 'Wednesday') ? 'selected' : ''; ?>>Wednesday</option>
            <option value="Thursday" <?php echo ($day_of_week == 'Thursday') ? 'selected' : ''; ?>>Thursday</option>
            <option value="Friday" <?php echo ($day_of_week == 'Friday') ? 'selected' : ''; ?>>Friday</option>
            <option value="Saturday" <?php echo ($day_of_week == 'Saturday') ? 'selected' : ''; ?>>Saturday</option>
            <option value="Sunday" <?php echo ($day_of_week == 'Sunday') ? 'selected' : ''; ?>>Sunday</option>
        </select><br><br>

        <input type="submit" value="Filter">
    </form>

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
            <?php if (count($staff_shifts) > 0): ?>
                <?php foreach ($staff_shifts as $shift): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($shift['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($shift['shift_date']); ?></td>
                        <td><?php echo htmlspecialchars($shift['day_of_week']); ?></td>
                        <td><?php echo htmlspecialchars($shift['shift']); ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="4">No shifts found for the selected criteria.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
