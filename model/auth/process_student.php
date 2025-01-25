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
                case 'add_student':
                    // Validate required fields
                    $requiredFields = ['firstName', 'lastName', 'dateOfBirth', 'gender', 
                                     'guardianName', 'guardianPhone', 'currentJuz', 
                                     'completedJuz', 'memorizationQuality', 'tajweedLevel'];
                    
                    foreach ($requiredFields as $field) {
                        if (!isset($_POST[$field]) || empty($_POST[$field])) {
                            throw new Exception("Missing required field: $field");
                        }
                    }

                    // Prepare student data
                    $studentData = [
                        'name' => $_POST['firstName'] . ' ' . $_POST['lastName'],
                        'date_of_birth' => $_POST['dateOfBirth'],
                        'gender' => $_POST['gender'],
                        'guardian_name' => $_POST['guardianName'],
                        'guardian_contact' => $_POST['guardianPhone'],
                        'email' => $_POST['email'] ?? null,
                        'address' => $_POST['address'] ?? null,
                        'enrollment_date' => date('Y-m-d'),
                        'status' => 'active'
                    ];

                    // Add the student
                    $result = $adminDb->addStudent($studentData);
                    
                    if ($result) {
                        $studentId = $adminDb->getLastInsertId();
                        
                        // Add initial progress
                        $progressData = [
                            'student_id' => $studentId,
                            'current_juz' => $_POST['currentJuz'],
                            'completed_juz' => $_POST['completedJuz'],
                            'last_completed_surah' => $_POST['lastCompletedSurah'] ?? null,
                            'memorization_quality' => $_POST['memorizationQuality'],
                            'tajweed_level' => $_POST['tajweedLevel'],
                            'revision_status' => $_POST['revisionStatus'] ?? 'onTrack',
                            'teacher_notes' => $_POST['teacherNotes'] ?? null,
                            'medical_info' => $_POST['medicalInfo'] ?? null
                        ];
                        
                        $progressResult = $adminDb->addProgress($progressData);
                        
                        if ($progressResult) {
                            echo json_encode([
                                'success' => true,
                                'message' => 'Student added successfully',
                                'redirect' => 'manage_students.php'
                            ]);
                        } else {
                            throw new Exception('Failed to add student progress');
                        }
                    } else {
                        throw new Exception('Failed to add student');
                    }
                    break;

                case 'update_student':
                    if (!isset($_POST['student_id'])) {
                        throw new Exception('Student ID is required');
                    }

                    // Start transaction
                    $db->beginTransaction();
                    try {
                        // Update student progress
                        $progressData = [
                            'student_id' => $_POST['student_id'],
                            'current_juz' => $_POST['currentJuz'],
                            'completed_juz' => $_POST['currentJuz'] - 1,
                            'memorization_quality' => $_POST['memorizationQuality'],
                            'tajweed_level' => $_POST['tajweedLevel'],
                            'teacher_notes' => $_POST['teacherNotes'] ?? '',
                            'updated_at' => date('Y-m-d H:i:s'),
                            'date' => date('Y-m-d')
                        ];
                        
                        $result = $adminDb->updateProgress($progressData);

                        // Update juz mastery
                        $masteryData = [
                            'student_id' => $_POST['student_id'],
                            'juz_number' => $_POST['currentJuz'],
                            'mastery_level' => $_POST['masteryLevel']
                        ];
                        
                        $masteryResult = $adminDb->updateJuzMastery($masteryData);

                        $db->commit();
                        echo json_encode(['success' => true, 'message' => 'Progress updated successfully']);
                    } catch (Exception $e) {
                        $db->rollBack();
                        throw new Exception('Failed to update progress: ' . $e->getMessage());
                    }
                    break;

                case 'add_revision':
                    if (!isset($_POST['student_id']) || !isset($_POST['juzRevised']) || !isset($_POST['revisionQuality'])) {
                        error_log("Missing fields in POST data: " . print_r($_POST, true));
                        throw new Exception('Missing required revision fields');
                    }

                    try {
                        // Validate student_id is numeric
                        if (!is_numeric($_POST['student_id'])) {
                            throw new Exception('Invalid student ID');
                        }

                        // Validate juzRevised is between 1 and 30
                        $juzRevised = intval($_POST['juzRevised']);
                        if ($juzRevised < 1 || $juzRevised > 30) {
                            throw new Exception('Invalid juz number');
                        }

                        // Validate quality_rating is one of the allowed values
                        $allowedQualityRatings = ['excellent', 'good', 'average', 'needsWork'];
                        if (!in_array($_POST['revisionQuality'], $allowedQualityRatings)) {
                            throw new Exception('Invalid quality rating');
                        }

                        $revisionData = [
                            'student_id' => intval($_POST['student_id']),
                            'juz_revised' => $juzRevised,
                            'revision_date' => date('Y-m-d'),
                            'quality_rating' => $_POST['revisionQuality'],
                            'teacher_notes' => $_POST['revisionNotes'] ?? null
                        ];
                        
                        error_log("Attempting to add revision with data: " . print_r($revisionData, true));
                        
                        try {
                            $result = $adminDb->addJuzRevision($revisionData);
                            if ($result === true) {
                                echo json_encode(['success' => true, 'message' => 'Revision recorded successfully']);
                            } else {
                                throw new Exception('Failed to record revision in database');
                            }
                        } catch (Exception $e) {
                            throw new Exception($e->getMessage());
                        }
                    } catch (Exception $e) {
                        error_log("Exception in add_revision: " . $e->getMessage());
                        echo json_encode([
                            'success' => false,
                            'message' => $e->getMessage()
                        ]);
                        exit;
                    }
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
    } else if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['action']) && $_GET['action'] === 'get_history') {
        if (!isset($_GET['id'])) {
            throw new Exception('Student ID is required');
        }
        try {
            $history = $adminDb->getStudentHistory($_GET['id']);
            echo json_encode([
                'success' => true,
                'data' => $history
            ]);
        } catch (Exception $e) {
            echo json_encode([
                'success' => false,
                'message' => $e->getMessage()
            ]);
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