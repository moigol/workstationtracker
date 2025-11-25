-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 23, 2025 at 07:26 AM
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
-- Database: `rfidlogger`
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
  `date_time` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `logs`
--

INSERT INTO `logs` (`id`, `type`, `scanner_id`, `tag_id`, `date_time`) VALUES
(1, 'In', 1, 'd21dccab', '2025-09-11 20:12:15'),
(2, 'In', 1, 'd21dccab', '2025-09-11 20:48:40'),
(3, 'In', 1, 'd21dccab', '2025-09-11 20:49:33'),
(4, 'In', 1, '95b2d9bc', '2025-09-11 23:24:11'),
(5, 'In', 1, '95b2d9bc', '2025-09-11 23:25:18'),
(6, 'In', 1, 'a5503947', '2025-09-11 23:26:11'),
(7, 'In', 1, '025f603e344000', '2025-09-11 23:26:48'),
(8, 'In', 1, 'c3bf7839', '2025-09-11 23:27:15'),
(9, 'In', 1, 'd21dccab', '2025-09-11 23:27:20'),
(10, 'In', 1, 'c3bf7839', '2025-09-12 01:07:09'),
(11, 'In', 1, 'd21dccab', '2025-09-12 01:07:43'),
(12, 'In', 1, 'd21dccab', '2025-09-12 01:11:56'),
(13, 'In', 1, 'd21dccab', '2025-09-12 01:12:32'),
(14, 'In', 1, 'd21dccab', '2025-09-12 01:30:22'),
(15, 'In', 1, 'd21dccab', '2025-09-12 01:30:26'),
(16, 'In', 1, 'd21dccab', '2025-09-12 01:30:31'),
(17, 'In', 1, 'd21dccab', '2025-09-12 01:47:58'),
(18, 'In', 2, 'd21dccab', '2025-09-12 01:58:49'),
(19, 'In', 2, 'c3bf7839', '2025-09-12 01:58:53'),
(28, 'In', 2, 'c3bf7839', '2025-09-12 03:21:06'),
(29, 'Out', 1, 'c3bf7839', '2025-10-29 22:48:38'),
(30, 'In', 1, 'c3bf7839', '2025-10-29 22:49:02'),
(31, 'Out', 1, 'c3bf7839', '2025-10-29 23:14:04');

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
(1, 'd21dccab', 'Key Fob', 'Key Chain tag', '2025-09-10 16:09:42'),
(2, 'c3bf7839', 'White Card', 'Plain white card which is printable.', '2025-09-10 16:09:58'),
(4, '95b2d9bc', 'Simpliciti MASTERCARD', 'MASTERCARD CC', '2025-09-12 04:57:36'),
(5, '025f603e344000', 'Simpliciti REWARDS', 'REWARDS VISA PLATINUM', '2025-09-12 04:58:42');

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
(1, 'Lobby', 'IC-RFID-0001', '2025-09-10 14:49:46'),
(2, 'Cafeteria', 'IC-RFID-0002', '2025-09-10 16:04:38'),
(3, 'Production', 'IC-RFID-0003', '2025-09-10 16:05:26'),
(4, 'HR Office', 'IC-RFID-0004', '2025-09-10 16:07:28'),
(5, 'Engineering Office', 'IC-RFID-0005', '2025-09-10 16:07:34');

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
(39, 2, 'cf6f9dda', NULL, '2025-09-12 02:14:43');

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `rfid_tags`
--
ALTER TABLE `rfid_tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `scanners`
--
ALTER TABLE `scanners`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `tags`
--
ALTER TABLE `tags`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

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
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
