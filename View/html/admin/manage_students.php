<?php
session_start();
require_once '../../../model/auth/admin_auth.php';
require_once '../../../model/sql/admin_db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

$adminDb = new AdminDatabase();
$students = $adminDb->getStudents();
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
            <div class="title-section">
                <h1>Users Management</h1>
                <p>Manage all users and their roles</p>
            </div>
            <button class="btn-primary" onclick="window.location.href='add_new_student.php'">
                <i class="fas fa-plus"></i> Add User
            </button>
        </div>

        <div class="users-table">
            <div class="table-header">
                <div class="search-filter">
                    <div class="search-box">
                        <i class="fas fa-search"></i>
                        <input type="text" placeholder="Search users..." class="search-users">
                    </div>
                    <button class="btn-filter">
                        <i class="fas fa-filter"></i> Filter
                    </button>
                </div>
            </div>

            <table class="data-table">
                <thead>
                    <tr>
                        <th>NAME</th>
                        <th>ROLE</th>
                        <th>GRADE</th>
                        <th>CURRENT JUZ</th>
                        <th>COMPLETED</th>
                        <th>STATUS</th>
                        <th>ACTIONS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($students as $student): ?>
                        <tr>
                            <td class="name-cell">
                                <div class="user-icon">
                                    <i class="fas fa-user"></i>
                                </div>
                                <div class="user-details">
                                    <div class="user-name"><?php echo htmlspecialchars($student['name']); ?></div>
                                    <div class="user-email"><?php echo htmlspecialchars($student['guardian_contact']); ?></div>
                                </div>
                            </td>
                            <td>Student</td>
                            <td><?php echo htmlspecialchars($student['class_name'] ?? 'Not Assigned'); ?></td>
                            <td>
                                <div class="juz-progress">
                                    <div class="juz-label"><?php echo htmlspecialchars($student['current_juz']); ?></div>
                                </div>
                            </td>
                            <td>
                                <div class="completion-progress">
                                    <div class="progress-bar">
                                        <div class="progress" style="width: <?php echo ($student['completed_juz'] / 30) * 100; ?>%"></div>
                                    </div>
                                    <span class="completion-text"><?php echo htmlspecialchars($student['completed_juz']); ?>/30</span>
                                </div>
                            </td>
                            <td>
                                <span class="status-badge <?php echo strtolower($student['status']); ?>">
                                    <?php echo ucfirst($student['status']); ?>
                                </span>
                            </td>
                            <td class="actions">
                                <button class="btn-icon edit" onclick="openEditModal(<?php echo $student['id']; ?>)">
                                    <i class="fas fa-pen"></i>
                                </button>
                                <button class="btn-icon delete" onclick="deleteStudent(<?php echo $student['id']; ?>)">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <button class="btn-icon more">
                                    <i class="fas fa-ellipsis-v"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../../components/layouts/sidebar.js"></script>
    <script src="../../../components/js/admin_manage_students.js"></script>

    <div id="editStudentModal" class="modal">
        <div class="modal-content">
            <div class="modal-header">
                <h2>Edit Student Progress</h2>
                <span class="close">&times;</span>
            </div>
            <div class="modal-body">
                <form id="editStudentForm">
                    <input type="hidden" id="studentId" name="studentId">
                    
                    <div class="form-group">
                        <label for="studentName">Student Name</label>
                        <input type="text" id="studentName" name="name" readonly>
                    </div>

                    <div class="form-group">
                        <label for="currentJuz">Current Juz</label>
                        <select id="currentJuz" name="current_juz" required>
                            <?php for($i = 0; $i <= 30; $i++): ?>
                                <option value="<?php echo $i; ?>">Juz <?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="completedJuz">Completed Juz</label>
                        <select id="completedJuz" name="completed_juz" required>
                            <?php for($i = 0; $i <= 30; $i++): ?>
                                <option value="<?php echo $i; ?>">Juz <?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="status">Status</label>
                        <select id="status" name="status" required>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn-primary">Save Changes</button>
                        <button type="button" class="btn-secondary" onclick="closeModal()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>