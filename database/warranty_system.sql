-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Apr 06, 2026 at 05:09 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `warranty_system`
--
CREATE DATABASE IF NOT EXISTS `warranty_system`;
USE `warranty_system`;

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `ip_address`, `user_agent`, `created_at`) VALUES
(1, 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 08:47:04'),
(2, 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 09:15:02'),
(3, 2, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 09:15:20'),
(4, 2, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 09:15:28'),
(5, 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 09:18:44'),
(6, 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 09:19:27'),
(7, 10, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 09:22:21'),
(8, 10, 'Registered warranty #WRN-69D37D21D3DDB-20260406', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 09:30:09'),
(9, 10, 'Filed claim #CLM-69D37F5A682AC-20260406', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 09:39:38'),
(10, 10, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 09:41:09'),
(11, 3, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 09:42:12'),
(12, 3, 'Started review of claim #CLM-69D37F5A682AC-20260406', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 09:46:18'),
(13, 3, 'Verified claim #CLM-69D37F5A682AC-20260406 - Valid: Yes', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 09:54:26'),
(14, 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 10:00:28'),
(15, 10, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 10:02:39'),
(16, 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 10:17:44'),
(17, 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 10:17:54'),
(18, 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 10:19:02'),
(19, 10, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 10:19:12'),
(20, 10, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 10:36:48'),
(21, 2, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 10:37:12'),
(22, 3, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 10:43:22'),
(23, 3, 'Decision made for claim #CLM-69D37F5A682AC-20260406: REJECTED', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 10:44:30'),
(24, 10, 'Filed claim #CLM-69D38ED1808E7-20260406', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 10:45:37'),
(25, 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 10:45:56'),
(26, 3, 'Started review of claim #CLM-69D38ED1808E7-20260406', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 10:46:32'),
(27, 3, 'Decision made for claim #CLM-69D38ED1808E7-20260406: APPROVED', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 10:46:47'),
(28, 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 11:03:46'),
(29, 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 11:30:27'),
(30, 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 11:35:35'),
(31, 2, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 11:36:10'),
(32, 2, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 11:36:11'),
(33, 2, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 11:37:47'),
(34, 3, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 11:38:37'),
(35, 3, 'Started repair for claim #2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 11:39:17'),
(36, 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 11:42:14'),
(37, 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 11:45:54'),
(38, 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 11:47:49'),
(39, 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:03:47'),
(40, 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:06:01'),
(41, 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:10:19'),
(42, 10, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:12:28'),
(43, 10, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:12:30'),
(44, 10, 'Filed claim #CLM-69D3A3E84E693-20260406', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:15:36'),
(45, 10, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:16:02'),
(46, 2, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:16:30'),
(47, 2, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:18:16'),
(48, 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:18:55'),
(49, 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:20:25'),
(50, 3, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:20:56'),
(51, 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:23:17'),
(52, 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:23:32'),
(53, 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:23:34'),
(54, 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:26:20'),
(55, 3, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:26:35'),
(56, 3, 'Decision made for claim #CLM-69D3A3E84E693-20260406: PENDING_REFUND', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:27:49'),
(57, 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:27:56'),
(58, 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:27:57'),
(59, 2, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:28:09'),
(60, 2, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:28:13'),
(61, 2, 'Denied refund for claim #7. Moved back to approved.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:29:11'),
(62, 2, 'Denied refund for claim #7. Moved back to approved.', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:29:17'),
(63, 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:32:15'),
(64, 10, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:32:24'),
(65, 2, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:35:52'),
(66, 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 12:36:00'),
(67, 10, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:08:34'),
(68, 10, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:09:19'),
(69, 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:09:28'),
(70, 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:09:39'),
(71, 2, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:09:53'),
(72, 2, 'Added pre-sold serial: sn-001-003-009', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:11:01'),
(73, 2, 'Authorized refund for claim #7', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:11:32'),
(74, 10, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:12:19'),
(75, 2, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:16:25'),
(76, 10, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:16:37'),
(77, 2, 'Added pre-sold serial: SN-000-111-222', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:22:13'),
(78, 10, 'Auto-registered warranty #WRN-69D3B3B479BD8-20260406 via serial check', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:23:00'),
(79, 2, 'Assigned claim #1 to technician: John Technician', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:37:04'),
(80, 2, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:39:08'),
(81, 1, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:39:16'),
(82, 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:40:00'),
(83, 3, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:40:23'),
(84, 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:41:26'),
(85, 10, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:42:23'),
(86, 10, 'Filed claim #CLM-69D3B9298BEEF-20260406', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:46:17'),
(87, 10, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:46:24'),
(88, 2, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:46:33'),
(89, 2, 'Assigned claim #2 to technician: John Technician', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:46:59'),
(90, 2, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:47:03'),
(91, 3, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:47:12'),
(92, 3, 'Started review of claim #CLM-69D3B9298BEEF-20260406', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:47:19'),
(93, 3, 'Decision made for claim #CLM-69D3B9298BEEF-20260406: APPROVED', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:48:06'),
(94, 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:48:16'),
(95, 2, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:48:24'),
(96, 2, 'Denied replace for claim #11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:48:47'),
(97, 2, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:48:49'),
(98, 10, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 13:48:59'),
(99, 10, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:00:28'),
(100, 3, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:00:39'),
(101, 3, 'Decision made for claim #CLM-69D38ED1808E7-20260406: APPROVED', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:01:04'),
(102, 3, 'Started repair for claim #2', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:01:16'),
(103, 3, 'Completed repair for claim #CLM-69D38ED1808E7-20260406', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:01:28'),
(104, 3, 'Completed repair for claim #C-NEW-5652', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:01:55'),
(105, 3, 'Completed repair for claim #C-NEW-4629', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:02:03'),
(106, 3, 'Decision made for claim #CLM-69D37F5A682AC-20260406: PENDING_REFUND', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:02:19'),
(107, 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:02:24'),
(108, 2, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:02:32'),
(109, 2, 'Authorized and Finalized refund for claim #1', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:02:46'),
(110, 2, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:03:10'),
(111, 10, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:03:24'),
(112, 10, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:10:06'),
(113, 2, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:11:09'),
(114, 2, 'Assigned claim #11 to technician: John Technician', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:11:31'),
(115, 2, 'Assigned claim #7 to technician: John Technician', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:11:37'),
(116, 2, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:12:09'),
(117, 3, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:12:19'),
(118, 3, 'Decision made for claim #CLM-69D3A3E84E693-20260406: APPROVED', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:12:46'),
(119, 3, 'Decision made for claim #CLM-69D3B9298BEEF-20260406: PENDING_REFUND', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:13:11'),
(120, 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:13:14'),
(121, 3, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:13:25'),
(122, 1, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:13:55'),
(123, 2, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:14:13'),
(124, 2, 'Authorized and Finalized refund for claim #11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:14:25'),
(125, 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:14:29'),
(126, 10, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:14:37'),
(127, 10, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:16:18'),
(128, 3, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:16:27'),
(129, 3, 'Started repair for claim #7', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:16:31'),
(130, 3, 'Completed repair for claim #CLM-69D3A3E84E693-20260406', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:16:41'),
(131, 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:16:43'),
(132, 10, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:16:54'),
(133, 10, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:21:46'),
(134, 2, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:21:56'),
(135, 2, 'Updated inventory item #8 (New Serial: X-SOLD-7329)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:27:43'),
(136, 2, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:31:43'),
(137, 11, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:32:29'),
(138, 11, 'Auto-registered warranty #WRN-69D3C424DCE25-20260406 via serial check', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:33:08'),
(139, 11, 'Filed claim #CLM-69D3C43AC9C48-20260406', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:33:30'),
(140, 2, 'Assigned claim #13 to technician: John Technician', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:33:40'),
(141, 11, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:33:43'),
(142, 3, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:33:50'),
(143, 2, 'Updated inventory item #5 (New Serial: SN-000-111-222)', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:34:23'),
(144, 3, 'Decision made for claim #CLM-69D3C43AC9C48-20260406: PENDING_REFUND', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:34:27'),
(145, 2, 'Authorized and Finalized refund for claim #13', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:34:41'),
(146, 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:34:53'),
(147, 11, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:35:01'),
(148, 2, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:35:48'),
(149, 3, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:35:58'),
(150, 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:36:50'),
(151, 2, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:36:58'),
(152, 11, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:43:55'),
(153, 10, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:44:09'),
(154, 10, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:44:59'),
(155, 11, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:45:07'),
(156, 11, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:46:14'),
(157, 11, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:46:23'),
(158, 11, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:46:33'),
(159, 10, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:46:40'),
(160, 10, 'Filed claim #CLM-69D3C7CE6DEC5-20260406', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:48:46'),
(161, 2, 'Assigned claim #14 to technician: John Technician', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:48:59'),
(162, 10, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:49:03'),
(163, 3, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:49:20'),
(164, 3, 'Decision made for claim #CLM-69D3C7CE6DEC5-20260406: APPROVED', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:49:38'),
(165, 3, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:49:46'),
(166, 10, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:49:55'),
(167, 2, 'Authorized and Finalized repair for claim #14', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:50:21'),
(168, 10, 'User logged out', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:50:30'),
(169, 3, 'User logged in', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:50:40'),
(170, 3, 'Completed repair for claim #CLM-69D3C7CE6DEC5-20260406', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 14:50:53'),
(171, 2, 'Updated product #1: Quantum Laptop Pro', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 15:04:23'),
(172, 2, 'Updated product #6: hp envy ci7/8/512gb ssd', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 15:04:42'),
(173, 2, 'Updated product #7: Quantum Laptop', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36', '2026-04-06 15:07:54');

-- --------------------------------------------------------

--
-- Table structure for table `claims`
--

CREATE TABLE `claims` (
  `id` int(11) NOT NULL,
  `claim_number` varchar(50) NOT NULL,
  `warranty_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `assigned_technician_id` int(11) DEFAULT NULL,
  `issue_description` text NOT NULL,
  `issue_category` varchar(50) NOT NULL,
  `priority` enum('low','medium','high','urgent') NOT NULL DEFAULT 'medium',
  `status` enum('pending','under_review','approved','rejected','in_progress','completed','replaced','pending_refund','refund_denied') NOT NULL DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `claims`
--

INSERT INTO `claims` (`id`, `claim_number`, `warranty_id`, `user_id`, `assigned_technician_id`, `issue_description`, `issue_category`, `priority`, `status`, `created_at`, `updated_at`) VALUES
(1, 'CLM-69D37F5A682AC-20260406', 1, 10, 3, 'The screen is flickering occasionally.', 'hardware', 'medium', '', '2026-04-06 09:39:38', '2026-04-06 14:02:46'),
(2, 'CLM-69D38ED1808E7-20260406', 1, 10, 3, 'sdfsdfsdfds', 'software', 'high', 'completed', '2026-04-06 10:45:37', '2026-04-06 14:01:28'),
(7, 'CLM-69D3A3E84E693-20260406', 1, 10, 3, 'non responsive', 'performance', 'urgent', 'completed', '2026-04-06 12:15:36', '2026-04-06 14:16:41'),
(10, 'C-NEW-4629', 8, 10, 3, 'Screen flickering issue', 'Display', 'medium', 'completed', '2026-04-06 13:08:26', '2026-04-06 14:02:03'),
(11, 'CLM-69D3B9298BEEF-20260406', 9, 10, 3, 'dasfsdfsd', 'software', 'urgent', '', '2026-04-06 13:46:17', '2026-04-06 14:14:25'),
(12, 'C-NEW-5652', 10, 10, 3, 'Screen flickering issue', 'Display', 'medium', 'completed', '2026-04-06 13:52:33', '2026-04-06 14:01:55'),
(13, 'CLM-69D3C43AC9C48-20260406', 11, 11, 3, 'zxcgvsxvcxvxcvcx', 'physical', 'high', '', '2026-04-06 14:33:30', '2026-04-06 14:34:41'),
(14, 'CLM-69D3C7CE6DEC5-20260406', 7, 10, 3, 'cxzcxzx', 'accessories', 'medium', 'completed', '2026-04-06 14:48:46', '2026-04-06 14:50:53');

-- --------------------------------------------------------

--
-- Table structure for table `claim_resolution`
--

CREATE TABLE `claim_resolution` (
  `id` int(11) NOT NULL,
  `claim_id` int(11) NOT NULL,
  `repaired_by` int(11) NOT NULL,
  `resolution_type` enum('repair','replacement','refund','reject') NOT NULL,
  `refund_amount` decimal(10,2) DEFAULT 0.00,
  `is_authorized` tinyint(1) DEFAULT 0,
  `resolution_notes` text DEFAULT NULL,
  `resolved_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `claim_resolution`
--

INSERT INTO `claim_resolution` (`id`, `claim_id`, `repaired_by`, `resolution_type`, `refund_amount`, `is_authorized`, `resolution_notes`, `resolved_at`, `notes`) VALUES
(2, 10, 2, 'repair', 0.00, 1, 'Authorized repair with genuine parts.', '2026-04-06 13:08:26', NULL),
(3, 7, 2, 'refund', 0.00, 1, 'DASDASDAS', '2026-04-06 13:11:32', NULL),
(4, 12, 2, 'repair', 0.00, 1, 'Authorized repair with genuine parts.', '2026-04-06 13:52:33', NULL),
(5, 2, 3, 'repair', 0.00, 0, 'gdfgdf', '2026-04-06 14:01:28', NULL),
(6, 12, 3, 'repair', 0.00, 0, 'fdgd', '2026-04-06 14:01:55', NULL),
(7, 10, 3, 'repair', 0.00, 0, 'tyu', '2026-04-06 14:02:03', NULL),
(8, 1, 2, 'refund', 0.00, 1, 'asdfasdf', '2026-04-06 14:02:46', NULL),
(9, 11, 2, 'refund', 0.00, 1, 'sadsadasdsadasdasd', '2026-04-06 14:14:25', NULL),
(10, 7, 3, 'repair', 0.00, 0, 'xvxcvxcvxc', '2026-04-06 14:16:41', NULL),
(11, 13, 2, 'refund', 4000.00, 1, 'hgjhgjhgjhgghjghjgh', '2026-04-06 14:34:41', NULL),
(12, 14, 2, 'repair', 0.00, 1, 'xczczxczx', '2026-04-06 14:50:21', NULL),
(13, 14, 3, 'repair', 0.00, 0, 'xczxczxczxcz', '2026-04-06 14:50:53', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `claim_verification`
--

CREATE TABLE `claim_verification` (
  `id` int(11) NOT NULL,
  `claim_id` int(11) NOT NULL,
  `technician_id` int(11) NOT NULL,
  `findings` text NOT NULL,
  `is_valid` tinyint(1) NOT NULL DEFAULT 0,
  `recommended_action` enum('repair','replace','refund','reject') NOT NULL,
  `verification_notes` text DEFAULT NULL,
  `verified_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `verification_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `claim_verification`
--

INSERT INTO `claim_verification` (`id`, `claim_id`, `technician_id`, `findings`, `is_valid`, `recommended_action`, `verification_notes`, `verified_at`, `verification_date`) VALUES
(1, 1, 3, 'ghjghjgh', 1, 'refund', 'ghjghjghj', '2026-04-06 09:46:57', '2026-04-06 09:46:57'),
(2, 2, 3, 'dfgdfg', 1, 'repair', 'dfgdfgdf', '2026-04-06 10:46:47', '2026-04-06 10:46:47'),
(4, 7, 3, 'gdfgdfgdfgdf', 1, 'repair', 'gdfgdfgd', '2026-04-06 12:27:49', '2026-04-06 12:27:49'),
(5, 10, 3, 'Hardware defect found in panel.', 1, 'repair', NULL, '2026-04-06 13:08:26', '2026-04-06 13:08:26'),
(6, 11, 3, 'dfgdfgdfgd', 1, 'refund', 'dfgdfgdfgdfgdf', '2026-04-06 13:48:06', '2026-04-06 13:48:06'),
(7, 12, 3, 'Hardware defect found in panel.', 1, 'repair', NULL, '2026-04-06 13:52:33', '2026-04-06 13:52:33'),
(8, 13, 3, 'ghfhfghfg', 1, 'refund', 'ghfghfghfghfghfghfghfg', '2026-04-06 14:34:27', '2026-04-06 14:34:27'),
(9, 14, 3, 'mbnmbnmnbm', 1, 'repair', 'bnmbnmvbmbb', '2026-04-06 14:49:38', '2026-04-06 14:49:38');

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(100) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(20) DEFAULT 'info',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `title`, `message`, `type`, `is_read`, `created_at`) VALUES
(1, 10, 'Warranty Registered', 'Your warranty #WRN-69D37D21D3DDB-20260406 has been registered successfully.', 'success', 0, '2026-04-06 09:30:09'),
(2, 1, 'New Claim Filed', 'New claim #CLM-69D37F5A682AC-20260406 has been filed and requires review.', 'info', 0, '2026-04-06 09:39:38'),
(3, 3, 'New Claim Filed', 'New claim #CLM-69D37F5A682AC-20260406 has been filed and requires review.', 'info', 0, '2026-04-06 09:39:38'),
(4, 10, 'Claim Filed', 'Your claim #CLM-69D37F5A682AC-20260406 has been filed successfully. We\'ll review it shortly.', 'success', 0, '2026-04-06 09:39:38'),
(5, 10, 'Claim Update', 'Your claim #CLM-69D37F5A682AC-20260406 has been reviewed. Status: IN_PROGRESS', 'success', 0, '2026-04-06 09:54:26'),
(6, 10, 'Claim Decision', 'Your claim #CLM-69D37F5A682AC-20260406 has been Rejected. Reason: Issue not covered', 'danger', 0, '2026-04-06 10:44:30'),
(7, 1, 'New Claim Filed', 'New claim #CLM-69D38ED1808E7-20260406 filed for Quantum Laptop Pro', 'info', 0, '2026-04-06 10:45:37'),
(8, 3, 'New Claim Filed', 'New claim #CLM-69D38ED1808E7-20260406 filed for Quantum Laptop Pro', 'info', 0, '2026-04-06 10:45:37'),
(9, 10, 'Claim Submitted', 'Claim #CLM-69D38ED1808E7-20260406 submitted successfully.', 'success', 0, '2026-04-06 10:45:37'),
(10, 10, 'Claim Decision', 'Your claim #CLM-69D38ED1808E7-20260406 has been Approved. Reason: Valid warranty coverage', 'success', 0, '2026-04-06 10:46:47'),
(11, 1, 'New Claim Filed', 'New claim #CLM-69D3A3E84E693-20260406 filed for Quantum Laptop Pro', 'info', 0, '2026-04-06 12:15:36'),
(12, 3, 'New Claim Filed', 'New claim #CLM-69D3A3E84E693-20260406 filed for Quantum Laptop Pro', 'info', 0, '2026-04-06 12:15:36'),
(13, 10, 'Claim Submitted', 'Claim #CLM-69D3A3E84E693-20260406 submitted successfully.', 'success', 0, '2026-04-06 12:15:36'),
(14, 10, 'Claim Decision', 'Your claim #CLM-69D3A3E84E693-20260406 has been Approved. Reason: Valid warranty coverage', 'success', 0, '2026-04-06 12:27:49'),
(15, 3, 'New Claim Assigned', 'You have been assigned to claim #1. Please evaluate it as soon as possible.', 'info', 0, '2026-04-06 13:37:04'),
(16, 1, 'New Claim Filed', 'New claim #CLM-69D3B9298BEEF-20260406 filed for Quantum Laptop', 'info', 0, '2026-04-06 13:46:17'),
(17, 3, 'New Claim Filed', 'New claim #CLM-69D3B9298BEEF-20260406 filed for Quantum Laptop', 'info', 0, '2026-04-06 13:46:17'),
(18, 10, 'Claim Submitted', 'Claim #CLM-69D3B9298BEEF-20260406 submitted successfully.', 'success', 0, '2026-04-06 13:46:17'),
(19, 3, 'New Claim Assigned', 'You have been assigned to claim #2. Please evaluate it as soon as possible.', 'info', 0, '2026-04-06 13:46:59'),
(20, 10, 'Claim Decision', 'Your claim #CLM-69D3B9298BEEF-20260406 has been Approved. Reason: Valid warranty coverage', 'success', 0, '2026-04-06 13:48:06'),
(21, 10, 'Claim Decision', 'Your claim #CLM-69D38ED1808E7-20260406 has been Approved. Reason: Valid warranty coverage', 'success', 0, '2026-04-06 14:01:04'),
(22, 10, 'Repair Completed', 'Your device for claim #CLM-69D38ED1808E7-20260406 has been repaired and is ready.', 'success', 0, '2026-04-06 14:01:28'),
(23, 10, 'Repair Completed', 'Your device for claim #C-NEW-5652 has been repaired and is ready.', 'success', 0, '2026-04-06 14:01:55'),
(24, 10, 'Repair Completed', 'Your device for claim #C-NEW-4629 has been repaired and is ready.', 'success', 0, '2026-04-06 14:02:03'),
(25, 10, 'Claim Decision', 'Your claim #CLM-69D37F5A682AC-20260406 has been Approved. Reason: Valid warranty coverage', 'success', 0, '2026-04-06 14:02:19'),
(26, 3, 'New Claim Assigned', 'You have been assigned to claim #11. Please evaluate it as soon as possible.', 'info', 0, '2026-04-06 14:11:31'),
(27, 3, 'New Claim Assigned', 'You have been assigned to claim #7. Please evaluate it as soon as possible.', 'info', 0, '2026-04-06 14:11:37'),
(28, 10, 'Claim Decision', 'Your claim #CLM-69D3A3E84E693-20260406 has been Approved. Reason: Valid warranty coverage', 'success', 0, '2026-04-06 14:12:46'),
(29, 10, 'Claim Decision', 'Your claim #CLM-69D3B9298BEEF-20260406 has been Approved. Reason: Valid warranty coverage', 'success', 0, '2026-04-06 14:13:11'),
(30, 10, 'Repair Completed', 'Your device for claim #CLM-69D3A3E84E693-20260406 has been repaired and is ready.', 'success', 0, '2026-04-06 14:16:41'),
(31, 1, 'New Claim Filed', 'New claim #CLM-69D3C43AC9C48-20260406 filed for Quantum Laptop', 'info', 0, '2026-04-06 14:33:30'),
(32, 3, 'New Claim Filed', 'New claim #CLM-69D3C43AC9C48-20260406 filed for Quantum Laptop', 'info', 0, '2026-04-06 14:33:30'),
(33, 11, 'Claim Submitted', 'Claim #CLM-69D3C43AC9C48-20260406 submitted successfully.', 'success', 0, '2026-04-06 14:33:30'),
(34, 3, 'New Claim Assigned', 'You have been assigned to claim #13. Please evaluate it as soon as possible.', 'info', 0, '2026-04-06 14:33:40'),
(35, 11, 'Claim Decision', 'Your claim #CLM-69D3C43AC9C48-20260406 has been Approved. Reason: Valid warranty coverage', 'success', 0, '2026-04-06 14:34:27'),
(36, 1, 'New Claim Filed', 'New claim #CLM-69D3C7CE6DEC5-20260406 filed for Quantum Laptop Pro', 'info', 0, '2026-04-06 14:48:46'),
(37, 3, 'New Claim Filed', 'New claim #CLM-69D3C7CE6DEC5-20260406 filed for Quantum Laptop Pro', 'info', 0, '2026-04-06 14:48:46'),
(38, 10, 'Claim Submitted', 'Claim #CLM-69D3C7CE6DEC5-20260406 submitted successfully.', 'success', 0, '2026-04-06 14:48:46'),
(39, 3, 'New Claim Assigned', 'You have been assigned to claim #14. Please evaluate it as soon as possible.', 'info', 0, '2026-04-06 14:48:59'),
(40, 10, 'Claim Decision', 'Your claim #CLM-69D3C7CE6DEC5-20260406 has been Approved. Reason: Valid warranty coverage', 'success', 0, '2026-04-06 14:49:38'),
(41, 10, 'Repair Completed', 'Your device for claim #CLM-69D3C7CE6DEC5-20260406 has been repaired and is ready.', 'success', 0, '2026-04-06 14:50:53');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `id` int(11) NOT NULL,
  `product_name` varchar(100) NOT NULL,
  `model_number` varchar(50) NOT NULL,
  `brand` varchar(50) NOT NULL,
  `price` decimal(10,2) DEFAULT 0.00,
  `warranty_period_months` int(11) NOT NULL,
  `warranty_terms` text DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`id`, `product_name`, `model_number`, `brand`, `price`, `warranty_period_months`, `warranty_terms`, `created_at`) VALUES
(1, 'Quantum Laptop Pro', 'AGT-2026', 'Antigravity Tech', 35000.00, 24, 'Standard tech warranty.', '2026-04-06 09:19:11'),
(6, 'hp envy ci7/8/512gb ssd', 'envy 200', 'hp', 65000.00, 12, 'Standard manufacturer warranty applies.', '2026-04-06 12:08:21'),
(7, 'Quantum Laptop', 'RIDEOP', 'hp', 100000.00, 12, 'Standard manufacturer warranty applies to parts and labor.', '2026-04-06 13:10:48'),
(8, '55-inch Q70A QLED 4K Smart TV', 'QE55Q70A', 'Samsung', 125000.00, 24, 'Covers all manufacturing defects including panel issues. Does not cover physical damage or liquid ingress.', '2026-04-06 14:57:42'),
(9, 'Grill Microwave Oven 23L', 'NN-GT35HB', 'Panasonic', 18500.00, 12, 'Standard manufacturer warranty for electrical components and magnetron. Excludes glass tray and bulb.', '2026-04-06 14:57:42'),
(10, 'PlayStation 5 Console (Disc Edition)', 'CFI-1215A', 'Sony', 82000.00, 12, 'Limited hardware warranty for the console and one controller. Joy-stick drift covered for the first 6 months.', '2026-04-06 14:57:42'),
(11, '9kg Vivace Front Load Washer', 'FV1409S4W', 'LG', 95000.00, 24, '24 months general warranty + 10 years warranty on the Inverter Direct Drive Motor.', '2026-04-06 14:57:42'),
(12, 'MacBook Pro 14 (M3 Chip, 512GB)', 'MRX33LL/A', 'Apple', 315000.00, 12, 'Apple Limited Warranty covers hardware failures. Screen and battery issues subject to diagnostic by authorized center.', '2026-04-06 14:57:42'),
(13, 'XPS 13 Laptop (Intel Core i7, 16GB RAM)', 'XPS9315', 'Dell', 185000.00, 12, 'Standard onsite hardware support after remote diagnosis. Does not cover software or battery wear over 12 months.', '2026-04-06 14:57:42'),
(14, 'WH-1000XM5 Noise Canceling Headphones', 'WH1000XM5/B', 'Sony', 45000.00, 12, 'Covers driver failure and Bluetooth connectivity issues. Excludes ear pad wear and tear.', '2026-04-06 14:57:42'),
(15, 'HP Swift Laptop', 'HP-A331', 'HP', 91000.00, 12, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(16, 'ASUS Inspiron Laptop', 'ASUS-18FB', 'ASUS', 213000.00, 12, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(17, 'Dell EliteBook Laptop', 'Dell-45DE', 'Dell', 132000.00, 12, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(18, 'Dell EliteBook Laptop', 'Dell-FE29', 'Dell', 98000.00, 12, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(19, 'Acer ThinkPad Laptop', 'Acer-6DB3', 'Acer', 109000.00, 12, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(20, 'Dell Inspiron Laptop', 'Dell-7A8D', 'Dell', 126000.00, 12, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(21, 'Dell Inspiron Laptop', 'Dell-4310', 'Dell', 177000.00, 12, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(22, 'Apple ZenBook Laptop', 'Apple-43A7', 'Apple', 139000.00, 12, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(23, 'Apple EliteBook Laptop', 'Apple-FC29', 'Apple', 191000.00, 12, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(24, 'Apple ZenBook Laptop', 'Apple-EF16', 'Apple', 172000.00, 12, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(25, 'Acer Inspiron Laptop', 'Acer-719F', 'Acer', 108000.00, 12, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(26, 'ASUS Swift Laptop', 'ASUS-C07A', 'ASUS', 215000.00, 12, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(27, 'ASUS Swift Laptop', 'ASUS-7099', 'ASUS', 178000.00, 12, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(28, 'Apple ThinkPad Laptop', 'Apple-A60A', 'Apple', 175000.00, 12, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(29, 'Acer EliteBook Laptop', 'Acer-CE9E', 'Acer', 127000.00, 12, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(30, 'Lenovo Inspiron Laptop', 'Lenovo-FFA5', 'Lenovo', 133000.00, 12, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(31, 'Acer Inspiron Laptop', 'Acer-D98C', 'Acer', 84000.00, 12, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(32, 'Dell EliteBook Laptop', 'Dell-D7AB', 'Dell', 129000.00, 12, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(33, 'Apple EliteBook Laptop', 'Apple-A400', 'Apple', 115000.00, 12, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(34, 'ASUS Inspiron Laptop', 'ASUS-A91C', 'ASUS', 204000.00, 12, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(35, 'ASUS EliteBook Laptop', 'ASUS-D97D', 'ASUS', 65000.00, 12, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(36, 'HP Inspiron Laptop', 'HP-863A', 'HP', 135000.00, 12, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(37, 'Acer Swift Laptop', 'Acer-2F81', 'Acer', 97000.00, 12, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(38, 'Acer EliteBook Laptop', 'Acer-833B', 'Acer', 98000.00, 12, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(39, 'Lenovo ThinkPad Laptop', 'Lenovo-8CE9', 'Lenovo', 137000.00, 12, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(40, 'ASUS EliteBook Laptop', 'ASUS-C6DA', 'ASUS', 149000.00, 12, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(41, 'ASUS Inspiron Laptop', 'ASUS-A31B', 'ASUS', 78000.00, 12, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(42, 'ASUS Swift Laptop', 'ASUS-731D', 'ASUS', 67000.00, 12, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(43, 'Acer ZenBook Laptop', 'Acer-C9C0', 'Acer', 90000.00, 12, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(44, 'Dell ZenBook Laptop', 'Dell-E2B8', 'Dell', 132000.00, 12, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(45, 'Custom Gaming Pro Desktop PC', 'Custom-084A', 'Custom', 68000.00, 24, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(46, 'Dell Pavilion Desktop PC', 'Dell-0D82', 'Dell', 129000.00, 24, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(47, 'Lenovo IdeaCentre Desktop PC', 'Lenovo-3E10', 'Lenovo', 59000.00, 24, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(48, 'Custom OptiPlex Desktop PC', 'Custom-7FFA', 'Custom', 114000.00, 24, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(49, 'HP Pavilion Desktop PC', 'HP-36ED', 'HP', 99000.00, 24, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(50, 'Dell OptiPlex Desktop PC', 'Dell-27CE', 'Dell', 55000.00, 24, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(51, 'Custom Gaming Pro Desktop PC', 'Custom-E7F1', 'Custom', 60000.00, 24, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(52, 'HP Pavilion Desktop PC', 'HP-1061', 'HP', 180000.00, 24, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(53, 'Dell IdeaCentre Desktop PC', 'Dell-189C', 'Dell', 202000.00, 24, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(54, 'HP Gaming Pro Desktop PC', 'HP-F802', 'HP', 123000.00, 24, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(55, 'Custom Gaming Pro Desktop PC', 'Custom-9C63', 'Custom', 124000.00, 24, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(56, 'HP Pavilion Desktop PC', 'HP-5EC3', 'HP', 137000.00, 24, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(57, 'Custom IdeaCentre Desktop PC', 'Custom-F7E1', 'Custom', 166000.00, 24, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(58, 'Dell IdeaCentre Desktop PC', 'Dell-C0FE', 'Dell', 69000.00, 24, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(59, 'Custom Gaming Pro Desktop PC', 'Custom-BA9D', 'Custom', 152000.00, 24, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(60, 'Dell Pavilion Desktop PC', 'Dell-10E4', 'Dell', 144000.00, 24, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(61, 'HP IdeaCentre Desktop PC', 'HP-3E72', 'HP', 203000.00, 24, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(62, 'Lenovo IdeaCentre Desktop PC', 'Lenovo-0064', 'Lenovo', 191000.00, 24, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(63, 'Dell OptiPlex Desktop PC', 'Dell-F98E', 'Dell', 102000.00, 24, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(64, 'Custom IdeaCentre Desktop PC', 'Custom-A33E', 'Custom', 160000.00, 24, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(65, 'Custom Gaming Pro Desktop PC', 'Custom-5FB1', 'Custom', 123000.00, 24, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(66, 'Dell Pavilion Desktop PC', 'Dell-F378', 'Dell', 107000.00, 24, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(67, 'Custom IdeaCentre Desktop PC', 'Custom-0B62', 'Custom', 71000.00, 24, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(68, 'Custom IdeaCentre Desktop PC', 'Custom-7E96', 'Custom', 125000.00, 24, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(69, 'Dell OptiPlex Desktop PC', 'Dell-2218', 'Dell', 140000.00, 24, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(70, 'HP IdeaCentre Desktop PC', 'HP-412E', 'HP', 82000.00, 24, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(71, 'Lenovo OptiPlex Desktop PC', 'Lenovo-7803', 'Lenovo', 169000.00, 24, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(72, 'Dell Pavilion Desktop PC', 'Dell-A2CF', 'Dell', 188000.00, 24, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(73, 'Dell OptiPlex Desktop PC', 'Dell-5085', 'Dell', 136000.00, 24, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(74, 'Lenovo OptiPlex Desktop PC', 'Lenovo-F8A9', 'Lenovo', 153000.00, 24, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(75, 'Hisense OLED C3 33-inch Television', 'Hisense-6E8F', 'Hisense', 195000.00, 24, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(76, 'LG Smart Ultra 34-inch Television', 'LG-5DBF', 'LG', 194000.00, 24, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(77, 'Samsung QLED 4K 35-inch Television', 'Samsung-4362', 'Samsung', 102000.00, 24, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(78, 'Hisense U7 Series 36-inch Television', 'Hisense-F001', 'Hisense', 76000.00, 24, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(79, 'Hisense Smart Ultra 37-inch Television', 'Hisense-9E0E', 'Hisense', 159000.00, 24, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(80, 'Sony Bravia XR 38-inch Television', 'Sony-8A92', 'Sony', 101000.00, 24, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(81, 'LG OLED C3 39-inch Television', 'LG-08FE', 'LG', 131000.00, 24, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(82, 'Hisense QLED 4K 40-inch Television', 'Hisense-E844', 'Hisense', 105000.00, 24, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(83, 'TCL Smart Ultra 41-inch Television', 'TCL-501A', 'TCL', 149000.00, 24, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(84, 'Hisense U7 Series 42-inch Television', 'Hisense-E914', 'Hisense', 97000.00, 24, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(85, 'Samsung OLED C3 43-inch Television', 'Samsung-DBF3', 'Samsung', 115000.00, 24, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(86, 'LG Smart Ultra 44-inch Television', 'LG-F5DA', 'LG', 129000.00, 24, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(87, 'Hisense Bravia XR 45-inch Television', 'Hisense-B368', 'Hisense', 163000.00, 24, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(88, 'Hisense QLED 4K 46-inch Television', 'Hisense-171A', 'Hisense', 182000.00, 24, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(89, 'Samsung Bravia XR 47-inch Television', 'Samsung-34AD', 'Samsung', 78000.00, 24, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(90, 'Hisense QLED 4K 48-inch Television', 'Hisense-A89E', 'Hisense', 195000.00, 24, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(91, 'LG Bravia XR 49-inch Television', 'LG-EAC1', 'LG', 96000.00, 24, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(92, 'Sony U7 Series 50-inch Television', 'Sony-D835', 'Sony', 147000.00, 24, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(93, 'Hisense OLED C3 51-inch Television', 'Hisense-5EF7', 'Hisense', 125000.00, 24, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(94, 'LG Bravia XR 52-inch Television', 'LG-FB4D', 'LG', 160000.00, 24, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(95, 'LG Bravia XR 53-inch Television', 'LG-B52D', 'LG', 103000.00, 24, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(96, 'TCL Bravia XR 54-inch Television', 'TCL-DA9B', 'TCL', 138000.00, 24, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(97, 'TCL U7 Series 55-inch Television', 'TCL-9523', 'TCL', 63000.00, 24, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(98, 'Hisense Bravia XR 56-inch Television', 'Hisense-FA72', 'Hisense', 60000.00, 24, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(99, 'LG U7 Series 57-inch Television', 'LG-042E', 'LG', 87000.00, 24, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(100, 'Sony QLED 4K 58-inch Television', 'Sony-8A13', 'Sony', 189000.00, 24, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(101, 'Samsung QLED 4K 59-inch Television', 'Samsung-557A', 'Samsung', 68000.00, 24, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(102, 'Hisense OLED C3 60-inch Television', 'Hisense-D176', 'Hisense', 64000.00, 24, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(103, 'LG U7 Series 61-inch Television', 'LG-08B5', 'LG', 193000.00, 24, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(104, 'TCL U7 Series 62-inch Television', 'TCL-2DBE', 'TCL', 148000.00, 24, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(105, 'Sony 990 Pro SSD Accessory', 'Sony-196B', 'Sony', 51000.00, 12, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(106, 'Samsung WH-1000XM5 Accessory', 'Samsung-955D', 'Samsung', 21000.00, 12, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(107, 'Samsung Black Desktop HDD Accessory', 'Samsung-F287', 'Samsung', 111000.00, 12, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(108, 'Sony Archer AX73 Accessory', 'Sony-5F25', 'Sony', 102000.00, 12, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(109, 'Razer Archer AX73 Accessory', 'Razer-3EAE', 'Razer', 142000.00, 12, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(110, 'TP-Link BlackWidow V4 Accessory', 'TP-Link-DCA5', 'TP-Link', 141000.00, 12, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(111, 'WD BlackWidow V4 Accessory', 'WD-1E7A', 'WD', 118000.00, 12, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(112, 'Sony BlackWidow V4 Accessory', 'Sony-79C1', 'Sony', 6000.00, 12, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(113, 'Samsung 990 Pro SSD Accessory', 'Samsung-A537', 'Samsung', 7000.00, 12, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(114, 'WD WH-1000XM5 Accessory', 'WD-2C61', 'WD', 103000.00, 12, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(115, 'TP-Link BlackWidow V4 Accessory', 'TP-Link-7765', 'TP-Link', 16000.00, 12, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(116, 'Logitech BlackWidow V4 Accessory', 'Logitech-5855', 'Logitech', 47000.00, 12, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(117, 'TP-Link BlackWidow V4 Accessory', 'TP-Link-E84E', 'TP-Link', 11000.00, 12, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(118, 'Razer 990 Pro SSD Accessory', 'Razer-F567', 'Razer', 80000.00, 12, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(119, 'Logitech 990 Pro SSD Accessory', 'Logitech-DB85', 'Logitech', 69000.00, 12, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(120, 'Sony WH-1000XM5 Accessory', 'Sony-29C0', 'Sony', 65000.00, 12, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(121, 'Razer Archer AX73 Accessory', 'Razer-05E6', 'Razer', 113000.00, 12, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(122, 'Sony WH-1000XM5 Accessory', 'Sony-5EB6', 'Sony', 139000.00, 12, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(123, 'WD BlackWidow V4 Accessory', 'WD-652C', 'WD', 32000.00, 12, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(124, 'Razer MX Master 3 Accessory', 'Razer-88E2', 'Razer', 111000.00, 12, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31'),
(125, 'WD WH-1000XM5 Accessory', 'WD-E2B2', 'WD', 133000.00, 12, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(126, 'Logitech BlackWidow V4 Accessory', 'Logitech-7B4A', 'Logitech', 132000.00, 12, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(127, 'Samsung Archer AX73 Accessory', 'Samsung-EC0A', 'Samsung', 18000.00, 12, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(128, 'Logitech Black Desktop HDD Accessory', 'Logitech-F2FE', 'Logitech', 88000.00, 12, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(129, 'Logitech Black Desktop HDD Accessory', 'Logitech-660B', 'Logitech', 30000.00, 12, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(130, 'Logitech WH-1000XM5 Accessory', 'Logitech-7590', 'Logitech', 54000.00, 12, 'Comprehensive technical support including hardware replacement for verified defects.', '2026-04-06 15:04:31'),
(131, 'Samsung Archer AX73 Accessory', 'Samsung-7089', 'Samsung', 98000.00, 12, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(132, 'Razer BlackWidow V4 Accessory', 'Razer-3B8B', 'Razer', 40000.00, 12, 'Limited international warranty. Battery and wear updates covered for first 6 months.', '2026-04-06 15:04:31'),
(133, 'Razer Archer AX73 Accessory', 'Razer-8672', 'Razer', 22000.00, 12, 'Full hardware coverage excluding physical damage or moisture.', '2026-04-06 15:04:31'),
(134, 'Razer Archer AX73 Accessory', 'Razer-ADFC', 'Razer', 106000.00, 12, 'Standard manufacturer warranty covering electronic failures and defects.', '2026-04-06 15:04:31');

-- --------------------------------------------------------

--
-- Table structure for table `sold_items`
--

CREATE TABLE `sold_items` (
  `id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `serial_number` varchar(100) NOT NULL,
  `purchase_price` decimal(10,2) DEFAULT 0.00,
  `sale_date` date NOT NULL,
  `warranty_expiry_date` date NOT NULL,
  `is_registered` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `sold_items`
--

INSERT INTO `sold_items` (`id`, `product_id`, `serial_number`, `purchase_price`, `sale_date`, `warranty_expiry_date`, `is_registered`, `created_at`) VALUES
(1, 1, 'X-SOLD-2595', 0.00, '2026-01-01', '2027-01-01', 1, '2026-04-06 13:04:30'),
(2, 1, 'X-SOLD-3871', 0.00, '2026-01-01', '2027-01-01', 1, '2026-04-06 13:06:04'),
(3, 1, 'X-SOLD-5596', 0.00, '2026-01-01', '2027-01-01', 1, '2026-04-06 13:08:26'),
(4, 7, 'sn-001-003-009', 0.00, '2026-04-06', '2027-04-06', 1, '2026-04-06 13:11:01'),
(5, 7, 'SN-000-111-222', 4000.00, '2026-04-06', '2027-04-06', 1, '2026-04-06 13:22:13'),
(8, 1, 'X-SOLD-7329', 10000.00, '2026-01-01', '2028-01-01', 1, '2026-04-06 13:52:33');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(100) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `user_type` enum('client','technician','admin','owner') NOT NULL DEFAULT 'client',
  `status` enum('active','inactive') NOT NULL DEFAULT 'active',
  `last_login` datetime DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `email`, `full_name`, `phone`, `address`, `user_type`, `status`, `last_login`, `created_at`) VALUES
(1, 'admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@warranty.com', 'System Administrator', NULL, NULL, 'admin', 'active', '2026-04-06 16:39:16', '2026-04-06 08:45:12'),
(2, 'owner', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'owner@warranty.com', 'Business Owner', NULL, NULL, 'owner', 'active', '2026-04-06 17:36:58', '2026-04-06 08:45:12'),
(3, 'technician1', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'tech1@warranty.com', 'John Technician', NULL, NULL, 'technician', 'active', '2026-04-06 17:50:40', '2026-04-06 08:45:12'),
(10, 'testclient', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'test@client.com', 'Test User', '1234567890', '123 Test St', 'client', 'active', '2026-04-06 17:49:55', '2026-04-06 09:22:05'),
(11, 'clienttest', '$2y$10$xXLxsCrC2l.zy2z0rIYcgumvrMhAGYXhzUIKDvjY1.v2Vmv6E863G', 'briangikandi@rodikenya.org', 'brian gikandi', '12345678', 'gjh', 'client', 'active', '2026-04-06 17:46:23', '2026-04-06 14:32:19');

-- --------------------------------------------------------

--
-- Table structure for table `warranty_registrations`
--

CREATE TABLE `warranty_registrations` (
  `id` int(11) NOT NULL,
  `warranty_number` varchar(50) NOT NULL,
  `user_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `serial_number` varchar(100) NOT NULL,
  `purchase_date` date NOT NULL,
  `purchase_price` decimal(10,2) DEFAULT 0.00,
  `quantity` int(11) DEFAULT 1,
  `warranty_start_date` date NOT NULL,
  `warranty_end_date` date NOT NULL,
  `purchase_receipt` varchar(255) DEFAULT NULL,
  `status` enum('active','expired','voided') NOT NULL DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `warranty_registrations`
--

INSERT INTO `warranty_registrations` (`id`, `warranty_number`, `user_id`, `product_id`, `serial_number`, `purchase_date`, `purchase_price`, `quantity`, `warranty_start_date`, `warranty_end_date`, `purchase_receipt`, `status`, `created_at`) VALUES
(1, 'WRN-69D37D21D3DDB-20260406', 10, 1, 'SN-000-111-222', '2026-04-06', 4000.00, 1, '2026-04-06', '2026-04-06', '', 'voided', '2026-04-06 09:30:09'),
(6, 'W-6240', 2, 1, 'X-SOLD-2595', '2026-01-01', 0.00, 1, '2026-01-01', '2027-01-01', NULL, 'active', '2026-04-06 13:04:30'),
(7, 'W-3753', 10, 1, 'X-SOLD-3871', '2026-01-01', 0.00, 1, '2026-01-01', '2027-01-01', NULL, 'active', '2026-04-06 13:06:04'),
(8, 'W-5539', 10, 1, 'X-SOLD-5596', '2026-01-01', 0.00, 1, '2026-01-01', '2027-01-01', NULL, 'active', '2026-04-06 13:08:26'),
(9, 'WRN-69D3B3B479BD8-20260406', 10, 7, 'sn-001-003-009', '2026-04-06', 0.00, 1, '2026-04-06', '2026-04-06', NULL, 'voided', '2026-04-06 13:23:00'),
(10, 'W-6259', 10, 1, 'X-SOLD-7329', '2026-01-01', 10000.00, 1, '2026-01-01', '2027-01-01', NULL, 'active', '2026-04-06 13:52:33'),
(11, 'WRN-69D3C424DCE25-20260406', 11, 7, 'SN-000-111-222', '2026-04-06', 4000.00, 1, '2026-04-06', '2026-04-06', NULL, 'voided', '2026-04-06 14:33:08');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `claims`
--
ALTER TABLE `claims`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `claim_number` (`claim_number`),
  ADD KEY `warranty_id` (`warranty_id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `fk_assigned_tech` (`assigned_technician_id`);

--
-- Indexes for table `claim_resolution`
--
ALTER TABLE `claim_resolution`
  ADD PRIMARY KEY (`id`),
  ADD KEY `claim_id` (`claim_id`),
  ADD KEY `repaired_by` (`repaired_by`);

--
-- Indexes for table `claim_verification`
--
ALTER TABLE `claim_verification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `claim_id` (`claim_id`),
  ADD KEY `technician_id` (`technician_id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `sold_items`
--
ALTER TABLE `sold_items`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `serial_number` (`serial_number`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `warranty_registrations`
--
ALTER TABLE `warranty_registrations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `warranty_number` (`warranty_number`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `product_id` (`product_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=174;

--
-- AUTO_INCREMENT for table `claims`
--
ALTER TABLE `claims`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `claim_resolution`
--
ALTER TABLE `claim_resolution`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `claim_verification`
--
ALTER TABLE `claim_verification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=42;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=135;

--
-- AUTO_INCREMENT for table `sold_items`
--
ALTER TABLE `sold_items`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `warranty_registrations`
--
ALTER TABLE `warranty_registrations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `claims`
--
ALTER TABLE `claims`
  ADD CONSTRAINT `claims_ibfk_1` FOREIGN KEY (`warranty_id`) REFERENCES `warranty_registrations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `claims_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_assigned_tech` FOREIGN KEY (`assigned_technician_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `claim_resolution`
--
ALTER TABLE `claim_resolution`
  ADD CONSTRAINT `claim_resolution_ibfk_1` FOREIGN KEY (`claim_id`) REFERENCES `claims` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `claim_resolution_ibfk_2` FOREIGN KEY (`repaired_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `claim_verification`
--
ALTER TABLE `claim_verification`
  ADD CONSTRAINT `claim_verification_ibfk_1` FOREIGN KEY (`claim_id`) REFERENCES `claims` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `claim_verification_ibfk_2` FOREIGN KEY (`technician_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `sold_items`
--
ALTER TABLE `sold_items`
  ADD CONSTRAINT `sold_items_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `warranty_registrations`
--
ALTER TABLE `warranty_registrations`
  ADD CONSTRAINT `warranty_registrations_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `warranty_registrations_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
