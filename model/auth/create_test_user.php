<?php
require_once '../config/database.php';

$database = new Database();
$db = $database->getConnection();

// Test user credentials
$username = "admin";
$password = password_hash("admin123", PASSWORD_DEFAULT);
$email = "admin@example.com";
$role = "admin";

try {
    $query = "INSERT INTO users (username, password, email, role) 
              VALUES (:username, :password, :email, :role)";
    $stmt = $db->prepare($query);
    
    $stmt->bindParam(":username", $username);
    $stmt->bindParam(":password", $password);
    $stmt->bindParam(":email", $email);
    $stmt->bindParam(":role", $role);
    
    $stmt->execute();
    echo "Test user created successfully!";
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 