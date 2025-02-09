<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');  // Add CORS header if needed
session_start();

require_once '../sql/admin_db.php';

try {
    // Check if user is logged in and is admin
    if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
        error_log("Unauthorized access attempt. Session data: " . print_r($_SESSION, true));
        throw new Exception('Unauthorized access');
    }

    $adminDb = new AdminDatabase();
    $result = $adminDb->getDashboardStats();
    
    error_log("Dashboard stats result: " . print_r($result, true));

    if ($result['success']) {
        echo json_encode([
            'success' => true,
            'data' => $result['data']
        ]);
    } else {
        throw new Exception($result['message']);
    }

} catch (Exception $e) {
    error_log("Error in process_dashboard.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 