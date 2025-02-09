<?php
/**
 * Teacher Attendance Page
 * Allows teachers to:
 * - Select class and date
 * - Mark student attendance
 * - Add attendance notes
 * - Save attendance records
 * Requires teacher authentication
 */
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
    <title>Attendance - Hifz Management System</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../css/teacher/styles.css">
    <link rel="stylesheet" href="../../../components/css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <?php include '../../../components/layouts/sidebar.html'; ?>
    
    <div class="main-content">
        <h1>Attendance Management</h1>

        <div class="attendance-container">
            <div class="class-selector">
                <select id="classSelect">
                    <?php
                    // Get teacher's classes
                    require_once '../../../model/sql/teacher_queries.php';
                    $classes = getTeacherClasses($_SESSION['user_id']);
                    foreach($classes as $class) {
                        echo "<option value='{$class['id']}'>{$class['name']}</option>";
                    }
                    ?>
                </select>
                <input type="date" id="attendanceDate" value="<?php echo date('Y-m-d'); ?>">
            </div>

            <div class="attendance-form">
                <table id="attendanceTable">
                    <thead>
                        <tr>
                            <th>Student Name</th>
                            <th>Status</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody id="attendanceList">
                        <!-- Will be populated by JavaScript -->
                    </tbody>
                </table>

                <button onclick="saveAttendance()" class="btn btn-primary">
                    Save Attendance
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../../components/js/sidebar.js"></script>
    <script src="../../../model/js/teacher_attendance.js"></script>
</body>
</html>