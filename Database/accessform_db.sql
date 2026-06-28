-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 23, 2026 at 11:23 AM
-- Server version: 10.4.28-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `accessform_db`
--

-- --------------------------------------------------------

--
-- Table structure for table `accessibility_logs`
--

CREATE TABLE `accessibility_logs` (
  `id` int(11) NOT NULL,
  `form_id` int(11) DEFAULT NULL,
  `user_id` int(11) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `ip_address` varchar(50) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `accessibility_logs`
--

INSERT INTO `accessibility_logs` (`id`, `form_id`, `user_id`, `action`, `ip_address`, `created_at`) VALUES
(1, NULL, 2, 'dyslexia', '::1', '2026-06-23 09:13:25'),
(2, NULL, 2, 'dyslexia', '::1', '2026-06-23 09:14:37'),
(3, NULL, 2, 'dyslexia', '::1', '2026-06-23 09:14:38'),
(4, NULL, 2, 'dyslexia', '::1', '2026-06-23 09:15:18'),
(5, NULL, 2, 'high-contrast', '::1', '2026-06-23 09:15:22');

-- --------------------------------------------------------

--
-- Table structure for table `admins`
--

CREATE TABLE `admins` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `admins`
--

INSERT INTO `admins` (`id`, `username`, `password`) VALUES
(1, 'admin', 'admin');

-- --------------------------------------------------------

--
-- Table structure for table `forms`
--

CREATE TABLE `forms` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `status` enum('draft','active','closed') DEFAULT 'active',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `forms`
--

INSERT INTO `forms` (`id`, `user_id`, `title`, `description`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'What is Your Degree', 'What is Your Degree', 'active', '2026-06-23 08:20:52', '2026-06-23 08:57:09');

-- --------------------------------------------------------

--
-- Table structure for table `form_answers`
--

CREATE TABLE `form_answers` (
  `id` int(11) NOT NULL,
  `response_id` int(11) NOT NULL,
  `question_id` int(11) NOT NULL,
  `answer` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form_answers`
--

INSERT INTO `form_answers` (`id`, `response_id`, `question_id`, `answer`) VALUES
(1, 1, 1, 'Degree'),
(2, 1, 4, '2.'),
(3, 1, 5, 'Lecture Based'),
(4, 1, 6, 'Computer Lab'),
(5, 1, 7, 'dds'),
(6, 2, 1, 'ds'),
(7, 2, 4, 'ds'),
(8, 2, 5, 'Group Discussions'),
(9, 3, 1, 'ew_fo'),
(10, 3, 4, 'ew_fo'),
(11, 3, 5, 'Lecture Based'),
(12, 4, 1, 'comments or suggestions?'),
(13, 4, 4, 'comments or suggestions?'),
(14, 4, 5, 'Lecture Based'),
(15, 5, 1, 'comments or suggestions?'),
(16, 5, 4, 'comments or suggestions?'),
(17, 5, 5, 'Lecture Based'),
(18, 6, 1, 'What is Your Degree'),
(19, 6, 4, 'What is Your Degree'),
(20, 6, 5, 'Practical / Hands-on'),
(21, 7, 1, 'What is Your Degree'),
(22, 7, 4, 'What is Your Degree'),
(23, 7, 5, 'Practical / Hands-on'),
(24, 8, 1, 'eaching quality rating scale'),
(25, 8, 4, 'eaching quality rating scale'),
(26, 8, 5, 'Practical / Hands-on'),
(27, 9, 1, 'No Internet? S'),
(28, 9, 4, 'No Internet? S'),
(29, 9, 5, 'Lecture Based'),
(30, 10, 1, 'No Internet? S'),
(31, 10, 4, 'No Internet? S'),
(32, 10, 5, 'Lecture Based'),
(33, 11, 1, 'No Internet? S'),
(34, 11, 4, 'No Internet? S'),
(35, 11, 5, 'Project Based'),
(36, 12, 1, 'What is Your Degree'),
(37, 12, 4, 'What is Your Degree'),
(38, 12, 5, 'Practical / Hands-on'),
(39, 12, 6, 'Sports Ground'),
(40, 13, 1, 'What is Your Degree'),
(41, 13, 4, 'What is Your Degree'),
(42, 13, 5, 'Practical / Hands-on'),
(43, 13, 6, 'Sports Ground');

-- --------------------------------------------------------

--
-- Table structure for table `form_responses`
--

CREATE TABLE `form_responses` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `respondent_id` int(11) DEFAULT NULL,
  `respondent_ip` varchar(50) DEFAULT NULL,
  `submitted_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `form_responses`
--

INSERT INTO `form_responses` (`id`, `form_id`, `respondent_id`, `respondent_ip`, `submitted_at`) VALUES
(1, 1, NULL, '::1', '2026-06-23 13:32:53'),
(2, 1, 2, '::1', '2026-06-23 13:45:47'),
(3, 1, 2, '::1', '2026-06-23 14:06:17'),
(4, 1, 2, '::1', '2026-06-23 14:08:27'),
(5, 1, 2, '::1', '2026-06-23 14:09:44'),
(6, 1, 2, '::1', '2026-06-23 14:10:09'),
(7, 1, 2, '::1', '2026-06-23 14:11:03'),
(8, 1, 2, '::1', '2026-06-23 14:11:23'),
(9, 1, 2, '::1', '2026-06-23 14:12:46'),
(10, 1, 2, '::1', '2026-06-23 14:13:19'),
(11, 1, 2, '::1', '2026-06-23 14:13:36'),
(12, 1, 2, '::1', '2026-06-23 14:14:54'),
(13, 1, 2, '::1', '2026-06-23 14:15:24');

-- --------------------------------------------------------

--
-- Table structure for table `questions`
--

CREATE TABLE `questions` (
  `id` int(11) NOT NULL,
  `form_id` int(11) NOT NULL,
  `question_text` text NOT NULL,
  `question_type` enum('text','textarea','radio','checkbox','dropdown','rating','file') NOT NULL,
  `is_required` tinyint(1) DEFAULT 0,
  `options` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `questions`
--

INSERT INTO `questions` (`id`, `form_id`, `question_text`, `question_type`, `is_required`, `options`, `sort_order`, `created_at`) VALUES
(1, 1, 'What is Your Degree', 'text', 1, '{\"options\":[],\"alt_text\":\"Degree\",\"video_url\":\"http:\\/\\/localhost\\/Spring26\\/Accessform\\/creator\\/create_form.php\",\"video_caption\":\"XYZ\"}', 2, '2026-06-23 08:20:52'),
(2, 1, 'What is Your NAme', 'text', 0, '{\"options\":[],\"alt_text\":\"Name\",\"video_url\":\"http:\\/\\/localhost\\/Spring26\\/Accessform\\/creator\\/create_form.php\",\"video_caption\":\"XYZ\"}', 3, '2026-06-23 08:20:52'),
(3, 1, 'How satisfied are you with the overall teaching quality?', 'rating', 1, '{\"alt_text\":\"Teaching quality rating scale\",\"video_url\":\"\",\"video_caption\":\"\"}', 1, '2026-06-23 08:22:14'),
(4, 1, 'What is your favorite subject this semester?', 'text', 1, '{\"alt_text\":\"\",\"video_url\":\"\",\"video_caption\":\"\"}', 2, '2026-06-23 08:22:14'),
(5, 1, 'Which teaching method do you prefer the most?', 'radio', 1, '{\"options\":[\"Lecture Based\",\"Practical / Hands-on\",\"Group Discussions\",\"Project Based\",\"Online Videos\"]}', 3, '2026-06-23 08:22:14'),
(6, 1, 'Select all facilities you think need improvement:', 'checkbox', 0, '{\"options\":[\"Library\",\"Computer Lab\",\"Cafeteria\",\"Sports Ground\",\"Hostel\",\"WiFi\"]}', 4, '2026-06-23 08:22:14'),
(7, 1, 'Any additional comments or suggestions?', 'textarea', 0, '{\"alt_text\":\"\",\"video_url\":\"https://youtu.be/example-signlanguage\",\"video_caption\":\"Please provide your suggestions in detail\"}', 5, '2026-06-23 08:22:14');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `email` varchar(150) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('Respondent','creator') DEFAULT 'creator',
  `phone` varchar(30) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `password`, `role`, `phone`, `created_at`, `updated_at`) VALUES
(1, 'NASIR ABBAS', 'nasiryt.827@gmail.com', '$2y$10$Pz/eRx2EQa2a.GnIit1F9eg4sYi5CDhdqkB0g1o62iWmQEzVQatj6', 'creator', '+923266029044', '2026-06-23 07:53:52', '2026-06-23 07:58:06'),
(2, 'Responder', 'saifx280@gmail.com', '$2y$10$Zb32tmjPH2PZ8ZqqCCga1ODqHpjJzg8AiRPW.UD0fW.ociY.lNGrm', 'Respondent', '03266029051', '2026-06-23 08:40:52', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `accessibility_logs`
--
ALTER TABLE `accessibility_logs`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `admins`
--
ALTER TABLE `admins`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `forms`
--
ALTER TABLE `forms`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `form_answers`
--
ALTER TABLE `form_answers`
  ADD PRIMARY KEY (`id`),
  ADD KEY `response_id` (`response_id`);

--
-- Indexes for table `form_responses`
--
ALTER TABLE `form_responses`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`);

--
-- Indexes for table `questions`
--
ALTER TABLE `questions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `form_id` (`form_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `accessibility_logs`
--
ALTER TABLE `accessibility_logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `admins`
--
ALTER TABLE `admins`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `forms`
--
ALTER TABLE `forms`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `form_answers`
--
ALTER TABLE `form_answers`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=44;

--
-- AUTO_INCREMENT for table `form_responses`
--
ALTER TABLE `form_responses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `questions`
--
ALTER TABLE `questions`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `forms`
--
ALTER TABLE `forms`
  ADD CONSTRAINT `forms_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `form_answers`
--
ALTER TABLE `form_answers`
  ADD CONSTRAINT `form_answers_ibfk_1` FOREIGN KEY (`response_id`) REFERENCES `form_responses` (`id`);

--
-- Constraints for table `form_responses`
--
ALTER TABLE `form_responses`
  ADD CONSTRAINT `form_responses_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`);

--
-- Constraints for table `questions`
--
ALTER TABLE `questions`
  ADD CONSTRAINT `questions_ibfk_1` FOREIGN KEY (`form_id`) REFERENCES `forms` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
