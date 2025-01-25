<?php
session_start();
require_once '../../model/config/config.php';

// Check if user is logged in
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role'])) {
    header('Location: login.php');
    exit();
}

// Initialize database based on role
if ($_SESSION['role'] === 'admin') {
    require_once '../../model/sql/admin_db.php';
    $db = new AdminDatabase();
    $students = $db->getAllStudents();
} elseif ($_SESSION['role'] === 'teacher') {
    require_once '../../model/sql/teacher_db.php';
    $db = new TeacherDatabase();
    $students = $db->getTeacherStudents($_SESSION['user_id']);
} else {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Dar-al-uloom</title>
    <link rel="stylesheet" href="../../components/layouts/sidebar.css">
    <link rel="stylesheet" href="../css/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        /* ... existing styles ... */
    </style>
</head>
<body>
    <?php 
    include '../../components/layouts/sidebar.php';
    ?>
    
    <div class="main-content">
        <div class="content-header">
            <div class="title-section">
                <h1>Students Management</h1>
                <p><?php echo $_SESSION['role'] === 'admin' ? 'Manage all students' : 'View your assigned students'; ?></p>
            </div>
            <?php if ($_SESSION['role'] === 'admin'): ?>
            <button class="btn-primary" onclick="window.location.href='admin/add_new_student.php'">
                <i class="fas fa-plus"></i> Add Student
            </button>
            <?php endif; ?>
        </div>

        <div class="search-container">
            <input type="text" class="search-input" placeholder="Search students...">
        </div>

        <div class="students-grid">
            <?php if (empty($students)): ?>
                <p><?php echo $_SESSION['role'] === 'admin' ? 'No students registered.' : 'No students assigned yet.'; ?></p>
            <?php else: ?>
                <?php foreach ($students as $student): ?>
                    <div class="student-card">
                        <div class="student-info">
                            <div class="student-avatar">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="student-details">
                                <h3><?php echo htmlspecialchars($student['name']); ?></h3>
                                <p>ID: <?php echo htmlspecialchars($student['student_id']); ?></p>
                                <p>Grade: <?php echo htmlspecialchars($student['grade']); ?></p>
                            </div>
                        </div>

                        <div class="student-actions">
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                                <button class="btn-edit" onclick="editStudent(<?php echo $student['student_id']; ?>)">
                                    <i class="fas fa-edit"></i> Edit
                                </button>
                                <button class="btn-delete" onclick="deleteStudent(<?php echo $student['student_id']; ?>)">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            <?php else: ?>
                                <button class="btn-progress" onclick="updateProgress(<?php echo $student['student_id']; ?>)">
                                    <i class="fas fa-tasks"></i> Update Progress
                                </button>
                                <button class="btn-attendance" onclick="markAttendance(<?php echo $student['student_id']; ?>)">
                                    <i class="fas fa-clipboard-check"></i> Attendance
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Role-specific functions
        <?php if ($_SESSION['role'] === 'admin'): ?>
        function editStudent(studentId) {
            window.location.href = `admin/edit_student.php?id=${studentId}`;
        }

        function deleteStudent(studentId) {
            if (confirm('Are you sure you want to delete this student?')) {
                // Add delete functionality
            }
        }
        <?php else: ?>
        function updateProgress(studentId) {
            window.location.href = `teacher/progress_update.php?student_id=${studentId}`;
        }

        function markAttendance(studentId) {
            window.location.href = `teacher/attendance.php?student_id=${studentId}`;
        }
        <?php endif; ?>

        // Search functionality
        document.querySelector('.search-input').addEventListener('input', function(e) {
            const searchTerm = e.target.value.toLowerCase();
            document.querySelectorAll('.student-card').forEach(card => {
                const studentName = card.querySelector('h3').textContent.toLowerCase();
                const studentId = card.querySelector('p').textContent.toLowerCase();
                if (studentName.includes(searchTerm) || studentId.includes(searchTerm)) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    </script>
</body>
</html> 