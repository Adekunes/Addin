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
            case 'add_teacher':
                // Validate required fields
                $requiredFields = ['name', 'username', 'email', 'phone', 'password'];
                foreach ($requiredFields as $field) {
                    if (!isset($_POST[$field]) || empty($_POST[$field])) {
                        throw new Exception("Missing required field: $field");
                    }
                }

                $teacherData = [
                    'name' => $_POST['name'],
                    'username' => $_POST['username'],
                    'email' => $_POST['email'],
                    'phone' => $_POST['phone'],
                    'password' => $_POST['password'],
                    'subjects' => $_POST['subjects'] ?? '',
                    'status' => $_POST['status'] ?? 'active'
                ];

                $result = $adminDb->addTeacher($teacherData);
                echo json_encode([
                    'success' => true,
                    'message' => 'Teacher added successfully'
                ]);
                break;

            case 'update_teacher':
                if (!isset($_POST['teacher_id'])) {
                    throw new Exception('Teacher ID is required');
                }

                $teacherData = [
                    'id' => $_POST['teacher_id'],
                    'name' => $_POST['name'],
                    'phone' => $_POST['phone'],
                    'email' => $_POST['email'],
                    'subjects' => $_POST['subjects'],
                    'status' => $_POST['status']
                ];

                $result = $adminDb->updateTeacher($teacherData);
                echo json_encode(['success' => true]);
                break;

            default:
                throw new Exception('Invalid action');
        }
    } else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
        if (!isset($_GET['action'])) {
            throw new Exception('No action specified');
        }

        if ($_GET['action'] === 'get_teacher' && isset($_GET['id'])) {
            $teacher = $adminDb->getTeacherById($_GET['id']);
            if ($teacher) {
                echo json_encode(['success' => true, 'data' => $teacher]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Teacher not found']);
            }
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