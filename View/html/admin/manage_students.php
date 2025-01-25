<?php
session_start();
require_once '../../../model/auth/admin_auth.php';
require_once '../../../model/sql/admin_db.php';

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

// Initialize database connection
$adminDb = new AdminDatabase();
$students = $adminDb->getAllStudents();

// Debug output
echo "<!-- Debug Information -->";
echo "<!-- Number of students found: " . count($students) . " -->";
echo "<!-- Student data: " . print_r($students, true) . " -->";

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Students - Dar-al-uloom</title>
    <link rel="stylesheet" href="../../../components/layouts/sidebar.css">
    <link rel="stylesheet" href="../../css/admin/styles.css">
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
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
    <?php include '../../../components/php/admin_sidebar.php'; ?>
    
    <div class="main-content">
        <h1>Students Management</h1>
        <button class="btn-primary" onclick="window.location.href='add_new_student.php'">
            <i class="fas fa-plus"></i> Add Student
        </button>

        <div class="search-container">
            <input type="text" id="studentSearch" class="search-input" placeholder="Search students...">
        </div>

        <div class="students-grid">
            <?php if (empty($students)): ?>
                <p>No students found</p>
            <?php else: ?>
                <?php foreach ($students as $student): ?>
                    <div class="student-card">
                        <h3><?php echo htmlspecialchars($student['name']); ?></h3>
                        <p>ID: <?php echo htmlspecialchars($student['id']); ?></p>
                        <p>Current Juz: <?php echo htmlspecialchars($student['current_juz'] ?? 'Not set'); ?></p>
                        <p>Guardian: <?php echo htmlspecialchars($student['guardian_name']); ?></p>
                        <p>Contact: <?php echo htmlspecialchars($student['guardian_contact']); ?></p>
                        <div class="student-actions">
                            <button onclick="editStudent(<?php echo $student['id']; ?>)" class="btn-edit">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button onclick="deleteStudent(<?php echo $student['id']; ?>)" class="btn-delete">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        function editStudent(studentId) {
            window.location.href = `edit_student.php?id=${studentId}`;
        }

        function deleteStudent(studentId) {
            if (confirm('Are you sure you want to delete this student?')) {
                // Add delete functionality
            }
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