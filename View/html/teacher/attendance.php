<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mark Attendance - Dar Al-'Ulum Montréal</title>
    <link rel="stylesheet" href="../../../View/css/teacher/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
</head>
<body>
    <button class="menu-toggle">
        <i class="fas fa-bars"></i>
    </button>

    <?php require_once('../../../components/php/teacher_sidebar.php'); ?>

    <div class="main-content">
        <div class="attendance-section">
            <h1>Mark Attendance</h1>

            <form id="attendanceForm" class="attendance-form">
                <div class="form-group">
                    <label for="classSelect">Select Class:</label>
                    <select id="classSelect" required>
                        <option value="">Choose a class</option>
                        <!-- Classes will be populated dynamically -->
                    </select>
                </div>

                <div class="form-group">
                    <label for="attendanceDate">Date:</label>
                    <input type="date" id="attendanceDate" required>
                </div>

                <div class="student-list" id="studentList">
                    <h3>Students</h3>
                    <div class="attendance-table">
                        <table>
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Status</th>
                                    <th>Notes</th>
                                </tr>
                            </thead>
                            <tbody>
                                <!-- Student list will be populated dynamically -->
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Attendance
                    </button>
                    <button type="reset" class="btn btn-secondary">
                        <i class="fas fa-undo"></i> Reset
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script src="../../../components/js/teacher_attendance.js"></script>
    <script>
        document.querySelector('.menu-toggle').addEventListener('click', function() {
            document.querySelector('.sidebar').classList.toggle('active');
        });
    </script>
</body>
</html>