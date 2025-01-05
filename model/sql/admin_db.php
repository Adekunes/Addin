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

    public function updateStudent($id, $currentJuz, $completedJuz, $status) {
        try {
            $query = "UPDATE students 
                      SET current_juz = :current_juz,
                          completed_juz = :completed_juz,
                          status = :status
                      WHERE id = :id";
            
            $stmt = $this->db->prepare($query);
            $stmt->bindParam(':id', $id);
            $stmt->bindParam(':current_juz', $currentJuz);
            $stmt->bindParam(':completed_juz', $completedJuz);
            $stmt->bindParam(':status', $status);
            
            return $stmt->execute();
        } catch(PDOException $e) {
            error_log("Error updating student: " . $e->getMessage());
            return false;
        }
    }
}

?> 