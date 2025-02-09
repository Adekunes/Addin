<?php
require_once '../config/config.php';

$database = new Database();
$db = $database->getConnection();

// Admin credentials - you can modify these
$adminData = [
    'username' => 'admin',
    'password' => 'admin123',  // This is the password you'll use to login
    'email' => 'admin@daralulum.com',
    'role' => 'admin'
];

try {
    // Check if admin already exists
    $checkQuery = "SELECT id FROM users WHERE username = :username AND role = 'admin'";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->bindParam(':username', $adminData['username']);
    $checkStmt->execute();

    if ($checkStmt->rowCount() > 0) {
        echo "Admin user already exists!\n";
        exit();
    }

    // Insert new admin
    $query = "INSERT INTO users (username, password, email, role) 
              VALUES (:username, :password, :email, :role)";
    $stmt = $db->prepare($query);
    
    // Hash the password
    $hashedPassword = password_hash($adminData['password'], PASSWORD_DEFAULT);
    
    // Bind parameters
    $stmt->bindParam(':username', $adminData['username']);
    $stmt->bindParam(':password', $hashedPassword);
    $stmt->bindParam(':email', $adminData['email']);
    $stmt->bindParam(':role', $adminData['role']);
    
    // Execute the query
    $stmt->execute();
    
    echo "Admin created successfully!\n";
    echo "Username: " . $adminData['username'] . "\n";
    echo "Password: " . $adminData['password'] . "\n";
    echo "Please save these credentials.\n";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?> 