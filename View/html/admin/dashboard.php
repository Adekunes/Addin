<?php
session_start();
require_once '../../../model/auth/admin_auth.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Hifz Management System</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../css/admin/styles.css">
    <link rel="stylesheet" href="../../../components/layouts/admin_sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <!-- Dashboard specific CSS -->
    <style>
        .dashboard-stats {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin: 20px 0;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <?php include 'C:\xampp\htdocs\Source_folder_final_v2\components\php\admin_sidebar.php'; ?>
    
    <div class="main-content">
        <h1>Admin Dashboard</h1>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total Students</h3>
                <div class="stat-number" id="totalStudents">Loading...</div>
            </div>
            <div class="stat-card">
                <h3>Total Teachers</h3>
                <div class="stat-number" id="totalTeachers">Loading...</div>
            </div>
            <div class="stat-card">
                <h3>Today's Attendance</h3>
                <div class="stat-number" id="todayAttendance">Loading...</div>
            </div>
            <div class="stat-card">
                <h3>Active Classes</h3>
                <div class="stat-number" id="activeClasses">Loading...</div>
            </div>
        </div>

        <div class="recent-activities">
            <h2>Recent Activities</h2>
            <div id="activitiesList"></div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="../../../components/layouts/sidebar.js"></script>
    <script src="../../../components/js/admin_dashboard.js"></script>
</body>
</html>