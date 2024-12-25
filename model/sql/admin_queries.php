function getAllTeachers() {
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