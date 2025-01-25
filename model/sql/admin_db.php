<?php
require_once __DIR__ . '/../config/config.php';

class AdminDatabase {
    private $db;

    public function __construct() {
        $database = new Database();
        $this->db = $database->getConnection();
    }

    public function getAllTeachers() {
        try {
            $query = "SELECT t.teacher_id as id, t.name, t.phone, t.subjects, t.status, 
                      u.email, u.username 
                      FROM teachers t 
                      JOIN users u ON t.user_id = u.id 
                      WHERE u.role = 'teacher' 
                      ORDER BY t.name ASC";
                      
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching teachers: " . $e->getMessage());
            return [];
        }
    }

    public function getStudents() {
        try {
            // Simpler query first to ensure basic functionality
            $query = "SELECT 
                        id,
                        name,
                        guardian_name,
                        guardian_contact,
                        enrollment_date,
                        current_juz,
                        completed_juz,
                        status
                    FROM students
                    WHERE status = 'active'";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Debug log
            error_log("Found " . count($result) . " students");
            
            return $result;
            
        } catch(PDOException $e) {
            error_log("Error fetching students: " . $e->getMessage());
            return [];
        }
    }

    public function getStudentById($id) {
        try {
            $query = "SELECT s.*, p.current_juz, p.completed_juz, 
                            p.memorization_quality, p.tajweed_level, 
                            p.teacher_notes, jm.mastery_level
                     FROM students s
                     LEFT JOIN progress p ON s.id = p.student_id
                     LEFT JOIN juz_mastery jm ON s.id = jm.student_id 
                         AND jm.juz_number = p.current_juz
                     WHERE s.id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$result) {
                error_log("No student found with ID: $id");
                return null;
            }
            
            return $result;
        } catch(PDOException $e) {
            error_log("Error fetching student: " . $e->getMessage());
            return false;
        }
    }

    public function updateStudent($data) {
        try {
            // Check if we're updating progress or full student data
            if (isset($data['current_juz'])) {
                // Updating progress
                $query = "UPDATE students 
                          SET current_juz = :current_juz,
                              completed_juz = :completed_juz,
                              status = :status
                          WHERE id = :id";
                
                $stmt = $this->db->prepare($query);
                return $stmt->execute([
                    ':id' => $data['id'],
                    ':current_juz' => $data['current_juz'],
                    ':completed_juz' => $data['completed_juz'],
                    ':status' => $data['status']
                ]);
            } else {
                // Updating student information
                $query = "UPDATE students 
                          SET name = :name,
                              date_of_birth = :date_of_birth,
                              guardian_name = :guardian_name,
                              guardian_contact = :guardian_contact,
                              enrollment_date = :enrollment_date,
                              status = :status
                          WHERE id = :id";
                
                $stmt = $this->db->prepare($query);
                return $stmt->execute([
                    ':id' => $data['id'],
                    ':name' => $data['name'],
                    ':date_of_birth' => $data['date_of_birth'],
                    ':guardian_name' => $data['guardian_name'],
                    ':guardian_contact' => $data['guardian_contact'],
                    ':enrollment_date' => $data['enrollment_date'],
                    ':status' => $data['status']
                ]);
            }
        } catch(PDOException $e) {
            error_log("Error updating student: " . $e->getMessage());
            return false;
        }
    }

    public function getAllStudents() {
        try {
            $query = "SELECT s.*, p.current_juz, p.completed_juz, p.memorization_quality, 
                         p.tajweed_level, p.teacher_notes, jm.mastery_level,
                         (
                             SELECT MAX(revision_date) 
                             FROM juz_revisions 
                             WHERE student_id = s.id
                         ) as last_revision_date,
                         (
                             SELECT COUNT(*) 
                             FROM juz_revisions 
                             WHERE student_id = s.id
                         ) as revision_count
                   FROM students s
                   LEFT JOIN progress p ON s.id = p.student_id
                   LEFT JOIN juz_mastery jm ON s.id = jm.student_id 
                       AND jm.juz_number = COALESCE(p.current_juz, 1)
                   GROUP BY s.id
                   ORDER BY s.name ASC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching students: " . $e->getMessage());
            return [];
        }
    }

    public function addStudent($data) {
        try {
            $query = "INSERT INTO students (name, date_of_birth, guardian_name, guardian_contact, enrollment_date, status) 
                      VALUES (:name, :date_of_birth, :guardian_name, :guardian_contact, :enrollment_date, :status)";
            
            $stmt = $this->db->prepare($query);
            
            return $stmt->execute([
                ':name' => $data['name'],
                ':date_of_birth' => $data['date_of_birth'],
                ':guardian_name' => $data['guardian_name'],
                ':guardian_contact' => $data['guardian_contact'],
                ':enrollment_date' => $data['enrollment_date'],
                ':status' => $data['status']
            ]);
        } catch(PDOException $e) {
            error_log("Error adding student: " . $e->getMessage());
            return false;
        }
    }

    public function getLastInsertId() {
        return $this->db->lastInsertId();
    }

    public function addProgress($data) {
        try {
            $query = "INSERT INTO progress (
                student_id, 
                current_juz, 
                completed_juz, 
                last_completed_surah,
                memorization_quality,
                tajweed_level,
                revision_status,
                last_revision_date,
                teacher_notes,
                date
            ) VALUES (
                :student_id,
                :current_juz,
                :completed_juz,
                :last_completed_surah,
                :memorization_quality,
                :tajweed_level,
                :revision_status,
                :last_revision_date,
                :teacher_notes,
                :date
            )";
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                ':student_id' => $data['student_id'],
                ':current_juz' => $data['current_juz'],
                ':completed_juz' => $data['completed_juz'],
                ':last_completed_surah' => $data['last_completed_surah'],
                ':memorization_quality' => $data['memorization_quality'],
                ':tajweed_level' => $data['tajweed_level'],
                ':revision_status' => $data['revision_status'],
                ':last_revision_date' => $data['last_revision_date'],
                ':teacher_notes' => $data['teacher_notes'],
                ':date' => $data['date']
            ]);
        } catch(PDOException $e) {
            error_log("Error adding progress: " . $e->getMessage());
            return false;
        }
    }

    public function deleteStudent($studentId) {
        try {
            // Start transaction
            $this->db->beginTransaction();
            
            // Delete from progress table first (if exists)
            $stmt = $this->db->prepare("DELETE FROM progress WHERE student_id = :student_id");
            $stmt->execute([':student_id' => $studentId]);
            
            // Then delete from students table
            $stmt = $this->db->prepare("DELETE FROM students WHERE id = :student_id");
            $result = $stmt->execute([':student_id' => $studentId]);
            
            // Commit transaction
            $this->db->commit();
            
            return $result;
        } catch(PDOException $e) {
            // Rollback transaction on error
            $this->db->rollBack();
            error_log("Error deleting student: " . $e->getMessage());
            return false;
        }
    }

    public function updateProgress($data) {
        try {
            $query = "UPDATE progress SET 
                      current_juz = :current_juz,
                      completed_juz = :completed_juz,
                      memorization_quality = :memorization_quality,
                      tajweed_level = :tajweed_level,
                      teacher_notes = :teacher_notes,
                      updated_at = :updated_at,
                      date = :date
                      WHERE student_id = :student_id";
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                ':student_id' => $data['student_id'],
                ':current_juz' => $data['current_juz'],
                ':completed_juz' => $data['completed_juz'],
                ':memorization_quality' => $data['memorization_quality'],
                ':tajweed_level' => $data['tajweed_level'],
                ':teacher_notes' => $data['teacher_notes'],
                ':updated_at' => $data['updated_at'],
                ':date' => $data['date']
            ]);
        } catch(PDOException $e) {
            error_log("Error updating progress: " . $e->getMessage());
            return false;
        }
    }

    public function updateJuzMastery($data) {
        try {
            $query = "INSERT INTO juz_mastery 
                      (student_id, juz_number, mastery_level, last_revision_date) 
                      VALUES (:student_id, :juz_number, :mastery_level, CURRENT_DATE)
                      ON DUPLICATE KEY UPDATE 
                      mastery_level = :mastery_level,
                      last_revision_date = CURRENT_DATE";
            
            $stmt = $this->db->prepare($query);
            return $stmt->execute([
                ':student_id' => $data['student_id'],
                ':juz_number' => $data['juz_number'],
                ':mastery_level' => $data['mastery_level']
            ]);
        } catch(PDOException $e) {
            error_log("Error updating juz mastery: " . $e->getMessage());
            return false;
        }
    }

    public function addJuzRevision($data) {
        try {
            error_log("Starting addJuzRevision with data: " . print_r($data, true));
            
            // First verify the student exists
            $checkStudent = $this->db->prepare("SELECT s.id, p.current_juz 
                                               FROM students s 
                                               LEFT JOIN progress p ON s.id = p.student_id 
                                               WHERE s.id = ?");
            $checkStudent->execute([$data['student_id']]);
            $student = $checkStudent->fetch(PDO::FETCH_ASSOC);
            if (!$student) {
                error_log("Student not found with ID: " . $data['student_id']);
                throw new Exception('Student not found');
            }

            // Update the progress table with the latest revision
            $updateProgress = $this->db->prepare("
                UPDATE progress 
                SET last_revision_date = :revision_date
                WHERE student_id = :student_id
            ");
            $updateProgress->execute([
                ':student_id' => $data['student_id'],
                ':revision_date' => $data['revision_date']
            ]);

            // Add the revision record
            $query = "INSERT INTO juz_revisions 
                      (student_id, juz_revised, revision_date, quality_rating, teacher_notes) 
                      VALUES (:student_id, :juz_revised, :revision_date, :quality_rating, :teacher_notes)";
            
            error_log("SQL Query: " . $query);
            $stmt = $this->db->prepare($query);
            
            $params = [
                ':student_id' => $data['student_id'],
                ':juz_revised' => $data['juz_revised'],
                ':revision_date' => $data['revision_date'],
                ':quality_rating' => $data['quality_rating'],
                ':teacher_notes' => $data['teacher_notes']
            ];
            
            error_log("Parameters: " . print_r($params, true));
            
            try {
                $result = $stmt->execute($params);
                if (!$result) {
                    $errorInfo = $stmt->errorInfo();
                    error_log("Database error: " . print_r($errorInfo, true));
                    throw new PDOException("Database error: " . $errorInfo[2]);
                }

                // Update juz_mastery table
                $updateMastery = $this->db->prepare("
                    INSERT INTO juz_mastery 
                    (student_id, juz_number, last_revision_date, revision_count)
                    VALUES (:student_id, :juz_number, :revision_date, 1)
                    ON DUPLICATE KEY UPDATE 
                    last_revision_date = :revision_date,
                    revision_count = revision_count + 1
                ");
                
                $updateMastery->execute([
                    ':student_id' => $data['student_id'],
                    ':juz_number' => $data['juz_revised'],
                    ':revision_date' => $data['revision_date']
                ]);

                return true;
            } catch (PDOException $e) {
                error_log("Execute error: " . $e->getMessage());
                if (strpos($e->getMessage(), 'Duplicate entry') !== false) {
                    throw new Exception('A revision for this juz has already been recorded today');
                }
                throw $e;
            }
        } catch(PDOException $e) {
            error_log("PDO Exception in addJuzRevision: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw new Exception('Database error: ' . $e->getMessage());
        }
    }

    public function getStudentHistory($studentId) {
        try {
            // Get revision history
            $revisionQuery = "
                SELECT 
                    revision_date,
                    juz_revised,
                    quality_rating,
                    teacher_notes
                FROM juz_revisions
                WHERE student_id = :student_id
                ORDER BY revision_date DESC, created_at DESC
            ";
            
            $revisionStmt = $this->db->prepare($revisionQuery);
            $revisionStmt->execute([':student_id' => $studentId]);
            $revisions = $revisionStmt->fetchAll(PDO::FETCH_ASSOC);

            // Get memorization progress for all juz
            $progressQuery = "
                SELECT 
                    jm.juz_number,
                    jm.mastery_level,
                    jm.last_revision_date,
                    jm.revision_count,
                    COALESCE(
                        (SELECT COUNT(*) 
                         FROM juz_revisions jr 
                         WHERE jr.student_id = jm.student_id 
                         AND jr.juz_revised = jm.juz_number),
                        0
                    ) as total_revisions
                FROM juz_mastery jm
                WHERE jm.student_id = :student_id
                UNION
                SELECT 
                    number as juz_number,
                    'not_started' as mastery_level,
                    NULL as last_revision_date,
                    0 as revision_count,
                    0 as total_revisions
                FROM (
                    SELECT 1 as number UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5
                    UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9 UNION SELECT 10
                    UNION SELECT 11 UNION SELECT 12 UNION SELECT 13 UNION SELECT 14 UNION SELECT 15
                    UNION SELECT 16 UNION SELECT 17 UNION SELECT 18 UNION SELECT 19 UNION SELECT 20
                    UNION SELECT 21 UNION SELECT 22 UNION SELECT 23 UNION SELECT 24 UNION SELECT 25
                    UNION SELECT 26 UNION SELECT 27 UNION SELECT 28 UNION SELECT 29 UNION SELECT 30
                ) numbers
                WHERE number NOT IN (
                    SELECT juz_number FROM juz_mastery WHERE student_id = :student_id
                )
                ORDER BY juz_number ASC
            ";
            
            $progressStmt = $this->db->prepare($progressQuery);
            $progressStmt->execute([':student_id' => $studentId]);
            $progress = $progressStmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'revisions' => $revisions,
                'progress' => $progress
            ];
        } catch(PDOException $e) {
            error_log("Error fetching student history: " . $e->getMessage());
            throw new Exception('Failed to fetch student history');
        }
    }

    public function updateTeacher($data) {
        try {
            $query = "UPDATE teachers 
                      SET name = :name,
                          phone = :phone,
                          subjects = :subjects,
                          status = :status
                      WHERE teacher_id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':id' => $data['id'],
                ':name' => $data['name'],
                ':phone' => $data['phone'],
                ':subjects' => $data['subjects'],
                ':status' => $data['status']
            ]);

            // Update email in users table
            $query = "UPDATE users u
                      JOIN teachers t ON u.id = t.user_id
                      SET u.email = :email
                      WHERE t.teacher_id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':id' => $data['id'],
                ':email' => $data['email']
            ]);

            return true;
        } catch(PDOException $e) {
            error_log("Error updating teacher: " . $e->getMessage());
            return false;
        }
    }

    public function getTeacherById($id) {
        try {
            $query = "SELECT t.teacher_id as id, t.name, t.phone, t.subjects, t.status,
                             u.email, u.username
                      FROM teachers t
                      JOIN users u ON t.user_id = u.id
                      WHERE t.teacher_id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching teacher: " . $e->getMessage());
            return null;
        }
    }

    public function addTeacher($data) {
        try {
            $this->db->beginTransaction();

            // First create the user
            $userQuery = "INSERT INTO users (username, password, email, role) 
                         VALUES (:username, :password, :email, 'teacher')";
             
            $userStmt = $this->db->prepare($userQuery);
            $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);
             
            $userStmt->execute([
                ':username' => $data['username'],
                ':password' => $hashedPassword,
                ':email' => $data['email']
            ]);

            $userId = $this->db->lastInsertId();

            // Then create the teacher
            $teacherQuery = "INSERT INTO teachers (user_id, name, phone, subjects, status) 
                            VALUES (:user_id, :name, :phone, :subjects, :status)";
             
            $teacherStmt = $this->db->prepare($teacherQuery);
            $teacherStmt->execute([
                ':user_id' => $userId,
                ':name' => $data['name'],
                ':phone' => $data['phone'],
                ':subjects' => $data['subjects'],
                ':status' => $data['status']
            ]);

            $this->db->commit();
            return true;

        } catch(PDOException $e) {
            $this->db->rollBack();
            error_log("Error adding teacher: " . $e->getMessage());
            throw new Exception('Failed to add teacher: ' . $e->getMessage());
        }
    }

    public function addTeacherSchedule($data) {
        try {
            $this->db->beginTransaction();

            foreach ($data['times'] as $time) {
                $query = "INSERT INTO teacher_schedule 
                         (teacher_id, day_of_week, time_slot) 
                         VALUES (:teacher_id, :day, :time)
                         ON DUPLICATE KEY UPDATE 
                         updated_at = CURRENT_TIMESTAMP";
                 
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    ':teacher_id' => $data['teacher_id'],
                    ':day' => $data['day'],
                    ':time' => $time
                ]);
            }

            $this->db->commit();
            return true;
        } catch(PDOException $e) {
            $this->db->rollBack();
            error_log("Error adding teacher schedule: " . $e->getMessage());
            throw new Exception('Failed to add schedule: ' . $e->getMessage());
        }
    }

    public function getTeacherSchedule($teacherId) {
        try {
            $query = "SELECT * FROM teacher_schedule 
                      WHERE teacher_id = :teacher_id 
                      ORDER BY day_of_week, time_slot";
              
            $stmt = $this->db->prepare($query);
            $stmt->execute([':teacher_id' => $teacherId]);
            
            $schedules = $stmt->fetchAll(PDO::FETCH_ASSOC);
            return $this->formatScheduleEvents($schedules);
        } catch(PDOException $e) {
            error_log("Error fetching teacher schedule: " . $e->getMessage());
            return [];
        }
    }

    private function formatScheduleEvents($schedules) {
        $events = [];
        $dayMap = [
            'monday' => 1,
            'tuesday' => 2,
            'wednesday' => 3,
            'thursday' => 4,
            'friday' => 5,
            'saturday' => 6,
            'sunday' => 0
        ];

        foreach ($schedules as $schedule) {
            $dayNum = $dayMap[strtolower($schedule['day_of_week'])];
            $date = date('Y-m-d', strtotime("this week +{$dayNum} days"));
            
            $events[] = [
                'title' => 'Available',
                'start' => $date . 'T' . $schedule['time_slot'],
                'end' => $date . 'T' . date('H:i:s', strtotime($schedule['time_slot'] . ' +30 minutes')),
                'backgroundColor' => '#2ecc71',
                'borderColor' => '#27ae60'
            ];
        }

        return $events;
    }

    public function getDashboardStats() {
        try {
            $stats = [];

            // Check if tables exist before querying
            $tables = $this->getExistingTables();
            error_log("Existing tables: " . print_r($tables, true));

            if (in_array('students', $tables)) {
                $stats['total_students'] = $this->getTotalStudents();
                error_log("Total students: " . $stats['total_students']);
            }

            if (in_array('teachers', $tables)) {
                $stats['total_teachers'] = $this->getTotalTeachers();
                error_log("Total teachers: " . $stats['total_teachers']);
            }

            if (in_array('progress', $tables)) {
                $stats['today_attendance'] = $this->getTodayAttendance();
                error_log("Today's attendance: " . $stats['today_attendance']);
            }

            if (in_array('teacher_schedule', $tables)) {
                $stats['active_classes'] = $this->getActiveClasses();
                error_log("Active classes: " . $stats['active_classes']);
            }

            return $stats;
        } catch(PDOException $e) {
            error_log("Error getting dashboard stats: " . $e->getMessage());
            throw new Exception('Failed to get dashboard statistics');
        }
    }

    private function getExistingTables() {
        try {
            $query = "SELECT TABLE_NAME 
                     FROM information_schema.tables 
                     WHERE table_schema = DATABASE()";
            $stmt = $this->db->query($query);
            $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
            error_log("Found tables: " . print_r($tables, true));
            return $tables;
        } catch(PDOException $e) {
            error_log("Error checking tables: " . $e->getMessage());
            return [];
        }
    }

    private function getTotalStudents() {
        $query = "SELECT COUNT(*) as count FROM students WHERE status = 'active'";
        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    private function getTotalTeachers() {
        $query = "SELECT COUNT(*) as count FROM teachers WHERE status = 'active'";
        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    private function getTodayAttendance() {
        $query = "SELECT COUNT(DISTINCT student_id) as count 
                  FROM progress 
                  WHERE DATE(date) = CURDATE()";
        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }

    private function getActiveClasses() {
        $query = "SELECT COUNT(*) as count FROM classes 
                  WHERE status = 'active'";
        $stmt = $this->db->query($query);
        return $stmt->fetch(PDO::FETCH_ASSOC)['count'];
    }
}

?> 