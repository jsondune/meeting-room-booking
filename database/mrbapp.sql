-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 21, 2026 at 12:50 PM
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
-- Table structure for table `attachment`
--

CREATE TABLE `attachment` (
  `id` int(10) UNSIGNED NOT NULL,
  `model_class` varchar(100) NOT NULL,
  `model_id` int(11) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) UNSIGNED DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `uploaded_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_log`
--

CREATE TABLE `audit_log` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(500) DEFAULT NULL,
  `action` varchar(50) NOT NULL,
  `model_class` varchar(100) DEFAULT NULL,
  `model_id` varchar(50) DEFAULT NULL,
  `old_values` text DEFAULT NULL COMMENT 'JSON',
  `new_values` text DEFAULT NULL COMMENT 'JSON',
  `url` varchar(500) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_log`
--

INSERT INTO `audit_log` (`id`, `user_id`, `username`, `ip_address`, `user_agent`, `action`, `model_class`, `model_id`, `old_values`, `new_values`, `url`, `description`, `created_at`) VALUES
(1, 1, 'admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'update', 'common\\models\\User', '1', '{\"last_login_at\":null,\"last_login_ip\":null}', '{\"id\":1,\"username\":\"admin\",\"email\":\"admin@example.com\",\"password_hash\":\"$2y$13$PA\\/wjsKQMoWuuvqQlB35yu2ELLjanSFOpSu1XLO29\\/BM6N6TOcNKK\",\"auth_key\":\"JoSb1NFibHQ7G6AnT9JH_MUjxxX2GKF9\",\"password_reset_token\":null,\"verification_token\":null,\"first_name\":\"System\",\"last_name\":\"Administrator\",\"phone\":null,\"avatar\":null,\"department_id\":null,\"position\":null,\"azure_id\":null,\"google_id\":null,\"thaid_id\":null,\"facebook_id\":null,\"two_factor_secret\":null,\"two_factor_enabled\":0,\"backup_codes\":null,\"failed_login_attempts\":0,\"locked_until\":null,\"password_changed_at\":\"2026-01-17 23:25:05\",\"last_login_at\":\"2026-01-17 23:46:30\",\"last_login_ip\":\"127.0.0.1\",\"status\":10,\"role\":\"superadmin\",\"created_at\":\"2026-01-17 23:25:05\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', 'http://www.mrb.test/frontend/web/login', NULL, '2026-01-17 16:46:30'),
(2, 1, 'admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'update', 'common\\models\\User', '1', '{\"phone\":null,\"avatar\":null,\"department_id\":null,\"position\":null,\"updated_at\":\"2026-01-17 23:46:30\"}', '{\"id\":1,\"username\":\"admin\",\"email\":\"admin@example.com\",\"password_hash\":\"$2y$13$PA\\/wjsKQMoWuuvqQlB35yu2ELLjanSFOpSu1XLO29\\/BM6N6TOcNKK\",\"auth_key\":\"JoSb1NFibHQ7G6AnT9JH_MUjxxX2GKF9\",\"password_reset_token\":null,\"verification_token\":null,\"email_verified_at\":null,\"first_name\":\"System\",\"last_name\":\"Administrator\",\"phone\":\"\",\"avatar\":\"\\/uploads\\/avatars\\/avatar_1_1768669043.jpg\",\"department_id\":\"\",\"position\":\"\",\"azure_id\":null,\"google_id\":null,\"thaid_id\":null,\"facebook_id\":null,\"two_factor_secret\":null,\"two_factor_enabled\":0,\"backup_codes\":null,\"failed_login_attempts\":0,\"locked_until\":null,\"password_changed_at\":\"2026-01-17 23:25:05\",\"last_login_at\":\"2026-01-17 23:46:30\",\"last_login_ip\":\"127.0.0.1\",\"status\":10,\"role\":\"superadmin\",\"created_at\":\"2026-01-17 23:25:05\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', 'http://www.mrb.test/frontend/web/profile/edit', NULL, '2026-01-17 16:57:24'),
(3, 1, 'admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'update', 'common\\models\\User', '1', '{\"last_login_at\":\"2026-01-17 23:46:30\"}', '{\"id\":1,\"username\":\"admin\",\"email\":\"admin@example.com\",\"password_hash\":\"$2y$13$PA\\/wjsKQMoWuuvqQlB35yu2ELLjanSFOpSu1XLO29\\/BM6N6TOcNKK\",\"auth_key\":\"JoSb1NFibHQ7G6AnT9JH_MUjxxX2GKF9\",\"password_reset_token\":null,\"verification_token\":null,\"email_verified_at\":null,\"first_name\":\"System\",\"last_name\":\"Administrator\",\"phone\":\"\",\"avatar\":\"\\/uploads\\/avatars\\/avatar_1_1768669043.jpg\",\"department_id\":null,\"position\":\"\",\"azure_id\":null,\"google_id\":null,\"thaid_id\":null,\"facebook_id\":null,\"two_factor_secret\":null,\"two_factor_enabled\":0,\"backup_codes\":null,\"failed_login_attempts\":0,\"locked_until\":null,\"password_changed_at\":\"2026-01-17 23:25:05\",\"last_login_at\":\"2026-01-18 00:08:28\",\"last_login_ip\":\"127.0.0.1\",\"status\":10,\"role\":\"superadmin\",\"created_at\":\"2026-01-17 23:25:05\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', 'http://www.mrb.test/backend/web/login', NULL, '2026-01-17 17:08:28'),
(4, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\MeetingRoom', '1', NULL, '{\"id\":1,\"room_code\":\"CONF-L1\",\"name_th\":\"ห้องประชุมใหญ่ 1\",\"name_en\":\"Large Meeting Room 1\",\"building_id\":2,\"floor\":2,\"room_number\":\"201\",\"capacity\":100,\"room_type\":\"conference\",\"room_layout\":null,\"has_projector\":true,\"has_video_conference\":true,\"has_whiteboard\":true,\"has_air_conditioning\":true,\"has_wifi\":null,\"has_audio_system\":true,\"has_recording\":null,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":0,\"half_day_rate\":null,\"full_day_rate\":null,\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องประชุมขนาดใหญ่ รองรับ 100 คน มีระบบประชุมทางไกล\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":null,\"sort_order\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:17:20'),
(5, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\MeetingRoom', '2', NULL, '{\"id\":2,\"room_code\":\"CONF-L2\",\"name_th\":\"ห้องประชุมใหญ่ 2\",\"name_en\":\"Large Meeting Room 2\",\"building_id\":2,\"floor\":3,\"room_number\":\"301\",\"capacity\":80,\"room_type\":\"conference\",\"room_layout\":null,\"has_projector\":true,\"has_video_conference\":false,\"has_whiteboard\":true,\"has_air_conditioning\":true,\"has_wifi\":null,\"has_audio_system\":true,\"has_recording\":null,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":0,\"half_day_rate\":null,\"full_day_rate\":null,\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องประชุมขนาดใหญ่ รองรับ 80 คน\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":null,\"sort_order\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:17:20'),
(6, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\MeetingRoom', '3', NULL, '{\"id\":3,\"room_code\":\"CONF-MA\",\"name_th\":\"ห้องประชุมกลาง A\",\"name_en\":\"Medium Meeting Room A\",\"building_id\":3,\"floor\":1,\"room_number\":\"101\",\"capacity\":40,\"room_type\":\"conference\",\"room_layout\":null,\"has_projector\":true,\"has_video_conference\":false,\"has_whiteboard\":true,\"has_air_conditioning\":true,\"has_wifi\":null,\"has_audio_system\":false,\"has_recording\":null,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":0,\"half_day_rate\":null,\"full_day_rate\":null,\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องประชุมขนาดกลาง รองรับ 40 คน\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":null,\"sort_order\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:17:20'),
(7, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\MeetingRoom', '4', NULL, '{\"id\":4,\"room_code\":\"CONF-MB\",\"name_th\":\"ห้องประชุมกลาง B\",\"name_en\":\"Medium Meeting Room B\",\"building_id\":3,\"floor\":2,\"room_number\":\"201\",\"capacity\":30,\"room_type\":\"conference\",\"room_layout\":null,\"has_projector\":false,\"has_video_conference\":false,\"has_whiteboard\":true,\"has_air_conditioning\":true,\"has_wifi\":null,\"has_audio_system\":false,\"has_recording\":null,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":0,\"half_day_rate\":null,\"full_day_rate\":null,\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องประชุมขนาดกลาง รองรับ 30 คน พร้อมจอ LED\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":null,\"sort_order\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:17:20'),
(8, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\MeetingRoom', '5', NULL, '{\"id\":5,\"room_code\":\"CONF-S1\",\"name_th\":\"ห้องประชุมเล็ก 1\",\"name_en\":\"Small Meeting Room 1\",\"building_id\":2,\"floor\":4,\"room_number\":\"401\",\"capacity\":15,\"room_type\":\"huddle\",\"room_layout\":null,\"has_projector\":false,\"has_video_conference\":false,\"has_whiteboard\":true,\"has_air_conditioning\":true,\"has_wifi\":null,\"has_audio_system\":false,\"has_recording\":null,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":0,\"half_day_rate\":null,\"full_day_rate\":null,\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องประชุมขนาดเล็ก รองรับ 15 คน เหมาะสำหรับการประชุมทีมงาน\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":null,\"sort_order\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:17:20'),
(9, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\MeetingRoom', '6', NULL, '{\"id\":6,\"room_code\":\"CONF-S2\",\"name_th\":\"ห้องประชุมเล็ก 2\",\"name_en\":\"Small Meeting Room 2\",\"building_id\":2,\"floor\":4,\"room_number\":\"402\",\"capacity\":12,\"room_type\":\"huddle\",\"room_layout\":null,\"has_projector\":true,\"has_video_conference\":false,\"has_whiteboard\":false,\"has_air_conditioning\":true,\"has_wifi\":null,\"has_audio_system\":false,\"has_recording\":null,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":0,\"half_day_rate\":null,\"full_day_rate\":null,\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องประชุมขนาดเล็ก รองรับ 12 คน\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":null,\"sort_order\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:17:20'),
(10, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\MeetingRoom', '7', NULL, '{\"id\":7,\"room_code\":\"CONF-VIP\",\"name_th\":\"ห้องประชุม VIP\",\"name_en\":\"VIP Meeting Room\",\"building_id\":5,\"floor\":5,\"room_number\":\"501\",\"capacity\":20,\"room_type\":\"boardroom\",\"room_layout\":null,\"has_projector\":true,\"has_video_conference\":true,\"has_whiteboard\":true,\"has_air_conditioning\":true,\"has_wifi\":null,\"has_audio_system\":true,\"has_recording\":true,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":0,\"half_day_rate\":null,\"full_day_rate\":null,\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องประชุม VIP สำหรับผู้บริหาร มีระบบประชุมทางไกลคุณภาพสูง\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":null,\"sort_order\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:17:20'),
(11, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\MeetingRoom', '8', NULL, '{\"id\":8,\"room_code\":\"TRAIN-1\",\"name_th\":\"ห้องฝึกอบรม 1\",\"name_en\":\"Training Room 1\",\"building_id\":4,\"floor\":1,\"room_number\":\"101\",\"capacity\":50,\"room_type\":\"training\",\"room_layout\":null,\"has_projector\":true,\"has_video_conference\":false,\"has_whiteboard\":true,\"has_air_conditioning\":true,\"has_wifi\":null,\"has_audio_system\":true,\"has_recording\":null,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":500,\"half_day_rate\":null,\"full_day_rate\":null,\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องฝึกอบรม รองรับ 50 คน พร้อมคอมพิวเตอร์\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":null,\"sort_order\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:17:20'),
(12, NULL, NULL, '127.0.0.1', 'Console', 'update', 'common\\models\\MeetingRoom', '1', '{\"has_projector\":1,\"has_video_conference\":1,\"has_whiteboard\":1,\"has_air_conditioning\":1,\"has_audio_system\":1,\"requires_approval\":0,\"hourly_rate\":\"0.00\",\"updated_at\":\"2026-01-18 02:17:20\"}', '{\"id\":1,\"room_code\":\"CONF-L1\",\"name_th\":\"ห้องประชุมใหญ่ 1\",\"name_en\":\"Large Meeting Room 1\",\"building_id\":2,\"floor\":2,\"room_number\":\"201\",\"capacity\":100,\"room_type\":\"conference\",\"room_layout\":null,\"has_projector\":true,\"has_video_conference\":true,\"has_whiteboard\":true,\"has_air_conditioning\":true,\"has_wifi\":1,\"has_audio_system\":true,\"has_recording\":0,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":0,\"half_day_rate\":\"0.00\",\"full_day_rate\":\"0.00\",\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องประชุมขนาดใหญ่ รองรับ 100 คน มีระบบประชุมทางไกล\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":0,\"sort_order\":0,\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-01-18 02:17:20\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:19:15'),
(13, NULL, NULL, '127.0.0.1', 'Console', 'update', 'common\\models\\MeetingRoom', '2', '{\"has_projector\":1,\"has_video_conference\":0,\"has_whiteboard\":1,\"has_air_conditioning\":1,\"has_audio_system\":1,\"requires_approval\":0,\"hourly_rate\":\"0.00\",\"updated_at\":\"2026-01-18 02:17:20\"}', '{\"id\":2,\"room_code\":\"CONF-L2\",\"name_th\":\"ห้องประชุมใหญ่ 2\",\"name_en\":\"Large Meeting Room 2\",\"building_id\":2,\"floor\":3,\"room_number\":\"301\",\"capacity\":80,\"room_type\":\"conference\",\"room_layout\":null,\"has_projector\":true,\"has_video_conference\":false,\"has_whiteboard\":true,\"has_air_conditioning\":true,\"has_wifi\":1,\"has_audio_system\":true,\"has_recording\":0,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":0,\"half_day_rate\":\"0.00\",\"full_day_rate\":\"0.00\",\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องประชุมขนาดใหญ่ รองรับ 80 คน\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":0,\"sort_order\":0,\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-01-18 02:17:20\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:19:15'),
(14, NULL, NULL, '127.0.0.1', 'Console', 'update', 'common\\models\\MeetingRoom', '3', '{\"has_projector\":1,\"has_video_conference\":0,\"has_whiteboard\":1,\"has_air_conditioning\":1,\"has_audio_system\":0,\"requires_approval\":0,\"hourly_rate\":\"0.00\",\"updated_at\":\"2026-01-18 02:17:20\"}', '{\"id\":3,\"room_code\":\"CONF-MA\",\"name_th\":\"ห้องประชุมกลาง A\",\"name_en\":\"Medium Meeting Room A\",\"building_id\":3,\"floor\":1,\"room_number\":\"101\",\"capacity\":40,\"room_type\":\"conference\",\"room_layout\":null,\"has_projector\":true,\"has_video_conference\":false,\"has_whiteboard\":true,\"has_air_conditioning\":true,\"has_wifi\":1,\"has_audio_system\":false,\"has_recording\":0,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":0,\"half_day_rate\":\"0.00\",\"full_day_rate\":\"0.00\",\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องประชุมขนาดกลาง รองรับ 40 คน\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":0,\"sort_order\":0,\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-01-18 02:17:20\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:19:15'),
(15, NULL, NULL, '127.0.0.1', 'Console', 'update', 'common\\models\\MeetingRoom', '4', '{\"has_projector\":0,\"has_video_conference\":0,\"has_whiteboard\":1,\"has_air_conditioning\":1,\"has_audio_system\":0,\"requires_approval\":0,\"hourly_rate\":\"0.00\",\"updated_at\":\"2026-01-18 02:17:20\"}', '{\"id\":4,\"room_code\":\"CONF-MB\",\"name_th\":\"ห้องประชุมกลาง B\",\"name_en\":\"Medium Meeting Room B\",\"building_id\":3,\"floor\":2,\"room_number\":\"201\",\"capacity\":30,\"room_type\":\"conference\",\"room_layout\":null,\"has_projector\":false,\"has_video_conference\":false,\"has_whiteboard\":true,\"has_air_conditioning\":true,\"has_wifi\":1,\"has_audio_system\":false,\"has_recording\":0,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":0,\"half_day_rate\":\"0.00\",\"full_day_rate\":\"0.00\",\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องประชุมขนาดกลาง รองรับ 30 คน พร้อมจอ LED\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":0,\"sort_order\":0,\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-01-18 02:17:20\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:19:15'),
(16, NULL, NULL, '127.0.0.1', 'Console', 'update', 'common\\models\\MeetingRoom', '5', '{\"has_projector\":0,\"has_video_conference\":0,\"has_whiteboard\":1,\"has_air_conditioning\":1,\"has_audio_system\":0,\"requires_approval\":0,\"hourly_rate\":\"0.00\",\"updated_at\":\"2026-01-18 02:17:20\"}', '{\"id\":5,\"room_code\":\"CONF-S1\",\"name_th\":\"ห้องประชุมเล็ก 1\",\"name_en\":\"Small Meeting Room 1\",\"building_id\":2,\"floor\":4,\"room_number\":\"401\",\"capacity\":15,\"room_type\":\"huddle\",\"room_layout\":null,\"has_projector\":false,\"has_video_conference\":false,\"has_whiteboard\":true,\"has_air_conditioning\":true,\"has_wifi\":1,\"has_audio_system\":false,\"has_recording\":0,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":0,\"half_day_rate\":\"0.00\",\"full_day_rate\":\"0.00\",\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องประชุมขนาดเล็ก รองรับ 15 คน เหมาะสำหรับการประชุมทีมงาน\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":0,\"sort_order\":0,\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-01-18 02:17:20\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:19:15'),
(17, NULL, NULL, '127.0.0.1', 'Console', 'update', 'common\\models\\MeetingRoom', '6', '{\"has_projector\":1,\"has_video_conference\":0,\"has_whiteboard\":0,\"has_air_conditioning\":1,\"has_audio_system\":0,\"requires_approval\":0,\"hourly_rate\":\"0.00\",\"updated_at\":\"2026-01-18 02:17:20\"}', '{\"id\":6,\"room_code\":\"CONF-S2\",\"name_th\":\"ห้องประชุมเล็ก 2\",\"name_en\":\"Small Meeting Room 2\",\"building_id\":2,\"floor\":4,\"room_number\":\"402\",\"capacity\":12,\"room_type\":\"huddle\",\"room_layout\":null,\"has_projector\":true,\"has_video_conference\":false,\"has_whiteboard\":false,\"has_air_conditioning\":true,\"has_wifi\":1,\"has_audio_system\":false,\"has_recording\":0,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":0,\"half_day_rate\":\"0.00\",\"full_day_rate\":\"0.00\",\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องประชุมขนาดเล็ก รองรับ 12 คน\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":0,\"sort_order\":0,\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-01-18 02:17:20\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:19:15'),
(18, NULL, NULL, '127.0.0.1', 'Console', 'update', 'common\\models\\MeetingRoom', '7', '{\"has_projector\":1,\"has_video_conference\":1,\"has_whiteboard\":1,\"has_air_conditioning\":1,\"has_audio_system\":1,\"has_recording\":1,\"requires_approval\":0,\"hourly_rate\":\"0.00\",\"updated_at\":\"2026-01-18 02:17:20\"}', '{\"id\":7,\"room_code\":\"CONF-VIP\",\"name_th\":\"ห้องประชุม VIP\",\"name_en\":\"VIP Meeting Room\",\"building_id\":5,\"floor\":5,\"room_number\":\"501\",\"capacity\":20,\"room_type\":\"boardroom\",\"room_layout\":null,\"has_projector\":true,\"has_video_conference\":true,\"has_whiteboard\":true,\"has_air_conditioning\":true,\"has_wifi\":1,\"has_audio_system\":true,\"has_recording\":true,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":0,\"half_day_rate\":\"0.00\",\"full_day_rate\":\"0.00\",\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องประชุม VIP สำหรับผู้บริหาร มีระบบประชุมทางไกลคุณภาพสูง\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":0,\"sort_order\":0,\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-01-18 02:17:20\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:19:15'),
(19, NULL, NULL, '127.0.0.1', 'Console', 'update', 'common\\models\\MeetingRoom', '8', '{\"has_projector\":1,\"has_video_conference\":0,\"has_whiteboard\":1,\"has_air_conditioning\":1,\"has_audio_system\":1,\"requires_approval\":0,\"hourly_rate\":\"500.00\",\"updated_at\":\"2026-01-18 02:17:20\"}', '{\"id\":8,\"room_code\":\"TRAIN-1\",\"name_th\":\"ห้องฝึกอบรม 1\",\"name_en\":\"Training Room 1\",\"building_id\":4,\"floor\":1,\"room_number\":\"101\",\"capacity\":50,\"room_type\":\"training\",\"room_layout\":null,\"has_projector\":true,\"has_video_conference\":false,\"has_whiteboard\":true,\"has_air_conditioning\":true,\"has_wifi\":1,\"has_audio_system\":true,\"has_recording\":0,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":500,\"half_day_rate\":\"0.00\",\"full_day_rate\":\"0.00\",\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องฝึกอบรม รองรับ 50 คน พร้อมคอมพิวเตอร์\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":0,\"sort_order\":0,\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-01-18 02:17:20\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:19:15'),
(20, NULL, NULL, '127.0.0.1', 'Console', 'update', 'common\\models\\MeetingRoom', '1', '{\"has_projector\":1,\"has_video_conference\":1,\"has_whiteboard\":1,\"has_air_conditioning\":1,\"has_audio_system\":1,\"requires_approval\":0,\"hourly_rate\":\"0.00\",\"updated_at\":\"2026-01-18 02:19:15\"}', '{\"id\":1,\"room_code\":\"CONF-L1\",\"name_th\":\"ห้องประชุมใหญ่ 1\",\"name_en\":\"Large Meeting Room 1\",\"building_id\":2,\"floor\":2,\"room_number\":\"201\",\"capacity\":100,\"room_type\":\"conference\",\"room_layout\":null,\"has_projector\":true,\"has_video_conference\":true,\"has_whiteboard\":true,\"has_air_conditioning\":true,\"has_wifi\":1,\"has_audio_system\":true,\"has_recording\":0,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":0,\"half_day_rate\":\"0.00\",\"full_day_rate\":\"0.00\",\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องประชุมขนาดใหญ่ รองรับ 100 คน มีระบบประชุมทางไกล\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":0,\"sort_order\":0,\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-01-18 02:17:20\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:42:04'),
(21, NULL, NULL, '127.0.0.1', 'Console', 'update', 'common\\models\\MeetingRoom', '2', '{\"has_projector\":1,\"has_video_conference\":0,\"has_whiteboard\":1,\"has_air_conditioning\":1,\"has_audio_system\":1,\"requires_approval\":0,\"hourly_rate\":\"0.00\",\"updated_at\":\"2026-01-18 02:19:15\"}', '{\"id\":2,\"room_code\":\"CONF-L2\",\"name_th\":\"ห้องประชุมใหญ่ 2\",\"name_en\":\"Large Meeting Room 2\",\"building_id\":2,\"floor\":3,\"room_number\":\"301\",\"capacity\":80,\"room_type\":\"conference\",\"room_layout\":null,\"has_projector\":true,\"has_video_conference\":false,\"has_whiteboard\":true,\"has_air_conditioning\":true,\"has_wifi\":1,\"has_audio_system\":true,\"has_recording\":0,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":0,\"half_day_rate\":\"0.00\",\"full_day_rate\":\"0.00\",\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องประชุมขนาดใหญ่ รองรับ 80 คน\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":0,\"sort_order\":0,\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-01-18 02:17:20\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:42:04'),
(22, NULL, NULL, '127.0.0.1', 'Console', 'update', 'common\\models\\MeetingRoom', '3', '{\"has_projector\":1,\"has_video_conference\":0,\"has_whiteboard\":1,\"has_air_conditioning\":1,\"has_audio_system\":0,\"requires_approval\":0,\"hourly_rate\":\"0.00\",\"updated_at\":\"2026-01-18 02:19:15\"}', '{\"id\":3,\"room_code\":\"CONF-MA\",\"name_th\":\"ห้องประชุมกลาง A\",\"name_en\":\"Medium Meeting Room A\",\"building_id\":3,\"floor\":1,\"room_number\":\"101\",\"capacity\":40,\"room_type\":\"conference\",\"room_layout\":null,\"has_projector\":true,\"has_video_conference\":false,\"has_whiteboard\":true,\"has_air_conditioning\":true,\"has_wifi\":1,\"has_audio_system\":false,\"has_recording\":0,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":0,\"half_day_rate\":\"0.00\",\"full_day_rate\":\"0.00\",\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องประชุมขนาดกลาง รองรับ 40 คน\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":0,\"sort_order\":0,\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-01-18 02:17:20\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:42:04'),
(23, NULL, NULL, '127.0.0.1', 'Console', 'update', 'common\\models\\MeetingRoom', '4', '{\"has_projector\":0,\"has_video_conference\":0,\"has_whiteboard\":1,\"has_air_conditioning\":1,\"has_audio_system\":0,\"requires_approval\":0,\"hourly_rate\":\"0.00\",\"updated_at\":\"2026-01-18 02:19:15\"}', '{\"id\":4,\"room_code\":\"CONF-MB\",\"name_th\":\"ห้องประชุมกลาง B\",\"name_en\":\"Medium Meeting Room B\",\"building_id\":3,\"floor\":2,\"room_number\":\"201\",\"capacity\":30,\"room_type\":\"conference\",\"room_layout\":null,\"has_projector\":false,\"has_video_conference\":false,\"has_whiteboard\":true,\"has_air_conditioning\":true,\"has_wifi\":1,\"has_audio_system\":false,\"has_recording\":0,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":0,\"half_day_rate\":\"0.00\",\"full_day_rate\":\"0.00\",\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องประชุมขนาดกลาง รองรับ 30 คน พร้อมจอ LED\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":0,\"sort_order\":0,\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-01-18 02:17:20\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:42:04'),
(24, NULL, NULL, '127.0.0.1', 'Console', 'update', 'common\\models\\MeetingRoom', '5', '{\"has_projector\":0,\"has_video_conference\":0,\"has_whiteboard\":1,\"has_air_conditioning\":1,\"has_audio_system\":0,\"requires_approval\":0,\"hourly_rate\":\"0.00\",\"updated_at\":\"2026-01-18 02:19:15\"}', '{\"id\":5,\"room_code\":\"CONF-S1\",\"name_th\":\"ห้องประชุมเล็ก 1\",\"name_en\":\"Small Meeting Room 1\",\"building_id\":2,\"floor\":4,\"room_number\":\"401\",\"capacity\":15,\"room_type\":\"huddle\",\"room_layout\":null,\"has_projector\":false,\"has_video_conference\":false,\"has_whiteboard\":true,\"has_air_conditioning\":true,\"has_wifi\":1,\"has_audio_system\":false,\"has_recording\":0,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":0,\"half_day_rate\":\"0.00\",\"full_day_rate\":\"0.00\",\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องประชุมขนาดเล็ก รองรับ 15 คน เหมาะสำหรับการประชุมทีมงาน\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":0,\"sort_order\":0,\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-01-18 02:17:20\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:42:04'),
(25, NULL, NULL, '127.0.0.1', 'Console', 'update', 'common\\models\\MeetingRoom', '6', '{\"has_projector\":1,\"has_video_conference\":0,\"has_whiteboard\":0,\"has_air_conditioning\":1,\"has_audio_system\":0,\"requires_approval\":0,\"hourly_rate\":\"0.00\",\"updated_at\":\"2026-01-18 02:19:15\"}', '{\"id\":6,\"room_code\":\"CONF-S2\",\"name_th\":\"ห้องประชุมเล็ก 2\",\"name_en\":\"Small Meeting Room 2\",\"building_id\":2,\"floor\":4,\"room_number\":\"402\",\"capacity\":12,\"room_type\":\"huddle\",\"room_layout\":null,\"has_projector\":true,\"has_video_conference\":false,\"has_whiteboard\":false,\"has_air_conditioning\":true,\"has_wifi\":1,\"has_audio_system\":false,\"has_recording\":0,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":0,\"half_day_rate\":\"0.00\",\"full_day_rate\":\"0.00\",\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องประชุมขนาดเล็ก รองรับ 12 คน\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":0,\"sort_order\":0,\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-01-18 02:17:20\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:42:04'),
(26, NULL, NULL, '127.0.0.1', 'Console', 'update', 'common\\models\\MeetingRoom', '7', '{\"has_projector\":1,\"has_video_conference\":1,\"has_whiteboard\":1,\"has_air_conditioning\":1,\"has_audio_system\":1,\"has_recording\":1,\"requires_approval\":0,\"hourly_rate\":\"0.00\",\"updated_at\":\"2026-01-18 02:19:15\"}', '{\"id\":7,\"room_code\":\"CONF-VIP\",\"name_th\":\"ห้องประชุม VIP\",\"name_en\":\"VIP Meeting Room\",\"building_id\":5,\"floor\":5,\"room_number\":\"501\",\"capacity\":20,\"room_type\":\"boardroom\",\"room_layout\":null,\"has_projector\":true,\"has_video_conference\":true,\"has_whiteboard\":true,\"has_air_conditioning\":true,\"has_wifi\":1,\"has_audio_system\":true,\"has_recording\":true,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":0,\"half_day_rate\":\"0.00\",\"full_day_rate\":\"0.00\",\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องประชุม VIP สำหรับผู้บริหาร มีระบบประชุมทางไกลคุณภาพสูง\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":0,\"sort_order\":0,\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-01-18 02:17:20\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:42:04'),
(27, NULL, NULL, '127.0.0.1', 'Console', 'update', 'common\\models\\MeetingRoom', '8', '{\"has_projector\":1,\"has_video_conference\":0,\"has_whiteboard\":1,\"has_air_conditioning\":1,\"has_audio_system\":1,\"requires_approval\":0,\"hourly_rate\":\"500.00\",\"updated_at\":\"2026-01-18 02:19:15\"}', '{\"id\":8,\"room_code\":\"TRAIN-1\",\"name_th\":\"ห้องฝึกอบรม 1\",\"name_en\":\"Training Room 1\",\"building_id\":4,\"floor\":1,\"room_number\":\"101\",\"capacity\":50,\"room_type\":\"training\",\"room_layout\":null,\"has_projector\":true,\"has_video_conference\":false,\"has_whiteboard\":true,\"has_air_conditioning\":true,\"has_wifi\":1,\"has_audio_system\":true,\"has_recording\":0,\"min_booking_duration\":30,\"max_booking_duration\":480,\"advance_booking_days\":30,\"requires_approval\":false,\"allowed_departments\":null,\"hourly_rate\":500,\"half_day_rate\":\"0.00\",\"full_day_rate\":\"0.00\",\"operating_start_time\":\"08:00:00\",\"operating_end_time\":\"18:00:00\",\"available_days\":\"[1,2,3,4,5]\",\"description\":\"ห้องฝึกอบรม รองรับ 50 คน พร้อมคอมพิวเตอร์\",\"usage_rules\":null,\"contact_person\":null,\"contact_phone\":null,\"status\":1,\"is_featured\":0,\"sort_order\":0,\"created_by\":null,\"updated_by\":null,\"created_at\":\"2026-01-18 02:17:20\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 19:42:04'),
(28, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\User', '2', NULL, '{\"id\":2,\"username\":\"superadmin\",\"email\":\"superadmin@bizco.co.th\",\"password_hash\":\"$2y$13$55LQK3f\\/1iMrdG39XbcbQOVw0VXTaFs51XFIxAnsY4sP7iVqiQRX2\",\"auth_key\":\"f_JcOO8kWxLNUrBMW2PTQmio0fRcFnQQ\",\"password_reset_token\":null,\"verification_token\":null,\"email_verified\":1,\"email_verified_at\":null,\"full_name\":\"ผู้ดูแลระบบสูงสุด\",\"first_name\":null,\"last_name\":null,\"phone\":\"02-712-7000\",\"avatar\":null,\"department_id\":1,\"position\":null,\"azure_id\":null,\"google_id\":null,\"thaid_id\":null,\"facebook_id\":null,\"two_factor_secret\":null,\"two_factor_enabled\":null,\"backup_codes\":null,\"failed_login_attempts\":null,\"locked_until\":null,\"password_changed_at\":\"2026-01-18 03:11:31\",\"last_login_at\":null,\"last_login_ip\":null,\"status\":10,\"role\":\"superadmin\",\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 20:11:31'),
(29, NULL, NULL, '127.0.0.1', 'Console', 'update', 'common\\models\\User', '1', '{\"email_verified\":0,\"full_name\":\"\",\"phone\":\"\",\"department_id\":null,\"role\":\"superadmin\",\"updated_at\":\"2026-01-18 00:08:28\"}', '{\"id\":1,\"username\":\"admin\",\"email\":\"admin@example.com\",\"password_hash\":\"$2y$13$PA\\/wjsKQMoWuuvqQlB35yu2ELLjanSFOpSu1XLO29\\/BM6N6TOcNKK\",\"auth_key\":\"JoSb1NFibHQ7G6AnT9JH_MUjxxX2GKF9\",\"password_reset_token\":null,\"verification_token\":null,\"email_verified\":1,\"email_verified_at\":null,\"full_name\":\"ผู้ดูแลระบบ\",\"first_name\":\"System\",\"last_name\":\"Administrator\",\"phone\":\"02-712-7000\",\"avatar\":\"\\/uploads\\/avatars\\/avatar_1_1768669043.jpg\",\"department_id\":1,\"position\":\"\",\"azure_id\":null,\"google_id\":null,\"thaid_id\":null,\"facebook_id\":null,\"two_factor_secret\":null,\"two_factor_enabled\":0,\"backup_codes\":null,\"failed_login_attempts\":0,\"locked_until\":null,\"password_changed_at\":\"2026-01-17 23:25:05\",\"last_login_at\":\"2026-01-18 00:08:28\",\"last_login_ip\":\"127.0.0.1\",\"status\":10,\"role\":\"admin\",\"created_at\":\"2026-01-17 23:25:05\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 20:11:31'),
(30, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\User', '3', NULL, '{\"id\":3,\"username\":\"approver\",\"email\":\"approver@bizco.co.th\",\"password_hash\":\"$2y$13$rN54cKpJ423OrK62X3PwY..UifEZMeRHC6xl8U5AS7ieJTy8N0WoO\",\"auth_key\":\"HPdZlxarFjbYXHEb1engDE4zkYs0l1yz\",\"password_reset_token\":null,\"verification_token\":null,\"email_verified\":1,\"email_verified_at\":null,\"full_name\":\"ผู้อนุมัติ\",\"first_name\":null,\"last_name\":null,\"phone\":\"02-712-7000\",\"avatar\":null,\"department_id\":1,\"position\":null,\"azure_id\":null,\"google_id\":null,\"thaid_id\":null,\"facebook_id\":null,\"two_factor_secret\":null,\"two_factor_enabled\":null,\"backup_codes\":null,\"failed_login_attempts\":null,\"locked_until\":null,\"password_changed_at\":\"2026-01-18 03:11:31\",\"last_login_at\":null,\"last_login_ip\":null,\"status\":10,\"role\":\"approver\",\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 20:11:31'),
(31, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\User', '4', NULL, '{\"id\":4,\"username\":\"user1\",\"email\":\"user1@bizco.co.th\",\"password_hash\":\"$2y$13$kn7iUbGbzym0nGDoBLBryeuHzna\\/PZmKP4Gy9dUKCco0PRAsbzb1e\",\"auth_key\":\"_k_2Ah4nADRHSjTQuNqp5UWNQBBK9DvJ\",\"password_reset_token\":null,\"verification_token\":null,\"email_verified\":1,\"email_verified_at\":null,\"full_name\":\"สมชาย ใจดี\",\"first_name\":null,\"last_name\":null,\"phone\":\"02-712-7000\",\"avatar\":null,\"department_id\":1,\"position\":null,\"azure_id\":null,\"google_id\":null,\"thaid_id\":null,\"facebook_id\":null,\"two_factor_secret\":null,\"two_factor_enabled\":null,\"backup_codes\":null,\"failed_login_attempts\":null,\"locked_until\":null,\"password_changed_at\":\"2026-01-18 03:11:31\",\"last_login_at\":null,\"last_login_ip\":null,\"status\":10,\"role\":\"user\",\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 20:11:31'),
(32, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\User', '5', NULL, '{\"id\":5,\"username\":\"user2\",\"email\":\"user2@bizco.co.th\",\"password_hash\":\"$2y$13$i.lpydRLCpJZN4sLY9B4WejEvavGwILTAfJb2I.MJvHfOkuaf9n5e\",\"auth_key\":\"Qc04Avrew9zSmFdpaur7lMOva6fCswFL\",\"password_reset_token\":null,\"verification_token\":null,\"email_verified\":1,\"email_verified_at\":null,\"full_name\":\"สมหญิง รักเรียน\",\"first_name\":null,\"last_name\":null,\"phone\":\"02-712-7000\",\"avatar\":null,\"department_id\":1,\"position\":null,\"azure_id\":null,\"google_id\":null,\"thaid_id\":null,\"facebook_id\":null,\"two_factor_secret\":null,\"two_factor_enabled\":null,\"backup_codes\":null,\"failed_login_attempts\":null,\"locked_until\":null,\"password_changed_at\":\"2026-01-18 03:11:32\",\"last_login_at\":null,\"last_login_ip\":null,\"status\":10,\"role\":\"user\",\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 20:11:32'),
(33, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\User', '6', NULL, '{\"id\":6,\"username\":\"user3\",\"email\":\"user3@bizco.co.th\",\"password_hash\":\"$2y$13$kOSpHIF7bAe2399Fg8VgX.sluKnkPgF5\\/woneZJqk5Sunh0SJAcdC\",\"auth_key\":\"5tyoVO0E5ezts2anJvxLCqpIi347_Zez\",\"password_reset_token\":null,\"verification_token\":null,\"email_verified\":1,\"email_verified_at\":null,\"full_name\":\"วิชัย พัฒนา\",\"first_name\":null,\"last_name\":null,\"phone\":\"02-712-7000\",\"avatar\":null,\"department_id\":1,\"position\":null,\"azure_id\":null,\"google_id\":null,\"thaid_id\":null,\"facebook_id\":null,\"two_factor_secret\":null,\"two_factor_enabled\":null,\"backup_codes\":null,\"failed_login_attempts\":null,\"locked_until\":null,\"password_changed_at\":\"2026-01-18 03:11:32\",\"last_login_at\":null,\"last_login_ip\":null,\"status\":10,\"role\":\"user\",\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', NULL, NULL, '2026-01-17 20:11:32'),
(34, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '1', NULL, '{\"id\":1,\"booking_code\":\"BK26010001\",\"room_id\":7,\"user_id\":4,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-01-31 13:00:00\",\"end_time\":\"2026-01-31 17:00:00\",\"duration_minutes\":240,\"meeting_title\":\"นำเสนอโครงการ\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":20,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":3,\"approved_at\":\"2026-01-07 04:36:39\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010001', '2026-01-17 20:36:39'),
(35, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '2', NULL, '{\"id\":2,\"booking_code\":\"BK26010002\",\"room_id\":2,\"user_id\":6,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-01-22 15:00:00\",\"end_time\":\"2026-01-22 16:00:00\",\"duration_minutes\":60,\"meeting_title\":\"ประชุมผู้บริหาร\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":79,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":null,\"approved_at\":\"2026-01-18 03:36:39\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010002', '2026-01-17 20:36:39'),
(36, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '3', NULL, '{\"id\":3,\"booking_code\":\"BK26010003\",\"room_id\":8,\"user_id\":4,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-02-08 13:00:00\",\"end_time\":\"2026-02-08 17:00:00\",\"duration_minutes\":240,\"meeting_title\":\"ประชุมทีมงาน\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":8,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":null,\"approved_at\":\"2026-01-18 03:36:39\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":2000,\"total_equipment_cost\":null,\"total_cost\":2000,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010003', '2026-01-17 20:36:39'),
(37, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '4', NULL, '{\"id\":4,\"booking_code\":\"BK26010004\",\"room_id\":3,\"user_id\":6,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-02-13 16:00:00\",\"end_time\":\"2026-02-13 19:00:00\",\"duration_minutes\":180,\"meeting_title\":\"ประชุมงบประมาณ\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":26,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":3,\"approved_at\":\"2025-12-28 04:36:39\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010004', '2026-01-17 20:36:39'),
(38, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '5', NULL, '{\"id\":5,\"booking_code\":\"BK26010005\",\"room_id\":1,\"user_id\":6,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-01-27 12:00:00\",\"end_time\":\"2026-01-27 14:00:00\",\"duration_minutes\":120,\"meeting_title\":\"ประชุมงบประมาณ\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":72,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":null,\"approved_at\":\"2026-01-18 03:36:39\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010005', '2026-01-17 20:36:39'),
(39, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '6', NULL, '{\"id\":6,\"booking_code\":\"BK26010006\",\"room_id\":2,\"user_id\":6,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-02-01 9:00:00\",\"end_time\":\"2026-02-01 12:00:00\",\"duration_minutes\":180,\"meeting_title\":\"ประชุมวิชาการ\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":38,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":3,\"approved_at\":\"2026-01-16 04:36:39\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010006', '2026-01-17 20:36:39');
INSERT INTO `audit_log` (`id`, `user_id`, `username`, `ip_address`, `user_agent`, `action`, `model_class`, `model_id`, `old_values`, `new_values`, `url`, `description`, `created_at`) VALUES
(40, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '7', NULL, '{\"id\":7,\"booking_code\":\"BK26010007\",\"room_id\":8,\"user_id\":6,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-02-11 9:00:00\",\"end_time\":\"2026-02-11 11:00:00\",\"duration_minutes\":120,\"meeting_title\":\"ประชุมงบประมาณ\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":20,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":null,\"approved_at\":\"2026-01-18 03:38:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":1000,\"total_equipment_cost\":null,\"total_cost\":1000,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010007', '2026-01-17 20:38:13'),
(41, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '8', NULL, '{\"id\":8,\"booking_code\":\"BK26010008\",\"room_id\":7,\"user_id\":6,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-01-24 12:00:00\",\"end_time\":\"2026-01-24 13:00:00\",\"duration_minutes\":60,\"meeting_title\":\"ประชุมผู้บริหาร\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":13,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":null,\"approved_at\":\"2026-01-18 03:38:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010008', '2026-01-17 20:38:13'),
(42, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '9', NULL, '{\"id\":9,\"booking_code\":\"BK26010009\",\"room_id\":5,\"user_id\":5,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-02-10 14:00:00\",\"end_time\":\"2026-02-10 15:00:00\",\"duration_minutes\":60,\"meeting_title\":\"ประชุมคณะกรรมการ\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":10,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":3,\"approved_at\":\"2025-12-25 04:38:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010009', '2026-01-17 20:38:13'),
(43, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '10', NULL, '{\"id\":10,\"booking_code\":\"BK26010010\",\"room_id\":6,\"user_id\":4,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-01-27 15:00:00\",\"end_time\":\"2026-01-27 16:00:00\",\"duration_minutes\":60,\"meeting_title\":\"ประชุมบุคลากร\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":10,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":null,\"approved_at\":\"2026-01-18 03:38:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010010', '2026-01-17 20:38:13'),
(44, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '11', NULL, '{\"id\":11,\"booking_code\":\"BK26010011\",\"room_id\":6,\"user_id\":5,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-01-11 12:00:00\",\"end_time\":\"2026-01-11 15:00:00\",\"duration_minutes\":180,\"meeting_title\":\"ประชุมผู้บริหาร\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":10,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"completed\",\"approved_by\":3,\"approved_at\":\"2025-12-21 04:38:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010011', '2026-01-17 20:38:13'),
(45, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '12', NULL, '{\"id\":12,\"booking_code\":\"BK26010012\",\"room_id\":4,\"user_id\":6,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-02-03 15:00:00\",\"end_time\":\"2026-02-03 19:00:00\",\"duration_minutes\":240,\"meeting_title\":\"ประชุมทีมงาน\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":14,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":3,\"approved_at\":\"2025-12-31 04:38:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010012', '2026-01-17 20:38:13'),
(46, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '13', NULL, '{\"id\":13,\"booking_code\":\"BK26010013\",\"room_id\":2,\"user_id\":6,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-01-25 12:00:00\",\"end_time\":\"2026-01-25 13:00:00\",\"duration_minutes\":60,\"meeting_title\":\"นำเสนอโครงการ\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":67,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":3,\"approved_at\":\"2025-12-30 04:38:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010013', '2026-01-17 20:38:13'),
(47, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '14', NULL, '{\"id\":14,\"booking_code\":\"BK26010014\",\"room_id\":5,\"user_id\":5,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-02-14 16:00:00\",\"end_time\":\"2026-02-14 18:00:00\",\"duration_minutes\":120,\"meeting_title\":\"อบรมเชิงปฏิบัติการ\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":8,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":null,\"approved_at\":\"2026-01-18 03:38:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010014', '2026-01-17 20:38:13'),
(48, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '15', NULL, '{\"id\":15,\"booking_code\":\"BK26010015\",\"room_id\":1,\"user_id\":5,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-02-11 8:00:00\",\"end_time\":\"2026-02-11 10:00:00\",\"duration_minutes\":120,\"meeting_title\":\"สัมมนาออนไลน์\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":98,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":null,\"approved_at\":\"2026-01-18 03:38:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010015', '2026-01-17 20:38:13'),
(49, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '16', NULL, '{\"id\":16,\"booking_code\":\"BK26010016\",\"room_id\":7,\"user_id\":4,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-01-11 10:00:00\",\"end_time\":\"2026-01-11 12:00:00\",\"duration_minutes\":120,\"meeting_title\":\"ประชุมบุคลากร\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":12,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"completed\",\"approved_by\":3,\"approved_at\":\"2026-01-07 04:38:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010016', '2026-01-17 20:38:13'),
(50, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '17', NULL, '{\"id\":17,\"booking_code\":\"BK26010017\",\"room_id\":1,\"user_id\":6,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-02-07 10:00:00\",\"end_time\":\"2026-02-07 11:00:00\",\"duration_minutes\":60,\"meeting_title\":\"ประชุมคณะกรรมการ\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":98,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":null,\"approved_at\":\"2026-01-18 03:38:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010017', '2026-01-17 20:38:13'),
(51, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '18', NULL, '{\"id\":18,\"booking_code\":\"BK26010018\",\"room_id\":4,\"user_id\":6,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-01-16 9:00:00\",\"end_time\":\"2026-01-16 12:00:00\",\"duration_minutes\":180,\"meeting_title\":\"ประชุมทีมงาน\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":5,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"completed\",\"approved_by\":3,\"approved_at\":\"2025-12-30 04:38:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010018', '2026-01-17 20:38:13'),
(52, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '19', NULL, '{\"id\":19,\"booking_code\":\"BK26010019\",\"room_id\":5,\"user_id\":6,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-02-02 11:00:00\",\"end_time\":\"2026-02-02 14:00:00\",\"duration_minutes\":180,\"meeting_title\":\"อบรมเชิงปฏิบัติการ\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":14,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":null,\"approved_at\":\"2026-01-18 03:38:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010019', '2026-01-17 20:38:13'),
(53, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '20', NULL, '{\"id\":20,\"booking_code\":\"BK26010020\",\"room_id\":2,\"user_id\":4,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-02-10 13:00:00\",\"end_time\":\"2026-02-10 15:00:00\",\"duration_minutes\":120,\"meeting_title\":\"นำเสนอโครงการ\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":71,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":3,\"approved_at\":\"2025-12-21 04:38:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010020', '2026-01-17 20:38:13'),
(54, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '21', NULL, '{\"id\":21,\"booking_code\":\"BK26010021\",\"room_id\":1,\"user_id\":5,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-02-08 11:00:00\",\"end_time\":\"2026-02-08 12:00:00\",\"duration_minutes\":60,\"meeting_title\":\"อบรมเชิงปฏิบัติการ\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":65,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":null,\"approved_at\":\"2026-01-18 03:38:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010021', '2026-01-17 20:38:13'),
(55, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '22', NULL, '{\"id\":22,\"booking_code\":\"BK26010022\",\"room_id\":6,\"user_id\":6,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-01-31 9:00:00\",\"end_time\":\"2026-01-31 10:00:00\",\"duration_minutes\":60,\"meeting_title\":\"ประชุมทีมงาน\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":9,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":null,\"approved_at\":\"2026-01-18 03:38:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010022', '2026-01-17 20:38:13'),
(56, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '23', NULL, '{\"id\":23,\"booking_code\":\"BK26010023\",\"room_id\":6,\"user_id\":4,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-01-21 16:00:00\",\"end_time\":\"2026-01-21 20:00:00\",\"duration_minutes\":240,\"meeting_title\":\"ประชุมทีมงาน\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":7,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":null,\"approved_at\":\"2026-01-18 03:38:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010023', '2026-01-17 20:38:13'),
(57, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '24', NULL, '{\"id\":24,\"booking_code\":\"BK26010024\",\"room_id\":5,\"user_id\":5,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-02-09 15:00:00\",\"end_time\":\"2026-02-09 16:00:00\",\"duration_minutes\":60,\"meeting_title\":\"ประชุมทีมงาน\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":7,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":null,\"approved_at\":\"2026-01-18 03:38:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancellation_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010024', '2026-01-17 20:38:13'),
(58, 1, 'admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'create', 'common\\models\\Booking', '25', NULL, '{\"id\":25,\"booking_code\":\"BK26010025\",\"room_id\":\"5\",\"user_id\":1,\"department_id\":1,\"booking_date\":\"2026-01-19\",\"start_time\":\"09:00\",\"end_time\":\"16:30\",\"duration_minutes\":450,\"meeting_title\":\"ประชุมประจำเดือนกองดิจิทัล\",\"meeting_description\":\"ประชุมประจำเดือนกองดิจิทัล ครั้งที่ 1\\/2569\",\"meeting_type\":\"internal\",\"attendees_count\":\"15\",\"external_attendees\":null,\"contact_person\":\"สมชาย\",\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":false,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":null,\"approved_at\":\"2026-01-18 09:07:33\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancel_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":0,\"total_cost\":0,\"special_requests\":\"\",\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":1,\"updated_by\":1,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', 'http://www.mrb.test/frontend/web/booking/create', 'Booking: BK26010025', '2026-01-18 02:07:34'),
(59, 1, 'admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'update', 'common\\models\\Booking', '25', NULL, '{\"id\":25,\"booking_code\":\"BK26010025\",\"room_id\":\"5\",\"user_id\":1,\"department_id\":1,\"booking_date\":\"2026-01-19\",\"start_time\":\"09:00\",\"end_time\":\"16:30\",\"duration_minutes\":450,\"meeting_title\":\"ประชุมประจำเดือนกองดิจิทัล\",\"meeting_description\":\"ประชุมประจำเดือนกองดิจิทัล ครั้งที่ 1\\/2569\",\"meeting_type\":\"internal\",\"attendees_count\":\"15\",\"external_attendees\":null,\"contact_person\":\"สมชาย\",\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":false,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":null,\"approved_at\":\"2026-01-18 09:07:33\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancel_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":0,\"total_cost\":0,\"special_requests\":\"\",\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":1,\"updated_by\":1,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', 'http://www.mrb.test/frontend/web/booking/create', 'Booking: BK26010025', '2026-01-18 02:07:34'),
(60, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '26', NULL, '{\"id\":26,\"booking_code\":\"BK26010026\",\"booking_title\":null,\"room_id\":3,\"user_id\":5,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-02-02 10:00:00\",\"end_time\":\"2026-02-02 11:00:00\",\"duration_minutes\":60,\"meeting_title\":\"ประชุมติดตามงาน\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":34,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":null,\"approved_at\":\"2026-01-20 00:26:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancel_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010026', '2026-01-19 17:26:13'),
(61, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '27', NULL, '{\"id\":27,\"booking_code\":\"BK26010027\",\"booking_title\":null,\"room_id\":1,\"user_id\":5,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-02-17 16:00:00\",\"end_time\":\"2026-02-17 18:00:00\",\"duration_minutes\":120,\"meeting_title\":\"ประชุมวิชาการ\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":34,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":null,\"approved_at\":\"2026-01-20 00:26:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancel_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010027', '2026-01-19 17:26:13'),
(62, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '28', NULL, '{\"id\":28,\"booking_code\":\"BK26010028\",\"booking_title\":null,\"room_id\":3,\"user_id\":6,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-01-22 13:00:00\",\"end_time\":\"2026-01-22 16:00:00\",\"duration_minutes\":180,\"meeting_title\":\"อบรมเชิงปฏิบัติการ\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":6,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":null,\"approved_at\":\"2026-01-09 01:26:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancel_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010028', '2026-01-19 17:26:13'),
(63, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '29', NULL, '{\"id\":29,\"booking_code\":\"BK26010029\",\"booking_title\":null,\"room_id\":7,\"user_id\":5,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-02-03 9:00:00\",\"end_time\":\"2026-02-03 10:00:00\",\"duration_minutes\":60,\"meeting_title\":\"ประชุมคณะกรรมการ\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":7,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":null,\"approved_at\":\"2026-01-15 01:26:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancel_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010029', '2026-01-19 17:26:13'),
(64, NULL, NULL, '127.0.0.1', 'Console', 'create', 'common\\models\\Booking', '30', NULL, '{\"id\":30,\"booking_code\":\"BK26010030\",\"booking_title\":null,\"room_id\":4,\"user_id\":5,\"department_id\":null,\"booking_date\":null,\"start_time\":\"2026-02-17 8:00:00\",\"end_time\":\"2026-02-17 9:00:00\",\"duration_minutes\":60,\"meeting_title\":\"ประชุมวิชาการ\",\"meeting_description\":\"การจองตัวอย่างสำหรับทดสอบระบบ\",\"meeting_type\":null,\"attendees_count\":26,\"external_attendees\":null,\"contact_person\":null,\"contact_phone\":null,\"contact_email\":null,\"is_recurring\":null,\"recurrence_pattern\":null,\"recurrence_end_date\":null,\"parent_booking_id\":null,\"status\":\"approved\",\"approved_by\":null,\"approved_at\":\"2026-01-17 01:26:13\",\"rejection_reason\":null,\"cancelled_by\":null,\"cancelled_at\":null,\"cancel_reason\":null,\"total_room_cost\":0,\"total_equipment_cost\":null,\"total_cost\":0,\"special_requests\":null,\"internal_notes\":null,\"check_in_at\":null,\"check_out_at\":null,\"created_by\":null,\"updated_by\":null,\"created_at\":{\"expression\":\"NOW()\",\"params\":[]},\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]}}', NULL, 'Booking: BK26010030', '2026-01-19 17:26:13'),
(65, 1, 'admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'update', 'common\\models\\User', '1', '{\"last_login_at\":\"2026-01-18 00:08:28\"}', '{\"id\":1,\"username\":\"admin\",\"email\":\"admin@example.com\",\"password_hash\":\"$2y$13$PA\\/wjsKQMoWuuvqQlB35yu2ELLjanSFOpSu1XLO29\\/BM6N6TOcNKK\",\"auth_key\":\"JoSb1NFibHQ7G6AnT9JH_MUjxxX2GKF9\",\"password_reset_token\":null,\"verification_token\":null,\"email_verified\":1,\"email_verified_at\":null,\"title\":null,\"full_name\":\"ผู้ดูแลระบบ\",\"first_name\":\"System\",\"last_name\":\"Administrator\",\"address\":null,\"phone\":\"02-712-7000\",\"avatar\":\"\\/uploads\\/avatars\\/avatar_1_1768669043.jpg\",\"department_id\":1,\"position\":\"\",\"azure_id\":null,\"google_id\":null,\"thaid_id\":null,\"facebook_id\":null,\"two_factor_secret\":null,\"two_factor_enabled\":0,\"backup_codes\":null,\"failed_login_attempts\":0,\"locked_until\":null,\"password_changed_at\":\"2026-01-17 23:25:05\",\"last_login_at\":\"2026-01-20 00:46:33\",\"last_login_ip\":\"127.0.0.1\",\"status\":10,\"role\":\"admin\",\"created_at\":\"2026-01-17 23:25:05\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', 'http://frontend.mrb.test/login', NULL, '2026-01-19 17:46:33'),
(66, 1, 'admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'update', 'common\\models\\User', '1', '{\"last_login_at\":\"2026-01-20 00:46:33\"}', '{\"id\":1,\"username\":\"admin\",\"email\":\"admin@example.com\",\"password_hash\":\"$2y$13$PA\\/wjsKQMoWuuvqQlB35yu2ELLjanSFOpSu1XLO29\\/BM6N6TOcNKK\",\"auth_key\":\"JoSb1NFibHQ7G6AnT9JH_MUjxxX2GKF9\",\"password_reset_token\":null,\"verification_token\":null,\"email_verified\":1,\"email_verified_at\":null,\"title\":null,\"full_name\":\"ผู้ดูแลระบบ\",\"first_name\":\"System\",\"last_name\":\"Administrator\",\"address\":null,\"phone\":\"02-712-7000\",\"avatar\":\"\\/uploads\\/avatars\\/avatar_1_1768669043.jpg\",\"department_id\":1,\"position\":\"\",\"azure_id\":null,\"google_id\":null,\"thaid_id\":null,\"facebook_id\":null,\"two_factor_secret\":null,\"two_factor_enabled\":0,\"backup_codes\":null,\"failed_login_attempts\":0,\"locked_until\":null,\"password_changed_at\":\"2026-01-17 23:25:05\",\"last_login_at\":\"2026-01-20 01:22:41\",\"last_login_ip\":\"127.0.0.1\",\"status\":10,\"role\":\"admin\",\"created_at\":\"2026-01-17 23:25:05\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', 'http://backend.mrb.test/login', NULL, '2026-01-19 18:22:41'),
(67, 1, 'admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'update', 'common\\models\\User', '1', '{\"avatar\":\"\\/uploads\\/avatars\\/avatar_1_1768669043.jpg\",\"department_id\":1,\"status\":10,\"updated_at\":\"2026-01-20 01:22:41\"}', '{\"id\":1,\"username\":\"admin\",\"email\":\"admin@example.com\",\"password_hash\":\"$2y$13$PA\\/wjsKQMoWuuvqQlB35yu2ELLjanSFOpSu1XLO29\\/BM6N6TOcNKK\",\"auth_key\":\"JoSb1NFibHQ7G6AnT9JH_MUjxxX2GKF9\",\"password_reset_token\":null,\"verification_token\":null,\"email_verified\":1,\"email_verified_at\":null,\"title\":null,\"full_name\":\"ผู้ดูแลระบบ\",\"first_name\":\"System\",\"last_name\":\"Administrator\",\"address\":null,\"phone\":\"02-712-7000\",\"avatar\":\"\\/uploads\\/avatars\\/avatar_1_1768924595.jpg\",\"department_id\":\"1\",\"position\":\"\",\"azure_id\":null,\"google_id\":null,\"thaid_id\":null,\"facebook_id\":null,\"two_factor_secret\":null,\"two_factor_enabled\":0,\"backup_codes\":null,\"failed_login_attempts\":0,\"locked_until\":null,\"password_changed_at\":\"2026-01-17 23:25:05\",\"last_login_at\":\"2026-01-20 01:22:41\",\"last_login_ip\":\"127.0.0.1\",\"status\":\"10\",\"role\":\"admin\",\"created_at\":\"2026-01-17 23:25:05\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', 'http://www.mrb.test/backend/web/user/1/update', NULL, '2026-01-20 15:56:35'),
(68, 1, 'admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'update', 'common\\models\\Building', '2', '{\"id\":2,\"code\":\"BLD-1\",\"name_th\":\"อาคาร 1\",\"name_en\":\"Building 1\",\"description\":null,\"address\":null,\"latitude\":null,\"longitude\":null,\"floor_count\":5,\"sort_order\":0,\"is_active\":1,\"created_at\":\"2026-01-18 02:17:20\",\"updated_at\":\"2026-01-18 02:17:20\"}', '{\"id\":2,\"code\":\"BLD-1\",\"name_th\":\"อาคาร 1\",\"name_en\":\"Building 1\",\"description\":null,\"address\":\"\",\"latitude\":null,\"longitude\":null,\"floor_count\":\"5\",\"sort_order\":0,\"is_active\":\"1\",\"created_at\":\"2026-01-18 02:17:20\",\"updated_at\":\"2026-01-18 02:17:20\"}', 'http://www.mrb.test/backend/web/building/update?id=2', 'Updated building: อาคาร 1', '2026-01-21 01:45:34'),
(69, 1, 'admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'update', 'common\\models\\User', '1', '{\"last_login_at\":\"2026-01-20 01:22:41\"}', '{\"id\":1,\"username\":\"admin\",\"email\":\"admin@example.com\",\"password_hash\":\"$2y$13$PA\\/wjsKQMoWuuvqQlB35yu2ELLjanSFOpSu1XLO29\\/BM6N6TOcNKK\",\"auth_key\":\"JoSb1NFibHQ7G6AnT9JH_MUjxxX2GKF9\",\"password_reset_token\":null,\"verification_token\":null,\"email_verified\":1,\"email_verified_at\":null,\"title\":null,\"full_name\":\"ผู้ดูแลระบบ\",\"first_name\":\"System\",\"last_name\":\"Administrator\",\"address\":null,\"phone\":\"02-712-7000\",\"avatar\":\"\\/uploads\\/avatars\\/avatar_1_1768924595.jpg\",\"department_id\":1,\"position\":\"\",\"azure_id\":null,\"google_id\":null,\"thaid_id\":null,\"facebook_id\":null,\"two_factor_secret\":null,\"two_factor_enabled\":0,\"backup_codes\":null,\"failed_login_attempts\":0,\"locked_until\":null,\"password_changed_at\":\"2026-01-17 23:25:05\",\"last_login_at\":\"2026-01-21 11:43:38\",\"last_login_ip\":\"127.0.0.1\",\"status\":10,\"role\":\"admin\",\"created_at\":\"2026-01-17 23:25:05\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', 'http://frontend.mrb.test/login', NULL, '2026-01-21 04:43:38'),
(70, 1, 'admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'update', 'common\\models\\User', '1', '{\"email\":\"admin@example.com\",\"department_id\":1,\"updated_at\":\"2026-01-21 11:43:38\"}', '{\"id\":1,\"username\":\"admin\",\"email\":\"admin@pi.ac.th\",\"password_hash\":\"$2y$13$PA\\/wjsKQMoWuuvqQlB35yu2ELLjanSFOpSu1XLO29\\/BM6N6TOcNKK\",\"auth_key\":\"JoSb1NFibHQ7G6AnT9JH_MUjxxX2GKF9\",\"password_reset_token\":null,\"verification_token\":null,\"email_verified\":1,\"email_verified_at\":null,\"title\":null,\"full_name\":\"ผู้ดูแลระบบ\",\"first_name\":\"System\",\"last_name\":\"Administrator\",\"address\":null,\"phone\":\"02-712-7000\",\"avatar\":\"\\/uploads\\/avatars\\/avatar_1_1768924595.jpg\",\"department_id\":\"1\",\"position\":\"\",\"azure_id\":null,\"google_id\":null,\"thaid_id\":null,\"facebook_id\":null,\"two_factor_secret\":null,\"two_factor_enabled\":0,\"backup_codes\":null,\"failed_login_attempts\":0,\"locked_until\":null,\"password_changed_at\":\"2026-01-17 23:25:05\",\"last_login_at\":\"2026-01-21 11:43:38\",\"last_login_ip\":\"127.0.0.1\",\"status\":10,\"role\":\"admin\",\"created_at\":\"2026-01-17 23:25:05\",\"updated_at\":{\"expression\":\"NOW()\",\"params\":[]},\"deleted_at\":null}', 'http://frontend.mrb.test/profile/edit', NULL, '2026-01-21 05:06:26');

-- --------------------------------------------------------

--
-- Table structure for table `auth_assignment`
--

CREATE TABLE `auth_assignment` (
  `item_name` varchar(64) NOT NULL,
  `user_id` varchar(64) NOT NULL,
  `created_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_item_child`
--

CREATE TABLE `auth_item_child` (
  `parent` varchar(64) NOT NULL,
  `child` varchar(64) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `auth_rule`
--

CREATE TABLE `auth_rule` (
  `name` varchar(64) NOT NULL,
  `data` blob DEFAULT NULL,
  `created_at` int(11) DEFAULT NULL,
  `updated_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking`
--

CREATE TABLE `booking` (
  `id` int(10) UNSIGNED NOT NULL,
  `booking_code` varchar(20) NOT NULL,
  `booking_title` varchar(255) DEFAULT NULL,
  `room_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `department_id` int(11) UNSIGNED DEFAULT NULL,
  `booking_date` date DEFAULT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `duration_minutes` smallint(6) UNSIGNED DEFAULT NULL,
  `meeting_title` varchar(255) DEFAULT NULL,
  `meeting_description` text DEFAULT NULL,
  `meeting_type` varchar(50) DEFAULT NULL COMMENT 'internal, external, training, interview, etc.',
  `attendees_count` int(11) UNSIGNED DEFAULT 1,
  `external_attendees` text DEFAULT NULL COMMENT 'JSON array of external attendee details',
  `contact_person` varchar(100) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `contact_email` varchar(255) DEFAULT NULL,
  `is_recurring` tinyint(1) DEFAULT 0,
  `recurrence_pattern` varchar(20) DEFAULT NULL COMMENT 'daily, weekly, monthly',
  `recurrence_end_date` date DEFAULT NULL,
  `parent_booking_id` int(11) UNSIGNED DEFAULT NULL COMMENT 'For recurring booking instances',
  `status` varchar(20) NOT NULL DEFAULT 'pending' COMMENT 'pending, approved, rejected, cancelled, completed',
  `approved_by` int(11) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `rejection_reason` text DEFAULT NULL,
  `cancelled_by` int(11) UNSIGNED DEFAULT NULL,
  `cancelled_at` timestamp NULL DEFAULT NULL,
  `cancel_reason` text DEFAULT NULL,
  `total_room_cost` decimal(10,2) DEFAULT 0.00,
  `total_equipment_cost` decimal(10,2) DEFAULT 0.00,
  `total_cost` decimal(10,2) DEFAULT 0.00,
  `special_requests` text DEFAULT NULL,
  `internal_notes` text DEFAULT NULL,
  `check_in_at` timestamp NULL DEFAULT NULL,
  `check_out_at` timestamp NULL DEFAULT NULL,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `updated_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `booking`
--

INSERT INTO `booking` (`id`, `booking_code`, `booking_title`, `room_id`, `user_id`, `department_id`, `booking_date`, `start_time`, `end_time`, `duration_minutes`, `meeting_title`, `meeting_description`, `meeting_type`, `attendees_count`, `external_attendees`, `contact_person`, `contact_phone`, `contact_email`, `is_recurring`, `recurrence_pattern`, `recurrence_end_date`, `parent_booking_id`, `status`, `approved_by`, `approved_at`, `rejection_reason`, `cancelled_by`, `cancelled_at`, `cancel_reason`, `total_room_cost`, `total_equipment_cost`, `total_cost`, `special_requests`, `internal_notes`, `check_in_at`, `check_out_at`, `created_by`, `updated_by`, `created_at`, `updated_at`) VALUES
(1, 'BK26010001', NULL, 7, 4, NULL, NULL, '13:00:00', '17:00:00', 240, 'นำเสนอโครงการ', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 20, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', 3, '2026-01-06 21:36:39', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:36:39', '2026-01-17 20:36:39'),
(2, 'BK26010002', NULL, 2, 6, NULL, NULL, '15:00:00', '16:00:00', 60, 'ประชุมผู้บริหาร', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 79, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', NULL, '2026-01-17 20:36:39', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:36:39', '2026-01-17 20:36:39'),
(3, 'BK26010003', NULL, 8, 4, NULL, NULL, '13:00:00', '17:00:00', 240, 'ประชุมทีมงาน', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 8, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', NULL, '2026-01-17 20:36:39', NULL, NULL, NULL, NULL, 2000.00, 0.00, 2000.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:36:39', '2026-01-17 20:36:39'),
(4, 'BK26010004', NULL, 3, 6, NULL, NULL, '16:00:00', '19:00:00', 180, 'ประชุมงบประมาณ', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 26, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', 3, '2025-12-27 21:36:39', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:36:39', '2026-01-17 20:36:39'),
(5, 'BK26010005', NULL, 1, 6, NULL, NULL, '12:00:00', '14:00:00', 120, 'ประชุมงบประมาณ', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 72, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', NULL, '2026-01-17 20:36:39', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:36:39', '2026-01-17 20:36:39'),
(6, 'BK26010006', NULL, 2, 6, NULL, NULL, '09:00:00', '12:00:00', 180, 'ประชุมวิชาการ', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 38, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', 3, '2026-01-15 21:36:39', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:36:39', '2026-01-17 20:36:39'),
(7, 'BK26010007', NULL, 8, 6, NULL, NULL, '09:00:00', '11:00:00', 120, 'ประชุมงบประมาณ', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 20, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', NULL, '2026-01-17 20:38:13', NULL, NULL, NULL, NULL, 1000.00, 0.00, 1000.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:38:13', '2026-01-17 20:38:13'),
(8, 'BK26010008', NULL, 7, 6, NULL, NULL, '12:00:00', '13:00:00', 60, 'ประชุมผู้บริหาร', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 13, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', NULL, '2026-01-17 20:38:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:38:13', '2026-01-17 20:38:13'),
(9, 'BK26010009', NULL, 5, 5, NULL, NULL, '14:00:00', '15:00:00', 60, 'ประชุมคณะกรรมการ', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 10, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', 3, '2025-12-24 21:38:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:38:13', '2026-01-17 20:38:13'),
(10, 'BK26010010', NULL, 6, 4, NULL, NULL, '15:00:00', '16:00:00', 60, 'ประชุมบุคลากร', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 10, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', NULL, '2026-01-17 20:38:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:38:13', '2026-01-17 20:38:13'),
(11, 'BK26010011', NULL, 6, 5, NULL, NULL, '12:00:00', '15:00:00', 180, 'ประชุมผู้บริหาร', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 10, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'completed', 3, '2025-12-20 21:38:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:38:13', '2026-01-17 20:38:13'),
(12, 'BK26010012', NULL, 4, 6, NULL, NULL, '15:00:00', '19:00:00', 240, 'ประชุมทีมงาน', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 14, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', 3, '2025-12-30 21:38:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:38:13', '2026-01-17 20:38:13'),
(13, 'BK26010013', NULL, 2, 6, NULL, NULL, '12:00:00', '13:00:00', 60, 'นำเสนอโครงการ', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 67, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', 3, '2025-12-29 21:38:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:38:13', '2026-01-17 20:38:13'),
(14, 'BK26010014', NULL, 5, 5, NULL, NULL, '16:00:00', '18:00:00', 120, 'อบรมเชิงปฏิบัติการ', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 8, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', NULL, '2026-01-17 20:38:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:38:13', '2026-01-17 20:38:13'),
(15, 'BK26010015', NULL, 1, 5, NULL, NULL, '08:00:00', '10:00:00', 120, 'สัมมนาออนไลน์', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 98, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', NULL, '2026-01-17 20:38:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:38:13', '2026-01-17 20:38:13'),
(16, 'BK26010016', NULL, 7, 4, NULL, NULL, '10:00:00', '12:00:00', 120, 'ประชุมบุคลากร', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 12, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'completed', 3, '2026-01-06 21:38:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:38:13', '2026-01-17 20:38:13'),
(17, 'BK26010017', NULL, 1, 6, NULL, NULL, '10:00:00', '11:00:00', 60, 'ประชุมคณะกรรมการ', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 98, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', NULL, '2026-01-17 20:38:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:38:13', '2026-01-17 20:38:13'),
(18, 'BK26010018', NULL, 4, 6, NULL, NULL, '09:00:00', '12:00:00', 180, 'ประชุมทีมงาน', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 5, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'completed', 3, '2025-12-29 21:38:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:38:13', '2026-01-17 20:38:13'),
(19, 'BK26010019', NULL, 5, 6, NULL, NULL, '11:00:00', '14:00:00', 180, 'อบรมเชิงปฏิบัติการ', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 14, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', NULL, '2026-01-17 20:38:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:38:13', '2026-01-17 20:38:13'),
(20, 'BK26010020', NULL, 2, 4, NULL, NULL, '13:00:00', '15:00:00', 120, 'นำเสนอโครงการ', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 71, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', 3, '2025-12-20 21:38:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:38:13', '2026-01-17 20:38:13'),
(21, 'BK26010021', NULL, 1, 5, NULL, NULL, '11:00:00', '12:00:00', 60, 'อบรมเชิงปฏิบัติการ', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 65, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', NULL, '2026-01-17 20:38:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:38:13', '2026-01-17 20:38:13'),
(22, 'BK26010022', NULL, 6, 6, NULL, NULL, '09:00:00', '10:00:00', 60, 'ประชุมทีมงาน', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 9, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', NULL, '2026-01-17 20:38:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:38:13', '2026-01-17 20:38:13'),
(23, 'BK26010023', NULL, 6, 4, NULL, NULL, '16:00:00', '20:00:00', 240, 'ประชุมทีมงาน', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 7, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', NULL, '2026-01-17 20:38:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:38:13', '2026-01-17 20:38:13'),
(24, 'BK26010024', NULL, 5, 5, NULL, NULL, '15:00:00', '16:00:00', 60, 'ประชุมทีมงาน', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 7, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', NULL, '2026-01-17 20:38:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-17 20:38:13', '2026-01-17 20:38:13'),
(25, 'BK26010025', NULL, 5, 1, 1, '2026-01-19', '09:00:00', '16:30:00', 450, 'ประชุมประจำเดือนกองดิจิทัล', 'ประชุมประจำเดือนกองดิจิทัล ครั้งที่ 1/2569', 'internal', 15, NULL, 'สมชาย', NULL, NULL, 0, NULL, NULL, NULL, 'approved', NULL, '2026-01-18 02:07:33', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, '', NULL, NULL, NULL, 1, 1, '2026-01-18 02:07:33', '2026-01-18 02:07:33'),
(26, 'BK26010026', NULL, 3, 5, NULL, NULL, '10:00:00', '11:00:00', 60, 'ประชุมติดตามงาน', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 34, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', NULL, '2026-01-19 17:26:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-19 17:26:13', '2026-01-19 17:26:13'),
(27, 'BK26010027', NULL, 1, 5, NULL, NULL, '16:00:00', '18:00:00', 120, 'ประชุมวิชาการ', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 34, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', NULL, '2026-01-19 17:26:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-19 17:26:13', '2026-01-19 17:26:13'),
(28, 'BK26010028', NULL, 3, 6, NULL, NULL, '13:00:00', '16:00:00', 180, 'อบรมเชิงปฏิบัติการ', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 6, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', NULL, '2026-01-08 18:26:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-19 17:26:13', '2026-01-19 17:26:13'),
(29, 'BK26010029', NULL, 7, 5, NULL, NULL, '09:00:00', '10:00:00', 60, 'ประชุมคณะกรรมการ', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 7, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', NULL, '2026-01-14 18:26:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-19 17:26:13', '2026-01-19 17:26:13'),
(30, 'BK26010030', NULL, 4, 5, NULL, NULL, '08:00:00', '09:00:00', 60, 'ประชุมวิชาการ', 'การจองตัวอย่างสำหรับทดสอบระบบ', NULL, 26, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 'approved', NULL, '2026-01-16 18:26:13', NULL, NULL, NULL, NULL, 0.00, 0.00, 0.00, NULL, NULL, NULL, NULL, NULL, NULL, '2026-01-19 17:26:13', '2026-01-19 17:26:13');

-- --------------------------------------------------------

--
-- Table structure for table `booking_attendee`
--

CREATE TABLE `booking_attendee` (
  `id` int(10) UNSIGNED NOT NULL,
  `booking_id` int(11) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `attendee_name` varchar(100) DEFAULT NULL,
  `attendee_email` varchar(255) DEFAULT NULL,
  `attendee_phone` varchar(20) DEFAULT NULL,
  `is_organizer` tinyint(1) DEFAULT 0,
  `attendance_status` varchar(20) DEFAULT 'pending' COMMENT 'pending, accepted, declined, tentative',
  `response_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `booking_equipment`
--

CREATE TABLE `booking_equipment` (
  `id` int(10) UNSIGNED NOT NULL,
  `booking_id` int(11) UNSIGNED NOT NULL,
  `equipment_id` int(11) UNSIGNED NOT NULL,
  `quantity_requested` int(11) UNSIGNED NOT NULL DEFAULT 1,
  `quantity_provided` int(11) UNSIGNED DEFAULT NULL,
  `unit_price` decimal(10,2) DEFAULT 0.00,
  `total_price` decimal(10,2) DEFAULT 0.00,
  `status` varchar(20) DEFAULT 'pending' COMMENT 'pending, confirmed, delivered, returned',
  `delivered_at` timestamp NULL DEFAULT NULL,
  `returned_at` timestamp NULL DEFAULT NULL,
  `condition_on_return` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `building`
--

CREATE TABLE `building` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `name_th` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `description` date DEFAULT NULL,
  `address` text DEFAULT NULL,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `floor_count` smallint(6) DEFAULT 1,
  `sort_order` int(10) UNSIGNED NOT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `building`
--

INSERT INTO `building` (`id`, `code`, `name_th`, `name_en`, `description`, `address`, `latitude`, `longitude`, `floor_count`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'MAIN', 'อาคารสำนักงานหลัก', 'Main Office Building', NULL, NULL, NULL, NULL, 5, 0, 1, '2026-01-17 16:25:04', '2026-01-17 16:25:04'),
(2, 'BLD-1', 'อาคาร 1', 'Building 1', NULL, '', NULL, NULL, 5, 0, 1, '2026-01-17 19:17:20', '2026-01-21 01:45:34'),
(3, 'BLD-2', 'อาคาร 2', 'Building 2', NULL, NULL, NULL, NULL, 4, 0, 1, '2026-01-17 19:17:20', '2026-01-17 19:17:20'),
(4, 'BLD-3', 'อาคาร 3', 'Building 3', NULL, NULL, NULL, NULL, 3, 0, 1, '2026-01-17 19:17:20', '2026-01-17 19:17:20'),
(5, 'BLD-ADM', 'อาคารบริหาร', 'Administration Building', NULL, NULL, NULL, NULL, 6, 0, 1, '2026-01-17 19:17:20', '2026-01-17 19:17:20');

-- --------------------------------------------------------

--
-- Table structure for table `department`
--

CREATE TABLE `department` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `name_th` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `parent_id` int(11) UNSIGNED DEFAULT NULL,
  `sort_order` int(11) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `department`
--

INSERT INTO `department` (`id`, `code`, `name_th`, `name_en`, `parent_id`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'EXEC', 'ผู้บริหาร', 'Executive', NULL, 1, 1, '2026-01-17 16:25:04', '2026-01-17 16:25:04'),
(2, 'ADMIN', 'ฝ่ายบริหารงานทั่วไป', 'Administration', NULL, 2, 1, '2026-01-17 16:25:04', '2026-01-17 16:25:04'),
(3, 'IT', 'ฝ่ายเทคโนโลยีดิจิทัลและ AI', 'Digital Technology & AI', NULL, 3, 1, '2026-01-17 16:25:04', '2026-01-17 16:25:04'),
(4, 'HR', 'ฝ่ายทรัพยากรบุคคล', 'Human Resources', NULL, 4, 1, '2026-01-17 16:25:04', '2026-01-17 16:25:04'),
(5, 'FIN', 'ฝ่ายการเงิน', 'Finance', NULL, 5, 1, '2026-01-17 16:25:04', '2026-01-17 16:25:04'),
(6, 'ACAD', 'ฝ่ายวิชาการ', 'Academic Affairs', NULL, 6, 1, '2026-01-17 16:25:04', '2026-01-17 16:25:04');

-- --------------------------------------------------------

--
-- Table structure for table `email_template`
--

CREATE TABLE `email_template` (
  `id` int(10) UNSIGNED NOT NULL,
  `template_key` varchar(50) NOT NULL,
  `name` varchar(100) NOT NULL,
  `subject` varchar(255) NOT NULL,
  `body_html` text DEFAULT NULL,
  `body_text` text DEFAULT NULL,
  `variables` text DEFAULT NULL COMMENT 'JSON array of available variables',
  `is_active` tinyint(1) DEFAULT 1,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `email_template`
--

INSERT INTO `email_template` (`id`, `template_key`, `name`, `subject`, `body_html`, `body_text`, `variables`, `is_active`, `updated_at`) VALUES
(1, 'booking_confirmation', 'ยืนยันการจอง', 'ยืนยันการจองห้องประชุม: {{meeting_title}}', '<p>เรียน {{user_name}}</p><p>การจองห้องประชุมของท่านได้รับการยืนยันแล้ว</p><p>รายละเอียด:</p><ul><li>ห้อง: {{room_name}}</li><li>วันที่: {{booking_date}}</li><li>เวลา: {{start_time}} - {{end_time}}</li></ul>', NULL, NULL, 1, '2026-01-17 16:25:04'),
(2, 'booking_reminder', 'แจ้งเตือนการประชุม', 'แจ้งเตือน: การประชุม {{meeting_title}} ใกล้เริ่มแล้ว', '<p>เรียน {{user_name}}</p><p>การประชุมของท่านจะเริ่มในอีก {{minutes}} นาที</p>', NULL, NULL, 1, '2026-01-17 16:25:04'),
(3, 'booking_cancelled', 'ยกเลิกการจอง', 'การจองห้องประชุมถูกยกเลิก: {{meeting_title}}', '<p>เรียน {{user_name}}</p><p>การจองห้องประชุมของท่านถูกยกเลิกแล้ว</p>', NULL, NULL, 1, '2026-01-17 16:25:04'),
(4, 'password_reset', 'รีเซ็ตรหัสผ่าน', 'รีเซ็ตรหัสผ่าน - ระบบจองห้องประชุม', '<p>เรียน {{user_name}}</p><p>คลิกลิงก์ด้านล่างเพื่อรีเซ็ตรหัสผ่านของท่าน:</p><p><a href=\"{{reset_link}}\">รีเซ็ตรหัสผ่าน</a></p>', NULL, NULL, 1, '2026-01-17 16:25:04');

-- --------------------------------------------------------

--
-- Table structure for table `equipment`
--

CREATE TABLE `equipment` (
  `id` int(10) UNSIGNED NOT NULL,
  `equipment_code` varchar(30) NOT NULL,
  `category_id` int(11) UNSIGNED NOT NULL,
  `name_th` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL COMMENT 'ไอคอน',
  `brand` varchar(100) DEFAULT NULL,
  `model` varchar(100) DEFAULT NULL,
  `serial_number` varchar(100) DEFAULT NULL,
  `building_id` int(11) UNSIGNED DEFAULT NULL,
  `storage_location` varchar(255) DEFAULT NULL,
  `total_quantity` int(11) UNSIGNED DEFAULT 1,
  `available_quantity` int(11) UNSIGNED DEFAULT 1,
  `is_portable` tinyint(1) DEFAULT 1 COMMENT 'Can be moved to different rooms',
  `hourly_rate` decimal(10,2) DEFAULT 0.00,
  `daily_rate` decimal(10,2) DEFAULT 0.00,
  `last_maintenance_date` date DEFAULT NULL,
  `next_maintenance_date` date DEFAULT NULL,
  `condition_status` varchar(20) DEFAULT 'good' COMMENT 'excellent, good, fair, poor',
  `description` text DEFAULT NULL,
  `usage_instructions` text DEFAULT NULL,
  `specifications` text DEFAULT NULL COMMENT 'JSON format',
  `image` varchar(255) DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT 1 COMMENT '1=available, 0=unavailable, 2=maintenance',
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `equipment`
--

INSERT INTO `equipment` (`id`, `equipment_code`, `category_id`, `name_th`, `name_en`, `icon`, `brand`, `model`, `serial_number`, `building_id`, `storage_location`, `total_quantity`, `available_quantity`, `is_portable`, `hourly_rate`, `daily_rate`, `last_maintenance_date`, `next_maintenance_date`, `condition_status`, `description`, `usage_instructions`, `specifications`, `image`, `status`, `created_by`, `created_at`, `updated_at`) VALUES
(1, 'PRJEPS001', 1, 'โปรเจคเตอร์', 'LCD Projector', 'bi-projector', 'EPSON', 'EB-X51', '', NULL, NULL, 20, 10, 1, 0.00, 0.00, NULL, NULL, 'good', 'Projector EPSON EB-X51', NULL, '', '/uploads/equipment/eq_1768937250_JPgoUH0h.jpg', 1, NULL, '2026-01-20 17:43:15', '2026-01-20 19:27:30'),
(2, 'LED65001', 1, 'จอ LED 65 นิ้ว', 'LED TV Display 65 inch', 'bi-display', '', '', '', NULL, NULL, 20, 5, 1, 0.00, 0.00, NULL, NULL, 'good', '4K Smart TV / Display จอ LED 65 นิ้ว', NULL, '', '/uploads/equipment/eq_1768936805_joLDvdCI.jpg', 1, NULL, '2026-01-20 17:43:15', '2026-01-21 01:36:01'),
(3, 'BRDWB001', 1, 'ไวท์บอร์ด ขนาด 120x180ซม.', 'Whiteboard 120x180cm', 'bi-easel', '', '', '', NULL, NULL, 15, 15, 1, 0.00, 0.00, NULL, NULL, 'good', 'ไวท์บอร์ด 120x180 ซม.\r\nWhiteboard 120x180cm', NULL, '', '/uploads/equipment/eq_1768937094_KvC3Z1T4.jpg', 1, NULL, '2026-01-20 17:43:15', '2026-01-20 19:45:48'),
(4, 'CAMVID001', 3, 'ระบบประชุมทางไกล Cisco WebEx', 'Cisco WebEx Room Kit', 'bi-camera-video', '', '', '', NULL, NULL, 1, 1, 1, 0.00, 0.00, NULL, NULL, 'good', 'กล้อง Video Conference สำหรับประชุมออนไลน์\r\nCisco WebEx Room Kit', NULL, '', '/uploads/equipment/eq_1768938720_eACFDaZb.jpg', 1, NULL, '2026-01-20 17:44:16', '2026-01-20 19:52:00'),
(5, 'MICSHU001', 4, 'ไมโครโฟนไร้สาย Shure', 'Microphone,Shure Wireless', 'bi-mic', '', '', '', NULL, NULL, 15, 10, 1, 0.00, 0.00, NULL, NULL, 'good', 'ไมโครโฟนไร้สาย Shure\r\nMicrophone,Shure Wireless', NULL, '', '/uploads/equipment/eq_1768937183_kSFoDof4.jpg', 1, NULL, '2026-01-20 17:44:16', '2026-01-20 19:26:23'),
(6, 'SPKGEN001', 4, 'ลำโพงห้องประชุม', 'Conference Speaker', 'bi-speaker', '', '', '', NULL, NULL, 5, 5, 1, 0.00, 0.00, NULL, NULL, 'good', 'ลำโพงห้องประชุม\r\nConference Speaker', NULL, '', '/uploads/equipment/eq_1768939100_Jy-AA7MM.jpg', 1, NULL, '2026-01-20 17:44:16', '2026-01-20 19:58:20'),
(7, 'LTP01', 6, 'โน้ตบุ๊ค', 'Laptop', 'bi-laptop', '', '', '', NULL, NULL, 35, 35, 1, 0.00, 0.00, NULL, NULL, 'good', 'Notebook สำหรับนำเสนอ', NULL, '', '/uploads/equipment/eq_1768938009_08abnbdY.avif', 1, NULL, '2026-01-20 17:44:16', '2026-01-20 19:40:09'),
(8, 'DCM01', 6, 'เครื่องฉายแผ่นใส', 'Document Camera', 'bi-file-slides', '', '', '', NULL, NULL, 20, 10, 1, 0.00, 0.00, NULL, NULL, 'good', 'Projector EPSON EB-X51', NULL, '', '/uploads/equipment/eq_1768938279_FZikfr-W.jpg', 1, NULL, '2026-01-20 17:44:16', '2026-01-20 19:44:39'),
(9, 'SNDSYS001', 4, 'ระบบเสียง', 'Sound System', 'bi-volume-up', '', '', '', NULL, NULL, 5, 2, 1, 0.00, 0.00, NULL, NULL, 'good', 'ชุดเครื่องเสียงพร้อมลำโพง\r\nเครื่องเสียง (Sound System)', 'ชุดเครื่องเสียงพร้อมลำโพง\r\nเครื่องเสียง (Sound System)', '', '/uploads/equipment/eq_1768938774_IBKzvivr.avif', 1, NULL, '2026-01-20 17:44:16', '2026-01-20 20:05:43'),
(10, 'CAMDIG001', 6, 'กล้องถ่ายรูป (Digital)', 'Digital Camera', 'bi-camera', '', '', '', NULL, NULL, 15, 10, 1, 0.00, 0.00, NULL, NULL, 'good', 'กล้องถ่ายรูป (Digital)', NULL, '', '/uploads/equipment/eq_1768939203_8N4bQT_j.jpg', 1, NULL, '2026-01-20 17:44:16', '2026-01-21 01:42:07'),
(11, 'CUTCUP001', 6, 'ชุดน้ำชา/กาแฟ', 'Tea/Coffee Cup,Catering Set', 'bi-cup-hot', '', '', '', NULL, NULL, 20, 10, 1, 0.00, 0.00, NULL, NULL, 'good', 'ชุดน้ำชา/กาแฟ\r\nTea/Coffee Cup,Catering Set', NULL, '', '/uploads/equipment/eq_1768939419_3JkbQPEZ.jpg', 1, NULL, '2026-01-20 17:44:16', '2026-01-20 20:03:39'),
(12, 'NETWIF001', 6, 'WiFi', 'WiFi', 'bi-wifi', '', '', '', NULL, NULL, 30, 10, 1, 0.00, 0.00, NULL, NULL, 'good', 'Pocket WiFI', NULL, '', '/uploads/equipment/eq_1768938254_dID7AwTd.jpg', 1, NULL, '2026-01-20 17:44:16', '2026-01-20 20:04:12'),
(13, 'BRDFC001', 6, 'Flipchart', 'Flipchart', 'bi-flipchart', '', '', '', NULL, NULL, 5, 5, 1, 0.00, 0.00, NULL, NULL, 'good', 'กระดาษ Flipchart พร้อมขาตั้ง', NULL, '', '/uploads/equipment/eq_1768933529_T8Vvj16-.jpg', 1, NULL, '2026-01-20 17:44:16', '2026-01-20 19:55:56'),
(14, 'POWEXT001', 6, 'ปลั๊กไฟพ่วง', 'Electric Outlet', 'bi-wifi', '', '', '', NULL, NULL, 40, 40, 1, 0.00, 0.00, NULL, NULL, 'good', 'ปลั๊กพ่วง 6 ช่อง', NULL, '', '/uploads/equipment/eq_1768939635_56JuhKau.jpg', 1, NULL, '2026-01-20 17:44:16', '2026-01-20 20:07:15');

-- --------------------------------------------------------

--
-- Table structure for table `equipment_category`
--

CREATE TABLE `equipment_category` (
  `id` int(10) UNSIGNED NOT NULL,
  `code` varchar(20) NOT NULL,
  `name_th` varchar(100) NOT NULL,
  `name_en` varchar(100) DEFAULT NULL,
  `icon` varchar(50) DEFAULT NULL COMMENT 'FontAwesome icon class',
  `description` text DEFAULT NULL,
  `sort_order` smallint(6) DEFAULT 0,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `equipment_category`
--

INSERT INTO `equipment_category` (`id`, `code`, `name_th`, `name_en`, `icon`, `description`, `sort_order`, `is_active`, `created_at`) VALUES
(1, 'PROJECTOR', 'เครื่องฉาย', 'Projector', 'fa-video', NULL, 1, 1, '2026-01-17 16:25:04'),
(2, 'DISPLAY', 'จอแสดงผล', 'Display', 'fa-tv', NULL, 2, 1, '2026-01-17 16:25:04'),
(3, 'COMPUTER', 'คอมพิวเตอร์', 'Computer', 'fa-laptop', NULL, 3, 1, '2026-01-17 16:25:04'),
(4, 'AUDIO', 'ระบบเสียง', 'Audio System', 'fa-volume-up', NULL, 4, 1, '2026-01-17 16:25:04'),
(5, 'VIDEO_CONF', 'ระบบประชุมทางไกล', 'Video Conference', 'fa-video-camera', NULL, 5, 1, '2026-01-17 16:25:04'),
(6, 'OTHER', 'อุปกรณ์อื่นๆ', 'Other Equipment', 'fa-cogs', NULL, 99, 1, '2026-01-17 16:25:04');

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
(62, '2027-12-31', 2027, 'วันสิ้นปี', 'New Year\'s Eve', '', 'national', 0, 0, '2026-01-20 11:52:02');

-- --------------------------------------------------------

--
-- Table structure for table `login_history`
--

CREATE TABLE `login_history` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED DEFAULT NULL,
  `username` varchar(50) DEFAULT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `login_method` varchar(20) NOT NULL COMMENT 'password, azure, google, thaid, facebook',
  `login_status` varchar(20) NOT NULL COMMENT 'success, failed, locked, captcha_required',
  `failure_reason` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `login_history`
--

INSERT INTO `login_history` (`id`, `user_id`, `username`, `ip_address`, `user_agent`, `login_method`, `login_status`, `failure_reason`, `created_at`) VALUES
(1, 1, 'admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'password', 'success', NULL, '2026-01-17 16:46:30'),
(2, 1, 'admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'password', 'failed', 'Invalid credentials', '2026-01-17 17:08:02'),
(3, 1, 'admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'password', 'success', NULL, '2026-01-17 17:08:28'),
(4, 1, 'admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'password', 'success', NULL, '2026-01-19 18:22:41'),
(5, 1, 'admin', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/143.0.0.0 Safari/537.36', 'password', 'success', NULL, '2026-01-21 04:43:38');

-- --------------------------------------------------------

--
-- Table structure for table `meeting_room`
--

CREATE TABLE `meeting_room` (
  `id` int(10) UNSIGNED NOT NULL,
  `room_code` varchar(20) NOT NULL,
  `name_th` varchar(255) NOT NULL,
  `name_en` varchar(255) DEFAULT NULL,
  `building_id` int(11) UNSIGNED NOT NULL,
  `floor` smallint(6) NOT NULL DEFAULT 1,
  `room_number` varchar(20) DEFAULT NULL,
  `capacity` int(11) NOT NULL COMMENT 'Maximum number of attendees',
  `room_type` varchar(50) NOT NULL COMMENT 'conference, training, boardroom, huddle, auditorium',
  `room_layout` varchar(50) DEFAULT NULL COMMENT 'theater, classroom, u_shape, boardroom, banquet',
  `has_projector` tinyint(1) DEFAULT 0,
  `has_video_conference` tinyint(1) DEFAULT 0,
  `has_whiteboard` tinyint(1) DEFAULT 0,
  `has_air_conditioning` tinyint(1) DEFAULT 1,
  `has_wifi` tinyint(1) DEFAULT 1,
  `has_audio_system` tinyint(1) DEFAULT 0,
  `has_recording` tinyint(1) DEFAULT 0,
  `min_booking_duration` smallint(6) DEFAULT 30 COMMENT 'Minutes',
  `max_booking_duration` smallint(6) DEFAULT 480 COMMENT 'Minutes',
  `advance_booking_days` smallint(6) DEFAULT 30 COMMENT 'How many days in advance',
  `requires_approval` tinyint(1) DEFAULT 0,
  `allowed_departments` text DEFAULT NULL COMMENT 'JSON array of department IDs, null = all',
  `hourly_rate` decimal(10,2) DEFAULT 0.00,
  `half_day_rate` decimal(10,2) DEFAULT 0.00,
  `full_day_rate` decimal(10,2) DEFAULT 0.00,
  `operating_start_time` time DEFAULT '08:00:00',
  `operating_end_time` time DEFAULT '18:00:00',
  `available_days` varchar(20) DEFAULT '1,2,3,4,5' COMMENT '0=Sun, 1=Mon, etc.',
  `description` text DEFAULT NULL,
  `usage_rules` text DEFAULT NULL,
  `contact_person` varchar(100) DEFAULT NULL,
  `contact_phone` varchar(20) DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT 1 COMMENT '1=active, 0=inactive, 2=maintenance',
  `is_featured` tinyint(1) DEFAULT 0 COMMENT 'Show on homepage',
  `sort_order` int(11) DEFAULT 0,
  `created_by` int(11) UNSIGNED DEFAULT NULL,
  `updated_by` int(11) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `meeting_room`
--

INSERT INTO `meeting_room` (`id`, `room_code`, `name_th`, `name_en`, `building_id`, `floor`, `room_number`, `capacity`, `room_type`, `room_layout`, `has_projector`, `has_video_conference`, `has_whiteboard`, `has_air_conditioning`, `has_wifi`, `has_audio_system`, `has_recording`, `min_booking_duration`, `max_booking_duration`, `advance_booking_days`, `requires_approval`, `allowed_departments`, `hourly_rate`, `half_day_rate`, `full_day_rate`, `operating_start_time`, `operating_end_time`, `available_days`, `description`, `usage_rules`, `contact_person`, `contact_phone`, `status`, `is_featured`, `sort_order`, `created_by`, `updated_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'CONF-L1', 'ห้องประชุมใหญ่ 1', 'Large Meeting Room 1', 2, 2, '201', 100, 'conference', NULL, 1, 1, 1, 1, 1, 1, 0, 30, 480, 30, 0, NULL, 0.00, 0.00, 0.00, '08:00:00', '18:00:00', '[1,2,3,4,5]', 'ห้องประชุมขนาดใหญ่ รองรับ 100 คน มีระบบประชุมทางไกล', NULL, NULL, NULL, 1, 0, 0, NULL, NULL, '2026-01-17 19:17:20', '2026-01-17 19:42:04', NULL),
(2, 'CONF-L2', 'ห้องประชุมใหญ่ 2', 'Large Meeting Room 2', 2, 3, '301', 80, 'conference', NULL, 1, 0, 1, 1, 1, 1, 0, 30, 480, 30, 0, NULL, 0.00, 0.00, 0.00, '08:00:00', '18:00:00', '[1,2,3,4,5]', 'ห้องประชุมขนาดใหญ่ รองรับ 80 คน', NULL, NULL, NULL, 1, 0, 0, NULL, NULL, '2026-01-17 19:17:20', '2026-01-17 19:42:04', NULL),
(3, 'CONF-MA', 'ห้องประชุมกลาง A', 'Medium Meeting Room A', 3, 1, '101', 40, 'conference', NULL, 1, 0, 1, 1, 1, 0, 0, 30, 480, 30, 0, NULL, 0.00, 0.00, 0.00, '08:00:00', '18:00:00', '[1,2,3,4,5]', 'ห้องประชุมขนาดกลาง รองรับ 40 คน', NULL, NULL, NULL, 1, 0, 0, NULL, NULL, '2026-01-17 19:17:20', '2026-01-17 19:42:04', NULL),
(4, 'CONF-MB', 'ห้องประชุมกลาง B', 'Medium Meeting Room B', 3, 2, '201', 30, 'conference', NULL, 0, 0, 1, 1, 1, 0, 0, 30, 480, 30, 0, NULL, 0.00, 0.00, 0.00, '08:00:00', '18:00:00', '[1,2,3,4,5]', 'ห้องประชุมขนาดกลาง รองรับ 30 คน พร้อมจอ LED', NULL, NULL, NULL, 1, 0, 0, NULL, NULL, '2026-01-17 19:17:20', '2026-01-17 19:42:04', NULL),
(5, 'CONF-S1', 'ห้องประชุมเล็ก 1', 'Small Meeting Room 1', 2, 4, '401', 15, 'huddle', NULL, 0, 0, 1, 1, 1, 0, 0, 30, 480, 30, 0, NULL, 0.00, 0.00, 0.00, '08:00:00', '18:00:00', '[1,2,3,4,5]', 'ห้องประชุมขนาดเล็ก รองรับ 15 คน เหมาะสำหรับการประชุมทีมงาน', NULL, NULL, NULL, 1, 0, 0, NULL, NULL, '2026-01-17 19:17:20', '2026-01-17 19:42:04', NULL),
(6, 'CONF-S2', 'ห้องประชุมเล็ก 2', 'Small Meeting Room 2', 2, 4, '402', 12, 'huddle', NULL, 1, 0, 0, 1, 1, 0, 0, 30, 480, 30, 0, NULL, 0.00, 0.00, 0.00, '08:00:00', '18:00:00', '[1,2,3,4,5]', 'ห้องประชุมขนาดเล็ก รองรับ 12 คน', NULL, NULL, NULL, 1, 0, 0, NULL, NULL, '2026-01-17 19:17:20', '2026-01-17 19:42:04', NULL),
(7, 'CONF-VIP', 'ห้องประชุม VIP', 'VIP Meeting Room', 5, 5, '501', 20, 'boardroom', NULL, 1, 1, 1, 1, 1, 1, 1, 30, 480, 30, 0, NULL, 0.00, 0.00, 0.00, '08:00:00', '18:00:00', '[1,2,3,4,5]', 'ห้องประชุม VIP สำหรับผู้บริหาร มีระบบประชุมทางไกลคุณภาพสูง', NULL, NULL, NULL, 1, 0, 0, NULL, NULL, '2026-01-17 19:17:20', '2026-01-17 19:42:04', NULL),
(8, 'TRAIN-1', 'ห้องฝึกอบรม 1', 'Training Room 1', 4, 1, '101', 50, 'training', NULL, 1, 0, 1, 1, 1, 1, 0, 30, 480, 30, 0, NULL, 500.00, 0.00, 0.00, '08:00:00', '18:00:00', '[1,2,3,4,5]', 'ห้องฝึกอบรม รองรับ 50 คน พร้อมคอมพิวเตอร์', NULL, NULL, NULL, 1, 0, 0, NULL, NULL, '2026-01-17 19:17:20', '2026-01-17 19:42:04', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `migration`
--

CREATE TABLE `migration` (
  `version` varchar(180) NOT NULL,
  `apply_time` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migration`
--

INSERT INTO `migration` (`version`, `apply_time`) VALUES
('m000000_000000_base', 1768667101),
('m130524_201442_init', 1768667103),
('m190124_110200_add_verification_token_column_to_user_table', 1768667103),
('m240101_000001_create_meeting_room_booking_tables', 1768667105);

-- --------------------------------------------------------

--
-- Table structure for table `notification`
--

CREATE TABLE `notification` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `type` varchar(50) NOT NULL,
  `title` varchar(255) NOT NULL,
  `message` text DEFAULT NULL,
  `data` text DEFAULT NULL COMMENT 'JSON additional data',
  `link` varchar(500) DEFAULT NULL,
  `is_read` tinyint(1) DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `sent_email` tinyint(1) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notification`
--

INSERT INTO `notification` (`id`, `user_id`, `type`, `title`, `message`, `data`, `link`, `is_read`, `read_at`, `sent_email`, `created_at`) VALUES
(1, 4, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010001 - นำเสนอโครงการ ได้รับการบันทึกแล้ว', '{\"booking_id\":1}', '/booking/view?id=1', 0, NULL, 0, '2026-01-17 20:36:39'),
(2, 6, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010002 - ประชุมผู้บริหาร ได้รับการบันทึกแล้ว', '{\"booking_id\":2}', '/booking/view?id=2', 0, NULL, 0, '2026-01-17 20:36:39'),
(3, 4, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010003 - ประชุมทีมงาน ได้รับการบันทึกแล้ว', '{\"booking_id\":3}', '/booking/view?id=3', 0, NULL, 0, '2026-01-17 20:36:39'),
(4, 6, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010004 - ประชุมงบประมาณ ได้รับการบันทึกแล้ว', '{\"booking_id\":4}', '/booking/view?id=4', 0, NULL, 0, '2026-01-17 20:36:39'),
(5, 6, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010005 - ประชุมงบประมาณ ได้รับการบันทึกแล้ว', '{\"booking_id\":5}', '/booking/view?id=5', 0, NULL, 0, '2026-01-17 20:36:39'),
(6, 6, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010006 - ประชุมวิชาการ ได้รับการบันทึกแล้ว', '{\"booking_id\":6}', '/booking/view?id=6', 0, NULL, 0, '2026-01-17 20:36:39'),
(7, 6, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010007 - ประชุมงบประมาณ ได้รับการบันทึกแล้ว', '{\"booking_id\":7}', '/booking/view?id=7', 0, NULL, 0, '2026-01-17 20:38:13'),
(8, 6, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010008 - ประชุมผู้บริหาร ได้รับการบันทึกแล้ว', '{\"booking_id\":8}', '/booking/view?id=8', 0, NULL, 0, '2026-01-17 20:38:13'),
(9, 5, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010009 - ประชุมคณะกรรมการ ได้รับการบันทึกแล้ว', '{\"booking_id\":9}', '/booking/view?id=9', 0, NULL, 0, '2026-01-17 20:38:13'),
(10, 4, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010010 - ประชุมบุคลากร ได้รับการบันทึกแล้ว', '{\"booking_id\":10}', '/booking/view?id=10', 0, NULL, 0, '2026-01-17 20:38:13'),
(11, 5, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010011 - ประชุมผู้บริหาร ได้รับการบันทึกแล้ว', '{\"booking_id\":11}', '/booking/view?id=11', 0, NULL, 0, '2026-01-17 20:38:13'),
(12, 6, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010012 - ประชุมทีมงาน ได้รับการบันทึกแล้ว', '{\"booking_id\":12}', '/booking/view?id=12', 0, NULL, 0, '2026-01-17 20:38:13'),
(13, 6, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010013 - นำเสนอโครงการ ได้รับการบันทึกแล้ว', '{\"booking_id\":13}', '/booking/view?id=13', 0, NULL, 0, '2026-01-17 20:38:13'),
(14, 5, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010014 - อบรมเชิงปฏิบัติการ ได้รับการบันทึกแล้ว', '{\"booking_id\":14}', '/booking/view?id=14', 0, NULL, 0, '2026-01-17 20:38:13'),
(15, 5, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010015 - สัมมนาออนไลน์ ได้รับการบันทึกแล้ว', '{\"booking_id\":15}', '/booking/view?id=15', 0, NULL, 0, '2026-01-17 20:38:13'),
(16, 4, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010016 - ประชุมบุคลากร ได้รับการบันทึกแล้ว', '{\"booking_id\":16}', '/booking/view?id=16', 0, NULL, 0, '2026-01-17 20:38:13'),
(17, 6, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010017 - ประชุมคณะกรรมการ ได้รับการบันทึกแล้ว', '{\"booking_id\":17}', '/booking/view?id=17', 0, NULL, 0, '2026-01-17 20:38:13'),
(18, 6, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010018 - ประชุมทีมงาน ได้รับการบันทึกแล้ว', '{\"booking_id\":18}', '/booking/view?id=18', 0, NULL, 0, '2026-01-17 20:38:13'),
(19, 6, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010019 - อบรมเชิงปฏิบัติการ ได้รับการบันทึกแล้ว', '{\"booking_id\":19}', '/booking/view?id=19', 0, NULL, 0, '2026-01-17 20:38:13'),
(20, 4, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010020 - นำเสนอโครงการ ได้รับการบันทึกแล้ว', '{\"booking_id\":20}', '/booking/view?id=20', 0, NULL, 0, '2026-01-17 20:38:13'),
(21, 5, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010021 - อบรมเชิงปฏิบัติการ ได้รับการบันทึกแล้ว', '{\"booking_id\":21}', '/booking/view?id=21', 0, NULL, 0, '2026-01-17 20:38:13'),
(22, 6, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010022 - ประชุมทีมงาน ได้รับการบันทึกแล้ว', '{\"booking_id\":22}', '/booking/view?id=22', 0, NULL, 0, '2026-01-17 20:38:13'),
(23, 4, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010023 - ประชุมทีมงาน ได้รับการบันทึกแล้ว', '{\"booking_id\":23}', '/booking/view?id=23', 0, NULL, 0, '2026-01-17 20:38:13'),
(24, 5, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010024 - ประชุมทีมงาน ได้รับการบันทึกแล้ว', '{\"booking_id\":24}', '/booking/view?id=24', 0, NULL, 0, '2026-01-17 20:38:13'),
(25, 1, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010025 - ประชุมประจำเดือนกองดิจิทัล ได้รับการบันทึกแล้ว', '{\"booking_id\":25}', '/booking/view?id=25', 0, NULL, 0, '2026-01-18 02:07:34'),
(26, 1, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010025 - ประชุมประจำเดือนกองดิจิทัล ได้รับการบันทึกแล้ว', '{\"booking_id\":25}', '/booking/view?id=25', 0, NULL, 0, '2026-01-18 02:07:34'),
(27, 5, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010026 - ประชุมติดตามงาน ได้รับการบันทึกแล้ว', '{\"booking_id\":26}', '/booking/view?id=26', 0, NULL, 0, '2026-01-19 17:26:13'),
(28, 5, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010027 - ประชุมวิชาการ ได้รับการบันทึกแล้ว', '{\"booking_id\":27}', '/booking/view?id=27', 0, NULL, 0, '2026-01-19 17:26:13'),
(29, 6, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010028 - อบรมเชิงปฏิบัติการ ได้รับการบันทึกแล้ว', '{\"booking_id\":28}', '/booking/view?id=28', 0, NULL, 0, '2026-01-19 17:26:13'),
(30, 5, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010029 - ประชุมคณะกรรมการ ได้รับการบันทึกแล้ว', '{\"booking_id\":29}', '/booking/view?id=29', 0, NULL, 0, '2026-01-19 17:26:13'),
(31, 5, 'booking_created', 'การจองห้องประชุมสำเร็จ', 'การจอง BK26010030 - ประชุมวิชาการ ได้รับการบันทึกแล้ว', '{\"booking_id\":30}', '/booking/view?id=30', 0, NULL, 0, '2026-01-19 17:26:13');

-- --------------------------------------------------------

--
-- Table structure for table `room_equipment`
--

CREATE TABLE `room_equipment` (
  `id` int(10) UNSIGNED NOT NULL,
  `room_id` int(11) UNSIGNED NOT NULL,
  `equipment_id` int(11) UNSIGNED NOT NULL,
  `quantity` int(11) UNSIGNED NOT NULL DEFAULT 1,
  `is_included` tinyint(1) DEFAULT 1 COMMENT 'Included in room booking',
  `notes` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `room_image`
--

CREATE TABLE `room_image` (
  `id` int(10) UNSIGNED NOT NULL,
  `room_id` int(11) UNSIGNED NOT NULL,
  `filename` varchar(255) NOT NULL,
  `original_name` varchar(255) NOT NULL,
  `file_path` varchar(500) NOT NULL,
  `file_size` int(11) UNSIGNED DEFAULT NULL,
  `mime_type` varchar(100) DEFAULT NULL,
  `image_width` int(11) UNSIGNED DEFAULT NULL,
  `image_height` int(11) UNSIGNED DEFAULT NULL,
  `is_primary` tinyint(1) DEFAULT 0,
  `sort_order` smallint(6) DEFAULT 0,
  `alt_text` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `system_setting`
--

CREATE TABLE `system_setting` (
  `id` int(10) UNSIGNED NOT NULL,
  `setting_key` varchar(100) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `setting_type` varchar(20) DEFAULT 'string' COMMENT 'string, integer, boolean, json',
  `category` varchar(50) DEFAULT 'general',
  `description` text DEFAULT NULL,
  `is_public` tinyint(1) DEFAULT 0,
  `updated_by` int(11) UNSIGNED DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `system_setting`
--

INSERT INTO `system_setting` (`id`, `setting_key`, `setting_value`, `setting_type`, `category`, `description`, `is_public`, `updated_by`, `updated_at`) VALUES
(1, 'site_name', 'ระบบจองห้องประชุม', 'string', 'general', 'ชื่อระบบ', 0, NULL, '2026-01-17 16:25:04'),
(2, 'site_name_en', 'Meeting Room Booking System', 'string', 'general', 'Site name (English)', 0, NULL, '2026-01-17 16:25:04'),
(3, 'organization_name', 'สถาบันพระบรมราชชนก', 'string', 'general', 'ชื่อหน่วยงาน', 0, NULL, '2026-01-17 16:25:04'),
(4, 'admin_email', 'admin@example.com', 'string', 'general', 'อีเมลผู้ดูแลระบบ', 0, NULL, '2026-01-17 16:25:04'),
(5, 'timezone', 'Asia/Bangkok', 'string', 'general', 'เขตเวลา', 0, NULL, '2026-01-17 16:25:04'),
(6, 'date_format', 'd/m/Y', 'string', 'general', 'รูปแบบวันที่', 0, NULL, '2026-01-17 16:25:04'),
(7, 'time_format', 'H:i', 'string', 'general', 'รูปแบบเวลา', 0, NULL, '2026-01-17 16:25:04'),
(8, 'default_booking_duration', '60', 'integer', 'booking', 'ระยะเวลาจองเริ่มต้น (นาที)', 0, NULL, '2026-01-17 16:25:04'),
(9, 'max_advance_booking_days', '30', 'integer', 'booking', 'จองล่วงหน้าได้สูงสุด (วัน)', 0, NULL, '2026-01-17 16:25:04'),
(10, 'allow_past_booking', '0', 'boolean', 'booking', 'อนุญาตให้จองย้อนหลัง', 0, NULL, '2026-01-17 16:25:04'),
(11, 'require_approval', '0', 'boolean', 'booking', 'ต้องรออนุมัติทุกการจอง', 0, NULL, '2026-01-17 16:25:04'),
(12, 'send_reminder_before', '30', 'integer', 'notification', 'ส่งแจ้งเตือนก่อนประชุม (นาที)', 0, NULL, '2026-01-17 16:25:04'),
(13, 'password_min_length', '8', 'integer', 'security', 'ความยาวรหัสผ่านขั้นต่ำ', 0, NULL, '2026-01-17 16:25:04'),
(14, 'password_require_uppercase', '1', 'boolean', 'security', 'ต้องมีตัวพิมพ์ใหญ่', 0, NULL, '2026-01-17 16:25:04'),
(15, 'password_require_number', '1', 'boolean', 'security', 'ต้องมีตัวเลข', 0, NULL, '2026-01-17 16:25:04'),
(16, 'password_require_special', '1', 'boolean', 'security', 'ต้องมีอักขระพิเศษ', 0, NULL, '2026-01-17 16:25:04'),
(17, 'max_login_attempts', '5', 'integer', 'security', 'จำนวนครั้งที่ล็อกอินผิดได้', 0, NULL, '2026-01-17 16:25:04'),
(18, 'lockout_duration', '30', 'integer', 'security', 'ระยะเวลาล็อค (นาที)', 0, NULL, '2026-01-17 16:25:04'),
(19, 'session_timeout', '3600', 'integer', 'security', 'หมดเวลาเซสชัน (วินาที)', 0, NULL, '2026-01-17 16:25:04'),
(20, 'enable_oauth_azure', '1', 'boolean', 'oauth', 'เปิดใช้ Azure AD Login', 0, NULL, '2026-01-17 16:25:04'),
(21, 'enable_oauth_google', '1', 'boolean', 'oauth', 'เปิดใช้ Google Login', 0, NULL, '2026-01-17 16:25:04'),
(22, 'enable_oauth_thaid', '1', 'boolean', 'oauth', 'เปิดใช้ ThaID Login', 0, NULL, '2026-01-17 16:25:04'),
(23, 'enable_oauth_facebook', '0', 'boolean', 'oauth', 'เปิดใช้ Facebook Login', 0, NULL, '2026-01-17 16:25:04'),
(24, 'enable_2fa', '1', 'boolean', 'security', 'เปิดใช้ Two-Factor Authentication', 0, NULL, '2026-01-17 16:25:04');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(10) UNSIGNED NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password_hash` varchar(255) NOT NULL,
  `auth_key` varchar(32) NOT NULL,
  `password_reset_token` varchar(255) DEFAULT NULL,
  `verification_token` varchar(255) DEFAULT NULL,
  `email_verified` tinyint(3) UNSIGNED NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `title` varchar(100) DEFAULT NULL,
  `full_name` varchar(255) DEFAULT NULL COMMENT 'Full Name',
  `first_name` varchar(100) DEFAULT NULL,
  `last_name` varchar(100) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `avatar` varchar(255) DEFAULT NULL,
  `department_id` int(11) UNSIGNED DEFAULT NULL,
  `position` varchar(100) DEFAULT NULL,
  `azure_id` varchar(255) DEFAULT NULL,
  `google_id` varchar(255) DEFAULT NULL,
  `thaid_id` varchar(255) DEFAULT NULL,
  `facebook_id` varchar(255) DEFAULT NULL,
  `two_factor_secret` varchar(255) DEFAULT NULL,
  `two_factor_enabled` tinyint(1) DEFAULT 0,
  `backup_codes` text DEFAULT NULL,
  `failed_login_attempts` smallint(6) DEFAULT 0,
  `locked_until` timestamp NULL DEFAULT NULL,
  `password_changed_at` timestamp NULL DEFAULT NULL,
  `last_login_at` timestamp NULL DEFAULT NULL,
  `last_login_ip` varchar(45) DEFAULT NULL,
  `status` smallint(6) NOT NULL DEFAULT 10,
  `role` varchar(20) NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password_hash`, `auth_key`, `password_reset_token`, `verification_token`, `email_verified`, `email_verified_at`, `title`, `full_name`, `first_name`, `last_name`, `address`, `phone`, `avatar`, `department_id`, `position`, `azure_id`, `google_id`, `thaid_id`, `facebook_id`, `two_factor_secret`, `two_factor_enabled`, `backup_codes`, `failed_login_attempts`, `locked_until`, `password_changed_at`, `last_login_at`, `last_login_ip`, `status`, `role`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'admin', 'admin@pi.ac.th', '$2y$13$PA/wjsKQMoWuuvqQlB35yu2ELLjanSFOpSu1XLO29/BM6N6TOcNKK', 'JoSb1NFibHQ7G6AnT9JH_MUjxxX2GKF9', NULL, NULL, 1, NULL, NULL, 'ผู้ดูแลระบบ', 'System', 'Administrator', NULL, '02-712-7000', '/uploads/avatars/avatar_1_1768924595.jpg', 1, '', NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, '2026-01-17 16:25:05', '2026-01-21 04:43:38', '127.0.0.1', 10, 'admin', '2026-01-17 16:25:05', '2026-01-21 05:06:26', NULL),
(2, 'superadmin', 'superadmin@bizco.co.th', '$2y$13$55LQK3f/1iMrdG39XbcbQOVw0VXTaFs51XFIxAnsY4sP7iVqiQRX2', 'f_JcOO8kWxLNUrBMW2PTQmio0fRcFnQQ', NULL, NULL, 1, NULL, NULL, 'ผู้ดูแลระบบสูงสุด', NULL, NULL, NULL, '02-712-7000', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, '2026-01-17 20:11:31', NULL, NULL, 10, 'superadmin', '2026-01-17 20:11:31', '2026-01-17 20:11:31', NULL),
(3, 'approver', 'approver@bizco.co.th', '$2y$13$rN54cKpJ423OrK62X3PwY..UifEZMeRHC6xl8U5AS7ieJTy8N0WoO', 'HPdZlxarFjbYXHEb1engDE4zkYs0l1yz', NULL, NULL, 1, NULL, NULL, 'ผู้อนุมัติ', NULL, NULL, NULL, '02-712-7000', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, '2026-01-17 20:11:31', NULL, NULL, 10, 'approver', '2026-01-17 20:11:31', '2026-01-17 20:11:31', NULL),
(4, 'user1', 'user1@bizco.co.th', '$2y$13$kn7iUbGbzym0nGDoBLBryeuHzna/PZmKP4Gy9dUKCco0PRAsbzb1e', '_k_2Ah4nADRHSjTQuNqp5UWNQBBK9DvJ', NULL, NULL, 1, NULL, NULL, 'สมชาย ใจดี', NULL, NULL, NULL, '02-712-7000', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, '2026-01-17 20:11:31', NULL, NULL, 10, 'user', '2026-01-17 20:11:31', '2026-01-17 20:11:31', NULL),
(5, 'user2', 'user2@bizco.co.th', '$2y$13$i.lpydRLCpJZN4sLY9B4WejEvavGwILTAfJb2I.MJvHfOkuaf9n5e', 'Qc04Avrew9zSmFdpaur7lMOva6fCswFL', NULL, NULL, 1, NULL, NULL, 'สมหญิง รักเรียน', NULL, NULL, NULL, '02-712-7000', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, '2026-01-17 20:11:32', NULL, NULL, 10, 'user', '2026-01-17 20:11:32', '2026-01-17 20:11:32', NULL),
(6, 'user3', 'user3@bizco.co.th', '$2y$13$kOSpHIF7bAe2399Fg8VgX.sluKnkPgF5/woneZJqk5Sunh0SJAcdC', '5tyoVO0E5ezts2anJvxLCqpIi347_Zez', NULL, NULL, 1, NULL, NULL, 'วิชัย พัฒนา', NULL, NULL, NULL, '02-712-7000', NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, 0, NULL, '2026-01-17 20:11:32', NULL, NULL, 10, 'user', '2026-01-17 20:11:32', '2026-01-17 20:11:32', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `user_oauth`
--

CREATE TABLE `user_oauth` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `provider` varchar(50) NOT NULL COMMENT 'OAuth provider: google, microsoft, thaid',
  `provider_user_id` varchar(255) NOT NULL COMMENT 'Unique ID from the OAuth provider',
  `access_token` text DEFAULT NULL COMMENT 'OAuth access token',
  `refresh_token` text DEFAULT NULL COMMENT 'OAuth refresh token',
  `token_expires_at` int(11) DEFAULT NULL COMMENT 'Token expiration timestamp',
  `profile_data` text DEFAULT NULL COMMENT 'JSON-encoded profile data from provider',
  `created_at` int(11) NOT NULL,
  `updated_at` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `user_session`
--

CREATE TABLE `user_session` (
  `id` int(10) UNSIGNED NOT NULL,
  `user_id` int(11) UNSIGNED NOT NULL,
  `session_id` varchar(128) NOT NULL,
  `ip_address` varchar(45) NOT NULL,
  `user_agent` text DEFAULT NULL,
  `device_type` varchar(50) DEFAULT NULL,
  `browser` varchar(100) DEFAULT NULL,
  `platform` varchar(100) DEFAULT NULL,
  `location` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  `last_activity_at` timestamp NULL DEFAULT current_timestamp(),
  `expired_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `attachment`
--
ALTER TABLE `attachment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_attachment_model` (`model_class`,`model_id`);

--
-- Indexes for table `audit_log`
--
ALTER TABLE `audit_log`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_audit_user` (`user_id`),
  ADD KEY `idx_audit_action` (`action`),
  ADD KEY `idx_audit_model` (`model_class`,`model_id`),
  ADD KEY `idx_audit_created` (`created_at`);

--
-- Indexes for table `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD PRIMARY KEY (`item_name`,`user_id`),
  ADD KEY `idx_auth_assignment_user` (`user_id`);

--
-- Indexes for table `auth_item`
--
ALTER TABLE `auth_item`
  ADD PRIMARY KEY (`name`),
  ADD KEY `idx_auth_item_type` (`type`),
  ADD KEY `fk_auth_item_rule` (`rule_name`);

--
-- Indexes for table `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD PRIMARY KEY (`parent`,`child`),
  ADD KEY `fk_auth_child_child` (`child`);

--
-- Indexes for table `auth_rule`
--
ALTER TABLE `auth_rule`
  ADD PRIMARY KEY (`name`);

--
-- Indexes for table `booking`
--
ALTER TABLE `booking`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `booking_code` (`booking_code`),
  ADD KEY `idx_booking_room` (`room_id`),
  ADD KEY `idx_booking_user` (`user_id`),
  ADD KEY `idx_booking_date` (`booking_date`),
  ADD KEY `idx_booking_status` (`status`),
  ADD KEY `idx_booking_department` (`department_id`),
  ADD KEY `idx_booking_parent` (`parent_booking_id`),
  ADD KEY `idx_booking_availability` (`room_id`,`booking_date`,`start_time`,`end_time`,`status`),
  ADD KEY `fk_booking_approved_by` (`approved_by`);

--
-- Indexes for table `booking_attendee`
--
ALTER TABLE `booking_attendee`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_attendee_booking` (`booking_id`),
  ADD KEY `idx_attendee_user` (`user_id`);

--
-- Indexes for table `booking_equipment`
--
ALTER TABLE `booking_equipment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_booking_equip_booking` (`booking_id`),
  ADD KEY `idx_booking_equip_equipment` (`equipment_id`);

--
-- Indexes for table `building`
--
ALTER TABLE `building`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `department`
--
ALTER TABLE `department`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`),
  ADD KEY `fk_department_parent` (`parent_id`);

--
-- Indexes for table `email_template`
--
ALTER TABLE `email_template`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `template_key` (`template_key`);

--
-- Indexes for table `equipment`
--
ALTER TABLE `equipment`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `equipment_code` (`equipment_code`),
  ADD KEY `idx_equipment_category` (`category_id`),
  ADD KEY `idx_equipment_building` (`building_id`),
  ADD KEY `idx_equipment_status` (`status`);

--
-- Indexes for table `equipment_category`
--
ALTER TABLE `equipment_category`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `code` (`code`);

--
-- Indexes for table `holiday`
--
ALTER TABLE `holiday`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_holiday_date` (`holiday_date`);

--
-- Indexes for table `login_history`
--
ALTER TABLE `login_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_login_user` (`user_id`),
  ADD KEY `idx_login_ip` (`ip_address`),
  ADD KEY `idx_login_status` (`login_status`),
  ADD KEY `idx_login_created` (`created_at`);

--
-- Indexes for table `meeting_room`
--
ALTER TABLE `meeting_room`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `room_code` (`room_code`),
  ADD KEY `idx_room_building` (`building_id`),
  ADD KEY `idx_room_status` (`status`),
  ADD KEY `idx_room_type` (`room_type`),
  ADD KEY `idx_room_capacity` (`capacity`),
  ADD KEY `idx_room_deleted` (`deleted_at`);

--
-- Indexes for table `migration`
--
ALTER TABLE `migration`
  ADD PRIMARY KEY (`version`);

--
-- Indexes for table `notification`
--
ALTER TABLE `notification`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_notification_user` (`user_id`),
  ADD KEY `idx_notification_read` (`is_read`);

--
-- Indexes for table `room_equipment`
--
ALTER TABLE `room_equipment`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_room_equip_room` (`room_id`),
  ADD KEY `idx_room_equip_equipment` (`equipment_id`);

--
-- Indexes for table `room_image`
--
ALTER TABLE `room_image`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_image_room` (`room_id`),
  ADD KEY `idx_image_primary` (`is_primary`);

--
-- Indexes for table `system_setting`
--
ALTER TABLE `system_setting`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `setting_key` (`setting_key`),
  ADD KEY `idx_setting_category` (`category`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`),
  ADD UNIQUE KEY `email` (`email`),
  ADD UNIQUE KEY `password_reset_token` (`password_reset_token`),
  ADD UNIQUE KEY `verification_token` (`verification_token`),
  ADD UNIQUE KEY `azure_id` (`azure_id`),
  ADD UNIQUE KEY `google_id` (`google_id`),
  ADD UNIQUE KEY `thaid_id` (`thaid_id`),
  ADD UNIQUE KEY `facebook_id` (`facebook_id`),
  ADD KEY `idx_user_status` (`status`),
  ADD KEY `idx_user_role` (`role`),
  ADD KEY `idx_user_department` (`department_id`),
  ADD KEY `idx_user_deleted` (`deleted_at`);

--
-- Indexes for table `user_oauth`
--
ALTER TABLE `user_oauth`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `user_session`
--
ALTER TABLE `user_session`
  ADD PRIMARY KEY (`id`),
  ADD KEY `idx_session_user` (`user_id`),
  ADD KEY `idx_session_id` (`session_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `attachment`
--
ALTER TABLE `attachment`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_log`
--
ALTER TABLE `audit_log`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=71;

--
-- AUTO_INCREMENT for table `booking`
--
ALTER TABLE `booking`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=31;

--
-- AUTO_INCREMENT for table `booking_attendee`
--
ALTER TABLE `booking_attendee`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `booking_equipment`
--
ALTER TABLE `booking_equipment`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `building`
--
ALTER TABLE `building`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `department`
--
ALTER TABLE `department`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `email_template`
--
ALTER TABLE `email_template`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `equipment`
--
ALTER TABLE `equipment`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `equipment_category`
--
ALTER TABLE `equipment_category`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `holiday`
--
ALTER TABLE `holiday`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'ID', AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `login_history`
--
ALTER TABLE `login_history`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `meeting_room`
--
ALTER TABLE `meeting_room`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `notification`
--
ALTER TABLE `notification`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `room_equipment`
--
ALTER TABLE `room_equipment`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `room_image`
--
ALTER TABLE `room_image`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `system_setting`
--
ALTER TABLE `system_setting`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `user_oauth`
--
ALTER TABLE `user_oauth`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `user_session`
--
ALTER TABLE `user_session`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `auth_assignment`
--
ALTER TABLE `auth_assignment`
  ADD CONSTRAINT `fk_auth_assignment_item` FOREIGN KEY (`item_name`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `auth_item`
--
ALTER TABLE `auth_item`
  ADD CONSTRAINT `fk_auth_item_rule` FOREIGN KEY (`rule_name`) REFERENCES `auth_rule` (`name`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `auth_item_child`
--
ALTER TABLE `auth_item_child`
  ADD CONSTRAINT `fk_auth_child_child` FOREIGN KEY (`child`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_auth_child_parent` FOREIGN KEY (`parent`) REFERENCES `auth_item` (`name`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `booking`
--
ALTER TABLE `booking`
  ADD CONSTRAINT `fk_booking_approved_by` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_booking_department` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_booking_parent` FOREIGN KEY (`parent_booking_id`) REFERENCES `booking` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_booking_room` FOREIGN KEY (`room_id`) REFERENCES `meeting_room` (`id`) ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_booking_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `booking_attendee`
--
ALTER TABLE `booking_attendee`
  ADD CONSTRAINT `fk_attendee_booking` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_attendee_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `booking_equipment`
--
ALTER TABLE `booking_equipment`
  ADD CONSTRAINT `fk_booking_equip_booking` FOREIGN KEY (`booking_id`) REFERENCES `booking` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_booking_equip_equipment` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `department`
--
ALTER TABLE `department`
  ADD CONSTRAINT `fk_department_parent` FOREIGN KEY (`parent_id`) REFERENCES `department` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `equipment`
--
ALTER TABLE `equipment`
  ADD CONSTRAINT `fk_equipment_building` FOREIGN KEY (`building_id`) REFERENCES `building` (`id`) ON DELETE SET NULL ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_equipment_category` FOREIGN KEY (`category_id`) REFERENCES `equipment_category` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `meeting_room`
--
ALTER TABLE `meeting_room`
  ADD CONSTRAINT `fk_room_building` FOREIGN KEY (`building_id`) REFERENCES `building` (`id`) ON UPDATE CASCADE;

--
-- Constraints for table `notification`
--
ALTER TABLE `notification`
  ADD CONSTRAINT `fk_notification_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `room_equipment`
--
ALTER TABLE `room_equipment`
  ADD CONSTRAINT `fk_room_equip_equipment` FOREIGN KEY (`equipment_id`) REFERENCES `equipment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_room_equip_room` FOREIGN KEY (`room_id`) REFERENCES `meeting_room` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `room_image`
--
ALTER TABLE `room_image`
  ADD CONSTRAINT `fk_image_room` FOREIGN KEY (`room_id`) REFERENCES `meeting_room` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_room_image_room` FOREIGN KEY (`room_id`) REFERENCES `meeting_room` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `fk_user_department` FOREIGN KEY (`department_id`) REFERENCES `department` (`id`) ON DELETE SET NULL ON UPDATE CASCADE;

--
-- Constraints for table `user_session`
--
ALTER TABLE `user_session`
  ADD CONSTRAINT `fk_session_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
