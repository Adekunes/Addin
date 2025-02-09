<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header('Location: /Source_folder_final_v2/View/login.php');
    exit();
}
?>

<link rel="stylesheet" href="/Source_folder_final_v2/components/layouts/admin_sidebar.css">

<!-- Add Burger Menu Button -->
<div class="burger-menu">
    <div class="burger-icon">
        <span></span>
        <span></span>
        <span></span>
    </div>
</div>

<div class="sidebar" id="mainSidebar" data-theme="dark">
    <div class="logo">
        <i class="fas fa-school"></i>
        Dar-al-uloom
    </div>
    <nav class="nav-menu">
        <?php
        // Determine the base path and role-specific folder
        $basePath = '/Source_folder_final_v2/View/html/';
        $roleFolder = ($_SESSION['role'] === 'teacher') ? 'teacher' : 'admin';
        ?>
        
        <a href="<?php echo $basePath . $roleFolder; ?>/dashboard.php" class="nav-item" data-page="dashboard">
            <i class="fas fa-th-large"></i>
            Dashboard
        </a>
        <a href="<?php echo $basePath; ?>manage_students.php" class="nav-item" data-page="users">
            <i class="fas fa-users"></i>
            Students
        </a>
        <?php if ($_SESSION['role'] === 'admin'): ?>
            <a href="<?php echo $basePath . $roleFolder; ?>/manage_teachers.php" class="nav-item" data-page="teachers">
                <i class="fas fa-chalkboard-teacher"></i>
                Teachers
            </a>
        <?php endif; ?>
        <a href="<?php echo $basePath . $roleFolder; ?>/schedule.php" class="nav-item" data-page="schedule">
            <i class="fas fa-calendar"></i>
            Schedule
        </a>
        <a href="<?php echo $basePath . $roleFolder; ?>/attendance.php" class="nav-item" data-page="attendance">
            <i class="fas fa-check-square"></i>
            Attendance
        </a>
        <a href="<?php echo $basePath . $roleFolder; ?>/courses.php" class="nav-item" data-page="courses">
            <i class="fas fa-book"></i>
            Courses
        </a>
        <a href="<?php echo $basePath . $roleFolder; ?>/assignments.php" class="nav-item" data-page="assignments">
            <i class="fas fa-tasks"></i>
            Assignments
        </a>
        <a href="<?php echo $basePath . $roleFolder; ?>/reports.php" class="nav-item" data-page="reports">
            <i class="fas fa-chart-bar"></i>
            Reports
        </a>
        <a href="<?php echo $basePath . $roleFolder; ?>/messages.php" class="nav-item" data-page="messages">
            <i class="fas fa-envelope"></i>
            Messages
        </a>
        <a href="<?php echo $basePath . $roleFolder; ?>/settings.php" class="nav-item" data-page="settings">
            <i class="fas fa-cog"></i>
            Settings
        </a>

        <!-- Updated Logout Button with absolute path -->
        <a href="<?php echo '/Source_folder_final_v2/model/auth/logout.php'; ?>" class="nav-item logout-btn">
            <i class="fas fa-sign-out-alt"></i>
            Logout
        </a>
    </nav>
</div>

<style>
    .logout-btn {
        margin-top: auto;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
        padding-top: 15px !important;
        color: #ff4444 !important;
        cursor: pointer;
    }

    .nav-menu {
        display: flex;
        flex-direction: column;
        height: calc(100% - 60px); /* Adjust based on your logo height */
    }

    /* New responsive styles */
    .burger-menu {
        display: none;
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1001;
        cursor: pointer;
    }

    .burger-icon {
        width: 30px;
        height: 20px;
        position: relative;
    }

    .burger-icon span {
        display: block;
        position: absolute;
        height: 3px;
        width: 100%;
        background: #006747;
        border-radius: 3px;
        transition: all 0.3s ease;
    }

    .burger-icon span:nth-child(1) { top: 0; }
    .burger-icon span:nth-child(2) { top: 8px; }
    .burger-icon span:nth-child(3) { top: 16px; }

    /* Burger menu active state */
    .burger-menu.active span:nth-child(1) {
        transform: rotate(45deg);
        top: 8px;
    }

    .burger-menu.active span:nth-child(2) {
        opacity: 0;
    }

    .burger-menu.active span:nth-child(3) {
        transform: rotate(-45deg);
        top: 8px;
    }

    @media screen and (max-width: 768px) {
        .burger-menu {
            display: block;
        }

        .sidebar {
            transform: translateX(-100%);
            transition: transform 0.3s ease;
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            z-index: 1000;
            
            box-shadow: 2px 0 5px rgba(0, 0, 0, 0.1);
        }

        .sidebar.active {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
            width: 100%;
        }
    }

    /* Overlay for mobile menu */
    .sidebar-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 999;
    }

    .sidebar-overlay.active {
        display: block;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const burgerMenu = document.querySelector('.burger-menu');
    const sidebar = document.getElementById('mainSidebar');
    const body = document.body;

    // Create overlay element
    const overlay = document.createElement('div');
    overlay.className = 'sidebar-overlay';
    body.appendChild(overlay);

    burgerMenu.addEventListener('click', function() {
        burgerMenu.classList.toggle('active');
        sidebar.classList.toggle('active');
        overlay.classList.toggle('active');
        body.style.overflow = sidebar.classList.contains('active') ? 'hidden' : '';
    });

    // Close sidebar when clicking overlay
    overlay.addEventListener('click', function() {
        burgerMenu.classList.remove('active');
        sidebar.classList.remove('active');
        overlay.classList.remove('active');
        body.style.overflow = '';
    });

    // Close sidebar when clicking links (for mobile)
    const navLinks = sidebar.querySelectorAll('a');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth <= 1024) {
                burgerMenu.classList.remove('active');
                sidebar.classList.remove('active');
                overlay.classList.remove('active');
                body.style.overflow = '';
            }
        });
    });

    // Handle resize events
    window.addEventListener('resize', function() {
        if (window.innerWidth > 1024) {
            burgerMenu.classList.remove('active');
            sidebar.classList.remove('active');
            overlay.classList.remove('active');
            body.style.overflow = '';
        }
    });
});
</script>

<?php
function renderBackButton($returnUrl, $text = 'Back') {
    echo '<div class="page-header">
            <button class="back-button" onclick="window.location.href=\'' . $returnUrl . '\'">
                <i class="fas fa-arrow-left"></i> ' . $text . '
            </button>
          </div>';
}
?>