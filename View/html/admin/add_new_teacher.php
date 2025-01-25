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
    <title>Add New Teacher - Dar-al-uloom</title>
    <link rel="stylesheet" href="../../../components/layouts/sidebar.css">
    <link rel="stylesheet" href="../../css/admin/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
            background-color: white;
            font-size: 14px;
        }

        select:focus {
            outline: none;
            border-color: #3498db;
        }
    </style>
</head>
<body>
    <?php include '../../../components/php/admin_sidebar.php'; ?>

    <div class="main-content">
        <?php renderBackButton('manage_teachers.php', 'Back to Teachers'); ?>
        <h2>Add New Teacher</h2>
        <div class="container">
            <div class="form-container">
                <form id="addTeacherForm" method="POST">
                    <input type="hidden" name="action" value="add_teacher">
                    
                    <div class="form-section">
                        <h3>Personal Information</h3>
                        <div class="form-group">
                            <label for="name">Full Name</label>
                            <input type="text" id="name" name="name" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="username">Username</label>
                            <input type="text" id="username" name="username" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="phone">Phone</label>
                            <input type="tel" id="phone" name="phone" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="password">Password</label>
                            <input type="password" id="password" name="password" required>
                        </div>
                        
                        <div class="form-group">
                            <label for="subjects">Subject</label>
                            <select id="subjects" name="subjects" required>
                                <option value="hifz">Hifz</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <label for="status">Status</label>
                            <select id="status" name="status" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Add Teacher</button>
                        <button type="button" class="btn btn-secondary" onclick="history.back()">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../../../components/layouts/sidebar.js"></script>
    <script>
        document.getElementById('addTeacherForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            
            try {
                const formData = new FormData(this);
                const response = await fetch('../../../model/auth/process_teacher.php', {
                    method: 'POST',
                    body: formData
                });

                const result = await response.json();
                
                if (result.success) {
                    alert('Teacher added successfully!');
                    window.location.href = 'manage_teachers.php';
                } else {
                    alert('Error: ' + (result.message || 'Failed to add teacher'));
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while adding the teacher');
            }
        });
    </script>
</body>
</html>