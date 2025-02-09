<?php
require_once __DIR__ . '/../config/config.php';

$database = new Database();
$db = $database->getConnection();

// Teacher information
$teacherData = [
    'username' => 'teacher1',
    'password' => 'teacher123',
    'email' => 'teacher1@daralulum.com',
    'name' => 'John Doe',
    'phone' => '514-123-4567',
    'subjects' => 'Quran, Tajweed, Islamic Studies'
];

try {
    // Begin transaction
    $db->beginTransaction();

    // Insert into users table
    $userQuery = "INSERT INTO users (username, password, email, role) 
                  VALUES (:username, :password, :email, 'teacher')";
    $userStmt = $db->prepare($userQuery);
    
    $hashedPassword = password_hash($teacherData['password'], PASSWORD_DEFAULT);
    
    $userStmt->bindParam(':username', $teacherData['username']);
    $userStmt->bindParam(':password', $hashedPassword);
    $userStmt->bindParam(':email', $teacherData['email']);
    $userStmt->execute();
    
    $userId = $db->lastInsertId();

    // Insert into teachers table
    $teacherQuery = "INSERT INTO teachers (user_id, name, phone, subjects, status) 
                     VALUES (:user_id, :name, :phone, :subjects, 'active')";
    $teacherStmt = $db->prepare($teacherQuery);
    
    $teacherStmt->bindParam(':user_id', $userId);
    $teacherStmt->bindParam(':name', $teacherData['name']);
    $teacherStmt->bindParam(':phone', $teacherData['phone']);
    $teacherStmt->bindParam(':subjects', $teacherData['subjects']);
    $teacherStmt->execute();

    // Commit transaction
    $db->commit();
    
    echo "Teacher created successfully!\n";
    echo "Username: " . $teacherData['username'] . "\n";
    echo "Password: " . $teacherData['password'] . "\n";
    echo "Please make sure to save these credentials.\n";

} catch(PDOException $e) {
    // Rollback transaction on error
    $db->rollBack();
    echo "Error: " . $e->getMessage();
}
?> 