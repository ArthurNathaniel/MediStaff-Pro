<?php
session_start();

// Check if admin is logged in
if (!isset($_SESSION['admin_username'])) {
    header('Location: login.php');
    exit;
}

// Include db.php
include 'db.php';

// Query for total staff
$total_staff_query = $conn->query("SELECT COUNT(*) as total_staff FROM staff");
$total_staff = $total_staff_query->fetchColumn();

// Query for gender distribution
$gender_query = $conn->query("SELECT gender, COUNT(*) as count FROM staff GROUP BY gender");
$gender_data = $gender_query->fetchAll(PDO::FETCH_ASSOC);

// Query for role distribution
$role_query = $conn->query("SELECT role, COUNT(*) as count FROM staff GROUP BY role");
$role_data = $role_query->fetchAll(PDO::FETCH_ASSOC);

// Query for department distribution
$department_query = $conn->query("SELECT department, COUNT(*) as count FROM staff GROUP BY department");
$department_data = $department_query->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard</title>
    <?php include 'cdn.php'?>
    <link rel="stylesheet" href="./css/base.css">
    <link rel="stylesheet" href="./css/dashboard.css">
</head>
<body>
  



   <div class="dashboard_all">
    <div class="dashboard_title">
        <h1>Staff Statistics</h1>
    </div>
    <div class="dashboard_grid">
 <div class="dash_box">
 <canvas id="totalStaffChart" width="400" height="200"></canvas>
 </div>
 <div class="dash_box">
    <canvas id="genderChart" width="400" height="200"></canvas>
    </div>
    <div class="dash_box">
    <canvas id="roleChart" width="400" height="200"></canvas>
    </div>
    <div class="dash_box">
    <canvas id="departmentChart" width="400" height="200"></canvas>
    </div>


    </div>
   </div>
    <script>
        // Total Staff Chart
        var totalStaffCtx = document.getElementById('totalStaffChart').getContext('2d');
        var totalStaffChart = new Chart(totalStaffCtx, {
            type: 'doughnut',
            data: {
                labels: ['Total Staff'],
                datasets: [{
                    label: 'Total Staff',
                    data: [<?php echo $total_staff; ?>],
                    backgroundColor: [
                        'rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(255, 205, 86)', 'rgb(75, 192, 192)', 'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)', 'rgb(255, 159, 64)', 'rgb(255, 99, 71)', 'rgb(144, 238, 144)', 'rgb(173, 216, 230)',
                        'rgb(250, 128, 114)', 'rgb(255, 69, 0)', 'rgb(255, 20, 147)', 'rgb(138, 43, 226)', 'rgb(139, 69, 19)',
                        'rgb(47, 79, 79)', 'rgb(112, 128, 144)', 'rgb(119, 136, 153)', 'rgb(0, 255, 255)', 'rgb(0, 128, 128)',
                        'rgb(123, 104, 238)', 'rgb(72, 61, 139)', 'rgb(106, 90, 205)', 'rgb(240, 230, 140)', 'rgb(255, 140, 0)',
                        'rgb(255, 215, 0)', 'rgb(255, 248, 220)', 'rgb(240, 255, 255)', 'rgb(70, 130, 180)', 'rgb(176, 196, 222)',
                        'rgb(220, 20, 60)', 'rgb(255, 182, 193)', 'rgb(255, 160, 122)', 'rgb(250, 250, 210)', 'rgb(127, 255, 0)',
                        'rgb(173, 255, 47)', 'rgb(0, 250, 154)', 'rgb(144, 238, 144)', 'rgb(32, 178, 170)', 'rgb(0, 255, 127)',
                        'rgb(50, 205, 50)', 'rgb(255, 127, 80)', 'rgb(222, 184, 135)', 'rgb(255, 228, 196)', 'rgb(255, 218, 185)',
                        'rgb(218, 112, 214)', 'rgb(186, 85, 211)', 'rgb(148, 0, 211)', 'rgb(153, 50, 204)', 'rgb(147, 112, 219)'
                    ],
                }]
            },
            options: {
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(tooltipItem) {
                                return 'Total Staff: ' + tooltipItem.raw;
                            }
                        }
                    }
                }
            }
        });

        // Gender Distribution Chart
        var genderCtx = document.getElementById('genderChart').getContext('2d');
        var genderChart = new Chart(genderCtx, {
            type: 'pie',
            data: {
                labels: [
                    <?php
                    foreach ($gender_data as $row) {
                        echo "'" . $row['gender'] . "',";
                    }
                    ?>
                ],
                datasets: [{
                    label: 'Gender Distribution',
                    data: [
                        <?php
                        foreach ($gender_data as $row) {
                            echo $row['count'] . ",";
                        }
                        ?>
                    ],
                    backgroundColor: [
                        'rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(255, 205, 86)', 'rgb(75, 192, 192)', 'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)', 'rgb(255, 159, 64)', 'rgb(255, 99, 71)', 'rgb(144, 238, 144)', 'rgb(173, 216, 230)',
                        'rgb(250, 128, 114)', 'rgb(255, 69, 0)', 'rgb(255, 20, 147)', 'rgb(138, 43, 226)', 'rgb(139, 69, 19)',
                        'rgb(47, 79, 79)', 'rgb(112, 128, 144)', 'rgb(119, 136, 153)', 'rgb(0, 255, 255)', 'rgb(0, 128, 128)',
                        'rgb(123, 104, 238)', 'rgb(72, 61, 139)', 'rgb(106, 90, 205)', 'rgb(240, 230, 140)', 'rgb(255, 140, 0)',
                        'rgb(255, 215, 0)', 'rgb(255, 248, 220)', 'rgb(240, 255, 255)', 'rgb(70, 130, 180)', 'rgb(176, 196, 222)',
                        'rgb(220, 20, 60)', 'rgb(255, 182, 193)', 'rgb(255, 160, 122)', 'rgb(250, 250, 210)', 'rgb(127, 255, 0)',
                        'rgb(173, 255, 47)', 'rgb(0, 250, 154)', 'rgb(144, 238, 144)', 'rgb(32, 178, 170)', 'rgb(0, 255, 127)',
                        'rgb(50, 205, 50)', 'rgb(255, 127, 80)', 'rgb(222, 184, 135)', 'rgb(255, 228, 196)', 'rgb(255, 218, 185)',
                        'rgb(218, 112, 214)', 'rgb(186, 85, 211)', 'rgb(148, 0, 211)', 'rgb(153, 50, 204)', 'rgb(147, 112, 219)'
                    ], // Custom colors for pie
                }]
            }
        });

        // Role Distribution Chart
        var roleCtx = document.getElementById('roleChart').getContext('2d');
        var roleChart = new Chart(roleCtx, {
            type: 'bar',
            data: {
                labels: [
                    <?php
                    foreach ($role_data as $row) {
                        echo "'" . $row['role'] . "',";
                    }
                    ?>
                ],
                datasets: [{
                    label: 'Role Distribution',
                    data: [
                        <?php
                        foreach ($role_data as $row) {
                            echo $row['count'] . ",";
                        }
                        ?>
                    ],
                    backgroundColor: [
                        'rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(255, 205, 86)', 'rgb(75, 192, 192)', 'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)', 'rgb(255, 159, 64)', 'rgb(255, 99, 71)', 'rgb(144, 238, 144)', 'rgb(173, 216, 230)',
                        'rgb(250, 128, 114)', 'rgb(255, 69, 0)', 'rgb(255, 20, 147)', 'rgb(138, 43, 226)', 'rgb(139, 69, 19)',
                        'rgb(47, 79, 79)', 'rgb(112, 128, 144)', 'rgb(119, 136, 153)', 'rgb(0, 255, 255)', 'rgb(0, 128, 128)',
                        'rgb(123, 104, 238)', 'rgb(72, 61, 139)', 'rgb(106, 90, 205)', 'rgb(240, 230, 140)', 'rgb(255, 140, 0)',
                        'rgb(255, 215, 0)', 'rgb(255, 248, 220)', 'rgb(240, 255, 255)', 'rgb(70, 130, 180)', 'rgb(176, 196, 222)',
                        'rgb(220, 20, 60)', 'rgb(255, 182, 193)', 'rgb(255, 160, 122)', 'rgb(250, 250, 210)', 'rgb(127, 255, 0)',
                        'rgb(173, 255, 47)', 'rgb(0, 250, 154)', 'rgb(144, 238, 144)', 'rgb(32, 178, 170)', 'rgb(0, 255, 127)',
                        'rgb(50, 205, 50)', 'rgb(255, 127, 80)', 'rgb(222, 184, 135)', 'rgb(255, 228, 196)', 'rgb(255, 218, 185)',
                        'rgb(218, 112, 214)', 'rgb(186, 85, 211)', 'rgb(148, 0, 211)', 'rgb(153, 50, 204)', 'rgb(147, 112, 219)'
                    ],// Custom bar color
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        // Department Distribution Chart
        var departmentCtx = document.getElementById('departmentChart').getContext('2d');
        var departmentChart = new Chart(departmentCtx, {
            type: 'bar',
            data: {
                labels: [
                    <?php
                    foreach ($department_data as $row) {
                        echo "'" . $row['department'] . "',";
                    }
                    ?>
                ],
                datasets: [{
                    label: 'Department Distribution',
                    data: [
                        <?php
                        foreach ($department_data as $row) {
                            echo $row['count'] . ",";
                        }
                        ?>
                    ],
                    backgroundColor: [
                        'rgb(255, 99, 132)', 'rgb(54, 162, 235)', 'rgb(255, 205, 86)', 'rgb(75, 192, 192)', 'rgb(153, 102, 255)',
                        'rgb(201, 203, 207)', 'rgb(255, 159, 64)', 'rgb(255, 99, 71)', 'rgb(144, 238, 144)', 'rgb(173, 216, 230)',
                        'rgb(250, 128, 114)', 'rgb(255, 69, 0)', 'rgb(255, 20, 147)', 'rgb(138, 43, 226)', 'rgb(139, 69, 19)',
                        'rgb(47, 79, 79)', 'rgb(112, 128, 144)', 'rgb(119, 136, 153)', 'rgb(0, 255, 255)', 'rgb(0, 128, 128)',
                        'rgb(123, 104, 238)', 'rgb(72, 61, 139)', 'rgb(106, 90, 205)', 'rgb(240, 230, 140)', 'rgb(255, 140, 0)',
                        'rgb(255, 215, 0)', 'rgb(255, 248, 220)', 'rgb(240, 255, 255)', 'rgb(70, 130, 180)', 'rgb(176, 196, 222)',
                        'rgb(220, 20, 60)', 'rgb(255, 182, 193)', 'rgb(255, 160, 122)', 'rgb(250, 250, 210)', 'rgb(127, 255, 0)',
                        'rgb(173, 255, 47)', 'rgb(0, 250, 154)', 'rgb(144, 238, 144)', 'rgb(32, 178, 170)', 'rgb(0, 255, 127)',
                        'rgb(50, 205, 50)', 'rgb(255, 127, 80)', 'rgb(222, 184, 135)', 'rgb(255, 228, 196)', 'rgb(255, 218, 185)',
                        'rgb(218, 112, 214)', 'rgb(186, 85, 211)', 'rgb(148, 0, 211)', 'rgb(153, 50, 204)', 'rgb(147, 112, 219)'
                    ], // Custom bar color
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>
