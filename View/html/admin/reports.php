<?php
session_start();
require_once '../../../model/auth/admin_auth.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - Hifz Management System</title>
    
    <!-- CSS Files -->
    <link rel="stylesheet" href="../../css/admin/styles.css">
    <link rel="stylesheet" href="../../../components/layouts/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css">
</head>
<body>
    <?php include '../../../components/layouts/sidebar.html'; ?>
    
    <div class="main-content">
        <h1>Reports</h1>

        <div class="reports-container">
            <div class="report-filters">
                <select id="reportType">
                    <option value="attendance">Attendance Report</option>
                    <option value="progress">Progress Report</option>
                    <option value="performance">Performance Report</option>
                </select>
                <input type="date" id="startDate">
                <input type="date" id="endDate">
                <button onclick="generateReport()" class="btn btn-primary">Generate Report</button>
            </div>

            <div class="report-sections">
                <div id="reportChart"></div>
                <div id="reportTable"></div>
            </div>

            <div class="export-options">
                <button onclick="exportToPDF()" class="btn btn-secondary">
                    <i class="fas fa-file-pdf"></i> Export to PDF
                </button>
                <button onclick="exportToExcel()" class="btn btn-secondary">
                    <i class="fas fa-file-excel"></i> Export to Excel
                </button>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/3.7.0/chart.min.js"></script>
    <script src="../../../components/layouts/sidebar.js"></script>
    <script src="../../../components/js/admin_reports.js"></script>
</body>
</html>