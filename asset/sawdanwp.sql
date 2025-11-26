-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 26, 2025 at 10:58 AM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `sawdanwp`
--

-- --------------------------------------------------------

--
-- Table structure for table `alternatives`
--

CREATE TABLE `alternatives` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `value1` float DEFAULT '1',
  `value2` float DEFAULT '1',
  `value3` float DEFAULT '1',
  `value4` float DEFAULT '1',
  `value5` float DEFAULT '1',
  `value6` float DEFAULT '1',
  `value7` float DEFAULT '1',
  `value8` float DEFAULT '1',
  `value9` float DEFAULT '1',
  `value10` float DEFAULT '1',
  `value11` float DEFAULT '1',
  `value12` float DEFAULT '1',
  `value13` float DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `alternatives`
--

INSERT INTO `alternatives` (`id`, `name`, `value1`, `value2`, `value3`, `value4`, `value5`, `value6`, `value7`, `value8`, `value9`, `value10`, `value11`, `value12`, `value13`) VALUES
(1, 'Toto Iswanto', 60, 95, 20, 60, 79, 20, 10, 30, 80, 70, 60, 90, 1),
(2, 'Budiman Jaya', 70, 90, 10, 10, 67, 70, 68, 68, 98, 39, 17, 80, 1),
(3, 'Aditya Parawansyah', 40, 97, 80, 80, 50, 67, 83, 78, 39, 67, 87, 88, 90);

-- --------------------------------------------------------

--
-- Table structure for table `criteria`
--

CREATE TABLE `criteria` (
  `id` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `weight` float NOT NULL,
  `type` enum('benefit','cost') NOT NULL,
  `status` enum('individu','normal','kantor') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `criteria`
--

INSERT INTO `criteria` (`id`, `name`, `weight`, `type`, `status`) VALUES
(1, 'Produktivitas', 5, 'benefit', 'individu'),
(2, 'Morning Briefing', 5, 'benefit', 'individu'),
(3, 'TPP', 5, 'benefit', 'individu'),
(4, 'Skor JakOne', 10, 'benefit', 'individu'),
(5, 'Tab Konven', 22, 'benefit', 'individu'),
(6, 'Giro Konven', 2, 'benefit', 'individu'),
(7, 'Depo Konven', 20, 'benefit', 'individu'),
(8, 'Tab DBLM', 15, 'benefit', 'individu'),
(9, 'Depo DBLM', 5, 'benefit', 'individu'),
(10, 'KMG Konven', 5, 'benefit', 'individu'),
(11, 'Referal JakOne', 2, 'benefit', 'individu'),
(12, 'e-Channel', 4, 'benefit', 'individu'),
(13, 'Kredit', 3, 'benefit', 'individu');

-- --------------------------------------------------------

--
-- Table structure for table `hasil`
--

CREATE TABLE `hasil` (
  `id_hasil` int NOT NULL,
  `id` varchar(100) NOT NULL,
  `rank` varchar(255) DEFAULT NULL,
  `name` varchar(255) NOT NULL,
  `s_value` decimal(10,2) DEFAULT NULL,
  `v_value` decimal(10,2) DEFAULT NULL,
  `final_value` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `hasil`
--

INSERT INTO `hasil` (`id_hasil`, `id`, `rank`, `name`, `s_value`, `v_value`, `final_value`) VALUES
(1, '2', '2', 'Budiman Jaya', '49.62', '0.32', '0.73'),
(2, '1', '3', 'Toto Iswanto', '38.33', '0.25', '0.61'),
(3, '3', '1', 'Aditya Parawansyah', '67.03', '0.43', '0.86');

-- --------------------------------------------------------

--
-- Table structure for table `master_data`
--

CREATE TABLE `master_data` (
  `id_master` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `final_value` varchar(100) NOT NULL,
  `rank` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rangking`
--

CREATE TABLE `rangking` (
  `id_rank` int NOT NULL,
  `sm` int NOT NULL,
  `m` int NOT NULL,
  `b` int NOT NULL,
  `cb` int NOT NULL,
  `ck` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `rangking`
--

INSERT INTO `rangking` (`id_rank`, `sm`, `m`, `b`, `cb`, `ck`) VALUES
(1, 6, 8, 8, 8, 10);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_users` int NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `role` enum('admin','superAdmin','karyawan') CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `nama` varchar(100) NOT NULL,
  `nik` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_users`, `username`, `password`, `role`, `nama`, `nik`) VALUES
(1, 'Anjas90', '$2y$10$MX9jpleY4IWVZYnmL7ihoeth4twN3pUB9ChZDqQpI5QNxacDiZPey', 'superAdmin', 'Anjas Kosasih', '918320912831'),
(2, 'Tois23', '$2y$10$MX9jpleY4IWVZYnmL7ihoeth4twN3pUB9ChZDqQpI5QNxacDiZPey', 'admin', 'Toto Iswanto', '882342731932'),
(3, 'jaya09', '$2y$10$aZ1uWrcv682Yby77YN8WD.wuhaYvxDsjiyAMICREohDVK06/WSwaC', 'karyawan', 'Budiman Jaya', '8237428379123'),
(4, 'Aditya_10', '$2y$10$ZNBDw6SOR6fYEz3O.6cbNuYjlloiyJvn8XY2e1/ae5OvKIA7x7lwi', 'karyawan', 'Aditya Parawansyah', '87139812398293'),
(5, 'Ivana789', '$2y$10$hqtkMisSfDJ3nwJ9QZjBde51vbz4.gxL9luz9TbD36eZbcGJcHao.', 'karyawan', 'Ivana Mayada', '87447734902301');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alternatives`
--
ALTER TABLE `alternatives`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `criteria`
--
ALTER TABLE `criteria`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `hasil`
--
ALTER TABLE `hasil`
  ADD PRIMARY KEY (`id_hasil`);

--
-- Indexes for table `master_data`
--
ALTER TABLE `master_data`
  ADD PRIMARY KEY (`id_master`);

--
-- Indexes for table `rangking`
--
ALTER TABLE `rangking`
  ADD PRIMARY KEY (`id_rank`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_users`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alternatives`
--
ALTER TABLE `alternatives`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `criteria`
--
ALTER TABLE `criteria`
  MODIFY `id` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `hasil`
--
ALTER TABLE `hasil`
  MODIFY `id_hasil` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `master_data`
--
ALTER TABLE `master_data`
  MODIFY `id_master` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rangking`
--
ALTER TABLE `rangking`
  MODIFY `id_rank` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_users` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
