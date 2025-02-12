-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 09, 2025 at 03:52 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `hifz_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `class_id` int(11) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `status` enum('present','absent','late') NOT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ayat`
--

CREATE TABLE `ayat` (
  `id` int(11) NOT NULL,
  `surah_id` int(11) NOT NULL,
  `ayah_number` int(11) NOT NULL,
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `teacher_id` int(11) DEFAULT NULL,
  `schedule` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `juz`
--

CREATE TABLE `juz` (
  `id` int(11) NOT NULL,
  `juz_number` int(11) NOT NULL,
  `surah_list` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `juz`
--

INSERT INTO `juz` (`id`, `juz_number`, `surah_list`) VALUES
(1, 1, '1, 2'),
(2, 2, '2'),
(3, 3, '2, 3'),
(4, 4, '3, 4'),
(5, 5, '4'),
(6, 6, '4, 5'),
(7, 7, '5, 6'),
(8, 8, '6, 7'),
(9, 9, '7, 8'),
(10, 10, '8, 9'),
(11, 11, '9, 10, 11'),
(12, 12, '11, 12'),
(13, 13, '12, 13, 14'),
(14, 14, '15, 16'),
(15, 15, '17, 18'),
(16, 16, '18, 19, 20'),
(17, 17, '21, 22'),
(18, 18, '23, 24, 25'),
(19, 19, '25, 26, 27'),
(20, 20, '27, 28, 29'),
(21, 21, '29, 30, 31, 32, 33'),
(22, 22, '33, 34, 35, 36'),
(23, 23, '36, 37, 38, 39'),
(24, 24, '39, 40, 41'),
(25, 25, '41, 42, 43, 44, 45'),
(26, 26, '46, 47, 48, 49, 50, 51'),
(27, 27, '51, 52, 53, 54, 55, 56, 57'),
(28, 28, '58, 59, 60, 61, 62, 63, 64, 65, 66'),
(29, 29, '67, 68, 69, 70, 71, 72, 73, 74, 75, 76, 77'),
(30, 30, '78, 79, 80, 81, 82, 83, 84, 85, 86, 87, 88, 89, 90, 91, 92, 93, 94, 95, 96, 97, 98, 99, 100, 101, 102, 103, 104, 105, 106, 107, 108, 109, 110, 111, 112, 113, 114');

-- --------------------------------------------------------

--
-- Table structure for table `juz_mastery`
--

CREATE TABLE `juz_mastery` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `juz_number` int(11) NOT NULL,
  `mastery_level` enum('not_started','in_progress','memorized','mastered') DEFAULT 'not_started',
  `last_revision_date` date DEFAULT NULL,
  `revision_count` int(11) DEFAULT 0,
  `consecutive_good_revisions` int(11) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `juz_revisions`
--

CREATE TABLE `juz_revisions` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `juz_revised` int(11) NOT NULL,
  `revision_date` date NOT NULL,
  `teacher_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `memorization_quality` enum('excellent','good','average','needsWork','horrible') DEFAULT 'average'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `juz_revisions`
--

INSERT INTO `juz_revisions` (`id`, `student_id`, `juz_revised`, `revision_date`, `teacher_notes`, `created_at`, `memorization_quality`) VALUES
(1, 1, 30, '2025-02-01', '', '2025-02-01 00:25:40', NULL),
(2, 3, 30, '2025-02-01', '', '2025-02-01 00:25:53', NULL),
(3, 1, 29, '2025-02-02', '', '2025-02-02 01:59:06', NULL),
(4, 4, 9, '2025-02-03', '', '2025-02-03 21:29:09', NULL),
(5, 1, 28, '2025-02-05', '', '2025-02-04 23:04:02', NULL),
(6, 4, 11, '2025-02-05', '', '2025-02-04 23:57:55', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `progress`
--

CREATE TABLE `progress` (
  `id` int(11) NOT NULL,
  `student_id` int(11) DEFAULT NULL,
  `current_surah` int(11) DEFAULT NULL,
  `start_ayat` int(11) DEFAULT NULL,
  `end_ayat` int(11) DEFAULT NULL,
  `verses_memorized` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `date` date DEFAULT NULL,
  `current_juz` int(11) DEFAULT NULL,
  `completed_juz` int(11) DEFAULT NULL,
  `last_completed_surah` varchar(100) DEFAULT NULL,
  `memorization_quality` enum('excellent','good','average','needsWork','horrible') DEFAULT 'average',
  `tajweed_level` varchar(50) DEFAULT NULL,
  `revision_status` varchar(50) DEFAULT NULL,
  `last_revision_date` date DEFAULT NULL,
  `teacher_notes` text DEFAULT NULL,
  `quality_rating` enum('excellent','good','average','needsWork','horrible') DEFAULT 'average'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `progress`
--

INSERT INTO `progress` (`id`, `student_id`, `current_surah`, `start_ayat`, `end_ayat`, `verses_memorized`, `notes`, `date`, `current_juz`, `completed_juz`, `last_completed_surah`, `memorization_quality`, `tajweed_level`, `revision_status`, `last_revision_date`, `teacher_notes`, `quality_rating`) VALUES
(1, 3, NULL, NULL, NULL, NULL, NULL, NULL, 4, 4, '', 'good', 'beginner', '', NULL, '', 'average'),
(3, 1, NULL, NULL, NULL, NULL, NULL, '2025-01-30', 10, NULL, NULL, 'average', NULL, NULL, NULL, '', 'average'),
(4, 1, NULL, NULL, NULL, NULL, NULL, '2025-01-30', 5, NULL, NULL, 'average', NULL, NULL, NULL, '', 'average'),
(5, 1, NULL, NULL, NULL, NULL, NULL, '2025-01-30', 5, NULL, NULL, 'average', NULL, NULL, NULL, '', 'average'),
(6, 1, NULL, NULL, NULL, NULL, NULL, '2025-01-30', 2, NULL, NULL, 'average', NULL, NULL, NULL, '', 'average'),
(7, 1, NULL, NULL, NULL, NULL, NULL, '2025-01-30', 2, NULL, NULL, 'average', NULL, NULL, NULL, '', 'average'),
(8, 1, NULL, NULL, NULL, NULL, NULL, '2025-01-30', 14, NULL, NULL, 'average', NULL, NULL, NULL, '', 'average'),
(9, 1, NULL, NULL, NULL, NULL, NULL, '2025-01-30', 14, NULL, NULL, 'average', NULL, NULL, NULL, '', 'average'),
(10, 1, NULL, NULL, NULL, NULL, NULL, '2025-01-30', 23, NULL, NULL, 'average', NULL, NULL, NULL, '', 'average'),
(11, 1, NULL, NULL, NULL, NULL, NULL, '2025-01-30', 23, NULL, NULL, 'average', NULL, NULL, NULL, '', 'average'),
(12, 1, NULL, NULL, NULL, NULL, NULL, '2025-01-30', 15, NULL, NULL, 'average', NULL, NULL, NULL, '', 'average'),
(13, 1, NULL, NULL, NULL, NULL, NULL, '2025-01-30', 15, NULL, NULL, 'average', NULL, NULL, NULL, '', 'average'),
(14, 1, NULL, NULL, NULL, NULL, NULL, '2025-02-01', 15, NULL, NULL, 'average', NULL, NULL, NULL, '', 'average'),
(15, 1, 17, 12, 13, NULL, NULL, '2025-02-01', 15, NULL, NULL, 'average', NULL, NULL, NULL, '', 'average'),
(16, 3, 3, 1, 10, NULL, NULL, '2025-02-01', 4, NULL, NULL, 'good', NULL, NULL, NULL, '', 'average'),
(17, 3, 3, 25, 35, 11, NULL, '2025-02-01', 4, NULL, NULL, 'good', NULL, NULL, NULL, '', 'average'),
(18, 1, 2, 128, 140, 12, NULL, '2025-02-01', 2, NULL, NULL, 'average', NULL, NULL, NULL, '', 'average'),
(19, 4, NULL, NULL, NULL, NULL, NULL, NULL, 10, 8, '', 'average', 'intermediate', '', NULL, '', 'average'),
(20, 4, NULL, NULL, NULL, NULL, NULL, '2025-02-03', 10, NULL, NULL, 'excellent', NULL, NULL, NULL, '', 'average'),
(21, 4, 8, 1, 15, NULL, NULL, '2025-02-03', 10, NULL, NULL, 'excellent', NULL, NULL, NULL, '', 'average'),
(22, 5, NULL, NULL, NULL, NULL, NULL, NULL, 3, 5, '', 'excellent', 'intermediate', '', NULL, '', 'average'),
(23, 6, NULL, NULL, NULL, NULL, NULL, NULL, 9, 6, '', 'good', 'beginner', '', NULL, '', 'average'),
(24, 4, 8, 1, 12, NULL, NULL, '2025-02-04', 10, NULL, NULL, 'excellent', NULL, NULL, NULL, '', 'average'),
(25, 4, 8, 1, 12, NULL, NULL, '2025-02-04', 10, NULL, NULL, 'excellent', NULL, NULL, NULL, '', 'average'),
(26, 1, 2, 8, 15, NULL, NULL, '2025-02-05', 2, NULL, NULL, 'average', NULL, NULL, NULL, '', 'average'),
(27, 1, 2, 1, 10, NULL, NULL, '2025-02-05', 3, NULL, NULL, 'average', NULL, NULL, NULL, '', 'average'),
(28, 1, 3, 1, 13, NULL, NULL, '2025-02-05', 3, NULL, NULL, 'average', NULL, NULL, NULL, '', 'average'),
(29, 1, 0, 0, 0, NULL, NULL, '2025-02-05', 3, NULL, NULL, 'excellent', NULL, NULL, NULL, '', 'average'),
(30, 1, 2, 39, 65, NULL, NULL, '2025-02-05', 3, NULL, NULL, 'excellent', NULL, NULL, NULL, '', 'average'),
(31, 7, NULL, NULL, NULL, NULL, NULL, NULL, 28, 28, '', 'good', 'beginner', '', NULL, '', 'average'),
(32, 8, NULL, NULL, NULL, NULL, NULL, NULL, 1, 1, '', 'excellent', 'beginner', '', NULL, '', 'average');

-- --------------------------------------------------------

--
-- Table structure for table `sabaq_para`
--

CREATE TABLE `sabaq_para` (
  `id` int(11) NOT NULL,
  `student_id` int(11) NOT NULL,
  `juz_number` int(11) NOT NULL,
  `quarters_revised` enum('1st_quarter','2_quarters','3_quarters','4_quarters') NOT NULL,
  `revision_date` date NOT NULL,
  `quality_rating` enum('excellent','good','average','needsWork','horrible') NOT NULL,
  `teacher_notes` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sabaq_para`
--

INSERT INTO `sabaq_para` (`id`, `student_id`, `juz_number`, `quarters_revised`, `revision_date`, `quality_rating`, `teacher_notes`, `created_at`) VALUES
(1, 1, 2, '1st_quarter', '2025-02-01', 'excellent', '', '2025-02-02 02:40:33'),
(2, 4, 10, '1st_quarter', '2025-02-03', 'excellent', '', '2025-02-03 21:42:16'),
(3, 1, 3, '2_quarters', '2025-02-09', 'excellent', '', '2025-02-09 14:12:00');

-- --------------------------------------------------------

--
-- Table structure for table `students`
--

CREATE TABLE `students` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `date_of_birth` date DEFAULT NULL,
  `guardian_name` varchar(100) DEFAULT NULL,
  `guardian_contact` varchar(20) DEFAULT NULL,
  `enrollment_date` date DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `students`
--

INSERT INTO `students` (`id`, `name`, `date_of_birth`, `guardian_name`, `guardian_contact`, `enrollment_date`, `status`) VALUES
(1, 'asdasd asdsadsa', '2024-12-30', 'asdsa', '911', '2025-01-29', 'active'),
(3, 'lol toto', '2025-01-01', 'nomi', '911', '2025-01-29', 'active'),
(4, 'testing mic', '2025-02-04', 'popo', '111', '2025-02-03', 'active'),
(5, 'new stud', '2025-02-12', 'salo', '111111', '2025-02-04', 'active'),
(6, 'cool guy cool', '2025-02-05', 'mini', '9888', '2025-02-04', 'active'),
(7, 'yola yolu', '2025-02-02', 'moumou', '119', '2025-02-09', 'active'),
(8, 'huehuehuhue huuhuhu', '2025-02-03', 'nani', '001', '2025-02-09', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `surah`
--

CREATE TABLE `surah` (
  `id` int(11) NOT NULL,
  `surah_number` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `total_ayat` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `surah`
--

INSERT INTO `surah` (`id`, `surah_number`, `name`, `total_ayat`) VALUES
(1, 1, 'Al-Fatiha', 7),
(2, 2, 'Al-Baqarah', 286),
(3, 3, 'Aal-E-Imran', 200),
(4, 4, 'An-Nisa', 176),
(5, 5, 'Al-Ma\'idah', 120),
(6, 6, 'Al-An\'am', 165),
(7, 7, 'Al-A\'raf', 206),
(8, 8, 'Al-Anfal', 75),
(9, 9, 'At-Tawbah', 129),
(10, 10, 'Yunus', 109),
(11, 11, 'Hud', 123),
(12, 12, 'Yusuf', 111),
(13, 13, 'Ar-Ra\'d', 43),
(14, 14, 'Ibrahim', 52),
(15, 15, 'Al-Hijr', 99),
(16, 16, 'An-Nahl', 128),
(17, 17, 'Al-Isra', 111),
(18, 18, 'Al-Kahf', 110),
(19, 19, 'Maryam', 98),
(20, 20, 'Taha', 135),
(21, 21, 'Al-Anbiya', 112),
(22, 22, 'Al-Hajj', 78),
(23, 23, 'Al-Mu\'minun', 118),
(24, 24, 'An-Nur', 64),
(25, 25, 'Al-Furqan', 77),
(26, 26, 'Ash-Shu\'ara', 227),
(27, 27, 'An-Naml', 93),
(28, 28, 'Al-Qasas', 88),
(29, 29, 'Al-Ankabut', 69),
(30, 30, 'Ar-Rum', 60),
(31, 31, 'Luqman', 34),
(32, 32, 'As-Sajda', 30),
(33, 33, 'Al-Ahzab', 73),
(34, 34, 'Saba', 54),
(35, 35, 'Fatir', 45),
(36, 36, 'Ya-Sin', 83),
(37, 37, 'As-Saffat', 182),
(38, 38, 'Sad', 88),
(39, 39, 'Az-Zumar', 75),
(40, 40, 'Ghafir', 85),
(41, 41, 'Fussilat', 54),
(42, 42, 'Ash-Shura', 53),
(43, 43, 'Az-Zukhruf', 89),
(44, 44, 'Ad-Dukhan', 59),
(45, 45, 'Al-Jathiya', 37),
(46, 46, 'Al-Ahqaf', 35),
(47, 47, 'Muhammad', 38),
(48, 48, 'Al-Fath', 29),
(49, 49, 'Al-Hujurat', 18),
(50, 50, 'Qaf', 45),
(51, 51, 'Adh-Dhariyat', 60),
(52, 52, 'At-Tur', 49),
(53, 53, 'An-Najm', 62),
(54, 54, 'Al-Qamar', 55),
(55, 55, 'Ar-Rahman', 78),
(56, 56, 'Al-Waqi\'a', 96),
(57, 57, 'Al-Hadid', 29),
(58, 58, 'Al-Mujadila', 22),
(59, 59, 'Al-Hashr', 24),
(60, 60, 'Al-Mumtahina', 13),
(61, 61, 'As-Saff', 14),
(62, 62, 'Al-Jumu\'ah', 11),
(63, 63, 'Al-Munafiqun', 11),
(64, 64, 'At-Taghabun', 18),
(65, 65, 'At-Talaq', 12),
(66, 66, 'At-Tahrim', 12),
(67, 67, 'Al-Mulk', 30),
(68, 68, 'Al-Qalam', 52),
(69, 69, 'Al-Haqqah', 52),
(70, 70, 'Al-Ma\'arij', 44),
(71, 71, 'Nuh', 28),
(72, 72, 'Al-Jinn', 28),
(73, 73, 'Al-Muzzammil', 20),
(74, 74, 'Al-Muddathir', 56),
(75, 75, 'Al-Qiyamah', 40),
(76, 76, 'Al-Insan', 31),
(77, 77, 'Al-Mursalat', 50),
(78, 78, 'An-Naba', 40),
(79, 79, 'An-Nazi\'at', 46),
(80, 80, 'Abasa', 42),
(81, 81, 'At-Takwir', 29),
(82, 82, 'Al-Infitar', 19),
(83, 83, 'Al-Mutaffifin', 36),
(84, 84, 'Al-Inshiqaq', 25),
(85, 85, 'Al-Buruj', 22),
(86, 86, 'At-Tariq', 17),
(87, 87, 'Al-A\'la', 19),
(88, 88, 'Al-Ghashiyah', 26),
(89, 89, 'Al-Fajr', 30),
(90, 90, 'Al-Balad', 20),
(91, 91, 'Ash-Shams', 15),
(92, 92, 'Al-Layl', 21),
(93, 93, 'Ad-Duha', 11),
(94, 94, 'Ash-Sharh', 8),
(95, 95, 'At-Tin', 8),
(96, 96, 'Al-Alaq', 19),
(97, 97, 'Al-Qadr', 5),
(98, 98, 'Al-Bayyinah', 8),
(99, 99, 'Az-Zalzalah', 8),
(100, 100, 'Al-Adiyat', 11),
(101, 101, 'Al-Qari\'ah', 11),
(102, 102, 'At-Takathur', 8),
(103, 103, 'Al-Asr', 3),
(104, 104, 'Al-Humazah', 9),
(105, 105, 'Al-Fil', 5),
(106, 106, 'Quraysh', 4),
(107, 107, 'Al-Ma\'un', 7),
(108, 108, 'Al-Kawthar', 3),
(109, 109, 'Al-Kafirun', 6),
(110, 110, 'An-Nasr', 3),
(111, 111, 'Al-Masad', 5),
(112, 112, 'Al-Ikhlas', 4),
(113, 113, 'Al-Falaq', 5),
(114, 114, 'An-Nas', 6);

-- --------------------------------------------------------

--
-- Table structure for table `teachers`
--

CREATE TABLE `teachers` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `subjects` text DEFAULT NULL,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teachers`
--

INSERT INTO `teachers` (`id`, `user_id`, `name`, `phone`, `subjects`, `status`) VALUES
(1, 2, 'John Doom', '514-123-4567', 'Quran, Tajweed, Islamic Studies', 'active'),
(2, 3, 'allo byebye', '911', 'hifz', 'active');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `role` enum('admin','teacher') NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `role`, `created_at`) VALUES
(1, 'admin', '$2y$10$9NLAVSiEQEDr3PipAQ.sCufhwDU.ZmXBClUUNlq9Gos2lIUzBTFYO', 'admin@daralulum.com', 'admin', '2025-01-29 01:35:14'),
(2, 'teacher1', '$2y$10$8puEcYsA7rfqLPw1iWMUyOCbNtw7.WqNQZS2OMgh65uH/Z/ewXo9u', 'teacher1@daralulum.com', 'teacher', '2025-01-29 01:35:25'),
(3, 'testtest', '$2y$10$qBBfzd1DE1.pxciW9f5Y/usB1fy5wcszKTk5cGdmFiXve6bpjZal.', 'test@tes.com', 'teacher', '2025-02-02 02:06:38');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `class_id` (`class_id`);

--
-- Indexes for table `ayat`
--
ALTER TABLE `ayat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `surah_id` (`surah_id`);

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `teacher_id` (`teacher_id`);

--
-- Indexes for table `juz`
--
ALTER TABLE `juz`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `juz_number` (`juz_number`);

--
-- Indexes for table `juz_mastery`
--
ALTER TABLE `juz_mastery`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_student_juz` (`student_id`,`juz_number`),
  ADD KEY `idx_mastery_student` (`student_id`);

--
-- Indexes for table `juz_revisions`
--
ALTER TABLE `juz_revisions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_revision` (`student_id`,`juz_revised`,`revision_date`),
  ADD KEY `idx_revision_date` (`revision_date`),
  ADD KEY `idx_student_juz` (`student_id`,`juz_revised`);

--
-- Indexes for table `progress`
--
ALTER TABLE `progress`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`);

--
-- Indexes for table `sabaq_para`
--
ALTER TABLE `sabaq_para`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_student_juz` (`student_id`,`juz_number`);

--
-- Indexes for table `students`
--
ALTER TABLE `students`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `surah`
--
ALTER TABLE `surah`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `surah_number` (`surah_number`);

--
-- Indexes for table `teachers`
--
ALTER TABLE `teachers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ayat`
--
ALTER TABLE `ayat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `juz`
--
ALTER TABLE `juz`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `juz_mastery`
--
ALTER TABLE `juz_mastery`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `juz_revisions`
--
ALTER TABLE `juz_revisions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `progress`
--
ALTER TABLE `progress`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=33;

--
-- AUTO_INCREMENT for table `sabaq_para`
--
ALTER TABLE `sabaq_para`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `students`
--
ALTER TABLE `students`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `surah`
--
ALTER TABLE `surah`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=115;

--
-- AUTO_INCREMENT for table `teachers`
--
ALTER TABLE `teachers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `attendance_ibfk_2` FOREIGN KEY (`class_id`) REFERENCES `classes` (`id`);

--
-- Constraints for table `ayat`
--
ALTER TABLE `ayat`
  ADD CONSTRAINT `ayat_ibfk_1` FOREIGN KEY (`surah_id`) REFERENCES `surah` (`id`);

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`teacher_id`) REFERENCES `teachers` (`id`);

--
-- Constraints for table `juz_mastery`
--
ALTER TABLE `juz_mastery`
  ADD CONSTRAINT `juz_mastery_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `juz_revisions`
--
ALTER TABLE `juz_revisions`
  ADD CONSTRAINT `juz_revisions_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `progress`
--
ALTER TABLE `progress`
  ADD CONSTRAINT `progress_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`),
  ADD CONSTRAINT `progress_ibfk_2` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`);

--
-- Constraints for table `sabaq_para`
--
ALTER TABLE `sabaq_para`
  ADD CONSTRAINT `sabaq_para_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `students` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `teachers`
--
ALTER TABLE `teachers`
  ADD CONSTRAINT `teachers_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
