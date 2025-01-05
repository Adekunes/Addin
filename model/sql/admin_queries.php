<?php function getAllTeachers() {
    global $db;
    
    $query = "SELECT t.teacher_id as id, t.name, t.phone, t.subjects, t.status, 
              u.email, u.username 
              FROM teachers t 
              JOIN users u ON t.user_id = u.id 
              WHERE u.role = 'teacher'
              ORDER BY t.name ASC";
              
    $stmt = $db->prepare($query);
    $stmt->execute();
    
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
} 

 function getStudents() {
    global $db;
        try {
            $query = "SELECT 
                        s.id,
                        s.name,
                        s.guardian_name,
                        s.guardian_contact,
                        s.enrollment_date,
                        s.status,
                        c.name as class_name,
                        COALESCE(p.verses_memorized, 0) as verses_memorized,
                        COALESCE(p.quality_rating, 'Not Rated') as quality_rating
                    FROM students s
                    LEFT JOIN classes c ON s.class_id = c.id
                    LEFT JOIN progress p ON s.id = p.student_id
                    WHERE s.status = 'active'
                    ORDER BY s.name ASC";
            
            $stmt = $this->db->prepare($query);
            $stmt->execute();
            
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch(PDOException $e) {
            error_log("Error fetching students: " . $e->getMessage());
            return [];
        }
    }
?>