<?php
// Include db.php
include 'db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $phone_number = $_POST['phone_number'];
    $gender = $_POST['gender']; // Gender field
    $role = $_POST['role'];
    $department = $_POST['department'];
    $password = $_POST['password'];

    // Hash the password before storing it
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insert the staff details into the database
    $stmt = $conn->prepare("INSERT INTO staff (full_name, email, phone_number, gender, role, department, password) 
                            VALUES (:full_name, :email, :phone_number, :gender, :role, :department, :password)");
    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':gender', $gender); // Bind gender
    $stmt->bindParam(':role', $role);
    $stmt->bindParam(':department', $department);
    $stmt->bindParam(':password', $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Staff member successfully onboarded!'); window.location.href = 'onboard_staff.php';</script>";
    } else {
        echo "<script>alert('Error onboarding staff. Please try again.');</script>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staff Onboarding</title>
    <?php include 'cdn.php'?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/onboard_staff.css">
</head>
<body>
<?php include 'admin_navbar.php'?>
  <div class="onboard_staff_all">
  <div class="forms">
  <h2>Staff Onboarding</h2>
  </div>
    <form method="POST" action="onboard_staff.php">
       <div class="forms">
       <label for="full_name">Full Name:</label>
       <input type="text" name="full_name" required>
       </div>

        <div class="forms">
        <label for="email">Email:</label>
        <input type="email" name="email" required>
        </div>

       <div class="forms">
       <label for="phone_number">Phone Number:</label>
       <input type="text" name="phone_number" required>
       </div>

      <div class="forms">
      <label for="gender">Gender:</label>
        <select name="gender" required>
            <option value="" selected hidden>Select Gender</option>
            <option value="Male">Male</option>
            <option value="Female">Female</option>
        </select>
      </div>

      <div class="forms">
      <label for="role">Role:</label>
        <select name="role" required>
            <option value="" selected hidden>Select Role</option>
            <option value="Nurse">Nurse</option>
            <option value="Doctor">Doctor</option>
            <option value="Midwife">Midwife</option>
        </select>
      </div>

     <div class="forms">
     <label for="department">Department:</label>
        <select name="department" required>
            <option value="" selected hidden>Select Department</option>
            <option value="Emergency">Emergency</option>
            <option value="Pediatrics">Pediatrics</option>
            <option value="Surgery">Surgery</option>
            <option value="Maternity">Maternity</option>
            <option value="Radiology">Radiology</option>
        </select>
     </div>

        <div class="forms">
        <label for="password">Password:</label>
        <input type="password" name="password" required>
        </div>
<div class="forms">
    <button type="submit" >Onboard Staff</button>
</div>
       
    </form>
  </div>
</body>
</html>
