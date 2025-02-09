ALTER TABLE progress 
DROP COLUMN current_surah,
DROP COLUMN verses_memorized,
ADD COLUMN current_juz INT DEFAULT 1,
ADD COLUMN completed_juz INT DEFAULT 0,
ADD COLUMN last_completed_surah VARCHAR(100),
ADD COLUMN memorization_quality ENUM('excellent', 'good', 'average', 'needsWork', 'horrible') DEFAULT 'average',
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
    quality_rating ENUM('excellent', 'good', 'average', 'needsWork', 'horrible') NOT NULL,
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

-- Update the existing records to handle any potential data inconsistencies
UPDATE progress 
SET memorization_quality = 'average' 
WHERE memorization_quality IS NULL;

UPDATE juz_revisions 
SET quality_rating = 'average' 
WHERE quality_rating NOT IN ('excellent', 'good', 'average', 'needsWork', 'horrible'); 



CREATE TABLE surah (
    id INT AUTO_INCREMENT PRIMARY KEY,
    surah_number INT UNIQUE NOT NULL,
    name VARCHAR(100) NOT NULL,
    total_ayat INT NOT NULL
);

INSERT INTO surah (surah_number, name, total_ayat) VALUES
(1, 'Al-Fatiha', 7),
(2, 'Al-Baqarah', 286),
(3, 'Aal-E-Imran', 200),
(4, 'An-Nisa', 176),
(5, 'Al-Ma\'idah', 120),
(6, 'Al-An\'am', 165),
(7, 'Al-A\'raf', 206),
(8, 'Al-Anfal', 75),
(9, 'At-Tawbah', 129),
(10, 'Yunus', 109),
(11, 'Hud', 123),
(12, 'Yusuf', 111),
(13, 'Ar-Ra\'d', 43),
(14, 'Ibrahim', 52),
(15, 'Al-Hijr', 99),
(16, 'An-Nahl', 128),
(17, 'Al-Isra', 111),
(18, 'Al-Kahf', 110),
(19, 'Maryam', 98),
(20, 'Taha', 135),
(21, 'Al-Anbiya', 112),
(22, 'Al-Hajj', 78),
(23, 'Al-Mu\'minun', 118),
(24, 'An-Nur', 64),
(25, 'Al-Furqan', 77),
(26, 'Ash-Shu\'ara', 227),
(27, 'An-Naml', 93),
(28, 'Al-Qasas', 88),
(29, 'Al-Ankabut', 69),
(30, 'Ar-Rum', 60),
(31, 'Luqman', 34),
(32, 'As-Sajda', 30),
(33, 'Al-Ahzab', 73),
(34, 'Saba', 54),
(35, 'Fatir', 45),
(36, 'Ya-Sin', 83),
(37, 'As-Saffat', 182),
(38, 'Sad', 88),
(39, 'Az-Zumar', 75),
(40, 'Ghafir', 85),
(41, 'Fussilat', 54),
(42, 'Ash-Shura', 53),
(43, 'Az-Zukhruf', 89),
(44, 'Ad-Dukhan', 59),
(45, 'Al-Jathiya', 37),
(46, 'Al-Ahqaf', 35),
(47, 'Muhammad', 38),
(48, 'Al-Fath', 29),
(49, 'Al-Hujurat', 18),
(50, 'Qaf', 45),
(51, 'Adh-Dhariyat', 60),
(52, 'At-Tur', 49),
(53, 'An-Najm', 62),
(54, 'Al-Qamar', 55),
(55, 'Ar-Rahman', 78),
(56, 'Al-Waqi\'a', 96),
(57, 'Al-Hadid', 29),
(58, 'Al-Mujadila', 22),
(59, 'Al-Hashr', 24),
(60, 'Al-Mumtahina', 13),
(61, 'As-Saff', 14),
(62, 'Al-Jumu\'ah', 11),
(63, 'Al-Munafiqun', 11),
(64, 'At-Taghabun', 18),
(65, 'At-Talaq', 12),
(66, 'At-Tahrim', 12),
(67, 'Al-Mulk', 30),
(68, 'Al-Qalam', 52),
(69, 'Al-Haqqah', 52),
(70, 'Al-Ma\'arij', 44),
(71, 'Nuh', 28),
(72, 'Al-Jinn', 28),
(73, 'Al-Muzzammil', 20),
(74, 'Al-Muddathir', 56),
(75, 'Al-Qiyamah', 40),
(76, 'Al-Insan', 31),
(77, 'Al-Mursalat', 50),
(78, 'An-Naba', 40),
(79, 'An-Nazi\'at', 46),
(80, 'Abasa', 42),
(81, 'At-Takwir', 29),
(82, 'Al-Infitar', 19),
(83, 'Al-Mutaffifin', 36),
(84, 'Al-Inshiqaq', 25),
(85, 'Al-Buruj', 22),
(86, 'At-Tariq', 17),
(87, 'Al-A\'la', 19),
(88, 'Al-Ghashiyah', 26),
(89, 'Al-Fajr', 30),
(90, 'Al-Balad', 20),
(91, 'Ash-Shams', 15),
(92, 'Al-Layl', 21),
(93, 'Ad-Duha', 11),
(94, 'Ash-Sharh', 8),
(95, 'At-Tin', 8),
(96, 'Al-Alaq', 19),
(97, 'Al-Qadr', 5),
(98, 'Al-Bayyinah', 8),
(99, 'Az-Zalzalah', 8),
(100, 'Al-Adiyat', 11),
(101, 'Al-Qari\'ah', 11),
(102, 'At-Takathur', 8),
(103, 'Al-Asr', 3),
(104, 'Al-Humazah', 9),
(105, 'Al-Fil', 5),
(106, 'Quraysh', 4),
(107, "Al-Ma\'un", 7),
(108, 'Al-Kawthar', 3),
(109, 'Al-Kafirun', 6),
(110, 'An-Nasr', 3),
(111, 'Al-Masad', 5),
(112, 'Al-Ikhlas', 4),
(113, 'Al-Falaq', 5),
(114, 'An-Nas', 6);

CREATE TABLE juz (
    id INT AUTO_INCREMENT PRIMARY KEY,
    juz_number INT UNIQUE NOT NULL,
    surah_list TEXT NOT NULL
);

INSERT INTO juz (juz_number, surah_list) VALUES
(1, '1, 2'),
(2, '2'),
(3, '2, 3'),
(4, '3, 4'),
(5, '4'),
(6, '4, 5'),
(7, '5, 6'),
(8, '6, 7'),
(9, '7, 8'),
(10, '8, 9'),
(11, '9, 10, 11'),
(12, '11, 12'),
(13, '12, 13, 14'),
(14, '15, 16'),
(15, '17, 18'),
(16, '18, 19, 20'),
(17, '21, 22'),
(18, '23, 24, 25'),
(19, '25, 26, 27'),
(20, '27, 28, 29'),
(21, '29, 30, 31, 32, 33'),
(22, '33, 34, 35, 36'),
(23, '36, 37, 38, 39'),
(24, '39, 40, 41'),
(25, '41, 42, 43, 44, 45'),
(26, '46, 47, 48, 49, 50, 51'),
(27, '51, 52, 53, 54, 55, 56, 57'),
(28, '58, 59, 60, 61, 62, 63, 64, 65, 66'),
(29, '67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77'),
(30, '78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114');

-- Create table for sabaq para (short term revision)
CREATE TABLE sabaq_para (
    id INT PRIMARY KEY AUTO_INCREMENT,
    student_id INT NOT NULL,
    juz_number INT NOT NULL,
    quarters_revised ENUM('1st_quarter', '2_quarters', '3_quarters', '4_quarters') NOT NULL,
    revision_date DATE NOT NULL,
    quality_rating ENUM('excellent', 'good', 'average', 'needsWork', 'horrible') NOT NULL,
    teacher_notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (student_id) REFERENCES students(id) ON DELETE CASCADE,
    INDEX idx_student_juz (student_id, juz_number)
);