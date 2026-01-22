-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 27, 2025 at 01:40 PM
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
-- Database: `exam_portal`
--

-- --------------------------------------------------------

--
-- Table structure for table `activity_logs`
--

CREATE TABLE `activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `model_type` varchar(255) DEFAULT NULL,
  `model_id` bigint(20) UNSIGNED DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `description` text DEFAULT NULL,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `activity_logs`
--

INSERT INTO `activity_logs` (`id`, `user_id`, `action`, `model_type`, `model_id`, `old_values`, `new_values`, `description`, `ip_address`, `user_agent`, `created_at`, `updated_at`) VALUES
(1, 2, 'login', NULL, NULL, NULL, NULL, 'User logged in with face + eye blink verification', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 02:34:45', '2025-12-27 02:34:45'),
(2, 2, 'face_updated', NULL, NULL, NULL, NULL, 'User updated their reference face image', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 02:35:04', '2025-12-27 02:35:04'),
(3, 2, 'logout', NULL, NULL, NULL, NULL, 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 02:35:50', '2025-12-27 02:35:50'),
(4, 3, 'register', 'App\\Models\\User', 3, NULL, NULL, 'New user registered with face + eye blink verification', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 02:36:39', '2025-12-27 02:36:39'),
(5, 3, 'face_updated', NULL, NULL, NULL, NULL, 'User updated their reference face image', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 02:37:05', '2025-12-27 02:37:05'),
(6, 3, 'logout', NULL, NULL, NULL, NULL, 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 02:37:12', '2025-12-27 02:37:12'),
(7, 3, 'login', NULL, NULL, NULL, NULL, 'User logged in with face + eye blink verification', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 02:37:44', '2025-12-27 02:37:44'),
(8, 1, 'login', NULL, NULL, NULL, NULL, 'User logged in with face + eye blink verification', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 02:39:17', '2025-12-27 02:39:17'),
(9, 2, 'login', NULL, NULL, NULL, NULL, 'User logged in with face + eye blink verification', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 02:40:26', '2025-12-27 02:40:26'),
(10, 2, 'face_updated', NULL, NULL, NULL, NULL, 'User updated their reference face image', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 02:40:49', '2025-12-27 02:40:49'),
(11, 2, 'face_updated', NULL, NULL, NULL, NULL, 'User updated their reference face image', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 02:41:00', '2025-12-27 02:41:00'),
(12, 2, 'logout', NULL, NULL, NULL, NULL, 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 02:49:06', '2025-12-27 02:49:06'),
(13, 2, 'login', NULL, NULL, NULL, NULL, 'User logged in with face + eye blink verification', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 02:49:21', '2025-12-27 02:49:21'),
(14, 2, 'logout', NULL, NULL, NULL, NULL, 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 03:58:11', '2025-12-27 03:58:11'),
(15, 2, 'login', NULL, NULL, NULL, NULL, 'User logged in with face + eye blink verification', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 03:58:28', '2025-12-27 03:58:28'),
(16, 2, 'logout', NULL, NULL, NULL, NULL, 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 04:05:52', '2025-12-27 04:05:52'),
(17, 2, 'login', NULL, NULL, NULL, NULL, 'User logged in with face match (Score: 68%)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 04:23:10', '2025-12-27 04:23:10'),
(18, 2, 'logout', NULL, NULL, NULL, NULL, 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 04:23:15', '2025-12-27 04:23:15'),
(19, 2, 'login', NULL, NULL, NULL, NULL, 'User logged in with face match (Score: 74%)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 04:26:53', '2025-12-27 04:26:53'),
(20, 2, 'logout', NULL, NULL, NULL, NULL, 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 04:26:56', '2025-12-27 04:26:56'),
(21, 2, 'login', NULL, NULL, NULL, NULL, 'User logged in with face match (Score: 73%)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 04:40:08', '2025-12-27 04:40:08'),
(22, 2, 'logout', NULL, NULL, NULL, NULL, 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 04:47:13', '2025-12-27 04:47:13'),
(23, 3, 'login', NULL, NULL, NULL, NULL, 'User logged in with face match (Score: 67%)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 05:30:58', '2025-12-27 05:30:58'),
(24, 3, 'logout', NULL, NULL, NULL, NULL, 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 05:32:54', '2025-12-27 05:32:54'),
(25, 3, 'login', NULL, NULL, NULL, NULL, 'User logged in with face match (Score: 70%)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 05:35:27', '2025-12-27 05:35:27'),
(26, 3, 'logout', NULL, NULL, NULL, NULL, 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 05:35:34', '2025-12-27 05:35:34'),
(27, 3, 'login', NULL, NULL, NULL, NULL, 'User logged in with face match (Score: 72%)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 06:07:34', '2025-12-27 06:07:34'),
(28, 3, 'logout', NULL, NULL, NULL, NULL, 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 06:07:40', '2025-12-27 06:07:40'),
(29, 2, 'login', NULL, NULL, NULL, NULL, 'User logged in with face match (Score: 68%)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 06:08:01', '2025-12-27 06:08:01'),
(30, 2, 'logout', NULL, NULL, NULL, NULL, 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 06:08:10', '2025-12-27 06:08:10'),
(31, 3, 'login', NULL, NULL, NULL, NULL, 'User logged in with face match (Score: 68%)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 06:19:33', '2025-12-27 06:19:33'),
(32, 3, 'logout', NULL, NULL, NULL, NULL, 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 06:19:43', '2025-12-27 06:19:43'),
(33, 3, 'login', NULL, NULL, NULL, NULL, 'User logged in with face match (Score: 74%)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 06:21:36', '2025-12-27 06:21:36'),
(34, 3, 'logout', NULL, NULL, NULL, NULL, 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 06:24:13', '2025-12-27 06:24:13'),
(35, 2, 'login', NULL, NULL, NULL, NULL, 'User logged in with face match (Score: 56%)', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 06:24:37', '2025-12-27 06:24:37'),
(36, 2, 'logout', NULL, NULL, NULL, NULL, 'User logged out', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '2025-12-27 06:24:40', '2025-12-27 06:24:40');

-- --------------------------------------------------------

--
-- Table structure for table `answers`
--

CREATE TABLE `answers` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `exam_attempt_id` bigint(20) UNSIGNED NOT NULL,
  `question_id` bigint(20) UNSIGNED NOT NULL,
  `answer` text DEFAULT NULL,
  `is_correct` tinyint(1) DEFAULT NULL,
  `marks_obtained` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exams`
--

CREATE TABLE `exams` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `duration_minutes` int(11) NOT NULL,
  `total_marks` int(11) NOT NULL,
  `passing_marks` int(11) NOT NULL,
  `start_time` datetime DEFAULT NULL,
  `end_time` datetime DEFAULT NULL,
  `status` enum('draft','published','active','completed','archived') NOT NULL DEFAULT 'draft',
  `shuffle_questions` tinyint(1) NOT NULL DEFAULT 0,
  `show_results` tinyint(1) NOT NULL DEFAULT 1,
  `face_verification_required` tinyint(1) NOT NULL DEFAULT 1,
  `max_attempts` int(11) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `exam_attempts`
--

CREATE TABLE `exam_attempts` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` bigint(20) UNSIGNED NOT NULL,
  `started_at` datetime NOT NULL,
  `completed_at` datetime DEFAULT NULL,
  `score` int(11) DEFAULT NULL,
  `total_marks` int(11) DEFAULT NULL,
  `percentage` decimal(5,2) DEFAULT NULL,
  `status` enum('in_progress','completed','abandoned','timed_out') NOT NULL DEFAULT 'in_progress',
  `passed` tinyint(1) DEFAULT NULL,
  `tab_switch_count` int(11) NOT NULL DEFAULT 0,
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `face_captures`
--

CREATE TABLE `face_captures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `exam_attempt_id` bigint(20) UNSIGNED DEFAULT NULL,
  `capture_type` enum('login','exam_start','exam_verification','registration') NOT NULL,
  `image_path` varchar(255) NOT NULL,
  `verification_passed` tinyint(1) DEFAULT NULL,
  `confidence_score` decimal(5,2) DEFAULT NULL,
  `liveness_verified` tinyint(1) NOT NULL DEFAULT 0,
  `blink_count` int(11) DEFAULT NULL,
  `metadata` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`metadata`)),
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `device_info` varchar(255) DEFAULT NULL,
  `browser_info` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `face_captures`
--

INSERT INTO `face_captures` (`id`, `user_id`, `exam_attempt_id`, `capture_type`, `image_path`, `verification_passed`, `confidence_score`, `liveness_verified`, `blink_count`, `metadata`, `ip_address`, `user_agent`, `device_info`, `browser_info`, `created_at`, `updated_at`) VALUES
(3, 3, NULL, 'login', 'face_captures/3/login_1766822864.png', NULL, NULL, 1, 3, '{\"liveness_verified\":true,\"blink_count\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '\"Windows\"', '\"Google Chrome\";v=\"143\", \"Chromium\";v=\"143\", \"Not A(Brand\";v=\"24\"', '2025-12-27 02:37:44', '2025-12-27 02:37:44'),
(4, 1, NULL, 'login', 'face_captures/1/login_1766822957.png', NULL, NULL, 1, 3, '{\"liveness_verified\":true,\"blink_count\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '\"Windows\"', '\"Google Chrome\";v=\"143\", \"Chromium\";v=\"143\", \"Not A(Brand\";v=\"24\"', '2025-12-27 02:39:17', '2025-12-27 02:39:17'),
(6, 2, NULL, 'login', 'face_captures/2/login_1766823561.png', NULL, NULL, 1, 3, '{\"liveness_verified\":true,\"blink_count\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '\"Windows\"', '\"Google Chrome\";v=\"143\", \"Chromium\";v=\"143\", \"Not A(Brand\";v=\"24\"', '2025-12-27 02:49:21', '2025-12-27 02:49:21'),
(8, 2, NULL, 'login', 'face_captures/2/login_1766829190.png', NULL, NULL, 1, 3, '{\"liveness_verified\":true,\"blink_count\":3,\"face_matched\":true,\"match_score\":68}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '\"Windows\"', '\"Google Chrome\";v=\"143\", \"Chromium\";v=\"143\", \"Not A(Brand\";v=\"24\"', '2025-12-27 04:23:10', '2025-12-27 04:23:10'),
(9, 2, NULL, 'login', 'face_captures/2/login_1766829413.png', NULL, NULL, 1, 3, '{\"liveness_verified\":true,\"blink_count\":3,\"face_matched\":true,\"match_score\":74}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '\"Windows\"', '\"Google Chrome\";v=\"143\", \"Chromium\";v=\"143\", \"Not A(Brand\";v=\"24\"', '2025-12-27 04:26:53', '2025-12-27 04:26:53'),
(10, 2, NULL, 'login', 'face_captures/2/login_1766830208.png', NULL, NULL, 1, 3, '{\"liveness_verified\":true,\"blink_count\":3,\"face_matched\":true,\"match_score\":73}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '\"Windows\"', '\"Google Chrome\";v=\"143\", \"Chromium\";v=\"143\", \"Not A(Brand\";v=\"24\"', '2025-12-27 04:40:08', '2025-12-27 04:40:08'),
(11, 3, NULL, 'login', 'face_captures/3/login_1766833258.png', NULL, NULL, 1, 3, '{\"liveness_verified\":true,\"blink_count\":3,\"face_matched\":true,\"match_score\":67}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '\"Windows\"', '\"Google Chrome\";v=\"143\", \"Chromium\";v=\"143\", \"Not A(Brand\";v=\"24\"', '2025-12-27 05:30:58', '2025-12-27 05:30:58'),
(12, 3, NULL, 'login', 'face_captures/3/login_1766833527.png', NULL, NULL, 1, 3, '{\"liveness_verified\":true,\"blink_count\":3,\"face_matched\":true,\"match_score\":70}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '\"Windows\"', '\"Google Chrome\";v=\"143\", \"Chromium\";v=\"143\", \"Not A(Brand\";v=\"24\"', '2025-12-27 05:35:27', '2025-12-27 05:35:27'),
(13, 3, NULL, 'login', 'face_captures/3/login_1766835454.png', NULL, NULL, 1, 3, '{\"liveness_verified\":true,\"blink_count\":3,\"face_matched\":true,\"match_score\":72}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '\"Windows\"', '\"Google Chrome\";v=\"143\", \"Chromium\";v=\"143\", \"Not A(Brand\";v=\"24\"', '2025-12-27 06:07:34', '2025-12-27 06:07:34'),
(14, 2, NULL, 'login', 'face_captures/2/login_1766835481.png', NULL, NULL, 1, 3, '{\"liveness_verified\":true,\"blink_count\":3,\"face_matched\":true,\"match_score\":68}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '\"Windows\"', '\"Google Chrome\";v=\"143\", \"Chromium\";v=\"143\", \"Not A(Brand\";v=\"24\"', '2025-12-27 06:08:01', '2025-12-27 06:08:01'),
(15, 3, NULL, 'login', 'face_captures/3/login_1766836172.png', NULL, NULL, 1, 3, '{\"liveness_verified\":true,\"blink_count\":3,\"face_matched\":true,\"match_score\":68}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '\"Windows\"', '\"Google Chrome\";v=\"143\", \"Chromium\";v=\"143\", \"Not A(Brand\";v=\"24\"', '2025-12-27 06:19:32', '2025-12-27 06:19:32'),
(16, 3, NULL, 'login', 'face_captures/3/login_1766836296.png', NULL, NULL, 1, 3, '{\"liveness_verified\":true,\"blink_count\":3,\"face_matched\":true,\"match_score\":74}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '\"Windows\"', '\"Google Chrome\";v=\"143\", \"Chromium\";v=\"143\", \"Not A(Brand\";v=\"24\"', '2025-12-27 06:21:36', '2025-12-27 06:21:36'),
(17, 2, NULL, 'login', 'face_captures/2/login_1766836477.png', NULL, NULL, 1, 3, '{\"liveness_verified\":true,\"blink_count\":3,\"face_matched\":true,\"match_score\":56}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', '\"Windows\"', '\"Google Chrome\";v=\"143\", \"Chromium\";v=\"143\", \"Not A(Brand\";v=\"24\"', '2025-12-27 06:24:37', '2025-12-27 06:24:37');

-- --------------------------------------------------------

--
-- Table structure for table `face_embeddings`
--

CREATE TABLE `face_embeddings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `embedding_data` longtext NOT NULL COMMENT 'Encrypted face embedding vector',
  `embedding_hash` varchar(255) NOT NULL COMMENT 'Hash for quick comparison',
  `quality_score` int(11) NOT NULL DEFAULT 0,
  `is_primary` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `face_embeddings`
--

INSERT INTO `face_embeddings` (`id`, `user_id`, `embedding_data`, `embedding_hash`, `quality_score`, `is_primary`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 1, '[-0.4137254901960784,-0.03725490196078429,0.44509803921568625,0.045098039215686225,0.3549019607843137,-0.07647058823529412,0.18627450980392157,-0.17450980392156862,0.3313725490196079,-0.4764705882352941,-0.02156862745098037,0.27254901960784317,-0.06470588235294117,-0.33529411764705885,0.4137254901960784,0.13921568627450975,-0.34705882352941175,0.025490196078431393,-0.3784313725490196,-0.3196078431372549,0.09999999999999998,-0.4607843137254902,0.056862745098039236,-0.15490196078431373,0.13137254901960782,-0.04901960784313725,0.4372549019607843,0.38627450980392153,0.11176470588235299,0.01764705882352946,0.21372549019607845,0.20588235294117652,0.4294117647058824,0.20196078431372544,-0.33137254901960783,0.06862745098039214,0.22941176470588232,0.3431372549019608,0.2529411764705882,-0.38235294117647056,-0.08039215686274509,-0.052941176470588214,0.49607843137254903,-0.33529411764705885,-0.21764705882352942,0.0725490196078431,-0.3627450980392157,0.22549019607843135,0.23725490196078436,0.37450980392156863,-0.43333333333333335,0.40980392156862744,0.06470588235294117,0.20980392156862748,0.22549019607843135,-0.21764705882352942,0.24117647058823533,0.4411764705882353,-0.4215686274509804,-0.3862745098039216,0.056862745098039236,-0.09607843137254901,0.04117647058823526,0.21764705882352942,0.4607843137254902,-0.39019607843137255,-0.42549019607843136,0.3156862745098039,0.1941176470588235,0.19019607843137254,-0.09607843137254901,-0.20980392156862743,-0.14313725490196078,-0.20980392156862743,-0.33137254901960783,-0.3588235294117647,0.35882352941176465,-0.013725490196078438,0.1941176470588235,-0.26078431372549016,0.20588235294117652,-0.02941176470588236,-0.03725490196078429,-0.103921568627451,0.11960784313725492,0.1470588235294118,0.34705882352941175,0.3313725490196079,-0.11176470588235293,-0.21764705882352942,0.11960784313725492,-0.44509803921568625,-0.3588235294117647,-0.08039215686274509,0.06470588235294117,-0.3431372549019608,0.4372549019607843,0.303921568627451,0.3509803921568627,-0.49215686274509807,-0.303921568627451,0.056862745098039236,-0.14313725490196078,0.08431372549019611,-0.4137254901960784,-0.3156862745098039,0.2803921568627451,0.08039215686274515,-0.08823529411764708,-0.307843137254902,-0.041176470588235314,-0.44509803921568625,0.23725490196078436,-0.041176470588235314,-0.17450980392156862,-0.3431372549019608,-0.14705882352941174,-0.34705882352941175,-0.27647058823529413,-0.13137254901960782,-0.3627450980392157,0.3196078431372549,0.15098039215686276,-0.33529411764705885,0.20196078431372544,-0.4647058823529412,-0.4411764705882353,-0.103921568627451]', 'a277f18919e93b646659ea18c362e973', 85, 1, 1, '2025-12-27 02:33:18', '2025-12-27 02:33:18'),
(2, 2, '[0.36274509803921573,-0.27254901960784317,-0.3941176470588235,-0.11960784313725492,-0.1823529411764706,0.17843137254901964,0.41764705882352937,-0.06862745098039214,-0.3196078431372549,-0.27254901960784317,-0.033333333333333326,-0.17843137254901958,-0.0607843137254902,0.38235294117647056,0.307843137254902,0.16666666666666663,-0.08039215686274509,0.46862745098039216,-0.49215686274509807,0.0607843137254902,-0.15098039215686276,-0.08431372549019606,0.20588235294117652,0.0490196078431373,0.3549019607843137,0.5,0.06470588235294117,0.43333333333333335,-0.04509803921568628,-0.39019607843137255,0.28431372549019607,-0.22549019607843135,0.22156862745098038,-0.21372549019607845,-0.3784313725490196,0.303921568627451,-0.3156862745098039,-0.19019607843137254,-0.48823529411764705,-0.06470588235294117,-0.3509803921568627,0.10784313725490191,-0.15490196078431373,0.41764705882352937,-0.013725490196078438,-0.02941176470588236,0.3156862745098039,0.21764705882352942,0.16666666666666663,-0.1823529411764706,0.30000000000000004,0.009803921568627416,0.01764705882352946,-0.1588235294117647,-0.48823529411764705,0.09215686274509804,0.1352941176470588,0.43333333333333335,0.22156862745098038,0.16274509803921566,0.09607843137254901,0.08823529411764708,0.44509803921568625,-0.0019607843137254832,0.4882352941176471,-0.02941176470588236,0.2529411764705882,-0.3666666666666667,0.12745098039215685,0.41764705882352937,-0.18627450980392157,-0.033333333333333326,-0.103921568627451,-0.32745098039215687,0.16666666666666663,-0.32352941176470584,0.4882352941176471,0.05294117647058827,0.11176470588235299,-0.34705882352941175,0.22549019607843135,-0.3196078431372549,0.303921568627451,-0.4803921568627451,-0.3588235294117647,-0.04901960784313725,0.12745098039215685,0.17843137254901964,0.14313725490196083,-0.2372549019607843,-0.3862745098039216,0.1705882352941176,-0.025490196078431393,-0.21372549019607845,0.29607843137254897,0.4372549019607843,0.26078431372549016,-0.2803921568627451,0.24117647058823533,-0.07647058823529412,-0.4803921568627451,-0.44901960784313727,0.45686274509803926,-0.27647058823529413,0.46862745098039216,0.24117647058823533,0.021568627450980427,0.48039215686274506,0.40980392156862744,-0.12745098039215685,-0.3784313725490196,0.38235294117647056,-0.24901960784313726,-0.39803921568627454,0.37450980392156863,0.4137254901960784,0.09607843137254901,0.3431372549019608,-0.20588235294117646,-0.33529411764705885,-0.2647058823529412,-0.21372549019607845,0.12352941176470589,-0.056862745098039236,0.4647058823529412,0.3549019607843137,0.48039215686274506,-0.009803921568627472]', '1dd785d70cf3d241fb0b1f719ac51b06', 85, 1, 1, '2025-12-27 02:33:18', '2025-12-27 02:33:18');

-- --------------------------------------------------------

--
-- Table structure for table `face_verification_logs`
--

CREATE TABLE `face_verification_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `verification_type` enum('login','registration','exam_start','re_enrollment') NOT NULL,
  `status` enum('success','failed','blocked','no_face','spoof_detected','low_quality') NOT NULL,
  `match_score` decimal(5,2) DEFAULT NULL,
  `liveness_score` decimal(5,2) DEFAULT NULL,
  `quality_score` decimal(5,2) DEFAULT NULL,
  `failure_reason` varchar(255) DEFAULT NULL,
  `liveness_checks` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`liveness_checks`)),
  `ip_address` varchar(255) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `device_fingerprint` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `face_verification_logs`
--

INSERT INTO `face_verification_logs` (`id`, `user_id`, `email`, `verification_type`, `status`, `match_score`, `liveness_score`, `quality_score`, `failure_reason`, `liveness_checks`, `ip_address`, `user_agent`, `device_fingerprint`, `created_at`, `updated_at`) VALUES
(1, 2, 'yash@examportal.com', 'login', 'success', 85.00, 100.00, 80.00, NULL, '{\"liveness_verified\":true,\"blink_count\":3,\"eye_blink_detection\":true}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-27 02:34:45', '2025-12-27 02:34:45'),
(2, 3, 'harsh@examportal.com', 'registration', 'success', NULL, 100.00, 80.00, NULL, '{\"liveness_verified\":true,\"blink_count\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-27 02:36:39', '2025-12-27 02:36:39'),
(3, 3, 'harsh@examportal.com', 'login', 'success', 85.00, 100.00, 80.00, NULL, '{\"liveness_verified\":true,\"blink_count\":3,\"eye_blink_detection\":true}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-27 02:37:44', '2025-12-27 02:37:44'),
(4, 1, 'admin@examportal.com', 'login', 'success', 85.00, 100.00, 80.00, NULL, '{\"liveness_verified\":true,\"blink_count\":3,\"eye_blink_detection\":true}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-27 02:39:17', '2025-12-27 02:39:17'),
(5, 2, 'yash@examportal.com', 'login', 'success', 85.00, 100.00, 80.00, NULL, '{\"liveness_verified\":true,\"blink_count\":3,\"eye_blink_detection\":true}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-27 02:40:26', '2025-12-27 02:40:26'),
(6, 2, 'yash@examportal.com', 'login', 'success', 85.00, 100.00, 80.00, NULL, '{\"liveness_verified\":true,\"blink_count\":3,\"eye_blink_detection\":true}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-27 02:49:21', '2025-12-27 02:49:21'),
(7, 2, 'yash@examportal.com', 'login', 'success', 85.00, 100.00, 80.00, NULL, '{\"liveness_verified\":true,\"blink_count\":3,\"eye_blink_detection\":true}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-27 03:58:28', '2025-12-27 03:58:28'),
(8, 2, 'yash@examportal.com', 'login', 'success', 68.00, 100.00, 80.00, NULL, '{\"liveness_verified\":true,\"blink_count\":3,\"face_matched\":true,\"match_score\":68}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-27 04:23:10', '2025-12-27 04:23:10'),
(9, 2, 'yash@examportal.com', 'login', 'success', 74.00, 100.00, 80.00, NULL, '{\"liveness_verified\":true,\"blink_count\":3,\"face_matched\":true,\"match_score\":74}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-27 04:26:53', '2025-12-27 04:26:53'),
(10, 2, 'yash@examportal.com', 'login', 'success', 73.00, 100.00, 80.00, NULL, '{\"liveness_verified\":true,\"blink_count\":3,\"face_matched\":true,\"match_score\":73}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-27 04:40:08', '2025-12-27 04:40:08'),
(11, 3, 'harsh@examportal.com', 'login', 'success', 67.00, 100.00, 80.00, NULL, '{\"liveness_verified\":true,\"blink_count\":3,\"face_matched\":true,\"match_score\":67}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-27 05:30:58', '2025-12-27 05:30:58'),
(12, 3, 'harsh@examportal.com', 'login', 'success', 70.00, 100.00, 80.00, NULL, '{\"liveness_verified\":true,\"blink_count\":3,\"face_matched\":true,\"match_score\":70}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-27 05:35:27', '2025-12-27 05:35:27'),
(13, 3, 'harsh@examportal.com', 'login', 'success', 72.00, 100.00, 80.00, NULL, '{\"liveness_verified\":true,\"blink_count\":3,\"face_matched\":true,\"match_score\":72}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-27 06:07:34', '2025-12-27 06:07:34'),
(14, 2, 'yash@examportal.com', 'login', 'success', 68.00, 100.00, 80.00, NULL, '{\"liveness_verified\":true,\"blink_count\":3,\"face_matched\":true,\"match_score\":68}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-27 06:08:01', '2025-12-27 06:08:01'),
(15, 3, 'harsh@examportal.com', 'login', 'success', 68.00, 100.00, 80.00, NULL, '{\"liveness_verified\":true,\"blink_count\":3,\"face_matched\":true,\"match_score\":68}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-27 06:19:32', '2025-12-27 06:19:32'),
(16, 3, 'harsh@examportal.com', 'login', 'success', 74.00, 100.00, 80.00, NULL, '{\"liveness_verified\":true,\"blink_count\":3,\"face_matched\":true,\"match_score\":74}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-27 06:21:36', '2025-12-27 06:21:36'),
(17, 2, 'yash@examportal.com', 'login', 'success', 56.00, 100.00, 80.00, NULL, '{\"liveness_verified\":true,\"blink_count\":3,\"face_matched\":true,\"match_score\":56}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, '2025-12-27 06:24:37', '2025-12-27 06:24:37');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2024_01_01_000001_create_roles_table', 1),
(5, '2024_01_01_000002_create_exams_table', 1),
(6, '2024_01_01_000003_create_questions_table', 1),
(7, '2024_01_01_000004_create_exam_attempts_table', 1),
(8, '2024_01_01_000005_create_answers_table', 1),
(9, '2024_01_01_000006_create_face_captures_table', 1),
(10, '2024_01_01_000007_create_activity_logs_table', 1),
(11, '2024_01_01_000008_create_face_verification_tables', 1);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `exam_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('mcq','descriptive') NOT NULL DEFAULT 'mcq',
  `question` text NOT NULL,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`options`)),
  `correct_answer` text DEFAULT NULL,
  `marks` int(11) NOT NULL DEFAULT 1,
  `order` int(11) NOT NULL DEFAULT 0,
  `explanation` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Administrator', 'admin', 'Full system access', '2025-12-27 02:33:17', '2025-12-27 02:33:17'),
(2, 'User', 'user', 'Student/User access', '2025-12-27 02:33:18', '2025-12-27 02:33:18');

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL DEFAULT 2,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `profile_image` varchar(255) DEFAULT NULL,
  `reference_face_image` varchar(255) DEFAULT NULL,
  `face_verified` tinyint(1) NOT NULL DEFAULT 0,
  `face_enrollment_count` int(11) NOT NULL DEFAULT 0,
  `face_enrolled_at` timestamp NULL DEFAULT NULL,
  `failed_face_attempts` int(11) NOT NULL DEFAULT 0,
  `face_locked_until` timestamp NULL DEFAULT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `last_login_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `role_id`, `name`, `email`, `phone`, `address`, `profile_image`, `reference_face_image`, `face_verified`, `face_enrollment_count`, `face_enrolled_at`, `failed_face_attempts`, `face_locked_until`, `status`, `last_login_at`, `email_verified_at`, `password`, `remember_token`, `created_at`, `updated_at`) VALUES
(1, 1, 'Admin User', 'admin@examportal.com', NULL, NULL, NULL, NULL, 1, 1, '2025-12-27 02:33:18', 0, NULL, 'active', '2025-12-27 02:39:17', '2025-12-27 02:33:18', '$2y$12$1IufVLNTtF3IDlPzRUwijeX6caNlRwBrxQ7anDPJIU3918BIqpiTa', NULL, '2025-12-27 02:33:18', '2025-12-27 02:39:17'),
(2, 2, 'Yash', 'yash@examportal.com', NULL, NULL, NULL, 'reference_faces/2_1766823060.png', 1, 1, '2025-12-27 02:33:18', 0, NULL, 'active', '2025-12-27 06:24:37', '2025-12-27 02:33:18', '$2y$12$1t7O0OP6Nuo.6fbsFSDjROpo2wv775XR0txnDuYKny34LoT8Fqv6a', NULL, '2025-12-27 02:33:18', '2025-12-27 06:24:37'),
(3, 2, 'harsh', 'harsh@examportal.com', NULL, NULL, NULL, 'reference_faces/3_1766822825.png', 1, 0, NULL, 0, NULL, 'active', '2025-12-27 06:21:36', NULL, '$2y$12$icMy/3pxEvADb9g1IEr9s.PslAZtzTkrGlD/bgLPr3kEK9iRhqfhq', NULL, '2025-12-27 02:36:39', '2025-12-27 06:21:36');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `activity_logs_user_id_action_index` (`user_id`,`action`),
  ADD KEY `activity_logs_model_type_model_id_index` (`model_type`,`model_id`);

--
-- Indexes for table `answers`
--
ALTER TABLE `answers`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `answers_exam_attempt_id_question_id_unique` (`exam_attempt_id`,`question_id`),
  ADD KEY `answers_question_id_foreign` (`question_id`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `exams`
--
ALTER TABLE `exams`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exams_created_by_foreign` (`created_by`);

--
-- Indexes for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  ADD PRIMARY KEY (`id`),
  ADD KEY `exam_attempts_exam_id_foreign` (`exam_id`),
  ADD KEY `exam_attempts_user_id_exam_id_index` (`user_id`,`exam_id`);

--
-- Indexes for table `face_captures`
--
ALTER TABLE `face_captures`
  ADD PRIMARY KEY (`id`),
  ADD KEY `face_captures_exam_attempt_id_foreign` (`exam_attempt_id`),
  ADD KEY `face_captures_user_id_capture_type_index` (`user_id`,`capture_type`);

--
-- Indexes for table `face_embeddings`
--
ALTER TABLE `face_embeddings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `face_embeddings_user_id_is_active_index` (`user_id`,`is_active`);

--
-- Indexes for table `face_verification_logs`
--
ALTER TABLE `face_verification_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `face_verification_logs_user_id_status_index` (`user_id`,`status`),
  ADD KEY `face_verification_logs_created_at_index` (`created_at`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `questions_exam_id_foreign` (`exam_id`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_name_unique` (`name`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_role_id_foreign` (`role_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `activity_logs`
--
ALTER TABLE `activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `answers`
--
ALTER TABLE `answers`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exams`
--
ALTER TABLE `exams`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `face_captures`
--
ALTER TABLE `face_captures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `face_embeddings`
--
ALTER TABLE `face_embeddings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `face_verification_logs`
--
ALTER TABLE `face_verification_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `activity_logs`
--
ALTER TABLE `activity_logs`
  ADD CONSTRAINT `activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `answers`
--
ALTER TABLE `answers`
  ADD CONSTRAINT `answers_exam_attempt_id_foreign` FOREIGN KEY (`exam_attempt_id`) REFERENCES `exam_attempts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `answers_question_id_foreign` FOREIGN KEY (`question_id`) REFERENCES `questions` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exams`
--
ALTER TABLE `exams`
  ADD CONSTRAINT `exams_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `exam_attempts`
--
ALTER TABLE `exam_attempts`
  ADD CONSTRAINT `exam_attempts_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `exam_attempts_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `face_captures`
--
ALTER TABLE `face_captures`
  ADD CONSTRAINT `face_captures_exam_attempt_id_foreign` FOREIGN KEY (`exam_attempt_id`) REFERENCES `exam_attempts` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `face_captures_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `face_embeddings`
--
ALTER TABLE `face_embeddings`
  ADD CONSTRAINT `face_embeddings_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `face_verification_logs`
--
ALTER TABLE `face_verification_logs`
  ADD CONSTRAINT `face_verification_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_exam_id_foreign` FOREIGN KEY (`exam_id`) REFERENCES `exams` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_role_id_foreign` FOREIGN KEY (`role_id`) REFERENCES `roles` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
