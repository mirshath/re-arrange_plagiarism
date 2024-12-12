-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 10, 2024 at 10:20 AM
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
-- Database: `plagiarism-checker`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('super_admin','plagiarism_checker') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `email`, `password`, `role`) VALUES
(4, 'Admin 1', 'admin@gmail.com', '$2y$10$TVyI5XiNGq6k4gHbWX8axuCidVpm8yhoIlX8i8jUfYBQNKqdQfdoG', 'super_admin'),
(12, 'Checker 1', 'mirshath.mmm@gmail.com', '$2y$10$FhVXKB7ZckpBnCnmYkh//ObehfgBuuIb1E4CVjYz0WdErdP/tOk3i', 'plagiarism_checker'),
(13, 'checker 2', 'mirmirsha123@gmail.com', '$2y$10$LSBOsppetIjrCgCL0jvIyOq6VaOUTq0iVhR0sQgBczmRguIiiqQZC', 'plagiarism_checker'),
(14, 'Checker 3', 'yournumplz@gmail.com', '$2y$10$LSBOsppetIjrCgCL0jvIyOq6VaOUTq0iVhR0sQgBczmRguIiiqQZC', 'plagiarism_checker');

-- --------------------------------------------------------

--
-- Table structure for table `allocate_checker`
--

CREATE TABLE `allocate_checker` (
  `id` int(11) NOT NULL,
  `student_id` int(20) NOT NULL,
  `student_reg_id` varchar(50) NOT NULL,
  `checker_id` int(20) NOT NULL,
  `batch_id` int(11) NOT NULL,
  `submitted_status` varchar(50) NOT NULL DEFAULT 'not_yet',
  `display` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `allocate_checker`
--

INSERT INTO `allocate_checker` (`id`, `student_id`, `student_reg_id`, `checker_id`, `batch_id`, `submitted_status`, `display`, `created_at`) VALUES
(469, 6, '2000', 14, 2, 'submitted', 0, '2024-12-03 09:18:51'),
(470, 16, '2011', 12, 2, 'not_yet', 0, '2024-12-03 09:18:51'),
(471, 7, '2001', 13, 2, 'not_yet', 0, '2024-12-03 09:18:51'),
(472, 9, '2002', 14, 2, 'not_yet', 0, '2024-12-03 09:18:51'),
(476, 13, '2008', 13, 4, 'not_yet', 0, '2024-12-05 09:18:51'),
(477, 10, '2003', 14, 4, 'submitted', 0, '2024-12-05 09:18:51'),
(478, 12, '2007', 12, 4, 'not_yet', 0, '2024-12-05 09:18:51'),
(479, 14, '2009', 13, 4, 'not_yet', 0, '2024-12-05 09:18:51'),
(480, 11, '2006', 14, 4, 'not_yet', 0, '2024-12-05 09:18:51'),
(483, 50, '3333', 13, 2, 'not_yet', 0, '2024-12-09 16:36:17'),
(484, 51, '456', 14, 2, 'not_yet', 0, '2024-12-09 16:36:17');

-- --------------------------------------------------------

--
-- Table structure for table `batch_table`
--

CREATE TABLE `batch_table` (
  `id` int(11) NOT NULL,
  `batch_name` varchar(255) NOT NULL,
  `program_id` int(5) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `batch_table`
--

INSERT INTO `batch_table` (`id`, `batch_name`, `program_id`) VALUES
(1, 'IFD_B1', 1),
(2, 'IFD_B2', 1),
(3, 'HND_B1', 2),
(4, 'HND_B2', 2),
(5, 'Batch_Test_Program_1', 4),
(6, 'Batch 11', 5);

-- --------------------------------------------------------

--
-- Table structure for table `checkers`
--

CREATE TABLE `checkers` (
  `id` int(11) NOT NULL,
  `checker_name` varchar(100) NOT NULL,
  `checker_email` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `checkers`
--

INSERT INTO `checkers` (`id`, `checker_name`, `checker_email`) VALUES
(12, 'Checker 1', 'mirshath.mmm@gmail.com'),
(13, 'checker 2', 'mirmirsha123@gmail.com'),
(14, 'Checker 3', 'yournumplz@gmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `module_attempt`
--

CREATE TABLE `module_attempt` (
  `id` int(11) NOT NULL,
  `student_id` int(5) NOT NULL,
  `module_id` int(5) NOT NULL,
  `attempts` int(5) NOT NULL,
  `created_at` datetime NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `module_table`
--

CREATE TABLE `module_table` (
  `id` int(11) NOT NULL,
  `module_name` varchar(200) NOT NULL,
  `program_id` int(5) NOT NULL,
  `deadline` date DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `module_table`
--

INSERT INTO `module_table` (`id`, `module_name`, `program_id`, `deadline`) VALUES
(1, 'IFD_MODULE_1', 1, '2024-12-20'),
(2, 'IFD_MODULE_2', 1, '2024-12-01'),
(3, 'HND_MODULE_1', 2, '2024-12-27'),
(4, 'HND_MODULE_2', 2, '2024-11-05'),
(8, 'Test Prog Module 1', 4, '2024-12-28'),
(9, 'Test Prog Module 2', 4, '2024-12-28');

-- --------------------------------------------------------

--
-- Table structure for table `old_student_db`
--

CREATE TABLE `old_student_db` (
  `id` int(11) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `DOB` date NOT NULL,
  `program_id` int(10) NOT NULL,
  `batch_id` int(10) NOT NULL,
  `email` varchar(50) NOT NULL,
  `bms_email` varchar(50) NOT NULL,
  `phone_no` varchar(50) NOT NULL,
  `allocate` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `old_student_db`
--

INSERT INTO `old_student_db` (`id`, `student_id`, `name`, `DOB`, `program_id`, `batch_id`, `email`, `bms_email`, `phone_no`, `allocate`) VALUES
(6, '2000', 'Mirshath Mohamed', '2024-10-01', 1, 2, 'yournumplz@gmail.com', 'mirmirsha123@gmail.com', '766158014', 'ff'),
(7, '2001', 'Hasni Nihar', '2024-10-01', 1, 2, 'yournumplz@gmail.com', 'mirmirsha123@gmail.coms', '0777123456', 'ff'),
(9, '2002', 'Asela', '2024-10-01', 1, 2, 'yournumplz@gmail.com', 'aselahar@bms.ac.lk', '0777123456', 'ff'),
(10, '2003', 'widhura', '2024-10-01', 2, 4, 'yournumplz@gmail.com', 'widhura@bms.ac.lk', '0777123456', 'allocated'),
(11, '2006', 'AAAA', '2024-10-01', 2, 4, 'yournumplz@gmail.com', 'yournumplz@gmail.com', '0777123456', 'allocated'),
(12, '2007', 'BBBB', '2024-10-01', 2, 4, 'yournumplz@gmail.com', 'bb@bms.ac.lk', '0777123456', 'allocated'),
(13, '2008', 'CCC', '2024-10-01', 2, 4, 'yournumplz@gmail.com', 'cccc@bms.ac.lk', '0777123456', 'allocated'),
(14, '2009', 'DDDD', '2024-10-01', 2, 4, 'yournumplz@gmail.com', 'DDDD@bms.ac.lk', '0777123456', 'allocated'),
(16, '2011', 'Mirshath Mohamed', '2024-10-01', 1, 2, 'yournumplz@gmail.com', 'mirsddddhath@bms.ac.lk', '766158014', ''),
(50, '3333', 'Mirshath Mohamed', '0000-00-00', 1, 2, '333@gmail.com', '4444@gmail.com', '766158014', 'allocated'),
(51, '456', 'Mirshath Mohamed', '0000-00-00', 1, 2, '767@gmail.com', '76784@gmail.com', '766158014', 'allocated');

-- --------------------------------------------------------

--
-- Table structure for table `portal`
--

CREATE TABLE `portal` (
  `id` int(11) NOT NULL,
  `portal_status` varchar(255) NOT NULL DEFAULT 'on'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `portal`
--

INSERT INTO `portal` (`id`, `portal_status`) VALUES
(1, 'on');

-- --------------------------------------------------------

--
-- Table structure for table `program_table`
--

CREATE TABLE `program_table` (
  `id` int(11) NOT NULL,
  `program_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `program_table`
--

INSERT INTO `program_table` (`id`, `program_name`) VALUES
(1, 'IFD'),
(2, 'HND'),
(4, 'Test Program Name 1'),
(5, 'IFDsdsdasd');

-- --------------------------------------------------------

--
-- Table structure for table `std_crs_details`
--

CREATE TABLE `std_crs_details` (
  `id` int(11) NOT NULL,
  `student_id` int(10) NOT NULL,
  `program_id` int(10) NOT NULL,
  `module_id` int(10) NOT NULL,
  `batch_id` int(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_submitted_form`
--

CREATE TABLE `student_submitted_form` (
  `id` int(11) NOT NULL,
  `student_id` varchar(255) NOT NULL,
  `date_of_birth` date NOT NULL,
  `name_full` varchar(255) NOT NULL,
  `bms_email` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `program_id` int(10) NOT NULL,
  `batch_id` int(10) NOT NULL,
  `module_id` int(10) NOT NULL,
  `Documents` varchar(255) DEFAULT NULL,
  `Documents_1` varchar(255) DEFAULT NULL,
  `Documents_2` varchar(255) DEFAULT NULL,
  `doc_status` varchar(25) NOT NULL DEFAULT 'submitted',
  `attempt` int(10) NOT NULL,
  `submitted_at` datetime NOT NULL DEFAULT current_timestamp(),
  `submitted_at_2nd_time` datetime DEFAULT NULL,
  `submitted_at_3rd_time` datetime DEFAULT NULL,
  `checker_id` int(10) DEFAULT NULL,
  `checker_downlaoded_at` datetime DEFAULT NULL,
  `checked_status` varchar(30) NOT NULL DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `allocate_checker`
--
ALTER TABLE `allocate_checker`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `student_id_2` (`student_id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `checker_id` (`checker_id`);

--
-- Indexes for table `batch_table`
--
ALTER TABLE `batch_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `checkers`
--
ALTER TABLE `checkers`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `module_attempt`
--
ALTER TABLE `module_attempt`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `module_table`
--
ALTER TABLE `module_table`
  ADD PRIMARY KEY (`id`),
  ADD KEY `program_id` (`program_id`);

--
-- Indexes for table `old_student_db`
--
ALTER TABLE `old_student_db`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bms_email` (`bms_email`),
  ADD UNIQUE KEY `student_id` (`student_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `portal`
--
ALTER TABLE `portal`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `program_table`
--
ALTER TABLE `program_table`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `std_crs_details`
--
ALTER TABLE `std_crs_details`
  ADD PRIMARY KEY (`id`),
  ADD KEY `student_id` (`student_id`),
  ADD KEY `program_id` (`program_id`),
  ADD KEY `module_id` (`module_id`),
  ADD KEY `batch_id` (`batch_id`);

--
-- Indexes for table `student_submitted_form`
--
ALTER TABLE `student_submitted_form`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `allocate_checker`
--
ALTER TABLE `allocate_checker`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=485;

--
-- AUTO_INCREMENT for table `batch_table`
--
ALTER TABLE `batch_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `checkers`
--
ALTER TABLE `checkers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `module_attempt`
--
ALTER TABLE `module_attempt`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `module_table`
--
ALTER TABLE `module_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `old_student_db`
--
ALTER TABLE `old_student_db`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=54;

--
-- AUTO_INCREMENT for table `portal`
--
ALTER TABLE `portal`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `program_table`
--
ALTER TABLE `program_table`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `std_crs_details`
--
ALTER TABLE `std_crs_details`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `student_submitted_form`
--
ALTER TABLE `student_submitted_form`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=160;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `allocate_checker`
--
ALTER TABLE `allocate_checker`
  ADD CONSTRAINT `allocate_checker_ibfk_1` FOREIGN KEY (`student_id`) REFERENCES `old_student_db` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `allocate_checker_ibfk_2` FOREIGN KEY (`checker_id`) REFERENCES `checkers` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `batch_table`
--
ALTER TABLE `batch_table`
  ADD CONSTRAINT `batch_table_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `program_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `module_table`
--
ALTER TABLE `module_table`
  ADD CONSTRAINT `module_table_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `program_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `old_student_db`
--
ALTER TABLE `old_student_db`
  ADD CONSTRAINT `old_student_db_ibfk_1` FOREIGN KEY (`program_id`) REFERENCES `program_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `old_student_db_ibfk_2` FOREIGN KEY (`batch_id`) REFERENCES `batch_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `std_crs_details`
--
ALTER TABLE `std_crs_details`
  ADD CONSTRAINT `std_crs_details_ibfk_1` FOREIGN KEY (`batch_id`) REFERENCES `batch_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `std_crs_details_ibfk_2` FOREIGN KEY (`module_id`) REFERENCES `module_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `std_crs_details_ibfk_3` FOREIGN KEY (`program_id`) REFERENCES `program_table` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `std_crs_details_ibfk_4` FOREIGN KEY (`student_id`) REFERENCES `old_student_db` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
