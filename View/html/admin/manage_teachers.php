<?php
session_start();
require_once '../../../model/auth/admin_auth.php';
require_once '../../../model/sql/admin_db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

// Get all teachers from database
$adminDb = new AdminDatabase();
$teachers = $adminDb->getAllTeachers();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers - Dar-al-uloom</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../css/admin/styles.css">
    <link rel="stylesheet" href="../../../components/layouts/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include '../../../components/php/admin_sidebar.php'; ?>
    
    <div class="main-content">
        <div class="header">
            <div class="search-container">
                <input type="text" placeholder="Search..." class="search-input">
            </div>
            <div class="user-info">
                <div class="notifications">
                    <span class="badge">1</span>
                    <i class="fas fa-bell"></i>
                </div>
                <div class="user">
                    <img src="/assets/admin-avatar.png" alt="Admin" class="avatar">
                    <span>Admin User</span>
                </div>
            </div>
        </div>

        <div class="content-header">
            <h1>Teachers</h1>
            <p>Manage faculty members and their assignments</p>
            <button class="btn-primary" onclick="window.location.href='add_new_teacher.php'">
                <i class="fas fa-plus"></i> Add Teacher
            </button>
        </div>

        <div class="teachers-grid">
            <?php if (empty($teachers)): ?>
                <div class="no-teachers">
                    <p>No teachers found. Click the "Add Teacher" button to add your first teacher.</p>
                </div>
            <?php else: 
                foreach($teachers as $teacher): ?>
                    <div class="teacher-card">
                        <div class="teacher-header">
                            <div class="status-badge <?php echo strtolower($teacher['status']); ?>">
                                <?php echo ucfirst($teacher['status']); ?>
                            </div>
                        </div>
                        
                        <div class="teacher-info">
                            <h3><?php echo htmlspecialchars($teacher['name']); ?></h3>
                            <p class="subject"><i class="fas fa-book"></i> <?php echo htmlspecialchars($teacher['subjects']); ?></p>
                            <p class="email"><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($teacher['email']); ?></p>
                            <p class="phone"><i class="fas fa-phone"></i> <?php echo htmlspecialchars($teacher['phone']); ?></p>
                        </div>

                        <div class="teacher-actions">
                            <button class="btn-schedule" onclick="viewSchedule(<?php echo $teacher['id']; ?>)">
                                <i class="fas fa-calendar"></i> Schedule
                            </button>
                            <button class="btn-edit" onclick="editTeacher(<?php echo $teacher['id']; ?>)">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </div>
                    </div>
                <?php endforeach;
            endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../../components/layouts/sidebar.js"></script>
    <script src="../../../components/js/admin_manage_teachers.js"></script>

    <!-- Initialize sidebar -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('mainSidebar');
            if (sidebar) {
                window.sidebar = new EnhancedSidebar();
            }
        });
    </script>
</body>
</html>