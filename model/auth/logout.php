<?php
session_start();

// Debug log
error_log("Logout initiated for user: " . ($_SESSION['user_id'] ?? 'unknown'));

try {
    // Clear all session variables
    $_SESSION = array();

    // Destroy the session cookie
    if (isset($_COOKIE[session_name()])) {
        setcookie(session_name(), '', time()-3600, '/');
    }

    // Destroy the session
    session_destroy();

    // Debug log
    error_log("Session destroyed successfully");

    // Redirect to login page
    header('Location: ../../View/html/login.php');
    exit();
} catch (Exception $e) {
    error_log("Error during logout: " . $e->getMessage());
    // Still try to redirect even if there's an error
    header('Location: ../../View/html/login.php');
    exit();
}
?> 