<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<div class="sidebar" id="teacherSidebar">
    <div class="sidebar-header">
        <img src="../../../assets/images/logo.png" alt="Dar Al-'Ulum Montréal Logo" class="sidebar-logo">
        <h3>Teacher Panel</h3>
    </div>
    
    <nav class="sidebar-nav">
        <ul>
            <li>
                <a href="dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                    <i class="fas fa-tachometer-alt"></i>
                    Dashboard
                </a>
            </li>
            <li>
                <a href="attendance.php" class="<?php echo ($current_page == 'attendance.php') ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-check"></i>
                    Attendance
                </a>
            </li>
            <li>
                <a href="progress_update.php" class="<?php echo ($current_page == 'progress_update.php') ? 'active' : ''; ?>">
                    <i class="fas fa-tasks"></i>
                    Progress Update
                </a>
            </li>
        </ul>
    </nav>

    <div class="sidebar-footer">
        <a href="#" id="logout">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </a>
    </div>
</div>