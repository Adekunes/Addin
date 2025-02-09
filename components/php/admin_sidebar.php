<link rel="stylesheet" href="/Source_folder_final_v2/components/css/admin_sidebar.css">

<div class="sidebar" id="mainSidebar" data-theme="dark">
        <div class="logo">
            <i class="fas fa-school"></i>
            Dar-al-uloom
        </div>
        <nav class="nav-menu">
            <a href="/Source_folder_final_v2/View/html/admin/dashboard.php" class="nav-item" data-page="dashboard">
                <i class="fas fa-th-large"></i>
                Dashboard
            </a>
            <a href="/Source_folder_final_v2/View/html/manage_students.php" class="nav-item" data-page="users">
                <i class="fas fa-users"></i>
                Students
            </a>
            <a href="/Source_folder_final_v2/View/html/admin/manage_teachers.php" class="nav-item" data-page="teachers">
                <i class="fas fa-chalkboard-teacher"></i>
                Teachers
            </a>
            <a href="/Source_folder_final_v2/View/html/admin/schedule.php" class="nav-item" data-page="schedule">
                <i class="fas fa-calendar"></i>
                Schedule
            </a>
            <a href="/Source_folder_final_v2/View/html/admin/attendance.php" class="nav-item" data-page="attendance">
                <i class="fas fa-check-square"></i>
                Attendance
            </a>
            <a href="/Source_folder_final_v2/View/html/admin/courses.php" class="nav-item" data-page="courses">
                <i class="fas fa-book"></i>
                Courses
            </a>
            <a href="/Source_folder_final_v2/View/html/admin/assignments.php" class="nav-item" data-page="assignments">
                <i class="fas fa-tasks"></i>
                Assignments
            </a>
            <a href="/Source_folder_final_v2/View/html/admin/reports.php" class="nav-item" data-page="reports">
                <i class="fas fa-chart-bar"></i>
                Reports
            </a>
            <a href="/Source_folder_final_v2/View/html/admin/messages.php" class="nav-item" data-page="messages">
                <i class="fas fa-envelope"></i>
                Messages
            </a>
            <a href="/Source_folder_final_v2/View/html/admin/settings.php" class="nav-item" data-page="settings">
                <i class="fas fa-cog"></i>
                Settings
            </a>
        </nav>
    </div>
    ?>

<?php
function renderBackButton($returnUrl, $text = 'Back') {
    echo '<div class="page-header">
            <button class="back-button" onclick="window.location.href=\'' . $returnUrl . '\'">
                <i class="fas fa-arrow-left"></i> ' . $text . '
            </button>
          </div>';
}
?>