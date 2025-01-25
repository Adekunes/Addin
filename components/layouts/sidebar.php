<?php
$current_page = basename($_SERVER['PHP_SELF']);

// Determine the base path for links based on current page depth
$depth = substr_count($_SERVER['PHP_SELF'], '/') - substr_count('/Source_folder_final_v2/', '/');
$base_path = str_repeat('../', $depth) . 'Source_folder_final_v2';

if ($_SESSION['role'] === 'admin'): ?>
    <div class="sidebar" id="mainSidebar" data-theme="dark">
        <div class="logo">
            <i class="fas fa-school"></i>
            Dar-al-uloom
        </div>
        <nav class="nav-menu">
            <a href="<?php echo $base_path; ?>/View/html/admin/dashboard.php" class="nav-item <?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                <i class="fas fa-th-large"></i>
                Dashboard
            </a>
            <a href="<?php echo $base_path; ?>/View/html/manage_students.php" class="nav-item <?php echo ($current_page == 'manage_students.php') ? 'active' : ''; ?>">
                <i class="fas fa-users"></i>
                Students
            </a>
            <a href="<?php echo $base_path; ?>/View/html/admin/manage_teachers.php" class="nav-item <?php echo ($current_page == 'manage_teachers.php') ? 'active' : ''; ?>">
                <i class="fas fa-chalkboard-teacher"></i>
                Teachers
            </a>
            <a href="<?php echo $base_path; ?>/View/html/admin/schedule.php" class="nav-item <?php echo ($current_page == 'schedule.php') ? 'active' : ''; ?>">
                <i class="fas fa-calendar"></i>
                Schedule
            </a>
            <a href="<?php echo $base_path; ?>/View/html/admin/attendance.php" class="nav-item <?php echo ($current_page == 'attendance.php') ? 'active' : ''; ?>">
                <i class="fas fa-check-square"></i>
                Attendance
            </a>
            <a href="<?php echo $base_path; ?>/View/html/admin/courses.php" class="nav-item <?php echo ($current_page == 'courses.php') ? 'active' : ''; ?>">
                <i class="fas fa-book"></i>
                Courses
            </a>
            <a href="<?php echo $base_path; ?>/View/html/admin/assignments.php" class="nav-item <?php echo ($current_page == 'assignments.php') ? 'active' : ''; ?>">
                <i class="fas fa-tasks"></i>
                Assignments
            </a>
            <a href="<?php echo $base_path; ?>/View/html/admin/reports.php" class="nav-item <?php echo ($current_page == 'reports.php') ? 'active' : ''; ?>">
                <i class="fas fa-chart-bar"></i>
                Reports
            </a>
            <a href="<?php echo $base_path; ?>/View/html/admin/messages.php" class="nav-item <?php echo ($current_page == 'messages.php') ? 'active' : ''; ?>">
                <i class="fas fa-envelope"></i>
                Messages
            </a>
            <a href="<?php echo $base_path; ?>/View/html/admin/settings.php" class="nav-item <?php echo ($current_page == 'settings.php') ? 'active' : ''; ?>">
                <i class="fas fa-cog"></i>
                Settings
            </a>
            <div class="logout-item">
                <a href="<?php echo $base_path; ?>/model/auth/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </a>
            </div>
        </nav>
    </div>
<?php else: ?>
    <div class="sidebar" id="teacherSidebar">
        <div class="sidebar-header">
            <img src="<?php echo $base_path; ?>/assets/images/logo.png" alt="Dar Al-'Ulum Montréal Logo" class="sidebar-logo">
            <h3>Teacher Panel</h3>
        </div>
        
        <nav class="sidebar-nav">
            <ul>
                <li>
                    <a href="<?php echo $base_path; ?>/View/html/teacher/dashboard.php" class="<?php echo ($current_page == 'dashboard.php') ? 'active' : ''; ?>">
                        <i class="fas fa-tachometer-alt"></i>
                        Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_path; ?>/View/html/manage_students.php" class="<?php echo ($current_page == 'manage_students.php') ? 'active' : ''; ?>">
                        <i class="fas fa-users"></i>
                        My Students
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_path; ?>/View/html/teacher/attendance.php" class="<?php echo ($current_page == 'attendance.php') ? 'active' : ''; ?>">
                        <i class="fas fa-clipboard-check"></i>
                        Attendance
                    </a>
                </li>
                <li>
                    <a href="<?php echo $base_path; ?>/View/html/teacher/progress_update.php" class="<?php echo ($current_page == 'progress_update.php') ? 'active' : ''; ?>">
                        <i class="fas fa-tasks"></i>
                        Progress Update
                    </a>
                </li>
            </ul>
        </nav>

        <div class="sidebar-footer">
            <a href="<?php echo $base_path; ?>/model/auth/logout.php" class="logout-btn">
                <i class="fas fa-sign-out-alt"></i>
                Logout
            </a>
        </div>
    </div>
<?php endif; ?> 