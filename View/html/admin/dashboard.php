<?php
session_start();
// Update these paths to use the absolute path from the project root
require_once '../../../model/config/config.php';
require_once '../../../model/auth/admin_auth.php';
require_once '../../../model/sql/admin_db.php';

// Add debugging
error_log("Dashboard loaded - Starting debug");

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

$adminDb = new AdminDatabase();
$result = $adminDb->getDashboardStats();

// Initialize stats with default values
$stats = [
    'students' => 0,
    'teachers' => 0,
    'attendance' => 0,
    'classes' => 0
];

// Only update stats if the query was successful
if ($result['success'] && isset($result['data'])) {
    $stats = $result['data'];
    error_log("Dashboard stats retrieved successfully: " . print_r($stats, true));
} else {
    error_log("Error retrieving dashboard stats: " . ($result['message'] ?? 'Unknown error'));
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
            transition: transform 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #2ecc71;
            margin: 10px 0;
            transition: opacity 0.3s ease;
        }
        .stat-number.updated {
            animation: pulse 1s ease;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
    </style>
</head>
<body>
    <?php include '../../../components/php/admin_sidebar.php'; ?>
    
    <div class="main-content">
        <h1>Admin Dashboard</h1>
        
        <div class="dashboard-stats">
            <div class="stat-card">
                <h3>Total Students</h3>
                <div class="stat-number" id="totalStudents"><?php echo $stats['students']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Total Teachers</h3>
                <div class="stat-number" id="totalTeachers"><?php echo $stats['teachers']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Today's Attendance</h3>
                <div class="stat-number" id="todayAttendance"><?php echo $stats['attendance']; ?></div>
            </div>
            <div class="stat-card">
                <h3>Active Classes</h3>
                <div class="stat-number" id="activeClasses"><?php echo $stats['classes']; ?></div>
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