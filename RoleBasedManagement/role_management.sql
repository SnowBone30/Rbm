-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 22, 2025 at 08:50 PM
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
-- Database: `role_management`
--

-- --------------------------------------------------------

--
-- Table structure for table `login_logs`
--

CREATE TABLE `login_logs` (
  `id` int(11) NOT NULL,
  `username` varchar(100) DEFAULT NULL,
  `role` varchar(50) DEFAULT NULL,
  `status` varchar(20) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_logs`
--

INSERT INTO `login_logs` (`id`, `username`, `role`, `status`, `ip_address`, `created_at`) VALUES
(1, 'admin', 'admin', 'failed', '::1', '2025-06-22 17:17:32'),
(2, 'admin', 'admin', 'failed', '::1', '2025-06-22 17:17:41'),
(3, 'user1', 'user', 'failed', '::1', '2025-06-22 17:19:01'),
(4, 'admin', 'admin', 'failed', '::1', '2025-06-22 17:19:18'),
(5, 'admin', 'admin', 'failed', '::1', '2025-06-22 17:24:48'),
(6, 'admin', 'admin', 'failed', '::1', '2025-06-22 17:24:57'),
(7, 'admin', 'admin', 'failed', '::1', '2025-06-22 17:29:32'),
(8, 'admin', 'admin', 'failed', '::1', '2025-06-22 17:29:39'),
(9, 'admin', 'admin', 'failed', '::1', '2025-06-22 17:29:48'),
(10, 'admin', 'admin', 'failed', '::1', '2025-06-22 17:30:46'),
(11, 'admin', 'admin', 'failed', '::1', '2025-06-22 17:31:27'),
(12, 'admin', 'admin', 'failed', '::1', '2025-06-22 17:46:01'),
(13, 'admin', 'admin', 'failed', '::1', '2025-06-22 17:46:09'),
(14, 'admin', 'admin', 'failed', '::1', '2025-06-22 17:47:21'),
(15, 'admin', 'admin', 'failed', '::1', '2025-06-22 17:47:28'),
(16, 'admin123', 'admin', 'failed', '::1', '2025-06-22 17:48:55'),
(17, 'admin123', 'admin', 'success', '::1', '2025-06-22 17:49:04'),
(18, 'user2', 'staff', 'success', '::1', '2025-06-22 18:26:22'),
(19, 'user2', 'staff', 'success', '::1', '2025-06-22 18:40:26'),
(20, 'user2', 'staff', 'success', '::1', '2025-06-22 18:47:00');

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` int(11) NOT NULL,
  `role_name` varchar(100) NOT NULL,
  `can_create` tinyint(1) DEFAULT 0,
  `can_read` tinyint(1) DEFAULT 0,
  `can_edit` tinyint(1) DEFAULT 0,
  `can_delete` tinyint(1) DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `role_name`, `can_create`, `can_read`, `can_edit`, `can_delete`) VALUES
(1, 'hehehe', 0, 1, 1, 1),
(3, 'doe', 0, 1, 1, 1),
(4, 'Staff1', 0, 1, 0, 0);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` varchar(5) NOT NULL,
  `failed_attempts` int(11) DEFAULT 0,
  `last_failed_login` datetime DEFAULT NULL,
  `can_create` tinyint(1) DEFAULT 0,
  `can_read` tinyint(1) DEFAULT 0,
  `can_edit` tinyint(1) DEFAULT 0,
  `can_delete` tinyint(1) DEFAULT 0,
  `last_login` datetime DEFAULT NULL,
  `account_status` enum('active','inactive') DEFAULT 'active',
  `deactivation_requested_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`, `failed_attempts`, `last_failed_login`, `can_create`, `can_read`, `can_edit`, `can_delete`, `last_login`, `account_status`, `deactivation_requested_at`) VALUES
(1, 'admin', 'admin123', 'admin', 0, NULL, 0, 0, 0, 0, NULL, 'active', NULL),
(3, 'JhonWick', '$2y$10$SemGSxT9FxaodJ8lNwbuKO56O3.QEMCFwBS0WeOYdUaBOLJFPpZ5i', 'staff', 0, NULL, 1, 1, 1, 0, NULL, 'active', NULL),
(5, 'JonWick', '$2y$10$fUIx/Ir8UxX2UHFChJkUlOEavrfYjSYXPDCTVvK3HIVuNBCxyFw1K', '', 0, NULL, 1, 1, 1, 0, NULL, 'active', NULL),
(7, 'user11', '$2y$10$7IgD9xiNQiFmS6OoQ5wNo.UJax.Mbmfqzt32W1gYlKjdOR27qfwjO', 'admin', 0, NULL, 1, 1, 0, 0, NULL, 'active', NULL),
(8, 'admin123', '$2y$10$BvAZFtUXf.ATigPaOcgoqeiwriY8.i.Z.XX3OGEB1ZLH86aMUyvN6', 'admin', 0, NULL, 1, 1, 1, 1, NULL, 'active', NULL),
(9, 'user2', '$2y$10$wU7WGYrsENHQVVDDHqKw4.i37QoPUdiqUP6GEOm87pn75mWw.9lfW', 'staff', 0, NULL, 0, 1, 0, 0, '2025-06-23 02:47:00', 'active', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `login_logs`
--
ALTER TABLE `login_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `login_logs`
--
ALTER TABLE `login_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
