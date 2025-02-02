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
            $query = "SELECT 
                t.id,
                t.name,
                t.phone,
                t.subjects,
                t.status,
                u.email,
                u.username
                FROM teachers t
                JOIN users u ON t.user_id = u.id
                WHERE status = 'active'
                ORDER BY t.name ASC";
            

            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
            error_log("Found " . count($result) . " teachers");
            return $result;
        } catch(PDOException $e) {
            error_log("Error in getAllTeachers: " . $e->getMessage());
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
            $query = "SELECT 
                s.*,
                p.current_juz,
                p.completed_juz,
                p.current_surah,
                p.verses_memorized,
                p.quality_rating as memorization_quality,
                p.tajweed_level,
                p.teacher_notes
            FROM students s
            LEFT JOIN (
                SELECT 
                    student_id,
                    current_juz,
                    completed_juz,
                    current_surah,
                    verses_memorized,
                    quality_rating,
                    tajweed_level,
                    teacher_notes
                FROM progress 
                WHERE student_id = :id 
                ORDER BY date DESC 
                LIMIT 1
            ) p ON s.id = p.student_id
            WHERE s.id = :id";

            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id, PDO::PARAM_INT);
            $stmt->execute();
            
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($student) {
                error_log("Found student data: " . print_r($student, true));
                return $student;
            } else {
                error_log("No student found with ID: " . $id);
                return null;
            }
        } catch(PDOException $e) {
            error_log("Error in getStudentById: " . $e->getMessage());
            return null;
        }
    }

    public function updateStudent($data) {
        try {
            $this->db->beginTransaction();
            
            // Update student table
            $query = "UPDATE students 
                     SET name = :name,
                         guardian_name = :guardian_name,
                         guardian_contact = :guardian_contact,
                         status = :status,
                         class_id = :class_id,
                         updated_at = CURRENT_TIMESTAMP
                     WHERE id = :student_id";
            
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                ':student_id' => $data['id'],
                ':name' => $data['name'],
                ':guardian_name' => $data['guardian_name'],
                ':guardian_contact' => $data['guardian_contact'],
                ':status' => $data['status'],
                ':class_id' => $data['class_id']
            ]);
            
            if (!$result) {
                throw new Exception("Failed to update student record");
            }

            // Update user email if it exists
            if (isset($data['email'])) {
                $query = "UPDATE users u
                          JOIN students s ON u.id = s.user_id
                          SET u.email = :email
                          WHERE s.id = :student_id";
                
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    ':student_id' => $data['id'],
                    ':email' => $data['email']
                ]);
            }

            $this->db->commit();
            error_log("Student update successful for ID: " . $data['id']);
            return true;

        } catch(Exception $e) {
            $this->db->rollBack();
            error_log("Error updating student: " . $e->getMessage());
            error_log("Update data: " . print_r($data, true));
            throw new Exception('Failed to update student: ' . $e->getMessage());
        }
    }

    public function getAllStudents() {
        try {
            $query = "SELECT 
                        s.*,
                        p.current_juz,
                        p.memorization_quality,
                        p.teacher_notes,
                        p.date as last_update
                     FROM students s
                     LEFT JOIN (
                        SELECT 
                            student_id,
                            current_juz,
                            memorization_quality,
                            teacher_notes,
                            date,
                            ROW_NUMBER() OVER (PARTITION BY student_id ORDER BY date DESC, id DESC) as rn
                        FROM progress
                     ) p ON s.id = p.student_id AND p.rn = 1
                     WHERE s.status = 'active'
                     ORDER BY s.name";

            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Set default current_juz for students without progress
            foreach ($students as &$student) {
                if (!isset($student['current_juz'])) {
                    $student['current_juz'] = 1;
                }
            }
            
            return $students;
            
        } catch(PDOException $e) {
            error_log("Database error in getAllStudents: " . $e->getMessage());
            throw new Exception("Database error while fetching students");
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
            $query = "INSERT INTO progress 
                        (student_id, 
                        current_juz,
                        current_surah,
                        start_ayat,
                        end_ayat, 
                        memorization_quality, 
                        teacher_notes, 
                        date) 
                     VALUES 
                        (:student_id, 
                        :current_juz,
                        :current_surah,
                        :start_ayat,
                        :end_ayat,
                        :memorization_quality, 
                        :teacher_notes, 
                        CURRENT_DATE)";
            
            $stmt = $this->db->prepare($query);
            $params = [
                ':student_id' => $data['student_id'],
                ':current_juz' => $data['current_juz'],
                ':current_surah' => $data['current_surah'],
                ':start_ayat' => $data['start_ayat'],
                ':end_ayat' => $data['end_ayat'],
                ':memorization_quality' => $data['memorization_quality'] ?? 'average',
                ':teacher_notes' => $data['teacher_notes'] ?? null
            ];
            
            $result = $stmt->execute($params);
            
            if (!$result) {
                throw new Exception("Failed to insert progress record");
            }
            
            return true;
            
        } catch(PDOException $e) {
            error_log("Database error in updateProgress: " . $e->getMessage());
            throw new Exception("Database error while inserting progress");
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

    public function getStudentRevisionHistory($studentId) {
        try {
            error_log("Starting getStudentRevisionHistory for student ID: " . $studentId);
            
            // First check if the juz_revisions table exists
            $checkTable = $this->db->query("SHOW TABLES LIKE 'juz_revisions'");
            if ($checkTable->rowCount() == 0) {
                // Create the table if it doesn't exist
                $this->db->exec("CREATE TABLE IF NOT EXISTS juz_revisions (
                    id INT PRIMARY KEY AUTO_INCREMENT,
                    student_id INT NOT NULL,
                    juz_revised INT NOT NULL,
                    revision_date DATE NOT NULL,
                    memorization_quality ENUM('excellent', 'good', 'average', 'needsWork') DEFAULT 'average',
                    teacher_notes TEXT,
                    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE

                )");
                error_log("Created juz_revisions table");
                return []; // Return empty array for new table
            }

            // Validate student ID
            if (!is_numeric($studentId)) {
                error_log("Invalid student ID format: " . $studentId);
                throw new Exception("Invalid student ID format");
            }

            $query = "SELECT 
                        revision_date,
                        juz_revised,
                        memorization_quality,
                        teacher_notes
                     FROM juz_revisions
                     WHERE student_id = :student_id
                     ORDER BY revision_date DESC, id DESC";
                     
            $stmt = $this->db->prepare($query);
            $stmt->execute([':student_id' => $studentId]);
            
            $revisions = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            error_log("Successfully retrieved " . count($revisions) . " revisions for student ID: " . $studentId);
            error_log("Revision data: " . print_r($revisions, true));
            
            return $revisions;
            
        } catch(PDOException $e) {
            error_log("Database error in getStudentRevisionHistory: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw new Exception("Database error while fetching revision history: " . $e->getMessage());
        } catch(Exception $e) {
            error_log("General error in getStudentRevisionHistory: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw $e;
        }
    }

    public function addJuzRevision($data) {
        try {
            error_log("Starting addJuzRevision with data: " . print_r($data, true));
            
            // Validate required fields
            if (!isset($data['student_id']) || !isset($data['juz_revised'])) {
                throw new Exception("Missing required revision data");
            }

            $query = "INSERT INTO juz_revisions 
                     (student_id, juz_revised, revision_date, memorization_quality, teacher_notes)
                     VALUES 
                     (:student_id, :juz_revised, :revision_date, :memorization_quality, :teacher_notes)";
                     
            $stmt = $this->db->prepare($query);
            $result = $stmt->execute([
                ':student_id' => $data['student_id'],
                ':juz_revised' => $data['juz_revised'],
                ':revision_date' => $data['revision_date'],
                ':memorization_quality' => $data['memorization_quality'],
                ':teacher_notes' => $data['teacher_notes']
            ]);
            
            error_log("Revision addition result: " . ($result ? "success" : "failed"));
            
            return $result;
            
        } catch(PDOException $e) {
            error_log("Database error in addJuzRevision: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            throw new Exception("Database error while adding revision: " . $e->getMessage());
        }
    }

    public function getStudentHistory($studentId) {
        try {
            $query = "SELECT 
                        'progress' as source,
                        date as revision_date,
                        current_juz as juz_revised,
                        current_surah,
                        start_ayat,
                        end_ayat,
                        verses_memorized,
                        memorization_quality as quality_rating,
                        teacher_notes
                     FROM progress 
                     WHERE student_id = :student_id
                     
                     UNION ALL
                     
                     SELECT 
                        'revision' as source,
                        revision_date,
                        juz_revised,
                        NULL as current_surah,
                        NULL as start_ayat,
                        NULL as end_ayat,
                        NULL as verses_memorized,
                        memorization_quality,
                        teacher_notes
                     FROM juz_revisions
                     WHERE student_id = :student_id
                     
                     UNION ALL
                     
                     SELECT 
                        'sabaq_para' as source,
                        revision_date,
                        juz_number as juz_revised,
                        NULL as current_surah,
                        NULL as start_ayat,
                        NULL as end_ayat,
                        NULL as verses_memorized,
                        quality_rating,
                        CONCAT('Quarters Revised: ', quarters_revised, ' - ', teacher_notes) as teacher_notes
                     FROM sabaq_para
                     WHERE student_id = :student_id
                     
                     ORDER BY revision_date DESC, source DESC
                     LIMIT 50";

            $stmt = $this->db->prepare($query);
            $stmt->execute([':student_id' => $studentId]);
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return [
                'success' => true,
                'revisions' => $history
            ];

        } catch(PDOException $e) {
            error_log("Database error in getStudentHistory: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Failed to fetch history: " . $e->getMessage()
            ];
        }
    }

    public function updateTeacher($data) {
        try {
            $query = "UPDATE teachers 
                      SET name = :name,
                          phone = :phone,
                          subjects = :subjects,
                          status = :status
                      WHERE id = :id";
            
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
                      WHERE t.id = :id";
            
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
            $query = "SELECT t.id, t.name, t.phone, t.subjects, t.status,
                             u.email, u.username
                      FROM teachers t
                      JOIN users u ON t.user_id = u.id
                      WHERE t.id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $id]);
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error in getTeacherById: " . $e->getMessage());
            return false;
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
                         (id, day_of_week, time_slot) 
                         VALUES (:id, :day, :time)
                         ON DUPLICATE KEY UPDATE 
                         updated_at = CURRENT_TIMESTAMP";
                 
                $stmt = $this->db->prepare($query);
                $stmt->execute([
                    ':id' => $data['id'],
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
                      WHERE id = :id 
                      ORDER BY day_of_week, time_slot";
              
            $stmt = $this->db->prepare($query);
            $stmt->execute([':id' => $teacherId]);
            
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

            // Get total active students
            $query = "SELECT COUNT(*) as count 
                     FROM students 
                     WHERE status = 'active'";
            $stmt = $this->db->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['students'] = $result['count'];
            error_log("Students count: " . $stats['students']);

            // Get total active teachers
            $query = "SELECT COUNT(*) as count 
                     FROM teachers 
                     WHERE status = 'active'";
            $stmt = $this->db->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['teachers'] = $result['count'];
            error_log("Teachers count: " . $stats['teachers']);

            // Get today's attendance - using date instead of created_at
            $today = date('Y-m-d');
            $query = "SELECT COUNT(DISTINCT student_id) as count 
                     FROM attendance 
                     WHERE DATE(date) = :today";  // Changed from created_at to date
            $stmt = $this->db->prepare($query);
            $stmt->execute([':today' => $today]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['attendance'] = $result['count'];
            error_log("Today's attendance: " . $stats['attendance']);

            // Get active classes
            $query = "SELECT COUNT(*) as count 
                     FROM classes";
            $stmt = $this->db->query($query);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['classes'] = $result['count'];
            error_log("Active classes: " . $stats['classes']);

            error_log("Final stats array: " . print_r($stats, true));
            
            return [
                'success' => true,
                'data' => $stats
            ];

        } catch(PDOException $e) {
            error_log("Database error in getDashboardStats: " . $e->getMessage());
            error_log("Stack trace: " . $e->getTraceAsString());
            return [
                'success' => false,
                'message' => "Database error: " . $e->getMessage()
            ];
        }
    }

    public function getStudent($studentId) {
        try {
            // Debug log
            error_log("Fetching student with ID: " . $studentId);
            
            $query = "SELECT s.*, p.current_juz, p.completed_juz, p.memorization_quality, p.teacher_notes 
                     FROM students s 
                     LEFT JOIN progress p ON s.id = p.student_id 
                     WHERE s.id = :student_id";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([':student_id' => $studentId]);
            
            $student = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Debug log
            error_log("Query result: " . print_r($student, true));
            
            if (!$student) {
                error_log("No student found with ID: " . $studentId);
                throw new Exception('Student not found');
            }
            
            return $student;
        } catch(PDOException $e) {
            error_log("Database error in getStudent: " . $e->getMessage());
            throw new Exception('Database error while fetching student');
        }
    }

    public function getLatestProgress($studentId) {
        try {
            $query = "SELECT 
                        current_juz,
                        memorization_quality,
                        teacher_notes,
                        date
                     FROM progress 
                     WHERE student_id = :student_id 
                     ORDER BY date DESC, id DESC 
                     LIMIT 1";

            $stmt = $this->db->prepare($query);
            $stmt->execute([':student_id' => $studentId]);
            $progress = $stmt->fetch(PDO::FETCH_ASSOC);

            return [
                'success' => true,
                'progress' => $progress ?: [
                    'current_juz' => '1',
                    'memorization_quality' => 'average',
                    'teacher_notes' => '',
                    'date' => date('Y-m-d')
                ]
            ];

        } catch(PDOException $e) {
            error_log("Database error in getLatestProgress: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Failed to fetch latest progress"
            ];
        }
    }

    public function getAllSurahs() {
        try {
            $query = "SELECT id, surah_number, name, total_ayat FROM surah ORDER BY surah_number";
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Database error in getAllSurahs: " . $e->getMessage());
            return [];
        }
    }

    public function getSurahsByJuz($juzNumber) {
        try {
            error_log("Fetching surahs for juz: " . $juzNumber);
            
            // First get the surah list for this juz
            $query = "SELECT surah_list FROM juz WHERE juz_number = :juz_number";
            $stmt = $this->db->prepare($query);
            $stmt->execute([':juz_number' => $juzNumber]);
            $juzData = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if (!$juzData) {
                return [
                    'success' => false,
                    'message' => "Juz not found"
                ];
            }
            
            // Convert surah list string to array
            $surahIds = explode(',', $juzData['surah_list']);
            $surahIds = array_map('trim', $surahIds);
            
            // Get surah details
            $placeholders = str_repeat('?,', count($surahIds) - 1) . '?';
            $query = "SELECT 
                        id as surah_id,
                        name,
                        total_ayat as end_ayat
                     FROM surah 
                     WHERE surah_number IN ($placeholders)
                     ORDER BY surah_number";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute($surahIds);
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Add start_ayat (1 for all surahs in this simple version)
            foreach ($results as &$surah) {
                $surah['start_ayat'] = 1;
            }
            
            return [
                'success' => true,
                'surahs' => $results
            ];
            
        } catch(PDOException $e) {
            error_log("Database error in getSurahsByJuz: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Database error: " . $e->getMessage()
            ];
        }
    }

    public function updateStudentProgress($studentId, $currentJuz, $currentSurah, $startAyat, $endAyat, $quality, $notes) {
        try {
            // Calculate verses memorized
            $verses_memorized = $endAyat - $startAyat; // Add 1 because it's inclusive
            error_log("Calculated verses memorized: $verses_memorized (from ayat $startAyat to $endAyat)");

            $query = "INSERT INTO progress 
                        (student_id, current_juz, current_surah, start_ayat, end_ayat, 
                         verses_memorized, memorization_quality, teacher_notes, date) 
                     VALUES 
                        (:student_id, :current_juz, :current_surah, :start_ayat, :end_ayat,
                         :verses_memorized, :quality, :notes, CURRENT_DATE)";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute([
                ':student_id' => $studentId,
                ':current_juz' => $currentJuz,
                ':current_surah' => $currentSurah,
                ':start_ayat' => $startAyat,
                ':end_ayat' => $endAyat,
                ':verses_memorized' => $verses_memorized,
                ':quality' => $quality,
                ':notes' => $notes
            ]);
            
            return [
                'success' => true,
                'message' => 'Progress updated successfully',
                'verses_memorized' => $verses_memorized
            ];
        } catch(PDOException $e) {
            error_log("Database error in updateStudentProgress: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Failed to update progress: " . $e->getMessage()
            ];
        }
    }

    public function addSabaqPara($studentId, $juzNumber, $quartersRevised, $qualityRating, $notes) {
        try {
            // Validate inputs
            if (!$studentId || !$juzNumber || !$quartersRevised || !$qualityRating) {
                throw new Exception("All required fields must be provided");
            }

            // Insert the sabaq para record
            $query = "INSERT INTO sabaq_para 
                        (student_id, juz_number, quarters_revised, quality_rating, teacher_notes, revision_date) 
                     VALUES 
                        (:student_id, :juz_number, :quarters_revised, :quality_rating, :notes, CURRENT_DATE)";
            
            $stmt = $this->db->prepare($query);
            $params = [
                ':student_id' => $studentId,
                ':juz_number' => $juzNumber,
                ':quarters_revised' => $quartersRevised,
                ':quality_rating' => $qualityRating,
                ':notes' => $notes
            ];

            // Log the query and parameters for debugging
            error_log("Adding Sabaq Para with params: " . print_r($params, true));
            
            $stmt->execute($params);
            
            return [
                'success' => true,
                'message' => 'Sabaq Para record added successfully'
            ];
            
        } catch(PDOException $e) {
            error_log("Database error in addSabaqPara: " . $e->getMessage());
            return [
                'success' => false,
                'message' => "Database error: " . $e->getMessage()
            ];
        } catch(Exception $e) {
            error_log("Error in addSabaqPara: " . $e->getMessage());
            return [
                'success' => false,
                'message' => $e->getMessage()
            ];
        }
    }
}

?> 