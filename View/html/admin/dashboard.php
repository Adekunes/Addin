<?php
session_start();
// Update these paths to use the absolute path from the project root
require_once '../../../model/config/config.php';
require_once '../../../model/auth/admin_auth.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

function getDashboardStats() {
    $stats = array();
    
    try {
        $database = new Database();
        $db = $database->getConnection();
        
        // Get total students
        $query = "SELECT COUNT(*) as count FROM students";
        $stmt = $db->query($query);
        $stats['students'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Get total teachers
        $query = "SELECT COUNT(*) as count FROM teachers";
        $stmt = $db->query($query);
        $stats['teachers'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Get today's attendance
        $today = date('Y-m-d');
        $query = "SELECT COUNT(DISTINCT student_id) as count 
                  FROM progress 
                  WHERE DATE(date) = :today";
        $stmt = $db->prepare($query);
        $stmt->execute([':today' => $today]);
        $stats['attendance'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        // Get active classes
        $query = "SELECT COUNT(DISTINCT teacher_id) as count 
                  FROM teachers";
        $stmt = $db->query($query);
        $stats['classes'] = $stmt->fetch(PDO::FETCH_ASSOC)['count'];
        
        return $stats;
    } catch(PDOException $e) {
        error_log("Error getting dashboard stats: " . $e->getMessage());
        error_log("Database error: " . $e->getMessage());
        return array(
            'students' => 0,
            'teachers' => 0,
            'attendance' => 0,
            'classes' => 0
        );
    }
}

// Get the statistics
$stats = getDashboardStats();

// Debug output
error_log("Dashboard stats: " . print_r($stats, true));
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
    <?php include 'C:\xampp\htdocs\Source_folder_final_v2\components\php\admin_sidebar.php'; ?>
    
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