-- phpMyAdmin SQL Dump
-- version 4.9.0.1
-- https://www.phpmyadmin.net/
--
-- Host: sql300.infinityfree.com
-- Generation Time: Jun 09, 2026 at 06:50 AM
-- Server version: 11.4.12-MariaDB
-- PHP Version: 7.2.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `if0_41932978_eusebia`
--

-- --------------------------------------------------------

--
-- Table structure for table `archives`
--

CREATE TABLE `archives` (
  `id_seven` int(11) NOT NULL,
  `original_grade` varchar(50) DEFAULT NULL,
  `id_resident` int(11) NOT NULL,
  `sy` varchar(50) NOT NULL,
  `lrn` varchar(50) NOT NULL,
  `course` varchar(100) DEFAULT NULL,
  `lname` varchar(50) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `bdate` date NOT NULL,
  `sex` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
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
  `archived_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `email`, `token`, `expires_at`, `created_at`) VALUES
(3, 'jeanrosenosipeda@gmail.com', '1052dec8b2ae9c23df84d7c2367521431d5791102b919402ee8c27eb293e8a13', '2026-06-04 23:41:17', '2026-06-05 02:41:18');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_admin`
--

CREATE TABLE `tbl_admin` (
  `id_admin` int(11) NOT NULL,
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
  `id_eight` int(11) NOT NULL,
  `id_resident` int(11) NOT NULL,
  `sy` varchar(50) NOT NULL,
  `lrn` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `bdate` date NOT NULL,
  `sex` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
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
  `documents` text DEFAULT NULL,
  `is_ip` enum('Yes','No') NOT NULL DEFAULT 'No' COMMENT 'Is the student an Indigenous People member?',
  `ip_group` varchar(100) NOT NULL DEFAULT '' COMMENT 'IP group or tribe name',
  `is_4ps` enum('Yes','No') NOT NULL DEFAULT 'No' COMMENT 'Is the student a 4Ps beneficiary?',
  `fourps_id` varchar(50) NOT NULL DEFAULT '' COMMENT '4Ps Household ID Number',
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `archived_at` timestamp NULL DEFAULT NULL,
  `enrollment_status` varchar(20) NOT NULL DEFAULT 'Pending',
  `reject_reason` text DEFAULT NULL,
  `prev_grade_table` varchar(20) DEFAULT NULL COMMENT 'Source table of the previous grade record (e.g. tbl_seven)',
  `prev_grade_id` int(11) DEFAULT NULL COMMENT 'Primary key of the previous grade record to archive on approval'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_eight`
--

INSERT INTO `tbl_eight` (`id_eight`, `id_resident`, `sy`, `lrn`, `lname`, `fname`, `mi`, `bdate`, `sex`, `age`, `contact`, `email`, `current_address`, `perm_address`, `ffname`, `flname`, `fmi`, `contact_f`, `mlname`, `mfname`, `mmi`, `contact_m`, `lglc`, `lsa`, `lysc`, `school_id`, `documents`, `is_ip`, `ip_group`, `is_4ps`, `fourps_id`, `is_archived`, `archived_at`, `enrollment_status`, `reject_reason`, `prev_grade_table`, `prev_grade_id`) VALUES
(29, 62, '2026-2027', '4568969087', 'x 11\")', 'US-Letter', '(', '2026-06-23', 'Female', 18, '09070569634', 'domingonobleza011@gmail.com', 'hibago', 'hxgshbcdvjbvfje', 'US-Letter', 'x 11\")', '(', '09070560963', 'Lumabe', 'Rowena ', '2edfgv', '09345678998', 'bttgtgr', 'eusebia', '2025-2025', '4567764', '[\"uploads\\/documents\\/eight\\/1780968464_0_481141634_1303127427469870_75946518727150831_n.jpg\",\"uploads\\/documents\\/eight\\/1780968464_1_494705055_1346025396513406_8211283353670620323_n.jpg\"]', 'No', '', 'No', '', 0, NULL, 'Approved', NULL, 'tbl_seven', 71),
(30, 62, '2026-2027', '4568969089', 'x 11\")', 'US-Letter', '(', '2026-06-23', 'Female', 18, '09070569634', 'domingonobleza011@gmail.com', 'hibago', 'oikyujtrgve', 'US-Letter', 'x 11\")', '(', '09070560963', 'Lumabe', 'Rowena ', '2edfgv', '09345678998', 'bttgtgr', 'eusebia', '2025-2025', '4567764', '[\"uploads\\/documents\\/eight\\/1780968507_0_481141634_1303127427469870_75946518727150831_n.jpg\",\"uploads\\/documents\\/eight\\/1780968507_1_494705055_1346025396513406_8211283353670620323_n.jpg\"]', 'No', '', 'No', '', 0, NULL, 'Pending', NULL, '', 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_eleven`
--

CREATE TABLE `tbl_eleven` (
  `id_eleven` int(11) NOT NULL,
  `id_resident` int(11) NOT NULL,
  `sy` varchar(50) NOT NULL,
  `lrn` varchar(50) NOT NULL,
  `course` varchar(100) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `bdate` date NOT NULL,
  `sex` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
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
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `archived_at` timestamp NULL DEFAULT NULL,
  `documents` text DEFAULT NULL,
  `prev_grade_table` varchar(20) DEFAULT NULL,
  `prev_grade_id` int(11) DEFAULT NULL,
  `enrollment_status` varchar(20) NOT NULL DEFAULT 'Pending',
  `reject_reason` text DEFAULT NULL,
  `is_ip` varchar(3) NOT NULL DEFAULT 'No',
  `ip_group` varchar(255) DEFAULT NULL,
  `is_4ps` varchar(3) NOT NULL DEFAULT 'No',
  `fourps_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_nine`
--

CREATE TABLE `tbl_nine` (
  `id_nine` int(11) NOT NULL,
  `id_resident` int(11) NOT NULL,
  `sy` varchar(50) NOT NULL,
  `lrn` varchar(50) NOT NULL,
  `course` varchar(100) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `bdate` date NOT NULL,
  `sex` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
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
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `archived_at` timestamp NULL DEFAULT NULL,
  `documents` text DEFAULT NULL,
  `enrollment_status` varchar(20) NOT NULL DEFAULT 'Pending',
  `reject_reason` text DEFAULT NULL,
  `prev_grade_table` varchar(20) DEFAULT NULL,
  `prev_grade_id` int(11) DEFAULT NULL,
  `is_ip` varchar(3) NOT NULL DEFAULT 'No',
  `ip_group` varchar(255) DEFAULT NULL,
  `is_4ps` varchar(3) NOT NULL DEFAULT 'No',
  `fourps_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_promotion_requests`
--

CREATE TABLE `tbl_promotion_requests` (
  `id` int(11) NOT NULL,
  `id_resident` int(11) NOT NULL,
  `from_grade` varchar(5) NOT NULL,
  `to_grade` varchar(5) NOT NULL,
  `record_id` int(11) NOT NULL,
  `documents` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'Pending',
  `reject_reason` text DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp(),
  `reviewed_at` datetime DEFAULT NULL,
  `reviewed_by` int(11) DEFAULT NULL
) ENGINE=MyISAM DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_resident`
--

CREATE TABLE `tbl_resident` (
  `id_resident` int(11) NOT NULL,
  `email` varchar(255) DEFAULT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
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
  `is_archived` tinyint(1) NOT NULL DEFAULT 0 COMMENT '0 = active, 1 = archived',
  `archived_at` datetime DEFAULT NULL COMMENT 'Timestamp when the resident was archived'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_resident`
--

INSERT INTO `tbl_resident` (`id_resident`, `email`, `phone_number`, `password`, `lname`, `fname`, `mi`, `age`, `sex`, `status`, `houseno`, `street`, `brgy`, `municipal`, `contact`, `bdate`, `bplace`, `nationality`, `addedby`, `is_archived`, `archived_at`) VALUES
(56, 'domingonobleza011@gmail.com', NULL, '$2y$10$3o2SqyMGxAiRtKzkYEy.4eLq/6Ng97609ISkCpUBRv1UQbjiG6ckK', 'x 11\")', 'US-Letter', '(', 23, 'Female', 'Widowed', 'Blk. 14 Lot 25', 'El Chapo', 'sagrada', 'iriga', '09070560963', '2026-05-21', 'Antipolo, Rizal', 'rety', 'Resident', 0, NULL),
(60, 'mbbmichaelbolobanaria25@gmail.com', NULL, '$2y$10$ixENfpOaCgcq7ZF5/u1og..JEKBTvgE3rs.fd1usD6nD7LyF/C2x2', 'BaÃ±aria', 'Michael', 'Bolo', 29, 'Male', 'Single', '1', 'National Rd Zone', 'san juan', 'baao', '09369266503', '0000-00-00', 'baao', 'filipino', 'Resident', 0, NULL),
(62, 'jeanrosenosipeda8@gmail.com', NULL, '$2y$10$1tM.WIymaCqziiFuvV6KhuMEC5dCNNtV/EPSthTzfEwh6w4otEm6m', 'Jean Rose', 'Nosipeda', '', 0, '', '', '', '', '', '', '', '0000-00-00', '', '', 'Google', 0, NULL),
(64, 'domingonobleza79@gmail.com', NULL, '$2y$10$Tr5IMg9jOyQa6GYQe.N0IOgYOtavKO.Znunf2uVfQXyAR16J/BSym', 'Nobleza', 'Domingo', '', 0, '', '', '', '', '', '', '', '0000-00-00', '', '', 'Google', 0, NULL),
(67, 'ritashlii676@gmail.com', NULL, '$2y$10$AQCG3p5eQDhl1nBuNHi/P.xXhwoY8mZc6Ma66aigUdUk/aQ7LUvQq', 'Ashley B.', 'Rita', '', 0, '', '', '', '', '', '', '', '0000-00-00', '', '', 'Google', 0, NULL),
(68, 'boaquinaelenor@gmail.com', NULL, '$2y$10$Wadn8g1tsmFPSfkiRQOUzOQLtWtXQFI54Rrlxl0wrdvaEF8N834si', 'Elenor B.', 'BoaquiÃ±a,', '', 0, '', '', '', '', '', '', '', '0000-00-00', '', '', 'Google', 0, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_seven`
--

CREATE TABLE `tbl_seven` (
  `id_seven` int(11) NOT NULL,
  `id_resident` int(11) NOT NULL,
  `sy` varchar(50) NOT NULL,
  `lrn` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `bdate` date NOT NULL,
  `sex` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
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
  `documents` text DEFAULT NULL,
  `is_ip` enum('Yes','No') NOT NULL DEFAULT 'No' COMMENT 'Is the student an Indigenous People member?',
  `ip_group` varchar(100) NOT NULL DEFAULT '' COMMENT 'IP group or tribe name',
  `is_4ps` enum('Yes','No') NOT NULL DEFAULT 'No' COMMENT 'Is the student a 4Ps beneficiary?',
  `fourps_id` varchar(50) NOT NULL DEFAULT '' COMMENT '4Ps Household ID Number',
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `archived_at` timestamp NULL DEFAULT NULL,
  `enrollment_status` varchar(20) NOT NULL DEFAULT 'Pending',
  `reject_reason` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_ten`
--

CREATE TABLE `tbl_ten` (
  `id_ten` int(11) NOT NULL,
  `id_resident` int(11) NOT NULL,
  `sy` varchar(50) NOT NULL,
  `lrn` varchar(50) NOT NULL,
  `course` varchar(100) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `bdate` date NOT NULL,
  `sex` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
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
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `archived_at` timestamp NULL DEFAULT NULL,
  `documents` text DEFAULT NULL,
  `prev_grade_table` varchar(20) DEFAULT NULL,
  `prev_grade_id` int(11) DEFAULT NULL,
  `enrollment_status` varchar(20) NOT NULL DEFAULT 'Pending',
  `reject_reason` text DEFAULT NULL,
  `is_ip` varchar(3) NOT NULL DEFAULT 'No',
  `ip_group` varchar(255) DEFAULT NULL,
  `is_4ps` varchar(3) NOT NULL DEFAULT 'No',
  `fourps_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_twelve`
--

CREATE TABLE `tbl_twelve` (
  `id_twelve` int(11) NOT NULL,
  `id_resident` int(11) NOT NULL,
  `sy` varchar(50) NOT NULL,
  `lrn` varchar(50) NOT NULL,
  `course` varchar(100) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `bdate` date NOT NULL,
  `sex` varchar(50) NOT NULL,
  `age` int(11) NOT NULL,
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
  `is_archived` tinyint(1) NOT NULL DEFAULT 0,
  `archived_at` timestamp NULL DEFAULT NULL,
  `documents` text DEFAULT NULL,
  `enrollment_status` varchar(20) NOT NULL DEFAULT 'Pending',
  `reject_reason` text DEFAULT NULL,
  `prev_grade_table` varchar(20) DEFAULT NULL,
  `prev_grade_id` int(11) DEFAULT NULL,
  `is_ip` varchar(3) NOT NULL DEFAULT 'No',
  `ip_group` varchar(255) DEFAULT NULL,
  `is_4ps` varchar(3) NOT NULL DEFAULT 'No',
  `fourps_id` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id_user` int(11) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `age` int(11) NOT NULL,
  `sex` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `addedby` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id_user`, `email`, `phone_number`, `password`, `lname`, `fname`, `mi`, `age`, `sex`, `address`, `contact`, `position`, `role`, `addedby`) VALUES
(11, 'jaymark@gmail.com', NULL, 'jaymark', 'lovite', 'jaymark', 'none', 21, 'Male', 'salvacion', '09222555100', 'Barangay Secretary', 'user', 'Vilfamat, Vincent'),
(13, 'domingonobleza011@gmail.com', NULL, 'dominga', 'x 11\")', 'US-Letter', '(', 122, 'Male', 'Sagrada, Iriga', '09070569634', 'Chairman', 'user', 'nobleza, domingo'),
(14, 'jaymarklovite@gmail.com', NULL, 'jayjay', 'lovite', 'jaymark', 'hamag', 18, 'Male', 'Sagrada, Iriga', '09123456789', 'Chairman', 'user', 'nobleza, domingo');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `archives`
--
ALTER TABLE `archives`
  ADD PRIMARY KEY (`id_seven`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `token` (`token`),
  ADD KEY `email` (`email`);

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
-- Indexes for table `tbl_promotion_requests`
--
ALTER TABLE `tbl_promotion_requests`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tbl_resident`
--
ALTER TABLE `tbl_resident`
  ADD PRIMARY KEY (`id_resident`),
  ADD KEY `idx_is_archived` (`is_archived`);

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
  MODIFY `id_seven` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `tbl_admin`
--
ALTER TABLE `tbl_admin`
  MODIFY `id_admin` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `tbl_eight`
--
ALTER TABLE `tbl_eight`
  MODIFY `id_eight` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `tbl_eleven`
--
ALTER TABLE `tbl_eleven`
  MODIFY `id_eleven` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tbl_nine`
--
ALTER TABLE `tbl_nine`
  MODIFY `id_nine` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `tbl_promotion_requests`
--
ALTER TABLE `tbl_promotion_requests`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_resident`
--
ALTER TABLE `tbl_resident`
  MODIFY `id_resident` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=69;

--
-- AUTO_INCREMENT for table `tbl_seven`
--
ALTER TABLE `tbl_seven`
  MODIFY `id_seven` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=72;

--
-- AUTO_INCREMENT for table `tbl_ten`
--
ALTER TABLE `tbl_ten`
  MODIFY `id_ten` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `tbl_twelve`
--
ALTER TABLE `tbl_twelve`
  MODIFY `id_twelve` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id_user` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
