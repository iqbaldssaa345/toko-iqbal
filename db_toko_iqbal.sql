-- phpMyAdmin SQL Dump
-- version 4.9.1
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Apr 25, 2026 at 07:30 AM
-- Server version: 8.0.17
-- PHP Version: 7.3.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `db_toko_iqbal`
--

-- --------------------------------------------------------

--
-- Table structure for table `alamat`
--

CREATE TABLE `alamat` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `nama_penerima` varchar(100) DEFAULT NULL,
  `alamat` text,
  `kota` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `alamat`
--

INSERT INTO `alamat` (`id`, `user_id`, `nama_penerima`, `alamat`, `kota`) VALUES
(1, 3, 'Iqbal', 'Jl. Jakarta No 1', 'Jakarta'),
(4, 4, 'Iqbal', 'terate', 'jakarta'),
(6, 3, 'yusuf', 'cikini', 'jakarta utara'),
(8, 4, 'iqbal', 'PURI NIRWANA 1\r\n', 'bogor'),
(9, 4, 'rusdi', 'ciremai', 'kuningan'),
(10, 8, 'hendro', 'jalan ciremai', 'kota bogor');

-- --------------------------------------------------------

--
-- Table structure for table `detail_pesanan`
--

CREATE TABLE `detail_pesanan` (
  `id` int(11) NOT NULL,
  `pesanan_id` int(11) DEFAULT NULL,
  `produk_id` int(11) DEFAULT NULL,
  `jumlah` int(11) DEFAULT NULL,
  `subtotal` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `detail_pesanan`
--

INSERT INTO `detail_pesanan` (`id`, `pesanan_id`, `produk_id`, `jumlah`, `subtotal`) VALUES
(1, 1, 1, 1, 15000),
(7, 5, 1, 1, 26000),
(8, 27, 1, 3, 78000),
(9, 29, 1, 3, 78000);

-- --------------------------------------------------------

--
-- Table structure for table `ongkir`
--

CREATE TABLE `ongkir` (
  `id` int(11) NOT NULL,
  `nama_jasa` varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `biaya` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ongkir`
--

INSERT INTO `ongkir` (`id`, `nama_jasa`, `biaya`) VALUES
(1, 'GoFood', 5000),
(2, 'GrabFood', 6000),
(3, 'ShopeeFood', 14000),
(4, 'JNT ', 15000),
(5, 'jne', 12000),
(6, 'lalamove', 14000);

-- --------------------------------------------------------

--
-- Table structure for table `pembayaran`
--

CREATE TABLE `pembayaran` (
  `id` int(11) NOT NULL,
  `pesanan_id` int(11) DEFAULT NULL,
  `metode` varchar(50) DEFAULT NULL,
  `status` enum('pending','lunas') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pembayaran`
--

INSERT INTO `pembayaran` (`id`, `pesanan_id`, `metode`, `status`) VALUES
(1, 1, 'Transfer', 'lunas'),
(14, 5, 'COD', 'lunas'),
(15, 22, 'COD', 'lunas'),
(16, 23, 'E-Wallet', 'lunas'),
(17, 1, 'COD', 'lunas'),
(18, 25, 'COD', 'lunas'),
(19, 26, 'E-Wallet', 'lunas'),
(20, 28, 'COD', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `pesanan`
--

CREATE TABLE `pesanan` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `alamat_id` int(11) DEFAULT NULL,
  `ongkir_id` int(11) DEFAULT NULL,
  `tanggal` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  `total` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `pesanan`
--

INSERT INTO `pesanan` (`id`, `user_id`, `alamat_id`, `ongkir_id`, `tanggal`, `total`) VALUES
(1, 3, 1, 1, '2026-04-21 10:11:28', 23000),
(5, 4, 4, 4, '2026-04-24 13:13:25', 104000),
(6, 9, 1, 2, '0000-00-00 00:00:00', 88000),
(7, 9, 1, 2, '0000-00-00 00:00:00', 78000),
(8, 9, 4, 1, '0000-00-00 00:00:00', 26000),
(13, 4, 1, 3, '2026-04-25 00:51:41', 78000),
(22, 3, 1, 1, '2026-04-25 06:49:32', 29000),
(23, 3, 1, 6, '2026-04-25 06:52:43', 66000),
(24, 3, 6, 6, '2026-04-25 07:01:56', 66000),
(25, 4, 8, 4, '2026-04-25 07:12:11', 119000),
(26, 4, 9, 4, '2026-04-25 07:18:23', 93000),
(27, 3, NULL, NULL, '2026-04-25 07:26:29', NULL),
(28, 8, 10, 3, '2026-04-25 07:28:55', 92000),
(29, 8, NULL, NULL, '2026-04-25 07:29:17', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `produk`
--

CREATE TABLE `produk` (
  `id` int(11) NOT NULL,
  `nama` varchar(100) DEFAULT NULL,
  `deskripsi` text,
  `harga` int(11) DEFAULT NULL,
  `gambar` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `produk`
--

INSERT INTO `produk` (`id`, `nama`, `deskripsi`, `harga`, `gambar`) VALUES
(1, 'tempe goreng sambal', 'tempe goreng sambal enalk banget', 26000, 'sayur.jpg'),
(2, 'sayur tumis bakso telor', 'sayur tumis bakso telor\r\n', 12000, 'syr.jpg'),
(3, 'nasi liwet sambal', 'nasi liwet sambal\r\n', 18000, 'r.jpg'),
(4, 'nasi sambal enak banget liwet', 'enak banget dah ', 12000, 'r.jpg');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) DEFAULT NULL,
  `password` varchar(50) DEFAULT NULL,
  `role` enum('admin','petugas','pengunjung') DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `password`, `role`) VALUES
(1, 'admin', '123', 'admin'),
(2, 'petugas', '123', 'petugas'),
(3, 'iqbal', '123', 'pengunjung'),
(4, 'rusdi', '123', 'pengunjung'),
(5, 'rudi@gmail.com', '123', 'petugas'),
(6, 'pak yusuf', '123', 'pengunjung'),
(7, 'hendro', '123', 'admin'),
(8, 'hendro1', '123', 'pengunjung'),
(9, 'pak hendro', '123', 'pengunjung'),
(10, 'pak agus', '234', 'admin');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `alamat`
--
ALTER TABLE `alamat`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pesanan_id` (`pesanan_id`),
  ADD KEY `produk_id` (`produk_id`);

--
-- Indexes for table `ongkir`
--
ALTER TABLE `ongkir`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD PRIMARY KEY (`id`),
  ADD KEY `pesanan_id` (`pesanan_id`);

--
-- Indexes for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`),
  ADD KEY `alamat_id` (`alamat_id`),
  ADD KEY `ongkir_id` (`ongkir_id`);

--
-- Indexes for table `produk`
--
ALTER TABLE `produk`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `alamat`
--
ALTER TABLE `alamat`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `ongkir`
--
ALTER TABLE `ongkir`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `pembayaran`
--
ALTER TABLE `pembayaran`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `pesanan`
--
ALTER TABLE `pesanan`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `produk`
--
ALTER TABLE `produk`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `alamat`
--
ALTER TABLE `alamat`
  ADD CONSTRAINT `alamat_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);

--
-- Constraints for table `detail_pesanan`
--
ALTER TABLE `detail_pesanan`
  ADD CONSTRAINT `detail_pesanan_ibfk_1` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`),
  ADD CONSTRAINT `detail_pesanan_ibfk_2` FOREIGN KEY (`produk_id`) REFERENCES `produk` (`id`);

--
-- Constraints for table `pembayaran`
--
ALTER TABLE `pembayaran`
  ADD CONSTRAINT `pembayaran_ibfk_1` FOREIGN KEY (`pesanan_id`) REFERENCES `pesanan` (`id`);

--
-- Constraints for table `pesanan`
--
ALTER TABLE `pesanan`
  ADD CONSTRAINT `pesanan_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `pesanan_ibfk_2` FOREIGN KEY (`alamat_id`) REFERENCES `alamat` (`id`),
  ADD CONSTRAINT `pesanan_ibfk_3` FOREIGN KEY (`ongkir_id`) REFERENCES `ongkir` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
