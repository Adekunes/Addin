-- Query to get teacher's classes
SELECT class_id, class_name, schedule
FROM classes
WHERE teacher_id = ?;

-- Query to get students in a class
SELECT student_id, student_name
FROM students
WHERE class_id = ?;

-- Query to insert attendance record
INSERT INTO attendance (student_id, class_id, date, status)
VALUES (?, ?, ?, ?);

-- Query to update student progress
UPDATE student_progress
SET current_surah = ?, verses_memorized = ?, last_revision_date = ?
WHERE student_id = ?;

-- Query to get teacher's dashboard data
SELECT 
    COUNT(DISTINCT s.student_id) as total_students,
    AVG(a.status = 'present') as average_attendance,
    AVG(sp.verses_memorized) as average_progress
FROM classes c
JOIN students s ON c.class_id = s.class_id
LEFT JOIN attendance a ON s.student_id = a.student_id
LEFT JOIN student_progress sp ON s.student_id = sp.student_id
WHERE c.teacher_id = ?;

-- Add more queries as needed for teacher functionality