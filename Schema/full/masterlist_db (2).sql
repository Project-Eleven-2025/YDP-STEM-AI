-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 23, 2025 at 10:31 PM
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
CREATE DATABASE IF NOT EXISTS `masterlist_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci;
USE `masterlist_db`;

-- --------------------------------------------------------

--
-- Table structure for table `classes`
--

CREATE TABLE `classes` (
  `classID` int(11) NOT NULL,
  `teacherID` int(11) NOT NULL,
  `class_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `class_schedule` varchar(255) DEFAULT NULL,
  `number_of_hours` int(11) DEFAULT NULL,
  `lesson_set_list` blob DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_attempts`
--

CREATE TABLE `login_attempts` (
  `id` int(11) NOT NULL,
  `session_id` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `logged_out_at` timestamp NULL DEFAULT NULL,
  `user_id` int(11) NOT NULL,
  `device_os` varchar(255) NOT NULL,
  `ip_address` varchar(45) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_attempts`
--

INSERT INTO `login_attempts` (`id`, `session_id`, `created_at`, `logged_out_at`, `user_id`, `device_os`, `ip_address`) VALUES
(1, 'pdjg2rhevc1hsmvs2jk8n65t0b', '2025-03-23 20:51:31', '2025-03-23 13:55:49', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '::1'),
(2, 'fdcu0p1f83csf2tgdrhilul0ig', '2025-03-23 20:59:36', '2025-03-23 14:12:37', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '::1'),
(3, 'fdcu0p1f83csf2tgdrhilul0ig', '2025-03-23 21:03:08', '2025-03-23 14:12:37', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '::1'),
(4, 'fdcu0p1f83csf2tgdrhilul0ig', '2025-03-23 21:03:08', '2025-03-23 14:12:37', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '::1'),
(5, 'fdcu0p1f83csf2tgdrhilul0ig', '2025-03-23 21:03:45', '2025-03-23 14:12:37', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '::1'),
(6, 'uom3uh8a7cr0mdd7lc4iurfkoa', '2025-03-23 21:12:57', '2025-03-23 14:19:21', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '::1'),
(7, 'uom3uh8a7cr0mdd7lc4iurfkoa', '2025-03-23 21:12:57', '2025-03-23 14:19:21', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '::1'),
(8, 'bn5b773cr63sf5crrvb48ohvdj', '2025-03-23 21:19:26', '2025-03-23 14:26:03', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '::1'),
(9, 'bn5b773cr63sf5crrvb48ohvdj', '2025-03-23 21:19:26', '2025-03-23 14:26:03', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '::1'),
(10, '829826kc1pg64bqg13vuv4nf1k', '2025-03-23 21:26:35', '2025-03-23 14:27:57', 1, 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/134.0.0.0 Safari/537.36', '::1');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `fname` varchar(50) NOT NULL,
  `lname` varchar(50) NOT NULL,
  `mname` varchar(50) DEFAULT NULL,
  `uname` varchar(50) NOT NULL,
  `nname` varchar(50) DEFAULT NULL,
  `email` varchar(100) NOT NULL,
  `birthdate` date NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `gender` enum('male','female') NOT NULL,
  `course` varchar(255) DEFAULT NULL,
  `school` varchar(100) NOT NULL,
  `passphrase` varchar(255) NOT NULL,
  `user_group` enum('student','teacher','admin') DEFAULT 'student',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `fname`, `lname`, `mname`, `uname`, `nname`, `email`, `birthdate`, `address`, `phone`, `gender`, `course`, `school`, `passphrase`, `user_group`, `created_at`) VALUES
(1, 'Richard', 'Villareal', 'B', 'Revdeluxe', 'Luco', 'revchannel01@gmail.com', '2003-04-02', '19 1st st. Gabriel ext', '09155143634', 'male', 'math-teacher', 'EARIST', '$2y$10$B5ZFwjaPkxCCgHeiDJ4cyOMO2O9itaLtbWhS4ARZxdHVT1y/ZcDCO', 'admin', '2025-03-23 12:12:45');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `classes`
--
ALTER TABLE `classes`
  ADD PRIMARY KEY (`classID`),
  ADD KEY `teacherID` (`teacherID`);

--
-- Indexes for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uname` (`uname`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `classes`
--
ALTER TABLE `classes`
  MODIFY `classID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_attempts`
--
ALTER TABLE `login_attempts`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `classes`
--
ALTER TABLE `classes`
  ADD CONSTRAINT `classes_ibfk_1` FOREIGN KEY (`teacherID`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `login_attempts`
--
ALTER TABLE `login_attempts`
  ADD CONSTRAINT `login_attempts_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
