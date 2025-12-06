-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 06, 2025 at 03:25 PM
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
  `lives` int(11) DEFAULT 5
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `student`
--

INSERT INTO `student` (`id_user`, `firstname`, `lastname`, `dob`, `class`, `year_level`, `bio`, `avatar`, `created_at`, `level`, `current_xp`, `max_xp`, `coins`, `lives`) VALUES
(5, 'anis21', 'nadirah', '2020-05-13', '4 beta', 5, 'helo, saya anis', 'dinasour2.png', '2025-12-01 08:16:48', 1, 0, 100, 0, 5);

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
(4, 'CIkgu', 'Farhan', '4 Bestari', 'Tahun 4-6', 'sasa', '', '2025-11-28 02:43:07');

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
  `user_level` varchar(20) NOT NULL DEFAULT 'pelajar'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_user`, `username`, `password_hash`, `role`, `created_at`, `user_level`) VALUES
(2, 'cikguDemo', '$2y$10$81L0RtHRTIKGzu8GiU4jqO0DSq0mUnZU7a.3beKQa9aH88mum2iQy', 'guru', '2025-11-28 02:21:28', 'pelajar'),
(3, 'muridDemo', '$2y$10$5xDqsisbXrjzhjZk99SBdu8SmDimPeq6ipz02Hr6QWqMSynvTtOti', 'pelajar', '2025-11-28 02:21:28', 'pelajar'),
(4, 'Faham', '$2y$10$QVK0ZXU3mPRKoSycXKvqy.zj2dV.QFhS.e4mQZvfqyPsddJN1RKBa', 'guru', '2025-11-28 02:43:07', 'pelajar'),
(5, 'anis21', '$2y$10$f7sYLlf0NeCCeT4lG/yfTObw65AwDy199JYMK6/evjs/Q66o42di2', 'pelajar', '2025-12-01 08:16:48', 'pelajar');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `student`
--
ALTER TABLE `student`
  ADD PRIMARY KEY (`id_user`);

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
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_user` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `student`
--
ALTER TABLE `student`
  ADD CONSTRAINT `fk_student_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;

--
-- Constraints for table `teacher`
--
ALTER TABLE `teacher`
  ADD CONSTRAINT `fk_teacher_user` FOREIGN KEY (`id_user`) REFERENCES `users` (`id_user`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
