-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 21, 2024 at 12:19 AM
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
-- Database: `spksaw`
--

-- --------------------------------------------------------

--
-- Table structure for table `Alternatif`
--

CREATE TABLE `Alternatif` (
  `id_alternatif` int(11) NOT NULL,
  `nama_alternatif` varchar(255) NOT NULL,
  `status_alternatif` enum('0','1') NOT NULL DEFAULT '1'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Alternatif`
--

INSERT INTO `Alternatif` (`id_alternatif`, `nama_alternatif`, `status_alternatif`) VALUES
(1, 'Puntadewa', '1');

-- --------------------------------------------------------

--
-- Table structure for table `Kriteria`
--

CREATE TABLE `Kriteria` (
  `id_kriteria` int(11) NOT NULL,
  `nama_kriteria` varchar(255) NOT NULL,
  `tipe_kriteria` enum('benefit','cost') DEFAULT NULL,
  `sub_kriteria` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Kriteria`
--

INSERT INTO `Kriteria` (`id_kriteria`, `nama_kriteria`, `tipe_kriteria`, `sub_kriteria`) VALUES
(1, 'Pendidikan', 'benefit', '0'),
(2, 'Rekam Jejak', NULL, '1');

-- --------------------------------------------------------

--
-- Table structure for table `Penilaian`
--

CREATE TABLE `Penilaian` (
  `id_penilaian` int(11) NOT NULL,
  `id_alternatif` int(11) DEFAULT NULL,
  `id_kriteria` int(11) DEFAULT NULL,
  `id_subkriteria` int(11) DEFAULT NULL,
  `nilai` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Rentang`
--

CREATE TABLE `Rentang` (
  `id_rentang` int(11) NOT NULL,
  `id_kriteria` int(11) DEFAULT NULL,
  `id_subkriteria` int(11) DEFAULT NULL,
  `jenis_penilaian` enum('1','2') NOT NULL,
  `uraian` varchar(255) DEFAULT NULL,
  `nilai_rentang` decimal(5,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `SubKriteria`
--

CREATE TABLE `SubKriteria` (
  `id_subkriteria` int(11) NOT NULL,
  `id_kriteria` int(11) DEFAULT NULL,
  `nama_subkriteria` varchar(255) NOT NULL,
  `tipe_subkriteria` enum('benefit','cost') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `SubKriteria`
--

INSERT INTO `SubKriteria` (`id_subkriteria`, `id_kriteria`, `nama_subkriteria`, `tipe_subkriteria`) VALUES
(9, 2, 'Pengalaman Pada Jabatan Administrator Publik', 'benefit'),
(10, 2, 'Riwayat Sanksi Disiplin', 'cost');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id_users` int(11) NOT NULL,
  `nama` varchar(50) NOT NULL,
  `username` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id_users`, `nama`, `username`, `password`) VALUES
(1, 'Ubaidilah AT', 'ate', 'at3/');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Alternatif`
--
ALTER TABLE `Alternatif`
  ADD PRIMARY KEY (`id_alternatif`);

--
-- Indexes for table `Kriteria`
--
ALTER TABLE `Kriteria`
  ADD PRIMARY KEY (`id_kriteria`);

--
-- Indexes for table `Penilaian`
--
ALTER TABLE `Penilaian`
  ADD PRIMARY KEY (`id_penilaian`),
  ADD KEY `id_alternatif` (`id_alternatif`),
  ADD KEY `id_kriteria` (`id_kriteria`),
  ADD KEY `id_subkriteria` (`id_subkriteria`);

--
-- Indexes for table `Rentang`
--
ALTER TABLE `Rentang`
  ADD PRIMARY KEY (`id_rentang`),
  ADD KEY `id_kriteria` (`id_kriteria`),
  ADD KEY `id_subkriteria` (`id_subkriteria`);

--
-- Indexes for table `SubKriteria`
--
ALTER TABLE `SubKriteria`
  ADD PRIMARY KEY (`id_subkriteria`),
  ADD KEY `id_kriteria` (`id_kriteria`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id_users`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Alternatif`
--
ALTER TABLE `Alternatif`
  MODIFY `id_alternatif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `Kriteria`
--
ALTER TABLE `Kriteria`
  MODIFY `id_kriteria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `Penilaian`
--
ALTER TABLE `Penilaian`
  MODIFY `id_penilaian` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Rentang`
--
ALTER TABLE `Rentang`
  MODIFY `id_rentang` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `SubKriteria`
--
ALTER TABLE `SubKriteria`
  MODIFY `id_subkriteria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_users` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Penilaian`
--
ALTER TABLE `Penilaian`
  ADD CONSTRAINT `penilaian_ibfk_1` FOREIGN KEY (`id_alternatif`) REFERENCES `Alternatif` (`id_alternatif`),
  ADD CONSTRAINT `penilaian_ibfk_2` FOREIGN KEY (`id_kriteria`) REFERENCES `Kriteria` (`id_kriteria`),
  ADD CONSTRAINT `penilaian_ibfk_3` FOREIGN KEY (`id_subkriteria`) REFERENCES `SubKriteria` (`id_subkriteria`);

--
-- Constraints for table `Rentang`
--
ALTER TABLE `Rentang`
  ADD CONSTRAINT `rentang_ibfk_1` FOREIGN KEY (`id_kriteria`) REFERENCES `Kriteria` (`id_kriteria`),
  ADD CONSTRAINT `rentang_ibfk_2` FOREIGN KEY (`id_subkriteria`) REFERENCES `SubKriteria` (`id_subkriteria`);

--
-- Constraints for table `SubKriteria`
--
ALTER TABLE `SubKriteria`
  ADD CONSTRAINT `subkriteria_ibfk_1` FOREIGN KEY (`id_kriteria`) REFERENCES `Kriteria` (`id_kriteria`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
