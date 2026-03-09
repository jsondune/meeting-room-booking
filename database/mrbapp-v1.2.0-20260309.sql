-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: localhost
-- Generation Time: Mar 09, 2026 at 06:52 AM
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
-- Database: `car_reservation`
--

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` int(11) UNSIGNED NOT NULL COMMENT 'ID',
  `user_id` int(11) DEFAULT NULL COMMENT 'FK: ผู้ดำเนินการ',
  `action_type` varchar(50) NOT NULL COMMENT 'การกระทำ: create, update, delete, login, logout, etc.',
  `action_category` varchar(50) DEFAULT NULL COMMENT 'Category',
  `model_class` varchar(100) DEFAULT NULL COMMENT 'Model Class ID',
  `model_record_id` int(100) UNSIGNED DEFAULT NULL COMMENT 'Model/Entity ID',
  `entity_name` varchar(100) DEFAULT NULL COMMENT 'Entity Name',
  `old_values` text DEFAULT NULL COMMENT 'Old Values',
  `new_values` text DEFAULT NULL COMMENT 'New Values',
  `changes` text DEFAULT NULL COMMENT 'Changes',
  `ip_address` varchar(45) DEFAULT NULL COMMENT 'IP Address',
  `user_agent` varchar(500) DEFAULT NULL COMMENT 'Browser/User Agent',
  `session_id` varchar(100) DEFAULT NULL COMMENT 'Session ID',
  `request_url` varchar(500) DEFAULT NULL COMMENT 'URL ที่เรียก',
  `request_method` varchar(10) DEFAULT NULL COMMENT 'HTTP method',
  `description` text DEFAULT NULL COMMENT 'คำอธิบาย',
  `extra_data` varchar(100) DEFAULT NULL COMMENT 'ข้อมูลเพิ่มเติม',
  `severity` varchar(100) DEFAULT NULL COMMENT 'Severity 0=info,1=warning, 2=error',
  `created_at` datetime DEFAULT NULL COMMENT 'สร้างเมื่อ',
  `entity_type` varchar(100) DEFAULT NULL,
  `entity_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id`, `user_id`, `action_type`, `action_category`, `model_class`, `model_record_id`, `entity_name`, `old_values`, `new_values`, `changes`, `ip_address`, `user_agent`, `session_id`, `request_url`, `request_method`, `description`, `extra_data`, `severity`, `created_at`, `entity_type`, `entity_id`) VALUES
(1, 212, 'user', 'system', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'dcf8dad0613f53a13f03d1bebfc37cb2', 'http://backend.crs.test/login', 'POST', 'user: รายการ', NULL, '0', '2026-02-26 20:34:35', NULL, NULL),
(2, 212, 'user', 'system', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'dcf8dad0613f53a13f03d1bebfc37cb2', 'http://backend.crs.test/logout', 'POST', 'user: รายการ', NULL, '0', '2026-02-26 22:59:57', NULL, NULL),
(3, 212, 'user', 'system', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '0ba16a2fb6ff6596a0f2764f3eab656d', 'http://backend.crs.test/login', 'POST', 'user: รายการ', NULL, '0', '2026-02-26 23:00:00', NULL, NULL),
(4, 212, 'user', 'system', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '0ba16a2fb6ff6596a0f2764f3eab656d', 'http://backend.crs.test/logout', 'POST', 'user: รายการ', NULL, '0', '2026-02-27 03:31:03', NULL, NULL),
(5, 212, 'user', 'system', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '7511b23e27b34b37346621fe78928580', 'http://backend.crs.test/login', 'POST', 'user: รายการ', NULL, '0', '2026-02-27 09:12:44', NULL, NULL),
(6, 212, 'user', 'system', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'd57cd39ac42004a175fb561f9e1bdd16', 'http://backend.crs.test/login', 'POST', 'user: รายการ', NULL, '0', '2026-02-27 22:40:28', NULL, NULL),
(7, 212, 'user', 'system', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '14e392e910c4b709edd57424a95d4ca7', 'http://backend.crs.test/site/profile', 'POST', 'user: รายการ', NULL, '0', '2026-03-01 20:31:03', NULL, NULL),
(8, 212, 'user', 'system', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '14e392e910c4b709edd57424a95d4ca7', 'http://backend.crs.test/site/profile', 'POST', 'user: รายการ', NULL, '0', '2026-03-01 20:56:35', NULL, NULL),
(9, 212, 'user', 'system', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '14e392e910c4b709edd57424a95d4ca7', 'http://backend.crs.test/site/profile', 'POST', 'user: รายการ', NULL, '0', '2026-03-01 20:57:23', NULL, NULL),
(10, 212, 'user', 'system', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '643fa7f4371ec341c2e6cc11d370f49c', 'http://backend.crs.test/site/profile', 'POST', 'user: รายการ', NULL, '0', '2026-03-02 11:04:31', NULL, NULL),
(11, 212, 'user', 'system', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '62f981fc9480b929392883b762349e6e', 'http://backend.crs.test/logout', 'POST', 'user: รายการ', NULL, '0', '2026-03-09 00:14:42', NULL, NULL),
(12, 212, 'user', 'system', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '96be6d38f7d84fb04f4d4f21649b187c', 'http://backend.crs.test/login', 'POST', 'user: รายการ', NULL, '0', '2026-03-09 00:16:27', NULL, NULL),
(13, 212, 'user', 'system', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', '96be6d38f7d84fb04f4d4f21649b187c', 'http://backend.crs.test/logout', 'POST', 'user: รายการ', NULL, '0', '2026-03-09 00:28:11', NULL, NULL),
(14, 212, 'user', 'system', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'c3d62e39ce223cd700438a347d7625e1', 'http://backend.crs.test/login', 'POST', 'user: รายการ', NULL, '0', '2026-03-09 00:29:27', NULL, NULL),
(15, 212, 'user', 'system', NULL, NULL, NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', 'c3d62e39ce223cd700438a347d7625e1', 'http://backend.crs.test/logout', 'POST', 'user: รายการ', NULL, '0', '2026-03-09 00:32:10', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `auth_assignment`
--

CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `created_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `auth_assignment`
--

INSERT INTO `auth_assignment` (`item_name`, `user_id`, `created_at`) VALUES
('admin', '212', 1772136722);

-- --------------------------------------------------------

--
-- Table structure for table `auth_item`
--

CREATE TABLE `auth_item` (
  `name` varchar(64) NOT NULL,
  `type` smallint(6) NOT NULL,
  `description` text DEFAULT NULL,
  `rule_name` varchar(64) DEFAULT NULL,
  `data` blob DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `auth_item`
--

INSERT INTO `auth_item` (`name`, `type`, `description`, `rule_name`, `data`, `created_at`, `updated_at`) VALUES
('admin', 1, 'ผู้ดูแลระบบ - จัดการข้อมูลหลักทั้งหมด', NULL, NULL, 1772121584, 1772121584),
('approveReservation', 2, 'อนุมัติ/ปฏิเสธการจอง', NULL, NULL, 1772121584, 1772121584),
('cancelReservation', 2, 'ยกเลิกการจอง', NULL, NULL, 1772121584, 1772121584),
('createDriver', 2, 'เพิ่มพนักงานขับรถ', NULL, NULL, 1772121584, 1772121584),
('createReservation', 2, 'สร้างการจอง', NULL, NULL, 1772121584, 1772121584),
('createVehicle', 2, 'เพิ่มรถยนต์', NULL, NULL, 1772121584, 1772121584),
('deleteDriver', 2, 'ลบพนักงานขับรถ', NULL, NULL, 1772121584, 1772121584),
('deleteReservation', 2, 'ลบการจอง', NULL, NULL, 1772121584, 1772121584),
('deleteVehicle', 2, 'ลบรถยนต์', NULL, NULL, 1772121584, 1772121584),
('exportData', 2, 'ส่งออกข้อมูล', NULL, NULL, 1772121584, 1772121584),
('manageDriver', 2, 'จัดการพนักงานขับรถทั้งหมด', NULL, NULL, 1772121584, 1772121584),
('manager', 1, 'ผู้จัดการ - อนุมัติการจองและดูรายงาน', NULL, NULL, 1772121584, 1772121584),
('manageRbac', 2, 'จัดการสิทธิ์ผู้ใช้ (RBAC)', NULL, NULL, 1772121584, 1772121584),
('manageSettings', 2, 'จัดการการตั้งค่าระบบ', NULL, NULL, 1772121584, 1772121584),
('manageTrip', 2, 'จัดการการเดินทาง (เริ่ม/สิ้นสุด)', NULL, NULL, 1772121584, 1772121584),
('manageUsers', 2, 'จัดการผู้ใช้งาน', NULL, NULL, 1772121584, 1772121584),
('manageVehicle', 2, 'จัดการรถยนต์ทั้งหมด', NULL, NULL, 1772121584, 1772121584),
('rateReservation', 2, 'ให้คะแนนการเดินทาง', NULL, NULL, 1772121584, 1772121584),
('superAdmin', 1, 'ผู้ดูแลระบบสูงสุด - สิทธิ์ทั้งหมด', NULL, NULL, 1772121584, 1772121584),
('updateDriver', 2, 'แก้ไขข้อมูลพนักงานขับรถ', NULL, NULL, 1772121584, 1772121584),
('updateReservation', 2, 'แก้ไขการจอง', NULL, NULL, 1772121584, 1772121584),
('updateVehicle', 2, 'แก้ไขข้อมูลรถยนต์', NULL, NULL, 1772121584, 1772121584),
('user', 1, 'ผู้ใช้ทั่วไป - สร้างและจัดการการจองของตัวเอง', NULL, NULL, 1772121584, 1772121584),
('viewAllReservations', 2, 'ดูการจองทั้งหมด (ทุกหน่วยงาน)', NULL, NULL, 1772121584, 1772121584),
('viewAuditLog', 2, 'ดูประวัติการใช้งาน', NULL, NULL, 1772121584, 1772121584),
('viewDashboard', 2, 'ดูแดชบอร์ด', NULL, NULL, 1772121584, 1772121584),
('viewDriver', 2, 'ดูข้อมูลพนักงานขับรถ', NULL, NULL, 1772121584, 1772121584),
('viewer', 1, 'ผู้ชม - ดูข้อมูลได้อย่างเดียว', NULL, NULL, 1772121584, 1772121584),
('viewReservation', 2, 'ดูข้อมูลการจอง', NULL, NULL, 1772121584, 1772121584),
('viewStatistics', 2, 'ดูสถิติและรายงาน', NULL, NULL, 1772121584, 1772121584),
('viewVehicle', 2, 'ดูข้อมูลรถยนต์', NULL, NULL, 1772121584, 1772121584);

-- --------------------------------------------------------

--
-- Table structure for table `auth_item_child`
--

CREATE TABLE `auth_item_child` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

--
-- Dumping data for table `auth_item_child`
--

INSERT INTO `auth_item_child` (`parent`, `child`) VALUES
('superAdmin', 'admin'),
('manager', 'approveReservation'),
('user', 'cancelReservation'),
('admin', 'createDriver'),
('user', 'createReservation'),
('admin', 'createVehicle'),
('admin', 'deleteDriver'),
('admin', 'deleteReservation'),
('admin', 'deleteVehicle'),
('manager', 'exportData'),
('admin', 'manageDriver'),
('admin', 'manager'),
('superAdmin', 'manageRbac'),
('superAdmin', 'manageSettings'),
('manager', 'manageTrip'),
('superAdmin', 'manageUsers'),
('admin', 'manageVehicle'),
('user', 'rateReservation'),
('admin', 'updateDriver'),
('user', 'updateReservation'),
('admin', 'updateVehicle'),
('manager', 'user'),
('manager', 'viewAllReservations'),
('admin', 'viewAuditLog'),
('viewer', 'viewDashboard'),
('viewer', 'viewDriver'),
('user', 'viewer'),
('viewer', 'viewReservation'),
('manager', 'viewStatistics'),
('viewer', 'viewVehicle');

-- --------------------------------------------------------

--
-- Table structure for table `auth_rule`
--

CREATE TABLE `auth_rule` (
  `name` varchar(64) NOT NULL,
  `data` blob DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb3 COLLATE=utf8mb3_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `budget_category`
--

CREATE TABLE `budget_category` (
  `id` int(11) NOT NULL,
  `category_code` varchar(20) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `category_description` text DEFAULT NULL,
  `category_type` varchar(50) DEFAULT 'operating',
  `category_status` varchar(20) DEFAULT 'active',
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `car_driver`
--

CREATE TABLE `car_driver` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `driver_code` varchar(20) NOT NULL COMMENT 'รหัสพนักงาน',
  `employee_code` varchar(50) DEFAULT NULL COMMENT 'รหัสบุคลากร',
  `id_card_number` varchar(20) DEFAULT NULL COMMENT 'เลขบัตรประชาชน',
  `prefix_name` varchar(20) DEFAULT NULL,
  `first_name` varchar(100) DEFAULT NULL COMMENT 'ชื่อ',
  `last_name` varchar(100) DEFAULT NULL COMMENT 'นามสกุล',
  `first_name_en` varchar(100) DEFAULT NULL COMMENT 'ชื่อ (EN)',
  `last_name_en` varchar(100) DEFAULT NULL COMMENT 'นามสกุล (EN)',
  `nickname` varchar(50) DEFAULT NULL,
  `gender` varchar(10) DEFAULT NULL,
  `birth_date` date DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `phone_secondary` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `line_id` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `position_title` varchar(100) DEFAULT NULL,
  `driver_license_number` varchar(50) DEFAULT NULL,
  `driver_license_type` varchar(20) DEFAULT NULL,
  `driver_license_expire_date` date DEFAULT NULL,
  `hire_date` date DEFAULT NULL,
  `years_experience` int(11) DEFAULT 0,
  `photo_url` varchar(255) DEFAULT NULL,
  `license_photo_url` varchar(255) DEFAULT NULL,
  `id_card_photo_url` varchar(255) DEFAULT NULL,
  `emergency_contact_name` varchar(100) DEFAULT NULL,
  `emergency_contact_phone` varchar(20) DEFAULT NULL,
  `emergency_contact_relation` varchar(50) DEFAULT NULL,
  `blood_type` varchar(5) DEFAULT NULL,
  `medical_conditions` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `total_trips` int(11) DEFAULT 0,
  `total_distance_km` decimal(12,2) DEFAULT 0.00,
  `average_rating` decimal(3,2) DEFAULT 0.00,
  `driver_status` varchar(20) DEFAULT 'available',
  `is_active` tinyint(1) DEFAULT 1,
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `car_driver`
--

INSERT INTO `car_driver` (`id`, `driver_code`, `employee_code`, `id_card_number`, `prefix_name`, `first_name`, `last_name`, `first_name_en`, `last_name_en`, `nickname`, `gender`, `birth_date`, `phone`, `phone_secondary`, `email`, `line_id`, `address`, `organization_id`, `position_title`, `driver_license_number`, `driver_license_type`, `driver_license_expire_date`, `hire_date`, `years_experience`, `photo_url`, `license_photo_url`, `id_card_photo_url`, `emergency_contact_name`, `emergency_contact_phone`, `emergency_contact_relation`, `blood_type`, `medical_conditions`, `notes`, `total_trips`, `total_distance_km`, `average_rating`, `driver_status`, `is_active`, `is_deleted`, `deleted_at`, `deleted_by`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'DRV001', 'EMP-D001', NULL, NULL, 'สมชาย ใจดี', NULL, NULL, NULL, NULL, NULL, '1981-02-26', '088-975-2217', NULL, NULL, NULL, NULL, 5, NULL, '6-58749-74612-76-5', 'T1', '2027-02-26', '2019-02-26', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 'available', 1, 0, NULL, NULL, 1772075274, 1772075274, NULL, NULL),
(2, 'DRV002', 'EMP-D002', NULL, NULL, 'สมศักดิ์ มั่นคง', NULL, NULL, NULL, NULL, NULL, '1990-02-26', '088-666-1606', NULL, NULL, NULL, NULL, 5, NULL, '5-16251-15138-72-8', 'T1', '2029-02-26', '2016-02-26', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 'available', 1, 0, NULL, NULL, 1772075274, 1772075274, NULL, NULL),
(3, 'DRV003', 'EMP-D003', NULL, NULL, 'ประสิทธิ์ เจริญสุข', NULL, NULL, NULL, NULL, NULL, '1987-02-26', '081-679-5185', NULL, NULL, NULL, NULL, 5, NULL, '8-81350-78908-27-5', 'T2', '2028-02-26', '2020-02-26', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 'available', 1, 0, NULL, NULL, 1772075274, 1772075274, NULL, NULL),
(4, 'DRV004', 'EMP-D004', NULL, NULL, 'วิชัย สุขใจ', NULL, NULL, NULL, NULL, NULL, '1989-02-26', '081-165-2860', NULL, NULL, NULL, NULL, 5, NULL, '3-50558-19951-76-3', 'T2', '2029-02-26', '2021-02-26', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 'available', 1, 0, NULL, NULL, 1772075274, 1772075274, NULL, NULL),
(5, 'DRV005', 'EMP-D005', NULL, NULL, 'สุชาติ รักงาน', NULL, NULL, NULL, NULL, NULL, '1980-02-26', '084-934-1297', NULL, NULL, NULL, NULL, 5, NULL, '2-74139-70874-49-8', 'T3', '2028-02-26', '2020-02-26', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 'available', 1, 0, NULL, NULL, 1772075274, 1772075274, NULL, NULL),
(6, 'DRV006', 'EMP-D006', NULL, NULL, 'อนุชา พึ่งพา', NULL, NULL, NULL, NULL, NULL, '1978-02-26', '085-418-9732', NULL, NULL, NULL, NULL, 5, NULL, '9-85350-87307-51-9', 'T1', '2027-02-26', '2018-02-26', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 'available', 1, 0, NULL, NULL, 1772075274, 1772075274, NULL, NULL),
(7, 'DRV007', 'EMP-D007', NULL, NULL, 'ชัยวัฒน์ ก้าวหน้า', NULL, NULL, NULL, NULL, NULL, '1992-02-26', '086-591-7335', NULL, NULL, NULL, NULL, 5, NULL, '3-66237-95113-89-8', 'T2', '2029-02-26', '2018-02-26', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 'available', 1, 0, NULL, NULL, 1772075274, 1772075274, NULL, NULL),
(8, 'DRV008', 'EMP-D008', NULL, NULL, 'สุรศักดิ์ มีศิลป์', NULL, NULL, NULL, NULL, NULL, '1987-02-26', '086-375-8736', NULL, NULL, NULL, NULL, 5, NULL, '9-97013-94473-65-7', 'T3', '2029-02-26', '2024-02-26', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, 0.00, 0.00, 'available', 1, 0, NULL, NULL, 1772075274, 1772075274, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `car_driver_license_history`
--

CREATE TABLE `car_driver_license_history` (
  `id` int(11) NOT NULL,
  `driver_id` int(11) NOT NULL,
  `license_number` varchar(50) NOT NULL,
  `license_type` varchar(20) DEFAULT NULL,
  `issue_date` date DEFAULT NULL,
  `expire_date` date DEFAULT NULL,
  `issued_province` varchar(100) DEFAULT NULL,
  `document_url` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `car_fuel_log`
--

CREATE TABLE `car_fuel_log` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `reservation_id` int(11) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `refuel_date` datetime NOT NULL,
  `fuel_type` varchar(20) NOT NULL,
  `quantity_liters` decimal(10,3) NOT NULL,
  `price_per_liter` decimal(10,2) NOT NULL,
  `total_cost` decimal(12,2) NOT NULL,
  `mileage_at_refuel` int(11) DEFAULT NULL,
  `gas_station_name` varchar(200) DEFAULT NULL,
  `gas_station_location` varchar(255) DEFAULT NULL,
  `receipt_number` varchar(50) DEFAULT NULL,
  `receipt_image` varchar(255) DEFAULT NULL,
  `is_full_tank` tinyint(1) DEFAULT 1,
  `payment_method` varchar(30) DEFAULT 'cash',
  `notes` text DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `car_maintenance_log`
--

CREATE TABLE `car_maintenance_log` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `maintenance_type` varchar(50) NOT NULL,
  `maintenance_date` date NOT NULL,
  `mileage_at_service` int(11) DEFAULT NULL,
  `description` text NOT NULL,
  `service_provider` varchar(200) DEFAULT NULL,
  `cost_amount` decimal(12,2) DEFAULT 0.00,
  `cost_currency` varchar(3) DEFAULT 'THB',
  `invoice_number` varchar(50) DEFAULT NULL,
  `warranty_end_date` date DEFAULT NULL,
  `next_service_date` date DEFAULT NULL,
  `next_service_mileage` int(11) DEFAULT NULL,
  `parts_replaced` text DEFAULT NULL,
  `document_files` text DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'completed',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `car_mission_type`
--

CREATE TABLE `car_mission_type` (
  `id` int(11) NOT NULL,
  `mission_code` varchar(20) NOT NULL,
  `mission_name` varchar(100) NOT NULL,
  `mission_name_en` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `requires_approval` tinyint(1) DEFAULT 1,
  `max_days_advance` int(11) DEFAULT 30,
  `color_code` varchar(10) DEFAULT '#3498db',
  `icon_class` varchar(50) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `car_mission_type`
--

INSERT INTO `car_mission_type` (`id`, `mission_code`, `mission_name`, `mission_name_en`, `description`, `requires_approval`, `max_days_advance`, `color_code`, `icon_class`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'MEET', 'ประชุม', 'Meeting', NULL, 1, 30, '#3498db', NULL, 1, 1, 1772075274, 1772075274),
(2, 'TRAIN', 'อบรม/สัมมนา', 'Training/Seminar', NULL, 1, 30, '#3498db', NULL, 2, 1, 1772075274, 1772075274),
(3, 'INSP', 'ตรวจราชการ', 'Inspection', NULL, 1, 30, '#3498db', NULL, 3, 1, 1772075274, 1772075274),
(4, 'COORD', 'ประสานงาน', 'Coordination', NULL, 1, 30, '#3498db', NULL, 4, 1, 1772075274, 1772075274),
(5, 'PICK', 'รับ-ส่งบุคคล', 'Pick-up/Drop-off', NULL, 1, 30, '#3498db', NULL, 5, 1, 1772075274, 1772075274),
(6, 'DOC', 'รับ-ส่งเอกสาร', 'Document Delivery', NULL, 1, 30, '#3498db', NULL, 6, 1, 1772075274, 1772075274),
(7, 'FIELD', 'ลงพื้นที่', 'Field Work', NULL, 1, 30, '#3498db', NULL, 7, 1, 1772075274, 1772075274),
(8, 'EVENT', 'งานพิธี/กิจกรรม', 'Ceremony/Event', NULL, 1, 30, '#3498db', NULL, 8, 1, 1772075274, 1772075274),
(9, 'OTHER', 'อื่นๆ', 'Others', NULL, 1, 30, '#3498db', NULL, 9, 1, 1772075274, 1772075274);

-- --------------------------------------------------------

--
-- Table structure for table `car_notification`
--

CREATE TABLE `car_notification` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `notification_type` varchar(50) NOT NULL,
  `reference_type` varchar(50) DEFAULT NULL,
  `reference_id` int(11) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `action_url` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` datetime DEFAULT NULL,
  `is_sent_email` tinyint(1) DEFAULT 0,
  `email_sent_at` datetime DEFAULT NULL,
  `is_sent_line` tinyint(1) DEFAULT 0,
  `line_sent_at` datetime DEFAULT NULL,
  `priority` varchar(20) DEFAULT 'normal',
  `expires_at` datetime DEFAULT NULL,
  `created_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `car_organization`
--

CREATE TABLE `car_organization` (
  `id` int(11) NOT NULL,
  `org_code` varchar(20) NOT NULL,
  `org_name` varchar(255) NOT NULL,
  `org_name_en` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `address` varchar(100) DEFAULT NULL COMMENT 'ที่อยู่',
  `contact_phone` varchar(50) DEFAULT NULL,
  `contact_email` varchar(100) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `car_organization`
--

INSERT INTO `car_organization` (`id`, `org_code`, `org_name`, `org_name_en`, `parent_id`, `description`, `address`, `contact_phone`, `contact_email`, `sort_order`, `is_active`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'PBRI', 'สถาบันพระบรมราชชนก', 'Praboromarajchanok Institute', NULL, NULL, NULL, NULL, NULL, 0, 1, 1772075274, 1772075274, NULL, NULL),
(2, 'ICT', 'สำนักเทคโนโลยีดิจิทัลและปัญญาประดิษฐ์', 'Digital Technology & AI Division', 1, NULL, NULL, NULL, NULL, 0, 1, 1772075274, 1772075274, NULL, NULL),
(3, 'HR', 'กองบริหารงานบุคคล', 'Human Resources Division', 1, NULL, NULL, NULL, NULL, 0, 1, 1772075274, 1772075274, NULL, NULL),
(4, 'FIN', 'กองคลัง', 'Finance Division', 1, NULL, NULL, NULL, NULL, 0, 1, 1772075274, 1772075274, NULL, NULL),
(5, 'ADMIN', 'กองบริหารงานทั่วไป', 'General Administration Division', 1, NULL, NULL, NULL, NULL, 0, 1, 1772075274, 1772075274, NULL, NULL),
(6, 'ACAD', 'กองวิชาการ', 'Academic Division', 1, NULL, NULL, NULL, NULL, 0, 1, 1772075274, 1772075274, NULL, NULL),
(7, 'PLAN', 'กองนโยบายและแผน', 'Policy and Planning Division', 1, NULL, NULL, NULL, NULL, 0, 1, 1772075274, 1772075274, NULL, NULL),
(8, 'QA', 'กองประกันคุณภาพการศึกษา', 'Quality Assurance Division', 1, NULL, NULL, NULL, NULL, 0, 1, 1772075274, 1772075274, NULL, NULL),
(9, 'RES', 'กองวิจัยและพัฒนา', 'Research and Development Division', 1, NULL, NULL, NULL, NULL, 0, 1, 1772075274, 1772075274, NULL, NULL),
(10, 'LAW', 'กองกฎหมาย', 'Legal Division', 1, NULL, NULL, NULL, NULL, 0, 1, 1772075274, 1772075274, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `car_reservation`
--

CREATE TABLE `car_reservation` (
  `id` int(11) NOT NULL,
  `reservation_code` varchar(30) NOT NULL,
  `fiscal_year` smallint(6) NOT NULL,
  `requester_user_id` int(11) NOT NULL,
  `requester_name` varchar(200) NOT NULL,
  `requester_organization_id` int(11) DEFAULT NULL,
  `requester_position` varchar(100) NOT NULL COMMENT 'ตำแหน่งผู้ขอใช้รถ',
  `requester_phone` varchar(50) DEFAULT NULL,
  `requester_email` varchar(100) DEFAULT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `driver_id` int(11) DEFAULT NULL,
  `mission_type_id` int(11) DEFAULT NULL COMMENT 'รหัสประเภทภารกิจ (FK)',
  `mission_title` varchar(255) NOT NULL COMMENT 'หัวข้อภารกิจ',
  `mission_description` text DEFAULT NULL COMMENT 'รายละเอียดภารกิจ',
  `departure_datetime` datetime NOT NULL,
  `return_datetime` datetime NOT NULL,
  `actual_departure_datetime` datetime DEFAULT NULL,
  `actual_return_datetime` datetime DEFAULT NULL,
  `origin_location` varchar(255) DEFAULT 'สถาบันพระบรมราชชนก',
  `destination_location` varchar(255) NOT NULL,
  `destination_province` varchar(100) DEFAULT NULL,
  `destination_address` text DEFAULT NULL,
  `route_description` text DEFAULT NULL,
  `estimated_distance_km` decimal(10,2) DEFAULT NULL,
  `passenger_count` int(11) DEFAULT 1,
  `passenger_names` text DEFAULT NULL,
  `luggage_description` text DEFAULT NULL,
  `estimated_fuel_cost` decimal(12,2) DEFAULT 0.00,
  `estimated_toll_cost` decimal(12,2) DEFAULT 0.00,
  `estimated_other_cost` decimal(12,2) DEFAULT 0.00,
  `actual_fuel_cost` decimal(12,2) DEFAULT 0.00,
  `actual_toll_cost` decimal(12,2) DEFAULT 0.00,
  `actual_other_cost` decimal(12,2) DEFAULT 0.00,
  `budget_code` varchar(50) DEFAULT NULL,
  `mileage_start` int(11) DEFAULT NULL,
  `mileage_end` int(11) DEFAULT NULL,
  `actual_distance_km` decimal(10,2) DEFAULT NULL,
  `reservation_status` varchar(30) DEFAULT 'pending',
  `priority_level` varchar(20) DEFAULT 'normal',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `approval_notes` text DEFAULT NULL,
  `rejected_by` int(11) DEFAULT NULL,
  `rejected_at` datetime DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `cancelled_by` int(11) DEFAULT NULL,
  `cancelled_at` datetime DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `special_requirements` text DEFAULT NULL,
  `admin_notes` text DEFAULT NULL,
  `driver_notes` text DEFAULT NULL,
  `requester_notes` text DEFAULT NULL,
  `completion_notes` text DEFAULT NULL,
  `driver_rating` decimal(3,2) DEFAULT NULL,
  `vehicle_rating` decimal(3,2) DEFAULT NULL,
  `service_rating` decimal(3,2) DEFAULT NULL,
  `rating_feedback` text DEFAULT NULL,
  `rated_at` datetime DEFAULT NULL,
  `attachment_files` text DEFAULT NULL,
  `is_deleted` tinyint(1) DEFAULT 0,
  `deleted_at` datetime DEFAULT NULL,
  `deleted_by` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `car_reservation_history`
--

CREATE TABLE `car_reservation_history` (
  `id` int(11) NOT NULL,
  `reservation_id` int(11) NOT NULL,
  `action_type` varchar(30) NOT NULL,
  `old_status` varchar(30) DEFAULT NULL,
  `new_status` varchar(30) DEFAULT NULL,
  `old_values` text DEFAULT NULL,
  `new_values` text DEFAULT NULL,
  `action_notes` text DEFAULT NULL,
  `action_by` int(11) NOT NULL,
  `action_by_name` varchar(200) DEFAULT NULL,
  `action_at` datetime NOT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `car_system_setting`
--

CREATE TABLE `car_system_setting` (
  `id` int(11) NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` varchar(20) DEFAULT 'string',
  `setting_group` varchar(50) DEFAULT 'general',
  `display_name` varchar(200) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `is_system` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `car_system_setting`
--

INSERT INTO `car_system_setting` (`id`, `setting_key`, `setting_value`, `setting_type`, `setting_group`, `display_name`, `description`, `is_system`, `sort_order`, `created_at`, `updated_at`) VALUES
(1, 'reservation_advance_days', '30', 'integer', 'reservation', 'จำนวนวันที่จองล่วงหน้าได้', 'จำนวนวันสูงสุดที่สามารถจองรถล่วงหน้าได้', 0, 1, 1766197735, 1766197735),
(2, 'reservation_min_hours', '2', 'integer', 'reservation', 'ชั่วโมงขั้นต่ำในการจอง', 'จำนวนชั่วโมงขั้นต่ำสำหรับการจองรถแต่ละครั้ง', 0, 2, 1766197735, 1766197735),
(3, 'auto_cancel_pending_hours', '24', 'integer', 'reservation', 'ยกเลิกอัตโนมัติหลังกี่ชั่วโมง', 'จำนวนชั่วโมงที่รอการอนุมัติก่อนยกเลิกอัตโนมัติ', 0, 3, 1766197735, 1766197735),
(4, 'require_approval', '1', 'boolean', 'reservation', 'ต้องมีการอนุมัติ', 'กำหนดว่าการจองต้องผ่านการอนุมัติหรือไม่', 0, 4, 1766197735, 1766197735),
(5, 'notification_email', '1', 'boolean', 'notification', 'แจ้งเตือนทางอีเมล', 'เปิด/ปิดการแจ้งเตือนทางอีเมล', 0, 10, 1766197735, 1766197735),
(6, 'notification_line', '0', 'boolean', 'notification', 'แจ้งเตือนทาง LINE', 'เปิด/ปิดการแจ้งเตือนทาง LINE', 0, 11, 1766197735, 1766197735),
(7, 'system_name', 'PBRI Car Reservation System', 'string', 'general', 'ชื่อระบบ', 'ชื่อที่แสดงในระบบ', 1, 1, 1766197735, 1766197735),
(8, 'system_short_name', 'PBRI CRS', 'string', 'general', 'ชื่อย่อระบบ', 'ชื่อย่อที่แสดงในระบบ', 1, 2, 1766197735, 1766197735),
(9, 'organization_name', 'สถาบันพระบรมราชชนก', 'string', 'general', 'ชื่อหน่วยงาน', 'ชื่อหน่วยงานเจ้าของระบบ', 0, 3, 1766197735, 1766197735),
(10, 'fiscal_year_start_month', '10', 'integer', 'general', 'เดือนเริ่มต้นปีงบประมาณ', 'เดือนที่เริ่มต้นปีงบประมาณ (1-12)', 1, 4, 1766197735, 1766197735);

-- --------------------------------------------------------

--
-- Table structure for table `car_vehicle`
--

CREATE TABLE `car_vehicle` (
  `id` int(11) NOT NULL COMMENT 'ID',
  `vehicle_code` varchar(20) NOT NULL COMMENT 'รถยนต์',
  `license_plate` varchar(20) NOT NULL COMMENT 'ทะเบียนรถ',
  `license_plate_province` varchar(50) DEFAULT NULL COMMENT 'ทะเบียนรถจังหวัด',
  `vehicle_type_id` int(11) NOT NULL COMMENT 'ประเภท',
  `brand` varchar(100) DEFAULT NULL COMMENT 'ยี่ห้อ',
  `model_name` varchar(100) DEFAULT NULL COMMENT 'รุ่น',
  `model_year` smallint(6) DEFAULT NULL COMMENT 'ปีรถ',
  `color` varchar(50) DEFAULT NULL COMMENT 'สี',
  `nickname` varchar(50) DEFAULT NULL,
  `seat_count` int(11) DEFAULT 4 COMMENT 'จำนวนที่นั่ง',
  `fuel_type` varchar(20) DEFAULT 'gasoline' COMMENT 'ประเภทเชื้อเพลิง',
  `transmission_type` varchar(20) DEFAULT 'automatic' COMMENT 'ระบบเกียร์',
  `engine_cc` int(11) DEFAULT NULL COMMENT 'ขนาดเครื่องยนต์',
  `chassis_number` varchar(50) DEFAULT NULL COMMENT 'เลขตัวถัง',
  `engine_number` varchar(50) DEFAULT NULL COMMENT 'เลขเครื่องยนต์',
  `registration_date` date DEFAULT NULL COMMENT 'จดทะเบียนเมื่อ',
  `insurance_expire_date` date DEFAULT NULL COMMENT 'ประกันภัยหมดอายุ',
  `insurance_company` varchar(100) DEFAULT NULL COMMENT 'บริษัทประกันภัย',
  `insurance_policy_number` varchar(100) DEFAULT NULL COMMENT 'กรมธรรม์ประกันภัย',
  `tax_expire_date` date DEFAULT NULL COMMENT 'ภาษีรถยนต์',
  `inspection_expire_date` date DEFAULT NULL COMMENT 'ตรวจสภาพประจำปี',
  `purchase_date` date DEFAULT NULL COMMENT 'วันที่ซื้อ',
  `purchase_price` decimal(12,2) DEFAULT NULL COMMENT 'ราคาซื้อ',
  `mileage_current` int(11) DEFAULT 0 COMMENT 'เลขไมล์ปัจจุบัน',
  `fuel_capacity` decimal(10,2) DEFAULT NULL COMMENT 'ความจุเชื้อเพลิง',
  `fuel_consumption` decimal(10,2) DEFAULT NULL COMMENT 'อัตราสิ้นเปลือง',
  `organization_id` int(11) DEFAULT NULL COMMENT 'หน่วยงาน',
  `assigned_driver_id` int(11) DEFAULT NULL COMMENT 'คนขับที่มอบหมาย',
  `photo_url` varchar(255) DEFAULT NULL COMMENT 'รูปภาพ',
  `document_file` varchar(255) DEFAULT NULL COMMENT 'ไฟล์เอกสารรถยนต์',
  `notes` text DEFAULT NULL COMMENT 'รายละเอียดเพิ่มเติม',
  `vehicle_status` varchar(20) DEFAULT 'available' COMMENT 'สถานะรถ',
  `is_active` tinyint(1) UNSIGNED DEFAULT 1 COMMENT 'สถานะการใช้งาน',
  `is_deleted` tinyint(1) UNSIGNED DEFAULT 0 COMMENT 'สถานะการลบ',
  `deleted_at` datetime DEFAULT NULL COMMENT 'ลบเมื่อ',
  `deleted_by` int(11) UNSIGNED DEFAULT NULL COMMENT 'ลบโดย',
  `created_at` datetime DEFAULT current_timestamp() COMMENT 'สร้างเมื่อ',
  `created_by` int(11) UNSIGNED DEFAULT NULL COMMENT 'สร้างโดย',
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp() COMMENT 'แก้ไขล่าสุด',
  `updated_by` int(11) UNSIGNED DEFAULT NULL COMMENT 'แก้ไชโดย'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `car_vehicle`
--

INSERT INTO `car_vehicle` (`id`, `vehicle_code`, `license_plate`, `license_plate_province`, `vehicle_type_id`, `brand`, `model_name`, `model_year`, `color`, `nickname`, `seat_count`, `fuel_type`, `transmission_type`, `engine_cc`, `chassis_number`, `engine_number`, `registration_date`, `insurance_expire_date`, `insurance_company`, `insurance_policy_number`, `tax_expire_date`, `inspection_expire_date`, `purchase_date`, `purchase_price`, `mileage_current`, `fuel_capacity`, `fuel_consumption`, `organization_id`, `assigned_driver_id`, `photo_url`, `document_file`, `notes`, `vehicle_status`, `is_active`, `is_deleted`, `deleted_at`, `deleted_by`, `created_at`, `created_by`, `updated_at`, `updated_by`) VALUES
(1, 'VH001', 'กข 1234', 'กรุงเทพมหานคร', 1, 'Toyota', 'Camry', 2022, 'ดำ', NULL, 4, 'diesel', 'automatic', 2500, 'CH2028811E5A79A20', 'EN11C06F28A1C4', '2025-02-26', '2027-02-26', NULL, NULL, '2026-12-26', '2026-05-26', NULL, NULL, 67448, NULL, NULL, 5, NULL, NULL, NULL, NULL, 'available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(2, 'VH002', 'กค 5678', 'กรุงเทพมหานคร', 1, 'Honda', 'Accord', 2023, 'ขาว', NULL, 4, 'diesel', 'automatic', 2000, 'CH0D545927B874DA7', 'EN0E8E5B5DBD7D', '2021-02-26', '2026-03-26', NULL, NULL, '2026-10-26', '2027-01-26', NULL, NULL, 29533, NULL, NULL, 5, NULL, NULL, NULL, NULL, 'available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(3, 'VH003', 'กง 9012', 'กรุงเทพมหานคร', 1, 'Nissan', 'Teana', 2021, 'เงิน', NULL, 4, 'diesel', 'automatic', 2500, 'CH7FC4C2AC2767DE9', 'EN66378A5C958F', '2025-02-26', '2026-12-26', NULL, NULL, '2026-12-26', '2026-08-26', NULL, NULL, 36114, NULL, NULL, 5, NULL, NULL, NULL, NULL, 'available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(4, 'VH004', 'ขก 1111', 'กรุงเทพมหานคร', 2, 'Toyota', 'Fortuner', 2023, 'ดำ', NULL, 7, 'diesel', 'automatic', 2800, 'CH7F1928385BB3E91', 'EN599689B94CFC', '2025-02-26', '2027-01-26', NULL, NULL, '2026-06-26', '2026-07-26', NULL, NULL, 41656, NULL, NULL, 5, NULL, NULL, NULL, NULL, 'available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(5, 'VH005', 'ขข 2222', 'กรุงเทพมหานคร', 2, 'Isuzu', 'MU-X', 2022, 'เทา', NULL, 7, 'diesel', 'automatic', 3000, 'CH970C577481BD549', 'END2A3755FC795', '2025-02-26', '2026-04-26', NULL, NULL, '2026-05-26', '2026-06-26', NULL, NULL, 24688, NULL, NULL, 5, NULL, NULL, NULL, NULL, 'available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(6, 'VH006', 'ขค 3333', 'กรุงเทพมหานคร', 2, 'Ford', 'Everest', 2023, 'น้ำเงิน', NULL, 7, 'diesel', 'automatic', 2000, 'CHBF88436E5960CFB', 'ENF03F6502F96E', '2025-02-26', '2026-11-26', NULL, NULL, '2026-07-26', '2026-06-26', NULL, NULL, 39109, NULL, NULL, 5, NULL, NULL, NULL, NULL, 'available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(7, 'VH007', 'คก 4444', 'กรุงเทพมหานคร', 3, 'Toyota', 'Commuter', 2022, 'ขาว', NULL, 12, 'diesel', 'automatic', 2800, 'CH1333AA9EA9C3551', 'EN63D95FB91CB2', '2024-02-26', '2026-05-26', NULL, NULL, '2026-06-26', '2026-03-26', NULL, NULL, 32075, NULL, NULL, 5, NULL, NULL, NULL, NULL, 'available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(8, 'VH008', 'คข 5555', 'กรุงเทพมหานคร', 3, 'Toyota', 'Hiace', 2023, 'เงิน', NULL, 14, 'diesel', 'automatic', 2800, 'CH99A4E71F667C78F', 'ENE02D79D9A323', '2021-02-26', '2027-02-26', NULL, NULL, '2026-03-26', '2026-07-26', NULL, NULL, 64483, NULL, NULL, 5, NULL, NULL, NULL, NULL, 'available', 1, 0, NULL, NULL, NULL, NULL, NULL, NULL),
(9, 'VH009', 'คค 6666', 'กรุงเทพมหานคร', 3, 'Hyundai', 'H1', 2021, 'ดำ', NULL, 11, 'diesel', 'automatic', 2500, 'CHEB2B400ACCF3366', 'ENC10AA31962D3', '2021-02-26', '2026-08-26', '', '', '2026-10-26', '2027-02-26', NULL, NULL, 70412, NULL, NULL, 5, 7, 'vehicle_9_1772576132_3t-EfI.jpeg', NULL, '', 'available', 1, 0, NULL, NULL, NULL, NULL, '2026-03-04 05:15:55', 212),
(10, 'VH010', 'งก 7777', 'กรุงเทพมหานคร', 4, 'Toyota', 'Hilux Revo', 2023, 'ขาว', NULL, 4, 'diesel', 'automatic', 2400, 'CH681E18DCEB022D7', 'EN4E08E9956F50', '2025-02-26', '2026-11-26', '', '', '2026-06-26', '2026-10-26', NULL, NULL, 22193, NULL, NULL, 5, 4, 'vehicle_10_1772478673_3V50fq.jpg', NULL, '', 'available', 1, 0, NULL, NULL, NULL, NULL, '2026-03-04 05:12:58', 212),
(11, 'VH011', 'งข 8888', 'กรุงเทพมหานคร', 4, 'Isuzu', 'D-Max', 2022, 'เงิน', NULL, 4, 'diesel', 'automatic', 3000, 'CH6E539EACDEF364B', 'EN7C2444D3BAEE', '2024-02-26', '2026-08-26', '', '', '2026-05-26', '2026-10-26', NULL, NULL, 39040, NULL, NULL, 5, NULL, NULL, NULL, '', 'available', 1, 0, NULL, NULL, NULL, NULL, '2026-03-04 05:03:32', 212),
(12, 'VH012', 'จก 9999', 'กรุงเทพมหานคร', 5, 'Hino', 'RK8', 2020, 'ขาว-น้ำเงิน', NULL, 40, 'diesel', 'automatic', 8000, 'CH725E92FA56699C5', 'ENC795FC2E8FD3', '2022-02-26', '2026-06-26', '', '', '2026-11-26', '2026-08-26', NULL, NULL, 47074, NULL, NULL, 5, 6, 'vehicle_12_1772575453_aTy1bL.jpg', NULL, '', 'available', 1, 0, NULL, NULL, NULL, NULL, '2026-03-04 05:04:13', 212),
(13, 'VH013', 'ฉก 1010', 'กรุงเทพมหานคร', 6, 'Toyota', 'Coaster', 2021, 'ขาว', NULL, 20, 'diesel', 'automatic', 4000, 'CH899E3A7F0490C02', 'EN6FD58FBAC407', '2022-02-26', '2026-09-26', '', '', '2026-06-26', '2027-01-26', NULL, NULL, 26252, NULL, NULL, 5, 8, 'vehicle_13_1772575951_g3rBpC.jpg', NULL, '', 'available', 1, 0, NULL, NULL, NULL, NULL, '2026-03-04 05:12:31', 212);

-- --------------------------------------------------------

--
-- Table structure for table `car_vehicle_type`
--

CREATE TABLE `car_vehicle_type` (
  `id` int(11) NOT NULL,
  `type_code` varchar(20) NOT NULL,
  `type_name` varchar(100) NOT NULL,
  `type_name_en` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `seat_capacity` int(11) DEFAULT 4,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `car_vehicle_type`
--

INSERT INTO `car_vehicle_type` (`id`, `type_code`, `type_name`, `type_name_en`, `description`, `seat_capacity`, `sort_order`, `is_active`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(1, 'SEDAN', 'รถเก๋ง', 'Sedan', NULL, 4, 1, 1, 1772075274, 1772075274, NULL, NULL),
(2, 'SUV', 'รถ SUV', 'SUV', NULL, 7, 2, 1, 1772075274, 1772075274, NULL, NULL),
(3, 'VAN', 'รถตู้', 'Van', NULL, 12, 3, 1, 1772075274, 1772075274, NULL, NULL),
(4, 'PICKUP', 'รถกระบะ', 'Pickup Truck', NULL, 4, 4, 1, 1772075274, 1772075274, NULL, NULL),
(5, 'BUS', 'รถบัส', 'Bus', NULL, 40, 5, 1, 1772075274, 1772075274, NULL, NULL),
(6, 'MINIBUS', 'รถมินิบัส', 'Mini Bus', NULL, 20, 6, 1, 1772075274, 1772075274, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `expenses`
--

CREATE TABLE `expenses` (
  `id` int(11) NOT NULL,
  `expense_code` varchar(30) NOT NULL,
  `project_id` int(11) NOT NULL,
  `project_budget_id` int(11) NOT NULL,
  `expense_category_id` int(11) NOT NULL,
  `disbursement_id` int(11) DEFAULT NULL,
  `expense_title` varchar(500) NOT NULL,
  `expense_description` text DEFAULT NULL,
  `expense_amount` decimal(15,2) NOT NULL,
  `expense_date` date NOT NULL,
  `fiscal_year_code` varchar(4) NOT NULL,
  `fiscal_month_code` varchar(2) NOT NULL,
  `vendor_name` varchar(255) DEFAULT NULL,
  `vendor_tax_id` varchar(20) DEFAULT NULL,
  `invoice_number` varchar(50) DEFAULT NULL,
  `invoice_date` date DEFAULT NULL,
  `receipt_number` varchar(50) DEFAULT NULL,
  `receipt_date` date DEFAULT NULL,
  `expense_status` varchar(30) DEFAULT 'draft',
  `approval_status` varchar(30) DEFAULT 'pending',
  `payment_status` varchar(30) DEFAULT 'pending',
  `submitted_by` int(11) DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `paid_by` int(11) DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `expense_notes` text DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `expense_categories`
--

CREATE TABLE `expense_categories` (
  `id` int(11) NOT NULL,
  `category_code` varchar(20) NOT NULL,
  `category_name` varchar(255) NOT NULL,
  `category_name_en` varchar(255) DEFAULT NULL,
  `parent_id` int(11) DEFAULT NULL,
  `category_level` int(11) DEFAULT 1,
  `is_budgetable` tinyint(1) DEFAULT 1,
  `is_expensable` tinyint(1) DEFAULT 1,
  `requires_approval` tinyint(1) DEFAULT 0,
  `approval_threshold` decimal(15,2) DEFAULT 0.00,
  `sort_order` int(11) DEFAULT 0,
  `category_status` varchar(20) DEFAULT 'active',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fiscal_years`
--

CREATE TABLE `fiscal_years` (
  `id` int(11) NOT NULL,
  `fiscal_year_code` varchar(4) NOT NULL,
  `fiscal_year_desc` varchar(50) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `approval_date` date DEFAULT NULL,
  `fy_status` varchar(20) DEFAULT 'planning',
  `is_current` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fuel_budget`
--

CREATE TABLE `fuel_budget` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `department` varchar(100) DEFAULT NULL,
  `budget_type` varchar(20) NOT NULL DEFAULT 'monthly',
  `budget_year` int(11) NOT NULL,
  `budget_month` int(11) DEFAULT NULL,
  `budget_amount` decimal(12,2) NOT NULL,
  `spent_amount` decimal(12,2) DEFAULT 0.00,
  `remaining_amount` decimal(12,2) DEFAULT NULL,
  `alert_threshold` int(11) DEFAULT 80,
  `notes` text DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `fuel_price_history`
--

CREATE TABLE `fuel_price_history` (
  `id` int(11) NOT NULL,
  `fuel_type` varchar(50) NOT NULL,
  `price_per_liter` decimal(10,2) NOT NULL,
  `effective_date` date NOT NULL,
  `source` varchar(100) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fuel_price_history`
--

INSERT INTO `fuel_price_history` (`id`, `fuel_type`, `price_per_liter`, `effective_date`, `source`, `notes`, `created_at`) VALUES
(1, 'gasoline_91', 35.00, '2025-12-29', 'PTT Reference', NULL, '2025-12-29 18:29:02'),
(2, 'gasoline_95', 42.00, '2025-12-29', 'PTT Reference', NULL, '2025-12-29 18:29:02'),
(3, 'gasohol_e20', 33.00, '2025-12-29', 'PTT Reference', NULL, '2025-12-29 18:29:02'),
(4, 'gasohol_e85', 28.00, '2025-12-29', 'PTT Reference', NULL, '2025-12-29 18:29:02'),
(5, 'diesel', 30.00, '2025-12-29', 'PTT Reference', NULL, '2025-12-29 18:29:02'),
(6, 'diesel_b7', 30.00, '2025-12-29', 'PTT Reference', NULL, '2025-12-29 18:29:02'),
(7, 'diesel_b20', 28.00, '2025-12-29', 'PTT Reference', NULL, '2025-12-29 18:29:02'),
(8, 'lpg', 15.00, '2025-12-29', 'PTT Reference', NULL, '2025-12-29 18:29:02'),
(9, 'ngv', 16.00, '2025-12-29', 'PTT Reference', NULL, '2025-12-29 18:29:02'),
(10, 'electric', 4.50, '2025-12-29', 'Per kWh', NULL, '2025-12-29 18:29:02');

-- --------------------------------------------------------

--
-- Table structure for table `fuel_record`
--

CREATE TABLE `fuel_record` (
  `id` int(11) UNSIGNED NOT NULL,
  `vehicle_id` int(11) UNSIGNED NOT NULL,
  `reservation_id` int(11) UNSIGNED DEFAULT NULL,
  `driver_id` int(11) UNSIGNED DEFAULT NULL,
  `record_date` date NOT NULL,
  `record_time` time DEFAULT NULL,
  `fuel_type` varchar(50) NOT NULL DEFAULT 'gasoline_95',
  `quantity_liters` decimal(10,2) NOT NULL,
  `price_per_liter` decimal(10,2) NOT NULL,
  `total_cost` decimal(12,2) NOT NULL,
  `odometer_reading` int(11) UNSIGNED NOT NULL,
  `previous_odometer` int(11) UNSIGNED DEFAULT NULL,
  `distance_traveled` int(11) UNSIGNED DEFAULT NULL,
  `fuel_efficiency` decimal(10,2) DEFAULT NULL COMMENT 'km per liter',
  `gas_station` varchar(255) DEFAULT NULL,
  `station_location` varchar(255) DEFAULT NULL,
  `receipt_number` varchar(100) DEFAULT NULL,
  `receipt_image` varchar(255) DEFAULT NULL,
  `is_full_tank` tinyint(1) UNSIGNED DEFAULT 1,
  `payment_method` varchar(50) DEFAULT 'cash',
  `notes` text DEFAULT NULL,
  `recorded_by` int(11) UNSIGNED DEFAULT NULL,
  `status` varchar(20) DEFAULT 'active',
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geofence`
--

CREATE TABLE `geofence` (
  `id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `geofence_type` varchar(20) DEFAULT 'circle',
  `center_latitude` decimal(10,8) DEFAULT NULL,
  `center_longitude` decimal(11,8) DEFAULT NULL,
  `radius_meters` int(11) DEFAULT NULL,
  `polygon_coordinates` text DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `alert_on_enter` tinyint(1) DEFAULT 1,
  `alert_on_exit` tinyint(1) DEFAULT 1,
  `is_active` tinyint(1) DEFAULT 1,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` varchar(20) NOT NULL DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `geofence_alert`
--

CREATE TABLE `geofence_alert` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `geofence_id` int(11) NOT NULL,
  `alert_type` varchar(20) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `is_acknowledged` tinyint(1) DEFAULT 0,
  `acknowledged_by` int(11) DEFAULT NULL,
  `acknowledged_at` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `login_history`
--

CREATE TABLE `login_history` (
  `id` int(11) NOT NULL,
  `user_id` int(11) DEFAULT NULL,
  `username` varchar(255) DEFAULT NULL,
  `login_type` varchar(50) DEFAULT 'password',
  `provider` varchar(255) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `browser_name` varchar(100) DEFAULT NULL,
  `os_name` varchar(100) DEFAULT NULL,
  `country_code` varchar(2) DEFAULT NULL,
  `city` varchar(100) DEFAULT NULL,
  `is_successful` tinyint(4) DEFAULT 0,
  `failure_reason` varchar(50) DEFAULT NULL,
  `session_id` varchar(255) DEFAULT NULL,
  `created_at` datetime DEFAULT NULL,
  `login_at` datetime DEFAULT NULL,
  `logout_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login_history`
--

INSERT INTO `login_history` (`id`, `user_id`, `username`, `login_type`, `provider`, `ip_address`, `user_agent`, `device_type`, `browser_name`, `os_name`, `country_code`, `city`, `is_successful`, `failure_reason`, `session_id`, `created_at`, `login_at`, `logout_at`) VALUES
(1, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อคเนื่องจากใส่รหัสผ่านผิดหลายครั้ง', NULL, '2025-12-30 02:03:21', '2025-12-30 02:03:21', NULL),
(2, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 10 นาที', NULL, '2025-12-30 02:08:33', '2025-12-30 02:08:33', NULL),
(3, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 02:08:36', '2025-12-30 02:08:36', NULL),
(4, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 02:08:43', '2025-12-30 02:08:43', NULL),
(5, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 02:08:50', '2025-12-30 02:08:50', NULL),
(6, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 02:09:07', '2025-12-30 02:09:07', NULL),
(7, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 02:09:17', '2025-12-30 02:09:17', NULL),
(8, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 02:09:27', '2025-12-30 02:09:27', NULL),
(9, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 02:09:47', '2025-12-30 02:09:47', NULL),
(10, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 02:10:00', '2025-12-30 02:10:00', NULL),
(11, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 02:10:11', '2025-12-30 02:10:11', NULL),
(12, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 14 นาที', NULL, '2025-12-30 02:12:07', '2025-12-30 02:12:07', NULL),
(13, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 02:12:14', '2025-12-30 02:12:14', NULL),
(14, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 02:12:39', '2025-12-30 02:12:39', NULL),
(15, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 02:12:46', '2025-12-30 02:12:46', NULL),
(16, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 02:13:03', '2025-12-30 02:13:03', NULL),
(17, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 6 นาที', NULL, '2025-12-30 02:22:42', '2025-12-30 02:22:42', NULL),
(18, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 02:22:55', '2025-12-30 02:22:55', NULL),
(19, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 02:23:05', '2025-12-30 02:23:05', NULL),
(20, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 02:23:08', '2025-12-30 02:23:08', NULL),
(21, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 02:23:19', '2025-12-30 02:23:19', NULL),
(22, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อคเนื่องจากใส่รหัสผ่านผิดหลายครั้ง', NULL, '2025-12-30 17:52:35', '2025-12-30 17:52:35', NULL),
(23, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 17:52:48', '2025-12-30 17:52:48', NULL),
(24, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 17:52:54', '2025-12-30 17:52:54', NULL),
(25, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อคเนื่องจากใส่รหัสผ่านผิดหลายครั้ง', NULL, '2025-12-30 18:14:04', '2025-12-30 18:14:04', NULL),
(26, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 18:14:16', '2025-12-30 18:14:16', NULL),
(27, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 18:14:24', '2025-12-30 18:14:24', NULL),
(28, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 18:14:35', '2025-12-30 18:14:35', NULL),
(29, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 18:15:08', '2025-12-30 18:15:08', NULL),
(30, 2, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2025-12-30 18:15:14', '2025-12-30 18:15:14', NULL),
(31, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2025-12-30 19:45:52', '2025-12-30 19:45:52', NULL),
(32, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-01-01 02:52:30', '2026-01-01 02:52:30', NULL),
(33, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 8 นาที', NULL, '2026-01-01 03:00:03', '2026-01-01 03:00:03', NULL),
(34, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2026-01-01 03:00:06', '2026-01-01 03:00:06', NULL),
(35, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2026-01-01 03:00:19', '2026-01-01 03:00:19', NULL),
(36, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2026-01-01 03:00:23', '2026-01-01 03:00:23', NULL),
(37, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-01-01 10:21:44', '2026-01-01 10:21:44', NULL),
(38, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2026-01-01 10:22:07', '2026-01-01 10:22:07', NULL),
(39, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 12 นาที', NULL, '2026-01-01 10:25:35', '2026-01-01 10:25:35', NULL),
(40, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2026-01-01 10:25:50', '2026-01-01 10:25:50', NULL),
(41, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, '2026-01-01 14:47:11', '2026-01-01 14:47:11', NULL),
(42, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2026-01-01 14:47:25', '2026-01-01 14:47:25', NULL),
(43, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 14 นาที', NULL, '2026-01-01 14:49:14', '2026-01-01 14:49:14', NULL),
(44, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-01-01 14:52:54', '2026-01-01 14:52:54', NULL),
(45, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (เหลือ 4 ครั้ง)', NULL, '2026-01-01 15:16:47', '2026-01-01 15:16:47', NULL),
(46, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-01-01 15:16:50', '2026-01-01 15:16:50', NULL),
(47, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-01-01 17:59:44', '2026-01-01 17:59:44', NULL),
(48, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (เหลือ 1 ครั้ง)', NULL, '2026-02-26 03:03:40', '2026-02-26 03:03:40', NULL),
(49, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อคเนื่องจากใส่รหัสผ่านผิดหลายครั้ง', NULL, '2026-02-26 03:03:43', '2026-02-26 03:03:43', NULL),
(50, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2026-02-26 03:03:47', '2026-02-26 03:03:47', NULL),
(51, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2026-02-26 03:03:49', '2026-02-26 03:03:49', NULL),
(52, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2026-02-26 03:03:55', '2026-02-26 03:03:55', NULL),
(53, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 8 นาที', NULL, '2026-02-26 03:11:53', '2026-02-26 03:11:53', NULL),
(54, 211, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (เหลือ 4 ครั้ง)', NULL, '2026-02-26 03:14:17', '2026-02-26 03:14:17', NULL),
(55, 211, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (เหลือ 4 ครั้ง)', NULL, '2026-02-26 03:16:20', '2026-02-26 03:16:20', NULL),
(56, 211, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (เหลือ 3 ครั้ง)', NULL, '2026-02-26 03:16:27', '2026-02-26 03:16:27', NULL),
(57, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (เหลือ 3 ครั้ง)', NULL, '2026-02-26 03:18:35', '2026-02-26 03:18:35', NULL),
(58, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (เหลือ 2 ครั้ง)', NULL, '2026-02-26 03:18:41', '2026-02-26 03:18:41', NULL),
(59, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (เหลือ 1 ครั้ง)', NULL, '2026-02-26 03:18:53', '2026-02-26 03:18:53', NULL),
(60, 211, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (เหลือ 2 ครั้ง)', NULL, '2026-02-26 10:11:16', '2026-02-26 10:11:16', NULL),
(61, 211, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (เหลือ 1 ครั้ง)', NULL, '2026-02-26 10:11:29', '2026-02-26 10:11:29', NULL),
(62, 211, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อคเนื่องจากใส่รหัสผ่านผิดหลายครั้ง', NULL, '2026-02-26 10:11:40', '2026-02-26 10:11:40', NULL),
(63, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (เหลือ 4 ครั้ง)', NULL, '2026-02-26 13:07:51', '2026-02-26 13:07:51', NULL),
(64, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (เหลือ 3 ครั้ง)', NULL, '2026-02-26 13:08:01', '2026-02-26 13:08:01', NULL),
(65, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (เหลือ 2 ครั้ง)', NULL, '2026-02-26 13:09:04', '2026-02-26 13:09:04', NULL),
(66, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (เหลือ 1 ครั้ง)', NULL, '2026-02-26 13:09:30', '2026-02-26 13:09:30', NULL),
(67, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อคเนื่องจากใส่รหัสผ่านผิดหลายครั้ง', NULL, '2026-02-26 13:09:35', '2026-02-26 13:09:35', NULL),
(68, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (เหลือ 4 ครั้ง)', NULL, '2026-02-26 13:13:56', '2026-02-26 13:13:56', NULL),
(69, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (เหลือ 3 ครั้ง)', NULL, '2026-02-26 13:14:08', '2026-02-26 13:14:08', NULL),
(70, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (เหลือ 2 ครั้ง)', NULL, '2026-02-26 13:18:36', '2026-02-26 13:18:36', NULL),
(71, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (เหลือ 1 ครั้ง)', NULL, '2026-02-26 13:19:22', '2026-02-26 13:19:22', NULL),
(72, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อคเนื่องจากใส่รหัสผ่านผิดหลายครั้ง', NULL, '2026-02-26 13:20:45', '2026-02-26 13:20:45', NULL),
(73, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2026-02-26 13:21:01', '2026-02-26 13:21:01', NULL),
(74, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2026-02-26 13:21:11', '2026-02-26 13:21:11', NULL),
(75, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 12 นาที', NULL, '2026-02-26 13:25:08', '2026-02-26 13:25:08', NULL),
(76, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 12 นาที', NULL, '2026-02-26 13:28:12', '2026-02-26 13:28:12', NULL),
(77, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'บัญชีถูกล็อค กรุณารอ 15 นาที', NULL, '2026-02-26 13:28:22', '2026-02-26 13:28:22', NULL),
(78, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (เหลือ 4 ครั้ง)', NULL, '2026-02-26 13:53:12', '2026-02-26 13:53:12', NULL),
(79, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-02-26 13:53:57', '2026-02-26 13:53:57', NULL),
(80, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 0, 'ชื่อผู้ใช้หรือรหัสผ่านไม่ถูกต้อง (เหลือ 4 ครั้ง)', NULL, '2026-02-26 14:00:21', '2026-02-26 14:00:21', NULL),
(81, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-02-26 14:00:38', '2026-02-26 14:00:38', NULL),
(82, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-02-26 14:02:21', '2026-02-26 14:02:21', NULL),
(83, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-02-26 20:34:35', '2026-02-26 20:34:35', NULL),
(84, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-02-26 23:00:00', '2026-02-26 23:00:00', NULL),
(85, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-02-27 09:12:44', NULL, NULL),
(86, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-02-27 22:40:27', NULL, NULL),
(87, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-03-09 00:16:27', NULL, NULL),
(88, 212, NULL, 'password', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36', NULL, NULL, NULL, NULL, NULL, 1, NULL, NULL, '2026-03-09 00:29:27', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_part`
--

CREATE TABLE `maintenance_part` (
  `id` int(11) NOT NULL,
  `maintenance_record_id` int(11) NOT NULL,
  `part_name` varchar(255) NOT NULL,
  `part_number` varchar(100) DEFAULT NULL,
  `quantity` int(11) DEFAULT 1,
  `unit_price` decimal(12,2) NOT NULL,
  `total_price` decimal(12,2) NOT NULL,
  `warranty_months` int(11) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_record`
--

CREATE TABLE `maintenance_record` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `maintenance_type_id` int(11) NOT NULL,
  `maintenance_date` date NOT NULL,
  `odometer_reading` int(11) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `work_performed` text DEFAULT NULL,
  `service_provider` varchar(255) DEFAULT NULL,
  `service_location` varchar(255) DEFAULT NULL,
  `technician_name` varchar(100) DEFAULT NULL,
  `labor_cost` decimal(12,2) DEFAULT 0.00,
  `parts_cost` decimal(12,2) DEFAULT 0.00,
  `total_cost` decimal(12,2) DEFAULT 0.00,
  `invoice_number` varchar(100) DEFAULT NULL,
  `invoice_image` varchar(255) DEFAULT NULL,
  `warranty_until` date DEFAULT NULL,
  `next_service_km` int(11) DEFAULT NULL,
  `next_service_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'completed',
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_schedule`
--

CREATE TABLE `maintenance_schedule` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `maintenance_type_id` int(11) NOT NULL,
  `scheduled_date` date NOT NULL,
  `scheduled_km` int(11) DEFAULT NULL,
  `priority` varchar(20) DEFAULT 'normal',
  `estimated_cost` decimal(12,2) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `status` varchar(20) DEFAULT 'pending',
  `is_overdue` tinyint(1) DEFAULT 0,
  `completed_at` datetime DEFAULT NULL,
  `maintenance_record_id` int(11) DEFAULT NULL,
  `reminder_sent` tinyint(1) DEFAULT 0,
  `reminder_sent_at` datetime DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `maintenance_type`
--

CREATE TABLE `maintenance_type` (
  `id` int(11) NOT NULL,
  `code` varchar(20) NOT NULL,
  `name` varchar(100) NOT NULL,
  `name_en` varchar(100) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT 'general',
  `default_interval_km` int(11) DEFAULT NULL,
  `default_interval_days` int(11) DEFAULT NULL,
  `estimated_duration_hours` decimal(5,2) DEFAULT NULL,
  `estimated_cost` decimal(12,2) DEFAULT NULL,
  `is_critical` tinyint(1) DEFAULT 0,
  `requires_specialist` tinyint(1) DEFAULT 0,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `status` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `maintenance_type`
--

INSERT INTO `maintenance_type` (`id`, `code`, `name`, `name_en`, `description`, `category`, `default_interval_km`, `default_interval_days`, `estimated_duration_hours`, `estimated_cost`, `is_critical`, `requires_specialist`, `sort_order`, `is_active`, `created_at`, `updated_at`, `status`) VALUES
(1, 'OIL_CHANGE', 'เปลี่ยนถ่ายน้ำมันเครื่อง', 'Oil Change', NULL, 'engine', 10000, 180, NULL, NULL, 0, 0, 1, 1, '2025-12-29 18:29:02', '2025-12-29 18:29:02', 1),
(2, 'OIL_FILTER', 'เปลี่ยนไส้กรองน้ำมัน', 'Oil Filter', NULL, 'engine', 10000, 180, NULL, NULL, 0, 0, 2, 1, '2025-12-29 18:29:02', '2025-12-29 18:29:02', 1),
(3, 'AIR_FILTER', 'เปลี่ยนไส้กรองอากาศ', 'Air Filter', NULL, 'engine', 20000, 365, NULL, NULL, 0, 0, 3, 1, '2025-12-29 18:29:02', '2025-12-29 18:29:02', 1),
(4, 'BRAKE_PAD', 'เปลี่ยนผ้าเบรก', 'Brake Pad', NULL, 'brakes', 30000, NULL, NULL, NULL, 1, 0, 4, 1, '2025-12-29 18:29:02', '2025-12-29 18:29:02', 1),
(5, 'BRAKE_FLUID', 'เปลี่ยนน้ำมันเบรก', 'Brake Fluid', NULL, 'brakes', 40000, 730, NULL, NULL, 1, 0, 5, 1, '2025-12-29 18:29:02', '2025-12-29 18:29:02', 1),
(6, 'TIRE_ROTATION', 'สลับยาง', 'Tire Rotation', NULL, 'tires', 10000, 180, NULL, NULL, 0, 0, 6, 1, '2025-12-29 18:29:02', '2025-12-29 18:29:02', 1),
(7, 'TIRE_REPLACE', 'เปลี่ยนยาง', 'Tire Replacement', NULL, 'tires', 50000, NULL, NULL, NULL, 1, 0, 7, 1, '2025-12-29 18:29:02', '2025-12-29 18:29:02', 1),
(8, 'BATTERY', 'เปลี่ยนแบตเตอรี่', 'Battery Replacement', NULL, 'electrical', NULL, 730, NULL, NULL, 1, 0, 8, 1, '2025-12-29 18:29:02', '2025-12-29 18:29:02', 1),
(9, 'AC_SERVICE', 'เช็คระบบแอร์', 'AC Service', NULL, 'climate', NULL, 365, NULL, NULL, 0, 0, 9, 1, '2025-12-29 18:29:02', '2025-12-29 18:29:02', 1),
(10, 'GENERAL_INSPECTION', 'ตรวจเช็คทั่วไป', 'General Inspection', NULL, 'inspection', 5000, 90, NULL, NULL, 0, 0, 10, 1, '2025-12-29 18:29:02', '2025-12-29 18:29:02', 1);

-- --------------------------------------------------------

--
-- Table structure for table `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m130524_201442_init', 1766197735),
('m140506_102106_rbac_init', 1767029410),
('m170907_052038_rbac_add_index_on_auth_assignment_user_id', 1767029410),
('m180523_151638_rbac_updates_indexes_without_prefix', 1767029410),
('m190124_110200_add_verification_token_column_to_user_table', 1767204056),
('m200409_110543_rbac_update_mssql_trigger', 1767029410),
('m240101_000001_create_budget_monitoring_tables', 1767205375),
('m240101_000001_create_car_vehicle_table', 1767029411),
('m240101_000002_create_car_driver_table', 1767029411),
('m240101_000003_create_car_reservation_table', 1767029411),
('m240101_000004_create_car_audit_table', 1766197735),
('m241220_090000_create_fuel_tracking_tables', 1767032942),
('m241220_100000_create_maintenance_tables', 1767032942),
('m241220_110000_create_gps_tracking_tables', 1767032942),
('m250001_000005_create_rbac_tables', 1767032942),
('m250001_000006_create_user_table', 1767032942),
('m250001_000007_create_security_tables', 1767032942),
('m250001_000008_create_audit_log_table', 1767106160),
('m250001_050000_init_rbac', 1767032942),
('m250101_100000_seed_users_and_data', 1767098688);

-- --------------------------------------------------------

--
-- Table structure for table `oauth2_client`
--

CREATE TABLE `oauth2_client` (
  `id` int(11) NOT NULL,
  `client_id` varchar(80) NOT NULL,
  `client_secret` varchar(255) NOT NULL,
  `redirect_uri` text DEFAULT NULL,
  `grant_types` varchar(100) DEFAULT NULL,
  `scope` varchar(255) DEFAULT NULL,
  `name` varchar(100) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `organizations`
--

CREATE TABLE `organizations` (
  `id` int(11) NOT NULL,
  `organization_code` varchar(20) NOT NULL,
  `organization_name` varchar(255) NOT NULL,
  `organization_name_en` varchar(255) DEFAULT NULL,
  `organization_type` varchar(50) DEFAULT 'department',
  `parent_id` int(11) DEFAULT NULL,
  `organization_level` int(11) DEFAULT 1,
  `organization_status` varchar(20) DEFAULT 'active',
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `website` varchar(255) DEFAULT NULL,
  `head_person` varchar(255) DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `projects`
--

CREATE TABLE `projects` (
  `id` int(11) NOT NULL,
  `project_code` varchar(30) NOT NULL,
  `project_name` varchar(500) NOT NULL,
  `project_description` text DEFAULT NULL,
  `project_objectives` text DEFAULT NULL,
  `expected_outcome` text DEFAULT NULL,
  `organization_id` int(11) DEFAULT NULL,
  `fiscal_year_id` int(11) DEFAULT NULL,
  `plan_id` int(11) DEFAULT NULL,
  `project_manager_id` int(11) DEFAULT NULL,
  `project_type` varchar(50) DEFAULT 'operational',
  `total_budget` decimal(15,2) DEFAULT 0.00,
  `approved_budget` decimal(15,2) DEFAULT 0.00,
  `client_name` varchar(255) DEFAULT NULL,
  `client_email` varchar(255) DEFAULT NULL,
  `start_date` date DEFAULT NULL,
  `end_date` date DEFAULT NULL,
  `project_status` varchar(30) DEFAULT 'draft',
  `approval_status` varchar(30) DEFAULT 'pending',
  `progress_percent` decimal(5,2) DEFAULT 0.00,
  `submitted_by` int(11) DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `approved_by` int(11) DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `project_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `project_budgets`
--

CREATE TABLE `project_budgets` (
  `id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL,
  `expense_category_id` int(11) NOT NULL,
  `budget_amount` decimal(15,2) NOT NULL DEFAULT 0.00,
  `used_amount` decimal(15,2) DEFAULT 0.00,
  `committed_amount` decimal(15,2) DEFAULT 0.00,
  `quarter_1_plan` decimal(15,2) DEFAULT 0.00,
  `quarter_2_plan` decimal(15,2) DEFAULT 0.00,
  `quarter_3_plan` decimal(15,2) DEFAULT 0.00,
  `quarter_4_plan` decimal(15,2) DEFAULT 0.00,
  `budget_status` varchar(20) DEFAULT 'active',
  `budget_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user`
--

CREATE TABLE `user` (
  `id` int(11) NOT NULL COMMENT 'รหัสผู้ใช้',
  `username` varchar(50) NOT NULL COMMENT 'ชื่อผู้ใช้',
  `email` varchar(100) NOT NULL COMMENT 'อีเมล',
  `password_hash` varchar(255) NOT NULL COMMENT 'รหัสผ่านเข้ารหัส',
  `password_reset_token` varchar(255) DEFAULT NULL COMMENT 'Token รีเซ็ตรหัสผ่าน',
  `auth_key` varchar(32) NOT NULL COMMENT 'Authentication key',
  `access_token` varchar(255) DEFAULT NULL COMMENT 'API access token',
  `title` varchar(50) DEFAULT NULL COMMENT 'คำนำหน้า',
  `full_name` varchar(255) DEFAULT NULL COMMENT 'ชื่อ-นามสกุล',
  `first_name` varchar(100) DEFAULT NULL COMMENT 'ชื่อ',
  `middle_name` varchar(100) DEFAULT NULL COMMENT 'ชื่อกลาง',
  `last_name` varchar(100) DEFAULT NULL COMMENT 'นามสกุล',
  `title_en` varchar(50) DEFAULT NULL COMMENT 'คำนำหน้า (EN)',
  `first_name_en` varchar(100) DEFAULT NULL COMMENT 'ชื่อ (EN)',
  `middle_name_en` varchar(100) DEFAULT NULL COMMENT 'ชื่อกลาง (EN)',
  `full_name_en` varchar(255) DEFAULT NULL COMMENT 'ชื่อ-นามสกุล (EN)',
  `phone` varchar(255) NOT NULL COMMENT 'เบอร์โทรศัพท์',
  `position` varchar(255) DEFAULT NULL COMMENT 'ตำแหน่ง',
  `employee_code` varchar(50) DEFAULT NULL COMMENT 'รหัสบุคลากร',
  `role` enum('superAdmin','admin','manager','user','viewer') DEFAULT NULL COMMENT 'บทบาท',
  `department` varchar(255) DEFAULT NULL COMMENT 'แผนก/ฝ่าย',
  `organization_id` int(11) DEFAULT NULL COMMENT 'หน่วยงาน',
  `email_verified_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'วันที่ยืนยันอีเมล',
  `verification_token` varchar(255) DEFAULT NULL COMMENT 'Token ยืนยันอีเมล',
  `last_login_at` timestamp NULL DEFAULT current_timestamp() COMMENT 'เข้าสู่ระบบครั้งล่าสุด',
  `last_login_ip` varchar(45) DEFAULT NULL COMMENT 'IP ล่าสุด',
  `failed_login_attempts` int(2) DEFAULT 0 COMMENT 'ครั้งล้มเหลวในการเข้าสู่ระบบ',
  `locked_until` timestamp NULL DEFAULT current_timestamp() COMMENT 'ล็อคจนถึง',
  `avatar` varchar(255) DEFAULT NULL COMMENT 'รูปโปรไฟล์',
  `oauth_provider` varchar(50) DEFAULT NULL COMMENT 'OAuth Provider',
  `azure_object_id` varchar(255) DEFAULT NULL COMMENT 'Microsoft Entra ID (Object ID)',
  `azure_upn` varchar(100) DEFAULT NULL COMMENT 'Azure UPN',
  `azure_synced_at` datetime DEFAULT NULL COMMENT 'Azure Sync Time',
  `must_change_password` tinyint(4) DEFAULT NULL COMMENT 'ต้องเปลี่ยนรหัสผ่าน',
  `password_changed_at` int(11) UNSIGNED DEFAULT NULL,
  `two_factor_enabled` tinyint(1) NOT NULL DEFAULT 0,
  `two_factor_secret` varchar(255) DEFAULT NULL,
  `status` smallint(6) DEFAULT 10 COMMENT 'สถานะผู้ใช้',
  `created_at` int(11) DEFAULT current_timestamp() COMMENT 'สร้างเมื่อ',
  `updated_at` int(11) DEFAULT NULL COMMENT 'แก้ไขเมื่อ',
  `created_by` int(11) DEFAULT NULL COMMENT 'สร้างโดย',
  `updated_by` int(11) DEFAULT NULL COMMENT 'แก้ไขโดย'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='ข้อมูลผู้ใช้งาน';

--
-- Dumping data for table `user`
--

INSERT INTO `user` (`id`, `username`, `email`, `password_hash`, `password_reset_token`, `auth_key`, `access_token`, `title`, `full_name`, `first_name`, `middle_name`, `last_name`, `title_en`, `first_name_en`, `middle_name_en`, `full_name_en`, `phone`, `position`, `employee_code`, `role`, `department`, `organization_id`, `email_verified_at`, `verification_token`, `last_login_at`, `last_login_ip`, `failed_login_attempts`, `locked_until`, `avatar`, `oauth_provider`, `azure_object_id`, `azure_upn`, `azure_synced_at`, `must_change_password`, `password_changed_at`, `two_factor_enabled`, `two_factor_secret`, `status`, `created_at`, `updated_at`, `created_by`, `updated_by`) VALUES
(211, 'superadmin', 'superadmin@bizai.co.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'RUFqxB0rkhWRtILjcq0UiokW-iWiz_Lo', NULL, '', '', 'ผู้ดูแลระบบ', NULL, 'สูงสุด', '', 'Super', NULL, 'Administrator', '02-590-1000', 'ผู้ดูแลระบบสูงสุด', 'ADMIN001', 'superAdmin', NULL, 1, '2025-12-30 12:44:48', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(212, 'admin', 'admin@bizai.co.th', '$argon2id$v=19$m=65536,t=4,p=1$OTlKLkt4NE5HYVY2bGdDMw$r49uZrjnFcmTAt7Ktxy0lVozRUi9mrKIteBrGhZo4aI', NULL, 'WhRRlmgLcjtfOaQIIFKUyMo5qI-86XrJ', NULL, '', 'ผู้ดูแลระบบ', 'ผู้ดูแลระบบ', NULL, '', '', 'System', NULL, 'System Administrator', '02-590-1001', 'ผู้ดูแลระบบ', 'EMP00001', 'admin', NULL, 1, '2025-12-30 12:44:48', NULL, '2026-03-08 17:29:27', '127.0.0.1', 0, NULL, 'avatar_212_1772424271_zqtdsR.jpg', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1772990967, NULL, NULL),
(213, 'manager_opbri', 'manager.opbri@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'N3sJgDFHnfd8t_zFZB_wwTgt7G_-TMNx', NULL, 'นาย', '0', 'วิชาญ', NULL, 'รักงาน', NULL, '', NULL, '', '02-590-1100', 'หัวหน้างานยานพาหนะ สนง.อธิการบดี', 'MGR001', 'manager', NULL, 1, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(214, 'staff_opbri', 'staff.opbri@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'cBv9KDuk6lsci9jZrRkp3EeplNjbzJXb', NULL, 'นางสาว', '0', 'สมหญิง', NULL, 'ตั้งใจ', NULL, 'สมหญิง ตั้งใจ', NULL, 'นางสาวสมหญิง ตั้งใจ', '02-590-2100', 'เจ้าหน้าที่ธุรการ สนง.อธิการบดี', 'STF001', 'user', NULL, 1, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(215, 'manager_policy', 'manager.policy@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'HZhhEsyUhU9Hb_c2mx04QJ-f0rXoxfRw', NULL, 'นาง', '0', 'สมศรี', NULL, 'ใจดี', NULL, '', NULL, '', '02-590-1101', 'หัวหน้างานยานพาหนะ ยุทธศาสตร์', 'MGR002', 'manager', NULL, 2, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(216, 'staff_policy', 'staff.policy@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'khJCg15aZMGnwCe9YnJ56NrwshuYQmx8', NULL, 'นาย', '0', 'สมบัติ', NULL, 'พร้อมเพรียง', NULL, 'สมบัติ พร้อมเพรียง', NULL, 'นายสมบัติ พร้อมเพรียง', '02-590-2101', 'เจ้าหน้าที่ธุรการ ยุทธศาสตร์', 'STF002', 'user', NULL, 2, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(217, 'manager_hr', 'manager.hr@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$SkpKZEZFZktUa01kLzBvaw$Ylq7gR1bCpP3pnIGT6z5/FNqvx9jkWZPKP6+vsDCuO8', NULL, 'X1-fnNWKEax73Dqg6uveY2yWgAnYutfq', NULL, 'นาย', 'สุรศักดิ์ ประเสริฐ (ผจก.)', 'สมชาย', NULL, 'มานะ', NULL, '', NULL, 'Somchai Poolsuk', '02-590-1102', 'ผู้อำนวยการสำนัก', 'EMP00006', 'manager', NULL, 3, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1772086650, NULL, NULL),
(218, 'staff_hr', 'staff.hr@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'R1xKV7dnlqBjvCmOVlaIJR4llRFyGU0P', NULL, 'นางสาว', '0', 'พรทิพย์', NULL, 'สุขสันต์', NULL, 'พรทิพย์ สุขสันต์', NULL, 'นางสาวพรทิพย์ สุขสันต์', '02-590-2102', 'เจ้าหน้าที่ธุรการ บุคคล', 'STF003', 'user', NULL, 3, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(219, 'manager_finance', 'manager.finance@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, '9mBNCnQJPrvGCpHfL2IVnsMRGAVUtAPQ', NULL, 'นางสาว', '0', 'วิภา', NULL, 'เก่งกาจ', NULL, '', NULL, '', '02-590-1103', 'หัวหน้างานยานพาหนะ การคลัง', 'MGR004', 'manager', NULL, 4, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(220, 'staff_finance', 'staff.finance@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'KuFtQfUJ8D9PJ7JdP2mS_QB9JwI62neQ', NULL, 'นาย', '0', 'ธนา', NULL, 'รุ่งเรือง', NULL, 'ธนา รุ่งเรือง', NULL, 'นายธนา รุ่งเรือง', '02-590-2103', 'เจ้าหน้าที่ธุรการ การคลัง', 'STF004', 'user', NULL, 4, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(221, 'manager_dtai', 'manager.dtai@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'Zl46ElIcyos0Kt5hjxEm2DU3bqFf6e9L', NULL, 'นาย', '0', 'ประเสริฐ', NULL, 'ยอดเยี่ยม', NULL, '', NULL, '', '02-590-1104', 'หัวหน้างานยานพาหนะ ดิจิทัล', 'MGR005', 'manager', NULL, 5, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(222, 'staff_dtai', 'staff.dtai@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'F224W1OS1P79-S7YuidrXNQJCAzF_-oR', NULL, 'นางสาว', '0', 'มณี', NULL, 'แจ่มใส', NULL, 'มณี แจ่มใส', NULL, 'นางสาวมณี แจ่มใส', '02-590-2104', 'เจ้าหน้าที่ธุรการ ดิจิทัล', 'STF005', 'user', NULL, 5, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(223, 'manager_acad', 'manager.acad@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$Zzh5NkJIOS5xRmhRcWE3WQ$O/nHHW/qeZhftrw/qO/W/WO2wtS4wM5ZXWHIGyBLNG8', NULL, 'GICpwULu_1FRsv-gbRmhzOewl5O5eX_6', NULL, 'นาง', 'วรรณา วงศ์สุข (ผจก.)', 'มาลี', NULL, 'สุขใจ', NULL, '', NULL, 'Kittiya Somboon', '02-590-1105', 'ผู้อำนวยการกอง', 'EMP00021', 'manager', NULL, 6, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1772086652, NULL, NULL),
(224, 'staff_acad', 'staff.acad@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'xFzISmihgbnWNmeHZ55PkQVVo66wNxGM', NULL, 'นาย', '0', 'พิทักษ์', NULL, 'เอาใจใส่', NULL, 'พิทักษ์ เอาใจใส่', NULL, 'นายพิทักษ์ เอาใจใส่', '02-590-2105', 'เจ้าหน้าที่ธุรการ วิชาการ', 'STF006', 'user', NULL, 6, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(225, 'manager_central', 'manager.central@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'UChDYnxPUlkYNe_QURHfspQ9eRRsJBhD', NULL, 'นาย', '0', 'สุชาติ', NULL, 'ทำดี', NULL, '', NULL, '', '02-590-1106', 'หัวหน้างานยานพาหนะ กองกลาง', 'MGR007', 'manager', NULL, 7, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(226, 'staff_central', 'staff.central@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'ig3k5R2iHCZ_r23arEBtWtW6EVAYpSdH', NULL, 'นางสาว', '0', 'ดวงใจ', NULL, 'ใฝ่ดี', NULL, 'ดวงใจ ใฝ่ดี', NULL, 'นางสาวดวงใจ ใฝ่ดี', '02-590-2106', 'เจ้าหน้าที่ธุรการ กองกลาง', 'STF007', 'user', NULL, 7, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(227, 'manager_qa', 'manager.qa@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$ekJmUnNlcG41Ujd5T2VjcA$/YrfvLyBvFcdpCsA9jLZIBuGiJSNicVV/C240cGfuHM', NULL, '9j1AlGL6ZXBbP2awk-RpRXU0WPXRn7Ma', NULL, 'นางสาว', 'สมชาย ทองดี (ผจก.)', 'พิมพ์', NULL, 'งามตา', NULL, '', NULL, 'Wanna Poolsuk', '02-590-1107', 'รองผู้อำนวยการ', 'EMP00031', 'manager', NULL, 8, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1772086654, NULL, NULL),
(228, 'staff_qa', 'staff.qa@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'REr6lyWBxNnWnYPH5YnLRbvnl29LwsZ1', NULL, 'นาย', '0', 'ภูมิ', NULL, 'พิทักษ์', NULL, 'ภูมิ พิทักษ์', NULL, 'นายภูมิ พิทักษ์', '02-590-2107', 'เจ้าหน้าที่ธุรการ ส่งเสริม', 'STF008', 'user', NULL, 8, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(229, 'manager_bcnnon', 'manager.bcnnon@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, '6Ucr8EHFPPXYX0gmvO9PmFWFpF0RJU5B', NULL, 'นาย', '0', 'ชัยวัฒน์', NULL, 'เจริญ', NULL, '', NULL, '', '02-590-1108', 'หัวหน้างานยานพาหนะ วพบ.นนทบุรี', 'MGR009', 'manager', NULL, 9, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(230, 'staff_bcnnon', 'staff.bcnnon@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, '1k88YphpmM4ThbiA0ni1g2KT8rHOt232', NULL, 'นางสาว', '0', 'แก้ว', NULL, 'ตาสว่าง', NULL, 'แก้ว ตาสว่าง', NULL, 'นางสาวแก้ว ตาสว่าง', '02-590-2108', 'เจ้าหน้าที่ธุรการ วพบ.นนทบุรี', 'STF009', 'user', NULL, 9, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(231, 'manager_bcnbkk', 'manager.bcnbkk@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, '2iYn8__jlnnJogQQ6cm6V7J0wPYIpfPs', NULL, 'นาง', '0', 'นิภา', NULL, 'สว่าง', NULL, '', NULL, '', '02-590-1109', 'หัวหน้างานยานพาหนะ วพบ.กรุงเทพ', 'MGR010', 'manager', NULL, 10, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(232, 'staff_bcnbkk', 'staff.bcnbkk@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'o__Tn-1JyETxx2Vou-cJ7yPtwgCMd-xh', NULL, 'นาย', '0', 'เอก', NULL, 'ยืนหยัด', NULL, 'เอก ยืนหยัด', NULL, 'นายเอก ยืนหยัด', '02-590-2109', 'เจ้าหน้าที่ธุรการ วพบ.กรุงเทพ', 'STF010', 'user', NULL, 10, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(233, 'manager_bcncm', 'manager.bcncm@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'eF3vCHQNZ6jlLUk24NxgmIc9mKJMxaTg', NULL, 'นาย', '0', 'อนุชา', NULL, 'แข็งขัน', NULL, '', NULL, '', '02-590-1110', 'หัวหน้างานยานพาหนะ วพบ.เชียงใหม่', 'MGR011', 'manager', NULL, 11, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(234, 'staff_bcncm', 'staff.bcncm@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'jJjiAgY1CYzqr2N-Yd3UHwuUGN9tQeki', NULL, 'นางสาว', '0', 'ฝน', NULL, 'ชื่นใจ', NULL, 'ฝน ชื่นใจ', NULL, 'นางสาวฝน ชื่นใจ', '02-590-2110', 'เจ้าหน้าที่ธุรการ วพบ.เชียงใหม่', 'STF011', 'user', NULL, 11, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(235, 'manager_bcnkk', 'manager.bcnkk@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, '80DK9uF4HWnWQqmjyAzNFKqJkcW65rJE', NULL, 'นางสาว', '0', 'รัตนา', NULL, 'สดใส', NULL, '', NULL, '', '02-590-1111', 'หัวหน้างานยานพาหนะ วพบ.ขอนแก่น', 'MGR012', 'manager', NULL, 12, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(236, 'staff_bcnkk', 'staff.bcnkk@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'FYwDZn0_vcaZ2PMZu_oV4qnOsMPTR3jJ', NULL, 'นาย', '0', 'ไผ่', NULL, 'แข็งแรง', NULL, 'ไผ่ แข็งแรง', NULL, 'นายไผ่ แข็งแรง', '02-590-2111', 'เจ้าหน้าที่ธุรการ วพบ.ขอนแก่น', 'STF012', 'user', NULL, 12, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(237, 'manager_bcnsk', 'manager.bcnsk@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'sINl9dWg1z6PzXldNRHWWEI0aMxrbKCt', NULL, 'นาย', '0', 'ธีรพงษ์', NULL, 'มั่นคง', NULL, '', NULL, '', '02-590-1112', 'หัวหน้างานยานพาหนะ วพบ.สงขลา', 'MGR013', 'manager', NULL, 13, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(238, 'staff_bcnsk', 'staff.bcnsk@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'k5o0fb04rtGIU2vOckmzAgWBVNfhcQSd', NULL, 'นางสาว', '0', 'น้ำ', NULL, 'ใส', NULL, 'น้ำ ใส', NULL, 'นางสาวน้ำ ใส', '02-590-2112', 'เจ้าหน้าที่ธุรการ วพบ.สงขลา', 'STF013', 'user', NULL, 13, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(239, 'manager_bcnnk', 'manager.bcnnk@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'q7M1jdnY6zPyogsOtVFkdwVX1mHjYNTe', NULL, 'นาง', '0', 'จินตนา', NULL, 'รอบคอบ', NULL, '', NULL, '', '02-590-1113', 'หัวหน้างานยานพาหนะ วพบ.นครราชสีมา', 'MGR014', 'manager', NULL, 14, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(240, 'staff_bcnnk', 'staff.bcnnk@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'VSprr7uJ4V1Jw3lRaiJk8TkTp2Bd0fYe', NULL, 'นาย', '0', 'ลม', NULL, 'เย็น', NULL, 'ลม เย็น', NULL, 'นายลม เย็น', '02-590-2113', 'เจ้าหน้าที่ธุรการ วพบ.นครราชสีมา', 'STF014', 'user', NULL, 14, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(241, 'manager_scphkk', 'manager.scphkk@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'rbb5okC5hEl3conPjz6Sudk-nFqN3XBb', NULL, 'นาย', '0', 'วรวุฒิ', NULL, 'กล้าหาญ', NULL, '', NULL, '', '02-590-1114', 'หัวหน้างานยานพาหนะ วสส.ขอนแก่น', 'MGR015', 'manager', NULL, 15, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(242, 'staff_scphkk', 'staff.scphkk@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'omDouisAA8sZS8IbOtOBQAL3LqELdcBl', NULL, 'นางสาว', '0', 'ไฟ', NULL, 'อบอุ่น', NULL, 'ไฟ อบอุ่น', NULL, 'นางสาวไฟ อบอุ่น', '02-590-2114', 'เจ้าหน้าที่ธุรการ วสส.ขอนแก่น', 'STF015', 'user', NULL, 15, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(243, 'manager_scphub', 'manager.scphub@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'CoTqM7XGEgwS2RE7B52fP3lKM_niCc7u', NULL, 'นางสาว', '0', 'ปิยะ', NULL, 'อ่อนโยน', NULL, '', NULL, '', '02-590-1115', 'หัวหน้างานยานพาหนะ วสส.อุบล', 'MGR016', 'manager', NULL, 16, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(244, 'staff_scphub', 'staff.scphub@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'Ywa8XTENphxujnxMtkmZEXtnlwGe23-X', NULL, 'นาย', '0', 'ดิน', NULL, 'มั่นคง', NULL, 'ดิน มั่นคง', NULL, 'นายดิน มั่นคง', '02-590-2115', 'เจ้าหน้าที่ธุรการ วสส.อุบล', 'STF016', 'user', NULL, 16, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(245, 'manager_scphsp', 'manager.scphsp@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'GV_RyXuA6ZMOB4bx8g0emvOuZy4LOXx7', NULL, 'นาย', '0', 'กิตติ', NULL, 'ซื่อสัตย์', NULL, '', NULL, '', '02-590-1116', 'หัวหน้างานยานพาหนะ วสส.สุพรรณ', 'MGR017', 'manager', NULL, 17, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(246, 'staff_scphsp', 'staff.scphsp@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'viWTS08JYFAcRF7BGyzftrUV9NIZpmql', NULL, 'นางสาว', '0', 'ทอง', NULL, 'เรืองรอง', NULL, 'ทอง เรืองรอง', NULL, 'นางสาวทอง เรืองรอง', '02-590-2116', 'เจ้าหน้าที่ธุรการ วสส.สุพรรณ', 'STF017', 'user', NULL, 17, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(247, 'manager_scphcb', 'manager.scphcb@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'o47mY9eN6lOQ0PCxk2nTMt0-C2kpu0_p', NULL, 'นาง', '0', 'อรุณี', NULL, 'เบิกบาน', NULL, '', NULL, '', '02-590-1117', 'หัวหน้างานยานพาหนะ วสส.ชลบุรี', 'MGR018', 'manager', NULL, 18, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(248, 'staff_scphcb', 'staff.scphcb@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'wXBbX-hdEQzdIjWQNAHGNK9zJXGehotH', NULL, 'นาย', '0', 'เงิน', NULL, 'งอกเงย', NULL, 'เงิน งอกเงย', NULL, 'นายเงิน งอกเงย', '02-590-2117', 'เจ้าหน้าที่ธุรการ วสส.ชลบุรี', 'STF018', 'user', NULL, 18, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(249, 'manager_scphtr', 'manager.scphtr@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'k2sL24eZ7x7FQChf0jybFlWLNSVIpesv', NULL, 'นาย', '0', 'ณัฐพล', NULL, 'ฉลาด', NULL, '', NULL, '', '02-590-1118', 'หัวหน้างานยานพาหนะ วสส.ตรัง', 'MGR019', 'manager', NULL, 19, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(250, 'staff_scphtr', 'staff.scphtr@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'ErbhdVOHHjwU8ALr-JfbojXFv4REy70c', NULL, 'นางสาว', '0', 'เพชร', NULL, 'ประกาย', NULL, 'เพชร ประกาย', NULL, 'นางสาวเพชร ประกาย', '02-590-2118', 'เจ้าหน้าที่ธุรการ วสส.ตรัง', 'STF019', 'user', NULL, 19, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(251, 'manager_scphys', 'manager.scphys@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, '0pCNBW0GggM-H2T40EVu2B6aKVCi5tf1', NULL, 'นางสาว', '0', 'สุนิสา', NULL, 'ขยัน', NULL, '', NULL, '', '02-590-1119', 'หัวหน้างานยานพาหนะ วสส.ยะลา', 'MGR020', 'manager', NULL, 20, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(252, 'staff_scphys', 'staff.scphys@pbri.ac.th', '$2y$13$KuuUaR82c4JEu945x2vm1.jNsLASX7hoRVnofgAg4rtWHfZ3o8Ira', NULL, 'Qgi5O78eyySSScyN5stct0DcB6Su7WmW', NULL, 'นาย', '0', 'พลอย', NULL, 'สดใส', NULL, 'พลอย สดใส', NULL, 'นายพลอย สดใส', '02-590-2119', 'เจ้าหน้าที่ธุรการ วสส.ยะลา', 'STF020', 'user', NULL, 20, '2025-12-30 12:44:48', NULL, '2025-12-30 12:44:48', NULL, 0, '2025-12-30 12:44:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767098688, 1767098688, NULL, NULL),
(253, 'manager_ict', 'manager.ict@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$Y25GMVI5UjdxU3AyUi5Zbw$ruj+bMeBeQEszpWWblac89EpUdufQCTW02pm671AWME', NULL, 'MWiQ3j-d__eVHdvZ3__5r_6sLn8jjJQR', NULL, NULL, 'สมชาย สันติสุข (ผจก.)', NULL, NULL, NULL, NULL, NULL, NULL, 'Thanida Wongsuk', '02-590-2644', 'ผู้อำนวยการกอง', 'EMP00002', NULL, NULL, 2, '2026-01-01 06:27:48', NULL, '2026-01-01 06:27:48', NULL, 0, '2026-01-01 06:27:48', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248868, 1772086649, NULL, NULL),
(254, 'approver_ict', 'approver.ict@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$bnVGbHg5cGlMaEJZZmNUcQ$PUbCu9lQ4nQJo5OleCBkHGeYGHmPW30a/8N8D0emaYQ', NULL, 'rtvOmgNXDxYKVYySNu0sxZ50vr6_aGZs', NULL, NULL, 'กรกฎ รุ่งเรือง', NULL, NULL, NULL, NULL, NULL, NULL, 'Kittiya Sangsuk', '02-590-4087', 'นักวิชาการชำนาญการพิเศษ', 'EMP00003', NULL, NULL, 2, '2026-01-01 06:27:48', NULL, '2026-03-08 17:15:03', '127.0.0.1', 0, NULL, 'avatar_254_1767276866.jpg', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248868, 1772086649, NULL, NULL),
(255, 'approver_ict02', 'approver.ict02@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$Z05vQzQ3dXpuTDVMTFdLbA$2+BZccPLmW6/tXBOx57iB3r9B3DA4sb5mOjpzsMAu1c', NULL, 'Z5rsvWtP9II8Zo3Jr3eOWp1WsS5qC5SE', NULL, NULL, 'สุชาติ กฎหมายสุข', NULL, NULL, NULL, NULL, NULL, NULL, 'Supaporn Chaiyasuk', '02-590-1674', 'หัวหน้างาน', 'EMP00004', NULL, NULL, 2, '2026-01-01 06:27:49', NULL, '2026-01-01 06:27:49', NULL, 0, '2026-01-01 06:27:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248869, 1772075157, NULL, NULL),
(256, 'user_ict01', 'user.ict01@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$R2FwdTVHdmRsNGh5UlcwSA$ut14UsFM8k3O70FpT679IvwtfPWRf/Mcw3FcIqTr1ZY', NULL, 'IZ6aH4OqiULBQpMYNG9b4TChGy4hS6tq', NULL, NULL, 'ชัยรัตน์ มีสุข', NULL, NULL, NULL, NULL, NULL, NULL, 'Surasak Somboon', '02-590-8191', 'นักวิชาการ', 'EMP00004', NULL, NULL, 2, '2026-01-01 06:27:49', NULL, '2026-02-26 03:11:56', '127.0.0.1', 0, NULL, 'avatar_256_1772051003.jpg', NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248869, 1772086650, NULL, NULL),
(257, 'user_ict02', 'user.ict02@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$SmxiL2llUkplYkhIZmk5dQ$RZ0ExrLN1lSXbJys897adWQ5zBwNk61qAt0LvweMGrc', NULL, 'DrjYYTOIaGpv4V2o5juiVlC6Px7dSRIs', NULL, NULL, 'ภาณุ วงศ์สุข', NULL, NULL, NULL, NULL, NULL, NULL, 'Anucha Suksawat', '02-590-6426', 'เจ้าพนักงาน', 'EMP00005', NULL, NULL, 2, '2026-01-01 06:27:49', NULL, '2026-01-01 06:27:49', NULL, 0, '2026-01-01 06:27:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248869, 1772086650, NULL, NULL),
(258, 'approver_hr', 'approver.hr@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$c0N0YWFVY2RwaC54bXpoTQ$0RJoxvuzGVvlN5wBamdlVWjR7+6sPgoqZPb+taWU+ks', NULL, 'bP75glt_wopkZ-GYYOYIH3OpQ91ir_ey', NULL, NULL, 'กรกฎ พรหมพันธ์', NULL, NULL, NULL, NULL, NULL, NULL, 'Somchai Chaiyasuk', '02-590-3972', 'หัวหน้างาน', 'EMP00007', NULL, NULL, 3, '2026-01-01 06:27:49', NULL, '2026-01-01 06:27:49', NULL, 0, '2026-01-01 06:27:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248869, 1772086650, NULL, NULL),
(259, 'user_hr01', 'user.hr01@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$N29pa1FNbnliL1d5VkFCLw$vh52GL9i9iDzXbyG6/Ow0bsUU35mbYUECDOB1HchKqY', NULL, 'IkV9L_HHPfEpGUapB7HO9TYttT1NpRld', NULL, NULL, 'กรกฎ วัฒนา', NULL, NULL, NULL, NULL, NULL, NULL, 'Wanna Somboon', '02-590-2718', 'เจ้าพนักงาน', 'EMP00008', NULL, NULL, 3, '2026-01-01 06:27:49', NULL, '2026-01-01 06:27:49', NULL, 0, '2026-01-01 06:27:49', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248869, 1772086650, NULL, NULL),
(260, 'user_hr02', 'user.hr02@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$T09OSVBDcnhianhFbVg3Ug$j9mu9iNceb5MutZusMCHGWOUOS77o7IGZ4JH3x231es', NULL, 'X_PNX8MUj1ZWz8Vk_Zfvx3tzi4iZRHwt', NULL, NULL, 'สมหญิง ทองดี', NULL, NULL, NULL, NULL, NULL, NULL, 'Supaporn Meesuk', '02-590-3753', 'นักวิเคราะห์นโยบายและแผน', 'EMP00009', NULL, NULL, 3, '2026-01-01 06:27:50', NULL, '2026-01-01 06:27:50', NULL, 0, '2026-01-01 06:27:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248870, 1772086650, NULL, NULL),
(261, 'manager_fin', 'manager.fin@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$L0ovYWZLREduTHdJZG8yaw$LK+LiDKzChss4c+NJZV99DRkOzWWkpPDvqVFuzEzVJc', NULL, 'ZElc1jpXe9MUvmsJKLwnoa8ujUNEoToV', NULL, NULL, 'ธนิดา ยิ้มแย้ม (ผจก.)', NULL, NULL, NULL, NULL, NULL, NULL, 'Kittiya Poolsuk', '02-590-3682', 'ผู้อำนวยการสำนัก', 'EMP00011', NULL, NULL, 4, '2026-01-01 06:27:50', NULL, '2026-01-01 06:27:50', NULL, 0, '2026-01-01 06:27:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248870, 1772086651, NULL, NULL),
(262, 'approver_fin', 'approver.fin@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$MXl4SkREblpNaUFSZXhTbw$L/kbUKZELBpDjBAffrbzF8jRWY//7XDzvx4rtMbIDe4', NULL, 'tyAdtDDh1w971a5jTburCGHXgWfdDq23', NULL, NULL, 'รัตนาภรณ์ ศรีสุข', NULL, NULL, NULL, NULL, NULL, NULL, 'Surasak Munkong', '02-590-1027', 'นักวิชาการชำนาญการพิเศษ', 'EMP00012', NULL, NULL, 4, '2026-01-01 06:27:50', NULL, '2026-01-01 06:27:50', NULL, 0, '2026-01-01 06:27:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248870, 1772086651, NULL, NULL),
(263, 'approver_fin02', 'approver.fin02@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$VHc1Q3NFaWYuZVRIVHVjLg$tHOhZvb6rpYTjZY1LIhpUdnGIicrloA1XxPIWmKmQbc', NULL, 'Hwm9BUPBgOOQslUIhSZp44f3oOczOmoq', NULL, NULL, 'วิชัย ศรีสุข', NULL, NULL, NULL, NULL, NULL, NULL, 'Wanna Poolsuk', '02-590-9965', 'หัวหน้างาน', 'EMP00013', NULL, NULL, 4, '2026-01-01 06:27:50', NULL, '2026-01-01 06:27:50', NULL, 0, '2026-01-01 06:27:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248870, 1772075276, NULL, NULL),
(264, 'user_fin01', 'user.fin01@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$Y1p6QUxVaTlIVXZOLm5VVQ$ovFvgLAURcLb/1h3Z4hkbAxpbh9CUyoQ1ssr9/6HjaQ', NULL, 'UsPTLRRMSka96K0nux82d4Z0xylqIf1i', NULL, NULL, 'กรกมล พิทักษ์', NULL, NULL, NULL, NULL, NULL, NULL, 'Kittiya Yimyam', '02-590-3406', 'นักวิเคราะห์นโยบายและแผน', 'EMP00013', NULL, NULL, 4, '2026-01-01 06:27:50', NULL, '2026-01-01 06:27:50', NULL, 0, '2026-01-01 06:27:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248870, 1772086651, NULL, NULL),
(265, 'user_fin02', 'user.fin02@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$NkI1ek1MUGdUY3ZZcHlFbA$gynKFGBZGdrFSBdcvCfAa4XdA9YhnnYKbTsXh7w+tCA', NULL, 'w2QfYypIWij5MupOZ8rRFhrqbv24JHa7', NULL, NULL, 'พงศ์พิชา ประเสริฐ', NULL, NULL, NULL, NULL, NULL, NULL, 'Surasak Somboon', '02-590-6459', 'นักวิเคราะห์นโยบายและแผน', 'EMP00014', NULL, NULL, 4, '2026-01-01 06:27:50', NULL, '2026-01-01 06:27:50', NULL, 0, '2026-01-01 06:27:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248870, 1772086651, NULL, NULL),
(266, 'manager_admin', 'manager.admin@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$Y2VYekI2T29sa284SnlaZw$+DxPS8YBEmoobdy2pfojzZFaCtnswXe7DBdII6sJe3E', NULL, 'jc1sHJetkMw7eNm0kDXCjN4N2J2h1lBU', NULL, NULL, 'ณัฐิดา รักดี (ผจก.)', NULL, NULL, NULL, NULL, NULL, NULL, 'Wanna Somboon', '02-590-2878', 'ผู้อำนวยการสำนัก', 'EMP00016', NULL, NULL, 5, '2026-01-01 06:27:50', NULL, '2026-01-01 06:27:50', NULL, 0, '2026-01-01 06:27:50', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248870, 1772086651, NULL, NULL),
(267, 'approver_admin', 'approver.admin@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$aXk3Zkw3bDJaYUdmd3R6Tw$JTmNVlHLa+wlOzB6cEnJsCSgK/WbgqxVYyr30XKbNwk', NULL, '0SmbG6uG3B98eBp-pKI4bE8SoClo6ME4', NULL, NULL, 'วิไล มั่นคง', NULL, NULL, NULL, NULL, NULL, NULL, 'Suchat Meesuk', '02-590-2021', 'นักวิชาการชำนาญการพิเศษ', 'EMP00017', NULL, NULL, 5, '2026-01-01 06:27:51', NULL, '2026-01-01 06:27:51', NULL, 0, '2026-01-01 06:27:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248871, 1772086652, NULL, NULL),
(268, 'approver_admin02', 'approver.admin02@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$aUFteUdPQmoyeUlhRlZSTA$mtsbCKkQAhH15x0tIJrXR3vb79Bm7VdhrUdWYUSX8xM', NULL, 'JsvMIGYvOmQV78mOxIw7u2-yX7tRVAo2', NULL, NULL, 'ชัยวัฒน์ ยิ้มแย้ม', NULL, NULL, NULL, NULL, NULL, NULL, 'Thanida Suksawat', '02-590-2675', 'นักวิชาการชำนาญการพิเศษ', 'EMP00019', NULL, NULL, 5, '2026-01-01 06:27:51', NULL, '2026-01-01 06:27:51', NULL, 0, '2026-01-01 06:27:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248871, 1772075277, NULL, NULL),
(269, 'user_admin01', 'user.admin01@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$UUt5VFpsWUQuZy5oTUJ5bw$TR7vXam3xBYoIcd2gZOdwBMl8pbmfOEZ/GUGmA5Bkw8', NULL, 'gXw6e4DftmtRCbd-9mtk9_yl6_Ry_hgL', NULL, NULL, 'วิชัย รุ่งเรือง', NULL, NULL, NULL, NULL, NULL, NULL, 'Pranee Somboon', '02-590-4979', 'นักจัดการงานทั่วไป', 'EMP00018', NULL, NULL, 5, '2026-01-01 06:27:51', NULL, '2026-01-01 06:27:51', NULL, 0, '2026-01-01 06:27:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248871, 1772086652, NULL, NULL),
(270, 'user_admin02', 'user.admin02@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$bVFTSTBrQmNUYjlCOHJkeA$NBloVsEkaKaIsGLxMKSAW4YvLhGNRkLy55jQmQLg5T0', NULL, 'sdOb7Dyl6JeR1CbL03hZwnbHXoAlc1S4', NULL, NULL, 'วรรณา มีสุข', NULL, NULL, NULL, NULL, NULL, NULL, 'Supaporn Poolsuk', '02-590-3964', 'นักวิเคราะห์นโยบายและแผน', 'EMP00019', NULL, NULL, 5, '2026-01-01 06:27:51', NULL, '2026-01-01 06:27:51', NULL, 0, '2026-01-01 06:27:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248871, 1772086652, NULL, NULL),
(271, 'approver_acad', 'approver.acad@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$b3RtcExFYVVQU3JrTmlPQw$STNNOvTT5JjqONY+CqIYxG+OJyuljRgzjqXXoFZh8X4', NULL, 'kG67cmrP3LIAsVOenQfgW7FfaKB8bSVa', NULL, NULL, 'พิศมัย สันติสุข', NULL, NULL, NULL, NULL, NULL, NULL, 'Siriwan Charoensuk', '02-590-8220', 'นักวิชาการชำนาญการพิเศษ', 'EMP00022', NULL, NULL, 6, '2026-01-01 06:27:51', NULL, '2026-01-01 06:27:51', NULL, 0, '2026-01-01 06:27:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248871, 1772086652, NULL, NULL),
(272, 'user_acad01', 'user.acad01@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$SFpPYWJTR0Rwb3M1R0I5Tg$/x+UsRGImckRqUCQnI2lT7uxiGMb3pUnXXwlFganLdQ', NULL, 'W-xN1ATKZNcT4aY0lSSxmQGMKyCxNhsC', NULL, NULL, 'พิศาล รักดี', NULL, NULL, NULL, NULL, NULL, NULL, 'Kittiya Rakdee', '02-590-4214', 'เจ้าพนักงาน', 'EMP00023', NULL, NULL, 6, '2026-01-01 06:27:51', NULL, '2026-01-01 06:27:51', NULL, 0, '2026-01-01 06:27:51', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248871, 1772086653, NULL, NULL),
(273, 'user_acad02', 'user.acad02@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$M2hscE5Qb3M0NHhTajFDVA$zIPFEYblQOLWh2gerxmcFlbETgACBVwSYyPqQU0mNmA', NULL, 'elsU40zi3l1b08jlzixGSE-clTVWn21c', NULL, NULL, 'สมชาย ยิ้มแย้ม', NULL, NULL, NULL, NULL, NULL, NULL, 'Siriwan Chaiyasuk', '02-590-6612', 'นักจัดการงานทั่วไป', 'EMP00024', NULL, NULL, 6, '2026-01-01 06:27:52', NULL, '2026-01-01 06:27:52', NULL, 0, '2026-01-01 06:27:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248872, 1772086653, NULL, NULL),
(274, 'manager_plan', 'manager.plan@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$ZzMwVmpiZ0hsM29xWGNXOA$oht21pLu14qzeieC8vZJQw55shZHywmq847p6iy0nWU', NULL, 'if4jf9Lz6V4En4m40XrRM626irXZUfOF', NULL, NULL, 'ภาณุ เจริญสุข (ผจก.)', NULL, NULL, NULL, NULL, NULL, NULL, 'Wichai Charoensuk', '02-590-6582', 'รองผู้อำนวยการ', 'EMP00026', NULL, NULL, 7, '2026-01-01 06:27:52', NULL, '2026-01-01 06:27:52', NULL, 0, '2026-01-01 06:27:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248872, 1772086653, NULL, NULL),
(275, 'approver_plan', 'approver.plan@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$NDNYUUg0enFscjUyTjBoVQ$ObxCAWr/65H5g2U3vEQ5ZlLEcMEiCeCoIwgDOYNt60A', NULL, 'JuLuI-fVSpAWOkEFkWcGj-k_u0s85iu8', NULL, NULL, 'พิศาล ประเสริฐ', NULL, NULL, NULL, NULL, NULL, NULL, 'Surasak Rakdee', '02-590-6734', 'หัวหน้างาน', 'EMP00027', NULL, NULL, 7, '2026-01-01 06:27:52', NULL, '2026-01-01 06:27:52', NULL, 0, '2026-01-01 06:27:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248872, 1772086653, NULL, NULL),
(276, 'user_plan01', 'user.plan01@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$dE1ER2lNbS5NNkxJaVREZg$ua9xzPntGTQjbEJSt/+4mKPsbcvKqiyB+2nvRagtjU4', NULL, '3-nmBa3p3xilqeeV2Z6ull9CErAPJtHQ', NULL, NULL, 'พิศาล รุ่งเรือง', NULL, NULL, NULL, NULL, NULL, NULL, 'Chaiwat Poolsuk', '02-590-7221', 'นักวิชาการ', 'EMP00028', NULL, NULL, 7, '2026-01-01 06:27:52', NULL, '2026-01-01 06:27:52', NULL, 0, '2026-01-01 06:27:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248872, 1772086653, NULL, NULL),
(277, 'user_plan02', 'user.plan02@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$di9xWlBTcy9Ma3ZYelNtVQ$de6G7FI/dXuSRUMw8xjzPW6TOo86l+of5W5Vw6PcMpw', NULL, '0sAGpOcTJNT8uMC9chg1eAiEd3V-YIcp', NULL, NULL, 'ชัยรัตน์ พรหมพันธ์', NULL, NULL, NULL, NULL, NULL, NULL, 'Somchai Chaiyasuk', '02-590-4004', 'นักจัดการงานทั่วไป', 'EMP00029', NULL, NULL, 7, '2026-01-01 06:27:52', NULL, '2026-01-01 06:27:52', NULL, 0, '2026-01-01 06:27:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248872, 1772086654, NULL, NULL),
(278, 'user_plan03', 'user.plan03@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$c2VWcHZER2FTckt3d1Z4TQ$X12Xgw76rHcjxVNQ6QBoDZnXWP0e1j1H7HDQ/yv7ZSw', NULL, 'mkuoPHuYckMpze-PrGA9oaGbmV9i8Xjv', NULL, NULL, 'พงศ์ ศรีสุข', NULL, NULL, NULL, NULL, NULL, NULL, 'Chaiwat Srisuk', '02-590-1716', 'นักวิชาการ', 'EMP00030', NULL, NULL, 7, '2026-01-01 06:27:52', NULL, '2026-01-01 06:27:52', NULL, 0, '2026-01-01 06:27:52', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248872, 1772086654, NULL, NULL),
(279, 'approver_qa', 'approver.qa@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$VE5RakdRRDFpN3JYZlVHcw$Ggq8zWBVRAYev+WMGxIHVPYHvVPjPYIpttQph7h1YnM', NULL, 'USC33VpFPvSl63hHZSZOMdr-p7CZGOTW', NULL, NULL, 'ณัฐิดา ประเสริฐ', NULL, NULL, NULL, NULL, NULL, NULL, 'Chaiwat Srisuk', '02-590-6834', 'หัวหน้างาน', 'EMP00032', NULL, NULL, 8, '2026-01-01 06:27:53', NULL, '2026-01-01 06:27:53', NULL, 0, '2026-01-01 06:27:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248873, 1772086654, NULL, NULL),
(280, 'user_qa01', 'user.qa01@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$Q0kxTzMwZS55cnNrMEdHNQ$JVqjChrGOdYzNUeYpiIgrWEMrnOGwR+rBcC1uH+H3DE', NULL, 'EMGmU9FyuC-5l-N0AbWAKtaz9yOv8d6N', NULL, NULL, 'พิศมัย สว่างจิต', NULL, NULL, NULL, NULL, NULL, NULL, 'Somchai Chaiyasuk', '02-590-8234', 'เจ้าพนักงาน', 'EMP00033', NULL, NULL, 8, '2026-01-01 06:27:53', NULL, '2026-01-01 06:27:53', NULL, 0, '2026-01-01 06:27:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248873, 1772086654, NULL, NULL),
(281, 'user_qa02', 'user.qa02@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$V1EzZk4wL3BLd2QwbWR1WQ$a32R0aV0RQhwPChc0ITDplz8jaEdIOcDsl08oWa3TCg', NULL, '9S1gqfT1pGCbD1SX0fMdOo3SELQmUvDj', NULL, NULL, 'วิชัย ใจดี', NULL, NULL, NULL, NULL, NULL, NULL, 'Wichai Jaidee', '02-590-8875', 'นักวิเคราะห์นโยบายและแผน', 'EMP00034', NULL, NULL, 8, '2026-01-01 06:27:53', NULL, '2026-01-01 06:27:53', NULL, 0, '2026-01-01 06:27:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248873, 1772086654, NULL, NULL),
(282, 'manager_res', 'manager.res@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$RGRkamNPUGdTNk04eFU3WA$bGFiX4RZ9kiUd9zqThoC1xicFUzp/j/xukOXdKx+bNY', NULL, '2XRDCOCKJ3h6BZwHEZxnwKMtGfc_6bMA', NULL, NULL, 'กรกฎ เพชรสุข (ผจก.)', NULL, NULL, NULL, NULL, NULL, NULL, 'Chaiwat Chaiyasuk', '02-590-7981', 'ผู้อำนวยการกอง', 'EMP00036', NULL, NULL, 9, '2026-01-01 06:27:53', NULL, '2026-01-01 06:27:53', NULL, 0, '2026-01-01 06:27:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248873, 1772086655, NULL, NULL),
(283, 'approver_res', 'approver.res@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$U2VDYU5Cajdjb2tYV0s2Qg$ctXKrx2Rme+Ud0WrZ9VXH1ODrpJph8zlDRfziYh0Elk', NULL, 'RpMK0lz6SHNdHhNdp9mTD8_IlPtlL3JJ', NULL, NULL, 'ณัฐิดา มั่นคง', NULL, NULL, NULL, NULL, NULL, NULL, 'Pawinee Prompan', '02-590-4411', 'นักวิชาการชำนาญการพิเศษ', 'EMP00037', NULL, NULL, 9, '2026-01-01 06:27:53', NULL, '2026-01-01 06:27:53', NULL, 0, '2026-01-01 06:27:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248873, 1772086655, NULL, NULL),
(284, 'approver_res02', 'approver.res02@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$a0IxT0lPVVVrRU1HaVFRRA$JEBsiW5ugutazj/8Z8Zu391ONbEHQsiXvigAx4HMIuk', NULL, 'R9YJRb7m83mwT-h-RpDLqGSwGezFuBdP', NULL, NULL, 'กิตติ พิทักษ์', NULL, NULL, NULL, NULL, NULL, NULL, 'Suchat Sangsuk', '02-590-5648', 'นักวิชาการชำนาญการพิเศษ', 'EMP00040', NULL, NULL, 9, '2026-01-01 06:27:53', NULL, '2026-01-01 06:27:53', NULL, 0, '2026-01-01 06:27:53', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248873, 1772050772, NULL, NULL),
(285, 'user_res01', 'user.res01@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$L2hRd0pFS0EuTHp5aEQ0Tw$ZXG037UoEJkm5yHP4Qsb0Gyi+FClCAer2JR4mWq+Nyc', NULL, 'Sk_z-W62G9XKOr9xE6i1FrF6DDVhPKJU', NULL, NULL, 'อนุสรา เจริญสุข', NULL, NULL, NULL, NULL, NULL, NULL, 'Wanna Yimyam', '02-590-6069', 'เจ้าพนักงาน', 'EMP00038', NULL, NULL, 9, '2026-01-01 06:27:54', NULL, '2026-01-01 06:27:54', NULL, 0, '2026-01-01 06:27:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248874, 1772086655, NULL, NULL),
(286, 'user_res02', 'user.res02@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$UUl4aWJKczlhUmJaTXF5dQ$Or2fO/N2C11J1e1QRNTdvs2nRuQcNhmykT84qBJz+sg', NULL, 'zTbZjfisJk-8049Wo6RtQ5BkE0h5wlWy', NULL, NULL, 'อนุสรา สันติสุข', NULL, NULL, NULL, NULL, NULL, NULL, 'Chaiwat Suksawat', '02-590-8187', 'นักวิชาการ', 'EMP00039', NULL, NULL, 9, '2026-01-01 06:27:54', NULL, '2026-01-01 06:27:54', NULL, 0, '2026-01-01 06:27:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248874, 1772086655, NULL, NULL),
(287, 'user_res03', 'user.res03@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$OURmYjdEUDNTN1Jad3gwLg$4bmAFvf0+7+AlDQDlBWLB6trSVQnxX7B59v7x1ZW8Qw', NULL, '5rXAmkyJfgYlJq14BGRW5kpyXNLwWW0E', NULL, NULL, 'สมหญิง ยิ้มแย้ม', NULL, NULL, NULL, NULL, NULL, NULL, 'Anucha Suksawat', '02-590-4887', 'เจ้าพนักงาน', 'EMP00043', NULL, NULL, 9, '2026-01-01 06:27:54', NULL, '2026-01-01 06:27:54', NULL, 0, '2026-01-01 06:27:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248874, 1772050773, NULL, NULL),
(288, 'manager_law', 'manager.law@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$OGhjVGpJRUFDcndxb1dCaA$CpPH1e/Q4abNZ5Q5UByUCgfOngLx+/SzP821zQ0fbIU', NULL, 'BXEHf11ybLZV09cJMMSi0aWsiyifmws6', NULL, NULL, 'พงศ์ ทองดี (ผจก.)', NULL, NULL, NULL, NULL, NULL, NULL, 'Pawinee Chaiyasuk', '02-590-1797', 'รองผู้อำนวยการ', 'EMP00040', NULL, NULL, 10, '2026-01-01 06:27:54', NULL, '2026-01-01 06:27:54', NULL, 0, '2026-01-01 06:27:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248874, 1772086655, NULL, NULL),
(289, 'approver_law', 'approver.law@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$WXlSRW91Y1g4bDEvenFQaQ$h2bxBrD6egM5xjoRF7tGGP0lgfR0NdcvIQx+lPt9puo', NULL, 'vVvmObdvqDpq-qP16vLx4Qu0KDz7twij', NULL, NULL, 'วรรณา อุดมศักดิ์', NULL, NULL, NULL, NULL, NULL, NULL, 'Wanna Somboon', '02-590-7641', 'หัวหน้ากลุ่มงาน', 'EMP00041', NULL, NULL, 10, '2026-01-01 06:27:54', NULL, '2026-01-01 06:27:54', NULL, 0, '2026-01-01 06:27:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248874, 1772086655, NULL, NULL),
(290, 'approver_law02', 'approver.law02@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$ZnhPekk5Z04xRC51UUVFUw$AzMy8r1wsli/gS2PQNn5Kokkee5jU4I0BwZRmz110us', NULL, 'z3U2GFoE1O81QhZ8bamw1PdtBmxT3_Vh', NULL, NULL, 'ชัยวัฒน์ วัฒนา', NULL, NULL, NULL, NULL, NULL, NULL, 'Thanida Rakdee', '02-590-3149', 'หัวหน้ากลุ่มงาน', 'EMP00046', NULL, NULL, 10, '2026-01-01 06:27:54', NULL, '2026-01-01 06:27:54', NULL, 0, '2026-01-01 06:27:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248874, 1772050773, NULL, NULL),
(291, 'user_law01', 'user.law01@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$UmRJWW85VFY3UHpZdWRPOA$bYVILP7CBTGllZIvaCpUmVd3J0L+iLiWK+l8dFhusQw', NULL, 'GMamq77KYqwb26qEoHBJKle9W7ig90zi', NULL, NULL, 'สุภา ทองดี', NULL, NULL, NULL, NULL, NULL, NULL, 'Pranee Wongsuk', '02-590-9195', 'นักจัดการงานทั่วไป', 'EMP00042', NULL, NULL, 10, '2026-01-01 06:27:54', NULL, '2026-01-01 06:27:54', NULL, 0, '2026-01-01 06:27:54', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248874, 1772086656, NULL, NULL),
(292, 'user_law02', 'user.law02@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$ejNUeDh5Z2xId2VFM3JNTg$2W+nurqamGDsVrW9XNefBPrToHLMf1sarABBZZrQ9eo', NULL, 'NWFY7oRv1TtcZ49UigjdZnESvG1lUCUF', NULL, NULL, 'รัตนาภรณ์ ศรีสุข', NULL, NULL, NULL, NULL, NULL, NULL, 'Siriwan Wongsuk', '02-590-3553', 'นักวิชาการ', 'EMP00043', NULL, NULL, 10, '2026-01-01 06:27:55', NULL, '2026-01-01 06:27:55', NULL, 0, '2026-01-01 06:27:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248875, 1772086656, NULL, NULL),
(293, 'driver01', 'driver01@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$c3JQNWdkZGRvbU84REVOOA$+duO4rhcHIwigPVWW2NQT9Ff9Tv5uRpp8OIVSXfHDLM', NULL, 'FDk82CaqbsNy2PqWe4TamIXTL4o_OW9r', NULL, NULL, 'กิตติ วัฒนา', NULL, NULL, NULL, NULL, NULL, NULL, 'Anucha Yimyam', '02-590-9062', 'พนักงานขับรถยนต์', 'DRV00001', NULL, NULL, 5, '2026-01-01 06:27:55', NULL, '2026-01-01 06:27:55', NULL, 0, '2026-01-01 06:27:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248875, 1772086656, NULL, NULL),
(294, 'driver02', 'driver02@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$QUtjTjF2MFBQTUgvLm9ZNw$inpeavgkHdwa/pyzzHx27H5gZchDJzkbPpefS1H4epE', NULL, 'u0eAW_BpidM5LszP4ZE27N5Q9WxxgUFo', NULL, NULL, 'ธนา ประเสริฐ', NULL, NULL, NULL, NULL, NULL, NULL, 'Supaporn Suksawat', '02-590-5349', 'พนักงานขับรถ', 'DRV00002', NULL, NULL, 5, '2026-01-01 06:27:55', NULL, '2026-01-01 06:27:55', NULL, 0, '2026-01-01 06:27:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248875, 1772086657, NULL, NULL),
(295, 'driver03', 'driver03@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$Y0dZWFhRbEwwRWJocXRxYw$ytCmB+V40LILmFmDIO4rhlgtPCq6RXTQ4511ZZGTWUk', NULL, '-RbmvuaG2dATg6zWyOzYzNAPCrfh6-PS', NULL, NULL, 'สุรีรัตน์ อุดมศักดิ์', NULL, NULL, NULL, NULL, NULL, NULL, 'Prasit Suksawat', '02-590-2333', 'พนักงานขับรถ', 'DRV00003', NULL, NULL, 5, '2026-01-01 06:27:55', NULL, '2026-01-01 06:27:55', NULL, 0, '2026-01-01 06:27:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248875, 1772086657, NULL, NULL),
(296, 'driver04', 'driver04@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$eGFnVEZZWWN4WlR3Ui5RNA$oVzgdBI67gSpWOkuzQthcD1g+oYl/VO4mPYE9yDLNPs', NULL, 'veqMUICHtTTl3tUbL2R9NoLWBm-TttGa', NULL, NULL, 'กิตติ อุดมศักดิ์', NULL, NULL, NULL, NULL, NULL, NULL, 'Pranee Jaidee', '02-590-3773', 'พนักงานขับรถ', 'DRV00004', NULL, NULL, 5, '2026-01-01 06:27:55', NULL, '2026-01-01 06:27:55', NULL, 0, '2026-01-01 06:27:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248875, 1772086657, NULL, NULL),
(297, 'driver05', 'driver05@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$VmtvOEVmUHpaaTYzSHpSeQ$8vC2/MQfnBsp9c6ZL/6P2/GosZX9HAsvwmWKJgkzF8o', NULL, '4frx928d8U8PVH8kOWJ-usOJYggKr0hX', NULL, NULL, 'อนุชา วงศ์สุข', NULL, NULL, NULL, NULL, NULL, NULL, 'Siriwan Petchsuk', '02-590-4690', 'พนักงานขับรถยนต์', 'DRV00005', NULL, NULL, 5, '2026-01-01 06:27:55', NULL, '2026-01-01 06:27:55', NULL, 0, '2026-01-01 06:27:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1767248875, 1772086657, NULL, NULL),
(298, 'user_ict03', 'user.ict03@bizai.co.th', '$argon2id$v=19$m=65536,t=4,p=1$WFR1WUpqcy4wY3VMRFpFag$87N0RHjxKJnm8Hk/hbe3/kaik1/V4nrCuLHjrJBoIhs', NULL, 'yvSBT_714Q-ri6EN0Hll6xRdsR6c9hR8', NULL, NULL, 'กรกมล ศรีสุข', NULL, NULL, NULL, NULL, NULL, NULL, 'Prasit Wongsuk', '02-590-6118', 'นักวิเคราะห์นโยบายและแผน', 'EMP00007', NULL, NULL, 2, '2026-02-25 20:19:27', NULL, '2026-02-25 20:19:27', NULL, 0, '2026-02-25 20:19:27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1772050767, 1772075158, NULL, NULL),
(299, 'approver_hr02', 'approver.hr02@bizai.co.th', '$argon2id$v=19$m=65536,t=4,p=1$RWh3Vk8wWTN6RVZjU2NkNg$SvglRxhIW1qb6HeVGJKC+Fifxe6+Fq/xsZR+Ru1+8d0', NULL, 'g7cmGnJ1lv-E02IwcHUlIwmQG5hxbJEn', NULL, NULL, 'พงศ์ สุขสวัสดิ์', NULL, NULL, NULL, NULL, NULL, NULL, 'Wichai Petchsuk', '02-590-2284', 'นักวิชาการชำนาญการพิเศษ', 'EMP00008', NULL, NULL, 3, '2026-02-25 20:19:27', NULL, '2026-02-25 20:19:27', NULL, 0, '2026-02-25 20:19:27', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1772050767, 1772075275, NULL, NULL),
(300, 'user_hr03', 'user.hr03@bizai.co.th', '$argon2id$v=19$m=65536,t=4,p=1$Mkg0WVVBZXZ5aDlxS21pTg$fb+6Mvm/hor6i0NyS81xr4sPH2ioiwdbGp1zgYhYzgY', NULL, 'CBWw6Tt7rXrras9DiKkRjVw40XWTHkVD', NULL, NULL, 'รัตนา ชัยสุข', NULL, NULL, NULL, NULL, NULL, NULL, 'Wanna Meesuk', '02-590-8399', 'นักวิเคราะห์นโยบายและแผน', 'EMP00010', NULL, NULL, 3, '2026-02-25 20:19:28', NULL, '2026-02-25 20:19:28', NULL, 0, '2026-02-25 20:19:28', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1772050768, 1772086650, NULL, NULL),
(301, 'user_admin03', 'user.admin03@bizai.co.th', '$argon2id$v=19$m=65536,t=4,p=1$OXJkNXRzVmREb1I5YmVvNQ$lnUkgB/yGhcDs2lmVLX7ZPqZEai5fkgEIe0HjC89cwk', NULL, 'cKiwx9471A-v2Tpy4W8fOiczRfV3ELsM', NULL, NULL, 'ประสิทธิ์ เพชรสุข', NULL, NULL, NULL, NULL, NULL, NULL, 'Surasak Yimyam', '02-590-1194', 'นักวิชาการ', 'EMP00020', NULL, NULL, 5, '2026-02-25 20:19:30', NULL, '2026-02-25 20:19:30', NULL, 0, '2026-02-25 20:19:30', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1772050770, 1772086652, NULL, NULL),
(302, 'approver_plan02', 'approver.plan02@bizai.co.th', '$argon2id$v=19$m=65536,t=4,p=1$MlI2WlBaaTVlL0xVZUk2Qg$+My2S6CuqU4ZogNpSvHPt0uoAlnlMhSsgKFtx7u301A', NULL, 'gexiDV6YHR-gV6457lvFBNTkDXU8VJQu', NULL, NULL, 'อนุชา รุ่งเรือง', NULL, NULL, NULL, NULL, NULL, NULL, 'Siriwan Somboon', '02-590-5812', 'นักวิชาการชำนาญการพิเศษ', 'EMP00029', NULL, NULL, 7, '2026-02-25 20:19:31', NULL, '2026-02-25 20:19:31', NULL, 0, '2026-02-25 20:19:31', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1772050771, 1772075279, NULL, NULL),
(303, 'approver_qa02', 'approver.qa02@bizai.co.th', '$argon2id$v=19$m=65536,t=4,p=1$bVZ1eWFSVmhSbUhzLkl5Vw$MGCFSZwQnVjD23w2VJ9fHjSUtbJUN7JrOc8XJCLHWHo', NULL, '9CxZmR5qEhisMb5xqIeATjNNFtLHSHo4', NULL, NULL, 'พิศมัย สุขสวัสดิ์', NULL, NULL, NULL, NULL, NULL, NULL, 'Pranee Wongsuk', '02-590-8943', 'หัวหน้างาน', 'EMP00034', NULL, NULL, 8, '2026-02-25 20:19:31', NULL, '2026-02-25 20:19:31', NULL, 0, '2026-02-25 20:19:31', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1772050771, 1772050771, NULL, NULL),
(304, 'user_qa03', 'user.qa03@bizai.co.th', '$argon2id$v=19$m=65536,t=4,p=1$blIxaVFKQ2RkSUNyTHlKNg$dTmi5T+USq2BWZKMXsua4Y9Y2iVDN63MnS3+/QYav3U', NULL, 'YcucKuBxH3w-4AHoreIROpHmxjBDZGEi', NULL, NULL, 'ประภา สันติสุข', NULL, NULL, NULL, NULL, NULL, NULL, 'Chaiwat Prompan', '02-590-5311', 'เจ้าพนักงาน', 'EMP00035', NULL, NULL, 8, '2026-02-25 20:19:32', NULL, '2026-02-25 20:19:32', NULL, 0, '2026-02-25 20:19:32', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1772050772, 1772086654, NULL, NULL),
(305, 'user_fin03', 'user.fin03@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$cjVnUDFKTWF5YVFlZTQvVA$6CAl+UAxsb0yWIFCgw/xlCVjwZVjrrywF693gRpFUzU', NULL, '1MxDf6DpH6rjacqMscnwjsG8CXf3Q9EM', NULL, NULL, 'ชัยวัฒน์ ชัยสุข', NULL, NULL, NULL, NULL, NULL, NULL, 'Anucha Meesuk', '02-590-5185', 'นักวิชาการ', 'EMP00015', NULL, NULL, 4, '2026-02-26 03:06:00', NULL, '2026-02-26 03:06:00', NULL, 0, '2026-02-26 03:06:00', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1772075160, 1772086651, NULL, NULL),
(306, 'user_acad03', 'user.acad03@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$U3RKbEJ5YnJTMjBjR3VCNA$C7n0hLBqv30c1Oa8EQhSkNtpuctj8ViVjz2GVHQxFt0', NULL, '9ugYCwSHNfRSLFrKDkcFcZH68tzGxpqS', NULL, NULL, 'กิตติ สันติสุข', NULL, NULL, NULL, NULL, NULL, NULL, 'Somsak Yimyam', '02-590-1223', 'นักจัดการงานทั่วไป', 'EMP00025', NULL, NULL, 6, '2026-02-26 03:07:58', NULL, '2026-02-26 03:07:58', NULL, 0, '2026-02-26 03:07:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1772075278, 1772086653, NULL, NULL),
(307, 'user_law03', 'user.law03@pbri.ac.th', '$argon2id$v=19$m=65536,t=4,p=1$dlhSSlNoZXhRM280eFhPaQ$uoXNFLh2ovivjGbdaLJ5tr2p9TPpVu9yEZIvsJdTCAY', NULL, '3MpoMw47_iwGZ3w5C_19LHtOOtwNUKki', NULL, NULL, 'ธนิดา อุดมศักดิ์', NULL, NULL, NULL, NULL, NULL, NULL, 'Somchai Rakdee', '02-590-3004', 'เจ้าพนักงาน', 'EMP00044', NULL, NULL, 10, '2026-02-26 06:17:36', NULL, '2026-02-26 06:17:36', NULL, 0, '2026-02-26 06:17:36', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 10, 1772086656, 1772086656, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_oauth`
--

CREATE TABLE `user_oauth` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `provider` varchar(50) NOT NULL,
  `provider_user_id` varchar(255) NOT NULL,
  `access_token` text DEFAULT NULL,
  `refresh_token` text DEFAULT NULL,
  `token_expires_at` datetime DEFAULT NULL,
  `profile_data` text DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_two_factor`
--

CREATE TABLE `user_two_factor` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `secret_key` varchar(255) NOT NULL,
  `backup_codes` text DEFAULT NULL,
  `is_enabled` tinyint(1) DEFAULT 0,
  `enabled_at` datetime DEFAULT NULL,
  `last_used_at` datetime DEFAULT NULL,
  `created_at` datetime NOT NULL,
  `updated_at` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_geofence`
--

CREATE TABLE `vehicle_geofence` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `geofence_id` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vehicle_location`
--

CREATE TABLE `vehicle_location` (
  `id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL,
  `latitude` decimal(10,8) NOT NULL,
  `longitude` decimal(11,8) NOT NULL,
  `speed` decimal(6,2) DEFAULT NULL,
  `heading` int(11) DEFAULT NULL,
  `altitude` decimal(10,2) DEFAULT NULL,
  `accuracy` decimal(10,2) DEFAULT NULL,
  `address` varchar(500) DEFAULT NULL,
  `recorded_at` datetime NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-audit_log-user_id` (`user_id`),
  ADD KEY `idx-audit_log-action` (`action_type`),
  ADD KEY `idx-audit_log-model_class` (`model_class`),
  ADD KEY `idx-audit_log-created_at` (`created_at`),
  ADD KEY `idx-audit_log-ip_address` (`ip_address`);

--
-- Indexes for table `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD PRIMARY KEY (`item_name`,`user_id`),
  ADD KEY `idx-auth_assignment-user_id` (`user_id`);

--
-- Indexes for table `auth_item`
--
ALTER TABLE `auth_item`
  ADD PRIMARY KEY (`name`),
  ADD KEY `rule_name` (`rule_name`),
  ADD KEY `idx-auth_item-type` (`type`);

--
-- Indexes for table `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD PRIMARY KEY (`parent`,`child`),
  ADD KEY `child` (`child`);

--
-- Indexes for table `auth_rule`
--
ALTER TABLE `auth_rule`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `budget_category`
--
ALTER TABLE `budget_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_code` (`category_code`);

--
-- Indexes for table `car_driver`
--
ALTER TABLE `car_driver`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `driver_code` (`driver_code`),
  ADD KEY `idx-car_driver-driver_code` (`driver_code`),
  ADD KEY `idx-car_driver-employee_id` (`employee_code`),
  ADD KEY `idx-car_driver-id_card_number` (`id_card_number`),
  ADD KEY `idx-car_driver-organization_id` (`organization_id`),
  ADD KEY `idx-car_driver-driver_status` (`driver_status`),
  ADD KEY `idx-car_driver-is_active` (`is_active`),
  ADD KEY `idx-car_driver-license_expire` (`driver_license_expire_date`),
  ADD KEY `idx-car_driver-fullname` (`first_name`,`last_name`),
  ADD KEY `idx_car_driver_is_deleted` (`is_deleted`);

--
-- Indexes for table `car_driver_license_history`
--
ALTER TABLE `car_driver_license_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-car_driver_license_history-driver_id` (`driver_id`);

--
-- Indexes for table `car_fuel_log`
--
ALTER TABLE `car_fuel_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-car_fuel_log-vehicle_id` (`vehicle_id`),
  ADD KEY `idx-car_fuel_log-reservation_id` (`reservation_id`),
  ADD KEY `idx-car_fuel_log-driver_id` (`driver_id`),
  ADD KEY `idx-car_fuel_log-refuel_date` (`refuel_date`);

--
-- Indexes for table `car_maintenance_log`
--
ALTER TABLE `car_maintenance_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-car_maintenance_log-vehicle_id` (`vehicle_id`),
  ADD KEY `idx-car_maintenance_log-maintenance_date` (`maintenance_date`),
  ADD KEY `idx-car_maintenance_log-maintenance_type` (`maintenance_type`),
  ADD KEY `idx-car_maintenance_log-status` (`status`);

--
-- Indexes for table `car_mission_type`
--
ALTER TABLE `car_mission_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `mission_code` (`mission_code`),
  ADD KEY `idx-car_mission_type-mission_code` (`mission_code`),
  ADD KEY `idx-car_mission_type-is_active` (`is_active`);

--
-- Indexes for table `car_notification`
--
ALTER TABLE `car_notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-car_notification-user_id` (`user_id`),
  ADD KEY `idx-car_notification-type` (`notification_type`),
  ADD KEY `idx-car_notification-is_read` (`is_read`),
  ADD KEY `idx-car_notification-created_at` (`created_at`),
  ADD KEY `idx-car_notification-reference` (`reference_type`,`reference_id`);

--
-- Indexes for table `car_organization`
--
ALTER TABLE `car_organization`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `org_code` (`org_code`),
  ADD KEY `idx-car_organization-org_code` (`org_code`),
  ADD KEY `idx-car_organization-parent_id` (`parent_id`),
  ADD KEY `idx-car_organization-is_active` (`is_active`);

--
-- Indexes for table `car_reservation`
--
ALTER TABLE `car_reservation`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reservation_code` (`reservation_code`),
  ADD KEY `idx-car_reservation-code` (`reservation_code`),
  ADD KEY `idx-car_reservation-fiscal_year` (`fiscal_year`),
  ADD KEY `idx-car_reservation-requester_user_id` (`requester_user_id`),
  ADD KEY `idx-car_reservation-requester_dept_id` (`requester_organization_id`),
  ADD KEY `idx-car_reservation-vehicle_id` (`vehicle_id`),
  ADD KEY `idx-car_reservation-driver_id` (`driver_id`),
  ADD KEY `idx-car_reservation-mission_type_id` (`mission_type_id`),
  ADD KEY `idx-car_reservation-departure_datetime` (`departure_datetime`),
  ADD KEY `idx-car_reservation-return_datetime` (`return_datetime`),
  ADD KEY `idx-car_reservation-status` (`reservation_status`),
  ADD KEY `idx-car_reservation-priority` (`priority_level`),
  ADD KEY `idx-car_reservation-is_deleted` (`is_deleted`),
  ADD KEY `idx-car_reservation-created_at` (`created_at`),
  ADD KEY `idx-car_reservation-status_date` (`reservation_status`,`departure_datetime`),
  ADD KEY `idx-car_reservation-vehicle_date` (`vehicle_id`,`departure_datetime`,`return_datetime`),
  ADD KEY `idx-car_reservation-driver_date` (`driver_id`,`departure_datetime`,`return_datetime`);

--
-- Indexes for table `car_reservation_history`
--
ALTER TABLE `car_reservation_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-car_reservation_history-reservation_id` (`reservation_id`),
  ADD KEY `idx-car_reservation_history-action_type` (`action_type`),
  ADD KEY `idx-car_reservation_history-action_at` (`action_at`),
  ADD KEY `idx-car_reservation_history-action_by` (`action_by`);

--
-- Indexes for table `car_system_setting`
--
ALTER TABLE `car_system_setting`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx-car_system_setting-key` (`setting_key`),
  ADD KEY `idx-car_system_setting-group` (`setting_group`);

--
-- Indexes for table `car_vehicle`
--
ALTER TABLE `car_vehicle`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vehicle_code` (`vehicle_code`),
  ADD KEY `idx-car_vehicle-vehicle_code` (`vehicle_code`),
  ADD KEY `idx-car_vehicle-license_plate` (`license_plate`),
  ADD KEY `idx-car_vehicle-vehicle_type_id` (`vehicle_type_id`),
  ADD KEY `idx-car_vehicle-organization_id` (`organization_id`),
  ADD KEY `idx-car_vehicle-vehicle_status` (`vehicle_status`),
  ADD KEY `idx-car_vehicle-is_active` (`is_active`),
  ADD KEY `idx-car_vehicle-insurance_expire` (`insurance_expire_date`),
  ADD KEY `idx-car_vehicle-tax_expire` (`tax_expire_date`),
  ADD KEY `fk-car_vehicle-assigned_driver_id` (`assigned_driver_id`),
  ADD KEY `idx_car_vehicle_is_deleted` (`is_deleted`);

--
-- Indexes for table `car_vehicle_type`
--
ALTER TABLE `car_vehicle_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `type_code` (`type_code`),
  ADD KEY `idx-car_vehicle_type-type_code` (`type_code`),
  ADD KEY `idx-car_vehicle_type-is_active` (`is_active`);

--
-- Indexes for table `expenses`
--
ALTER TABLE `expenses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `expense_code` (`expense_code`),
  ADD KEY `fk_expenses_project` (`project_id`),
  ADD KEY `fk_expenses_budget` (`project_budget_id`),
  ADD KEY `fk_expenses_category` (`expense_category_id`),
  ADD KEY `idx_expenses_status` (`expense_status`),
  ADD KEY `idx_expenses_date` (`expense_date`);

--
-- Indexes for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `category_code` (`category_code`),
  ADD KEY `fk_expense_categories_parent` (`parent_id`);

--
-- Indexes for table `fiscal_years`
--
ALTER TABLE `fiscal_years`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `fiscal_year_code` (`fiscal_year_code`);

--
-- Indexes for table `fuel_budget`
--
ALTER TABLE `fuel_budget`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-fuel_budget-vehicle_id` (`vehicle_id`),
  ADD KEY `idx-fuel_budget-budget_year` (`budget_year`),
  ADD KEY `idx-fuel_budget-budget_month` (`budget_month`),
  ADD KEY `idx-fuel_budget-department` (`department`);

--
-- Indexes for table `fuel_price_history`
--
ALTER TABLE `fuel_price_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-fuel_price-fuel_type` (`fuel_type`),
  ADD KEY `idx-fuel_price-effective_date` (`effective_date`);

--
-- Indexes for table `fuel_record`
--
ALTER TABLE `fuel_record`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-fuel_record-vehicle_id` (`vehicle_id`),
  ADD KEY `idx-fuel_record-record_date` (`record_date`),
  ADD KEY `idx-fuel_record-fuel_type` (`fuel_type`),
  ADD KEY `idx-fuel_record-status` (`status`);

--
-- Indexes for table `geofence`
--
ALTER TABLE `geofence`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-geofence-is_active` (`is_active`);

--
-- Indexes for table `geofence_alert`
--
ALTER TABLE `geofence_alert`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-geofence_alert-vehicle_id` (`vehicle_id`),
  ADD KEY `idx-geofence_alert-geofence_id` (`geofence_id`),
  ADD KEY `idx-geofence_alert-is_acknowledged` (`is_acknowledged`),
  ADD KEY `idx-geofence_alert-created_at` (`created_at`);

--
-- Indexes for table `login_history`
--
ALTER TABLE `login_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_user_id` (`user_id`),
  ADD KEY `idx_created_at` (`created_at`),
  ADD KEY `idx_ip_address` (`ip_address`),
  ADD KEY `idx_is_successful` (`is_successful`);

--
-- Indexes for table `maintenance_part`
--
ALTER TABLE `maintenance_part`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-maintenance_part-maintenance_record_id` (`maintenance_record_id`);

--
-- Indexes for table `maintenance_record`
--
ALTER TABLE `maintenance_record`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-maintenance_record-vehicle_id` (`vehicle_id`),
  ADD KEY `idx-maintenance_record-maintenance_type_id` (`maintenance_type_id`),
  ADD KEY `idx-maintenance_record-maintenance_date` (`maintenance_date`),
  ADD KEY `idx-maintenance_record-status` (`status`);

--
-- Indexes for table `maintenance_schedule`
--
ALTER TABLE `maintenance_schedule`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-maintenance_schedule-vehicle_id` (`vehicle_id`),
  ADD KEY `idx-maintenance_schedule-maintenance_type_id` (`maintenance_type_id`),
  ADD KEY `idx-maintenance_schedule-scheduled_date` (`scheduled_date`),
  ADD KEY `idx-maintenance_schedule-status` (`status`);

--
-- Indexes for table `maintenance_type`
--
ALTER TABLE `maintenance_type`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx-maintenance_type-code` (`code`),
  ADD KEY `idx-maintenance_type-category` (`category`),
  ADD KEY `idx-maintenance_type-is_active` (`is_active`);

--
-- Indexes for table `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `oauth2_client`
--
ALTER TABLE `oauth2_client`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx-oauth2_client-client_id` (`client_id`);

--
-- Indexes for table `organizations`
--
ALTER TABLE `organizations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `organization_code` (`organization_code`),
  ADD KEY `fk_organizations_parent` (`parent_id`),
  ADD KEY `idx_organizations_status` (`organization_status`);

--
-- Indexes for table `projects`
--
ALTER TABLE `projects`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `project_code` (`project_code`),
  ADD KEY `fk_projects_organization` (`organization_id`),
  ADD KEY `fk_projects_manager` (`project_manager_id`),
  ADD KEY `idx_projects_status` (`project_status`),
  ADD KEY `idx_projects_fiscal_year` (`fiscal_year_id`);

--
-- Indexes for table `project_budgets`
--
ALTER TABLE `project_budgets`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx_project_budgets_unique` (`project_id`,`expense_category_id`),
  ADD KEY `fk_project_budgets_category` (`expense_category_id`);

--
-- Indexes for table `user`
--
ALTER TABLE `user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uk_users_username` (`username`),
  ADD UNIQUE KEY `uk_users_email_address` (`email`),
  ADD UNIQUE KEY `idx-user-username` (`username`),
  ADD UNIQUE KEY `idx-user-email` (`email`),
  ADD UNIQUE KEY `uk_users_password_reset_token` (`password_reset_token`),
  ADD UNIQUE KEY `idx-user-password_reset_token` (`password_reset_token`),
  ADD KEY `idx_users_organization` (`organization_id`),
  ADD KEY `idx_users_status` (`status`),
  ADD KEY `idx_users_name` (`first_name`,`last_name`),
  ADD KEY `idx_users_auth_key` (`auth_key`),
  ADD KEY `idx-user-status` (`status`);

--
-- Indexes for table `user_oauth`
--
ALTER TABLE `user_oauth`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx-user_oauth-provider` (`provider`,`provider_user_id`),
  ADD KEY `idx-user_oauth-user_id` (`user_id`);

--
-- Indexes for table `user_two_factor`
--
ALTER TABLE `user_two_factor`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `idx-user_two_factor-user_id` (`user_id`);

--
-- Indexes for table `vehicle_geofence`
--
ALTER TABLE `vehicle_geofence`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-vehicle_geofence-vehicle_id` (`vehicle_id`),
  ADD KEY `idx-vehicle_geofence-geofence_id` (`geofence_id`);

--
-- Indexes for table `vehicle_location`
--
ALTER TABLE `vehicle_location`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx-vehicle_location-vehicle_id` (`vehicle_id`),
  ADD KEY `idx-vehicle_location-recorded_at` (`recorded_at`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `budget_category`
--
ALTER TABLE `budget_category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `car_driver`
--
ALTER TABLE `car_driver`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `car_driver_license_history`
--
ALTER TABLE `car_driver_license_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `car_fuel_log`
--
ALTER TABLE `car_fuel_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `car_maintenance_log`
--
ALTER TABLE `car_maintenance_log`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `car_mission_type`
--
ALTER TABLE `car_mission_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `car_notification`
--
ALTER TABLE `car_notification`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `car_organization`
--
ALTER TABLE `car_organization`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `car_reservation`
--
ALTER TABLE `car_reservation`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `car_reservation_history`
--
ALTER TABLE `car_reservation_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `car_system_setting`
--
ALTER TABLE `car_system_setting`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `car_vehicle`
--
ALTER TABLE `car_vehicle`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `car_vehicle_type`
--
ALTER TABLE `car_vehicle_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `expenses`
--
ALTER TABLE `expenses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `expense_categories`
--
ALTER TABLE `expense_categories`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fiscal_years`
--
ALTER TABLE `fiscal_years`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fuel_budget`
--
ALTER TABLE `fuel_budget`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fuel_price_history`
--
ALTER TABLE `fuel_price_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `fuel_record`
--
ALTER TABLE `fuel_record`
  MODIFY `id` int(11) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geofence`
--
ALTER TABLE `geofence`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `geofence_alert`
--
ALTER TABLE `geofence_alert`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `login_history`
--
ALTER TABLE `login_history`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=89;

--
-- AUTO_INCREMENT for table `maintenance_part`
--
ALTER TABLE `maintenance_part`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maintenance_record`
--
ALTER TABLE `maintenance_record`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maintenance_schedule`
--
ALTER TABLE `maintenance_schedule`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `maintenance_type`
--
ALTER TABLE `maintenance_type`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `oauth2_client`
--
ALTER TABLE `oauth2_client`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `organizations`
--
ALTER TABLE `organizations`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `projects`
--
ALTER TABLE `projects`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `project_budgets`
--
ALTER TABLE `project_budgets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user`
--
ALTER TABLE `user`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT COMMENT 'รหัสผู้ใช้', AUTO_INCREMENT=308;

--
-- AUTO_INCREMENT for table `user_oauth`
--
ALTER TABLE `user_oauth`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_two_factor`
--
ALTER TABLE `user_two_factor`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vehicle_geofence`
--
ALTER TABLE `vehicle_geofence`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vehicle_location`
--
ALTER TABLE `vehicle_location`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD CONSTRAINT `1` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `auth_item`
--
ALTER TABLE `auth_item`
  ADD CONSTRAINT `1` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD CONSTRAINT `1` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `2` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `car_driver`
--
ALTER TABLE `car_driver`
  ADD CONSTRAINT `fk-car_driver-organization_id` FOREIGN KEY (`organization_id`) REFERENCES `car_organization` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `car_driver_license_history`
--
ALTER TABLE `car_driver_license_history`
  ADD CONSTRAINT `fk-car_driver_license_history-driver_id` FOREIGN KEY (`driver_id`) REFERENCES `car_driver` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `car_fuel_log`
--
ALTER TABLE `car_fuel_log`
  ADD CONSTRAINT `fk-car_fuel_log-driver_id` FOREIGN KEY (`driver_id`) REFERENCES `car_driver` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-car_fuel_log-reservation_id` FOREIGN KEY (`reservation_id`) REFERENCES `car_reservation` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-car_fuel_log-vehicle_id` FOREIGN KEY (`vehicle_id`) REFERENCES `car_vehicle` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `car_maintenance_log`
--
ALTER TABLE `car_maintenance_log`
  ADD CONSTRAINT `fk-car_maintenance_log-vehicle_id` FOREIGN KEY (`vehicle_id`) REFERENCES `car_vehicle` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `car_organization`
--
ALTER TABLE `car_organization`
  ADD CONSTRAINT `fk-car_organization-parent_id` FOREIGN KEY (`parent_id`) REFERENCES `car_organization` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `car_reservation`
--
ALTER TABLE `car_reservation`
  ADD CONSTRAINT `fk-car_reservation-driver_id` FOREIGN KEY (`driver_id`) REFERENCES `car_driver` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-car_reservation-mission_type_id` FOREIGN KEY (`mission_type_id`) REFERENCES `car_mission_type` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-car_reservation-requester_dept_id` FOREIGN KEY (`requester_organization_id`) REFERENCES `car_organization` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-car_reservation-vehicle_id` FOREIGN KEY (`vehicle_id`) REFERENCES `car_vehicle` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `car_reservation_history`
--
ALTER TABLE `car_reservation_history`
  ADD CONSTRAINT `fk-car_reservation_history-reservation_id` FOREIGN KEY (`reservation_id`) REFERENCES `car_reservation` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `car_vehicle`
--
ALTER TABLE `car_vehicle`
  ADD CONSTRAINT `fk-car_vehicle-assigned_driver_id` FOREIGN KEY (`assigned_driver_id`) REFERENCES `car_driver` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-car_vehicle-organization_id` FOREIGN KEY (`organization_id`) REFERENCES `car_organization` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk-car_vehicle-vehicle_type_id` FOREIGN KEY (`vehicle_type_id`) REFERENCES `car_vehicle_type` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `expenses`
--
ALTER TABLE `expenses`
  ADD CONSTRAINT `fk_expenses_budget` FOREIGN KEY (`project_budget_id`) REFERENCES `project_budgets` (`id`),
  ADD CONSTRAINT `fk_expenses_category` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`),
  ADD CONSTRAINT `fk_expenses_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`);

--
-- Constraints for table `expense_categories`
--
ALTER TABLE `expense_categories`
  ADD CONSTRAINT `fk_expense_categories_parent` FOREIGN KEY (`parent_id`) REFERENCES `expense_categories` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `organizations`
--
ALTER TABLE `organizations`
  ADD CONSTRAINT `fk_organizations_parent` FOREIGN KEY (`parent_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `projects`
--
ALTER TABLE `projects`
  ADD CONSTRAINT `fk_projects_fiscal_year` FOREIGN KEY (`fiscal_year_id`) REFERENCES `fiscal_years` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_projects_manager` FOREIGN KEY (`project_manager_id`) REFERENCES `user` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_projects_organization` FOREIGN KEY (`organization_id`) REFERENCES `organizations` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `project_budgets`
--
ALTER TABLE `project_budgets`
  ADD CONSTRAINT `fk_project_budgets_category` FOREIGN KEY (`expense_category_id`) REFERENCES `expense_categories` (`id`),
  ADD CONSTRAINT `fk_project_budgets_project` FOREIGN KEY (`project_id`) REFERENCES `projects` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
