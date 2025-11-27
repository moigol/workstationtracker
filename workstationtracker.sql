-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2025 at 04:47 PM
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
-- Database: `workstationtracker`
--

-- --------------------------------------------------------

--
-- Table structure for table `items`
--

CREATE TABLE `items` (
  `id` int(11) NOT NULL,
  `tag_id` varchar(100) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `items`
--

INSERT INTO `items` (`id`, `tag_id`, `name`, `description`, `date_added`) VALUES
(1, 'd21dccab', 'Allen Wrench', 'Allen wrench full set', '2025-09-10 16:17:39'),
(2, 'c3bf7839', 'Caliper', 'Battery operated', '2025-09-10 16:17:45'),
(9, '95b2d9bc', 'Digital Multimeter', 'Digital multi-meter and 3 probes', '2025-09-12 08:03:19'),
(10, '025f603e344000', 'Microcsope', '1080p Macro auto depth', '2025-09-12 08:03:56');

-- --------------------------------------------------------

--
-- Table structure for table `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `type` enum('In','Out') NOT NULL DEFAULT 'In',
  `scanner_id` int(11) DEFAULT NULL,
  `tag_id` varchar(100) NOT NULL,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `date_time_out` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `type`, `scanner_id`, `tag_id`, `date_time`, `date_time_out`) VALUES
(1, 'In', 1, 'c3bf7839', '2025-11-25 05:57:42', '2025-11-25 14:37:44'),
(2, 'In', 1, 'd21dccab', '2025-11-25 06:38:17', '2025-11-25 15:08:44'),
(3, 'In', 1, 'a5503947', '2025-11-25 08:09:09', '2025-11-25 16:39:44'),
(4, 'In', 3, '95b2d9bc', '2025-11-25 15:06:21', '2025-11-25 23:36:44'),
(5, 'Out', 3, '95b2d9bc', '2025-11-25 15:14:39', NULL),
(6, 'In', 3, '95b2d9bc', '2025-11-25 15:14:53', '2025-11-25 23:45:44'),
(7, 'Out', 1, 'c3bf7839', '2025-11-25 15:15:32', NULL),
(8, 'In', 1, 'c3bf7839', '2025-11-25 15:16:52', '2025-11-25 23:47:44'),
(9, 'Out', 1, 'c3bf7839', '2025-11-25 15:16:57', NULL),
(10, 'In', 1, 'c3bf7839', '2025-11-25 15:20:51', '2025-11-25 23:51:44');

-- --------------------------------------------------------

--
-- Table structure for table `rfid_tags`
--

CREATE TABLE `rfid_tags` (
  `id` int(11) NOT NULL,
  `tag_id` varchar(100) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rfid_tags`
--

INSERT INTO `rfid_tags` (`id`, `tag_id`, `name`, `description`, `date_added`) VALUES
(1, 'd21dccab', 'Key Fob', '', '2025-09-10 16:09:42'),
(2, 'c3bf7839', 'White Card', '', '2025-09-10 16:09:58'),
(4, '95b2d9bc', 'Blue Card', '', '2025-09-12 04:57:36'),
(5, '025f603e344000', 'Orange Card', '', '2025-09-12 04:58:42'),
(6, 'a5503947', 'Citi Card', '', '2025-11-25 05:28:24');

-- --------------------------------------------------------

--
-- Table structure for table `scanners`
--

CREATE TABLE `scanners` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `scanners`
--

INSERT INTO `scanners` (`id`, `name`, `description`, `date_added`) VALUES
(1, 'Station 1', 'IC-RFID-0001', '2025-09-10 14:49:46'),
(2, 'Station 2', 'IC-RFID-0002', '2025-09-10 16:04:38'),
(3, 'Station 3', 'IC-RFID-0003', '2025-09-10 16:05:26'),
(4, 'Station 4', 'IC-RFID-0004', '2025-09-10 16:07:28'),
(5, 'Station 5', 'IC-RFID-0005', '2025-09-10 16:07:34');

-- --------------------------------------------------------

--
-- Table structure for table `staffs`
--

CREATE TABLE `staffs` (
  `id` int(11) NOT NULL,
  `tag_id` varchar(100) DEFAULT NULL,
  `name` varchar(100) NOT NULL,
  `position` varchar(128) DEFAULT NULL,
  `date_added` timestamp NOT NULL DEFAULT current_timestamp(),
  `avatar` text DEFAULT NULL,
  `allowed_stations` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `staffs`
--

INSERT INTO `staffs` (`id`, `tag_id`, `name`, `position`, `date_added`, `avatar`, `allowed_stations`) VALUES
(1, 'd21dccab', 'Allen Iverson', 'Lead Operator', '2025-09-10 16:17:39', 'avatar-s-1.png', '1|4|5'),
(2, 'c3bf7839', 'Magnus Carlsen', 'Quality Control', '2025-09-10 16:17:45', 'avatar-s-2.png', '1|2|3|4|5'),
(9, '95b2d9bc', 'Luka Doncic', 'Supervisor', '2025-09-12 08:03:19', 'avatar-s-3.png', '3'),
(10, '025f603e344000', 'Anatoly Karpov', 'Manager', '2025-09-12 08:03:56', 'avatar-s-4.png', '2'),
(14, 'a5503947', 'Lito Mano', 'Janitor', '2025-11-25 05:26:23', 'avatar-s-5.png', '1|2|3|4|5');

-- --------------------------------------------------------

--
-- Table structure for table `tags`
--

CREATE TABLE `tags` (
  `id` int(11) NOT NULL,
  `scanner_id` int(11) DEFAULT NULL,
  `tag_id` varchar(100) NOT NULL,
  `description` longtext DEFAULT NULL,
  `date_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tags`
--

INSERT INTO `tags` (`id`, `scanner_id`, `tag_id`, `description`, `date_time`) VALUES
(2, 1, '025f603e344000', NULL, '2025-09-11 20:30:39'),
(3, 1, '95b2d9bc', NULL, '2025-09-11 22:03:16'),
(4, 1, '95b2d9bc', NULL, '2025-09-11 22:04:22'),
(5, 1, '025f603e344000', NULL, '2025-09-11 22:04:32'),
(6, 1, '025f603e344000', NULL, '2025-09-11 22:06:56'),
(7, 1, '95b2d9bc', NULL, '2025-09-11 22:07:02'),
(8, 1, '95b2d9bc', NULL, '2025-09-11 22:09:24'),
(9, 1, '025f603e344000', NULL, '2025-09-11 22:09:28'),
(10, 1, '95b2d9bc', NULL, '2025-09-11 22:10:15'),
(11, 1, '026f202216a000', NULL, '2025-09-11 22:10:45'),
(12, 1, 'a5503947', NULL, '2025-09-11 22:10:56'),
(13, 1, '025f603e344000', NULL, '2025-09-11 22:11:20'),
(14, 1, '025f603e344000', NULL, '2025-09-11 22:11:24'),
(15, 1, '025f603e344000', NULL, '2025-09-11 22:11:28'),
(16, 1, '025f603e344000', NULL, '2025-09-11 22:11:33'),
(17, 1, '025f603e344000', NULL, '2025-09-11 22:14:06'),
(18, 1, '025f603e344000', NULL, '2025-09-11 22:14:12'),
(19, 1, '026f202216a000', NULL, '2025-09-11 22:14:19'),
(20, 1, '026f202216a000', NULL, '2025-09-11 22:14:57'),
(21, 1, '025f603e344000', NULL, '2025-09-11 22:15:05'),
(22, 1, '026f202216a000', NULL, '2025-09-11 22:15:39'),
(23, 1, '95b2d9bc', NULL, '2025-09-11 22:16:00'),
(24, 1, '95b2d9bc', NULL, '2025-09-11 22:16:22'),
(25, 1, '025f603e344000', NULL, '2025-09-11 22:28:10'),
(26, 1, '025f603e344000', NULL, '2025-09-11 22:28:48'),
(27, 1, '95b2d9bc', NULL, '2025-09-11 22:28:54'),
(28, 1, 'a5503947', NULL, '2025-09-11 22:29:17'),
(29, 1, 'a5503947', NULL, '2025-09-11 22:56:24'),
(30, 1, '95b2d9bc', NULL, '2025-09-11 22:57:08'),
(31, 1, '025f603e344000', NULL, '2025-09-11 22:58:15'),
(32, 1, '9236a8ab', NULL, '2025-09-12 01:09:21'),
(33, 1, '9236a8ab', NULL, '2025-09-12 01:09:54'),
(34, 1, 'cf6f9dda', NULL, '2025-09-12 01:10:25'),
(35, 1, '9236a8ab', NULL, '2025-09-12 01:10:45'),
(36, 1, '9236a8ab', NULL, '2025-09-12 01:12:45'),
(37, 2, '9236a8ab', NULL, '2025-09-12 01:58:42'),
(38, 2, 'cf6f9dda', NULL, '2025-09-12 01:58:57'),
(39, 2, 'cf6f9dda', NULL, '2025-09-12 02:14:43'),
(40, 2, '123123123', NULL, '2025-11-23 03:10:47'),
(41, 2, 'asdfasdfasdf', NULL, '2025-11-23 03:15:47'),
(42, 2, '$device', NULL, '2025-11-23 03:18:28'),
(43, 3, 'a5503947', NULL, '2025-11-24 22:27:20'),
(44, 3, 'a5503947', NULL, '2025-11-24 22:28:07');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `full_name` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password_hash`, `email`, `full_name`, `is_active`, `created_at`, `last_login`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@rfidlogger.com', 'System Administrator', 1, '2025-11-20 01:02:20', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_sessions`
--

CREATE TABLE `user_sessions` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `session_token` varchar(64) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `expires_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `items`
--
ALTER TABLE `items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rfid_tags`
--
ALTER TABLE `rfid_tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `scanners`
--
ALTER TABLE `scanners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `staffs`
--
ALTER TABLE `staffs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `tags`
--
ALTER TABLE `tags`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `session_token` (`session_token`),
  ADD KEY `user_id` (`user_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `items`
--
ALTER TABLE `items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `rfid_tags`
--
ALTER TABLE `rfid_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `scanners`
--
ALTER TABLE `scanners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `staffs`
--
ALTER TABLE `staffs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `user_sessions`
--
ALTER TABLE `user_sessions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `user_sessions`
--
ALTER TABLE `user_sessions`
  ADD CONSTRAINT `user_sessions_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

DELIMITER $$
--
-- Events
--
CREATE DEFINER=`root`@`localhost` EVENT `auto_update_date_time_out` ON SCHEDULE EVERY 1 MINUTE STARTS '2025-11-25 13:44:44' ON COMPLETION NOT PRESERVE ENABLE DO BEGIN
    UPDATE logs 
    SET date_time_out = NOW() 
    WHERE type = 'In' 
    AND date_time_out IS NULL 
    AND TIMESTAMPDIFF(MINUTE, date_time, NOW()) >= 30;
END$$

DELIMITER ;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
