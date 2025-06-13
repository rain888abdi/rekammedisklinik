-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 13, 2025 at 07:45 PM
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
-- Database: `rekam_medis_klinik`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `GetRekapPasienPerDokter` (IN `bulan_param` INT, IN `tahun_param` INT)   BEGIN
    SELECT
        D.nama_dokter,
        COUNT(K.id_konsultasi) AS total_pasien
    FROM
        Dokter AS D
    LEFT JOIN
        Konsultasi AS K ON D.id_dokter = K.id_dokter
        AND MONTH(K.tanggal_konsultasi) = bulan_param
        AND YEAR(K.tanggal_konsultasi) = tahun_param
    GROUP BY
        D.id_dokter, D.nama_dokter
    ORDER BY
        D.nama_dokter;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `detailkonsultasiobat`
--

CREATE TABLE `detailkonsultasiobat` (
  `id_detail_obat` int(11) NOT NULL,
  `id_konsultasi` int(11) NOT NULL,
  `id_obat` int(11) NOT NULL,
  `jumlah` int(11) NOT NULL,
  `instruksi_pemakaian` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `detailkonsultasiobat`
--

INSERT INTO `detailkonsultasiobat` (`id_detail_obat`, `id_konsultasi`, `id_obat`, `jumlah`, `instruksi_pemakaian`) VALUES
(20, 12, 4, 2, 'setelah makan'),
(21, 12, 4, 3, 'seblum makan'),
(22, 14, 4, 2, 'setelah makan'),
(23, 15, 5, 2, 'setelah makan'),
(24, 15, 4, 5, 'setelah makan');

-- --------------------------------------------------------

--
-- Table structure for table `diagnosa`
--

CREATE TABLE `diagnosa` (
  `id_diagnosa` int(11) NOT NULL,
  `nama_diagnosa` varchar(255) NOT NULL,
  `deskripsi_diagnosa` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `diagnosa`
--

INSERT INTO `diagnosa` (`id_diagnosa`, `nama_diagnosa`, `deskripsi_diagnosa`) VALUES
(6, 'batuk', 'batuk'),
(7, 'Demam', 'Panas tinggi');

-- --------------------------------------------------------

--
-- Table structure for table `dokter`
--

CREATE TABLE `dokter` (
  `id_dokter` int(11) NOT NULL,
  `nama_dokter` varchar(255) NOT NULL,
  `spesialisasi` varchar(100) DEFAULT NULL,
  `telepon` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dokter`
--

INSERT INTO `dokter` (`id_dokter`, `nama_dokter`, `spesialisasi`, `telepon`) VALUES
(8, 'ana', 'cinta', '089'),
(9, 'fadilah', 'jantung', '123');

-- --------------------------------------------------------

--
-- Table structure for table `jadwal`
--

CREATE TABLE `jadwal` (
  `id_jadwal` int(11) NOT NULL,
  `id_dokter` int(11) NOT NULL,
  `hari` enum('Senin','Selasa','Rabu','Kamis','Jumat','Sabtu','Minggu') NOT NULL,
  `jam_mulai` time NOT NULL,
  `jam_selesai` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `jadwal`
--

INSERT INTO `jadwal` (`id_jadwal`, `id_dokter`, `hari`, `jam_mulai`, `jam_selesai`) VALUES
(5, 8, 'Jumat', '08:30:00', '12:00:00'),
(6, 8, 'Senin', '01:11:00', '04:11:00'),
(7, 8, 'Selasa', '00:11:00', '04:11:00'),
(8, 8, 'Rabu', '02:11:00', '13:11:00'),
(9, 8, 'Kamis', '07:12:00', '04:12:00'),
(10, 8, 'Sabtu', '00:12:00', '04:12:00'),
(11, 8, 'Minggu', '02:12:00', '04:12:00'),
(12, 8, 'Jumat', '22:22:00', '23:26:00'),
(13, 9, 'Jumat', '22:22:00', '23:27:00');

-- --------------------------------------------------------

--
-- Table structure for table `konsultasi`
--

CREATE TABLE `konsultasi` (
  `id_konsultasi` int(11) NOT NULL,
  `id_pasien` int(11) NOT NULL,
  `id_dokter` int(11) NOT NULL,
  `tanggal_konsultasi` datetime DEFAULT current_timestamp(),
  `Deskripsi` text DEFAULT NULL,
  `id_diagnosa` int(11) DEFAULT NULL,
  `catatan_dokter` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `konsultasi`
--

INSERT INTO `konsultasi` (`id_konsultasi`, `id_pasien`, `id_dokter`, `tanggal_konsultasi`, `Deskripsi`, `id_diagnosa`, `catatan_dokter`) VALUES
(10, 14, 8, '2025-06-11 11:52:44', 'mnmn', 6, 'nkjhkjh'),
(12, 15, 8, '2025-06-12 20:58:14', 'Sakit panas', 7, 'Rajin minum air putih ya'),
(14, 15, 8, '2025-06-12 22:00:40', 'bjbj', NULL, 'jbsbxhbhjsx'),
(15, 15, 8, '2025-06-13 22:04:40', 'csssc', 6, 'ssdsd'),
(16, 14, 8, '2025-06-13 23:07:20', 'panas ya', 7, '');

--
-- Triggers `konsultasi`
--
DELIMITER $$
CREATE TRIGGER `after_delete_konsultasi` AFTER DELETE ON `konsultasi` FOR EACH ROW BEGIN  
  INSERT INTO rekammedis (id_konsultasi, id_pasien, id_dokter, tanggal_konsultasi,  id_diagnosa, catatan_dokter, aksi)
  VALUES (OLD.id_konsultasi, OLD.id_pasien, OLD.id_dokter, OLD.tanggal_konsultasi,  OLD.id_diagnosa, OLD.catatan_dokter, 'delete');  
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_insert_konsultasi` AFTER INSERT ON `konsultasi` FOR EACH ROW BEGIN  
  INSERT INTO rekammedis (id_konsultasi, id_pasien, id_dokter, tanggal_konsultasi,  id_diagnosa, catatan_dokter, aksi)
  VALUES (NEW.id_konsultasi, NEW.id_pasien, NEW.id_dokter, NEW.tanggal_konsultasi,  NEW.id_diagnosa, NEW.catatan_dokter, 'insert');  
END
$$
DELIMITER ;
DELIMITER $$
CREATE TRIGGER `after_update_konsultasi` AFTER UPDATE ON `konsultasi` FOR EACH ROW BEGIN  
  INSERT INTO rekammedis (id_konsultasi, id_pasien, id_dokter, tanggal_konsultasi,  id_diagnosa, catatan_dokter, aksi)
  VALUES (NEW.id_konsultasi, NEW.id_pasien, NEW.id_dokter, NEW.tanggal_konsultasi,  NEW.id_diagnosa, NEW.catatan_dokter, 'update');  
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `obat`
--

CREATE TABLE `obat` (
  `id_obat` int(11) NOT NULL,
  `nama_obat` varchar(255) NOT NULL,
  `kategori` enum('tablet','kapsul','sirup') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `obat`
--

INSERT INTO `obat` (`id_obat`, `nama_obat`, `kategori`) VALUES
(4, 'paracetamol', 'sirup'),
(5, 'ibu profen', 'kapsul');

-- --------------------------------------------------------

--
-- Table structure for table `pasien`
--

CREATE TABLE `pasien` (
  `id_pasien` int(11) NOT NULL,
  `nama_pasien` varchar(255) NOT NULL,
  `tanggal_lahir` date DEFAULT NULL,
  `jenis_kelamin` enum('Laki-laki','Perempuan') NOT NULL,
  `alamat` text DEFAULT NULL,
  `telepon` varchar(15) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `pasien`
--

INSERT INTO `pasien` (`id_pasien`, `nama_pasien`, `tanggal_lahir`, `jenis_kelamin`, `alamat`, `telepon`) VALUES
(14, 'dilah', '2025-06-11', 'Perempuan', 'jln jln', '083'),
(15, 'Fadilah', '2025-06-04', 'Perempuan', 'Jln. Kenangan', '0812345678');

-- --------------------------------------------------------

--
-- Table structure for table `rekammedis`
--

CREATE TABLE `rekammedis` (
  `id_rekam` int(11) NOT NULL,
  `id_konsultasi` int(11) NOT NULL,
  `id_pasien` int(11) DEFAULT NULL,
  `id_dokter` int(11) DEFAULT NULL,
  `tanggal_konsultasi` datetime DEFAULT NULL,
  `id_diagnosa` int(11) DEFAULT NULL,
  `catatan_dokter` text DEFAULT NULL,
  `aksi` enum('insert','update','delete') DEFAULT NULL,
  `waktu` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `rekammedis`
--

INSERT INTO `rekammedis` (`id_rekam`, `id_konsultasi`, `id_pasien`, `id_dokter`, `tanggal_konsultasi`, `id_diagnosa`, `catatan_dokter`, `aksi`, `waktu`) VALUES
(1, 11, 14, 8, '2025-06-11 12:01:23', NULL, 'njhjh', 'update', '2025-06-12 12:08:55'),
(2, 11, 14, 8, '2025-06-11 12:01:23', NULL, 'njhjho', 'update', '2025-06-12 12:09:01'),
(3, 11, 14, 8, '2025-06-11 12:01:23', NULL, 'njhjho', 'update', '2025-06-12 12:12:21'),
(4, 11, 14, 8, '2025-06-11 12:01:23', 6, 'njhjho', 'update', '2025-06-12 12:12:35'),
(5, 11, 14, 8, '2025-06-11 12:01:23', NULL, 'njhjho', 'update', '2025-06-12 12:12:43'),
(6, 11, 14, 8, '2025-06-11 12:01:23', NULL, 'njhjhoik', 'update', '2025-06-12 13:53:36'),
(7, 12, 15, 8, '2025-06-12 20:58:14', 7, 'Rajin minum air putih ya', 'insert', '2025-06-12 13:58:14'),
(8, 11, 14, 8, '2025-06-11 12:01:23', NULL, 'njhjhoik', 'delete', '2025-06-12 13:58:58'),
(9, 13, 15, 8, '2025-06-12 21:02:50', 6, 'jnjj', 'insert', '2025-06-12 14:02:50'),
(10, 13, 15, 8, '2025-06-12 21:02:50', 6, 'jnjj', 'delete', '2025-06-12 14:57:24'),
(11, 14, 15, 8, '2025-06-12 22:00:40', NULL, 'jbsbxhbhjsx', 'insert', '2025-06-12 15:00:40'),
(12, 15, 15, 8, '2025-06-13 22:04:40', 6, 'ssdsd', 'insert', '2025-06-13 15:04:40'),
(13, 10, 14, 8, '2025-06-11 11:52:44', 6, 'nkjhkjh', 'update', '2025-06-13 15:08:20'),
(14, 16, 14, 8, '2025-06-13 23:07:20', NULL, '', 'insert', '2025-06-13 16:07:20'),
(15, 16, 14, 8, '2025-06-13 23:07:20', 7, '', 'update', '2025-06-13 16:07:43'),
(16, 16, 14, 8, '2025-06-13 23:07:20', 7, '', 'update', '2025-06-13 17:42:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `detailkonsultasiobat`
--
ALTER TABLE `detailkonsultasiobat`
  ADD PRIMARY KEY (`id_detail_obat`),
  ADD KEY `id_konsultasi` (`id_konsultasi`),
  ADD KEY `id_obat` (`id_obat`);

--
-- Indexes for table `diagnosa`
--
ALTER TABLE `diagnosa`
  ADD PRIMARY KEY (`id_diagnosa`),
  ADD UNIQUE KEY `nama_diagnosa` (`nama_diagnosa`);

--
-- Indexes for table `dokter`
--
ALTER TABLE `dokter`
  ADD PRIMARY KEY (`id_dokter`);

--
-- Indexes for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD PRIMARY KEY (`id_jadwal`),
  ADD KEY `id_dokter` (`id_dokter`);

--
-- Indexes for table `konsultasi`
--
ALTER TABLE `konsultasi`
  ADD PRIMARY KEY (`id_konsultasi`),
  ADD KEY `id_pasien` (`id_pasien`),
  ADD KEY `id_dokter` (`id_dokter`),
  ADD KEY `id_diagnosa` (`id_diagnosa`);

--
-- Indexes for table `obat`
--
ALTER TABLE `obat`
  ADD PRIMARY KEY (`id_obat`),
  ADD UNIQUE KEY `nama_obat` (`nama_obat`);

--
-- Indexes for table `pasien`
--
ALTER TABLE `pasien`
  ADD PRIMARY KEY (`id_pasien`);

--
-- Indexes for table `rekammedis`
--
ALTER TABLE `rekammedis`
  ADD PRIMARY KEY (`id_rekam`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `detailkonsultasiobat`
--
ALTER TABLE `detailkonsultasiobat`
  MODIFY `id_detail_obat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `diagnosa`
--
ALTER TABLE `diagnosa`
  MODIFY `id_diagnosa` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `dokter`
--
ALTER TABLE `dokter`
  MODIFY `id_dokter` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `jadwal`
--
ALTER TABLE `jadwal`
  MODIFY `id_jadwal` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `konsultasi`
--
ALTER TABLE `konsultasi`
  MODIFY `id_konsultasi` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `obat`
--
ALTER TABLE `obat`
  MODIFY `id_obat` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `pasien`
--
ALTER TABLE `pasien`
  MODIFY `id_pasien` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `rekammedis`
--
ALTER TABLE `rekammedis`
  MODIFY `id_rekam` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `detailkonsultasiobat`
--
ALTER TABLE `detailkonsultasiobat`
  ADD CONSTRAINT `detailkonsultasiobat_ibfk_1` FOREIGN KEY (`id_konsultasi`) REFERENCES `konsultasi` (`id_konsultasi`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `detailkonsultasiobat_ibfk_2` FOREIGN KEY (`id_obat`) REFERENCES `obat` (`id_obat`) ON UPDATE CASCADE;

--
-- Constraints for table `jadwal`
--
ALTER TABLE `jadwal`
  ADD CONSTRAINT `jadwal_ibfk_1` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `konsultasi`
--
ALTER TABLE `konsultasi`
  ADD CONSTRAINT `konsultasi_ibfk_1` FOREIGN KEY (`id_pasien`) REFERENCES `pasien` (`id_pasien`) ON UPDATE CASCADE,
  ADD CONSTRAINT `konsultasi_ibfk_2` FOREIGN KEY (`id_dokter`) REFERENCES `dokter` (`id_dokter`) ON UPDATE CASCADE,
  ADD CONSTRAINT `konsultasi_ibfk_3` FOREIGN KEY (`id_diagnosa`) REFERENCES `diagnosa` (`id_diagnosa`) ON DELETE SET NULL ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
