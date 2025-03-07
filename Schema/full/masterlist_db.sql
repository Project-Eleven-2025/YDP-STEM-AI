-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 07, 2025 at 03:50 PM
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
-- Database: `masterlist_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `assessments`
--

CREATE TABLE `assessments` (
  `id` int(11) NOT NULL,
  `userID` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `available_courses`
--

CREATE TABLE `available_courses` (
  `course_id` varchar(50) NOT NULL,
  `course_name` varchar(255) NOT NULL,
  `course_description` text DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `instructor_name` varchar(50) NOT NULL,
  `course_data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `certificates`
--

CREATE TABLE `certificates` (
  `id` int(11) NOT NULL,
  `userID` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `course`
--

CREATE TABLE `course` (
  `courseID` varchar(50) NOT NULL,
  `label` varchar(255) NOT NULL,
  `type` enum('lesson','quiz','assessment','memo') NOT NULL,
  `date_created` datetime NOT NULL DEFAULT current_timestamp(),
  `created_by` varchar(50) NOT NULL,
  `access_control` varchar(50) NOT NULL,
  `quiz_data` blob NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `lessons`
--

CREATE TABLE `lessons` (
  `id` int(11) NOT NULL,
  `userID` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_session_logs`
--

CREATE TABLE `login_session_logs` (
  `session_id` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `logged_out_at` datetime DEFAULT NULL,
  `user_id` varchar(255) DEFAULT NULL,
  `device_os` varchar(255) DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_session_logs`
--

INSERT INTO `login_session_logs` (`session_id`, `created_at`, `logged_out_at`, `user_id`, `device_os`, `ip_address`) VALUES
('0hnl7d0qqpp7qg65idilhortr1', '2025-03-07 02:47:38', '2025-03-06 19:50:23', '2025-student-8174399054-4095-0306', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '::1'),
('6svqn1e45jpcvdkddv9o31j7j0', '2025-03-07 03:15:06', '2025-03-06 20:17:45', '2025-student-8174399054-4095-0306', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '::1'),
('82q64dvben3cirb6be2re03ois', '2025-03-07 03:23:30', NULL, '2025-student-8174399054-4095-0306', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '::1'),
('hs1e8v92cpsgs28oo93df2bpn5', '2025-03-07 02:23:13', '2025-03-06 19:47:33', '2025-student-8174399054-4095-0306', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '::1'),
('l2vhphjc9jebm4tfpt51jgtfuf', '2025-03-07 03:17:50', '2025-03-06 20:23:25', '2025-student-8174399054-4095-0306', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '::1'),
('vr5vl1jk5ijsopr4pojpdt615d', '2025-03-07 07:55:02', '2025-03-07 01:32:09', '2025-student-8174399054-4095-0306', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/133.0.0.0 Safari/537.36', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `quizzes`
--

CREATE TABLE `quizzes` (
  `id` int(11) NOT NULL,
  `userID` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `teachers_info`
--

CREATE TABLE `teachers_info` (
  `teacherID` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `pass_hash` varchar(255) NOT NULL,
  `teacher_emailadd` varchar(100) DEFAULT NULL,
  `teacher_phonenum` varchar(20) DEFAULT NULL,
  `teacher_fname` varchar(50) DEFAULT NULL,
  `teacher_lname` varchar(50) DEFAULT NULL,
  `teacher_mname` varchar(50) DEFAULT NULL,
  `teacher_post_nominal` varchar(50) DEFAULT NULL,
  `teacher_birthdate` date DEFAULT NULL,
  `teacher_address` text DEFAULT NULL,
  `teacher_gender` varchar(10) DEFAULT NULL,
  `teacher_faculty` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_info`
--

CREATE TABLE `user_info` (
  `userID` varchar(50) NOT NULL,
  `username` varchar(50) NOT NULL,
  `pass_hash` varchar(64) NOT NULL,
  `user_emailadd` varchar(50) NOT NULL,
  `user_phonenum` varchar(10) NOT NULL,
  `user_fname` varchar(50) NOT NULL,
  `user_lname` varchar(50) NOT NULL,
  `user_mname` varchar(50) DEFAULT NULL,
  `user_nickname` varchar(25) DEFAULT NULL,
  `user_birthdate` date NOT NULL,
  `user_address` varchar(50) NOT NULL,
  `user_gender` varchar(10) NOT NULL,
  `user_school` varchar(50) NOT NULL,
  `courseID` varchar(50) NOT NULL,
  `datecreated` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `user_info`
--

INSERT INTO `user_info` (`userID`, `username`, `pass_hash`, `user_emailadd`, `user_phonenum`, `user_fname`, `user_lname`, `user_mname`, `user_nickname`, `user_birthdate`, `user_address`, `user_gender`, `user_school`, `courseID`, `datecreated`) VALUES
('2025-student-8174399054-4095-0306', 'Luco', '$2y$10$..BQOS1dCa8RZzouFoAi8OKeRjpDVfeFSirxmWgEmI7L1bfbLE4fm', 'revchannel01@gmail.com', '0915514363', 'Richard', 'Villareal', 'B', 'RJ', '2003-04-02', '19 1st st. Gabriel ext', 'male', 'EARIST', 'math-teacher', '2025-03-06 18:23:08');

--
-- Triggers `user_info`
--
DELIMITER $$
CREATE TRIGGER `user_nickname_trigger` BEFORE INSERT ON `user_info` FOR EACH ROW BEGIN
    
    IF NEW.user_nickname IS NULL THEN
        SET NEW.user_nickname = NEW.user_fname;
    END IF;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `user_milestone`
--

CREATE TABLE `user_milestone` (
  `milestone_userID` varchar(50) NOT NULL,
  `milestone_courseID` varchar(5) NOT NULL,
  `milestone_lesson` int(11) NOT NULL,
  `milestone_progress` decimal(5,2) NOT NULL DEFAULT 0.00,
  `milestone_checkpoints` int(11) DEFAULT NULL,
  `milestone_status` enum('In Progress','Completed') DEFAULT 'In Progress',
  `milestone_certificate_userID` varchar(50) DEFAULT NULL,
  `milestone_certificate_courseID` varchar(5) DEFAULT NULL,
  `milestone_user_performance` varchar(255) DEFAULT NULL,
  `milestone_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Triggers `user_milestone`
--
DELIMITER $$
CREATE TRIGGER `status_completion_trigger` BEFORE UPDATE ON `user_milestone` FOR EACH ROW BEGIN
    
    IF NEW.milestone_progress = 100 THEN
        SET NEW.milestone_status = 'Completed';

        
        SET NEW.milestone_certificate_userID = NEW.milestone_userID;
        SET NEW.milestone_certificate_courseID = NEW.milestone_courseID;
    END IF;
END
$$
DELIMITER ;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `assessments`
--
ALTER TABLE `assessments`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `available_courses`
--
ALTER TABLE `available_courses`
  ADD PRIMARY KEY (`course_id`),
  ADD UNIQUE KEY `instructor_name` (`instructor_name`),
  ADD UNIQUE KEY `instructor_name_3` (`instructor_name`),
  ADD KEY `instructor_name_2` (`instructor_name`),
  ADD KEY `instructor_name_4` (`instructor_name`);

--
-- Indexes for table `certificates`
--
ALTER TABLE `certificates`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `course`
--
ALTER TABLE `course`
  ADD PRIMARY KEY (`courseID`),
  ADD KEY `course_ibfk_2` (`created_by`);

--
-- Indexes for table `lessons`
--
ALTER TABLE `lessons`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `login_session_logs`
--
ALTER TABLE `login_session_logs`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `quizzes`
--
ALTER TABLE `quizzes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `teachers_info`
--
ALTER TABLE `teachers_info`
  ADD PRIMARY KEY (`teacherID`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `teacher_emailadd` (`teacher_emailadd`),
  ADD UNIQUE KEY `teacher_phonenum` (`teacher_phonenum`);

--
-- Indexes for table `user_info`
--
ALTER TABLE `user_info`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `user_emailadd` (`user_emailadd`),
  ADD UNIQUE KEY `user_phonenum` (`user_phonenum`);

--
-- Indexes for table `user_milestone`
--
ALTER TABLE `user_milestone`
  ADD PRIMARY KEY (`milestone_userID`,`milestone_courseID`),
  ADD KEY `milestone_courseID` (`milestone_courseID`),
  ADD KEY `milestone_lesson` (`milestone_lesson`),
  ADD KEY `milestone_certificate_userID` (`milestone_certificate_userID`,`milestone_certificate_courseID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `assessments`
--
ALTER TABLE `assessments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `certificates`
--
ALTER TABLE `certificates`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `lessons`
--
ALTER TABLE `lessons`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `quizzes`
--
ALTER TABLE `quizzes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `available_courses`
--
ALTER TABLE `available_courses`
  ADD CONSTRAINT `available_courses_ibfk_1` FOREIGN KEY (`instructor_name`) REFERENCES `teachers_info` (`username`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `course`
--
ALTER TABLE `course`
  ADD CONSTRAINT `course_ibfk_2` FOREIGN KEY (`created_by`) REFERENCES `teachers_info` (`teacherID`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `course_ibfk_3` FOREIGN KEY (`courseID`) REFERENCES `available_courses` (`course_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `login_session_logs`
--
ALTER TABLE `login_session_logs`
  ADD CONSTRAINT `login_session_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `user_info` (`userID`);

--
-- Constraints for table `user_milestone`
--
ALTER TABLE `user_milestone`
  ADD CONSTRAINT `user_milestone_ibfk_1` FOREIGN KEY (`milestone_userID`) REFERENCES `user_info` (`userID`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
