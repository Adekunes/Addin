<?php
session_start();
require_once '../../../model/auth/admin_auth.php';

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
    <title>Manage Students - Hifz Management System</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../css/admin/styles.css">
    <link rel="stylesheet" href="../../../components/layouts/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
</head>
<body>
    <?php include '../../../components/layouts/sidebar.html'; ?>
    
    <div class="main-content">
        <div class="header-actions">
            <h1>Manage Students</h1>
            <button class="btn btn-primary" onclick="window.location.href='add_new_student.php'">
                <i class="fas fa-plus"></i> Add New Student
            </button>
        </div>

        <div class="table-container">
            <table id="studentsTable" class="display">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Name</th>
                        <th>Class</th>
                        <th>Guardian</th>
                        <th>Progress</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    require_once '../../../model/sql/admin_queries.php';
                    // Get and display students
                    $students = getStudents(); // You'll need to create this function
                    foreach($students as $student) {
                        echo "<tr>";
                        echo "<td>{$student['id']}</td>";
                        echo "<td>{$student['name']}</td>";
                        echo "<td>{$student['class']}</td>";
                        echo "<td>{$student['guardian']}</td>";
                        echo "<td>{$student['progress']}%</td>";
                        echo "<td class='actions'>
                                <button onclick='viewStudent({$student['id']})' class='btn-icon'><i class='fas fa-eye'></i></button>
                                <button onclick='editStudent({$student['id']})' class='btn-icon'><i class='fas fa-edit'></i></button>
                                <button onclick='deleteStudent({$student['id']})' class='btn-icon'><i class='fas fa-trash'></i></button>
                              </td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="../../../components/layouts/sidebar.js"></script>
    <script src="../../../components/js/admin_manage_students.js"></script>
</body>
</html>