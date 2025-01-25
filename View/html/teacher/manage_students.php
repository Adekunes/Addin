<?php
session_start();
require_once '../../../model/auth/teacher_auth.php';
require_once '../../../model/sql/teacher_db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'teacher') {
    header('Location: ../../login.php');
    exit();
}

// Initialize database connection
$teacherDb = new TeacherDatabase();
$students = $teacherDb->getTeacherStudents($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Dar-al-uloom</title>
    <link rel="stylesheet" href="../../../components/layouts/sidebar.css">
    <link rel="stylesheet" href="../../css/teacher/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    
    <style>
        .main-content {
            padding: 2rem;
            margin-left: 250px;
        }

        .students-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .student-card {
            background: white;
            border-radius: 8px;
            padding: 1.5rem;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .student-info {
            display: flex;
            align-items: center;
            margin-bottom: 1rem;
        }

        .student-avatar {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            margin-right: 1rem;
            background-color: #e2e8f0;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .student-details h3 {
            margin: 0;
            color: #2d3748;
        }

        .student-details p {
            margin: 0.25rem 0;
            color: #718096;
        }

        .student-actions {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .btn-progress, .btn-attendance {
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: background-color 0.3s;
        }

        .btn-progress {
            background-color: #4299e1;
            color: white;
        }

        .btn-attendance {
            background-color: #48bb78;
            color: white;
        }

        .btn-progress:hover {
            background-color: #3182ce;
        }

        .btn-attendance:hover {
            background-color: #38a169;
        }

        .search-container {
            margin-bottom: 2rem;
        }

        .search-input {
            padding: 0.5rem 1rem;
            border: 1px solid #e2e8f0;
            border-radius: 4px;
            width: 300px;
        }
    </style>
</head>
<body>
    <?php include '../../../components/php/teacher_sidebar.php'; ?>
    
    <div class="main-content">
        <div class="content-header">
            <h1>My Students</h1>
            <p>Manage your assigned students and their progress</p>
        </div>

        <div class="search-container">
            <input type="text" class="search-input" placeholder="Search students...">
        </div>

        <div class="students-grid">
            <?php if (empty($students)): ?>
                <p>No students assigned yet.</p>
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
                            <button class="btn-progress" onclick="updateProgress(<?php echo $student['student_id']; ?>)">
                                <i class="fas fa-tasks"></i> Update Progress
                            </button>
                            <button class="btn-attendance" onclick="markAttendance(<?php echo $student['student_id']; ?>)">
                                <i class="fas fa-clipboard-check"></i> Attendance
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../../components/layouts/sidebar.js"></script>
    <script>
        function updateProgress(studentId) {
            window.location.href = `progress_update.php?student_id=${studentId}`;
        }

        function markAttendance(studentId) {
            window.location.href = `attendance.php?student_id=${studentId}`;
        }

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