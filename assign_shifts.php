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
$stmt = $conn->prepare("SELECT id, full_name FROM staff");
$stmt->execute();
$staff_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate the current week's start and end dates
$current_date = new DateTime();
$start_of_week = (clone $current_date)->modify('Monday this week')->format('Y-m-d');
$end_of_week = (clone $current_date)->modify('Sunday this week')->format('Y-m-d');

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_shifts'])) {
    $staff_id = $_POST['staff_id'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    // Check if dates are within the same week
    $start_week = date('W', strtotime($start_date));
    $end_week = date('W', strtotime($end_date));
    if ($start_week != $end_week) {
        echo "<script>alert('Start date and end date must be in the same week.'); window.location.href = 'assign_shifts.php';</script>";
        exit;
    }

    // Delete existing shifts for the specified date range and staff
    $stmt = $conn->prepare("DELETE FROM staff_shifts WHERE staff_id = :staff_id AND shift_date BETWEEN :start_date AND :end_date");
    $stmt->bindParam(':staff_id', $staff_id);
    $stmt->bindParam(':start_date', $start_date);
    $stmt->bindParam(':end_date', $end_date);
    $stmt->execute();

    // Insert new shifts for each day in the specified date range
    $current_date = $start_date;
    while (strtotime($current_date) <= strtotime($end_date)) {
        $day_of_week = date('l', strtotime($current_date));
        $shift = $_POST['shifts'][$day_of_week] ?? '';

        if ($shift) {
            $stmt = $conn->prepare("INSERT INTO staff_shifts (staff_id, shift_date, day_of_week, shift) VALUES (:staff_id, :shift_date, :day_of_week, :shift)");
            $stmt->bindParam(':staff_id', $staff_id);
            $stmt->bindParam(':shift_date', $current_date);
            $stmt->bindParam(':day_of_week', $day_of_week);
            $stmt->bindParam(':shift', $shift);
            $stmt->execute();
        }

        // Move to the next day
        $current_date = date('Y-m-d', strtotime($current_date . ' +1 day'));
    }

    echo "<script>alert('Shifts assigned successfully!'); window.location.href = 'assign_shifts.php';</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Shifts</title>
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

        select {
            width: 100%;
        }
    </style>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Get today's date
            const today = new Date();

            // Get the start and end of the week
            const startOfWeek = new Date(today.setDate(today.getDate() - today.getDay() + 1)); // Monday
            const endOfWeek = new Date(today.setDate(today.getDate() - today.getDay() + 7)); // Sunday

            // Format dates as YYYY-MM-DD
            function formatDate(date) {
                const year = date.getFullYear();
                const month = ('0' + (date.getMonth() + 1)).slice(-2);
                const day = ('0' + date.getDate()).slice(-2);
                return `${year}-${month}-${day}`;
            }

            // Set default values for the date inputs
            document.getElementById('start_date').value = formatDate(startOfWeek);
            document.getElementById('end_date').value = formatDate(endOfWeek);
        });
    </script>
</head>
<body>
    <h2>Assign Shifts to Staff</h2>
    <p>Note: The start date is set to the Monday of the current week and the end date is set to the Sunday of the current week.</p>

    <form method="POST" action="assign_shifts.php">
        <label for="staff">Select Staff:</label>
        <select name="staff_id" id="staff" required>
            <option value="" selected hidden>Select Staff</option>
            <?php foreach ($staff_members as $staff): ?>
                <option value="<?php echo $staff['id']; ?>"><?php echo htmlspecialchars($staff['full_name']); ?></option>
            <?php endforeach; ?>
        </select><br><br>

        <label for="start_date">Start Date (Monday):</label>
        <input type="date" name="start_date" id="start_date" required><br><br>

        <label for="end_date">End Date (Sunday):</label>
        <input type="date" name="end_date" id="end_date" required><br><br>

        <table>
            <thead>
                <tr>
                    <th>Day of the Week</th>
                    <th>Shift</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $days_of_week = ['Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday', 'Sunday'];
                $shifts = ['Morning', 'Afternoon', 'Evening', 'Night'];
                foreach ($days_of_week as $day):
                ?>
                    <tr>
                        <td><?php echo $day; ?></td>
                        <td>
                            <select name="shifts[<?php echo $day; ?>]" required>
                                <option value="" selected hidden>Select Shift</option>
                                <?php foreach ($shifts as $shift): ?>
                                    <option value="<?php echo $shift; ?>"><?php echo $shift; ?></option>
                                <?php endforeach; ?>
                            </select>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table><br>

        <input type="submit" name="assign_shifts" value="Assign Shifts">
    </form>
</body>
</html>
