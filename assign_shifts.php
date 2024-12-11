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

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['assign_shifts'])) {
    $staff_id = isset($_POST['staff_id']) ? $_POST['staff_id'] : null;
    $shift_date = isset($_POST['shift_date']) ? $_POST['shift_date'] : null;
    $shift = isset($_POST['shift']) ? $_POST['shift'] : null;

    // Check if all required fields are filled
    if (!empty($staff_id) && !empty($shift_date) && !empty($shift)) {
        try {
            // Check if the staff is already assigned to a shift on the selected date
            $stmt = $conn->prepare("SELECT COUNT(*) FROM staff_shifts WHERE staff_id = :staff_id AND shift_date = :shift_date");
            $stmt->bindParam(':staff_id', $staff_id);
            $stmt->bindParam(':shift_date', $shift_date);
            $stmt->execute();
            $shift_exists = $stmt->fetchColumn();

            if ($shift_exists > 0) {
                echo "<script>alert('This staff member is already assigned to a shift on the selected date.');</script>";
            } else {
                // Insert the new shift
                $stmt = $conn->prepare("INSERT INTO staff_shifts (staff_id, shift_date, day_of_week, shift) VALUES (:staff_id, :shift_date, :day_of_week, :shift)");
                $day_of_week = date('l', strtotime($shift_date));
                $stmt->bindParam(':staff_id', $staff_id);
                $stmt->bindParam(':shift_date', $shift_date);
                $stmt->bindParam(':day_of_week', $day_of_week);
                $stmt->bindParam(':shift', $shift);
                $stmt->execute();

                echo "<script>alert('Shift assigned successfully!'); window.location.href = 'assign_shifts.php';</script>";
            }
        } catch (PDOException $e) {
            echo "Error: " . $e->getMessage();
        }
    } else {
        echo "<script>alert('Please fill in all fields.');</script>";
    }
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assign Shifts</title>
    <?php include 'cdn.php' ?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/shifts.css">
</head>

<body>
    <?php include 'admin_navbar.php' ?>
  <div class="shifts_all">
  <div class="forms">
        <h2>Assign Shift to Staff</h2>
    </div>

    <form method="POST" action="assign_shifts.php">
        <div class="forms">
            <label for="staff">Select Staff:</label>
            <select name="staff_id" id="staff" required>
                <option value="" selected hidden>Select Staff</option>
                <?php foreach ($staff_members as $staff): ?>
                    <option value="<?php echo $staff['id']; ?>"><?php echo htmlspecialchars($staff['full_name']); ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="forms">
            <label for="shift_date">Select Date:</label>
            <input type="date" name="shift_date" id="shift_date" required>
        </div>

        <div class="forms">
            <label for="shift">Select Shift:</label>
            <select name="shift" id="shift" required>
                <option value="" selected hidden>Select Shift</option>
                <option value="Morning">Morning</option>
                <option value="Afternoon">Afternoon</option>
                <option value="Evening">Evening</option>
                <option value="Night">Night</option>
            </select>
        </div>
        <div class="forms">
            <button type="submit" name="assign_shifts">Assign Shift</button>
        </div>
    </form>
  </div>
</body>

</html>