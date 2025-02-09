CREATE TABLE teacher_schedule (
    id INT PRIMARY KEY AUTO_INCREMENT,
    teacher_id INT NOT NULL,
    day_of_week ENUM('monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday') NOT NULL,
    time_slot TIME NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (teacher_id) REFERENCES teachers(teacher_id) ON DELETE CASCADE,
    UNIQUE KEY unique_schedule (teacher_id, day_of_week, time_slot)
);

-- Insert default schedule for teacher_id 1 (adjust the teacher_id if needed)
INSERT INTO teacher_schedule (teacher_id, day_of_week, time_slot) VALUES
-- Monday schedule
(1, 'monday', '08:00:00'),
(1, 'monday', '08:30:00'),
(1, 'monday', '09:00:00'),
(1, 'monday', '09:30:00'),

-- Tuesday schedule
(1, 'tuesday', '08:00:00'),
(1, 'tuesday', '08:30:00'),
(1, 'tuesday', '09:00:00'),
(1, 'tuesday', '09:30:00'),

-- Wednesday schedule
(1, 'wednesday', '14:00:00'),
(1, 'wednesday', '14:30:00'),
(1, 'wednesday', '15:00:00'),
(1, 'wednesday', '15:30:00'),

-- Thursday schedule
(1, 'thursday', '14:00:00'),
(1, 'thursday', '14:30:00'),
(1, 'thursday', '15:00:00'),
(1, 'thursday', '15:30:00'),

-- Friday schedule
(1, 'friday', '10:00:00'),
(1, 'friday', '10:30:00'),
(1, 'friday', '11:00:00'),
(1, 'friday', '11:30:00'); 