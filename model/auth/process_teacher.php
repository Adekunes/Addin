<?php
session_start();
require_once '../config/config.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../View/html/login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $database = new Database();
    $db = $database->getConnection();
    
    try {
        // Begin transaction
        $db->beginTransaction();
        
        // Insert into users table first
        $userQuery = "INSERT INTO users (username, password, email, role) 
                     VALUES (:username, :password, :email, 'teacher')";
        $userStmt = $db->prepare($userQuery);
        
        $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
        
        $userStmt->bindParam(':username', $_POST['username']);
        $userStmt->bindParam(':password', $hashedPassword);
        $userStmt->bindParam(':email', $_POST['email']);
        $userStmt->execute();
        
        $userId = $db->lastInsertId();
        
        // Insert into teachers table
        $teacherQuery = "INSERT INTO teachers (user_id, name, phone, subjects, status) 
                        VALUES (:user_id, :name, :phone, :subjects, :status)";
        $teacherStmt = $db->prepare($teacherQuery);
        
        $teacherStmt->bindParam(':user_id', $userId);
        $teacherStmt->bindParam(':name', $_POST['name']);
        $teacherStmt->bindParam(':phone', $_POST['phone']);
        $teacherStmt->bindParam(':subjects', $_POST['subjects']);
        $teacherStmt->bindParam(':status', $_POST['status']);
        $teacherStmt->execute();
        
        // Commit transaction
        $db->commit();
        
        $_SESSION['success_message'] = "Teacher added successfully!";
        header('Location: ../../View/html/admin/manage_teachers.php');
        exit();
        
    } catch(PDOException $e) {
        // Rollback transaction on error
        $db->rollBack();
        $_SESSION['error_message'] = "Error adding teacher: " . $e->getMessage();
        header('Location: ../../View/html/admin/add_new_teacher.php');
        exit();
    }
}
?> 