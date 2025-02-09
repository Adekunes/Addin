<?php
header('Content-Type: application/json');
session_start();

require_once '../sql/admin_db.php';

try {
    $adminDb = new AdminDatabase();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (!isset($_POST['action'])) {
            throw new Exception('No action specified');
        }

        switch ($_POST['action']) {
            case 'add_schedule':
                if (!isset($_POST['teacher_id']) || !isset($_POST['day']) || !isset($_POST['times'])) {
                    throw new Exception('Missing required fields');
                }

                $scheduleData = [
                    'teacher_id' => $_POST['teacher_id'],
                    'day' => $_POST['day'],
                    'times' => $_POST['times']
                ];

                $result = $adminDb->addTeacherSchedule($scheduleData);
                echo json_encode([
                    'success' => true,
                    'message' => 'Schedule added successfully'
                ]);
                break;

            default:
                throw new Exception('Invalid action');
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (!isset($_GET['action'])) {
            throw new Exception('No action specified');
        }

        if ($_GET['action'] === 'get_schedule' && isset($_GET['teacher_id'])) {
            $schedule = $adminDb->getTeacherSchedule($_GET['teacher_id']);
            echo json_encode([
                'success' => true,
                'events' => $schedule
            ]);
        } else {
            throw new Exception('Invalid action');
        }
    }
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
} 