<?php
// Start the session
session_start();

// Check if user is logged in
// if (!isset($_SESSION['admin_id'])) {
//     header('Location: login.php');
//     exit();
// }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Dar Al-'Ulum Montréal</title>
    <link rel="stylesheet" href="../../../View/css/admin/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <button class="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>

    <?php 
    require_once('../../../components/php/admin_sidebar.php');
    ?>

    <div class="main-content">
        <div class="dashboard-overview">
            <h1>Admin Dashboard</h1>
            
            <div class="stat-cards">
                <div class="stat-card">
                    <h3>Total Students</h3>
                    <p id="totalStudents">Loading...</p>
                </div>
                <div class="stat-card">
                    <h3>Total Teachers</h3>
                    <p id="totalTeachers">Loading...</p>
                </div>
                <div class="stat-card">
                    <h3>Active Classes</h3>
                    <p id="activeClasses">Loading...</p>
                </div>
                <div class="stat-card">
                    <h3>Average Attendance</h3>
                    <p id="averageAttendance">Loading...</p>
                </div>
            </div>

            <div class="dashboard-charts">
                <div class="chart-container">
                    <h3>Student Progress</h3>
                    <canvas id="studentProgressChart"></canvas>
                </div>
                <div class="chart-container">
                    <h3>Attendance Trends</h3>
                    <canvas id="attendanceTrendChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../../../components/js/admin_dashboard.js"></script>
    <script>
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>