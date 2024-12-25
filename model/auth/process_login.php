<?php
session_start();
require_once '../config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $role = $_POST['role'];

    $database = new Database();
    $db = $database->getConnection();

    try {
        // Prepare the SQL query
        $query = "SELECT id, username, password, role FROM users 
                 WHERE username = :username AND role = :role";
        $stmt = $db->prepare($query);
        
        // Bind parameters
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":role", $role);
        
        // Execute the query
        $stmt->execute();
        
        if ($stmt->rowCount() == 1) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (password_verify($password, $row['password'])) {
                // Login successful
                $_SESSION['user_id'] = $row['id'];
                $_SESSION['username'] = $row['username'];
                $_SESSION['role'] = $row['role'];
                
                // Redirect based on role
                if ($role == 'admin') {
                    header("Location: ../Source_folder_final_v2/View/html/admin/dashboard.php");
                } else {
                    header("Location: ../Source_folder_final_v2/View/html/teacher/dashboard.php");
                }
                exit();
            } else {
                header("Location: ../../View/html/login.php?error=Invalid password");
            }
        } else {
            header("Location: ../../View/html/login.php?error=User not found");
        }
    } catch(PDOException $e) {
        header("Location: ../../View/html/login.php?error=Database error");
    }
} else {
    header("Location: ../../View/html/login.php");
}
exit();
?>