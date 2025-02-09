<?php
session_start();
header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Not authenticated']);
    exit();
}

require_once '../config/config.php';

try {
    $database = new Database();
    $db = $database->getConnection();
    
    $query = "SELECT u.username as name, u.role, t.name as full_name 
              FROM users u 
              LEFT JOIN teachers t ON u.id = t.user_id 
              WHERE u.id = :user_id";
              
    $stmt = $db->prepare($query);
    $stmt->bindParam(':user_id', $_SESSION['user_id']);
    $stmt->execute();
    
    $userData = $stmt->fetch(PDO::FETCH_ASSOC);
    
    echo json_encode([
        'name' => $userData['full_name'] ?? $userData['name'],
        'role' => $userData['role'],
        'avatar' => '/assets/images/default-avatar.png'
    ]);
    
} catch(PDOException $e) {
    echo json_encode(['error' => 'Database error']);
}
?> 