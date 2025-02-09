-- Create Database
CREATE DATABASE IF NOT EXISTS hifz_system;
USE hifz_system;

-- Users Table
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    role ENUM('admin', 'teacher') NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Teachers Table
CREATE TABLE teachers (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT,
    name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    subjects TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    FOREIGN KEY (user_id) REFERENCES users(id)
);

-- Students Table
CREATE TABLE students (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    date_of_birth DATE,
    guardian_name VARCHAR(100),
    guardian_contact VARCHAR(20),
    enrollment_date DATE,
    status ENUM('active', 'inactive') DEFAULT 'active'
);

-- Classes Table
CREATE TABLE classes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    teacher_id INT,
    schedule TEXT,
    FOREIGN KEY (teacher_id) REFERENCES teachers(id)
);

-- Attendance Table
CREATE TABLE attendance (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    class_id INT,
    date DATE,
    status ENUM('present', 'absent', 'late') NOT NULL,
    notes TEXT,
    FOREIGN KEY (student_id) REFERENCES students(id),
    FOREIGN KEY (class_id) REFERENCES classes(id)
);

-- Progress Table
CREATE TABLE progress (
    id INT AUTO_INCREMENT PRIMARY KEY,
    student_id INT,
    current_surah INT,
    current_juz INT,
    completed_juz INT,
    verses_memorized INT,
    quality_rating ENUM('excellent', 'good', 'average', 'needsWork', 'not_rated') NOT NULL DEFAULT 'not_rated',
    tajweed_level ENUM('beginner', 'intermediate', 'advanced'),
    teacher_notes TEXT,
    date DATE,
    FOREIGN KEY (student_id) REFERENCES students(id)
);

-- Add any missing columns if needed
ALTER TABLE progress 
    ADD COLUMN IF NOT EXISTS current_juz INT AFTER student_id,
    ADD COLUMN IF NOT EXISTS completed_juz INT AFTER current_juz,
    ADD COLUMN IF NOT EXISTS teacher_notes TEXT AFTER quality_rating;