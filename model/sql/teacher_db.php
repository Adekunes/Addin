<?php

class TeacherDatabase {
    public function getTeacherStudents($teacherId) {
        try {
            $query = "SELECT s.* 
                      FROM students s
                      JOIN teacher_students ts ON s.student_id = ts.student_id
                      WHERE ts.teacher_id = :teacher_id
                      ORDER BY s.name";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute(['teacher_id' => $teacherId]);
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log("Error getting teacher's students: " . $e->getMessage());
            return [];
        }
    }
} 