ALTER TABLE progress 
DROP COLUMN current_surah,
DROP COLUMN verses_memorized,
ADD COLUMN current_juz INT DEFAULT 1,
ADD COLUMN completed_juz INT DEFAULT 0,
ADD COLUMN last_completed_surah VARCHAR(100),
ADD COLUMN memorization_quality ENUM('excellent', 'good', 'average', 'needsWork') DEFAULT 'average',
ADD COLUMN tajweed_level ENUM('beginner', 'intermediate', 'advanced') DEFAULT 'beginner',
ADD COLUMN revision_status VARCHAR(50),
ADD COLUMN teacher_notes TEXT,
ADD COLUMN last_revision_date DATE;

-- First, let's drop the table if it exists and recreate it
DROP TABLE IF EXISTS juz_revisions;

CREATE TABLE juz_revisions (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    juz_revised INT NOT NULL,
    revision_date DATE NOT NULL,
    quality_rating ENUM('excellent', 'good', 'average', 'needsWork') NOT NULL,
    teacher_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY unique_revision (student_id, juz_revised, revision_date)
);

-- Create a table to track overall juz mastery
CREATE TABLE juz_mastery (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    juz_number INT NOT NULL,
    mastery_level ENUM('not_started', 'in_progress', 'memorized', 'mastered') DEFAULT 'not_started',
    last_revision_date DATE,
    revision_count INT DEFAULT 0,
    consecutive_good_revisions INT DEFAULT 0,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    UNIQUE KEY unique_student_juz (student_id, juz_number)
);

-- Add indexes for better query performance
CREATE INDEX idx_revision_date ON juz_revisions(revision_date);
CREATE INDEX idx_student_juz ON juz_revisions(student_id, juz_revised);
CREATE INDEX idx_mastery_student ON juz_mastery(student_id);

UPDATE juz_mastery 
SET mastery_level = 'mastered',
    last_revision_date = CURRENT_DATE,
    revision_count = revision_count + 1,
    consecutive_good_revisions = CASE 
        WHEN quality_rating IN ('excellent', 'good') THEN consecutive_good_revisions + 1
        ELSE 0
    END
WHERE student_id = 1 AND juz_number = 1; 