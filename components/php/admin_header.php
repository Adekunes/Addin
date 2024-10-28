<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hifz Management System - Admin</title>
    <!-- Common CSS -->
    <link rel="stylesheet" href="../../css/admin/styles.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<header class="admin-header">
    <div class="logo">
        <img src="../../assets/images/logo.png" alt="Hifz Management System">
    </div>
    <div class="user-info">
        <span>Welcome, <?php echo $_SESSION['username']; ?></span>
        <a href="../../../model/auth/admin_auth.php?action=logout" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</header>