<?php
// Prevent any output before headers
ob_start();

// Disable error display but log them
error_reporting(E_ALL);
ini_set('display_errors', 0);

// Set JSON header
header('Content-Type: application/json');
session_start();

try {
    require_once '../sql/admin_db.php';
    $adminDb = new AdminDatabase();
    
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if (isset($_POST['action'])) {
            switch ($_POST['action']) {
                case 'update_student':
                    if (!isset($_POST['student_id']) || !isset($_POST['currentJuz'])) {
                        throw new Exception('Missing required fields');
                    }

                    $progressData = [
                        'student_id' => $_POST['student_id'],
                        'surah_name' => $_POST['surahName'],
                        'verse_start' => $_POST['verseStart'],
                        'verse_end' => $_POST['verseEnd'],
                        'lines_memorized' => $_POST['linesMemorized'],
                        'lesson_date' => $_POST['lessonDate'],
                        'memorization_quality' => $_POST['memorizationQuality'],
                        'tajweed_level' => $_POST['tajweedLevel'],
                        'teacher_notes' => $_POST['teacherNotes'] ?? '',
                    ];
                    
                    $result = $adminDb->addProgress($progressData);
                    echo json_encode(['success' => $result, 'message' => $result ? 'Success' : 'Failed to update']);
                    break;

                case 'delete_student':
                    if (!isset($_POST['student_id'])) {
                        throw new Exception('Student ID is required');
                    }
                    $result = $adminDb->deleteStudent($_POST['student_id']);
                    echo json_encode(['success' => $result, 'message' => $result ? 'Success' : 'Failed to delete']);
                    break;

                default:
                    throw new Exception('Invalid action');
            }
        } else {
            throw new Exception('No action specified');
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_student') {
        if (!isset($_GET['id'])) {
            echo json_encode(['success' => false, 'message' => 'Student ID is required']);
            exit;
        }

        $studentData = $adminDb->getStudentById($_GET['id']);
        if ($studentData) {
            echo json_encode(['success' => true, 'data' => $studentData]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Student not found']);
        }
        exit;
    } else {
        throw new Exception('Invalid request method');
    }
} catch (Exception $e) {
    error_log("Error in process_student.php: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Clear any buffered output and end the script
ob_end_flush();
exit; 