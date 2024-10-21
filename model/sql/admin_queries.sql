-- Query to get all teachers
SELECT teacher_id, name, email, hire_date
FROM teachers;

-- Query to get all students
SELECT student_id, name, class_id, enrollment_date
FROM students;

-- Query to add a new teacher
INSERT INTO teachers (name, email, password, hire_date)
VALUES (?, ?, ?, ?);

-- Query to add a new student
INSERT INTO students (name, class_id, enrollment_date)
VALUES (?, ?, ?);

-- Query to get system overview
SELECT 
    (SELECT COUNT(*) FROM teachers) as teacher_count,
    (SELECT COUNT(*) FROM students) as student_count,
    (SELECT COUNT(*) FROM classes) as class_count;

-- Add more queries as needed for admin functionality