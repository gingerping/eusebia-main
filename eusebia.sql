-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 14, 2026 at 03:05 AM
-- Server version: 8.4.3
-- PHP Version: 8.3.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eusebia`
--

-- --------------------------------------------------------

--
-- Table structure for table `archives`
--

CREATE TABLE `archives` (
  `id_seven` int NOT NULL,
  `original_grade` varchar(50) DEFAULT NULL,
  `id_student` int NOT NULL,
  `sy` varchar(50) NOT NULL,
  `lrn` varchar(50) NOT NULL,
  `course` varchar(100) DEFAULT NULL,
  `lname` varchar(50) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `bdate` date NOT NULL,
  `sex` varchar(50) NOT NULL,
  `age` int NOT NULL,
  `contact` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `current_address` varchar(255) NOT NULL,
  `perm_address` varchar(255) NOT NULL,
  `ffname` varchar(255) NOT NULL,
  `flname` varchar(255) NOT NULL,
  `fmi` varchar(255) NOT NULL,
  `contact_f` varchar(100) NOT NULL,
  `mlname` varchar(255) NOT NULL,
  `mfname` varchar(255) NOT NULL,
  `mmi` varchar(255) NOT NULL,
  `contact_m` varchar(20) NOT NULL,
  `lglc` varchar(255) NOT NULL,
  `lsa` varchar(255) NOT NULL,
  `lysc` varchar(255) NOT NULL,
  `school_id` varchar(50) NOT NULL,
  `archived_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id_admin` int NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`id_admin`, `email`, `phone_number`, `password`, `lname`, `fname`, `mi`, `role`) VALUES
(5, 'eusebiahighschool@gmail.com', NULL, '$2y$10$QBNX8c5Vngq4P9TyZHrHsuk2DsQ2F6e3ypivLGqdZ81w0aMQF6qai', 'This', 'Is', 'Admin', 'administrator'),
(6, NULL, '09070569634', '$2y$10$OHNGozFTR/ItXOFEIyYBQObvdNLkFWfAgKBnB/ltXZG54RjWoN/vO', 'x 11\")', 'US-Letter', '(', 'administrator');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_eight`
--

CREATE TABLE `tbl_eight` (
  `id_eight` int NOT NULL,
  `id_student` int NOT NULL,
  `sy` varchar(50) NOT NULL,
  `lrn` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `bdate` date NOT NULL,
  `sex` varchar(50) NOT NULL,
  `age` int NOT NULL,
  `contact` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `current_address` varchar(255) NOT NULL,
  `perm_address` varchar(255) NOT NULL,
  `ffname` varchar(255) NOT NULL,
  `flname` varchar(255) NOT NULL,
  `fmi` varchar(255) NOT NULL,
  `contact_f` varchar(100) NOT NULL,
  `mlname` varchar(255) NOT NULL,
  `mfname` varchar(255) NOT NULL,
  `mmi` varchar(255) NOT NULL,
  `contact_m` varchar(20) NOT NULL,
  `lglc` varchar(255) NOT NULL,
  `lsa` varchar(255) NOT NULL,
  `lysc` varchar(255) NOT NULL,
  `school_id` varchar(50) NOT NULL,
  `documents` text,
  `is_archived` tinyint(1) NOT NULL DEFAULT '0',
  `archived_at` timestamp NULL DEFAULT NULL,
  `is_ip` varchar(10) NOT NULL DEFAULT 'No',
  `ip_group` varchar(255) NOT NULL DEFAULT '',
  `is_4ps` varchar(10) NOT NULL DEFAULT 'No',
  `fourps_id` varchar(100) NOT NULL DEFAULT '',
  `prev_grade_table` varchar(50) DEFAULT NULL,
  `prev_grade_id` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_eight`
--

INSERT INTO `tbl_eight` (`id_eight`, `id_student`, `sy`, `lrn`, `lname`, `fname`, `mi`, `bdate`, `sex`, `age`, `contact`, `email`, `current_address`, `perm_address`, `ffname`, `flname`, `fmi`, `contact_f`, `mlname`, `mfname`, `mmi`, `contact_m`, `lglc`, `lsa`, `lysc`, `school_id`, `documents`, `is_archived`, `archived_at`, `is_ip`, `ip_group`, `is_4ps`, `fourps_id`, `prev_grade_table`, `prev_grade_id`) VALUES
(19, 59, '2026-2027', '4568969080', 'x 11\")', 'US-Letter', '(', '2026-06-24', 'Female', 18, '09070569634', 'domingonobleza011@gmail.com', 'hibago', 'heheheh', 'US-Letter', 'x 11\")', '(', '09070560963', 'Lumabe', 'Rowena ', '2edfgv', '09070560968', 'bttgtgr', 'eusebia', '2025-2025', '4567764', '[\"uploads\\/documents\\/eight\\/1781004752_0_481141634_1303127427469870_75946518727150831_n.jpg\",\"uploads\\/documents\\/eight\\/1781004752_1_494705055_1346025396513406_8211283353670620323_n.jpg\"]', 0, NULL, 'Yes', 'Agta', 'Yes', '8765re4w3q987654', 'tbl_seven', 42);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_eleven`
--

CREATE TABLE `tbl_eleven` (
  `id_eleven` int NOT NULL,
  `id_student` int NOT NULL,
  `sy` varchar(50) NOT NULL,
  `lrn` varchar(50) NOT NULL,
  `course` varchar(100) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `bdate` date NOT NULL,
  `sex` varchar(50) NOT NULL,
  `age` int NOT NULL,
  `contact` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `current_address` varchar(255) NOT NULL,
  `perm_address` varchar(255) NOT NULL,
  `ffname` varchar(255) NOT NULL,
  `flname` varchar(255) NOT NULL,
  `fmi` varchar(255) NOT NULL,
  `contact_f` varchar(100) NOT NULL,
  `mlname` varchar(255) NOT NULL,
  `mfname` varchar(255) NOT NULL,
  `mmi` varchar(255) NOT NULL,
  `contact_m` varchar(20) NOT NULL,
  `lglc` varchar(255) NOT NULL,
  `lsa` varchar(255) NOT NULL,
  `lysc` varchar(255) NOT NULL,
  `school_id` varchar(50) NOT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT '0',
  `archived_at` timestamp NULL DEFAULT NULL,
  `documents` text,
  `is_ip` varchar(10) NOT NULL DEFAULT 'No',
  `ip_group` varchar(255) NOT NULL DEFAULT '',
  `is_4ps` varchar(10) NOT NULL DEFAULT 'No',
  `fourps_id` varchar(100) NOT NULL DEFAULT '',
  `prev_grade_table` varchar(50) DEFAULT NULL,
  `prev_grade_id` int DEFAULT '0',
  `enrollment_status` varchar(20) NOT NULL DEFAULT 'Pending',
  `reject_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_nine`
--

CREATE TABLE `tbl_nine` (
  `id_nine` int NOT NULL,
  `id_student` int NOT NULL,
  `sy` varchar(50) NOT NULL,
  `lrn` varchar(50) NOT NULL,
  `course` varchar(100) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `bdate` date NOT NULL,
  `sex` varchar(50) NOT NULL,
  `age` int NOT NULL,
  `contact` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `current_address` varchar(255) NOT NULL,
  `perm_address` varchar(255) NOT NULL,
  `ffname` varchar(255) NOT NULL,
  `flname` varchar(255) NOT NULL,
  `fmi` varchar(255) NOT NULL,
  `contact_f` varchar(100) NOT NULL,
  `mlname` varchar(255) NOT NULL,
  `mfname` varchar(255) NOT NULL,
  `mmi` varchar(255) NOT NULL,
  `contact_m` varchar(20) NOT NULL,
  `lglc` varchar(255) NOT NULL,
  `lsa` varchar(255) NOT NULL,
  `lysc` varchar(255) NOT NULL,
  `school_id` varchar(50) NOT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT '0',
  `archived_at` timestamp NULL DEFAULT NULL,
  `documents` text,
  `is_ip` varchar(10) NOT NULL DEFAULT 'No',
  `ip_group` varchar(255) NOT NULL DEFAULT '',
  `is_4ps` varchar(10) NOT NULL DEFAULT 'No',
  `fourps_id` varchar(100) NOT NULL DEFAULT '',
  `prev_grade_table` varchar(50) DEFAULT NULL,
  `prev_grade_id` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_student`
--

CREATE TABLE `tbl_student` (
  `id_student` int NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `age` int NOT NULL,
  `sex` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `houseno` varchar(255) NOT NULL,
  `street` varchar(50) NOT NULL,
  `brgy` varchar(50) NOT NULL,
  `municipal` varchar(50) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `bdate` date NOT NULL,
  `bplace` varchar(255) NOT NULL,
  `nationality` varchar(255) NOT NULL,
  `addedby` varchar(255) DEFAULT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT '0' COMMENT '0 = active, 1 = archived',
  `google_id` varchar(255) DEFAULT NULL,
  `facebook_id` varchar(255) DEFAULT NULL,
  `archived_at` datetime DEFAULT NULL COMMENT 'Timestamp when the student was archived',
  `social_provider` varchar(20) DEFAULT NULL,
  `social_id` varchar(128) DEFAULT NULL,
  `social_avatar_url` varchar(512) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_student`
--

INSERT INTO `tbl_student` (`id_student`, `email`, `phone_number`, `password`, `lname`, `fname`, `mi`, `age`, `sex`, `status`, `houseno`, `street`, `brgy`, `municipal`, `contact`, `bdate`, `bplace`, `nationality`, `addedby`, `is_archived`, `google_id`, `facebook_id`, `archived_at`, `social_provider`, `social_id`, `social_avatar_url`) VALUES
(61, 'domingonobleza011@gmail.com', NULL, '$2y$10$RuqGoziqP/vukKl5C2PmteM1In11yK/b5Ds0txu5xdOwcxjUxfwbm', 'DOmingo', 'nobleza', 'bendal', 18, 'Female', 'Widowed', 'Bagong Sirang', 'Zone 3', 'xw', 'Iriga City', '09070560963', '2026-06-09', 'Antipolo, Rizal', 'batman', 'Student', 0, NULL, NULL, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_seven`
--

CREATE TABLE `tbl_seven` (
  `id_seven` int NOT NULL,
  `id_student` int NOT NULL,
  `sy` varchar(50) NOT NULL,
  `lrn` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `bdate` date NOT NULL,
  `sex` varchar(50) NOT NULL,
  `age` int NOT NULL,
  `contact` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `current_address` varchar(255) NOT NULL,
  `perm_address` varchar(255) NOT NULL,
  `ffname` varchar(255) NOT NULL,
  `flname` varchar(255) NOT NULL,
  `fmi` varchar(255) NOT NULL,
  `contact_f` varchar(100) NOT NULL,
  `mlname` varchar(255) NOT NULL,
  `mfname` varchar(255) NOT NULL,
  `mmi` varchar(255) NOT NULL,
  `contact_m` varchar(20) NOT NULL,
  `lglc` varchar(255) NOT NULL,
  `lsa` varchar(255) NOT NULL,
  `lysc` varchar(255) NOT NULL,
  `school_id` varchar(50) NOT NULL,
  `documents` text,
  `is_archived` tinyint(1) NOT NULL DEFAULT '0',
  `archived_at` timestamp NULL DEFAULT NULL,
  `enrollment_status` varchar(20) NOT NULL DEFAULT 'Pending',
  `is_ip` varchar(10) NOT NULL DEFAULT 'No',
  `ip_group` varchar(255) NOT NULL DEFAULT '',
  `is_4ps` varchar(10) NOT NULL DEFAULT 'No',
  `fourps_id` varchar(100) NOT NULL DEFAULT '',
  `reject_reason` text
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_seven`
--

INSERT INTO `tbl_seven` (`id_seven`, `id_student`, `sy`, `lrn`, `lname`, `fname`, `mi`, `bdate`, `sex`, `age`, `contact`, `email`, `current_address`, `perm_address`, `ffname`, `flname`, `fmi`, `contact_f`, `mlname`, `mfname`, `mmi`, `contact_m`, `lglc`, `lsa`, `lysc`, `school_id`, `documents`, `is_archived`, `archived_at`, `enrollment_status`, `is_ip`, `ip_group`, `is_4ps`, `fourps_id`, `reject_reason`) VALUES
(42, 59, '2026-2027', '4568969080', 'x 11\")', 'US-Letter', '(', '2026-06-24', 'Female', 18, '09070569634', 'domingonobleza011@gmail.com', 'hibago', 'heheheh', 'US-Letter', 'x 11\")', '(', '09070560963', 'Lumabe', 'Rowena ', '2edfgv', '09070560968', 'bttgtgr', 'eusebia', '2025-2025', '4567764', '[\"uploads\\/documents\\/seven\\/1781004523_0_1.png\",\"uploads\\/documents\\/seven\\/1781004523_1_2.png\"]', 0, NULL, 'Approved', 'Yes', 'Agta', 'Yes', '34567890', NULL),
(43, 59, '2026-2027', '4568969080', 'x 11\")', 'US-Letter', '(', '2026-06-24', 'Female', 18, '09070569634', 'domingonobleza011@gmail.com', 'hibago', 'heheheh', 'US-Letter', 'x 11\")', '(', '09070560963', 'Lumabe', 'Rowena ', '2edfgv', '09070560968', 'bttgtgr', 'eusebia', '2025-2025', '4567764', '[\"uploads\\/documents\\/seven\\/1781690102_0_1.png\",\"uploads\\/documents\\/seven\\/1781690102_1_2.png\"]', 0, NULL, 'Pending', 'No', '', 'No', '', NULL),
(44, 59, '2026-2027', '4568969080', 'x 11\")', 'US-Letter', '(', '2026-06-24', 'Female', 18, '09070569634', 'domingonobleza011@gmail.com', 'hibago', 'heheheh', 'US-Letter', 'x 11\")', '(', '09070560963', 'Lumabe', 'Rowena ', '2edfgv', '09070560968', 'bttgtgr', 'eusebia', '2025-2025', '4567764', '[\"uploads\\/documents\\/seven\\/1781690111_0_1.png\",\"uploads\\/documents\\/seven\\/1781690111_1_2.png\"]', 0, NULL, 'Pending', 'No', '', 'No', '', NULL),
(45, 61, '2026-2027', '45689690805', 'Domingo', 'nobleza', 'bendal', '2026-06-10', 'Male', 18, '09070560963', 'domingonobleza011@gmail.com', 'hibago', 'hibago', 'juan', 'di magiba', 'meow', '09070560963', 'Lumabe', 'Rowena ', 'nosipeda', '09070560968', 'grade 6', 'eusebia', '2025-2025', '4567764', '[\"uploads\\/documents\\/seven\\/1782611492_0_Screenshot_2026-06-27_105057.png\",\"uploads\\/documents\\/seven\\/1782611492_1_Screenshot_2026-06-27_175232.png\",\"uploads\\/documents\\/seven\\/1782611492_2_Screenshot_2026-06-28_094317.png\"]', 0, NULL, 'Approved', 'Yes', 'Agta', 'Yes', '234567890098765', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_ten`
--

CREATE TABLE `tbl_ten` (
  `id_ten` int NOT NULL,
  `id_student` int NOT NULL,
  `sy` varchar(50) NOT NULL,
  `lrn` varchar(50) NOT NULL,
  `course` varchar(100) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `bdate` date NOT NULL,
  `sex` varchar(50) NOT NULL,
  `age` int NOT NULL,
  `contact` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `current_address` varchar(255) NOT NULL,
  `perm_address` varchar(255) NOT NULL,
  `ffname` varchar(255) NOT NULL,
  `flname` varchar(255) NOT NULL,
  `fmi` varchar(255) NOT NULL,
  `contact_f` varchar(100) NOT NULL,
  `mlname` varchar(255) NOT NULL,
  `mfname` varchar(255) NOT NULL,
  `mmi` varchar(255) NOT NULL,
  `contact_m` varchar(20) NOT NULL,
  `lglc` varchar(255) NOT NULL,
  `lsa` varchar(255) NOT NULL,
  `lysc` varchar(255) NOT NULL,
  `school_id` varchar(50) NOT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT '0',
  `archived_at` timestamp NULL DEFAULT NULL,
  `documents` text,
  `is_ip` varchar(10) NOT NULL DEFAULT 'No',
  `ip_group` varchar(255) NOT NULL DEFAULT '',
  `is_4ps` varchar(10) NOT NULL DEFAULT 'No',
  `fourps_id` varchar(100) NOT NULL DEFAULT '',
  `prev_grade_table` varchar(50) DEFAULT NULL,
  `prev_grade_id` int DEFAULT '0',
  `enrollment_status` varchar(20) NOT NULL DEFAULT 'Pending',
  `reject_reason` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_twelve`
--

CREATE TABLE `tbl_twelve` (
  `id_twelve` int NOT NULL,
  `id_student` int NOT NULL,
  `sy` varchar(50) NOT NULL,
  `lrn` varchar(50) NOT NULL,
  `course` varchar(100) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `bdate` date NOT NULL,
  `sex` varchar(50) NOT NULL,
  `age` int NOT NULL,
  `contact` varchar(50) NOT NULL,
  `email` varchar(50) NOT NULL,
  `current_address` varchar(255) NOT NULL,
  `perm_address` varchar(255) NOT NULL,
  `ffname` varchar(255) NOT NULL,
  `flname` varchar(255) NOT NULL,
  `fmi` varchar(255) NOT NULL,
  `contact_f` varchar(100) NOT NULL,
  `mlname` varchar(255) NOT NULL,
  `mfname` varchar(255) NOT NULL,
  `mmi` varchar(255) NOT NULL,
  `contact_m` varchar(20) NOT NULL,
  `lglc` varchar(255) NOT NULL,
  `lsa` varchar(255) NOT NULL,
  `lysc` varchar(255) NOT NULL,
  `school_id` varchar(50) NOT NULL,
  `is_archived` tinyint(1) NOT NULL DEFAULT '0',
  `archived_at` timestamp NULL DEFAULT NULL,
  `documents` text,
  `is_ip` varchar(10) NOT NULL DEFAULT 'No',
  `ip_group` varchar(255) NOT NULL DEFAULT '',
  `is_4ps` varchar(10) NOT NULL DEFAULT 'No',
  `fourps_id` varchar(100) NOT NULL DEFAULT '',
  `prev_grade_table` varchar(50) DEFAULT NULL,
  `prev_grade_id` int DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id_user` int NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `age` int NOT NULL,
  `sex` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `addedby` varchar(255) DEFAULT NULL,
  `subject_handled` varchar(255) DEFAULT NULL COMMENT 'Comma-separated subjects e.g. Math, English',
  `adviser_grade` varchar(100) DEFAULT NULL COMMENT 'Grade/section the teacher advises e.g. Grade 7, Grade 11 - STEM',
  `subject_grades` varchar(500) DEFAULT NULL COMMENT 'Comma-separated grade levels where this teacher handles subjects e.g. Grade 7,Grade 9,Grade 11 - STEM'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id_user`, `email`, `phone_number`, `password`, `lname`, `fname`, `mi`, `age`, `sex`, `address`, `contact`, `position`, `role`, `addedby`, `subject_handled`, `adviser_grade`, `subject_grades`) VALUES
(19, 'domingonobleza@gmail.com', NULL, '$2y$10$lGpgIuzDFhszmP03MSmvLupsZaFQQXg9KryIIrGuSCoK6ynYAEf/C', 'nosipeda', 'Jean rose', 'lumabe', 20, 'Male', 'Bagong Sirang, Zone 3, Dalig, Iriga City', '09070569634', 'Teacher III', 'staff', 'Admin-Promoted', 'math', 'Grade 12 - TVL-ICT', 'Grade 7,Grade 8,Grade 10,Grade 11 - STEM,Grade 11 - GAS,Grade 11 - TVL-ICT,Grade 12 - STEM,Grade 12 - ABM');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `archives`
--
ALTER TABLE `archives`
  ADD PRIMARY KEY (`id_seven`);

--
-- Indexes for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  ADD PRIMARY KEY (`id_admin`);

--
-- Indexes for table `tbl_eight`
--
ALTER TABLE `tbl_eight`
  ADD PRIMARY KEY (`id_eight`);

--
-- Indexes for table `tbl_eleven`
--
ALTER TABLE `tbl_eleven`
  ADD PRIMARY KEY (`id_eleven`);

--
-- Indexes for table `tbl_nine`
--
ALTER TABLE `tbl_nine`
  ADD PRIMARY KEY (`id_nine`);

--
-- Indexes for table `tbl_student`
--
ALTER TABLE `tbl_student`
  ADD PRIMARY KEY (`id_student`),
  ADD UNIQUE KEY `uq_social` (`social_provider`,`social_id`),
  ADD KEY `idx_is_archived` (`is_archived`),
  ADD KEY `idx_student_google_id` (`google_id`),
  ADD KEY `idx_student_facebook_id` (`facebook_id`);

--
-- Indexes for table `tbl_seven`
--
ALTER TABLE `tbl_seven`
  ADD PRIMARY KEY (`id_seven`);

--
-- Indexes for table `tbl_ten`
--
ALTER TABLE `tbl_ten`
  ADD PRIMARY KEY (`id_ten`);

--
-- Indexes for table `tbl_twelve`
--
ALTER TABLE `tbl_twelve`
  ADD PRIMARY KEY (`id_twelve`);

--
-- Indexes for table `tbl_user`
--
ALTER TABLE `tbl_user`
  ADD PRIMARY KEY (`id_user`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `archives`
--
ALTER TABLE `archives`
  MODIFY `id_seven` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id_admin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_eight`
--
ALTER TABLE `tbl_eight`
  MODIFY `id_eight` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `tbl_eleven`
--
ALTER TABLE `tbl_eleven`
  MODIFY `id_eleven` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tbl_nine`
--
ALTER TABLE `tbl_nine`
  MODIFY `id_nine` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tbl_student`
--
ALTER TABLE `tbl_student`
  MODIFY `id_student` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=62;

--
-- AUTO_INCREMENT for table `tbl_seven`
--
ALTER TABLE `tbl_seven`
  MODIFY `id_seven` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `tbl_ten`
--
ALTER TABLE `tbl_ten`
  MODIFY `id_ten` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_twelve`
--
ALTER TABLE `tbl_twelve`
  MODIFY `id_twelve` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
