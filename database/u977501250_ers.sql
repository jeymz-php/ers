-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Jun 03, 2026 at 07:20 PM
-- Server version: 11.8.6-MariaDB-log
-- PHP Version: 7.2.34

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `u977501250_ers`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin_action_logs`
--

CREATE TABLE `admin_action_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED NOT NULL,
  `target_user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `details` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `admin_action_logs`
--

INSERT INTO `admin_action_logs` (`id`, `admin_id`, `target_user_id`, `action`, `details`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'approve_user', 'User account approved (bulk)', '2026-06-04 02:07:06', '2026-06-04 02:07:06'),
(2, 1, 2, 'reject_reservation', 'Rejected reservation #1: ITechtivity 2026. Reason: Required OSAS Approval.', '2026-06-04 02:11:22', '2026-06-04 02:11:22');

-- --------------------------------------------------------

--
-- Table structure for table `campuses`
--

CREATE TABLE `campuses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `display_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `campuses`
--

INSERT INTO `campuses` (`id`, `name`, `code`, `address`, `is_active`, `display_order`, `created_at`, `updated_at`) VALUES
(1, 'Main Campus', 'MC', 'Caloocan City', 1, 1, '2026-06-04 01:58:59', '2026-06-04 01:58:59'),
(2, 'Congressional Extension Campus', 'CEC', 'Caloocan City', 1, 2, '2026-06-04 01:58:59', '2026-06-04 01:59:05'),
(3, 'Camarin Extension Campus', 'CAM', 'Caloocan City', 1, 3, '2026-06-04 01:58:59', '2026-06-04 01:59:05'),
(4, 'Bagong Silang Campus', 'BS', 'Caloocan City', 1, 4, '2026-06-04 01:58:59', '2026-06-04 01:59:05');

-- --------------------------------------------------------

--
-- Table structure for table `chatbot_sessions`
--

CREATE TABLE `chatbot_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `step` varchar(255) NOT NULL DEFAULT 'idle',
  `data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chatbot_sessions`
--

INSERT INTO `chatbot_sessions` (`id`, `user_id`, `step`, `data`, `created_at`, `updated_at`) VALUES
(1, 2, 'idle', '[]', '2026-06-04 03:12:37', '2026-06-04 03:15:53');

-- --------------------------------------------------------

--
-- Table structure for table `chat_sessions`
--

CREATE TABLE `chat_sessions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `handled_by_admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_handled_by_admin_id` bigint(20) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `ended_at` timestamp NULL DEFAULT NULL,
  `closing_message` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `chat_sessions`
--

INSERT INTO `chat_sessions` (`id`, `user_id`, `admin_id`, `handled_by_admin_id`, `is_handled_by_admin_id`, `is_active`, `ended_at`, `closing_message`, `created_at`, `updated_at`) VALUES
(1, 2, 1, NULL, NULL, 1, NULL, NULL, '2026-06-04 03:12:37', '2026-06-04 03:18:32');

-- --------------------------------------------------------

--
-- Table structure for table `establishments`
--

CREATE TABLE `establishments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(200) NOT NULL,
  `campus_id` bigint(20) UNSIGNED NOT NULL,
  `capacity` int(11) NOT NULL,
  `type` enum('Indoor','Outdoor') NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `establishments`
--

INSERT INTO `establishments` (`id`, `name`, `campus_id`, `capacity`, `type`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Social Hall', 1, 300, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(2, 'Covered Court', 1, 300, 'Outdoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(3, 'CLEP Room', 1, 50, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(4, 'MOOT Court', 1, 300, 'Outdoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(5, 'BIO Lab', 1, 50, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(6, 'Physics Lab', 1, 50, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(7, 'Chemistry Lab', 1, 50, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(8, '5th Floor Veranda', 1, 50, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(9, 'Speech Lab', 1, 50, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(10, 'Room 201', 1, 50, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(11, 'Student Activity Center', 1, 30, 'Outdoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(12, '4th Floor Student Lounge', 1, 30, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(13, 'Computer Lab 1', 1, 50, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(14, 'Computer Lab 2', 1, 50, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(15, 'Computer Lab 3', 1, 50, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(16, 'Library AVR', 1, 30, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(17, '2nd Floor Lobby', 1, 10, 'Outdoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(18, 'Room 203', 1, 40, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(19, 'Room 204', 1, 40, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(20, 'Lecture Room 302', 1, 150, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(21, 'Room 206', 1, 40, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(22, 'External Venue - SM Sangandaan', 1, 500, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(23, 'Room 104', 1, 40, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(24, 'Room 106', 1, 40, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(25, 'Room 209', 1, 40, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(26, 'Room 101', 1, 30, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(27, 'Room 210', 1, 40, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(28, 'Room 401', 1, 40, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(29, 'Multimedia Room', 1, 50, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(30, 'Room 208', 1, 40, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(31, 'Covered Court', 2, 300, 'Outdoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(32, 'Social Hall', 2, 300, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(33, 'Speech Lab', 2, 40, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(34, 'Room 403', 2, 40, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(35, 'Parking Area', 2, 50, 'Outdoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(36, 'Room 406', 2, 50, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(37, 'Room 301', 2, 40, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(38, 'Room 302', 2, 40, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(39, 'Room 303', 2, 40, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(40, 'Room 304', 2, 40, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(41, 'Covered Court', 3, 300, 'Outdoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(42, 'Audio Visual Room', 3, 60, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(43, 'University Library', 3, 30, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(44, 'Multi-Purpose Hall', 4, 200, 'Indoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06'),
(45, 'Covered Court', 4, 250, 'Outdoor', 1, '2026-06-04 01:59:06', '2026-06-04 01:59:06');

-- --------------------------------------------------------

--
-- Table structure for table `failed_jobs`
--

CREATE TABLE `failed_jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `uuid` varchar(255) NOT NULL,
  `connection` text NOT NULL,
  `queue` text NOT NULL,
  `payload` longtext NOT NULL,
  `exception` longtext NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `messages`
--

CREATE TABLE `messages` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `sender_id` bigint(20) UNSIGNED DEFAULT NULL,
  `receiver_id` bigint(20) UNSIGNED DEFAULT NULL,
  `session_id` bigint(20) UNSIGNED DEFAULT NULL,
  `message` text NOT NULL,
  `attachment` varchar(255) DEFAULT NULL,
  `attachment_type` varchar(255) DEFAULT NULL,
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `messages`
--

INSERT INTO `messages` (`id`, `sender_id`, `receiver_id`, `session_id`, `message`, `attachment`, `attachment_type`, `is_read`, `read_at`, `created_at`, `updated_at`) VALUES
(1, NULL, 2, 1, '👋 Hello! Customer support is now available.\n\nPlease type your message below and our team will respond shortly.', NULL, NULL, 1, '2026-06-04 03:18:29', '2026-06-04 03:12:37', '2026-06-04 03:18:29'),
(2, NULL, 2, 1, '👋 Hello! Customer support is now available.\n\nPlease type your message below and our team will respond shortly.', NULL, NULL, 1, '2026-06-04 03:18:29', '2026-06-04 03:12:47', '2026-06-04 03:18:29'),
(3, NULL, 2, 1, '👋 Hello! Customer support is now available.\n\nPlease type your message below and our team will respond shortly.', NULL, NULL, 1, '2026-06-04 03:18:29', '2026-06-04 03:14:49', '2026-06-04 03:18:29'),
(4, NULL, 2, 1, '👋 Hello! Customer support is now available.\n\nPlease type your message below and our team will respond shortly.', NULL, NULL, 1, '2026-06-04 03:18:29', '2026-06-04 03:15:27', '2026-06-04 03:18:29'),
(5, NULL, 2, 1, '👋 Hello! Customer support is now available.\n\nPlease type your message below and our team will respond shortly.', NULL, NULL, 1, '2026-06-04 03:18:29', '2026-06-04 03:15:56', '2026-06-04 03:18:29'),
(6, NULL, 2, 1, '👋 Hello! Customer support is now available.\n\nPlease type your message below and our team will respond shortly.', NULL, NULL, 1, '2026-06-04 03:18:29', '2026-06-04 03:17:16', '2026-06-04 03:18:29'),
(7, NULL, 2, 1, '👋 Hello! Customer support is now available.\n\nPlease type your message below and our team will respond shortly.', NULL, NULL, 1, '2026-06-04 03:18:29', '2026-06-04 03:18:26', '2026-06-04 03:18:29'),
(8, 2, NULL, 1, '', 'chat_attachments/1780514326_6a207e1647c83.pdf', 'application/pdf', 1, '2026-06-04 03:18:47', '2026-06-04 03:18:46', '2026-06-04 03:18:47'),
(9, 2, NULL, 1, '', 'chat_attachments/1780514336_6a207e208a7c3.png', 'image/png', 1, '2026-06-04 03:18:56', '2026-06-04 03:18:56', '2026-06-04 03:18:56');

-- --------------------------------------------------------

--
-- Table structure for table `migrations`
--

CREATE TABLE `migrations` (
  `id` int(10) UNSIGNED NOT NULL,
  `migration` varchar(255) NOT NULL,
  `batch` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `migrations`
--

INSERT INTO `migrations` (`id`, `migration`, `batch`) VALUES
(1, '2014_10_12_000000_create_users_table', 1),
(2, '2019_08_19_000000_create_failed_jobs_table', 1),
(3, '2019_12_14_000001_create_personal_access_tokens_table', 1),
(4, '2026_05_28_124444_create_campuses_table', 1),
(5, '2026_05_28_124457_add_phone_and_campus_to_users_table', 1),
(6, '2026_05_28_124523_create_password_resets_table', 1),
(7, '2026_05_28_131729_add_role_and_status_to_users_table', 1),
(8, '2026_05_28_131751_create_admin_action_logs_table', 1),
(9, '2026_05_28_222306_create_establishments_table', 1),
(10, '2026_05_28_222325_create_reservations_table', 1),
(11, '2026_05_30_012640_create_notifications_table', 1),
(12, '2026_05_30_202438_create_chatbot_sessions_table', 1),
(13, '2026_06_01_194057_create_messages_table', 1),
(14, '2026_06_01_204920_add_attachments_to_messages_table', 1),
(15, '2026_06_01_205010_create_chat_sessions_table', 1),
(16, '2026_06_03_143606_add_handled_by_to_chat_sessions', 1),
(17, '2026_06_03_143944_add_session_id_to_messages', 1),
(18, '2026_06_03_150000_make_sender_receiver_nullable_in_messages', 1);

-- --------------------------------------------------------

--
-- Table structure for table `notifications`
--

CREATE TABLE `notifications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `reservation_id` bigint(20) UNSIGNED DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'reservation',
  `is_read` tinyint(1) NOT NULL DEFAULT 0,
  `read_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `notifications`
--

INSERT INTO `notifications` (`id`, `user_id`, `reservation_id`, `title`, `message`, `type`, `is_read`, `read_at`, `created_at`, `updated_at`) VALUES
(1, 1, 1, '📅 New Multi-Date Reservation Request', 'James Ryan Gregorio requested \'ITechtivity 2026\' at Main Campus for 2 dates (Jun 23 - Jun 24)', 'reservation', 1, '2026-06-04 02:10:34', '2026-06-04 02:10:25', '2026-06-04 02:10:34'),
(2, 1, 2, '📅 New Multi-Date Reservation Request', 'James Ryan Gregorio requested \'ITechtivity 2026\' at Main Campus for 2 dates (Jun 23 - Jun 24)', 'reservation', 1, '2026-06-04 02:13:12', '2026-06-04 02:13:08', '2026-06-04 02:13:12'),
(3, 1, 3, '📅 New Multi-Date Reservation Request', 'James Ryan Gregorio requested \'TEST\' at Main Campus for 2 dates (Jun 24 - Jun 25)', 'reservation', 1, '2026-06-04 02:39:53', '2026-06-04 02:39:49', '2026-06-04 02:39:53'),
(4, 1, 4, '📅 New Multi-Date Reservation Request', 'James Ryan Gregorio requested \'TEST\' at Main Campus for 2 dates (Jun 24 - Jun 25)', 'reservation', 1, '2026-06-04 03:08:54', '2026-06-04 03:08:48', '2026-06-04 03:08:54'),
(5, 1, NULL, '💬 New Chat Session Started', 'User James Ryan Gregorio has started a chat session.', 'chat', 1, '2026-06-04 03:18:32', '2026-06-04 03:18:26', '2026-06-04 03:18:32'),
(6, 1, NULL, '💬 New Chat Message', 'James Ryan Gregorio: Sent an attachment', 'chat', 1, '2026-06-04 03:18:47', '2026-06-04 03:18:46', '2026-06-04 03:18:47'),
(7, 1, NULL, '💬 New Chat Message', 'James Ryan Gregorio: Sent an attachment', 'chat', 1, '2026-06-04 03:18:56', '2026-06-04 03:18:56', '2026-06-04 03:18:56');

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `personal_access_tokens`
--

CREATE TABLE `personal_access_tokens` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tokenable_type` varchar(255) NOT NULL,
  `tokenable_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `token` varchar(64) NOT NULL,
  `abilities` text DEFAULT NULL,
  `last_used_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `reservations`
--

CREATE TABLE `reservations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `establishment_id` bigint(20) UNSIGNED NOT NULL,
  `campus_id` bigint(20) UNSIGNED NOT NULL,
  `event_name` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `event_date` date NOT NULL,
  `start_time` time NOT NULL,
  `end_time` time NOT NULL,
  `status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `reservations`
--

INSERT INTO `reservations` (`id`, `user_id`, `establishment_id`, `campus_id`, `event_name`, `description`, `event_date`, `start_time`, `end_time`, `status`, `remarks`, `approved_at`, `approved_by`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 1, 'ITechtivity 2026', 'Showcase of CSD\'s Capstone Project and Research 2', '2026-06-23', '07:00:00', '21:00:00', 'rejected', '{\"user_type\":\"student\",\"department\":\"CSD\",\"equipment\":[\"Sound System\",\"2pcs. TV\"],\"attachments\":[],\"multiple_dates\":[\"2026-06-23\",\"2026-06-24\"],\"is_multi_date\":true,\"rejection_reason\":\"Required OSAS Approval.\",\"rejected_at\":\"2026-06-04 02:11:18\",\"rejected_by\":\"System Super Administrator\"}', NULL, NULL, '2026-06-04 02:10:25', '2026-06-04 02:11:18'),
(2, 2, 2, 1, 'ITechtivity 2026', 'Showcase of CSD\'s Capstone Project and Research 2', '2026-06-23', '07:00:00', '21:00:00', 'pending', '{\"user_type\":\"student\",\"department\":\"CSD\",\"equipment\":[\"Sound System\",\"2pcs. TV\",\"24pcs. Table\",\"100pcs. Chair\",\"Podium\"],\"attachments\":[\"reservations\\/2026\\/06\\/0s0dRTsQeSf73JkXnP2Vvb16ugrDbjUuYa52nszH.png\",\"reservations\\/2026\\/06\\/FtUhcc0kb5RwvJaTsf1DBrE8SgOHQbdoulKO4jXr.pdf\"],\"multiple_dates\":[\"2026-06-23\",\"2026-06-24\"],\"is_multi_date\":true}', NULL, NULL, '2026-06-04 02:13:08', '2026-06-04 02:13:08'),
(3, 2, 12, 1, 'TEST', 'TEST', '2026-06-24', '07:00:00', '21:00:00', 'pending', '{\"user_type\":\"student\",\"department\":\"CSSD\",\"equipment\":[],\"attachments\":[\"reservations\\/2026\\/06\\/NYgGKZxgs7YzkT4hfihQ5nsurDGa6xbbhRQEilSP.pdf\"],\"multiple_dates\":[\"2026-06-24\",\"2026-06-25\"],\"is_multi_date\":true}', NULL, NULL, '2026-06-04 02:39:49', '2026-06-04 02:39:49'),
(4, 2, 12, 1, 'TEST', 'TEST', '2026-06-24', '07:00:00', '21:00:00', 'pending', '{\"user_type\":\"student\",\"department\":\"CSD\",\"equipment\":[],\"attachments\":[\"reservations\\/2026\\/06\\/TXhxjROR6yFcoBKxzMJFyQb90oLN5cBWDqaTtH0l.pdf\",\"reservations\\/2026\\/06\\/NFHCQ14QXIFcdL65FALRXHyWDESEwitbdtos9Yi8.png\"],\"multiple_dates\":[\"2026-06-24\",\"2026-06-25\"],\"is_multi_date\":true}', NULL, NULL, '2026-06-04 03:08:48', '2026-06-04 03:08:48');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) DEFAULT NULL,
  `campus_id` bigint(20) UNSIGNED DEFAULT NULL,
  `role` enum('user','admin','super_admin') NOT NULL DEFAULT 'user',
  `account_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_password_generated` tinyint(1) NOT NULL DEFAULT 0,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `name`, `email`, `phone_number`, `campus_id`, `role`, `account_status`, `rejection_reason`, `approved_at`, `email_verified_at`, `password`, `is_password_generated`, `remember_token`, `created_at`, `updated_at`, `approved_by`) VALUES
(1, 'System Super Administrator', 'ers@ucc-caloocan.edu.ph', '00000000000', 1, 'super_admin', 'approved', NULL, '2026-06-04 01:59:06', NULL, '$2y$10$2l2d0Uizsh29JqPkrr/3M.7rDj0IYH7FqWetcusIcw7TV6Bp4zRTa', 0, NULL, '2026-06-04 01:59:06', '2026-06-04 01:59:06', 1),
(2, 'James Ryan Gregorio', 'gregorio.jamesryanbsit2022@gmail.com', '09103692385', 1, 'user', 'approved', NULL, '2026-06-04 02:07:06', NULL, '$2y$10$43Jsn9rEsgeXXfw29ztZ8.NdDpZ5usqMTdbJzU50MB9L5h8im6jA.', 0, NULL, '2026-06-04 02:06:34', '2026-06-04 02:07:06', 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin_action_logs`
--
ALTER TABLE `admin_action_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `admin_action_logs_admin_id_foreign` (`admin_id`),
  ADD KEY `admin_action_logs_target_user_id_foreign` (`target_user_id`);

--
-- Indexes for table `campuses`
--
ALTER TABLE `campuses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `campuses_code_unique` (`code`);

--
-- Indexes for table `chatbot_sessions`
--
ALTER TABLE `chatbot_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chatbot_sessions_user_id_foreign` (`user_id`);

--
-- Indexes for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `chat_sessions_user_id_foreign` (`user_id`),
  ADD KEY `chat_sessions_admin_id_foreign` (`admin_id`),
  ADD KEY `chat_sessions_is_handled_by_admin_id_foreign` (`is_handled_by_admin_id`);

--
-- Indexes for table `establishments`
--
ALTER TABLE `establishments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `establishments_campus_id_foreign` (`campus_id`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `messages`
--
ALTER TABLE `messages`
  ADD PRIMARY KEY (`id`),
  ADD KEY `messages_sender_id_foreign` (`sender_id`),
  ADD KEY `messages_receiver_id_foreign` (`receiver_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `notifications`
--
ALTER TABLE `notifications`
  ADD PRIMARY KEY (`id`),
  ADD KEY `notifications_user_id_foreign` (`user_id`),
  ADD KEY `notifications_reservation_id_foreign` (`reservation_id`);

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD KEY `password_resets_email_index` (`email`);

--
-- Indexes for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `personal_access_tokens_token_unique` (`token`),
  ADD KEY `personal_access_tokens_tokenable_type_tokenable_id_index` (`tokenable_type`,`tokenable_id`);

--
-- Indexes for table `reservations`
--
ALTER TABLE `reservations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `reservations_user_id_foreign` (`user_id`),
  ADD KEY `reservations_establishment_id_foreign` (`establishment_id`),
  ADD KEY `reservations_campus_id_foreign` (`campus_id`),
  ADD KEY `reservations_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `users_email_unique` (`email`),
  ADD KEY `users_campus_id_foreign` (`campus_id`),
  ADD KEY `users_approved_by_foreign` (`approved_by`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin_action_logs`
--
ALTER TABLE `admin_action_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `campuses`
--
ALTER TABLE `campuses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `chatbot_sessions`
--
ALTER TABLE `chatbot_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `establishments`
--
ALTER TABLE `establishments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=46;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `messages`
--
ALTER TABLE `messages`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `notifications`
--
ALTER TABLE `notifications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `personal_access_tokens`
--
ALTER TABLE `personal_access_tokens`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `reservations`
--
ALTER TABLE `reservations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `admin_action_logs`
--
ALTER TABLE `admin_action_logs`
  ADD CONSTRAINT `admin_action_logs_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `admin_action_logs_target_user_id_foreign` FOREIGN KEY (`target_user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `chatbot_sessions`
--
ALTER TABLE `chatbot_sessions`
  ADD CONSTRAINT `chatbot_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `chat_sessions`
--
ALTER TABLE `chat_sessions`
  ADD CONSTRAINT `chat_sessions_admin_id_foreign` FOREIGN KEY (`admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chat_sessions_is_handled_by_admin_id_foreign` FOREIGN KEY (`is_handled_by_admin_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `chat_sessions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `establishments`
--
ALTER TABLE `establishments`
  ADD CONSTRAINT `establishments_campus_id_foreign` FOREIGN KEY (`campus_id`) REFERENCES `campuses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `messages`
--
ALTER TABLE `messages`
  ADD CONSTRAINT `messages_receiver_id_foreign` FOREIGN KEY (`receiver_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `messages_sender_id_foreign` FOREIGN KEY (`sender_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `notifications`
--
ALTER TABLE `notifications`
  ADD CONSTRAINT `notifications_reservation_id_foreign` FOREIGN KEY (`reservation_id`) REFERENCES `reservations` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `notifications_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `reservations`
--
ALTER TABLE `reservations`
  ADD CONSTRAINT `reservations_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `reservations_campus_id_foreign` FOREIGN KEY (`campus_id`) REFERENCES `campuses` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_establishment_id_foreign` FOREIGN KEY (`establishment_id`) REFERENCES `establishments` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `reservations_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `users`
--
ALTER TABLE `users`
  ADD CONSTRAINT `users_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `users_campus_id_foreign` FOREIGN KEY (`campus_id`) REFERENCES `campuses` (`id`) ON DELETE SET NULL;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
