<?php
session_start();
require_once '../../../model/auth/teacher_auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: ../../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Dashboard - Hifz Management System</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../css/admin/styles.css">
    <link rel="stylesheet" href="../../../components/layouts/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include '../../../components/php/sidebar.php'; ?>
    
    <div class="main-content">
        <h1>Teacher Dashboard</h1>
        
        <div class="overview-cards">
            <div class="card">
                <h3>Today's Classes</h3> 
                <div id="todayClasses">Loading...</div>
            </div>
            <div class="card">
                <h3>Total Students</h3>
                <div id="totalStudents">Loading...</div>
            </div>
            <div class="card">
                <h3>Pending Assessments</h3>
                <div id="pendingAssessments">Loading...</div>
            </div>
        </div>

        <div class="today-schedule">
            <h2>Today's Schedule</h2>
            <div id="scheduleTable"></div>
        </div>

        <div class="recent-activities">
            <h2>Recent Activities</h2>
            <div id="activitiesList"></div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../../components/layouts/sidebar.js"></script>
    <script src="../../../components/js/teacher_dashboard.js"></script>
</body>
</html>