<?php
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    $role = $_POST['role'] ?? '';

    // Basic validation
    if (empty($username) || empty($password) || empty($role)) {
        header("Location: ../../View/html/login.php?error=All fields are required");
        exit();
    }

    try {
        // Start session at the beginning
        session_start();
        
        // Sanitize inputs
        $username = filter_var(trim($username), FILTER_SANITIZE_STRING);
        $password = trim($password);
        $role = filter_var(trim($role), FILTER_SANITIZE_STRING);
        
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, password, role, status FROM users WHERE username = ? AND role = ?");
        $stmt->execute([$username, $role]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && $user['status'] === 'active' && password_verify($password, $user['password'])) {
            // Set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $username;
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();

            // Regenerate session ID for security
            session_regenerate_id(true);

            // Redirect based on role
            if ($role === 'admin') {
                header("Location: ../../View/html/admin/dashboard.php");
            } else {
                header("Location: ../../View/html/teacher/dashboard.php");
            }
            exit();
        } else {
            header("Location: ../../View/html/login.php?error=" . urlencode("Invalid username or password"));
            exit();
        }
    } catch (PDOException $e) {
        error_log("Login error: " . $e->getMessage());
        header("Location: ../../View/html/login.php?error=" . urlencode("Database error occurred"));
        exit();
    }
}
?>