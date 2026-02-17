-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 20, 2026 at 10:32 PM
-- Server version: 12.1.2-MariaDB
-- PHP Version: 8.5.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `mrbapp`
--

-- --------------------------------------------------------

--
-- Table structure for table `holiday`
--

CREATE TABLE `holiday` (
  `id` int(10) UNSIGNED NOT NULL COMMENT 'ID',
  `holiday_date` date NOT NULL COMMENT 'วันที่',
  `year` int(3) UNSIGNED DEFAULT NULL COMMENT 'ปี พ.ศ.',
  `name_th` varchar(255) NOT NULL COMMENT 'ชื่อวันหยุด',
  `name_en` varchar(255) DEFAULT NULL COMMENT 'ชื่อวันหยุด (EN)',
  `description` varchar(255) DEFAULT NULL COMMENT 'รายละเอียด',
  `holiday_type` varchar(50) DEFAULT 'public' COMMENT 'public, organization, special',
  `is_recurring` tinyint(1) DEFAULT 0 COMMENT 'เกิดซ้ำทุกปี',
  `is_active` int(3) UNSIGNED NOT NULL COMMENT 'สถานะ',
  `created_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'สร้างเมื่อ'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `holiday`
--

INSERT INTO `holiday` (`id`, `holiday_date`, `year`, `name_th`, `name_en`, `description`, `holiday_type`, `is_recurring`, `is_active`, `created_at`) VALUES
(1, '2025-01-01', 2025, 'วันขึ้นปีใหม่', 'New Year\'s Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(2, '2025-02-12', 2025, 'วันมาฆบูชา', 'Makha Bucha Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(3, '2025-04-06', 2025, 'วันจักรี', 'Chakri Memorial Day', NULL, '1', 1, 0, '2026-01-20 11:52:02'),
(4, '2025-04-13', 2025, 'วันสงกรานต์', 'Songkran Festival', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(5, '2025-04-14', 2025, 'วันสงกรานต์', 'Songkran Festival', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(6, '2025-04-15', 2025, 'วันสงกรานต์', 'Songkran Festival', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(7, '2025-05-01', 2025, 'วันแรงงานแห่งชาติ', 'National Labour Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(8, '2025-05-04', 2025, 'วันฉัตรมงคล', 'Coronation Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(9, '2025-05-11', 2025, 'วันวิสาขบูชา', 'Visakha Bucha Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(10, '2025-05-12', 2025, 'วันชดเชยวันวิสาขบูชา', 'Visakha Bucha Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(11, '2025-06-03', 2025, 'วันเฉลิมพระชนมพรรษา สมเด็จพระนางเจ้าฯ พระบรมราชินี', 'Queen\'s Birthday', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(12, '2025-07-10', 2025, 'วันอาสาฬหบูชา', 'Asalha Puja Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(13, '2025-07-11', 2025, 'วันเข้าพรรษา', 'Buddhist Lent Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(14, '2025-07-28', 2025, 'วันเฉลิมพระชนมพรรษา พระบาทสมเด็จพระเจ้าอยู่หัว', 'King\'s Birthday', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(15, '2025-08-12', 2025, 'วันเฉลิมพระชนมพรรษา สมเด็จพระนางเจ้าสิริกิติ์ฯ / วันแม่แห่งชาติ', 'Queen Mother\'s Birthday', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(16, '2025-10-13', 2025, 'วันคล้ายวันสวรรคต พระบาทสมเด็จพระบรมชนกาธิเบศร', 'King Bhumibol Memorial Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(17, '2025-10-23', 2025, 'วันปิยมหาราช', 'Chulalongkorn Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(18, '2025-12-05', 2025, 'วันคล้ายวันพระราชสมภพ รัชกาลที่ 9 / วันพ่อแห่งชาติ', 'King Bhumibol\'s Birthday', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(19, '2025-12-10', 2025, 'วันรัฐธรรมนูญ', 'Constitution Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(20, '2025-12-31', 2025, 'วันสิ้นปี', 'New Year\'s Eve', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(21, '2026-01-01', 2026, 'วันขึ้นปีใหม่', 'New Year\'s Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(22, '2026-01-02', 2026, 'วันชดเชยวันขึ้นปีใหม่', 'New Year\'s Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(23, '2026-03-01', 2026, 'วันมาฆบูชา', 'Makha Bucha Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(24, '2026-03-02', 2026, 'วันชดเชยวันมาฆบูชา', 'Makha Bucha Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(25, '2026-04-06', 2026, 'วันจักรี', 'Chakri Memorial Day', NULL, '1', NULL, 0, '2026-01-20 11:52:02'),
(26, '2026-04-13', 2026, 'วันสงกรานต์', 'Songkran Festival', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(27, '2026-04-14', 2026, 'วันสงกรานต์', 'Songkran Festival', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(28, '2026-04-15', 2026, 'วันสงกรานต์', 'Songkran Festival', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(29, '2026-05-01', 2026, 'วันแรงงานแห่งชาติ', 'National Labour Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(30, '2026-05-04', 2026, 'วันฉัตรมงคล', 'Coronation Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(31, '2026-05-31', 2026, 'วันวิสาขบูชา', 'Visakha Bucha Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(32, '2026-06-01', 2026, 'วันชดเชยวันวิสาขบูชา', 'Asalha Puja Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(33, '2026-06-03', 2026, 'วันเฉลิมพระชนมพรรษา สมเด็จพระนางเจ้าฯ พระบรมราชินี', 'Queen\'s Birthday', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(34, '2026-06-28', 2026, 'วันอาสาฬหบูชา', 'Asalha Puja Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(35, '2026-06-29', 2026, 'วันเข้าพรรษา', 'Buddhist Lent Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(36, '2026-07-28', 2026, 'วันเฉลิมพระชนมพรรษา พระบาทสมเด็จพระเจ้าอยู่หัว', 'King\'s Birthday', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(37, '2026-08-12', 2026, 'วันเฉลิมพระชนมพรรษา สมเด็จพระนางเจ้าสิริกิติ์ฯ / วันแม่แห่งชาติ', 'Queen Mother\'s Birthday', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(38, '2026-10-13', 2026, 'วันคล้ายวันสวรรคต พระบาทสมเด็จพระบรมชนกาธิเบศร', 'King Bhumibol Memorial Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(39, '2026-10-23', 2026, 'วันปิยมหาราช', 'Chulalongkorn Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(40, '2026-12-05', 2026, 'วันคล้ายวันพระราชสมภพ รัชกาลที่ 9 / วันพ่อแห่งชาติ', 'King Bhumibol\'s Birthday', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(41, '2026-12-10', 2026, 'วันรัฐธรรมนูญ', 'Constitution Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(42, '2026-12-31', 2026, 'วันสิ้นปี', 'New Year\'s Eve', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(43, '2027-01-01', 2027, 'วันขึ้นปีใหม่', 'New Year\'s Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(44, '2027-02-18', 2027, 'วันมาฆบูชา', 'Makha Bucha Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(45, '2027-04-06', 2027, 'วันจักรี', 'Chakri Memorial Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(46, '2027-04-13', 2027, 'วันสงกรานต์', 'Songkran Festival', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(47, '2027-04-14', 2027, 'วันสงกรานต์', 'Songkran Festival', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(48, '2027-04-15', 2027, 'วันสงกรานต์', 'Songkran Festival', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(49, '2027-05-01', 2027, 'วันแรงงานแห่งชาติ', 'National Labour Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(50, '2027-05-04', 2027, 'วันฉัตรมงคล', 'Coronation Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(51, '2027-05-20', 2027, 'วันวิสาขบูชา', 'Visakha Bucha Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(52, '2027-06-03', 2027, 'วันเฉลิมพระชนมพรรษา สมเด็จพระนางเจ้าฯ พระบรมราชินี', 'Queen\'s Birthday', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(53, '2027-07-18', 2027, 'วันอาสาฬหบูชา', 'Asalha Puja Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(54, '2027-07-19', 2027, 'วันเข้าพรรษา', 'Buddhist Lent Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(55, '2027-07-28', 2027, 'วันเฉลิมพระชนมพรรษา พระบาทสมเด็จพระเจ้าอยู่หัว', 'King\'s Birthday', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(56, '2027-08-12', 2027, 'วันเฉลิมพระชนมพรรษา สมเด็จพระนางเจ้าสิริกิติ์ฯ / วันแม่แห่งชาติ', 'Queen Mother\'s Birthday', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(57, '2027-10-13', 2027, 'วันคล้ายวันสวรรคต พระบาทสมเด็จพระบรมชนกาธิเบศร', 'King Bhumibol Memorial Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(58, '2027-10-23', 2027, 'วันปิยมหาราช', 'Chulalongkorn Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(59, '2027-12-05', 2027, 'วันคล้ายวันพระราชสมภพ รัชกาลที่ 9 / วันพ่อแห่งชาติ', 'King Bhumibol\'s Birthday', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(60, '2027-12-06', 2027, 'วันชดเชยวันพ่อแห่งชาติ', 'King Bhumibol\'s Birthday', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(61, '2027-12-10', 2027, 'วันรัฐธรรมนูญ', 'Constitution Day', NULL, '1', 0, 0, '2026-01-20 11:52:02'),
(62, '2027-12-31', 2027, 'วันสิ้นปี', 'New Year\'s Eve', NULL, '1', 0, 0, '2026-01-20 11:52:02');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `holiday`
--
ALTER TABLE `holiday`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_holiday_date` (`holiday_date`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `holiday`
--
ALTER TABLE `holiday`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=63;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
