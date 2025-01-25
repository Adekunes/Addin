<?php
header('Content-Type: application/json');
session_start();

require_once '../sql/admin_db.php';

try {
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        throw new Exception('Unauthorized access');
    }

    $adminDb = new AdminDatabase();
    $stats = $adminDb->getDashboardStats();
    
    echo json_encode([
        'success' => true,
        'data' => $stats
    ]);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 