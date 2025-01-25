<?php
session_start();

// Clear all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header('Location: /Source_folder_final_v2/View/html/login.php');
exit();
?> 