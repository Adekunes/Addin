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
}
?> 