<?php
session_start();

// Unset all session variables
$_SESSION = array();

// Destroy the session
session_destroy();

// Redirect to login page
header("Location: ../../View/html/login.php?message=Successfully logged out");
exit();
?> 