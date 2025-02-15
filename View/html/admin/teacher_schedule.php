<?php
session_start();
require_once '../../../model/auth/admin_auth.php';
require_once '../../../model/sql/admin_db.php';

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../../login.php');
    exit();
}

$teacherId = isset($_GET['id']) ? $_GET['id'] : null;
if (!$teacherId) {
    header('Location: manage_teachers.php');
    exit();
}

$adminDb = new AdminDatabase();
$teacher = $adminDb->getTeacherById($teacherId);
$schedule = $adminDb->getTeacherSchedule($teacherId);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Teacher Schedule - Dar-al-uloom</title>
    <link rel="stylesheet" href="../../../components/css/sidebar.css">
    <link rel="stylesheet" href="../../css/admin/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.css">
    <style>
        .schedule-container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            margin-top: 20px;
        }

        .schedule-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
        }

        .fc-event {
            cursor: pointer;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0,0,0,0.5);
            z-index: 1000;
        }

        .modal-content {
            background: white;
            width: 90%;
            max-width: 500px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 8px;
            position: relative;
        }

        .close {
            position: absolute;
            right: 20px;
            top: 20px;
            font-size: 24px;
            cursor: pointer;
        }

        .time-slot {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 10px;
            padding: 10px;
            background: #f8f9fa;
            border-radius: 4px;
        }

        .time-slot:hover {
            background: #e9ecef;
        }

        .time-slot input[type="checkbox"] {
            width: 18px;
            height: 18px;
        }
    </style>
</head>
<body>
    <?php include '../../../components/php/admin_sidebar.php'; ?>

    <div class="main-content">
        <div class="content-header">
            <h1>Schedule for <?php echo htmlspecialchars($teacher['name']); ?></h1>
            <button class="btn-primary" onclick="openAddScheduleModal()">
                <i class="fas fa-plus"></i> Add Schedule
            </button>
        </div>

        <div class="schedule-container">
            <div id="calendar"></div>
        </div>
    </div>

    <!-- Add Schedule Modal -->
    <div id="scheduleModal" class="modal">
        <div class="modal-content">
            <span class="close">&times;</span>
            <h2>Add Schedule</h2>
            <form id="scheduleForm">
                <input type="hidden" name="teacher_id" value="<?php echo $teacherId; ?>">
                <input type="hidden" name="action" value="add_schedule">
                
                <div class="form-group">
                    <label for="day">Day</label>
                    <select id="day" name="day" required>
                        <option value="monday">Monday</option>
                        <option value="tuesday">Tuesday</option>
                        <option value="wednesday">Wednesday</option>
                        <option value="thursday">Thursday</option>
                        <option value="friday">Friday</option>
                        <option value="saturday">Saturday</option>
                        <option value="sunday">Sunday</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Time Slots</label>
                    <div id="timeSlots">
                        <!-- Time slots will be generated by JavaScript -->
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Schedule</button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@5.11.3/main.min.js"></script>
    <script src="../../../components/js/sidebar.js"></script>
    <script src="../../../model/js/admin_teacher_schedule.js"></script>
</body>
</html> 