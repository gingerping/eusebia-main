-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Mar 30, 2026 at 12:10 AM
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
  `id_resident` int NOT NULL,
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
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_admin`
--

INSERT INTO `tbl_admin` (`id_admin`, `email`, `password`, `lname`, `fname`, `mi`, `role`) VALUES
(5, 'domingonobleza011@gmail.com', 'domdom', 'nobleza', 'domingo', 'bendal', 'administrator');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_announcement`
--

CREATE TABLE `tbl_announcement` (
  `id_announcement` int NOT NULL,
  `event` varchar(1000) NOT NULL,
  `target` varchar(255) DEFAULT NULL,
  `start_date` date NOT NULL,
  `addedby` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_announcement`
--

INSERT INTO `tbl_announcement` (`id_announcement`, `event`, `target`, `start_date`, `addedby`) VALUES
(12, 'hamag mag request kamo', NULL, '2026-03-14', 'nobleza, domingo'),
(14, 'upload id', NULL, '2026-03-24', 'nobleza, domingo');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_eight`
--

CREATE TABLE `tbl_eight` (
  `id_eight` int NOT NULL,
  `id_resident` int NOT NULL,
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
  `school_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_eight`
--

INSERT INTO `tbl_eight` (`id_eight`, `id_resident`, `sy`, `lrn`, `lname`, `fname`, `mi`, `bdate`, `sex`, `age`, `contact`, `email`, `current_address`, `perm_address`, `ffname`, `flname`, `fmi`, `contact_f`, `mlname`, `mfname`, `mmi`, `contact_m`, `lglc`, `lsa`, `lysc`, `school_id`) VALUES
(1, 41, '2026-2027', '4568969080', 'domdom', 'US-Letter', '(', '2026-03-03', 'Female', 18, '09070560963', 'domingonobleza011@gmail.com', 'rvgtuyik', 'iolip;op', 'rvge', 'rgrht', '', 'grtgr54', 'rgrht', 'yyyuu', '', '09070560963', 'bttgtgr', 'rtvrty', 'rvtv34', '4567764');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_eleven`
--

CREATE TABLE `tbl_eleven` (
  `id_eleven` int NOT NULL,
  `id_resident` int NOT NULL,
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
  `school_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_eleven`
--

INSERT INTO `tbl_eleven` (`id_eleven`, `id_resident`, `sy`, `lrn`, `course`, `lname`, `fname`, `mi`, `bdate`, `sex`, `age`, `contact`, `email`, `current_address`, `perm_address`, `ffname`, `flname`, `fmi`, `contact_f`, `mlname`, `mfname`, `mmi`, `contact_m`, `lglc`, `lsa`, `lysc`, `school_id`) VALUES
(1, 41, '2026-2027', '4568969080', 'STEM', 'domdom', 'US-Letter', '(', '2026-03-03', 'Female', 18, '09070560963', 'domingonobleza011@gmail.com', 'rvgtuyik', 'iolip;op', 'rvge', 'rgrht', '', 'grtgr54', 'rgrht', 'yyyuu', '', '09070560963', 'bttgtgr', 'rtvrty', 'rvtv34', '4567764'),
(2, 41, '2026-2027', '4568969080', 'ABM', 'domdom', 'US-Letter', '(', '2026-03-03', 'Female', 18, '09070560963', 'domingonobleza011@gmail.com', 'rvgtuyik', 'iolip;op', 'rvge', 'rgrht', '', 'grtgr54', 'rgrht', 'yyyuu', '', '09070560963', 'bttgtgr', 'rtvrty', 'rvtv34', '4567764');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_nine`
--

CREATE TABLE `tbl_nine` (
  `id_nine` int NOT NULL,
  `id_resident` int NOT NULL,
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
  `school_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_nine`
--

INSERT INTO `tbl_nine` (`id_nine`, `id_resident`, `sy`, `lrn`, `course`, `lname`, `fname`, `mi`, `bdate`, `sex`, `age`, `contact`, `email`, `current_address`, `perm_address`, `ffname`, `flname`, `fmi`, `contact_f`, `mlname`, `mfname`, `mmi`, `contact_m`, `lglc`, `lsa`, `lysc`, `school_id`) VALUES
(5, 41, '2026-2027', '4568969080', 'ICT - Computer Programming', 'domdom', 'US-Letter', '(', '2026-03-04', 'Female', 18, '09070560963', 'domingonobleza011@gmail.com', 'rvgtuyik', 'iolip;op', 'rvge', 'rgrht', '', 'grtgr54', 'rgrht', 'yyyuu', '', '09070560963', 'bttgtgr', 'rtvrty', 'rvtv34', '4567764'),
(7, 41, '2026-2027', '4568969080', 'Home Economics - Bread and Pastry', 'domdom', 'US-Letter', '(', '2026-03-04', 'Female', 18, '09070560963', 'domingonobleza011@gmail.com', 'rvgtuyik', 'iolip;op', 'rvge', 'rgrht', '', 'grtgr54', 'rgrht', 'yyyuu', '', '09070560963', 'bttgtgr', 'rtvrty', 'rvtv34', '4567764');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_resident`
--

CREATE TABLE `tbl_resident` (
  `id_resident` int NOT NULL,
  `res_photo` mediumblob,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `fname` varchar(255) NOT NULL,
  `mi` varchar(255) NOT NULL,
  `age` int NOT NULL,
  `sex` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL,
  `houseno` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `brgy` varchar(255) DEFAULT NULL,
  `municipal` varchar(255) DEFAULT NULL,
  `address` varchar(255) DEFAULT NULL,
  `contact` varchar(255) NOT NULL,
  `bdate` date NOT NULL,
  `bplace` varchar(255) NOT NULL,
  `nationality` varchar(255) NOT NULL,
  `family_role` varchar(255) NOT NULL,
  `voter` varchar(255) DEFAULT NULL,
  `role` varchar(255) NOT NULL,
  `addedby` varchar(255) DEFAULT NULL,
  `is_archived` tinyint(1) DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_resident`
--

INSERT INTO `tbl_resident` (`id_resident`, `res_photo`, `email`, `password`, `lname`, `fname`, `mi`, `age`, `sex`, `status`, `houseno`, `street`, `brgy`, `municipal`, `address`, `contact`, `bdate`, `bplace`, `nationality`, `family_role`, `voter`, `role`, `addedby`, `is_archived`) VALUES
(38, NULL, 'juliaallehnagrampa50@gmail.com', 'liah_juliax', 'Nagrampa', 'Julia Alleh', 'Esoy', 20, 'Female', 'Single', '4431', 'Monamot St', 'Sagrada ', 'Iriga City', NULL, '09385680889', '2005-10-17', 'Quezon City', 'filipino', 'No', 'No', 'resident', NULL, 0),
(39, NULL, 'clintjosephbriones90@gmail.com', 'briones', 'Briones', 'Clint joseph', 'Beldad', 25, 'Male', 'Single', '1234', 'E badiola ', 'San jose', 'Baao', NULL, '09108300407', '2000-05-03', 'San jose', 'Cam sur', 'Yes', 'Yes', 'resident', NULL, 0),
(41, NULL, 'domingonobleza011@gmail.com', 'dom', 'domdom', 'US-Letter', '(', 18, 'Male', 'Married', '111', 'El Chapo', 'sagrada', 'iriga', NULL, '09070560963', '2005-03-20', 'hibago', 'batman', 'Yes', 'Yes', 'resident', NULL, 0),
(42, NULL, 'domingo11@gmail.com', 'password', 'domdom', 'yoe bro', 'hanep an', 18, 'Male', 'Married', '111', '222', 'sagrada', 'iriga', NULL, '09070560963', '2026-03-10', 'hibago', 'batman', 'No', 'Yes', 'resident', NULL, 0);

-- --------------------------------------------------------

--
-- Table structure for table `tbl_seven`
--

CREATE TABLE `tbl_seven` (
  `id_seven` int NOT NULL,
  `id_resident` int NOT NULL,
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
  `school_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_seven`
--

INSERT INTO `tbl_seven` (`id_seven`, `id_resident`, `sy`, `lrn`, `lname`, `fname`, `mi`, `bdate`, `sex`, `age`, `contact`, `email`, `current_address`, `perm_address`, `ffname`, `flname`, `fmi`, `contact_f`, `mlname`, `mfname`, `mmi`, `contact_m`, `lglc`, `lsa`, `lysc`, `school_id`) VALUES
(7, 41, '2026-2027', '4568969080', 'domdom', 'US-Letter', '(', '2026-03-02', 'Male', 18, '09070560963', 'domingonobleza011@gmail.com', 'rvgtuyik', 'iolip;op', 'rvge', 'x 11\")', '', 'grtgr54', 'rgrht', 'yyyuu', '', 'tyyhty', 'bttgtgr', 'rtvrty', '', '4567764'),
(8, 41, '2026-2027', '4568969080', 'domdom', 'US-Letter', '(', '2026-03-10', 'Male', 18, '09070560963', 'domingonobleza011@gmail.com', 'rvgtuyik', 'iolip;op', 'rvge', 'rgrht', '', 'tyyhty', 'rgrht', 'yyyuu', '', 'tyyhty', 'bttgtgr', 'rtvrty', '', '4567764'),
(9, 41, '2026-2027', '4568969080', 'domdom', 'US-Letter', '(', '2026-03-03', 'Male', 18, '09070560963', 'domingonobleza011@gmail.com', 'rvgtuyik', 'iolip;op', 'rvge', 'rgrht', '', 'grtgr54', 'rgrht', 'yyyuu', '', '09070560963', 'bttgtgr', 'rtvrty', 'rvtv34', '4567764'),
(10, 41, '2026-2027', '4568969080', 'domdom', 'US-Letter', '(', '2026-03-03', 'Male', 18, '09070560963', 'domingonobleza011@gmail.com', 'rvgtuyik', 'iolip;op', 'rvge', 'rgrht', '', 'grtgr54', 'rgrht', 'yyyuu', '', '09070560963', 'bttgtgr', 'rtvrty', 'rvtv34', '4567764');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_ten`
--

CREATE TABLE `tbl_ten` (
  `id_ten` int NOT NULL,
  `id_resident` int NOT NULL,
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
  `school_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_ten`
--

INSERT INTO `tbl_ten` (`id_ten`, `id_resident`, `sy`, `lrn`, `course`, `lname`, `fname`, `mi`, `bdate`, `sex`, `age`, `contact`, `email`, `current_address`, `perm_address`, `ffname`, `flname`, `fmi`, `contact_f`, `mlname`, `mfname`, `mmi`, `contact_m`, `lglc`, `lsa`, `lysc`, `school_id`) VALUES
(1, 41, '2026-2027', '4568969080', 'ICT - Computer Programming', 'domdom', 'US-Letter', '(', '2026-03-04', 'Male', 18, '09070560963', 'domingonobleza011@gmail.com', 'rvgtuyik', 'iolip;op', 'rvge', 'rgrht', '', 'grtgr54', 'rgrht', 'yyyuu', '', '09070560963', 'bttgtgr', 'rtvrty', 'rvtv34', '4567764');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_twelve`
--

CREATE TABLE `tbl_twelve` (
  `id_twelve` int NOT NULL,
  `id_resident` int NOT NULL,
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
  `school_id` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_twelve`
--

INSERT INTO `tbl_twelve` (`id_twelve`, `id_resident`, `sy`, `lrn`, `course`, `lname`, `fname`, `mi`, `bdate`, `sex`, `age`, `contact`, `email`, `current_address`, `perm_address`, `ffname`, `flname`, `fmi`, `contact_f`, `mlname`, `mfname`, `mmi`, `contact_m`, `lglc`, `lsa`, `lysc`, `school_id`) VALUES
(1, 41, '2026-2027', '4568969080', 'GAS', 'domdom', 'US-Letter', '(', '2026-03-03', 'Female', 18, '09070560963', 'domingonobleza011@gmail.com', 'rvgtuyik', 'iolip;op', 'rvge', 'rgrht', '', 'grtgr54', 'rgrht', 'yyyuu', '', '09070560963', 'bttgtgr', 'rtvrty', 'rvtv34', '4567764'),
(2, 41, '2026-2027', '4568969080', 'TVL-ICT', 'domdom', 'US-Letter', '(', '2026-03-03', 'Female', 18, '09070560963', 'domingonobleza011@gmail.com', 'rvgtuyik', 'iolip;op', 'rvge', 'rgrht', '', 'grtgr54', 'rgrht', 'yyyuu', '', '09070560963', 'bttgtgr', 'rtvrty', 'rvtv34', '4567764');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_user`
--

CREATE TABLE `tbl_user` (
  `id_user` int NOT NULL,
  `email` varchar(255) NOT NULL,
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
  `addedby` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_user`
--

INSERT INTO `tbl_user` (`id_user`, `email`, `password`, `lname`, `fname`, `mi`, `age`, `sex`, `address`, `contact`, `position`, `role`, `addedby`) VALUES
(11, 'jaymark@gmail.com', 'jaymark', 'lovite', 'jaymark', 'none', 21, 'Male', 'salvacion', '09222555100', 'Barangay Secretary', 'user', 'Vilfamat, Vincent'),
(13, 'domingonobleza011@gmail.com', 'dominga', 'x 11\")', 'US-Letter', '(', 122, 'Male', 'Sagrada, Iriga', '09070569634', 'Chairman', 'user', 'nobleza, domingo'),
(14, 'jaymarklovite@gmail.com', 'jayjay', 'lovite', 'jaymark', 'hamag', 18, 'Male', 'Sagrada, Iriga', '09123456789', 'Chairman', 'user', 'nobleza, domingo');

-- --------------------------------------------------------

--
-- Table structure for table `tbl_youth`
--

CREATE TABLE `tbl_youth` (
  `id_youth` int NOT NULL,
  `fname` varchar(255) NOT NULL,
  `lname` varchar(255) NOT NULL,
  `age` varchar(50) NOT NULL,
  `sex` enum('Male','Female') NOT NULL,
  `civil_status` enum('Single','Married','Solo Parent','Widowed') NOT NULL,
  `contact_number` varchar(15) NOT NULL,
  `email_address` varchar(100) NOT NULL,
  `educ_attain` varchar(100) NOT NULL,
  `emp_status` enum('Employed','Unemployed','Self-Employed','Student') NOT NULL,
  `skill_name` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `tbl_youth`
--

INSERT INTO `tbl_youth` (`id_youth`, `fname`, `lname`, `age`, `sex`, `civil_status`, `contact_number`, `email_address`, `educ_attain`, `emp_status`, `skill_name`) VALUES
(5, 'US-Letter', 'x 11\")', '122', 'Male', 'Single', '09070560963', 'domingonobleza011@gmail.com', 'college graduate', 'Employed', '3defe'),
(6, 'US-Letter', 'x 11\")', '18', 'Male', 'Single', '09070560963', 'domingonobleza011@gmail.com', 'college graduate', 'Employed', 'magkakan');

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
-- Indexes for table `tbl_announcement`
--
ALTER TABLE `tbl_announcement`
  ADD PRIMARY KEY (`id_announcement`);

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
-- Indexes for table `tbl_resident`
--
ALTER TABLE `tbl_resident`
  ADD PRIMARY KEY (`id_resident`);

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
-- Indexes for table `tbl_youth`
--
ALTER TABLE `tbl_youth`
  ADD PRIMARY KEY (`id_youth`);

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
  MODIFY `id_admin` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `tbl_announcement`
--
ALTER TABLE `tbl_announcement`
  MODIFY `id_announcement` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tbl_eight`
--
ALTER TABLE `tbl_eight`
  MODIFY `id_eight` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_eleven`
--
ALTER TABLE `tbl_eleven`
  MODIFY `id_eleven` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_nine`
--
ALTER TABLE `tbl_nine`
  MODIFY `id_nine` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tbl_resident`
--
ALTER TABLE `tbl_resident`
  MODIFY `id_resident` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=43;

--
-- AUTO_INCREMENT for table `tbl_seven`
--
ALTER TABLE `tbl_seven`
  MODIFY `id_seven` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `tbl_ten`
--
ALTER TABLE `tbl_ten`
  MODIFY `id_ten` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `tbl_twelve`
--
ALTER TABLE `tbl_twelve`
  MODIFY `id_twelve` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `tbl_user`
--
ALTER TABLE `tbl_user`
  MODIFY `id_user` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tbl_youth`
--
ALTER TABLE `tbl_youth`
  MODIFY `id_youth` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
