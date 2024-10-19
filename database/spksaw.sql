-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Oct 19, 2024 at 08:25 AM
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
(1, 'Ubaidilah Aminuddin Thoyieb', '1'),
(2, 'Rohana Murniati Furshotun', '1'),
(3, 'Meilin Budiarti', '1'),
(4, 'Muh. Akbar Hamid', '1'),
(5, 'Salsabila Naura Putri', '1'),
(6, 'Sugeng Dwi Cahyono', '1'),
(7, 'Agus Zulvani', '1');

-- --------------------------------------------------------

--
-- Table structure for table `Kriteria`
--

CREATE TABLE `Kriteria` (
  `id_kriteria` int(11) NOT NULL,
  `nama_kriteria` varchar(255) NOT NULL,
  `bobot_kriteria` decimal(5,2) DEFAULT NULL,
  `tipe_kriteria` enum('benefit','cost') DEFAULT NULL,
  `punyasub` enum('0','1') NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `Kriteria`
--

INSERT INTO `Kriteria` (`id_kriteria`, `nama_kriteria`, `bobot_kriteria`, `tipe_kriteria`, `punyasub`) VALUES
(6, 'Pendidikan', 0.25, 'benefit', '0'),
(9, 'Rekam Jejak', NULL, NULL, '1'),
(13, 'Nilai Ujian Kompetensi', 0.20, 'benefit', '0'),
(17, 'Kemampuan Komunikasi', 0.15, 'benefit', '0');

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

--
-- Dumping data for table `Penilaian`
--

INSERT INTO `Penilaian` (`id_penilaian`, `id_alternatif`, `id_kriteria`, `id_subkriteria`, `nilai`) VALUES
(1, 1, 6, NULL, 4.00),
(2, 1, 9, 9, 2.00),
(3, 1, 9, 10, 1.00),
(4, 1, 13, NULL, 80.00),
(5, 1, 17, NULL, 70.00),
(6, 2, 6, NULL, 1.00),
(7, 2, 9, 9, 1.00),
(8, 2, 9, 10, 1.00),
(9, 2, 13, NULL, 80.00),
(10, 2, 17, NULL, 90.00),
(11, 6, 6, NULL, 3.00),
(12, 6, 9, 9, 2.00),
(13, 6, 9, 10, 2.00),
(14, 6, 13, NULL, 80.00),
(15, 6, 17, NULL, 90.00),
(16, 3, 6, NULL, 1.00),
(17, 3, 9, 9, 1.00),
(18, 3, 9, 10, 1.00),
(19, 3, 13, NULL, 70.00),
(20, 3, 17, NULL, 75.00),
(21, 4, 6, NULL, 2.00),
(22, 4, 9, 9, 1.00),
(23, 4, 9, 10, 1.00),
(24, 4, 13, NULL, 90.00),
(25, 4, 17, NULL, 80.00),
(26, 5, 6, NULL, 2.00),
(27, 5, 9, 9, 1.00),
(28, 5, 9, 10, 1.00),
(29, 5, 13, NULL, 95.00),
(30, 5, 17, NULL, 86.00),
(31, 7, 6, NULL, 4.00),
(32, 7, 9, 9, 3.00),
(33, 7, 9, 10, 1.00),
(34, 7, 13, NULL, 100.00),
(35, 7, 17, NULL, 100.00);

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

--
-- Dumping data for table `Rentang`
--

INSERT INTO `Rentang` (`id_rentang`, `id_kriteria`, `id_subkriteria`, `jenis_penilaian`, `uraian`, `nilai_rentang`) VALUES
(1, 6, NULL, '1', 'Dibawah Strata-2', 1.00),
(2, 6, NULL, '1', 'Strata-2 Non-Linier', 2.00),
(3, 6, NULL, '1', 'Strata-2 Linier', 3.00),
(4, 6, NULL, '1', 'Diatas Strata-2', 4.00),
(5, 9, 9, '1', 'Kurang dari 2 Tahun', 1.00),
(6, 9, 9, '1', 'Lebih dari 2 Tahun Dalam Jabatan Yang Sama', 2.00),
(7, 9, 9, '1', 'Lebih dari 2 Tahun Pada Jabatan Yang Berbeda', 3.00),
(8, 9, 10, '1', 'Tidak Pernah Mendapat Sanksi Disiplin', 1.00),
(9, 9, 10, '1', 'Pernah Mendapat Sanksi Tertulis', 2.00),
(10, 9, 10, '1', 'Pernah Mendapat Sanksi Administratif', 3.00),
(11, 9, 10, '1', 'Pernah Mendapat Sanski Penurunan Jabatan', 4.00),
(12, 13, NULL, '2', NULL, NULL),
(13, 17, NULL, '2', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `SubKriteria`
--

CREATE TABLE `SubKriteria` (
  `id_subkriteria` int(11) NOT NULL,
  `id_kriteria` int(11) DEFAULT NULL,
  `nama_subkriteria` varchar(255) NOT NULL,
  `bobot_subkriteria` decimal(5,2) NOT NULL,
  `tipe_subkriteria` enum('benefit','cost') NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `SubKriteria`
--

INSERT INTO `SubKriteria` (`id_subkriteria`, `id_kriteria`, `nama_subkriteria`, `bobot_subkriteria`, `tipe_subkriteria`) VALUES
(9, 9, 'Pengalaman Pada Jabatan Administrator', 0.25, 'benefit'),
(10, 9, 'Riwayat Pelanggaran Disiplin', 0.15, 'cost');

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
(2, 'Administrator', 'admin', '0000');

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
  MODIFY `id_alternatif` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `Kriteria`
--
ALTER TABLE `Kriteria`
  MODIFY `id_kriteria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `Penilaian`
--
ALTER TABLE `Penilaian`
  MODIFY `id_penilaian` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=36;

--
-- AUTO_INCREMENT for table `Rentang`
--
ALTER TABLE `Rentang`
  MODIFY `id_rentang` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `SubKriteria`
--
ALTER TABLE `SubKriteria`
  MODIFY `id_subkriteria` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id_users` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

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
