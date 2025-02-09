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

error_log("Teachers data: " . print_r($teachers, true));

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Teachers - Dar-al-uloom</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../css/admin/styles.css">
    <link rel="stylesheet" href="../../../components/css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        .main-content {
            padding: 2rem;
            margin-left: 250px;
        }

        .teachers-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .teacher-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            transition: transform 0.2s;
        }

        .teacher-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.15);
        }

        .teacher-info {
            margin-bottom: 1rem;
        }

        .teacher-name {
            font-size: 1.2rem;
            font-weight: 600;
            color: #2c3e50;
            margin-bottom: 0.5rem;
        }

        .teacher-details {
            color: #666;
            font-size: 0.9rem;
            margin-bottom: 0.5rem;
        }

        .teacher-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .btn-edit, .btn-schedule {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: background-color 0.2s;
        }

        .btn-edit {
            background-color: #3498db;
            color: white;
        }

        .btn-schedule {
            background-color: #2ecc71;
            color: white;
        }

        .btn-edit:hover {
            background-color: #2980b9;
        }

        .btn-schedule:hover {
            background-color: #27ae60;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            background-color: white;
            margin: 10% auto;
            padding: 2rem;
            border-radius: 8px;
            width: 90%;
            max-width: 600px;
            position: relative;
        }

        .close {
            position: absolute;
            right: 1rem;
            top: 1rem;
            font-size: 1.5rem;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <?php include '../../../components/php/sidebar.php'; ?>
    
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
            <?php 
            if ($teachers && is_array($teachers) && count($teachers) > 0): 
                foreach($teachers as $teacher): 
            ?>
                <div class="teacher-card">
                    <div class="teacher-header">
                        <div class="status-badge <?php echo strtolower($teacher['status'] ?? 'inactive'); ?>">
                            <?php echo ucfirst($teacher['status'] ?? 'Inactive'); ?>
                        </div>
                    </div>
                    
                    <div class="teacher-info">
                        <h3><?php echo htmlspecialchars($teacher['name'] ?? 'Unknown'); ?></h3>
                        <p class="subject">
                            <i class="fas fa-book"></i> 
                            <?php echo htmlspecialchars($teacher['subjects'] ?? 'Not specified'); ?>
                        </p>
                        <p class="email">
                            <i class="fas fa-envelope"></i> 
                            <?php echo htmlspecialchars($teacher['email'] ?? 'No email'); ?>
                        </p>
                        <p class="phone">
                            <i class="fas fa-phone"></i> 
                            <?php echo htmlspecialchars($teacher['phone'] ?? 'No phone'); ?>
                        </p>
                    </div>

                    <div class="teacher-actions">
                        <button class="btn-schedule" onclick="viewSchedule(<?php echo $teacher['id']; ?>)">
                            <i class="fas fa-calendar"></i> Schedule
                        </button>
                        <button class="btn-edit" onclick="editTeacher('<?php echo $teacher['teacher_id'] ?? $teacher['id']; ?>')">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    </div>
                </div>
            <?php 
                endforeach;
            else: 
            ?>
                <div class="no-teachers">
                    <p>No teachers found. Click the "Add Teacher" button to add your first teacher.</p>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../../components/js/sidebar.js"></script>
    <script src="../../../model/js/admin_manage_teachers.js"></script>

    <!-- Initialize sidebar -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('mainSidebar');
            if (sidebar) {
                window.sidebar = new EnhancedSidebar();
            }
        });
    </script>

    <!-- Edit Teacher Modal -->
    <div id="editTeacherModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Edit Teacher</h2>
            <form id="editTeacherForm">
                <input type="hidden" id="teacherId" name="teacher_id">
                <input type="hidden" name="action" value="update_teacher">
                
                <div class="form-group">
                    <label for="teacherName">Name</label>
                    <input type="text" id="teacherName" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="teacherPhone">Phone</label>
                    <input type="tel" id="teacherPhone" name="phone" required>
                </div>
                
                <div class="form-group">
                    <label for="teacherEmail">Email</label>
                    <input type="email" id="teacherEmail" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="teacherSubjects">Subjects</label>
                    <input type="text" id="teacherSubjects" name="subjects">
                </div>
                
                <div class="form-group">
                    <label for="teacherStatus">Status</label>
                    <select id="teacherStatus" name="status" required>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Update Teacher</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>