-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 25, 2026 at 02:32 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.4

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mathventure`
--

-- --------------------------------------------------------

--
-- Table structure for table `attendance`
--

CREATE TABLE `attendance` (
  `id_attendance` int(11) NOT NULL,
  `id_user` int(10) UNSIGNED DEFAULT NULL,
  `status` enum('H','TH') NOT NULL,
  `date_recorded` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `attendance`
--

INSERT INTO `attendance` (`id_attendance`, `id_user`, `status`, `date_recorded`) VALUES
(1, 5, 'H', '2026-01-19'),
(2, 3, 'H', '2026-01-19'),
(3, 5, 'H', '2026-01-19'),
(4, 3, 'H', '2026-01-19'),
(5, 5, 'H', '2026-01-19'),
(6, 3, 'H', '2026-01-19'),
(7, 5, 'H', '2026-01-21'),
(8, 5, 'H', '2026-01-21'),
(9, 5, 'H', '2026-01-21');

-- --------------------------------------------------------

--
-- Table structure for table `student`
--

CREATE TABLE `student` (
  `id_user` int(10) UNSIGNED NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `dob` date DEFAULT NULL,
  `class` varchar(50) DEFAULT NULL,
  `year_level` tinyint(3) UNSIGNED DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `level` int(11) DEFAULT 1,
  `current_xp` int(11) DEFAULT 0,
  `max_xp` int(11) DEFAULT 100,
  `coins` int(11) DEFAULT 0,
  `lives` int(11) DEFAULT 5,
  `level_t4` int(11) DEFAULT 1,
  `level_t5` int(11) DEFAULT 1,
  `level_t6` int(11) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id_user`, `firstname`, `lastname`, `dob`, `class`, `year_level`, `bio`, `avatar`, `created_at`, `level`, `current_xp`, `max_xp`, `coins`, `lives`, `level_t4`, `level_t5`, `level_t6`) VALUES
(3, 'muridDemo', '', NULL, '4 Dinamik', NULL, NULL, NULL, '2026-01-19 03:07:33', 1, 0, 100, 0, 5, 1, 1, 1),
(5, 'anis21', 'nadirah', '2020-05-13', '4 beta', 5, 'helo, saya anis', 'dinasour2.png', '2025-12-01 08:16:48', 1, 0, 100, 0, 5, 1, 1, 1);

-- --------------------------------------------------------

--
-- Table structure for table `student_badges`
--

CREATE TABLE `student_badges` (
  `id_badge_win` int(11) NOT NULL,
  `id_user` int(11) DEFAULT NULL,
  `badge_name` varchar(100) DEFAULT NULL,
  `tahun` int(11) DEFAULT NULL,
  `level` int(11) DEFAULT NULL,
  `date_unlocked` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `student_scores`
--

CREATE TABLE `student_scores` (
  `id_score` int(11) NOT NULL,
  `id_user` int(10) UNSIGNED NOT NULL,
  `topic_name` varchar(100) NOT NULL,
  `score` int(11) NOT NULL,
  `total_questions` int(11) NOT NULL,
  `date_completed` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student_scores`
--

INSERT INTO `student_scores` (`id_score`, `id_user`, `topic_name`, `score`, `total_questions`, `date_completed`) VALUES
(1, 5, 'Latihan Tahun 4 Level 1', 2, 3, '2026-01-19 03:26:06'),
(2, 5, 'Mathventure Tahun 4 Level 1', 3, 3, '2026-01-19 03:33:44'),
(3, 5, 'Mathventure Tahun 4 Level 1', 3, 3, '2026-01-19 03:46:10');

-- --------------------------------------------------------

--
-- Table structure for table `teacher`
--

CREATE TABLE `teacher` (
  `id_user` int(10) UNSIGNED NOT NULL,
  `firstname` varchar(100) NOT NULL,
  `lastname` varchar(100) NOT NULL,
  `class` varchar(50) DEFAULT NULL,
  `year` varchar(20) DEFAULT NULL,
  `bio` text DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `teacher`
--

INSERT INTO `teacher` (`id_user`, `firstname`, `lastname`, `class`, `year`, `bio`, `avatar`, `created_at`) VALUES
(2, 'Cikgu', 'Demo', '4 Dinamik', NULL, NULL, NULL, '2026-01-19 02:41:35'),
(4, 'CIkgu', 'Farhan', '4 Bestari', 'Tahun 4-6', 'sasa', '', '2025-11-28 02:43:07'),
(6, 'amnah', 'liza', '4 beta', '4', 'guru matematik paling disayangi', '', '2025-12-11 00:23:19'),
(7, 'alia', 'timah', '4 beta', '4', 'saya guru matematik', '', '2026-01-20 17:55:32');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_user` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `role` enum('pelajar','guru','admin') NOT NULL DEFAULT 'pelajar',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_level` varchar(20) NOT NULL DEFAULT 'pelajar',
  `last_activity` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password_hash`, `role`, `created_at`, `user_level`, `last_activity`) VALUES
(2, 'cikguDemo', '$2y$10$81L0RtHRTIKGzu8GiU4jqO0DSq0mUnZU7a.3beKQa9aH88mum2iQy', 'guru', '2025-11-28 02:21:28', 'pelajar', '2026-01-20 19:04:22'),
(3, 'muridDemo', '$2y$10$5xDqsisbXrjzhjZk99SBdu8SmDimPeq6ipz02Hr6QWqMSynvTtOti', 'pelajar', '2025-11-28 02:21:28', 'pelajar', '2026-01-20 19:04:22'),
(4, 'Faham', '$2y$10$QVK0ZXU3mPRKoSycXKvqy.zj2dV.QFhS.e4mQZvfqyPsddJN1RKBa', 'guru', '2025-11-28 02:43:07', 'pelajar', '2026-01-20 19:04:22'),
(5, 'anis21', '$2y$10$f7sYLlf0NeCCeT4lG/yfTObw65AwDy199JYMK6/evjs/Q66o42di2', 'pelajar', '2025-12-01 08:16:48', 'pelajar', '2026-01-21 03:38:41'),
(6, 'amnahliz', '$2y$10$1NsXnKeZla3B3WGh1/gPPe7F.BT0cEzbX./9V0TDL2NlvCWb/NNxG', 'guru', '2025-12-11 00:23:19', 'pelajar', '2026-01-20 19:04:22'),
(7, 'CikguAlia', '$2y$10$vNzbnykg6FPpvQg889W1Yeot.q9SIa1n/ZNwjM90HLWmZqGfhXr.K', 'guru', '2026-01-20 17:55:32', 'pelajar', '2026-01-21 04:55:01');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attendance`
--
ALTER TABLE `attendance`
  ADD PRIMARY KEY (`id_attendance`),
  ADD KEY `id_user` (`id_user`);

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id_user`);

--
-- Indexes for table `student_badges`
--
ALTER TABLE `student_badges`
  ADD PRIMARY KEY (`id_badge_win`);

--
-- Indexes for table `student_scores`
--
ALTER TABLE `student_scores`
  ADD PRIMARY KEY (`id_score`),
  ADD KEY `fk_scores_user` (`id_user`);

--
-- Indexes for table `teacher`
--
ALTER TABLE `teacher`
  ADD PRIMARY KEY (`id_user`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_user`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attendance`
--
ALTER TABLE `attendance`
  MODIFY `id_attendance` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `student_badges`
--
ALTER TABLE `student_badges`
  MODIFY `id_badge_win` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `student_scores`
--
ALTER TABLE `student_scores`
  MODIFY `id_score` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `attendance`
--
ALTER TABLE `attendance`
  ADD CONSTRAINT `attendance_ibfk_1` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `fk_student_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `student_scores`
--
ALTER TABLE `student_scores`
  ADD CONSTRAINT `fk_scores_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `teacher`
--
ALTER TABLE `teacher`
  ADD CONSTRAINT `fk_teacher_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
