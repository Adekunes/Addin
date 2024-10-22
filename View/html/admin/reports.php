<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Dar Al-'Ulum Montréal</title>
    <link rel="stylesheet" href="../../../View/css/admin/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <button class="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>

    <?php require_once('../../../components/php/admin_sidebar.php'); ?>

    <div class="main-content">
        <div class="reports-section">
            <h1>Reports</h1>
            
            <div class="report-buttons">
                <button id="attendanceReportBtn" class="btn btn-primary">
                    <i class="fas fa-clipboard-check"></i> Attendance Report
                </button>
                <button id="progressReportBtn" class="btn btn-primary">
                    <i class="fas fa-chart-line"></i> Progress Report
                </button>
                <button id="teacherPerformanceBtn" class="btn btn-primary">
                    <i class="fas fa-chalkboard-teacher"></i> Teacher Performance
                </button>
            </div>

            <div class="report-filters">
                <select id="dateRange">
                    <option value="week">Last Week</option>
                    <option value="month">Last Month</option>
                    <option value="quarter">Last Quarter</option>
                    <option value="year">Last Year</option>
                </select>
                <button id="generateReportBtn" class="btn btn-secondary">Generate Report</button>
            </div>

            <div id="reportContent" class="report-content">
                <!-- Report content will be displayed here -->
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="../../../components/js/admin_reports.js"></script>
    <script>
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>