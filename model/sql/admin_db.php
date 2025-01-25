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
            $query = "SELECT id, name, current_juz, completed_juz, status 
                      FROM students 
                      WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->execute();
            
            return $stmt->fetch(PDO::FETCH_ASSOC);
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
            $query = "SELECT 
                s.id,
                s.name,
                s.guardian_name,
                s.guardian_contact,
                s.enrollment_date,
                s.status,
                p.current_juz,
                p.completed_juz,
                p.last_completed_surah,
                p.memorization_quality,
                p.tajweed_level,
                p.revision_status,
                p.last_revision_date,
                p.teacher_notes
            FROM students s
            LEFT JOIN progress p ON s.id = p.student_id
                AND p.date = (
                    SELECT MAX(date)
                    FROM progress
                    WHERE student_id = s.id
                )
            ORDER BY s.name ASC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
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
}

?> 