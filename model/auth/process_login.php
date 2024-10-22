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
        // Prepare SQL statement to prevent SQL injection
        $stmt = $conn->prepare("SELECT id, password, role FROM users WHERE username = ? AND role = ?");
        $stmt->execute([$username, $role]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            // Start session and set session variables
            session_start();
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['role'] = $user['role'];

            // Redirect based on role
            if ($role == 'admin') {
                header("Location: ../../View/html/admin/dashboard.php");
            } else {
                header("Location: ../../View/html/teacher/dashboard.php");
            }
            exit();
        } else {
            header("Location: ../../View/html/login.php?error=Invalid username or password");
            exit();
        }
    } catch (PDOException $e) {
        header("Location: ../../View/html/login.php?error=Database error occurred");
        exit();
    }
}
?>