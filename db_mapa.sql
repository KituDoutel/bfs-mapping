-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 28, 2026 at 01:55 AM
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
-- Database: `db_mapa`
--

-- --------------------------------------------------------

--
-- Table structure for table `lokasaun`
--

CREATE TABLE `lokasaun` (
  `id` int(11) NOT NULL,
  `naran` varchar(100) NOT NULL COMMENT 'Nama lokasi',
  `distansia` int(11) NOT NULL DEFAULT 0 COMMENT 'Jarak dalam meter',
  `kategoria` varchar(10) NOT NULL COMMENT 'Kategori interasaun: I, II, III, ... XII'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `lokasaun`
--

INSERT INTO `lokasaun` (`id`, `naran`, `distansia`, `kategoria`) VALUES
(1, 'A - Residensia Becora Centro', 0, 'I'),
(2, 'B - Perempatan Becora Centro', 150, 'I'),
(3, 'C - Jalan Utama Becora Centro', 300, 'I'),
(4, 'D - Simpang Becora Centro Timur', 480, 'I'),
(5, 'E - Pasar Becora Centro', 620, 'I'),
(6, 'F - Jalan Colmera', 800, 'I'),
(7, 'G - Simpang Colmera', 950, 'I'),
(8, 'H - Perempatan Colmera', 1100, 'I'),
(9, 'I - Jalan Kaikoli', 1280, 'I'),
(10, 'J - Simpang Kaikoli', 1450, 'I'),
(11, 'A - Residensia Becora Centro', 0, 'II'),
(12, 'B - Perempatan Becora Centro', 150, 'II'),
(13, 'C - Jalan Utama Becora Centro', 300, 'II'),
(14, 'E - Pasar Becora Centro', 620, 'II'),
(15, 'G - Simpang Colmera', 950, 'II'),
(16, 'I - Jalan Kaikoli', 1280, 'II'),
(17, 'L - Perempatan Mandarin', 1600, 'II'),
(18, 'R - Jalan Farol', 2100, 'II'),
(19, 'S - Simpang Farol', 2350, 'II'),
(20, 'U - Jalan Caicoli', 2700, 'II'),
(21, 'A - Residensia Becora Centro', 0, 'III'),
(22, 'B - Perempatan Becora Centro', 150, 'III'),
(23, 'D - Simpang Becora Centro Timur', 480, 'III'),
(24, 'F - Jalan Colmera', 800, 'III'),
(25, 'H - Perempatan Colmera', 1100, 'III'),
(26, 'J - Simpang Kaikoli', 1450, 'III'),
(27, 'M - Jalan Mandarin', 1750, 'III'),
(28, 'P - Perempatan UNTL', 2050, 'III'),
(29, 'T - Jalan Kampus', 2400, 'III'),
(30, 'V - Gerbang IPDC', 2800, 'III'),
(31, 'A - Residensia Becora Centro', 0, 'IV'),
(32, 'B - Perempatan Becora Centro', 150, 'IV'),
(33, 'C - Jalan Utama Becora Centro', 300, 'IV'),
(34, 'E - Pasar Becora Centro', 620, 'IV'),
(35, 'G - Simpang Colmera', 950, 'IV'),
(36, 'I - Jalan Kaikoli', 1280, 'IV'),
(37, 'L - Perempatan Mandarin', 1600, 'IV'),
(38, 'R - Jalan Farol', 2100, 'IV'),
(39, 'S - Simpang Farol', 2350, 'IV'),
(40, 'U - Caicoli', 2700, 'IV'),
(41, 'Y - Jalan Bidau', 3100, 'IV'),
(42, 'AA - Simpang Bidau', 3500, 'IV'),
(43, 'CC - Jalan Santa Cruz', 3950, 'IV'),
(44, 'EE - Perempatan Santa Cruz', 4400, 'IV'),
(45, 'DD - Jalan Motael', 4800, 'IV'),
(46, 'FF - Simpang Motael', 5200, 'IV'),
(47, 'JJ - Jalan Bairo Pite', 5700, 'IV'),
(48, 'PP - Simpang Bairo Pite', 6200, 'IV'),
(49, 'NN - Jalan Comoro', 6800, 'IV'),
(50, 'QQ - Simpang Comoro', 7400, 'IV'),
(51, 'RR - Kampus IPDC', 9952, 'IV'),
(52, 'A - Residensia Becora Centro', 0, 'V'),
(53, 'B - Perempatan Becora Centro', 150, 'V'),
(54, 'C - Jalan Utama Becora Centro', 300, 'V'),
(55, 'F - Jalan Colmera', 800, 'V'),
(56, 'I - Jalan Kaikoli', 1280, 'V'),
(57, 'M - Jalan Mandarin', 1750, 'V'),
(58, 'R - Jalan Farol', 2100, 'V'),
(59, 'U - Caicoli', 2700, 'V'),
(60, 'Y - Jalan Bidau', 3100, 'V'),
(61, 'CC - Jalan Santa Cruz', 3950, 'V'),
(62, 'FF - Simpang Motael', 5200, 'V'),
(63, 'RR - Kampus IPDC', 10500, 'V'),
(64, 'A - Residensia Becora Centro', 0, 'VI'),
(65, 'B - Perempatan Becora Centro', 150, 'VI'),
(66, 'D - Simpang Becora Centro Timur', 480, 'VI'),
(67, 'G - Simpang Colmera', 950, 'VI'),
(68, 'J - Simpang Kaikoli', 1450, 'VI'),
(69, 'L - Perempatan Mandarin', 1600, 'VI'),
(70, 'P - Perempatan UNTL', 2050, 'VI'),
(71, 'S - Simpang Farol', 2350, 'VI'),
(72, 'V - Gerbang IPDC', 2800, 'VI'),
(73, 'Z - Jalan Alternatif', 3300, 'VI'),
(74, 'BB - Simpang Alternatif', 3800, 'VI'),
(75, 'RR - Kampus IPDC', 11200, 'VI'),
(76, 'A - Residensia Becora Centro', 0, 'VII'),
(77, 'C - Jalan Utama Becora Centro', 300, 'VII'),
(78, 'E - Pasar Becora Centro', 620, 'VII'),
(79, 'H - Perempatan Colmera', 1100, 'VII'),
(80, 'K - Jalan Kaikoli Barat', 1380, 'VII'),
(81, 'N - Jalan Mandarin Timur', 1800, 'VII'),
(82, 'Q - Simpang UNTL', 2150, 'VII'),
(83, 'T - Jalan Kampus', 2400, 'VII'),
(84, 'W - Simpang Farol Barat', 2900, 'VII'),
(85, 'AA - Simpang Bidau', 3500, 'VII'),
(86, 'EE - Perempatan Santa Cruz', 4400, 'VII'),
(87, 'RR - Kampus IPDC', 11500, 'VII'),
(88, 'A - Residensia Becora Centro', 0, 'VIII'),
(89, 'B - Perempatan Becora Centro', 150, 'VIII'),
(90, 'E - Pasar Becora Centro', 620, 'VIII'),
(91, 'G - Simpang Colmera', 950, 'VIII'),
(92, 'L - Perempatan Mandarin', 1600, 'VIII'),
(93, 'R - Jalan Farol', 2100, 'VIII'),
(94, 'U - Caicoli', 2700, 'VIII'),
(95, 'AA - Simpang Bidau', 3500, 'VIII'),
(96, 'DD - Jalan Motael', 4800, 'VIII'),
(97, 'NN - Jalan Comoro', 6800, 'VIII'),
(98, 'RR - Kampus IPDC', 11800, 'VIII'),
(99, 'A - Residensia Becora Centro', 0, 'IX'),
(100, 'C - Jalan Utama Becora Centro', 300, 'IX'),
(101, 'F - Jalan Colmera', 800, 'IX'),
(102, 'I - Jalan Kaikoli', 1280, 'IX'),
(103, 'M - Jalan Mandarin', 1750, 'IX'),
(104, 'P - Perempatan UNTL', 2050, 'IX'),
(105, 'S - Simpang Farol', 2350, 'IX'),
(106, 'Y - Jalan Bidau', 3100, 'IX'),
(107, 'CC - Jalan Santa Cruz', 3950, 'IX'),
(108, 'JJ - Jalan Bairo Pite', 5700, 'IX'),
(109, 'QQ - Simpang Comoro', 7400, 'IX'),
(110, 'RR - Kampus IPDC', 12100, 'IX'),
(111, 'A - Residensia Becora Centro', 0, 'X'),
(112, 'B - Perempatan Becora Centro', 150, 'X'),
(113, 'D - Simpang Becora Centro Timur', 480, 'X'),
(114, 'F - Jalan Colmera', 800, 'X'),
(115, 'H - Perempatan Colmera', 1100, 'X'),
(116, 'J - Simpang Kaikoli', 1450, 'X'),
(117, 'N - Jalan Mandarin Timur', 1800, 'X'),
(118, 'Q - Simpang UNTL', 2150, 'X'),
(119, 'T - Jalan Kampus', 2400, 'X'),
(120, 'X - Jalan Farol Selatan', 2950, 'X'),
(121, 'BB - Simpang Alternatif', 3800, 'X'),
(122, 'RR - Kampus IPDC', 12400, 'X'),
(123, 'A - Residensia Becora Centro', 0, 'XI'),
(124, 'C - Jalan Utama Becora Centro', 300, 'XI'),
(125, 'E - Pasar Becora Centro', 620, 'XI'),
(126, 'G - Simpang Colmera', 950, 'XI'),
(127, 'I - Jalan Kaikoli', 1280, 'XI'),
(128, 'L - Perempatan Mandarin', 1600, 'XI'),
(129, 'R - Jalan Farol', 2100, 'XI'),
(130, 'S - Simpang Farol', 2350, 'XI'),
(131, 'U - Caicoli', 2700, 'XI'),
(132, 'AA - Simpang Bidau', 3500, 'XI'),
(133, 'FF - Simpang Motael', 5200, 'XI'),
(134, 'RR - Kampus IPDC', 12700, 'XI'),
(135, 'A - Residensia Becora Centro', 0, 'XII'),
(136, 'B - Perempatan Becora Centro', 150, 'XII'),
(137, 'D - Simpang Becora Centro Timur', 480, 'XII'),
(138, 'G - Simpang Colmera', 950, 'XII'),
(139, 'J - Simpang Kaikoli', 1450, 'XII'),
(140, 'M - Jalan Mandarin', 1750, 'XII'),
(141, 'P - Perempatan UNTL', 2050, 'XII'),
(142, 'T - Jalan Kampus', 2400, 'XII'),
(143, 'W - Simpang Farol Barat', 2900, 'XII'),
(144, 'CC - Jalan Santa Cruz', 3950, 'XII'),
(145, 'PP - Simpang Bairo Pite', 6200, 'XII'),
(146, 'RR - Kampus IPDC', 13000, 'XII');

-- --------------------------------------------------------

--
-- Table structure for table `mapa`
--

CREATE TABLE `mapa` (
  `id` int(11) NOT NULL,
  `interasaun_i` varchar(1000) DEFAULT NULL COMMENT 'Hasil interasaun 1',
  `interasaun_ii` varchar(1000) DEFAULT NULL COMMENT 'Hasil interasaun 2',
  `interasaun_iii` varchar(1000) DEFAULT NULL COMMENT 'Hasil interasaun 3',
  `interasaun_iv` varchar(1000) DEFAULT NULL COMMENT 'Hasil interasaun 4',
  `interasaun_v` varchar(1000) DEFAULT NULL COMMENT 'Hasil interasaun 5',
  `interasaun_vi` varchar(1000) DEFAULT NULL COMMENT 'Hasil interasaun 6',
  `interasaun_vii` varchar(1000) DEFAULT NULL COMMENT 'Hasil interasaun 7',
  `interasaun_viii` varchar(1000) DEFAULT NULL COMMENT 'Hasil interasaun 8',
  `interasaun_ix` varchar(1000) DEFAULT NULL COMMENT 'Hasil interasaun 9',
  `interasaun_x` varchar(1000) DEFAULT NULL COMMENT 'Hasil interasaun 10',
  `interasaun_xi` varchar(1000) DEFAULT NULL COMMENT 'Hasil interasaun 11',
  `interasaun_xii` varchar(1000) DEFAULT NULL COMMENT 'Hasil interasaun 12',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `lokasaun`
--
ALTER TABLE `lokasaun`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_kategoria` (`kategoria`);

--
-- Indexes for table `mapa`
--
ALTER TABLE `mapa`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `lokasaun`
--
ALTER TABLE `lokasaun`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=147;

--
-- AUTO_INCREMENT for table `mapa`
--
ALTER TABLE `mapa`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
