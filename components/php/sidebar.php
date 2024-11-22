<?php
if (!isset($_SESSION)) {
    session_start();
}
?>

<div class="sidebar">
    <div class="logo-container">
        <img src="../../../assets/images/logo.png" alt="Dar Al-'Ulum Montréal Logo" class="logo">
    </div>
    <nav>
        <ul>
            <?php if ($_SESSION['role'] === 'teacher'): ?>
                <li><a href="../teacher/dashboard.php"><i class="fas fa-home"></i> Dashboard</a></li>
                <li><a href="../teacher/students.php"><i class="fas fa-user-graduate"></i> Students</a></li>
                <li><a href="../teacher/attendance.php"><i class="fas fa-clipboard-list"></i> Attendance</a></li>
                <li><a href="../teacher/progress.php"><i class="fas fa-chart-line"></i> Progress</a></li>
                <li><a href="../teacher/classes.php"><i class="fas fa-chalkboard-teacher"></i> Classes</a></li>
            <?php endif; ?>
        </ul>
    </nav>
    
    <div class="sidebar-footer">
        <div class="user-info">
            <i class="fas fa-user-circle"></i>
            <span><?php echo htmlspecialchars($_SESSION['username']); ?></span>
        </div>
        <a href="../../../model/auth/logout.php" class="logout-btn">
            <i class="fas fa-sign-out-alt"></i> Logout
        </a>
    </div>
</div>
