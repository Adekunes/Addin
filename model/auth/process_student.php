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
    
    // Get the action from POST or GET
    $action = $_POST['action'] ?? $_GET['action'] ?? '';
    
    switch($action) {
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
                error_log("New student added with ID: " . $studentId);
                
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
                error_log("Progress addition result: " . ($progressResult ? "success" : "failed"));
                
                if ($progressResult) {
                    echo json_encode([
                        'success' => true,
                        'message' => 'Student added successfully',
                        'student_id' => $studentId
                    ]);
                } else {
                    error_log("Failed to add progress for student ID: " . $studentId);
                    throw new Exception('Failed to add student progress');
                }
            } else {
                throw new Exception('Failed to add student');
            }
            break;

        case 'update_student':
            try {
                // Validate and sanitize input
                $studentId = filter_input(INPUT_POST, 'student_id', FILTER_SANITIZE_NUMBER_INT);
                $currentJuz = filter_input(INPUT_POST, 'currentJuz', FILTER_SANITIZE_NUMBER_INT);
                $currentSurah = filter_input(INPUT_POST, 'currentSurah', FILTER_SANITIZE_NUMBER_INT);
                $startAyat = filter_input(INPUT_POST, 'start_ayat', FILTER_SANITIZE_NUMBER_INT);
                $endAyat = filter_input(INPUT_POST, 'end_ayat', FILTER_SANITIZE_NUMBER_INT);
                $memorizationQuality = filter_input(INPUT_POST, 'memorizationQuality', FILTER_SANITIZE_STRING);
                $teacherNotes = filter_input(INPUT_POST, 'teacherNotes', FILTER_SANITIZE_STRING);
                
                // Update progress
                $result = $adminDb->updateStudentProgress(
                    $studentId, 
                    $currentJuz, 
                    $currentSurah,
                    $startAyat,
                    $endAyat,
                    $memorizationQuality,
                    $teacherNotes
                );
                
                echo json_encode($result);
                
            } catch (Exception $e) {
                error_log("Error updating student: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            break;

        case 'add_revision':
            if (!isset($_POST['student_id']) || !isset($_POST['juzRevised'])) {
                throw new Exception('Missing required fields');
            }
            
            $revisionData = [
                'student_id' => intval($_POST['student_id']),
                'juz_revised' => intval($_POST['juzRevised']),
                'revision_date' => date('Y-m-d'),
                'quality_rating' => $_POST['revisionQuality'] ?? 'average',
                'teacher_notes' => $_POST['revisionNotes'] ?? null
            ];
            
            error_log("Adding revision with data: " . print_r($revisionData, true)); // Debug log
            
            try {
                $result = $adminDb->addJuzRevision($revisionData);
                echo json_encode([
                    'success' => true,
                    'message' => 'Revision recorded successfully'
                ]);
            } catch (Exception $e) {
                error_log("Error adding revision: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            break;

        case 'delete_student':
            if (!isset($_POST['student_id'])) {
                throw new Exception('Student ID is required');
            }
            $result = $adminDb->deleteStudent($_POST['student_id']);
            echo json_encode(['success' => $result, 'message' => $result ? 'Success' : 'Failed to delete']);
            break;

        case 'get_student':
            $studentId = $_GET['id'] ?? null;
            error_log("Fetching student with ID: " . $studentId);
            
            if (!$studentId) {
                throw new Exception('Student ID is required');
            }
            
            try {
                $student = $adminDb->getStudent($studentId);
                if ($student) {
                    echo json_encode([
                        'success' => true,
                        'data' => $student
                    ]);
                } else {
                    throw new Exception('Student not found');
                }
            } catch (Exception $e) {
                error_log("Error getting student: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            break;
            
        case 'get_revision_history':
            $studentId = $_GET['student_id'] ?? null;
            
            if (!$studentId) {
                throw new Exception('Student ID is required');
            }
            
            try {
                $revisions = $adminDb->getStudentRevisionHistory($studentId);
                echo json_encode([
                    'success' => true,
                    'revisions' => $revisions
                ]);
            } catch (Exception $e) {
                error_log("Error getting revision history: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            break;

        case 'get_student_history':
            try {
                $studentId = $_GET['student_id'] ?? null;
                if (!$studentId) {
                    throw new Exception('Student ID is required');
                }

                $history = $adminDb->getStudentHistory($studentId);
                echo json_encode($history);
                
            } catch (Exception $e) {
                error_log("Error getting student history: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            break;

        case 'get_latest_progress':
            try {
                $studentId = $_GET['student_id'] ?? null;
                if (!$studentId) {
                    throw new Exception('Student ID is required');
                }

                $progress = $adminDb->getLatestProgress($studentId);
                echo json_encode($progress);
                
            } catch (Exception $e) {
                error_log("Error getting latest progress: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            break;

        case 'get_surahs_by_juz':
            try {
                $juzNumber = $_GET['juz'] ?? null;
                if (!$juzNumber) {
                    throw new Exception('Juz number is required');
                }

                $surahs = $adminDb->getSurahsByJuz($juzNumber);
                echo json_encode($surahs);
                
            } catch (Exception $e) {
                error_log("Error getting surahs: " . $e->getMessage());
                echo json_encode([
                    'success' => false,
                    'message' => $e->getMessage()
                ]);
            }
            break;

        default:
            throw new Exception('Invalid action');
    }
} catch (Exception $e) {
    error_log("Process student error: " . $e->getMessage());
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}

// Clear any buffered output and end the script
ob_end_flush();
exit; 