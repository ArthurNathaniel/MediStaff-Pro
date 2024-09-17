<?php
// Include db.php
include 'db.php';
session_start();

// Check if the admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header('Location: admin_login.php'); // Redirect to admin login if not logged in
    exit;
}

// Fetch all staff members from the database
$stmt = $conn->prepare("SELECT * FROM staff");
$stmt->execute();
$staff_members = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Update staff member details (Edit action)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_staff'])) {
    $staff_id = $_POST['staff_id'];
    $full_name = $_POST['full_name'];
    $phone_number = $_POST['phone_number'];
    $role = $_POST['role'];
    $department = $_POST['department'];
    $gender = $_POST['gender']; // Get gender

    $stmt = $conn->prepare("UPDATE staff SET full_name = :full_name, phone_number = :phone_number, role = :role, department = :department, gender = :gender WHERE id = :id");
    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':department', $department);
    $stmt->bindParam(':gender', $gender); // Bind gender
    $stmt->bindParam(':id', $staff_id);
    $stmt->execute();

    header("Location: view_all_staff.php");
    exit;
}

// Delete staff member (Delete action)
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_staff'])) {
    $staff_id = $_POST['staff_id'];

    $stmt = $conn->prepare("DELETE FROM staff WHERE id = :id");
    $stmt->bindParam(':id', $staff_id);
    $stmt->execute();

    header("Location: view_all_staff.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Staff Members</title>
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

        img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 50%;
        }

        .close {
            float: right;
            cursor: pointer;
            font-size: 20px;
        }
    </style>
</head>
<body>
    <h2>All Staff Members</h2>

    <table>
        <thead>
            <tr>
                <th>Profile Image</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Phone Number</th>
                <th>Role</th>
                <th>Department</th>
                <th>Gender</th> <!-- Add Gender Column -->
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php if (count($staff_members) > 0): ?>
                <?php foreach ($staff_members as $staff): ?>
                    <tr>
                        <td>
                            <?php if (!empty($staff['profile_image'])): ?>
                                <img src="<?php echo $staff['profile_image']; ?>" alt="Profile Image">
                            <?php else: ?>
                                <img src="images/default_profile.png" alt="Default Image"> 
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($staff['full_name']); ?></td>
                        <td><?php echo htmlspecialchars($staff['email']); ?></td>
                        <td><?php echo htmlspecialchars($staff['phone_number']); ?></td>
                        <td><?php echo htmlspecialchars($staff['role']); ?></td>
                        <td><?php echo htmlspecialchars($staff['department']); ?></td>
                        <td><?php echo htmlspecialchars($staff['gender']); ?></td> <!-- Display Gender -->
                        <td>
                            <button onclick="openEditModal('<?php echo $staff['id']; ?>', '<?php echo htmlspecialchars($staff['full_name']); ?>', '<?php echo htmlspecialchars($staff['phone_number']); ?>', '<?php echo htmlspecialchars($staff['role']); ?>', '<?php echo htmlspecialchars($staff['department']); ?>', '<?php echo htmlspecialchars($staff['gender']); ?>')">Edit</button>
                            <button onclick="openDeleteModal('<?php echo $staff['id']; ?>')">Delete</button>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php else: ?>
                <tr>
                    <td colspan="8">No staff members found.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>

    <!-- Edit Modal -->
    <div id="editModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeEditModal()">&times;</span>
            <h2>Edit Staff Member</h2>
            <form method="POST" action="view_all_staff.php">
                <input type="hidden" name="staff_id" id="edit_staff_id">
                
                <label for="full_name">Full Name:</label>
                <input type="text" name="full_name" id="edit_full_name" required><br><br>
                
                <label for="phone_number">Phone Number:</label>
                <input type="text" name="phone_number" id="edit_phone_number" required><br><br>
                
                <label for="role">Role:</label>
                <select name="role" id="edit_role" required>
                    <option value="" selected hidden>Select Role</option>
                    <option value="Nurse">Nurse</option>
                    <option value="Doctor">Doctor</option>
                    <option value="Midwife">Midwife</option>
                    <option value="Other">Other</option>
                </select><br><br>
                
                <label for="department">Department:</label>
                <select name="department" id="edit_department" required>
                    <option value="" selected hidden>Select Department</option>
                    <option value="Emergency">Emergency</option>
                    <option value="Pediatrics">Pediatrics</option>
                    <option value="Surgery">Surgery</option>
                    <option value="Maternity">Maternity</option>
                    <option value="Radiology">Radiology</option>
                </select><br><br>

                <label for="gender">Gender:</label> <!-- Gender Selection -->
                <select name="gender" id="edit_gender" required>
                    <option value="" selected hidden>Select Gender</option>
                    <option value="Male">Male</option>
                    <option value="Female">Female</option>
                </select><br><br>
                
                <input type="submit" name="edit_staff" value="Save Changes">
            </form>
        </div>
    </div>

    <!-- Delete Modal -->
    <div id="deleteModal" class="modal">
        <div class="modal-content">
            <span class="close" onclick="closeDeleteModal()">&times;</span>
            <h2>Confirm Deletion</h2>
            <p>Are you sure you want to delete this staff member?</p>
            <form method="POST" action="view_all_staff.php">
                <input type="hidden" name="staff_id" id="delete_staff_id">
                <input type="submit" name="delete_staff" value="Yes, Delete">
                <button type="button" onclick="closeDeleteModal()">Cancel</button>
            </form>
        </div>
    </div>

    <script>
        // Functions to handle Edit modal
        function openEditModal(id, fullName, phoneNumber, role, department, gender) {
            document.getElementById('edit_staff_id').value = id;
            document.getElementById('edit_full_name').value = fullName;
            document.getElementById('edit_phone_number').value = phoneNumber;
            
            // Set the selected role, department, and gender
            document.getElementById('edit_role').value = role;
            document.getElementById('edit_department').value = department;
            document.getElementById('edit_gender').value = gender;
            
            document.getElementById('editModal').style.display = 'flex';
        }

        function closeEditModal() {
            document.getElementById('editModal').style.display = 'none';
        }

        // Functions to handle Delete modal
        function openDeleteModal(id) {
            document.getElementById('delete_staff_id').value = id;
            document.getElementById('deleteModal').style.display = 'flex';
        }

        function closeDeleteModal() {
            document.getElementById('deleteModal').style.display = 'none';
        }
    </script>
</body>
</html>
