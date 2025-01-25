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