-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 23, 2026 at 02:53 AM
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
-- Database: `gfinal`
--

-- --------------------------------------------------------

--
-- Table structure for table `applicants`
--

CREATE TABLE `applicants` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `job_vacancy_id` bigint(20) UNSIGNED NOT NULL,
  `application_number` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `gender` enum('male','female','other') DEFAULT NULL,
  `civil_status` varchar(50) DEFAULT NULL,
  `education` varchar(255) DEFAULT NULL,
  `work_experience` text DEFAULT NULL,
  `eligibility` varchar(255) DEFAULT NULL,
  `status` enum('pending','screening','interview','selected','not_selected','withdrawn','appointed') NOT NULL DEFAULT 'pending',
  `remarks` text DEFAULT NULL,
  `application_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `applicant_documents`
--

CREATE TABLE `applicant_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `applicant_id` bigint(20) UNSIGNED NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `appointments`
--

CREATE TABLE `appointments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `appointment_number` varchar(255) NOT NULL,
  `applicant_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employee_id` bigint(20) UNSIGNED DEFAULT NULL,
  `plantilla_id` bigint(20) UNSIGNED DEFAULT NULL,
  `office_id` bigint(20) UNSIGNED NOT NULL,
  `employment_type_id` bigint(20) UNSIGNED NOT NULL,
  `salary_grade_id` bigint(20) UNSIGNED NOT NULL,
  `salary_step` tinyint(4) NOT NULL DEFAULT 1,
  `position_title` varchar(255) NOT NULL,
  `appointment_type` varchar(50) NOT NULL,
  `effectivity_date` date NOT NULL,
  `end_date` date DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'probationary',
  `place_of_work` text DEFAULT NULL,
  `funding_source` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `audit_logs`
--

CREATE TABLE `audit_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `user_name` varchar(150) DEFAULT NULL,
  `module` varchar(100) NOT NULL,
  `action` varchar(100) NOT NULL,
  `description` text NOT NULL,
  `model_type` varchar(255) DEFAULT NULL,
  `model_id` bigint(20) UNSIGNED DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `url` text DEFAULT NULL,
  `method` varchar(10) DEFAULT NULL,
  `status` enum('success','failed','warning') NOT NULL DEFAULT 'success',
  `extra` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`extra`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `audit_logs`
--

INSERT INTO `audit_logs` (`id`, `user_id`, `user_name`, `module`, `action`, `description`, `model_type`, `model_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `url`, `method`, `status`, `extra`, `created_at`, `updated_at`) VALUES
(1, NULL, 'System', 'BusinessEntry', 'created', 'BusinessEntry \"dadad\" was created.', 'App\\Models\\BusinessEntry', 12, NULL, '{\"last_name\":\"dada\",\"first_name\":\"daa\",\"middle_name\":\"daad\",\"citizenship\":null,\"civil_status\":null,\"gender\":null,\"birthdate\":null,\"mobile_no\":null,\"email\":null,\"is_pwd\":false,\"is_4ps\":false,\"is_solo_parent\":false,\"is_senior\":false,\"discount_10\":false,\"discount_5\":false,\"owner_region\":null,\"owner_province\":null,\"owner_municipality\":null,\"owner_barangay\":null,\"owner_street\":null,\"emergency_contact_person\":null,\"emergency_mobile\":null,\"emergency_email\":null,\"business_name\":\"dadad\",\"trade_name\":\"dada\",\"date_of_application\":\"2026-02-23 00:00:00\",\"tin_no\":null,\"dti_sec_cda_no\":null,\"dti_sec_cda_date\":null,\"business_mobile\":null,\"business_email\":null,\"type_of_business\":null,\"amendment_from\":null,\"amendment_to\":null,\"tax_incentive\":false,\"business_organization\":null,\"business_area_type\":null,\"business_scale\":null,\"business_sector\":null,\"zone\":null,\"occupancy\":null,\"business_area_sqm\":null,\"total_employees\":null,\"employees_lgu\":null,\"business_region\":null,\"business_province\":null,\"business_municipality\":null,\"business_barangay\":null,\"business_street\":null,\"status\":\"pending\",\"permit_year\":2026,\"renewal_cycle\":0,\"updated_at\":\"2026-02-23 07:46:04\",\"created_at\":\"2026-02-23 07:46:04\",\"id\":12}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', 'http://127.0.0.1:8000/portal/apply', 'POST', 'success', NULL, '2026-02-22 23:46:05', '2026-02-22 23:46:05'),
(2, NULL, 'System', 'BusinessEntry', 'created', 'BusinessEntry \"qwe\" was created.', 'App\\Models\\BusinessEntry', 13, NULL, '{\"last_name\":\"qwe\",\"first_name\":\"qwe\",\"middle_name\":\"qwe\",\"citizenship\":null,\"civil_status\":null,\"gender\":null,\"birthdate\":null,\"mobile_no\":null,\"email\":null,\"is_pwd\":false,\"is_4ps\":false,\"is_solo_parent\":false,\"is_senior\":false,\"discount_10\":false,\"discount_5\":false,\"owner_region\":null,\"owner_province\":null,\"owner_municipality\":null,\"owner_barangay\":null,\"owner_street\":null,\"emergency_contact_person\":null,\"emergency_mobile\":null,\"emergency_email\":null,\"business_name\":\"qwe\",\"trade_name\":\"q\",\"date_of_application\":\"2026-02-23 00:00:00\",\"tin_no\":null,\"dti_sec_cda_no\":null,\"dti_sec_cda_date\":null,\"business_mobile\":null,\"business_email\":null,\"type_of_business\":null,\"amendment_from\":null,\"amendment_to\":null,\"tax_incentive\":false,\"business_organization\":null,\"business_area_type\":null,\"business_scale\":null,\"business_sector\":null,\"zone\":null,\"occupancy\":null,\"business_area_sqm\":null,\"total_employees\":null,\"employees_lgu\":null,\"business_region\":null,\"business_province\":null,\"business_municipality\":null,\"business_barangay\":null,\"business_street\":null,\"status\":\"pending\",\"permit_year\":2026,\"renewal_cycle\":0,\"updated_at\":\"2026-02-23 08:20:39\",\"created_at\":\"2026-02-23 08:20:39\",\"id\":13}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', 'http://127.0.0.1:8000/portal/apply', 'POST', 'success', NULL, '2026-02-23 00:20:39', '2026-02-23 00:20:39'),
(3, NULL, 'System', 'BusinessEntry', 'created', 'BusinessEntry \"asd\" was created.', 'App\\Models\\BusinessEntry', 14, NULL, '{\"last_name\":\"asd\",\"first_name\":\"asd\",\"middle_name\":\"asd\",\"citizenship\":\"Foreign National\",\"civil_status\":\"Widowed\",\"gender\":null,\"birthdate\":null,\"mobile_no\":null,\"email\":null,\"is_pwd\":false,\"is_4ps\":false,\"is_solo_parent\":false,\"is_senior\":false,\"discount_10\":false,\"discount_5\":false,\"owner_region\":null,\"owner_province\":null,\"owner_municipality\":null,\"owner_barangay\":null,\"owner_street\":null,\"emergency_contact_person\":null,\"emergency_mobile\":null,\"emergency_email\":null,\"business_name\":\"asd\",\"trade_name\":null,\"date_of_application\":\"2026-02-23 00:00:00\",\"tin_no\":null,\"dti_sec_cda_no\":null,\"dti_sec_cda_date\":null,\"business_mobile\":null,\"business_email\":null,\"type_of_business\":null,\"amendment_from\":null,\"amendment_to\":null,\"tax_incentive\":false,\"business_organization\":null,\"business_area_type\":null,\"business_scale\":null,\"business_sector\":null,\"zone\":null,\"occupancy\":null,\"business_area_sqm\":null,\"total_employees\":null,\"employees_lgu\":null,\"business_region\":null,\"business_province\":null,\"business_municipality\":null,\"business_barangay\":null,\"business_street\":null,\"status\":\"pending\",\"permit_year\":2026,\"renewal_cycle\":0,\"updated_at\":\"2026-02-23 09:07:24\",\"created_at\":\"2026-02-23 09:07:24\",\"id\":14}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', 'http://127.0.0.1:8000/portal/apply', 'POST', 'success', NULL, '2026-02-23 01:07:24', '2026-02-23 01:07:24'),
(4, 2, 'sample', 'OrAssignment', 'created', 'OrAssignment #2 was created.', 'App\\Models\\OrAssignment', 2, NULL, '{\"start_or\":\"123451\",\"end_or\":\"123500\",\"user_id\":2,\"cashier_name\":\"sample\",\"receipt_type\":\"51C\",\"updated_at\":\"2026-02-24 03:32:01\",\"created_at\":\"2026-02-24 03:32:01\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/settings/or-assignments', 'POST', 'success', NULL, '2026-02-23 19:32:01', '2026-02-23 19:32:01'),
(5, 3, 'treasury', 'OrAssignment', 'deleted', 'OrAssignment #1 was deleted.', 'App\\Models\\OrAssignment', 1, '{\"id\":1,\"start_or\":\"123400\",\"end_or\":\"123450\",\"receipt_type\":\"51C\",\"user_id\":2,\"cashier_name\":\"samples\",\"created_at\":null,\"updated_at\":\"2026-02-24 03:32:52\",\"deleted_at\":\"2026-02-24 03:32:52\"}', NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/settings/or-assignments/1', 'DELETE', 'success', NULL, '2026-02-23 19:32:52', '2026-02-23 19:32:52'),
(6, 3, 'treasury', 'OrAssignment', 'created', 'OrAssignment #3 was created.', 'App\\Models\\OrAssignment', 3, NULL, '{\"start_or\":\"123400\",\"end_or\":\"123450\",\"user_id\":3,\"cashier_name\":\"treasury\",\"receipt_type\":\"51C\",\"updated_at\":\"2026-02-24 03:33:24\",\"created_at\":\"2026-02-24 03:33:24\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/settings/or-assignments', 'POST', 'success', NULL, '2026-02-23 19:33:24', '2026-02-23 19:33:24'),
(7, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"asd\" was updated.', 'App\\Models\\BusinessEntry', 14, '{\"status\":\"pending\",\"updated_at\":\"2026-02-23T09:07:24.000000Z\"}', '{\"status\":\"approved\",\"updated_at\":\"2026-02-24 04:20:58\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-list/14/change-status', 'POST', 'success', NULL, '2026-02-23 20:20:58', '2026-02-23 20:20:58'),
(8, 2, 'sample', 'BusinessEntry', 'created', 'BusinessEntry \"qwe\" was created.', 'App\\Models\\BusinessEntry', 15, NULL, '{\"last_name\":\"qwe\",\"first_name\":\"qwe\",\"middle_name\":\"qwe\",\"citizenship\":null,\"civil_status\":null,\"gender\":null,\"birthdate\":null,\"mobile_no\":null,\"email\":null,\"is_pwd\":false,\"is_4ps\":false,\"is_solo_parent\":false,\"is_senior\":false,\"discount_10\":false,\"discount_5\":false,\"owner_region\":null,\"owner_province\":null,\"owner_municipality\":null,\"owner_barangay\":null,\"owner_street\":null,\"emergency_contact_person\":null,\"emergency_mobile\":null,\"emergency_email\":null,\"business_name\":\"qwe\",\"trade_name\":null,\"date_of_application\":\"2026-02-24 00:00:00\",\"tin_no\":null,\"dti_sec_cda_no\":null,\"dti_sec_cda_date\":null,\"business_mobile\":null,\"business_email\":null,\"type_of_business\":null,\"amendment_from\":null,\"amendment_to\":null,\"tax_incentive\":false,\"business_organization\":null,\"business_area_type\":null,\"business_scale\":null,\"business_sector\":null,\"zone\":null,\"occupancy\":null,\"business_area_sqm\":null,\"total_employees\":null,\"employees_lgu\":null,\"business_region\":null,\"business_province\":null,\"business_municipality\":null,\"business_barangay\":null,\"business_street\":null,\"status\":\"pending\",\"updated_at\":\"2026-02-24 05:06:46\",\"created_at\":\"2026-02-24 05:06:46\",\"id\":15}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-entries', 'POST', 'success', NULL, '2026-02-23 21:06:46', '2026-02-23 21:06:46'),
(9, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"qwe\" was updated.', 'App\\Models\\BusinessEntry', 15, '{\"status\":\"pending\",\"updated_at\":\"2026-02-24T05:06:46.000000Z\"}', '{\"status\":\"approved\",\"updated_at\":\"2026-02-24 05:30:47\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-list/15/change-status', 'POST', 'success', NULL, '2026-02-23 21:30:47', '2026-02-23 21:30:47'),
(10, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"qwe\" was updated.', 'App\\Models\\BusinessEntry', 15, '{\"status\":\"approved\",\"updated_at\":\"2026-02-24T05:30:47.000000Z\"}', '{\"status\":\"for_payment\",\"updated_at\":\"2026-02-24 05:46:25\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-list/15/change-status', 'POST', 'success', NULL, '2026-02-23 21:46:25', '2026-02-23 21:46:25'),
(11, NULL, 'System', 'BusinessEntry', 'created', 'BusinessEntry \"QWEWQEQWE\" was created.', 'App\\Models\\BusinessEntry', 16, NULL, '{\"last_name\":\"QWEEWQEQWE\",\"first_name\":\"QWEQWEQWEQW\",\"middle_name\":\"QWEQWEWQE\",\"citizenship\":null,\"civil_status\":null,\"gender\":null,\"birthdate\":null,\"mobile_no\":null,\"email\":null,\"is_pwd\":false,\"is_4ps\":false,\"is_solo_parent\":false,\"is_senior\":false,\"discount_10\":false,\"discount_5\":false,\"owner_region\":null,\"owner_province\":null,\"owner_municipality\":null,\"owner_barangay\":null,\"owner_street\":null,\"emergency_contact_person\":null,\"emergency_mobile\":null,\"emergency_email\":null,\"business_name\":\"QWEWQEQWE\",\"trade_name\":null,\"date_of_application\":\"2026-02-24 00:00:00\",\"tin_no\":null,\"dti_sec_cda_no\":null,\"dti_sec_cda_date\":null,\"business_mobile\":null,\"business_email\":null,\"type_of_business\":null,\"amendment_from\":null,\"amendment_to\":null,\"tax_incentive\":false,\"business_organization\":null,\"business_area_type\":null,\"business_scale\":null,\"business_sector\":null,\"zone\":null,\"occupancy\":null,\"business_area_sqm\":null,\"total_employees\":null,\"employees_lgu\":null,\"business_region\":null,\"business_province\":null,\"business_municipality\":null,\"business_barangay\":null,\"business_street\":null,\"status\":\"pending\",\"permit_year\":2026,\"renewal_cycle\":0,\"updated_at\":\"2026-02-24 07:13:03\",\"created_at\":\"2026-02-24 07:13:03\",\"id\":16}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', 'http://127.0.0.1:8000/portal/apply', 'POST', 'success', NULL, '2026-02-23 23:13:03', '2026-02-23 23:13:03'),
(12, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"qwe\" was updated.', 'App\\Models\\BusinessEntry', 15, '{\"status\":\"for_payment\",\"updated_at\":\"2026-02-24T05:46:25.000000Z\"}', '{\"status\":\"pending\",\"updated_at\":\"2026-02-24 08:08:04\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-list/15/change-status', 'POST', 'success', NULL, '2026-02-24 00:08:04', '2026-02-24 00:08:04'),
(13, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"qwe\" was updated.', 'App\\Models\\BusinessEntry', 15, '{\"business_nature\":null,\"business_scale\":null,\"capital_investment\":null,\"mode_of_payment\":null,\"total_due\":null,\"approved_at\":null,\"status\":\"pending\",\"permit_year\":null,\"updated_at\":\"2026-02-24T08:08:04.000000Z\"}', '{\"business_nature\":\"Trading\",\"business_scale\":\"Micro (Assets up to P3M)\",\"capital_investment\":\"10000\",\"mode_of_payment\":\"quarterly\",\"total_due\":2030,\"approved_at\":\"2026-02-24 08:09:26\",\"status\":\"for_payment\",\"permit_year\":2026,\"updated_at\":\"2026-02-24 08:09:26\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-list/15/approve-payment', 'POST', 'success', NULL, '2026-02-24 00:09:26', '2026-02-24 00:09:26'),
(14, NULL, 'System', 'BusinessEntry', 'created', 'BusinessEntry \"John\" was created.', 'App\\Models\\BusinessEntry', 17, NULL, '{\"last_name\":\"John\",\"first_name\":\"Juan\",\"middle_name\":null,\"citizenship\":\"Filipino\",\"civil_status\":\"Single\",\"gender\":\"Male\",\"birthdate\":null,\"mobile_no\":\"0987654321\",\"email\":null,\"is_pwd\":false,\"is_4ps\":false,\"is_solo_parent\":false,\"is_senior\":false,\"discount_10\":false,\"discount_5\":false,\"owner_region\":\"Region IV-A (CALABARZON)\",\"owner_province\":\"Laguna\",\"owner_municipality\":\"City of Bi\\u00f1an\",\"owner_barangay\":\"Bi\\u00f1an\",\"owner_street\":\"Purok 1\",\"emergency_contact_person\":\"gerr\",\"emergency_mobile\":null,\"emergency_email\":null,\"business_name\":\"John\",\"trade_name\":null,\"date_of_application\":\"2026-02-25 00:00:00\",\"tin_no\":null,\"dti_sec_cda_no\":null,\"dti_sec_cda_date\":null,\"business_mobile\":null,\"business_email\":null,\"type_of_business\":\"Sole Proprietorship\",\"amendment_from\":null,\"amendment_to\":null,\"tax_incentive\":false,\"business_organization\":null,\"business_area_type\":null,\"business_scale\":null,\"business_sector\":null,\"zone\":null,\"occupancy\":null,\"business_area_sqm\":null,\"total_employees\":null,\"employees_lgu\":null,\"business_region\":\"Region IV-A (CALABARZON)\",\"business_province\":\"Laguna\",\"business_municipality\":\"City of Bi\\u00f1an\",\"business_barangay\":\"Langkiwa\",\"business_street\":null,\"status\":\"pending\",\"permit_year\":2026,\"renewal_cycle\":0,\"updated_at\":\"2026-02-25 02:52:39\",\"created_at\":\"2026-02-25 02:52:39\",\"id\":17}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', 'http://127.0.0.1:8000/portal/apply', 'POST', 'success', NULL, '2026-02-24 18:52:39', '2026-02-24 18:52:39'),
(15, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"John\" was updated.', 'App\\Models\\BusinessEntry', 17, '{\"business_scale\":null,\"capital_investment\":null,\"mode_of_payment\":null,\"updated_at\":\"2026-02-25T02:52:39.000000Z\"}', '{\"business_scale\":\"Micro (Assets up to P3M)\",\"capital_investment\":\"10000\",\"mode_of_payment\":\"semi_annual\",\"updated_at\":\"2026-02-25 03:23:25\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-list/17/assess', 'POST', 'success', NULL, '2026-02-24 19:23:25', '2026-02-24 19:23:25'),
(16, 2, 'sample', 'BusinessEntry', 'created', 'BusinessEntry \"shabushabu\" was created.', 'App\\Models\\BusinessEntry', 18, NULL, '{\"last_name\":\"QWERTY\",\"first_name\":\"UIOP\",\"middle_name\":\"POIUY\",\"citizenship\":\"Foreign National\",\"civil_status\":\"Married\",\"gender\":\"Female\",\"birthdate\":\"2026-02-25 00:00:00\",\"mobile_no\":null,\"email\":null,\"is_pwd\":false,\"is_4ps\":false,\"is_solo_parent\":false,\"is_senior\":false,\"discount_10\":false,\"discount_5\":false,\"owner_region\":null,\"owner_province\":null,\"owner_municipality\":null,\"owner_barangay\":null,\"owner_street\":\"Philippines\",\"emergency_contact_person\":null,\"emergency_mobile\":null,\"emergency_email\":null,\"business_name\":\"shabushabu\",\"trade_name\":null,\"date_of_application\":\"2026-02-25 00:00:00\",\"tin_no\":null,\"dti_sec_cda_no\":null,\"dti_sec_cda_date\":null,\"business_mobile\":null,\"business_email\":null,\"type_of_business\":null,\"amendment_from\":null,\"amendment_to\":null,\"tax_incentive\":false,\"business_organization\":null,\"business_area_type\":null,\"business_scale\":null,\"business_sector\":null,\"zone\":null,\"occupancy\":null,\"business_area_sqm\":null,\"total_employees\":null,\"employees_lgu\":null,\"business_region\":\"2\",\"business_province\":null,\"business_municipality\":null,\"business_barangay\":null,\"business_street\":null,\"status\":\"pending\",\"updated_at\":\"2026-02-25 03:25:17\",\"created_at\":\"2026-02-25 03:25:17\",\"id\":18}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-entries', 'POST', 'success', NULL, '2026-02-24 19:25:17', '2026-02-24 19:25:17'),
(17, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"shabushabu\" was updated.', 'App\\Models\\BusinessEntry', 18, '{\"business_nature\":null,\"business_scale\":null,\"capital_investment\":null,\"mode_of_payment\":null,\"updated_at\":\"2026-02-25T03:25:17.000000Z\"}', '{\"business_nature\":\"Trading\",\"business_scale\":\"Small (P3M - P15M)\",\"capital_investment\":\"1000000\",\"mode_of_payment\":\"semi_annual\",\"updated_at\":\"2026-02-25 03:29:07\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-list/18/assess', 'POST', 'success', NULL, '2026-02-24 19:29:07', '2026-02-24 19:29:07'),
(18, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"shabushabu\" was updated.', 'App\\Models\\BusinessEntry', 18, '{\"business_scale\":\"Small (P3M - P15M)\",\"updated_at\":\"2026-02-25T03:29:07.000000Z\"}', '{\"business_scale\":\"Medium (P15M - P100M)\",\"updated_at\":\"2026-02-25 03:30:18\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-list/18/assess', 'POST', 'success', NULL, '2026-02-24 19:30:18', '2026-02-24 19:30:18'),
(19, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"QWEWQEQWE\" was updated.', 'App\\Models\\BusinessEntry', 16, '{\"business_nature\":null,\"business_scale\":null,\"capital_investment\":null,\"mode_of_payment\":null,\"total_due\":null,\"approved_at\":null,\"status\":\"pending\",\"updated_at\":\"2026-02-24T07:13:03.000000Z\"}', '{\"business_nature\":\"Trading\",\"business_scale\":\"Large (Above P100M)\",\"capital_investment\":\"10000\",\"mode_of_payment\":\"semi_annual\",\"total_due\":4780,\"approved_at\":\"2026-02-25 03:45:53\",\"status\":\"for_payment\",\"updated_at\":\"2026-02-25 03:45:53\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-list/16/approve-payment', 'POST', 'success', NULL, '2026-02-24 19:45:53', '2026-02-24 19:45:53'),
(20, 2, 'sample', 'BplsPayment', 'created', 'BplsPayment #1 was created.', 'App\\Models\\BplsPayment', 1, NULL, '{\"business_entry_id\":16,\"payment_year\":2026,\"renewal_cycle\":0,\"or_number\":\"123451\",\"payment_date\":\"2026-02-25 00:00:00\",\"quarters_paid\":\"\\\"[1]\\\"\",\"amount_paid\":2390,\"surcharges\":0,\"backtaxes\":0,\"discount\":0,\"total_collected\":2390,\"payment_method\":\"cash\",\"drawee_bank\":null,\"check_number\":null,\"check_date\":null,\"fund_code\":\"100\",\"payor\":\"QWEEWQEQWE, QWEQWEQWEQW QWEQWEWQE\",\"remarks\":\"\",\"received_by\":\"sample\",\"updated_at\":\"2026-02-25 03:46:11\",\"created_at\":\"2026-02-25 03:46:11\",\"id\":1}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/payment/16/pay', 'POST', 'success', NULL, '2026-02-24 19:46:11', '2026-02-24 19:46:11'),
(21, 2, 'sample', 'BplsPayment', 'created', 'BplsPayment #2 was created.', 'App\\Models\\BplsPayment', 2, NULL, '{\"business_entry_id\":16,\"payment_year\":2026,\"renewal_cycle\":0,\"or_number\":\"123452\",\"payment_date\":\"2026-02-25 00:00:00\",\"quarters_paid\":\"\\\"[2]\\\"\",\"amount_paid\":2390,\"surcharges\":0,\"backtaxes\":0,\"discount\":239,\"total_collected\":2151,\"payment_method\":\"cash\",\"drawee_bank\":null,\"check_number\":null,\"check_date\":null,\"fund_code\":\"100\",\"payor\":\"QWEEWQEQWE, QWEQWEQWEQW QWEQWEWQE\",\"remarks\":\"\",\"received_by\":\"sample\",\"updated_at\":\"2026-02-25 03:46:56\",\"created_at\":\"2026-02-25 03:46:56\",\"id\":2}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/payment/16/pay', 'POST', 'success', NULL, '2026-02-24 19:46:56', '2026-02-24 19:46:56'),
(22, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"QWEWQEQWE\" was updated.', 'App\\Models\\BusinessEntry', 16, '{\"status\":\"for_payment\",\"updated_at\":\"2026-02-25T03:45:53.000000Z\"}', '{\"status\":\"approved\",\"updated_at\":\"2026-02-25 03:46:56\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/payment/16/pay', 'POST', 'success', NULL, '2026-02-24 19:46:56', '2026-02-24 19:46:56'),
(23, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"QWEWQEQWE\" was updated.', 'App\\Models\\BusinessEntry', 16, '{\"status\":\"approved\",\"renewal_cycle\":0,\"last_renewed_at\":null,\"updated_at\":\"2026-02-25T03:46:56.000000Z\"}', '{\"status\":\"completed\",\"renewal_cycle\":1,\"last_renewed_at\":\"2026-02-25 11:47:20\",\"updated_at\":\"2026-02-25 03:47:20\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-list/16/change-status', 'POST', 'success', NULL, '2026-02-24 19:47:20', '2026-02-24 19:47:20'),
(24, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"QWEWQEQWE\" was updated.', 'App\\Models\\BusinessEntry', 16, '{\"approved_at\":\"2026-02-25T03:45:53.000000Z\",\"status\":\"completed\",\"updated_at\":\"2026-02-25T03:47:20.000000Z\"}', '{\"approved_at\":\"2026-02-25 03:47:29\",\"status\":\"for_payment\",\"updated_at\":\"2026-02-25 03:47:29\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-list/16/approve-payment', 'POST', 'success', NULL, '2026-02-24 19:47:29', '2026-02-24 19:47:29'),
(25, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"QWEWQEQWE\" was updated.', 'App\\Models\\BusinessEntry', 16, '{\"permit_year\":2026,\"updated_at\":\"2026-02-25T03:47:29.000000Z\"}', '{\"permit_year\":2027,\"updated_at\":\"2026-02-25 03:47:30\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/payment/16', 'GET', 'success', NULL, '2026-02-24 19:47:30', '2026-02-24 19:47:30'),
(26, 2, 'sample', 'BplsPayment', 'created', 'BplsPayment #3 was created.', 'App\\Models\\BplsPayment', 3, NULL, '{\"business_entry_id\":16,\"payment_year\":2027,\"renewal_cycle\":1,\"or_number\":\"123453\",\"payment_date\":\"2026-02-25 00:00:00\",\"quarters_paid\":\"\\\"[1]\\\"\",\"amount_paid\":2390,\"surcharges\":0,\"backtaxes\":0,\"discount\":239,\"total_collected\":2151,\"payment_method\":\"cash\",\"drawee_bank\":null,\"check_number\":null,\"check_date\":null,\"fund_code\":\"100\",\"payor\":\"QWEEWQEQWE, QWEQWEQWEQW QWEQWEWQE\",\"remarks\":\"\",\"received_by\":\"sample\",\"updated_at\":\"2026-02-25 03:47:39\",\"created_at\":\"2026-02-25 03:47:39\",\"id\":3}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/payment/16/pay', 'POST', 'success', NULL, '2026-02-24 19:47:39', '2026-02-24 19:47:39'),
(27, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"QWEWQEQWE\" was updated.', 'App\\Models\\BusinessEntry', 16, '{\"status\":\"for_payment\",\"updated_at\":\"2026-02-25T03:47:30.000000Z\"}', '{\"status\":\"for_renewal_payment\",\"updated_at\":\"2026-02-25 03:47:39\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/payment/16/pay', 'POST', 'success', NULL, '2026-02-24 19:47:39', '2026-02-24 19:47:39'),
(28, 2, 'sample', 'BplsPayment', 'created', 'BplsPayment #4 was created.', 'App\\Models\\BplsPayment', 4, NULL, '{\"business_entry_id\":16,\"payment_year\":2027,\"renewal_cycle\":1,\"or_number\":\"123454\",\"payment_date\":\"2027-06-15 00:00:00\",\"quarters_paid\":\"\\\"[2]\\\"\",\"amount_paid\":2390,\"surcharges\":47.8,\"backtaxes\":0,\"discount\":0,\"total_collected\":2437.8,\"payment_method\":\"cash\",\"drawee_bank\":null,\"check_number\":null,\"check_date\":null,\"fund_code\":\"100\",\"payor\":\"QWEEWQEQWE, QWEQWEQWEQW QWEQWEWQE\",\"remarks\":\"\",\"received_by\":\"sample\",\"updated_at\":\"2026-02-25 03:48:56\",\"created_at\":\"2026-02-25 03:48:56\",\"id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/payment/16/pay', 'POST', 'success', NULL, '2026-02-24 19:48:56', '2026-02-24 19:48:56'),
(29, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"QWEWQEQWE\" was updated.', 'App\\Models\\BusinessEntry', 16, '{\"status\":\"for_renewal_payment\",\"updated_at\":\"2026-02-25T03:47:39.000000Z\"}', '{\"status\":\"approved\",\"updated_at\":\"2026-02-25 03:48:56\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/payment/16/pay', 'POST', 'success', NULL, '2026-02-24 19:48:56', '2026-02-24 19:48:56'),
(30, NULL, 'System', 'BusinessEntry', 'created', 'BusinessEntry \"QWEWQEQWE\" was created.', 'App\\Models\\BusinessEntry', 19, NULL, '{\"last_name\":\"QWEEWQEQWE\",\"first_name\":\"QWEQWEQWEQW\",\"middle_name\":\"QWEQWEWQE\",\"citizenship\":null,\"civil_status\":null,\"gender\":null,\"birthdate\":null,\"mobile_no\":null,\"email\":null,\"is_pwd\":false,\"is_4ps\":false,\"is_solo_parent\":false,\"is_senior\":false,\"discount_10\":false,\"discount_5\":false,\"owner_region\":null,\"owner_province\":null,\"owner_municipality\":null,\"owner_barangay\":null,\"owner_street\":null,\"emergency_contact_person\":null,\"emergency_mobile\":null,\"emergency_email\":null,\"business_name\":\"QWEWQEQWE\",\"trade_name\":null,\"date_of_application\":\"2026-02-24 00:00:00\",\"tin_no\":null,\"dti_sec_cda_no\":null,\"dti_sec_cda_date\":null,\"business_mobile\":null,\"business_email\":null,\"type_of_business\":null,\"amendment_from\":null,\"amendment_to\":null,\"tax_incentive\":false,\"business_organization\":null,\"business_area_type\":null,\"business_scale\":null,\"business_sector\":null,\"zone\":null,\"occupancy\":null,\"business_area_sqm\":null,\"total_employees\":null,\"employees_lgu\":null,\"business_region\":null,\"business_province\":null,\"business_municipality\":null,\"business_barangay\":null,\"business_street\":null,\"status\":\"pending\",\"permit_year\":2026,\"renewal_cycle\":0,\"updated_at\":\"2026-02-25 04:06:19\",\"created_at\":\"2026-02-25 04:06:19\",\"id\":19}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', 'http://127.0.0.1:8000/portal/apply', 'POST', 'success', NULL, '2026-02-24 20:06:19', '2026-02-24 20:06:19'),
(31, NULL, 'System', 'BusinessEntry', 'updated', 'BusinessEntry \"rarer\" was updated.', 'App\\Models\\BusinessEntry', 1, '{\"status\":\"pending\",\"updated_at\":\"2026-02-21T05:56:18.000000Z\"}', '{\"status\":\"approved\",\"updated_at\":\"2026-02-25 06:12:52\"}', '127.0.0.1', 'Symfony', 'http://localhost', 'GET', 'success', NULL, '2026-02-24 22:12:52', '2026-02-24 22:12:52'),
(32, NULL, 'System', 'BusinessEntry', 'updated', 'BusinessEntry \"rarer\" was updated.', 'App\\Models\\BusinessEntry', 3, '{\"status\":\"pending\",\"updated_at\":\"2026-02-21T06:26:02.000000Z\"}', '{\"status\":\"approved\",\"updated_at\":\"2026-02-25 06:12:52\"}', '127.0.0.1', 'Symfony', 'http://localhost', 'GET', 'success', NULL, '2026-02-24 22:12:52', '2026-02-24 22:12:52'),
(33, NULL, 'System', 'BusinessEntry', 'updated', 'BusinessEntry \"tata\" was updated.', 'App\\Models\\BusinessEntry', 4, '{\"status\":\"completed\",\"updated_at\":\"2026-02-21T08:13:31.000000Z\"}', '{\"status\":\"approved\",\"updated_at\":\"2026-02-25 06:12:52\"}', '127.0.0.1', 'Symfony', 'http://localhost', 'GET', 'success', NULL, '2026-02-24 22:12:52', '2026-02-24 22:12:52'),
(34, NULL, 'System', 'BusinessEntry', 'updated', 'BusinessEntry \"qwe\" was updated.', 'App\\Models\\BusinessEntry', 5, '{\"status\":\"pending\",\"updated_at\":\"2026-02-21T08:34:13.000000Z\"}', '{\"status\":\"approved\",\"updated_at\":\"2026-02-25 06:12:52\"}', '127.0.0.1', 'Symfony', 'http://localhost', 'GET', 'success', NULL, '2026-02-24 22:12:52', '2026-02-24 22:12:52'),
(35, NULL, 'System', 'BusinessEntry', 'updated', 'BusinessEntry \"QWEEQEQWE\" was updated.', 'App\\Models\\BusinessEntry', 6, '{\"status\":\"pending\",\"updated_at\":\"2026-02-22T08:45:47.000000Z\"}', '{\"status\":\"approved\",\"updated_at\":\"2026-02-25 06:12:52\"}', '127.0.0.1', 'Symfony', 'http://localhost', 'GET', 'success', NULL, '2026-02-24 22:12:52', '2026-02-24 22:12:52'),
(36, NULL, 'System', 'BusinessEntry', 'updated', 'BusinessEntry \"qwewqe\" was updated.', 'App\\Models\\BusinessEntry', 7, '{\"status\":\"pending\",\"updated_at\":\"2026-02-22T08:52:35.000000Z\"}', '{\"status\":\"rejected\",\"updated_at\":\"2026-02-25 06:12:52\"}', '127.0.0.1', 'Symfony', 'http://localhost', 'GET', 'success', NULL, '2026-02-24 22:12:53', '2026-02-24 22:12:53'),
(37, NULL, 'System', 'BusinessEntry', 'updated', 'BusinessEntry \"Jojo\" was updated.', 'App\\Models\\BusinessEntry', 8, '{\"status\":\"pending\",\"updated_at\":\"2026-02-23T01:28:27.000000Z\"}', '{\"status\":\"approved\",\"updated_at\":\"2026-02-25 06:12:53\"}', '127.0.0.1', 'Symfony', 'http://localhost', 'GET', 'success', NULL, '2026-02-24 22:12:53', '2026-02-24 22:12:53'),
(38, NULL, 'System', 'BusinessEntry', 'updated', 'BusinessEntry \"asd\" was updated.', 'App\\Models\\BusinessEntry', 9, '{\"status\":\"pending\",\"updated_at\":\"2026-02-23T03:09:45.000000Z\"}', '{\"status\":\"rejected\",\"updated_at\":\"2026-02-25 06:12:53\"}', '127.0.0.1', 'Symfony', 'http://localhost', 'GET', 'success', NULL, '2026-02-24 22:12:53', '2026-02-24 22:12:53'),
(39, NULL, 'System', 'BusinessEntry', 'updated', 'BusinessEntry \"asd\" was updated.', 'App\\Models\\BusinessEntry', 10, '{\"status\":\"pending\",\"updated_at\":\"2026-02-23T03:59:50.000000Z\"}', '{\"status\":\"approved\",\"updated_at\":\"2026-02-25 06:12:53\"}', '127.0.0.1', 'Symfony', 'http://localhost', 'GET', 'success', NULL, '2026-02-24 22:12:53', '2026-02-24 22:12:53'),
(40, NULL, 'System', 'BusinessEntry', 'updated', 'BusinessEntry \"dadad\" was updated.', 'App\\Models\\BusinessEntry', 11, '{\"status\":\"pending\",\"updated_at\":\"2026-02-23T05:45:24.000000Z\"}', '{\"status\":\"approved\",\"updated_at\":\"2026-02-25 06:12:53\"}', '127.0.0.1', 'Symfony', 'http://localhost', 'GET', 'success', NULL, '2026-02-24 22:12:53', '2026-02-24 22:12:53'),
(41, NULL, 'System', 'BusinessEntry', 'updated', 'BusinessEntry \"dadad\" was updated.', 'App\\Models\\BusinessEntry', 12, '{\"status\":\"pending\",\"updated_at\":\"2026-02-23T07:46:04.000000Z\"}', '{\"status\":\"approved\",\"updated_at\":\"2026-02-25 06:12:53\"}', '127.0.0.1', 'Symfony', 'http://localhost', 'GET', 'success', NULL, '2026-02-24 22:12:53', '2026-02-24 22:12:53'),
(42, NULL, 'System', 'BusinessEntry', 'updated', 'BusinessEntry \"qwe\" was updated.', 'App\\Models\\BusinessEntry', 13, '{\"status\":\"pending\",\"updated_at\":\"2026-02-23T08:20:39.000000Z\"}', '{\"status\":\"approved\",\"updated_at\":\"2026-02-25 06:12:53\"}', '127.0.0.1', 'Symfony', 'http://localhost', 'GET', 'success', NULL, '2026-02-24 22:12:53', '2026-02-24 22:12:53'),
(43, NULL, 'System', 'BusinessEntry', 'updated', 'BusinessEntry \"asd\" was updated.', 'App\\Models\\BusinessEntry', 14, '{\"status\":\"pending\",\"updated_at\":\"2026-02-24T04:20:58.000000Z\"}', '{\"status\":\"approved\",\"updated_at\":\"2026-02-25 06:12:53\"}', '127.0.0.1', 'Symfony', 'http://localhost', 'GET', 'success', NULL, '2026-02-24 22:12:53', '2026-02-24 22:12:53'),
(44, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"asd\" was updated.', 'App\\Models\\BusinessEntry', 14, '{\"status\":\"approved\",\"updated_at\":\"2026-02-25T06:12:53.000000Z\"}', '{\"status\":\"for_payment\",\"updated_at\":\"2026-02-25 06:32:19\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-list/14/change-status', 'POST', 'success', NULL, '2026-02-24 22:32:19', '2026-02-24 22:32:19'),
(45, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"QWEWQEQWE\" was updated.', 'App\\Models\\BusinessEntry', 19, '{\"status\":\"pending\",\"updated_at\":\"2026-02-25T04:06:19.000000Z\"}', '{\"status\":\"for_payment\",\"updated_at\":\"2026-02-25 06:33:07\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/online/application/17/assess', 'POST', 'success', NULL, '2026-02-24 22:33:07', '2026-02-24 22:33:07'),
(46, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"QWEWQEQWE\" was updated.', 'App\\Models\\BusinessEntry', 19, '{\"status\":\"for_payment\",\"updated_at\":\"2026-02-25T06:33:07.000000Z\"}', '{\"status\":\"approved\",\"updated_at\":\"2026-02-25 06:33:19\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/online/application/17/final-approve', 'POST', 'success', NULL, '2026-02-24 22:33:19', '2026-02-24 22:33:19'),
(47, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"shabushabu\" was updated.', 'App\\Models\\BusinessEntry', 18, '{\"total_due\":null,\"approved_at\":null,\"status\":\"pending\",\"permit_year\":null,\"updated_at\":\"2026-02-25T03:30:18.000000Z\"}', '{\"total_due\":20950,\"approved_at\":\"2026-02-25 07:57:48\",\"status\":\"for_payment\",\"permit_year\":2026,\"updated_at\":\"2026-02-25 07:57:48\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-list/18/approve-payment', 'POST', 'success', NULL, '2026-02-24 23:57:48', '2026-02-24 23:57:48'),
(48, 2, 'sample', 'BplsPayment', 'created', 'BplsPayment #5 was created.', 'App\\Models\\BplsPayment', 5, NULL, '{\"business_entry_id\":18,\"payment_year\":2026,\"renewal_cycle\":0,\"or_number\":\"123455\",\"payment_date\":\"2026-02-25 00:00:00\",\"quarters_paid\":\"\\\"[1]\\\"\",\"amount_paid\":10475,\"surcharges\":0,\"backtaxes\":0,\"discount\":0,\"total_collected\":10475,\"payment_method\":\"cash\",\"drawee_bank\":null,\"check_number\":null,\"check_date\":null,\"fund_code\":\"100\",\"payor\":\"QWERTY, UIOP POIUY\",\"remarks\":\"\",\"received_by\":\"sample\",\"updated_at\":\"2026-02-25 07:59:36\",\"created_at\":\"2026-02-25 07:59:36\",\"id\":5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/payment/18/pay', 'POST', 'success', NULL, '2026-02-24 23:59:36', '2026-02-24 23:59:36'),
(49, 2, 'sample', 'BplsPayment', 'created', 'BplsPayment #6 was created.', 'App\\Models\\BplsPayment', 6, NULL, '{\"business_entry_id\":18,\"payment_year\":2026,\"renewal_cycle\":0,\"or_number\":\"123456\",\"payment_date\":\"2026-02-25 00:00:00\",\"quarters_paid\":\"\\\"[2]\\\"\",\"amount_paid\":10475,\"surcharges\":0,\"backtaxes\":0,\"discount\":1047.5,\"total_collected\":9427.5,\"payment_method\":\"cash\",\"drawee_bank\":null,\"check_number\":null,\"check_date\":null,\"fund_code\":\"100\",\"payor\":\"QWERTY, UIOP POIUY\",\"remarks\":\"\",\"received_by\":\"sample\",\"updated_at\":\"2026-02-25 08:00:04\",\"created_at\":\"2026-02-25 08:00:04\",\"id\":6}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/payment/18/pay', 'POST', 'success', NULL, '2026-02-25 00:00:04', '2026-02-25 00:00:04'),
(50, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"shabushabu\" was updated.', 'App\\Models\\BusinessEntry', 18, '{\"status\":\"for_payment\",\"updated_at\":\"2026-02-25T07:57:48.000000Z\"}', '{\"status\":\"approved\",\"updated_at\":\"2026-02-25 08:00:04\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/payment/18/pay', 'POST', 'success', NULL, '2026-02-25 00:00:04', '2026-02-25 00:00:04'),
(51, 2, 'sample', 'BplsPayment', 'created', 'BplsPayment #7 was created.', 'App\\Models\\BplsPayment', 7, NULL, '{\"business_entry_id\":15,\"payment_year\":2026,\"renewal_cycle\":0,\"or_number\":\"123457\",\"payment_date\":\"2026-02-25 00:00:00\",\"quarters_paid\":\"\\\"[1,2,3,4]\\\"\",\"amount_paid\":2030,\"surcharges\":0,\"backtaxes\":0,\"discount\":76.14,\"total_collected\":1953.86,\"payment_method\":\"cash\",\"drawee_bank\":null,\"check_number\":null,\"check_date\":null,\"fund_code\":\"100\",\"payor\":\"QWE, QWE QWE\",\"remarks\":\"\",\"received_by\":\"sample\",\"updated_at\":\"2026-02-25 08:08:20\",\"created_at\":\"2026-02-25 08:08:20\",\"id\":7}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/payment/15/pay', 'POST', 'success', NULL, '2026-02-25 00:08:20', '2026-02-25 00:08:20'),
(52, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"qwe\" was updated.', 'App\\Models\\BusinessEntry', 15, '{\"status\":\"for_payment\",\"updated_at\":\"2026-02-24T08:09:26.000000Z\"}', '{\"status\":\"approved\",\"updated_at\":\"2026-02-25 08:08:20\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/payment/15/pay', 'POST', 'success', NULL, '2026-02-25 00:08:20', '2026-02-25 00:08:20'),
(53, NULL, 'System', 'BusinessEntry', 'created', 'BusinessEntry \"Pacpac\" was created.', 'App\\Models\\BusinessEntry', 20, NULL, '{\"last_name\":\"pacman\",\"first_name\":\"papacc\",\"middle_name\":\"pac\",\"citizenship\":\"Filipino\",\"civil_status\":\"Single\",\"gender\":\"Female\",\"birthdate\":null,\"mobile_no\":\"0987654321\",\"email\":null,\"is_pwd\":false,\"is_4ps\":false,\"is_solo_parent\":false,\"is_senior\":false,\"discount_10\":false,\"discount_5\":false,\"owner_region\":\"Region IV-A (CALABARZON)\",\"owner_province\":\"Laguna\",\"owner_municipality\":\"City of Bi\\u00f1an\",\"owner_barangay\":\"Bi\\u00f1an\",\"owner_street\":null,\"emergency_contact_person\":null,\"emergency_mobile\":null,\"emergency_email\":null,\"business_name\":\"Pacpac\",\"trade_name\":null,\"date_of_application\":\"2026-02-25 00:00:00\",\"tin_no\":null,\"dti_sec_cda_no\":null,\"dti_sec_cda_date\":null,\"business_mobile\":null,\"business_email\":null,\"type_of_business\":\"Partnership\",\"amendment_from\":null,\"amendment_to\":null,\"tax_incentive\":false,\"business_organization\":null,\"business_area_type\":null,\"business_scale\":null,\"business_sector\":null,\"zone\":null,\"occupancy\":null,\"business_area_sqm\":null,\"total_employees\":\"2\",\"employees_lgu\":null,\"business_region\":\"Region IV-A (CALABARZON)\",\"business_province\":\"Laguna\",\"business_municipality\":\"Los Ba\\u00f1os\",\"business_barangay\":\"Bagong Silang\",\"business_street\":null,\"status\":\"pending\",\"permit_year\":2026,\"renewal_cycle\":0,\"updated_at\":\"2026-02-25 09:21:55\",\"created_at\":\"2026-02-25 09:21:55\",\"id\":20}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', 'http://127.0.0.1:8000/portal/apply', 'POST', 'success', NULL, '2026-02-25 01:21:55', '2026-02-25 01:21:55'),
(54, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"qwe\" was updated.', 'App\\Models\\BusinessEntry', 15, '{\"status\":\"approved\",\"updated_at\":\"2026-02-25T08:08:20.000000Z\"}', '{\"status\":\"for_payment\",\"updated_at\":\"2026-02-25 09:25:03\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-list/15/change-status', 'POST', 'success', NULL, '2026-02-25 01:25:03', '2026-02-25 01:25:03'),
(55, 2, 'sample', 'BusinessEntry', 'created', 'BusinessEntry \"rarer\" was created.', 'App\\Models\\BusinessEntry', 21, NULL, '{\"last_name\":\"QWERTY\",\"first_name\":\"UIOP\",\"middle_name\":\"POIUY\",\"citizenship\":null,\"civil_status\":null,\"gender\":null,\"birthdate\":null,\"mobile_no\":null,\"email\":null,\"is_pwd\":false,\"is_4ps\":false,\"is_solo_parent\":false,\"is_senior\":false,\"discount_10\":false,\"discount_5\":false,\"owner_region\":null,\"owner_province\":null,\"owner_municipality\":null,\"owner_barangay\":null,\"owner_street\":\"Philippines\",\"emergency_contact_person\":null,\"emergency_mobile\":null,\"emergency_email\":null,\"business_name\":\"rarer\",\"trade_name\":null,\"date_of_application\":\"2026-02-25 00:00:00\",\"tin_no\":null,\"dti_sec_cda_no\":null,\"dti_sec_cda_date\":null,\"business_mobile\":null,\"business_email\":null,\"type_of_business\":null,\"amendment_from\":null,\"amendment_to\":null,\"tax_incentive\":false,\"business_organization\":null,\"business_area_type\":null,\"business_scale\":null,\"business_sector\":null,\"zone\":null,\"occupancy\":null,\"business_area_sqm\":null,\"total_employees\":null,\"employees_lgu\":null,\"business_region\":null,\"business_province\":null,\"business_municipality\":null,\"business_barangay\":null,\"business_street\":null,\"status\":\"pending\",\"updated_at\":\"2026-02-25 09:25:55\",\"created_at\":\"2026-02-25 09:25:55\",\"id\":21}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-entries', 'POST', 'success', NULL, '2026-02-25 01:25:55', '2026-02-25 01:25:55'),
(56, NULL, 'System', 'BusinessEntry', 'created', 'BusinessEntry \"ttsa\" was created.', 'App\\Models\\BusinessEntry', 22, NULL, '{\"last_name\":\"tata\",\"first_name\":\"tata\",\"middle_name\":null,\"citizenship\":\"Filipino\",\"civil_status\":\"Single\",\"gender\":\"Male\",\"birthdate\":null,\"mobile_no\":\"0987654321\",\"email\":null,\"is_pwd\":false,\"is_4ps\":false,\"is_solo_parent\":false,\"is_senior\":false,\"discount_10\":false,\"discount_5\":false,\"owner_region\":\"Region IV-A (CALABARZON)\",\"owner_province\":\"Laguna\",\"owner_municipality\":\"Magdalena\",\"owner_barangay\":\"Poblacion\",\"owner_street\":null,\"emergency_contact_person\":null,\"emergency_mobile\":null,\"emergency_email\":null,\"business_name\":\"ttsa\",\"trade_name\":null,\"date_of_application\":\"2026-02-25 00:00:00\",\"tin_no\":null,\"dti_sec_cda_no\":null,\"dti_sec_cda_date\":null,\"business_mobile\":null,\"business_email\":null,\"type_of_business\":\"Sole Proprietorship\",\"amendment_from\":null,\"amendment_to\":null,\"tax_incentive\":false,\"business_organization\":null,\"business_area_type\":null,\"business_scale\":null,\"business_sector\":null,\"zone\":null,\"occupancy\":null,\"business_area_sqm\":null,\"total_employees\":null,\"employees_lgu\":null,\"business_region\":\"Region IV-A (CALABARZON)\",\"business_province\":\"Laguna\",\"business_municipality\":\"Famy\",\"business_barangay\":\"Magdalo\",\"business_street\":null,\"status\":\"pending\",\"permit_year\":2026,\"renewal_cycle\":0,\"updated_at\":\"2026-02-25 09:38:01\",\"created_at\":\"2026-02-25 09:38:01\",\"id\":22}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:147.0) Gecko/20100101 Firefox/147.0', 'http://127.0.0.1:8000/portal/apply', 'POST', 'success', NULL, '2026-02-25 01:38:01', '2026-02-25 01:38:01'),
(57, 2, 'sample', 'OrAssignment', 'created', 'OrAssignment #4 was created.', 'App\\Models\\OrAssignment', 4, NULL, '{\"start_or\":\"123451\",\"end_or\":\"123500\",\"user_id\":2,\"cashier_name\":\"sample\",\"receipt_type\":\"RPTA\",\"updated_at\":\"2026-03-06 15:41:11\",\"created_at\":\"2026-03-06 15:41:11\",\"id\":4}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/settings/or-assignments', 'POST', 'success', NULL, '2026-03-06 07:41:11', '2026-03-06 07:41:11'),
(58, 2, 'sample', 'BusinessEntry', 'created', 'BusinessEntry \"shabushabu\" was created.', 'App\\Models\\BusinessEntry', 23, NULL, '{\"last_name\":\"QWERTY\",\"first_name\":\"UIOP\",\"middle_name\":\"POIUY\",\"citizenship\":\"Filipino\",\"civil_status\":\"Married\",\"gender\":\"Female\",\"birthdate\":null,\"mobile_no\":null,\"email\":null,\"owner_region\":null,\"owner_province\":null,\"owner_municipality\":null,\"owner_barangay\":null,\"owner_street\":\"Philippines\",\"emergency_contact_person\":null,\"emergency_mobile\":null,\"emergency_email\":null,\"business_name\":\"shabushabu\",\"trade_name\":null,\"date_of_application\":\"2026-03-09 00:00:00\",\"tin_no\":null,\"dti_sec_cda_no\":null,\"dti_sec_cda_date\":null,\"business_mobile\":null,\"business_email\":null,\"type_of_business\":null,\"amendment_from\":null,\"amendment_to\":null,\"tax_incentive\":false,\"business_organization\":null,\"business_area_type\":null,\"business_scale\":null,\"business_sector\":null,\"zone\":null,\"occupancy\":null,\"business_area_sqm\":null,\"total_employees\":null,\"employees_lgu\":null,\"business_region\":null,\"business_province\":null,\"business_municipality\":null,\"business_barangay\":null,\"business_street\":null,\"status\":\"pending\",\"updated_at\":\"2026-03-09 05:20:32\",\"created_at\":\"2026-03-09 05:20:32\",\"id\":23}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-entries', 'POST', 'success', NULL, '2026-03-08 21:20:32', '2026-03-08 21:20:32'),
(59, 2, 'sample', 'BusinessEntry', 'created', 'BusinessEntry \"busiñess\" was created.', 'App\\Models\\BusinessEntry', 24, NULL, '{\"last_name\":\"Santiago\",\"first_name\":\"Gerry\",\"middle_name\":null,\"citizenship\":\"Filipino\",\"civil_status\":\"Single\",\"gender\":null,\"birthdate\":null,\"mobile_no\":null,\"email\":\"santiagogerry79@gmail.com\",\"owner_region\":null,\"owner_province\":null,\"owner_municipality\":null,\"owner_barangay\":null,\"owner_street\":\"Victoria, Mallig, Isabela\",\"emergency_contact_person\":null,\"emergency_mobile\":null,\"emergency_email\":null,\"business_name\":\"busi\\u00f1ess\",\"trade_name\":null,\"date_of_application\":\"2026-03-09 00:00:00\",\"tin_no\":null,\"dti_sec_cda_no\":null,\"dti_sec_cda_date\":null,\"business_mobile\":null,\"business_email\":null,\"type_of_business\":\"Retail\",\"amendment_from\":null,\"amendment_to\":null,\"tax_incentive\":false,\"business_organization\":\"Partnership\",\"business_area_type\":\"Leased\",\"business_scale\":\"Small (P3M - P15M)\",\"business_sector\":\"Education\",\"zone\":\"Zone 2 - Industrial\",\"occupancy\":\"Ground Floor\",\"business_area_sqm\":\"2.00\",\"total_employees\":null,\"employees_lgu\":null,\"business_region\":null,\"business_province\":null,\"business_municipality\":null,\"business_barangay\":null,\"business_street\":null,\"status\":\"pending\",\"updated_at\":\"2026-03-09 05:21:42\",\"created_at\":\"2026-03-09 05:21:42\",\"id\":24}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-entries', 'POST', 'success', NULL, '2026-03-08 21:21:42', '2026-03-08 21:21:42');
INSERT INTO `audit_logs` (`id`, `user_id`, `user_name`, `module`, `action`, `description`, `model_type`, `model_id`, `old_values`, `new_values`, `ip_address`, `user_agent`, `url`, `method`, `status`, `extra`, `created_at`, `updated_at`) VALUES
(60, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"busiñess\" was updated.', 'App\\Models\\BusinessEntry', 24, '{\"business_nature\":null,\"capital_investment\":null,\"mode_of_payment\":null,\"total_due\":null,\"approved_at\":null,\"status\":\"pending\",\"permit_year\":null,\"updated_at\":\"2026-03-09T05:21:42.000000Z\"}', '{\"business_nature\":\"Eatery\",\"capital_investment\":\"10000\",\"mode_of_payment\":\"quarterly\",\"total_due\":2580,\"approved_at\":\"2026-03-09 14:03:43\",\"status\":\"for_payment\",\"permit_year\":2026,\"updated_at\":\"2026-03-09 06:03:43\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-list/24/approve-payment', 'POST', 'success', NULL, '2026-03-08 22:03:43', '2026-03-08 22:03:43'),
(61, 2, 'sample', 'OrAssignment', 'created', 'OrAssignment #5 was created.', 'App\\Models\\OrAssignment', 5, NULL, '{\"start_or\":\"123501\",\"end_or\":\"123550\",\"user_id\":2,\"cashier_name\":\"sample\",\"receipt_type\":\"51C\",\"updated_at\":\"2026-03-09 06:06:15\",\"created_at\":\"2026-03-09 06:06:15\",\"id\":5}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/settings/or-assignments', 'POST', 'success', NULL, '2026-03-08 22:06:15', '2026-03-08 22:06:15'),
(62, 2, 'sample', 'BplsPayment', 'created', 'BplsPayment #8 was created.', 'App\\Models\\BplsPayment', 8, NULL, '{\"payment_year\":2026,\"renewal_cycle\":0,\"or_number\":\"123501\",\"payment_date\":\"2026-03-09 00:00:00\",\"quarters_paid\":\"\\\"[1]\\\"\",\"amount_paid\":645,\"surcharges\":0,\"backtaxes\":0,\"discount\":0,\"total_collected\":645,\"payment_method\":\"cash\",\"drawee_bank\":null,\"check_number\":null,\"check_date\":null,\"fund_code\":\"100\",\"payor\":\"SANTIAGO, GERRY\",\"remarks\":\"\",\"received_by\":\"sample\",\"business_entry_id\":24,\"updated_at\":\"2026-03-09 06:24:45\",\"created_at\":\"2026-03-09 06:24:45\",\"id\":8}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/payment/24/pay', 'POST', 'success', NULL, '2026-03-08 22:24:45', '2026-03-08 22:24:45'),
(63, 2, 'sample', 'BplsPayment', 'created', 'BplsPayment #9 was created.', 'App\\Models\\BplsPayment', 9, NULL, '{\"payment_year\":2026,\"renewal_cycle\":0,\"or_number\":\"123502\",\"payment_date\":\"2026-03-09 00:00:00\",\"quarters_paid\":\"\\\"[2]\\\"\",\"amount_paid\":645,\"surcharges\":0,\"backtaxes\":0,\"discount\":32.25,\"total_collected\":612.75,\"payment_method\":\"cash\",\"drawee_bank\":null,\"check_number\":null,\"check_date\":null,\"fund_code\":\"100\",\"payor\":\"SANTIAGO, GERRY\",\"remarks\":\"\",\"received_by\":\"sample\",\"business_entry_id\":24,\"updated_at\":\"2026-03-09 06:26:22\",\"created_at\":\"2026-03-09 06:26:22\",\"id\":9}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/payment/24/pay', 'POST', 'success', NULL, '2026-03-08 22:26:22', '2026-03-08 22:26:22'),
(64, 2, 'sample', 'BplsPayment', 'created', 'BplsPayment #10 was created.', 'App\\Models\\BplsPayment', 10, NULL, '{\"payment_year\":2026,\"renewal_cycle\":0,\"or_number\":\"123503\",\"payment_date\":\"2026-03-09 00:00:00\",\"quarters_paid\":\"\\\"[3]\\\"\",\"amount_paid\":645,\"surcharges\":0,\"backtaxes\":0,\"discount\":32.25,\"total_collected\":612.75,\"payment_method\":\"cash\",\"drawee_bank\":null,\"check_number\":null,\"check_date\":null,\"fund_code\":\"100\",\"payor\":\"SANTIAGO, GERRY\",\"remarks\":\"\",\"received_by\":\"sample\",\"business_entry_id\":24,\"updated_at\":\"2026-03-09 06:30:15\",\"created_at\":\"2026-03-09 06:30:15\",\"id\":10}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/payment/24/pay', 'POST', 'success', NULL, '2026-03-08 22:30:15', '2026-03-08 22:30:15'),
(65, 2, 'sample', 'BplsPayment', 'created', 'BplsPayment #11 was created.', 'App\\Models\\BplsPayment', 11, NULL, '{\"payment_year\":2026,\"renewal_cycle\":0,\"or_number\":\"123504\",\"payment_date\":\"2026-03-09 00:00:00\",\"quarters_paid\":\"\\\"[4]\\\"\",\"amount_paid\":645,\"surcharges\":0,\"backtaxes\":0,\"discount\":32.25,\"total_collected\":612.75,\"payment_method\":\"cash\",\"drawee_bank\":null,\"check_number\":null,\"check_date\":null,\"fund_code\":\"100\",\"payor\":\"SANTIAGO, GERRY\",\"remarks\":\"\",\"received_by\":\"sample\",\"business_entry_id\":24,\"updated_at\":\"2026-03-09 06:31:17\",\"created_at\":\"2026-03-09 06:31:17\",\"id\":11}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/payment/24/pay', 'POST', 'success', NULL, '2026-03-08 22:31:17', '2026-03-08 22:31:17'),
(66, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"shabushabu\" was updated.', 'App\\Models\\BusinessEntry', 23, '{\"business_nature\":null,\"business_scale\":null,\"capital_investment\":null,\"mode_of_payment\":null,\"total_due\":null,\"approved_at\":null,\"status\":\"pending\",\"permit_year\":null,\"updated_at\":\"2026-03-09T05:20:32.000000Z\"}', '{\"business_nature\":\"Eatery\",\"business_scale\":\"Micro (Assets up to P3M)\",\"capital_investment\":\"10000\",\"mode_of_payment\":\"quarterly\",\"total_due\":2030,\"approved_at\":\"2026-03-09 14:32:24\",\"status\":\"for_payment\",\"permit_year\":2026,\"updated_at\":\"2026-03-09 06:32:24\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-list/23/approve-payment', 'POST', 'success', NULL, '2026-03-08 22:32:24', '2026-03-08 22:32:24'),
(67, 2, 'sample', 'BplsPayment', 'created', 'BplsPayment #12 was created.', 'App\\Models\\BplsPayment', 12, NULL, '{\"payment_year\":2026,\"renewal_cycle\":0,\"or_number\":\"123505\",\"payment_date\":\"2026-03-09 00:00:00\",\"quarters_paid\":\"\\\"[1]\\\"\",\"amount_paid\":507.5,\"surcharges\":0,\"backtaxes\":0,\"discount\":0,\"total_collected\":507.5,\"payment_method\":\"cash\",\"drawee_bank\":null,\"check_number\":null,\"check_date\":null,\"fund_code\":\"100\",\"payor\":\"QWERTY, UIOP POIUY\",\"remarks\":\"\",\"received_by\":\"sample\",\"business_entry_id\":23,\"updated_at\":\"2026-03-09 06:32:40\",\"created_at\":\"2026-03-09 06:32:40\",\"id\":12}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/payment/23/pay', 'POST', 'success', NULL, '2026-03-08 22:32:40', '2026-03-08 22:32:40'),
(68, 2, 'sample', 'BplsPayment', 'created', 'BplsPayment #13 was created.', 'App\\Models\\BplsPayment', 13, NULL, '{\"payment_year\":2026,\"renewal_cycle\":0,\"or_number\":\"123506\",\"payment_date\":\"2026-03-09 00:00:00\",\"quarters_paid\":\"\\\"[2]\\\"\",\"amount_paid\":507.5,\"surcharges\":0,\"backtaxes\":0,\"discount\":25.38,\"total_collected\":482.12,\"payment_method\":\"cash\",\"drawee_bank\":null,\"check_number\":null,\"check_date\":null,\"fund_code\":\"100\",\"payor\":\"QWERTY, UIOP POIUY\",\"remarks\":\"\",\"received_by\":\"sample\",\"business_entry_id\":23,\"updated_at\":\"2026-03-09 06:39:42\",\"created_at\":\"2026-03-09 06:39:42\",\"id\":13}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/payment/23/pay', 'POST', 'success', NULL, '2026-03-08 22:39:42', '2026-03-08 22:39:42'),
(69, 2, 'sample', 'BplsPayment', 'created', 'BplsPayment #14 was created.', 'App\\Models\\BplsPayment', 14, NULL, '{\"payment_year\":2026,\"renewal_cycle\":0,\"or_number\":\"123507\",\"payment_date\":\"2026-03-09 00:00:00\",\"quarters_paid\":\"\\\"[3]\\\"\",\"amount_paid\":507.5,\"surcharges\":0,\"backtaxes\":0,\"discount\":25.38,\"total_collected\":482.12,\"payment_method\":\"cash\",\"drawee_bank\":null,\"check_number\":null,\"check_date\":null,\"fund_code\":\"100\",\"payor\":\"QWERTY, UIOP POIUY\",\"remarks\":\"\",\"received_by\":\"sample\",\"business_entry_id\":23,\"updated_at\":\"2026-03-09 06:42:46\",\"created_at\":\"2026-03-09 06:42:46\",\"id\":14}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/payment/23/pay', 'POST', 'success', NULL, '2026-03-08 22:42:46', '2026-03-08 22:42:46'),
(70, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"ttsa\" was updated.', 'App\\Models\\BusinessEntry', 22, '{\"business_nature\":null,\"capital_investment\":null,\"mode_of_payment\":null,\"total_due\":null,\"approved_at\":null,\"status\":\"pending\",\"updated_at\":\"2026-02-25T09:38:01.000000Z\"}', '{\"business_nature\":\"Eatery\",\"capital_investment\":\"10000\",\"mode_of_payment\":\"semi_annual\",\"total_due\":2030,\"approved_at\":\"2026-03-09 14:48:43\",\"status\":\"for_payment\",\"updated_at\":\"2026-03-09 06:48:43\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-list/22/approve-payment', 'POST', 'success', NULL, '2026-03-08 22:48:43', '2026-03-08 22:48:43'),
(71, NULL, 'System', 'BplsPayment', 'created', 'BplsPayment #15 was created.', 'App\\Models\\BplsPayment', 15, NULL, '{\"bpls_application_id\":15,\"payment_year\":2026,\"renewal_cycle\":0,\"or_number\":\"PAY-20260224-38AD42\",\"payment_date\":\"2026-03-09 10:18:08\",\"quarters_paid\":\"[3]\",\"amount_paid\":2500,\"total_collected\":2500,\"payment_method\":\"online\",\"payor\":\"QWEQWEQWEQW QWEEWQEQWE\",\"received_by\":\"System (Online)\",\"updated_at\":\"2026-03-09 10:18:08\",\"created_at\":\"2026-03-09 10:18:08\",\"id\":15}', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1 Edg/145.0.0.0', 'http://127.0.0.1:8000/portal/applications/15/payment/success', 'GET', 'success', NULL, '2026-03-09 02:18:08', '2026-03-09 02:18:08'),
(72, NULL, 'System', 'BplsPayment', 'created', 'BplsPayment #16 was created.', 'App\\Models\\BplsPayment', 16, NULL, '{\"bpls_application_id\":17,\"payment_year\":2026,\"renewal_cycle\":0,\"or_number\":\"PAY-20260225-F349C8\",\"payment_date\":\"2026-03-09 10:18:23\",\"quarters_paid\":\"[1]\",\"amount_paid\":12500,\"total_collected\":12500,\"payment_method\":\"online\",\"payor\":\"QWEQWEQWEQW QWEEWQEQWE\",\"received_by\":\"System (Online)\",\"updated_at\":\"2026-03-09 10:18:23\",\"created_at\":\"2026-03-09 10:18:23\",\"id\":16}', '127.0.0.1', 'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1 Edg/145.0.0.0', 'http://127.0.0.1:8000/portal/applications/17/payment/success', 'GET', 'success', NULL, '2026-03-09 02:18:23', '2026-03-09 02:18:23'),
(73, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"rarer\" was updated.', 'App\\Models\\BusinessEntry', 21, '{\"business_nature\":null,\"capital_investment\":null,\"mode_of_payment\":null,\"total_due\":null,\"approved_at\":null,\"status\":\"pending\",\"permit_year\":null,\"updated_at\":\"2026-02-25T09:25:55.000000Z\"}', '{\"business_nature\":\"Eatery\",\"capital_investment\":\"10000\",\"mode_of_payment\":\"semi_annual\",\"total_due\":2030,\"approved_at\":\"2026-03-09 20:24:58\",\"status\":\"for_payment\",\"permit_year\":2026,\"updated_at\":\"2026-03-09 12:24:58\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/business-list/21/approve-payment', 'POST', 'success', NULL, '2026-03-09 04:24:58', '2026-03-09 04:24:58'),
(74, 2, 'sample', 'OrAssignment', 'created', 'OrAssignment #6 was created.', 'App\\Models\\OrAssignment', 6, NULL, '{\"start_or\":\"2323232\",\"end_or\":\"23232323\",\"user_id\":2,\"cashier_name\":\"sample\",\"receipt_type\":\"AF51\",\"updated_at\":\"2026-03-12 07:14:09\",\"created_at\":\"2026-03-12 07:14:09\",\"id\":6}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/145.0.0.0 Safari/537.36 Edg/145.0.0.0', 'http://127.0.0.1:8000/bpls/settings/or-assignments', 'POST', 'success', NULL, '2026-03-11 23:14:09', '2026-03-11 23:14:09'),
(75, 2, 'sample', 'BusinessEntry', 'updated', 'BusinessEntry \"busiñess\" was updated.', 'App\\Models\\BusinessEntry', 24, '{\"status\":\"for_payment\",\"retirement_reason\":null,\"retirement_date\":null,\"retirement_remarks\":null,\"retired_at\":null,\"retired_by\":null,\"updated_at\":\"2026-03-09T06:03:43.000000Z\"}', '{\"status\":\"retired\",\"retirement_reason\":\"Business Closure\",\"retirement_date\":\"2026-03-17 00:00:00\",\"retirement_remarks\":\"Nalugi\",\"retired_at\":\"2026-03-17 03:55:13\",\"retired_by\":2,\"updated_at\":\"2026-03-17 03:55:13\"}', '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'http://127.0.0.1:8000/bpls/business-list/24/retire', 'POST', 'success', NULL, '2026-03-16 19:55:13', '2026-03-16 19:55:13'),
(76, 2, 'sample', 'Settings', 'export', 'Exported audit logs to CSV.', NULL, NULL, NULL, NULL, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'http://127.0.0.1:8000/audit-logs/export', 'GET', 'success', NULL, '2026-03-17 21:28:01', '2026-03-17 21:28:01');

-- --------------------------------------------------------

--
-- Table structure for table `barangays`
--

CREATE TABLE `barangays` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `brgy_district` varchar(255) DEFAULT NULL,
  `brgy_name` varchar(255) NOT NULL,
  `brgy_code` varchar(255) NOT NULL,
  `brgy_desc` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `barangays`
--

INSERT INTO `barangays` (`id`, `brgy_district`, `brgy_name`, `brgy_code`, `brgy_desc`, `created_at`, `updated_at`) VALUES
(1, NULL, 'Origuel', '0002', NULL, '2026-01-26 23:57:46', '2026-01-26 23:57:58'),
(3, NULL, 'San Francisco', '0003', NULL, '2026-01-27 00:46:36', '2026-01-27 00:46:36'),
(4, NULL, 'Bakia', '0007', NULL, '2026-02-18 18:33:14', '2026-02-18 18:33:14'),
(5, NULL, 'Test Barangay', '0012', NULL, '2026-03-07 19:21:42', '2026-03-07 19:21:42');

-- --------------------------------------------------------

--
-- Table structure for table `bpls_activity_logs`
--

CREATE TABLE `bpls_activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bpls_application_id` bigint(20) UNSIGNED NOT NULL,
  `actor_type` varchar(255) NOT NULL,
  `actor_id` bigint(20) UNSIGNED NOT NULL,
  `action` varchar(255) NOT NULL,
  `from_status` varchar(255) NOT NULL,
  `to_status` varchar(255) NOT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bpls_activity_logs`
--

INSERT INTO `bpls_activity_logs` (`id`, `bpls_application_id`, `actor_type`, `actor_id`, `action`, `from_status`, `to_status`, `remarks`, `created_at`, `updated_at`) VALUES
(1, 3, 'client', 1, 'submitted', 'draft', 'submitted', 'Application submitted with documents by client.', '2026-02-20 22:26:02', NULL),
(2, 3, 'admin', 2, 'approved_documents', 'submitted', 'verified', 'Documents verified and application approved for assessment.', '2026-02-20 23:25:34', NULL),
(3, 3, 'admin', 2, 'assessed', 'verified', 'assessed', 'Assessment: ₱500.00', '2026-02-20 23:28:00', NULL),
(4, 3, 'admin', 2, 'payment_received', 'assessed', 'paid', 'Payment received. OR#: OR1234', '2026-02-20 23:30:00', NULL),
(5, 3, 'admin', 2, 'final_approved', 'paid', 'approved', 'Business permit approved. OR#: OR1234', '2026-02-20 23:30:16', NULL),
(6, 4, 'client', 1, 'submitted', 'draft', 'submitted', 'Application submitted with documents by client.', '2026-02-20 23:48:56', NULL),
(7, 4, 'admin', 2, 'approved_documents', 'submitted', 'verified', 'Documents verified and application approved for assessment.', '2026-02-20 23:49:48', NULL),
(8, 4, 'admin', 2, 'assessed', 'verified', 'assessed', 'Assessment: ₱5,000.00', '2026-02-21 00:16:25', NULL),
(9, 5, 'client', 1, 'submitted', 'draft', 'submitted', 'Application submitted with documents by client.', '2026-02-21 00:34:13', NULL),
(10, 5, 'admin', 2, 'approved_documents', 'submitted', 'verified', 'Documents verified and application approved for assessment.', '2026-02-21 00:34:47', NULL),
(11, 1, 'client', 1, 'submitted', 'draft', 'submitted', 'Application submitted by client.', '2026-02-21 18:00:53', NULL),
(12, 1, 'admin', 2, 'approved_documents', 'submitted', 'verified', 'Documents verified and application approved for assessment.', '2026-02-21 18:02:33', NULL),
(13, 1, 'admin', 2, 'assessed', 'verified', 'assessed', 'Assessment: ₱5,000.00', '2026-02-21 18:02:52', NULL),
(14, 1, 'admin', 2, 'payment_received', 'assessed', 'paid', 'Payment received. OR#: OR12345', '2026-02-21 18:03:02', NULL),
(15, 1, 'admin', 2, 'final_approved', 'paid', 'approved', 'Business permit approved. OR#: OR12345', '2026-02-21 18:03:30', NULL),
(16, 5, 'admin', 2, 'assessed', 'verified', 'assessed', 'Assessment: ₱1,000.00 — Quarterly (4×)', '2026-02-21 18:31:45', NULL),
(17, 5, 'admin', 2, 'payment_received', 'assessed', 'paid', 'Payment confirmed. OR#: OR1234', '2026-02-21 20:11:29', NULL),
(18, 5, 'admin', 2, 'final_approved', 'paid', 'approved', 'Business permit issued. OR#: OR1234', '2026-02-21 20:12:07', NULL),
(19, 4, 'admin', 2, 'payment_received', 'assessed', 'paid', 'Payment confirmed. OR#: OR12344', '2026-02-21 20:54:57', NULL),
(20, 4, 'admin', 2, 'final_approved', 'paid', 'approved', 'Business permit issued. OR#: OR12344', '2026-02-22 00:27:45', NULL),
(21, 6, 'client', 1, 'submitted', 'draft', 'submitted', 'Application submitted with documents by client.', '2026-02-22 00:45:47', NULL),
(22, 6, 'admin', 2, 'approved_documents', 'submitted', 'verified', 'All documents verified. Application forwarded for fee assessment.', '2026-02-22 00:46:44', NULL),
(23, 6, 'admin', 2, 'assessed', 'verified', 'assessed', 'Fee: ₱12,220.00 | Quarterly — ₱3,055.00 × 4', '2026-02-22 00:46:55', NULL),
(24, 6, 'admin', 2, 'payment_received', 'assessed', 'paid', 'Payment confirmed. OR#: OR1234', '2026-02-22 00:47:23', NULL),
(25, 6, 'admin', 2, 'final_approved', 'paid', 'approved', 'Business permit issued. OR#: OR1234', '2026-02-22 00:47:55', NULL),
(26, 7, 'client', 2, 'submitted', 'draft', 'submitted', 'Application submitted with documents by client.', '2026-02-22 00:52:35', NULL),
(27, 7, 'admin', 2, 'rejected', 'submitted', 'rejected', 'lack of info', '2026-02-22 00:52:58', NULL),
(28, 8, 'client', 2, 'submitted', 'draft', 'submitted', 'Application submitted with documents by client.', '2026-02-22 17:28:27', NULL),
(29, 8, 'admin', 2, 'returned', 'submitted', 'returned', 'correct the documents', '2026-02-22 17:29:31', NULL),
(30, 8, 'client', 2, 'edited', 'returned', 'returned', 'Application details updated by client.', '2026-02-22 18:13:29', NULL),
(31, 8, 'client', 2, 'edited', 'returned', 'returned', 'Application details updated by client.', '2026-02-22 18:44:52', NULL),
(32, 8, 'client', 2, 'submitted', 'draft', 'submitted', 'Application submitted by client.', '2026-02-22 18:46:52', NULL),
(33, 8, 'admin', 2, 'returned', 'submitted', 'returned', 'Complete the form', '2026-02-22 18:47:42', NULL),
(34, 8, 'client', 2, 'edited', 'returned', 'returned', 'Application details updated by client.', '2026-02-22 18:48:47', NULL),
(35, 8, 'client', 2, 'edited', 'returned', 'returned', 'Application details updated by client.', '2026-02-22 18:52:57', NULL),
(36, 8, 'client', 2, 'edited', 'returned', 'returned', 'Application details updated by client.', '2026-02-22 18:53:12', NULL),
(37, 8, 'client', 2, 'edited', 'returned', 'returned', 'Application details updated by client.', '2026-02-22 18:57:21', NULL),
(38, 8, 'client', 2, 'edited', 'returned', 'returned', 'Application details updated by client.', '2026-02-22 18:58:01', NULL),
(39, 8, 'client', 2, 'edited', 'returned', 'returned', 'Application details updated by client.', '2026-02-22 18:59:29', NULL),
(40, 8, 'client', 2, 'submitted', 'draft', 'submitted', 'Application submitted by client.', '2026-02-22 19:00:17', NULL),
(41, 9, 'client', 2, 'submitted', 'draft', 'submitted', 'Application submitted with documents by client.', '2026-02-22 19:09:45', NULL),
(42, 8, 'admin', 2, 'returned', 'submitted', 'returned', 'bad documents', '2026-02-22 19:10:19', NULL),
(43, 8, 'client', 2, 'edited', 'returned', 'returned', 'Application and documents updated by client.', '2026-02-22 19:11:03', NULL),
(44, 8, 'client', 2, 'submitted', 'draft', 'submitted', 'Application submitted by client.', '2026-02-22 19:12:09', NULL),
(45, 9, 'admin', 2, 'returned', 'submitted', 'returned', 'bad', '2026-02-22 19:13:09', NULL),
(46, 9, 'client', 2, 'edited', 'returned', 'returned', 'Application and documents updated by client.', '2026-02-22 19:13:56', NULL),
(47, 9, 'client', 2, 'submitted', 'returned', 'submitted', 'Application updated and submitted by client.', '2026-02-22 19:21:40', NULL),
(48, 9, 'admin', 2, 'rejected', 'submitted', 'rejected', 'bad docs', '2026-02-22 19:23:25', NULL),
(49, 8, 'admin', 2, 'returned', 'submitted', 'returned', 'revise', '2026-02-22 19:24:10', NULL),
(50, 8, 'client', 2, 'submitted', 'returned', 'submitted', 'Application updated and submitted by client.', '2026-02-22 19:24:51', NULL),
(51, 8, 'admin', 2, 'approved_documents', 'submitted', 'verified', 'All documents verified. Application forwarded for fee assessment.', '2026-02-22 19:25:34', NULL),
(52, 10, 'client', 2, 'submitted', 'draft', 'submitted', 'Application submitted with documents by client.', '2026-02-22 19:59:50', NULL),
(53, 10, 'admin', 2, 'approved_documents', 'submitted', 'verified', 'All documents verified. Application forwarded for fee assessment.', '2026-02-22 20:00:11', NULL),
(54, 10, 'admin', 2, 'payment_received', 'assessed', 'paid', 'Payment confirmed. OR#: 123404', '2026-02-22 21:08:42', NULL),
(55, 10, 'admin', 2, 'final_approved', 'paid', 'approved', 'Business permit issued. OR#: 123404', '2026-02-22 21:08:47', NULL),
(56, 8, 'admin', 2, 'payment_received', 'assessed', 'paid', 'Payment confirmed. OR#: 123400', '2026-02-22 21:18:09', NULL),
(57, 8, 'admin', 2, 'final_approved', 'paid', 'approved', 'Business permit issued. OR#: 123400', '2026-02-22 21:26:22', NULL),
(58, 11, 'client', 2, 'submitted', 'draft', 'submitted', 'Application submitted with documents by client.', '2026-02-22 21:45:24', NULL),
(59, 11, 'admin', 2, 'approved_documents', 'submitted', 'verified', 'All documents verified. Application forwarded for fee assessment.', '2026-02-22 21:45:53', NULL),
(60, 11, 'admin', 2, 'assessed', 'verified', 'assessed', 'Assessment set: ₱50000 (semi_annual). 2 OR(s) auto-assigned.', '2026-02-22 21:46:09', NULL),
(61, 11, 'admin', 2, 'ors_confirmed', 'assessed', 'assessed', 'OR numbers confirmed by officer: 123408, 123409', '2026-02-22 21:46:21', NULL),
(62, 11, 'admin', 2, 'payment_received', 'assessed', 'paid', 'Payment confirmed. OR#: 123408', '2026-02-22 21:46:39', NULL),
(63, 11, 'admin', 2, 'final_approved', 'paid', 'approved', 'Business permit issued. Signatory: John', '2026-02-22 23:30:45', NULL),
(64, 12, 'client', 2, 'submitted', 'draft', 'submitted', 'Application submitted with documents by client.', '2026-02-22 23:46:05', NULL),
(65, 12, 'admin', 2, 'approved_documents', 'submitted', 'verified', 'All documents verified. Application forwarded for fee assessment.', '2026-02-22 23:47:37', NULL),
(66, 12, 'admin', 2, 'assessed', 'verified', 'assessed', 'Assessment set: ₱2000 (quarterly). 4 OR(s) auto-assigned.', '2026-02-22 23:48:05', NULL),
(67, 12, 'admin', 2, 'ors_confirmed', 'assessed', 'assessed', 'OR numbers confirmed by officer: 123410, 123411, 123412, 123413', '2026-02-22 23:48:13', NULL),
(68, 12, 'admin', 2, 'payment_received', 'assessed', 'paid', 'Payment confirmed. OR#: 123410', '2026-02-22 23:48:20', NULL),
(69, 12, 'admin', 2, 'final_approved', 'paid', 'approved', 'Business permit issued. Signatory: John', '2026-02-22 23:48:39', NULL),
(70, 13, 'client', 2, 'submitted', 'draft', 'submitted', 'Application submitted with documents by client.', '2026-02-23 00:20:39', NULL),
(71, 14, 'client', 2, 'submitted', 'draft', 'submitted', 'Application submitted with documents by client.', '2026-02-23 01:07:24', NULL),
(72, 14, 'admin', 2, 'approved_documents', 'submitted', 'verified', 'All documents verified. Application forwarded for fee assessment.', '2026-02-23 01:09:17', NULL),
(73, 14, 'admin', 2, 'assessed', 'verified', 'assessed', 'Assessment set: ₱5000 (semi_annual). 2 OR(s) auto-assigned.', '2026-02-23 01:09:28', NULL),
(74, 14, 'admin', 2, 'ors_confirmed', 'assessed', 'assessed', 'OR numbers confirmed by officer: 123414, 123415', '2026-02-23 01:09:32', NULL),
(75, 14, 'admin', 2, 'payment_received', 'assessed', 'paid', 'Payment confirmed. OR#: 123414', '2026-02-23 01:09:37', NULL),
(76, 14, 'admin', 2, 'final_approved', 'paid', 'approved', 'Business permit issued. Signatory: John', '2026-02-23 01:09:49', NULL),
(77, 13, 'admin', 2, 'approved_documents', 'submitted', 'verified', 'All documents verified. Application forwarded for fee assessment.', '2026-02-23 01:12:38', NULL),
(78, 13, 'admin', 2, 'assessed', 'verified', 'assessed', 'Assessment set: ₱10000 (annual). 1 OR(s) auto-assigned.', '2026-02-23 01:13:39', NULL),
(79, 13, 'admin', 2, 'ors_confirmed', 'assessed', 'assessed', 'OR numbers confirmed by officer: 123416', '2026-02-23 01:13:43', NULL),
(80, 13, 'admin', 2, 'payment_received', 'assessed', 'paid', 'Payment confirmed. OR#: 123416', '2026-02-23 01:13:46', NULL),
(81, 13, 'admin', 2, 'final_approved', 'paid', 'approved', 'Business permit issued. Signatory: John', '2026-02-23 01:14:01', NULL),
(82, 15, 'client', 2, 'submitted', 'draft', 'submitted', 'Application submitted with documents by client.', '2026-02-23 23:13:03', NULL),
(83, 15, 'admin', 2, 'approved_documents', 'submitted', 'verified', 'All documents verified. Application forwarded for fee assessment.', '2026-02-23 23:13:26', NULL),
(84, 15, 'admin', 2, 'assessed', 'verified', 'assessed', 'Assessment set: ₱10000 (quarterly). 4 OR(s) auto-assigned.', '2026-02-23 23:13:35', NULL),
(85, 15, 'admin', 2, 'ors_confirmed', 'assessed', 'assessed', 'OR numbers confirmed by officer: 123451, 123452, 123453, 123454', '2026-02-23 23:13:38', NULL),
(86, 15, 'admin', 2, 'ors_confirmed', 'assessed', 'assessed', 'OR numbers confirmed by officer: 123451, 123452, 123453, 123454', '2026-02-23 23:13:45', NULL),
(87, 15, 'admin', 2, 'payment_received', 'assessed', 'paid', 'Payment confirmed. OR#: 123451', '2026-02-23 23:13:49', NULL),
(88, 15, 'admin', 2, 'final_approved', 'paid', 'approved', 'Business permit issued. Signatory: John', '2026-02-23 23:14:04', NULL),
(89, 16, 'client', 2, 'submitted', 'draft', 'submitted', 'Application submitted with documents by client.', '2026-02-24 18:52:39', NULL),
(90, 16, 'admin', 2, 'approved_documents', 'submitted', 'verified', 'All documents verified. Application forwarded for fee assessment.', '2026-02-24 18:53:36', NULL),
(91, 17, 'client', 2, 'submitted', 'draft', 'submitted', 'Application submitted with documents by client.', '2026-02-24 20:06:19', NULL),
(92, 17, 'admin', 2, 'approved_documents', 'submitted', 'verified', 'All documents verified. Application forwarded for fee assessment.', '2026-02-24 22:32:56', NULL),
(93, 17, 'admin', 2, 'assessed', 'verified', 'assessed', 'Assessment set: ₱50000 (quarterly). 4 OR(s) auto-assigned.', '2026-02-24 22:33:07', NULL),
(94, 17, 'admin', 2, 'ors_confirmed', 'assessed', 'assessed', 'OR numbers confirmed by officer: 123455, 123456, 123457, 123458', '2026-02-24 22:33:10', NULL),
(95, 17, 'admin', 2, 'payment_received', 'assessed', 'paid', 'Payment confirmed. OR#: 123455', '2026-02-24 22:33:13', NULL),
(96, 17, 'admin', 2, 'final_approved', 'paid', 'approved', 'Business permit issued. Signatory: John', '2026-02-24 22:33:19', NULL),
(97, 18, 'client', 2, 'submitted', 'draft', 'submitted', 'Application submitted with documents by client.', '2026-02-25 01:21:55', NULL),
(98, 19, 'client', 2, 'submitted', 'draft', 'submitted', 'Application submitted with documents by client.', '2026-02-25 01:38:01', NULL),
(99, 19, 'admin', 2, 'approved_documents', 'submitted', 'verified', 'All documents verified. Application forwarded for fee assessment.', '2026-03-08 21:39:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bpls_application_ors`
--

CREATE TABLE `bpls_application_ors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bpls_application_id` bigint(20) UNSIGNED NOT NULL,
  `or_assignment_id` bigint(20) UNSIGNED NOT NULL,
  `or_number` varchar(255) NOT NULL,
  `installment_number` tinyint(3) UNSIGNED NOT NULL,
  `period_label` varchar(255) NOT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'unpaid',
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bpls_application_ors`
--

INSERT INTO `bpls_application_ors` (`id`, `bpls_application_id`, `or_assignment_id`, `or_number`, `installment_number`, `period_label`, `status`, `paid_at`, `created_at`, `updated_at`) VALUES
(1, 8, 1, '123400', 1, 'Q1 2026', 'unpaid', NULL, '2026-02-22 19:56:05', '2026-02-22 19:56:05'),
(2, 8, 1, '123401', 2, 'Q2 2026', 'unpaid', NULL, '2026-02-22 19:56:05', '2026-02-22 19:56:05'),
(3, 8, 1, '123402', 3, 'Q3 2026', 'unpaid', NULL, '2026-02-22 19:56:05', '2026-02-22 19:56:05'),
(4, 8, 1, '123403', 4, 'Q4 2026', 'unpaid', NULL, '2026-02-22 19:56:05', '2026-02-22 19:56:05'),
(5, 10, 1, '123404', 1, 'Q1 2026', 'unpaid', NULL, '2026-02-22 20:19:55', '2026-02-22 20:19:55'),
(6, 10, 1, '123405', 2, 'Q2 2026', 'unpaid', NULL, '2026-02-22 20:19:55', '2026-02-22 20:19:55'),
(7, 10, 1, '123406', 3, 'Q3 2026', 'unpaid', NULL, '2026-02-22 20:19:55', '2026-02-22 20:19:55'),
(8, 10, 1, '123407', 4, 'Q4 2026', 'unpaid', NULL, '2026-02-22 20:19:55', '2026-02-22 20:19:55'),
(9, 11, 1, '123408', 1, '1st Half 2026', 'unpaid', NULL, '2026-02-22 21:46:09', '2026-02-22 21:46:09'),
(10, 11, 1, '123409', 2, '2nd Half 2026', 'unpaid', NULL, '2026-02-22 21:46:09', '2026-02-22 21:46:09'),
(11, 12, 1, '123410', 1, 'Q1 2026', 'unpaid', NULL, '2026-02-22 23:48:05', '2026-02-22 23:48:05'),
(12, 12, 1, '123411', 2, 'Q2 2026', 'unpaid', NULL, '2026-02-22 23:48:05', '2026-02-22 23:48:05'),
(13, 12, 1, '123412', 3, 'Q3 2026', 'unpaid', NULL, '2026-02-22 23:48:05', '2026-02-22 23:48:05'),
(14, 12, 1, '123413', 4, 'Q4 2026', 'unpaid', NULL, '2026-02-22 23:48:05', '2026-02-22 23:48:05'),
(15, 14, 1, '123414', 1, '1st Half 2026', 'unpaid', NULL, '2026-02-23 01:09:28', '2026-02-23 01:09:28'),
(16, 14, 1, '123415', 2, '2nd Half 2026', 'unpaid', NULL, '2026-02-23 01:09:28', '2026-02-23 01:09:28'),
(17, 13, 1, '123416', 1, '2026', 'unpaid', NULL, '2026-02-23 01:13:39', '2026-02-23 01:13:39'),
(18, 15, 2, '123451', 1, 'Q1 2026', 'unpaid', NULL, '2026-02-23 23:13:35', '2026-02-23 23:13:35'),
(19, 15, 2, '123452', 2, 'Q2 2026', 'unpaid', NULL, '2026-02-23 23:13:35', '2026-02-23 23:13:35'),
(20, 15, 2, 'PAY-20260224-38AD42', 3, 'Q3 2026', 'paid', '2026-03-09 02:18:08', '2026-02-23 23:13:35', '2026-03-09 02:18:08'),
(21, 15, 2, '123454', 4, 'Q4 2026', 'unpaid', NULL, '2026-02-23 23:13:35', '2026-02-23 23:13:35'),
(22, 17, 2, 'PAY-20260225-F349C8', 1, 'Q1 2026', 'paid', '2026-03-09 02:18:23', '2026-02-24 22:33:07', '2026-03-09 02:18:23'),
(23, 17, 2, '123456', 2, 'Q2 2026', 'unpaid', NULL, '2026-02-24 22:33:07', '2026-02-24 22:33:07'),
(24, 17, 2, '123457', 3, 'Q3 2026', 'unpaid', NULL, '2026-02-24 22:33:07', '2026-02-24 22:33:07'),
(25, 17, 2, '123458', 4, 'Q4 2026', 'unpaid', NULL, '2026-02-24 22:33:07', '2026-02-24 22:33:07');

-- --------------------------------------------------------

--
-- Table structure for table `bpls_assessments`
--

CREATE TABLE `bpls_assessments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bpls_application_id` bigint(20) UNSIGNED NOT NULL,
  `capital_investment` decimal(15,2) NOT NULL DEFAULT 0.00,
  `business_tax` decimal(12,2) NOT NULL DEFAULT 0.00,
  `mayors_permit_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `sanitary_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `fire_inspection_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `zoning_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `garbage_fee` decimal(12,2) NOT NULL DEFAULT 0.00,
  `surcharge` decimal(12,2) NOT NULL DEFAULT 0.00,
  `penalty` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_due` decimal(12,2) NOT NULL DEFAULT 0.00,
  `mode_of_payment` enum('full','quarterly') NOT NULL DEFAULT 'full',
  `notes` text DEFAULT NULL,
  `assessed_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bpls_benefits`
--

CREATE TABLE `bpls_benefits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `label` varchar(255) NOT NULL,
  `field_key` varchar(255) NOT NULL,
  `discount_percent` decimal(5,2) NOT NULL DEFAULT 0.00,
  `apply_to` varchar(255) NOT NULL DEFAULT 'permit_only',
  `description` text DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bpls_benefits`
--

INSERT INTO `bpls_benefits` (`id`, `name`, `label`, `field_key`, `discount_percent`, `apply_to`, `description`, `is_active`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'PWD', 'Person', 'is_pwd', 2.00, 'permit_only', NULL, 1, 0, '2026-03-08 23:01:35', '2026-03-08 23:02:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bpls_businesses`
--

CREATE TABLE `bpls_businesses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `owner_id` bigint(20) UNSIGNED NOT NULL,
  `business_name` varchar(255) NOT NULL,
  `trade_name` varchar(255) DEFAULT NULL,
  `date_of_application` date DEFAULT NULL,
  `tin_no` varchar(255) DEFAULT NULL,
  `dti_sec_cda_no` varchar(255) DEFAULT NULL,
  `dti_sec_cda_date` date DEFAULT NULL,
  `business_mobile` varchar(255) DEFAULT NULL,
  `business_email` varchar(255) DEFAULT NULL,
  `type_of_business` varchar(255) DEFAULT NULL,
  `business_nature` varchar(255) DEFAULT NULL,
  `capital_investment` decimal(15,2) DEFAULT NULL,
  `amendment_from` varchar(255) DEFAULT NULL,
  `amendment_to` varchar(255) DEFAULT NULL,
  `tax_incentive` tinyint(1) NOT NULL DEFAULT 0,
  `business_organization` varchar(255) DEFAULT NULL,
  `business_area_type` varchar(255) DEFAULT NULL,
  `business_scale` varchar(255) DEFAULT NULL,
  `business_sector` varchar(255) DEFAULT NULL,
  `zone` varchar(255) DEFAULT NULL,
  `occupancy` varchar(255) DEFAULT NULL,
  `business_area_sqm` decimal(10,2) DEFAULT NULL,
  `total_employees` int(11) DEFAULT NULL,
  `employees_lgu` int(11) DEFAULT NULL,
  `region` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `municipality` varchar(255) DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bpls_businesses`
--

INSERT INTO `bpls_businesses` (`id`, `owner_id`, `business_name`, `trade_name`, `date_of_application`, `tin_no`, `dti_sec_cda_no`, `dti_sec_cda_date`, `business_mobile`, `business_email`, `type_of_business`, `business_nature`, `capital_investment`, `amendment_from`, `amendment_to`, `tax_incentive`, `business_organization`, `business_area_type`, `business_scale`, `business_sector`, `zone`, `occupancy`, `business_area_sqm`, `total_employees`, `employees_lgu`, `region`, `province`, `municipality`, `barangay`, `street`, `status`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'rarer', 'qwert', '2026-02-21', '11233', '50.00', '2026-02-21', '09123456789', 'juan@example.com', 'Wholesale', NULL, NULL, 'Sole Proprietorship', 'Corporation', 0, 'Sole Proprietorship', 'Leased', 'Small (P3M - P15M)', 'Health', 'Zone 5 - Agricultural', 'Multi-level', 1.00, 2, 2, '2', 'sabel', 'lig', 'vic', '223', 'pending', '2026-02-20 21:56:18', '2026-02-20 21:56:18', NULL),
(4, 5, 'rarer', 'qwert', '2026-02-21', '11233', '50.00', '2026-02-21', '09123456789', 'juan@example.com', 'Partnership', NULL, NULL, 'Single Proprietorship', 'Partnership', 0, 'Single Proprietorship', 'Owned', 'Micro', 'Agriculture', 'Commercial', 'Leased', 1.00, 2, 2, '2', 'sabel', 'lig', 'vic', '223', 'pending', '2026-02-20 22:26:02', '2026-02-20 22:26:02', NULL),
(5, 6, 'tata', 'wawa', '2026-02-21', '1234', '1234', '2026-02-21', '1234', 'sample@gmail.com', 'Sole Proprietorship', NULL, NULL, 'Single Proprietorship', 'Partnership', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', '12313', '1231', 'weqwe', 'qwew', 'pending', '2026-02-20 23:48:56', '2026-02-20 23:48:56', NULL),
(6, 7, 'qwe', 'qwe', '2026-02-21', 'qwe', 'eqwe', '2026-02-21', 'qweqw', 'qwe', 'Partnership', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'qwe', 'qwe', NULL, NULL, NULL, 'pending', '2026-02-21 00:34:13', '2026-02-21 00:34:13', NULL),
(7, 8, 'QWEEQEQWE', 'QWEQWE', '2026-02-22', 'QWE', 'QWE', NULL, 'QWE', 'QWE@GMAIL.COM', 'Cooperative', NULL, NULL, NULL, NULL, 0, 'Single Proprietorship', 'Owned', 'Small', 'Agriculture', 'Industrial', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', '2026-02-22 00:45:47', '2026-02-22 00:45:47', NULL),
(8, 9, 'qwewqe', NULL, '2026-02-22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', '2026-02-22 00:52:35', '2026-02-22 00:52:35', NULL),
(9, 10, 'Jojo', 'ojoj', '2026-02-23', '12123', '123', '2026-02-23', '12', 'john@adadsad', 'Partnership', NULL, NULL, NULL, NULL, 0, NULL, 'Owned', 'Small', 'Trade', 'Commercial', 'Owned', 22.00, 2, 12, NULL, NULL, NULL, NULL, NULL, 'pending', '2026-02-22 17:28:27', '2026-02-22 18:13:29', NULL),
(10, 11, 'asd', 'asd', '2026-02-23', NULL, NULL, '2026-02-23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', '2026-02-22 19:09:45', '2026-02-22 19:13:56', NULL),
(11, 12, 'asd', NULL, '2026-02-23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', '2026-02-22 19:59:50', '2026-02-22 19:59:50', NULL),
(12, 13, 'dadad', 'dada', '2026-02-23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', '2026-02-22 21:45:24', '2026-02-22 21:45:24', NULL),
(13, 14, 'qwe', 'q', '2026-02-23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', '2026-02-23 00:20:39', '2026-02-23 00:20:39', NULL),
(14, 15, 'asd', NULL, '2026-02-23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', '2026-02-23 01:07:24', '2026-02-23 01:07:24', NULL),
(15, 16, 'qwe', NULL, '2026-02-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', '2026-02-23 21:06:46', '2026-02-23 21:06:46', NULL),
(16, 17, 'QWEWQEQWE', NULL, '2026-02-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', '2026-02-23 23:13:03', '2026-02-23 23:13:03', NULL),
(17, 18, 'John', NULL, '2026-02-25', NULL, NULL, NULL, NULL, NULL, 'Sole Proprietorship', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Region IV-A (CALABARZON)', 'Laguna', 'City of Biñan', 'Langkiwa', NULL, 'pending', '2026-02-24 18:52:39', '2026-02-24 18:52:39', NULL),
(18, 19, 'shabushabu', NULL, '2026-02-25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, NULL, NULL, NULL, 'pending', '2026-02-24 19:25:17', '2026-02-24 19:25:17', NULL),
(19, 20, 'Pacpac', NULL, '2026-02-25', NULL, NULL, NULL, NULL, NULL, 'Partnership', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 2, NULL, 'Region IV-A (CALABARZON)', 'Laguna', 'Los Baños', 'Bagong Silang', NULL, 'pending', '2026-02-25 01:21:55', '2026-02-25 01:21:55', NULL),
(20, 21, 'rarer', NULL, '2026-02-25', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', '2026-02-25 01:25:55', '2026-02-25 01:25:55', NULL),
(21, 22, 'ttsa', NULL, '2026-02-25', NULL, NULL, NULL, NULL, NULL, 'Sole Proprietorship', NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Region IV-A (CALABARZON)', 'Laguna', 'Famy', 'Magdalo', NULL, 'pending', '2026-02-25 01:38:01', '2026-02-25 01:38:01', NULL),
(22, 23, 'shabushabu', NULL, '2026-03-09', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', '2026-03-08 21:20:32', '2026-03-08 21:20:32', NULL),
(23, 24, 'busiñess', NULL, '2026-03-09', NULL, NULL, NULL, NULL, NULL, 'Retail', NULL, NULL, NULL, NULL, 0, 'Partnership', 'Leased', 'Small (P3M - P15M)', 'Education', 'Zone 2 - Industrial', 'Ground Floor', 2.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'pending', '2026-03-08 21:21:42', '2026-03-08 21:21:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bpls_business_amendments`
--

CREATE TABLE `bpls_business_amendments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_entry_id` bigint(20) UNSIGNED NOT NULL,
  `old_business_name` varchar(255) DEFAULT NULL,
  `old_trade_name` varchar(255) DEFAULT NULL,
  `old_tin_no` varchar(255) DEFAULT NULL,
  `old_type_of_business` varchar(255) DEFAULT NULL,
  `old_business_nature` varchar(255) DEFAULT NULL,
  `old_business_scale` varchar(255) DEFAULT NULL,
  `old_business_barangay` varchar(255) DEFAULT NULL,
  `old_business_municipality` varchar(255) DEFAULT NULL,
  `old_business_street` varchar(255) DEFAULT NULL,
  `old_last_name` varchar(255) DEFAULT NULL,
  `old_first_name` varchar(255) DEFAULT NULL,
  `old_middle_name` varchar(255) DEFAULT NULL,
  `old_mobile_no` varchar(255) DEFAULT NULL,
  `old_email` varchar(255) DEFAULT NULL,
  `old_business_mobile` varchar(255) DEFAULT NULL,
  `old_business_email` varchar(255) DEFAULT NULL,
  `old_business_organization` varchar(255) DEFAULT NULL,
  `old_zone` varchar(255) DEFAULT NULL,
  `old_total_employees` int(11) DEFAULT NULL,
  `new_business_name` varchar(255) DEFAULT NULL,
  `new_trade_name` varchar(255) DEFAULT NULL,
  `new_tin_no` varchar(255) DEFAULT NULL,
  `new_type_of_business` varchar(255) DEFAULT NULL,
  `new_business_nature` varchar(255) DEFAULT NULL,
  `new_business_scale` varchar(255) DEFAULT NULL,
  `new_business_barangay` varchar(255) DEFAULT NULL,
  `new_business_municipality` varchar(255) DEFAULT NULL,
  `new_business_street` varchar(255) DEFAULT NULL,
  `new_last_name` varchar(255) DEFAULT NULL,
  `new_first_name` varchar(255) DEFAULT NULL,
  `new_middle_name` varchar(255) DEFAULT NULL,
  `new_mobile_no` varchar(255) DEFAULT NULL,
  `new_email` varchar(255) DEFAULT NULL,
  `new_business_mobile` varchar(255) DEFAULT NULL,
  `new_business_email` varchar(255) DEFAULT NULL,
  `new_business_organization` varchar(255) DEFAULT NULL,
  `new_zone` varchar(255) DEFAULT NULL,
  `new_total_employees` int(11) DEFAULT NULL,
  `changed_fields` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`changed_fields`)),
  `amendment_type` varchar(255) NOT NULL DEFAULT 'edit',
  `reason` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `amended_by_name` varchar(255) DEFAULT NULL,
  `amended_by` bigint(20) UNSIGNED DEFAULT NULL,
  `amended_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bpls_business_entries`
--

CREATE TABLE `bpls_business_entries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `citizenship` varchar(255) DEFAULT NULL,
  `civil_status` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `mobile_no` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `is_pwd` tinyint(1) NOT NULL DEFAULT 0,
  `is_4ps` tinyint(1) NOT NULL DEFAULT 0,
  `is_solo_parent` tinyint(1) NOT NULL DEFAULT 0,
  `is_senior` tinyint(1) NOT NULL DEFAULT 0,
  `is_bmbe` tinyint(1) NOT NULL DEFAULT 0,
  `is_cooperative` tinyint(1) NOT NULL DEFAULT 0,
  `discount_10` tinyint(1) NOT NULL DEFAULT 0,
  `discount_5` tinyint(1) NOT NULL DEFAULT 0,
  `owner_region` varchar(255) DEFAULT NULL,
  `owner_province` varchar(255) DEFAULT NULL,
  `owner_municipality` varchar(255) DEFAULT NULL,
  `owner_barangay` varchar(255) DEFAULT NULL,
  `owner_street` varchar(255) DEFAULT NULL,
  `business_name` varchar(255) NOT NULL,
  `trade_name` varchar(255) DEFAULT NULL,
  `date_of_application` date DEFAULT NULL,
  `tin_no` varchar(255) DEFAULT NULL,
  `dti_sec_cda_no` varchar(255) DEFAULT NULL,
  `dti_sec_cda_date` date DEFAULT NULL,
  `business_mobile` varchar(255) DEFAULT NULL,
  `business_email` varchar(255) DEFAULT NULL,
  `type_of_business` varchar(255) DEFAULT NULL,
  `business_nature` varchar(255) DEFAULT NULL,
  `amendment_from` varchar(255) DEFAULT NULL,
  `amendment_to` varchar(255) DEFAULT NULL,
  `tax_incentive` tinyint(1) NOT NULL DEFAULT 0,
  `business_organization` varchar(255) DEFAULT NULL,
  `business_area_type` varchar(255) DEFAULT NULL,
  `business_scale` varchar(255) DEFAULT NULL,
  `capital_investment` decimal(15,2) DEFAULT NULL,
  `mode_of_payment` varchar(255) DEFAULT NULL,
  `total_due` decimal(15,2) DEFAULT NULL,
  `renewal_total_due` decimal(15,2) DEFAULT NULL COMMENT 'Total due for the current renewal cycle',
  `late_renewal` tinyint(4) NOT NULL DEFAULT 0 COMMENT '1 = renewal happened after Jan 20, 25% surcharge applies per RA 7160 Sec. 168',
  `approved_at` timestamp NULL DEFAULT NULL,
  `business_sector` varchar(255) DEFAULT NULL,
  `zone` varchar(255) DEFAULT NULL,
  `occupancy` varchar(255) DEFAULT NULL,
  `business_area_sqm` decimal(10,2) DEFAULT NULL,
  `total_employees` int(11) DEFAULT NULL,
  `employees_lgu` int(11) DEFAULT NULL,
  `business_region` varchar(255) DEFAULT NULL,
  `business_province` varchar(255) DEFAULT NULL,
  `business_municipality` varchar(255) DEFAULT NULL,
  `business_barangay` varchar(255) DEFAULT NULL,
  `business_street` varchar(255) DEFAULT NULL,
  `emergency_contact_person` varchar(255) DEFAULT NULL,
  `emergency_mobile` varchar(255) DEFAULT NULL,
  `emergency_email` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'pending',
  `retirement_reason` text DEFAULT NULL,
  `retirement_date` date DEFAULT NULL,
  `retirement_remarks` text DEFAULT NULL,
  `retired_at` timestamp NULL DEFAULT NULL,
  `retired_by` bigint(20) UNSIGNED DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `renewal_cycle` tinyint(3) UNSIGNED NOT NULL DEFAULT 0 COMMENT '0 = original, 1 = 1st renewal, etc.',
  `permit_year` smallint(5) UNSIGNED DEFAULT NULL COMMENT 'Year the current total_due assessment is for',
  `business_id` varchar(50) DEFAULT NULL COMMENT 'Generated on Approve to Payment. Format: {MUNI}-{YEAR}-{ID}',
  `last_renewed_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bpls_business_entries`
--

INSERT INTO `bpls_business_entries` (`id`, `last_name`, `first_name`, `middle_name`, `citizenship`, `civil_status`, `gender`, `birthdate`, `mobile_no`, `email`, `is_pwd`, `is_4ps`, `is_solo_parent`, `is_senior`, `is_bmbe`, `is_cooperative`, `discount_10`, `discount_5`, `owner_region`, `owner_province`, `owner_municipality`, `owner_barangay`, `owner_street`, `business_name`, `trade_name`, `date_of_application`, `tin_no`, `dti_sec_cda_no`, `dti_sec_cda_date`, `business_mobile`, `business_email`, `type_of_business`, `business_nature`, `amendment_from`, `amendment_to`, `tax_incentive`, `business_organization`, `business_area_type`, `business_scale`, `capital_investment`, `mode_of_payment`, `total_due`, `renewal_total_due`, `late_renewal`, `approved_at`, `business_sector`, `zone`, `occupancy`, `business_area_sqm`, `total_employees`, `employees_lgu`, `business_region`, `business_province`, `business_municipality`, `business_barangay`, `business_street`, `emergency_contact_person`, `emergency_mobile`, `emergency_email`, `status`, `retirement_reason`, `retirement_date`, `retirement_remarks`, `retired_at`, `retired_by`, `remarks`, `renewal_cycle`, `permit_year`, `business_id`, `last_renewed_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Dela Cruz', 'Juan', 'Santos', 'Filipino', 'Single', 'Male', '2026-02-21', '09123456789', 'juan@example.com', 1, 0, 0, 0, 0, 0, 0, 0, '2', 'sabel', 'lig', 'vic', '223', 'rarer', 'qwert', '2026-02-21', '11233', '50.00', '2026-02-21', '09123456789', 'juan@example.com', 'Wholesale', NULL, 'Sole Proprietorship', 'Corporation', 0, 'Sole Proprietorship', 'Leased', 'Small (P3M - P15M)', NULL, NULL, NULL, NULL, 0, NULL, 'Health', 'Zone 5 - Agricultural', 'Multi-level', 1.00, 2, 2, '2', 'sabel', 'lig', 'vic', '223', 'UIOP POIUY QWERTY', '09123456789', 'juan@example.com', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, 0, 2026, NULL, NULL, '2026-02-20 21:56:18', '2026-02-24 22:12:52', NULL),
(3, 'Dela Cruz', 'Juan', 'Santos', 'Filipino', 'Single', NULL, '2026-02-21', '09123456789', 'juan@example.com', 0, 0, 1, 0, 0, 0, 0, 0, '2', 'sabel', 'lig', 'vic', '223', 'rarer', 'qwert', '2026-02-21', '11233', '50.00', '2026-02-21', '09123456789', 'juan@example.com', 'Partnership', NULL, 'Single Proprietorship', 'Partnership', 0, 'Single Proprietorship', 'Owned', 'Micro', NULL, NULL, NULL, NULL, 0, NULL, 'Agriculture', 'Commercial', 'Leased', 1.00, 2, 2, '2', 'sabel', 'lig', 'vic', '223', 'UIOP POIUY QWERTY', '09123456789', 'juan@example.com', 'approved', NULL, NULL, NULL, NULL, NULL, NULL, 0, 2026, NULL, NULL, '2026-02-20 22:26:02', '2026-02-24 22:12:52', NULL),
(4, 'Dela vcru', 'ewe', 'qwe', 'Filipino', 'Single', 'Male', '2026-02-21', '0987654321', 'qwerty@gmail.com', 1, 0, 0, 0, 0, 0, 0, 0, '2', 'qwe', 'qwe', 'qwe', 'qwe', 'tata', 'wawa', '2026-02-21', '1234', '1234', '2026-02-21', '1234', 'sample@gmail.com', 'Sole Proprietorship', NULL, 'Single Proprietorship', 'Partnership', 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2', '12313', '1231', 'weqwe', 'qwew', NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, 'good', 1, 2026, NULL, '2026-02-21 00:13:31', '2026-02-20 23:48:56', '2026-02-24 22:12:52', NULL),
(5, 'qweqw', 'qweqwe', 'qweqwe', 'Filipino', 'Single', 'Female', '2026-02-21', '987654321', 'qwe@123123', 0, 0, 1, 0, 0, 0, 0, 0, 'qwe', 'qwe', 'qwe', 'qwe', 'qwe', 'qwe', 'qwe', '2026-02-21', 'qwe', 'eqwe', '2026-02-21', 'qweqw', 'qwe', 'Partnership', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'qwe', 'qwe', NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, 0, 2026, NULL, NULL, '2026-02-21 00:34:13', '2026-02-24 22:12:52', NULL),
(6, 'QWER', 'QWER', 'QWER', 'Filipino', 'Married', 'Female', '2026-02-22', '0987654321', 'qwe@123123', 1, 0, 0, 1, 0, 0, 0, 0, '123', '123', '123', '123', '123', 'QWEEQEQWE', 'QWEQWE', '2026-02-22', 'QWE', 'QWE', NULL, 'QWE', 'QWE@GMAIL.COM', 'Cooperative', NULL, NULL, NULL, 0, 'Single Proprietorship', 'Owned', 'Small', NULL, NULL, NULL, NULL, 0, NULL, 'Agriculture', 'Industrial', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, 0, 2026, NULL, NULL, '2026-02-22 00:45:47', '2026-02-24 22:12:52', NULL),
(7, 'qwe', 'qwe', 'qweqe', 'Filipino', 'Married', 'Female', '2026-02-22', '1456789', NULL, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, 'qwewqe', NULL, '2026-02-22', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, NULL, NULL, NULL, 0, 2026, NULL, NULL, '2026-02-22 00:52:35', '2026-02-24 22:12:52', NULL),
(8, 'dela', 'John', 'Santos', 'Filipino', 'Single', 'Male', '2026-02-23', '0987654321', 'john@gmail.com', 0, 0, 1, 0, 0, 0, 0, 0, '2', '2', '2', '2', '2', 'Jojo', 'ojoj', '2026-02-23', '12123', '123', '2026-02-23', '12', 'john@adadsad', 'Partnership', NULL, 'Single Proprietorship', 'Partnership', 0, 'Partnership', 'Owned', 'Small', NULL, NULL, NULL, NULL, 0, NULL, 'Trade', 'Commercial', 'Owned', 22.00, 2, 12, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, 0, 2026, NULL, NULL, '2026-02-22 17:28:27', '2026-02-24 22:12:53', NULL),
(9, 'delaasd', 'asd', 'asd', 'Filipino', 'Married', 'Male', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, 'asd', 'asd', '2026-02-23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'rejected', NULL, NULL, NULL, NULL, NULL, NULL, 0, 2026, NULL, NULL, '2026-02-22 19:09:45', '2026-02-24 22:12:53', NULL),
(10, 'asd', 'asd', 'asd', 'Filipino', 'Single', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, 'asd', NULL, '2026-02-23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, 0, 2026, NULL, NULL, '2026-02-22 19:59:50', '2026-02-24 22:12:53', NULL),
(11, 'dada', 'daa', 'daad', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, 'dadad', 'dada', '2026-02-23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, 0, 2026, NULL, NULL, '2026-02-22 21:45:24', '2026-02-24 22:12:53', NULL),
(12, 'dada', 'daa', 'daad', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, 'dadad', 'dada', '2026-02-23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, 0, 2026, NULL, NULL, '2026-02-22 23:46:04', '2026-02-24 22:12:53', NULL),
(13, 'qwe', 'qwe', 'qwe', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, 'qwe', 'q', '2026-02-23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, 0, 2026, NULL, NULL, '2026-02-23 00:20:39', '2026-02-24 22:12:53', NULL),
(14, 'asd', 'asd', 'asd', 'Foreign National', 'Widowed', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, 'asd', NULL, '2026-02-23', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'for_payment', NULL, NULL, NULL, NULL, NULL, NULL, 0, 2026, NULL, NULL, '2026-02-23 01:07:24', '2026-02-24 22:32:19', NULL),
(15, 'qwe', 'qwe', 'qwe', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, 'qwe', NULL, '2026-02-24', NULL, NULL, NULL, NULL, NULL, NULL, 'Trading', NULL, NULL, 0, NULL, NULL, 'Micro (Assets up to P3M)', 10000.00, 'quarterly', 2030.00, NULL, 0, '2026-02-24 00:09:26', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'for_payment', NULL, NULL, NULL, NULL, NULL, NULL, 0, 2026, 'BUS-2026-000015', NULL, '2026-02-23 21:06:46', '2026-02-25 01:25:03', NULL),
(16, 'QWEEWQEQWE', 'QWEQWEQWEQW', 'QWEQWEWQE', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, 'QWEWQEQWE', NULL, '2026-02-24', NULL, NULL, NULL, NULL, NULL, NULL, 'Trading', NULL, NULL, 0, NULL, NULL, 'Large (Above P100M)', 10000.00, 'semi_annual', 4780.00, NULL, 0, '2026-02-24 19:47:29', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, 1, 2027, 'BUS-2027-000016', '2026-02-25 03:47:20', '2026-02-23 23:13:03', '2026-02-24 19:48:56', NULL),
(17, 'John', 'Juan', NULL, 'Filipino', 'Single', 'Male', NULL, '0987654321', NULL, 0, 0, 0, 0, 0, 0, 0, 0, 'Region IV-A (CALABARZON)', 'Laguna', 'City of Biñan', 'Biñan', 'Purok 1', 'John', NULL, '2026-02-25', NULL, NULL, NULL, NULL, NULL, 'Sole Proprietorship', NULL, NULL, NULL, 0, NULL, NULL, 'Micro (Assets up to P3M)', 10000.00, 'semi_annual', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'Region IV-A (CALABARZON)', 'Laguna', 'City of Biñan', 'Langkiwa', NULL, 'gerr', NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, 2026, NULL, NULL, '2026-02-24 18:52:39', '2026-02-24 19:23:25', NULL),
(18, 'QWERTY', 'UIOP', 'POIUY', 'Foreign National', 'Married', 'Female', '2026-02-25', NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, 'Philippines', 'shabushabu', NULL, '2026-02-25', NULL, NULL, NULL, NULL, NULL, NULL, 'Trading', NULL, NULL, 0, NULL, NULL, 'Medium (P15M - P100M)', 1000000.00, 'semi_annual', 20950.00, NULL, 0, '2026-02-24 23:57:48', NULL, NULL, NULL, NULL, NULL, NULL, '2', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, 0, 2026, 'BUS-2026-000018', NULL, '2026-02-24 19:25:17', '2026-02-25 00:00:04', NULL),
(19, 'QWEEWQEQWE', 'QWEQWEQWEQW', 'QWEQWEWQE', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, 'QWEWQEQWE', NULL, '2026-02-24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, 10000.00, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, 0, 2026, NULL, NULL, '2026-02-24 20:06:19', '2026-02-24 22:33:19', NULL),
(20, 'pacman', 'papacc', 'pac', 'Filipino', 'Single', 'Female', NULL, '0987654321', NULL, 0, 0, 0, 0, 0, 0, 0, 0, 'Region IV-A (CALABARZON)', 'Laguna', 'City of Biñan', 'Biñan', NULL, 'Pacpac', NULL, '2026-02-25', NULL, NULL, NULL, NULL, NULL, 'Partnership', NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, 2, NULL, 'Region IV-A (CALABARZON)', 'Laguna', 'Los Baños', 'Bagong Silang', NULL, NULL, NULL, NULL, 'pending', NULL, NULL, NULL, NULL, NULL, NULL, 0, 2026, NULL, NULL, '2026-02-25 01:21:55', '2026-02-25 01:21:55', NULL),
(21, 'QWERTY', 'UIOP', 'POIUY', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, 'Philippines', 'rarer', NULL, '2026-02-25', NULL, NULL, NULL, NULL, NULL, NULL, 'Eatery', NULL, NULL, 0, NULL, NULL, NULL, 10000.00, 'semi_annual', 2030.00, NULL, 0, '2026-03-09 12:24:58', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'for_payment', NULL, NULL, NULL, NULL, NULL, NULL, 0, 2026, NULL, NULL, '2026-02-25 01:25:55', '2026-03-09 04:24:58', NULL),
(22, 'tata', 'tata', NULL, 'Filipino', 'Single', 'Male', NULL, '0987654321', NULL, 0, 0, 0, 0, 0, 0, 0, 0, 'Region IV-A (CALABARZON)', 'Laguna', 'Magdalena', 'Poblacion', NULL, 'ttsa', NULL, '2026-02-25', NULL, NULL, NULL, NULL, NULL, 'Sole Proprietorship', 'Eatery', NULL, NULL, 0, NULL, NULL, NULL, 10000.00, 'semi_annual', 2030.00, NULL, 0, '2026-03-09 06:48:43', NULL, NULL, NULL, NULL, NULL, NULL, 'Region IV-A (CALABARZON)', 'Laguna', 'Famy', 'Magdalo', NULL, NULL, NULL, NULL, 'for_payment', NULL, NULL, NULL, NULL, NULL, NULL, 0, 2026, NULL, NULL, '2026-02-25 01:38:01', '2026-03-08 22:48:43', NULL),
(23, 'QWERTY', 'UIOP', 'POIUY', 'Filipino', 'Married', 'Female', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, 'Philippines', 'shabushabu', NULL, '2026-03-09', NULL, NULL, NULL, NULL, NULL, NULL, 'Eatery', NULL, NULL, 0, NULL, NULL, 'Micro (Assets up to P3M)', 10000.00, 'quarterly', 2030.00, NULL, 0, '2026-03-09 06:32:24', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'for_payment', NULL, NULL, NULL, NULL, NULL, NULL, 0, 2026, NULL, NULL, '2026-03-08 21:20:32', '2026-03-08 22:32:24', NULL),
(24, 'Santiago', 'Gerry', NULL, 'Filipino', 'Single', NULL, NULL, NULL, 'santiagogerry79@gmail.com', 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, 'Victoria, Mallig, Isabela', 'busiñess', NULL, '2026-03-09', NULL, NULL, NULL, NULL, NULL, 'Retail', 'Eatery', NULL, NULL, 0, 'Partnership', 'Leased', 'Small (P3M - P15M)', 10000.00, 'quarterly', 2580.00, NULL, 0, '2026-03-09 06:03:43', 'Education', 'Zone 2 - Industrial', 'Ground Floor', 2.00, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'retired', 'Business Closure', '2026-03-17', 'Nalugi', '2026-03-16 19:55:13', 2, NULL, 0, 2026, NULL, NULL, '2026-03-08 21:21:42', '2026-03-16 19:55:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bpls_documents`
--

CREATE TABLE `bpls_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bpls_application_id` bigint(20) UNSIGNED NOT NULL,
  `document_type` varchar(255) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `mime_type` varchar(255) NOT NULL,
  `file_size` bigint(20) NOT NULL,
  `status` enum('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bpls_documents`
--

INSERT INTO `bpls_documents` (`id`, `bpls_application_id`, `document_type`, `file_name`, `file_path`, `mime_type`, `file_size`, `status`, `rejection_reason`, `created_at`, `updated_at`, `deleted_at`) VALUES
(3, 3, 'dti_sec_cda', '7a78937e-47bf-4dd5-be2d-75868659ee54.jpg', 'bpls/applications/3/documents/XBGDFTPuJHQIYMhiHbdh36e8kQDXLyotkkasKcoo.jpg', 'image/jpeg', 918753, 'verified', NULL, '2026-02-20 22:26:02', '2026-02-20 23:23:41', NULL),
(4, 3, 'barangay_clearance', '7a78937e-47bf-4dd5-be2d-75868659ee54.jpg', 'bpls/applications/3/documents/B2wU4omCuA55g1EcGM5ouXZovEmlEruyQapC4J7N.jpg', 'image/jpeg', 918753, 'verified', NULL, '2026-02-20 22:26:02', '2026-02-20 23:23:50', NULL),
(5, 3, 'community_tax', '7a78937e-47bf-4dd5-be2d-75868659ee54.jpg', 'bpls/applications/3/documents/wFmuProPvewWfQPBzsJ9l4SDeyasOEizLQ8ZIOZp.jpg', 'image/jpeg', 918753, 'verified', NULL, '2026-02-20 22:26:02', '2026-02-20 23:23:57', NULL),
(6, 4, 'dti_sec_cda', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/4/documents/VjWyh5dMFNrLWdv63JF9AbFE8ZMD6fYi7UcwiVN5.pdf', 'application/pdf', 50151, 'verified', NULL, '2026-02-20 23:48:56', '2026-02-20 23:49:35', NULL),
(7, 4, 'barangay_clearance', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/4/documents/fO4JOWtYdOMB1gS0eworV1cevQ5d1KVY8A259PVE.pdf', 'application/pdf', 50151, 'verified', NULL, '2026-02-20 23:48:56', '2026-02-20 23:49:38', NULL),
(8, 4, 'community_tax', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/4/documents/0Ce8HYB2iNYQKQ86pobUyp34TOJ3Vwe4vlnuPswW.pdf', 'application/pdf', 50151, 'verified', NULL, '2026-02-20 23:48:56', '2026-02-20 23:49:41', NULL),
(9, 5, 'dti_sec_cda', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/5/documents/qkKMH3tFaAwXGmxw7SATTrITQ4RKjkXTCCWe9dJs.pdf', 'application/pdf', 50151, 'verified', NULL, '2026-02-21 00:34:13', '2026-02-21 00:34:37', NULL),
(10, 5, 'barangay_clearance', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/5/documents/2NtEbXybDzrmQ6r6v1S1PQ4PuN5CMmuIcXo7Fdoa.pdf', 'application/pdf', 50151, 'verified', NULL, '2026-02-21 00:34:13', '2026-02-21 00:34:41', NULL),
(11, 5, 'community_tax', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/5/documents/J0RfkYIo24w6LsRX3E0DFvvHnNfP8lMPKYkey2GI.pdf', 'application/pdf', 50151, 'verified', NULL, '2026-02-21 00:34:13', '2026-02-21 00:34:44', NULL),
(12, 1, 'dti_sec_cda', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/1/documents/q72P6WQsemvNsj9uXiRTZddzy7bGp29e9gtoZkzZ.pdf', 'application/pdf', 50151, 'verified', NULL, '2026-02-21 18:00:33', '2026-02-21 18:02:24', NULL),
(13, 1, 'barangay_clearance', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/1/documents/SONh8AfL3fGEV2VmI8R5fvEVwc9qdElUIkk5vjnH.pdf', 'application/pdf', 50151, 'verified', NULL, '2026-02-21 18:00:39', '2026-02-21 18:02:26', NULL),
(14, 1, 'community_tax', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/1/documents/QRRoylYYpeWNrJ8JR0QtoNh7jHmUhe0isDfGKuBk.pdf', 'application/pdf', 50151, 'verified', NULL, '2026-02-21 18:00:45', '2026-02-21 18:02:29', NULL),
(15, 6, 'dti_sec_cda', 'TD-TD-00000019.pdf', 'bpls/applications/6/documents/VKDJnhvQMPyj5PQVco8niaYXgRervTeDRgMnrjKM.pdf', 'application/pdf', 3903, 'verified', NULL, '2026-02-22 00:45:47', '2026-02-22 00:46:35', NULL),
(16, 6, 'barangay_clearance', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/6/documents/3hLm7dPFAkroFxE94n9gdoSyGWkLnl0CYfrXH3x3.pdf', 'application/pdf', 50151, 'verified', NULL, '2026-02-22 00:45:47', '2026-02-22 00:46:32', NULL),
(17, 6, 'community_tax', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/6/documents/eWJXVubsSGFclscR0uyLAdr8rLLnUQpBaOKEoY8B.pdf', 'application/pdf', 50151, 'verified', NULL, '2026-02-22 00:45:47', '2026-02-22 00:46:41', NULL),
(18, 7, 'dti_sec_cda', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/7/documents/gwhQHB83ffZ6TfMpwcRDdwGy7SWyp7c2jBIlf8PB.pdf', 'application/pdf', 50151, 'pending', NULL, '2026-02-22 00:52:35', '2026-02-22 00:52:35', NULL),
(19, 7, 'barangay_clearance', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/7/documents/wer4EiFYbgXn4yqFLZCS7BwrAhlYdVwOV1Rv2eBp.pdf', 'application/pdf', 50151, 'pending', NULL, '2026-02-22 00:52:35', '2026-02-22 00:52:35', NULL),
(20, 7, 'community_tax', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/7/documents/dnTeO5yNXF2BI74NVlorGsAONpztQ2MZghfjkP1p.pdf', 'application/pdf', 50151, 'pending', NULL, '2026-02-22 00:52:35', '2026-02-22 00:52:35', NULL),
(21, 8, 'dti_sec_cda', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/8/documents/92Vl117xMRmt4pmm7G5rh92pEWw2PHDOialnuWLh.pdf', 'application/pdf', 50151, 'pending', NULL, '2026-02-22 17:28:27', '2026-02-22 18:45:17', '2026-02-22 18:45:17'),
(22, 8, 'barangay_clearance', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/8/documents/jOPZfHvXfbQGDuVQJvKWMB3Ct4saDvTchiIQ4ybT.pdf', 'application/pdf', 50151, 'pending', NULL, '2026-02-22 17:28:27', '2026-02-22 18:49:07', '2026-02-22 18:49:07'),
(23, 8, 'community_tax', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/8/documents/HxNZUI3ElspqzMKMbUTtCOUi5mdJiidpszTP0Nuv.pdf', 'application/pdf', 50151, 'pending', NULL, '2026-02-22 17:28:27', '2026-02-22 18:53:24', '2026-02-22 18:53:24'),
(24, 8, 'dti_sec_cda', 'TD-TD-00000019.pdf', 'bpls/applications/8/documents/R97s4MwXcAWr3fkO7OiCfPbUqkjmRdC8i2XZL4g8.pdf', 'application/pdf', 3903, 'pending', NULL, '2026-02-22 18:45:17', '2026-02-22 19:24:51', '2026-02-22 19:24:51'),
(25, 8, 'barangay_clearance', '7a78937e-47bf-4dd5-be2d-75868659ee54.jpg', 'bpls/applications/8/documents/vguJmso7u4ONb9BI8ew7YioswTRrOyT8IJk5iMjI.jpg', 'image/jpeg', 918753, 'pending', NULL, '2026-02-22 18:49:07', '2026-02-22 19:12:03', '2026-02-22 19:12:03'),
(26, 8, 'community_tax', '7a78937e-47bf-4dd5-be2d-75868659ee54.jpg', 'bpls/applications/8/documents/I7edSN8vu6o7hgzMZ6vRTEzMS32pgpDkf7eY5znD.jpg', 'image/jpeg', 918753, 'verified', NULL, '2026-02-22 18:53:24', '2026-02-22 19:25:25', NULL),
(27, 9, 'dti_sec_cda', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/9/documents/AJRzmg0LBv7PedJZgO5kQ6BkYtYSOd5oqsXulMWo.pdf', 'application/pdf', 50151, 'pending', NULL, '2026-02-22 19:09:45', '2026-02-22 19:21:40', '2026-02-22 19:21:40'),
(28, 9, 'barangay_clearance', '7a78937e-47bf-4dd5-be2d-75868659ee54.jpg', 'bpls/applications/9/documents/to13KPJE90Dm0ZUmmF0Cbzo7vrZc82jL6dbKRvuR.jpg', 'image/jpeg', 918753, 'rejected', 'bad', '2026-02-22 19:09:45', '2026-02-22 19:23:02', NULL),
(29, 9, 'community_tax', '7a78937e-47bf-4dd5-be2d-75868659ee54.jpg', 'bpls/applications/9/documents/cgCA8MDjbNlwk3GKB3YFhpso0vSSyKFVHBWBGFjw.jpg', 'image/jpeg', 918753, 'rejected', 'bad too', '2026-02-22 19:09:45', '2026-02-22 19:23:14', NULL),
(30, 8, 'barangay_clearance', 'parcel-list-2026-02-16 (1).pdf', 'bpls/applications/8/documents/i4iBjIZoGWfvVj2rEgKslv5jstFXIOf1148ibuVJ.pdf', 'application/pdf', 879065, 'verified', NULL, '2026-02-22 19:12:03', '2026-02-22 19:25:28', NULL),
(31, 9, 'dti_sec_cda', '7a78937e-47bf-4dd5-be2d-75868659ee54.jpg', 'bpls/applications/9/documents/PmT9wDJYZFVK0pZ44yMD3nrgDLTaOXorj25WorfF.jpg', 'image/jpeg', 918753, 'pending', NULL, '2026-02-22 19:21:40', '2026-02-22 19:21:40', NULL),
(32, 8, 'dti_sec_cda', '7a78937e-47bf-4dd5-be2d-75868659ee54.jpg', 'bpls/applications/8/documents/ZLDleZpndrMZ9mrhUdLhlbqGMw7fQ9h10GAfIpdD.jpg', 'image/jpeg', 918753, 'verified', NULL, '2026-02-22 19:24:51', '2026-02-22 19:25:30', NULL),
(33, 10, 'dti_sec_cda', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/10/documents/NgSLqMQjPal6724g005TtOZUbUdFc6qeDzKQOObp.pdf', 'application/pdf', 50151, 'verified', NULL, '2026-02-22 19:59:50', '2026-02-22 19:59:59', NULL),
(34, 10, 'barangay_clearance', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/10/documents/5qJ4Gm9ZVksjq2nXRFIyVdX65RWdAZddBCukpJH1.pdf', 'application/pdf', 50151, 'verified', NULL, '2026-02-22 19:59:50', '2026-02-22 20:00:00', NULL),
(35, 10, 'community_tax', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/10/documents/B61GPnTuF7igfGSYj9SlNiU78b1NhBa6wZyCMPcT.pdf', 'application/pdf', 50151, 'verified', NULL, '2026-02-22 19:59:50', '2026-02-22 20:00:01', NULL),
(36, 11, 'dti_sec_cda', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/11/documents/LObdQlcc80fG0DzlElOvJgsQlhpBYdwrB7fUqNyI.pdf', 'application/pdf', 50151, 'verified', NULL, '2026-02-22 21:45:24', '2026-02-22 21:45:46', NULL),
(37, 11, 'barangay_clearance', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/11/documents/dakzb1P98CnKiodlE9y8EO3F6UuEFdRmmlatUipP.pdf', 'application/pdf', 50151, 'verified', NULL, '2026-02-22 21:45:24', '2026-02-22 21:45:49', NULL),
(38, 11, 'community_tax', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/11/documents/GY50mnbg2eDihTbtfjXulm6VGZ0SYXVPgdTnfpdG.pdf', 'application/pdf', 50151, 'verified', NULL, '2026-02-22 21:45:24', '2026-02-22 21:45:51', NULL),
(39, 12, 'dti_sec_cda', '7a78937e-47bf-4dd5-be2d-75868659ee54.jpg', 'bpls/applications/12/documents/lIa6L2BOzqQ6BNnzRaTqfFaOgwESKUZl30cnrI4k.jpg', 'image/jpeg', 918753, 'verified', NULL, '2026-02-22 23:46:05', '2026-02-22 23:47:29', NULL),
(40, 12, 'barangay_clearance', '7a78937e-47bf-4dd5-be2d-75868659ee54.jpg', 'bpls/applications/12/documents/DFwoFTyx0SI1ihhPJMwb0EdEpiBedKq91Mslytb2.jpg', 'image/jpeg', 918753, 'verified', NULL, '2026-02-22 23:46:05', '2026-02-22 23:47:32', NULL),
(41, 12, 'community_tax', '7a78937e-47bf-4dd5-be2d-75868659ee54.jpg', 'bpls/applications/12/documents/3IS0y3qzSkhufB6wCl337MHjfdAZFeOjLWUOolYi.jpg', 'image/jpeg', 918753, 'verified', NULL, '2026-02-22 23:46:05', '2026-02-22 23:47:34', NULL),
(42, 13, 'dti_sec_cda', '7a78937e-47bf-4dd5-be2d-75868659ee54.jpg', 'bpls/applications/13/documents/CFVfJcOZrreaXZsZJZjiJr4fRvBoa3EBFy0AZ7Lt.jpg', 'image/jpeg', 918753, 'verified', NULL, '2026-02-23 00:20:39', '2026-02-23 01:12:32', NULL),
(43, 13, 'barangay_clearance', '7a78937e-47bf-4dd5-be2d-75868659ee54.jpg', 'bpls/applications/13/documents/NIkMtlF37uCnTGTxQDB4DKyQvmPEKXAK9oGAB6vt.jpg', 'image/jpeg', 918753, 'verified', NULL, '2026-02-23 00:20:39', '2026-02-23 01:12:33', NULL),
(44, 13, 'community_tax', 'TD-TD-00000004.pdf', 'bpls/applications/13/documents/cd9WmS07XHDkuKFMSkjMgXStrp3w3bNfmLahNgAk.pdf', 'application/pdf', 4422, 'verified', NULL, '2026-02-23 00:20:39', '2026-02-23 01:12:33', NULL),
(45, 14, 'dti_sec_cda', 'TD-TD-00000019.pdf', 'bpls/applications/14/documents/QjwOqTpzTQcmoHAh7qOGpgSxiI3ism2uuLppvTx7.pdf', 'application/pdf', 3903, 'verified', NULL, '2026-02-23 01:07:24', '2026-02-23 01:09:09', NULL),
(46, 14, 'barangay_clearance', 'TD-TD-00000019.pdf', 'bpls/applications/14/documents/Jbs0rp18bXz0UvEa72PVzGjvmxKFxeYOpMhY1DaS.pdf', 'application/pdf', 3903, 'verified', NULL, '2026-02-23 01:07:24', '2026-02-23 01:09:12', NULL),
(47, 14, 'community_tax', 'TD-TD-00000019.pdf', 'bpls/applications/14/documents/zQjLijpXpSbYmxUFgC5SllzdCHEKDPggaJvKVmJQ.pdf', 'application/pdf', 3903, 'verified', NULL, '2026-02-23 01:07:24', '2026-02-23 01:09:14', NULL),
(48, 15, 'dti_sec_cda', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/15/documents/vA8RBVixwOIVjVKfnqNTIIEL1kGveF9BCQje9U8o.pdf', 'application/pdf', 50151, 'verified', NULL, '2026-02-23 23:13:03', '2026-02-23 23:13:14', NULL),
(49, 15, 'barangay_clearance', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/15/documents/WsJMwptmv5xKJZb8u6WCbxDW8ptR487IgCidBV3a.pdf', 'application/pdf', 50151, 'verified', NULL, '2026-02-23 23:13:03', '2026-02-23 23:13:22', NULL),
(50, 15, 'community_tax', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/15/documents/UdA1KfUjS8DcmovsdpLphnQ3gFsDau18yoYjz3rQ.pdf', 'application/pdf', 50151, 'verified', NULL, '2026-02-23 23:13:03', '2026-02-23 23:13:23', NULL),
(51, 16, 'dti_sec_cda', 'BusinessPermit-APP-2026-00014-2026.pdf', 'bpls/applications/16/documents/ZD4lzNfWQAdO0HSI6u43A4iJsFEEMi1WuKyYR0U6.pdf', 'application/pdf', 41955, 'verified', NULL, '2026-02-24 18:52:39', '2026-02-24 18:53:21', NULL),
(52, 16, 'barangay_clearance', 'BusinessPermit-APP-2026-00014-2026.pdf', 'bpls/applications/16/documents/WM01CDQ2ZTpKBViqPVkxGBxRrs74fogYEWQeBMkx.pdf', 'application/pdf', 41955, 'verified', NULL, '2026-02-24 18:52:39', '2026-02-24 18:53:25', NULL),
(53, 16, 'community_tax', 'BusinessPermit-APP-2026-00014-2026.pdf', 'bpls/applications/16/documents/2YjF1KMDNqfhNeEiL6kq12O8ySsuGUv87HO7K85O.pdf', 'application/pdf', 41955, 'verified', NULL, '2026-02-24 18:52:39', '2026-02-24 18:53:33', NULL),
(54, 17, 'dti_sec_cda', 'BusinessPermit-APP-2026-00014-2026.pdf', 'bpls/applications/17/documents/zfq6MCMbvBTqBhbxJz80VQw9ejCX55MqpzzTXRW7.pdf', 'application/pdf', 41955, 'verified', NULL, '2026-02-24 20:06:19', '2026-02-24 22:32:45', NULL),
(55, 17, 'barangay_clearance', 'BusinessPermit-APP-2026-00014-2026.pdf', 'bpls/applications/17/documents/imgVaMhlt8rkYnCSCR6gLV0juh7PjGXGdddicOWk.pdf', 'application/pdf', 41955, 'verified', NULL, '2026-02-24 20:06:19', '2026-02-24 22:32:48', NULL),
(56, 17, 'community_tax', 'BusinessPermit-APP-2026-00014-2026.pdf', 'bpls/applications/17/documents/CnIKMLAu0IpB066OxcK47BsPbnM68AKkcNDnLEpL.pdf', 'application/pdf', 41955, 'verified', NULL, '2026-02-24 20:06:19', '2026-02-24 22:32:52', NULL),
(57, 18, 'dti_sec_cda', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/18/documents/QJZIIafUTVb9FtBJREeo0MiSXW6amQp1tSY5bkpr.pdf', 'application/pdf', 50151, 'pending', NULL, '2026-02-25 01:21:55', '2026-02-25 01:21:55', NULL),
(58, 18, 'barangay_clearance', 'TD-TD-00000019.pdf', 'bpls/applications/18/documents/CQYctUX99fias8viVRftRtsxNHmmYNQGdQDLhVh9.pdf', 'application/pdf', 3903, 'pending', NULL, '2026-02-25 01:21:55', '2026-02-25 01:21:55', NULL),
(59, 18, 'community_tax', 'TD-TD-00000019.pdf', 'bpls/applications/18/documents/zzQpbm83VftkK5cVRWNrNXb1gs12Mkn72P8XiePK.pdf', 'application/pdf', 3903, 'pending', NULL, '2026-02-25 01:21:55', '2026-02-25 01:21:55', NULL),
(60, 19, 'dti_sec_cda', 'BusinessPermit-APP-2026-00002-2026.pdf', 'bpls/applications/19/documents/yGQuUUnM2uhWG1ELHtbljl6JMEIDhYr7qmU1FLhq.pdf', 'application/pdf', 50151, 'verified', NULL, '2026-02-25 01:38:01', '2026-03-08 21:39:10', NULL),
(61, 19, 'barangay_clearance', 'TD-TD-00000019.pdf', 'bpls/applications/19/documents/IszLW50d9wEtf716QNRYDVfcXkGT4JOjERIpI7qT.pdf', 'application/pdf', 3903, 'verified', NULL, '2026-02-25 01:38:01', '2026-03-08 21:39:10', NULL),
(62, 19, 'community_tax', 'TD-TD-00000019.pdf', 'bpls/applications/19/documents/JXCw3fvfOzXwfdXdNuDmt9EQK6iSrREMGgR4pO9q.pdf', 'application/pdf', 3903, 'verified', NULL, '2026-02-25 01:38:01', '2026-03-08 21:39:13', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bpls_entry_benefits`
--

CREATE TABLE `bpls_entry_benefits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `business_entry_id` bigint(20) UNSIGNED NOT NULL,
  `benefit_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bpls_entry_benefits`
--

INSERT INTO `bpls_entry_benefits` (`id`, `business_entry_id`, `benefit_id`, `created_at`, `updated_at`) VALUES
(2, 24, 1, '2026-03-09 05:30:18', '2026-03-09 05:30:18'),
(3, 23, 1, '2026-03-18 19:35:53', '2026-03-18 19:35:53');

-- --------------------------------------------------------

--
-- Table structure for table `bpls_online_applications`
--

CREATE TABLE `bpls_online_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `application_number` varchar(255) NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `bpls_business_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bpls_owner_id` bigint(20) UNSIGNED DEFAULT NULL,
  `application_type` enum('new','renewal','amendment') NOT NULL DEFAULT 'new',
  `discount_claimed` tinyint(1) NOT NULL DEFAULT 0,
  `permit_year` int(11) NOT NULL DEFAULT 2026,
  `workflow_status` enum('draft','submitted','returned','verified','assessed','paid','approved','rejected') NOT NULL DEFAULT 'draft',
  `assessment_amount` decimal(12,2) DEFAULT NULL,
  `assessment_notes` text DEFAULT NULL,
  `ors_confirmed` tinyint(1) NOT NULL DEFAULT 0,
  `mode_of_payment` varchar(255) DEFAULT NULL COMMENT 'quarterly | semi_annual | annual',
  `or_number` varchar(100) DEFAULT NULL,
  `permit_notes` text DEFAULT NULL,
  `signatory_id` bigint(20) UNSIGNED DEFAULT NULL,
  `signatory_name` varchar(255) DEFAULT NULL,
  `signatory_position` varchar(255) DEFAULT NULL,
  `permit_valid_from` date DEFAULT NULL,
  `permit_valid_until` date DEFAULT NULL,
  `submitted_at` timestamp NULL DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `assessed_at` timestamp NULL DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `assessed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bpls_online_applications`
--

INSERT INTO `bpls_online_applications` (`id`, `application_number`, `client_id`, `bpls_business_id`, `bpls_owner_id`, `application_type`, `discount_claimed`, `permit_year`, `workflow_status`, `assessment_amount`, `assessment_notes`, `ors_confirmed`, `mode_of_payment`, `or_number`, `permit_notes`, `signatory_id`, `signatory_name`, `signatory_position`, `permit_valid_from`, `permit_valid_until`, `submitted_at`, `verified_at`, `assessed_at`, `paid_at`, `approved_at`, `verified_by`, `assessed_by`, `approved_by`, `remarks`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'APP-2026-00001', 1, 1, 1, 'new', 0, 2026, 'approved', 5000.00, NULL, 0, NULL, 'OR12345', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 18:00:53', '2026-02-21 18:02:33', '2026-02-21 18:02:52', '2026-02-21 18:03:02', '2026-02-21 18:03:30', 2, 2, 2, NULL, '2026-02-20 21:56:18', '2026-02-21 18:03:30', NULL),
(3, 'APP-2026-00002', 1, 4, 5, 'new', 0, 2026, 'approved', 500.00, NULL, 0, NULL, 'OR1234', 'Goods', NULL, NULL, NULL, NULL, NULL, '2026-02-20 22:26:02', '2026-02-20 23:25:34', '2026-02-20 23:28:00', '2026-02-20 23:30:00', '2026-02-20 23:30:16', 2, 2, 2, NULL, '2026-02-20 22:26:02', '2026-02-20 23:30:16', NULL),
(4, 'APP-2026-00003', 1, 5, 6, 'new', 0, 2026, 'approved', 5000.00, NULL, 0, NULL, 'OR12344', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-20 23:48:56', '2026-02-20 23:49:48', '2026-02-21 00:16:25', '2026-02-21 20:54:57', '2026-02-22 00:27:45', 2, 2, 2, NULL, '2026-02-20 23:48:56', '2026-02-22 00:27:45', NULL),
(5, 'APP-2026-00004', 1, 6, 7, 'new', 0, 2026, 'approved', 1000.00, NULL, 0, 'quarterly', 'OR1234', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-21 00:34:13', '2026-02-21 00:34:47', '2026-02-21 18:31:45', '2026-02-21 20:11:29', '2026-02-21 20:12:07', 2, 2, 2, NULL, '2026-02-21 00:34:13', '2026-02-21 20:12:07', NULL),
(6, 'APP-2026-00005', 1, 7, 8, 'new', 0, 2026, 'approved', 12220.00, NULL, 0, 'quarterly', 'OR1234', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-22 00:45:47', '2026-02-22 00:46:44', '2026-02-22 00:46:55', '2026-02-22 00:47:23', '2026-02-22 00:47:55', 2, 2, 2, NULL, '2026-02-22 00:45:47', '2026-02-22 00:47:55', NULL),
(7, 'APP-2026-00006', 2, 8, 9, 'new', 0, 2026, 'rejected', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-22 00:52:35', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'lack of info', '2026-02-22 00:52:35', '2026-02-22 00:52:58', NULL),
(8, 'APP-2026-00007', 2, 9, 10, 'new', 0, 2026, 'approved', 5000.00, NULL, 0, 'quarterly', '123400', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-22 19:12:09', '2026-02-22 19:25:34', NULL, '2026-02-22 21:18:09', '2026-02-22 21:26:22', 2, NULL, 2, 'revise', '2026-02-22 17:28:27', '2026-02-22 21:26:22', NULL),
(9, 'APP-2026-00008', 2, 10, 11, 'new', 0, 2026, 'rejected', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-22 19:09:45', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'bad docs', '2026-02-22 19:09:45', '2026-02-22 19:23:25', NULL),
(10, 'APP-2026-00009', 2, 11, 12, 'new', 0, 2026, 'approved', 2000.00, NULL, 0, 'quarterly', '123404', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-22 19:59:50', '2026-02-22 20:00:11', NULL, '2026-02-22 21:08:42', '2026-02-22 21:08:47', 2, NULL, 2, NULL, '2026-02-22 19:59:50', '2026-02-22 21:08:47', NULL),
(11, 'APP-2026-00010', 2, 12, 13, 'new', 0, 2026, 'approved', 50000.00, NULL, 1, 'semi_annual', '123408', NULL, 1, 'John', 'BPLS head', '2026-01-01', '2026-12-31', '2026-02-22 21:45:24', '2026-02-22 21:45:53', '2026-02-22 21:46:21', '2026-02-22 21:46:39', '2026-02-22 23:30:45', 2, NULL, 2, NULL, '2026-02-22 21:45:24', '2026-02-22 23:30:45', NULL),
(12, 'APP-2026-00011', 2, 12, 13, 'renewal', 0, 2026, 'approved', 2000.00, NULL, 1, 'quarterly', '123410', NULL, 1, 'John', 'BPLS head', '2026-01-01', '2026-12-31', '2026-02-22 23:46:04', '2026-02-22 23:47:37', '2026-02-22 23:48:13', '2026-02-22 23:48:20', '2026-02-22 23:48:39', 2, NULL, 2, NULL, '2026-02-22 23:46:05', '2026-02-22 23:48:39', NULL),
(13, 'APP-2026-00012', 2, 13, 14, 'new', 0, 2026, 'approved', 10000.00, NULL, 1, 'annual', '123416', NULL, 1, 'John', 'BPLS head', '2026-01-01', '2026-12-31', '2026-02-23 00:20:39', '2026-02-23 01:12:38', '2026-02-23 01:13:43', '2026-02-23 01:13:46', '2026-02-23 01:14:01', 2, NULL, 2, NULL, '2026-02-23 00:20:39', '2026-02-23 01:14:01', NULL),
(14, 'APP-2026-00013', 2, 14, 15, 'new', 0, 2026, 'approved', 5000.00, NULL, 1, 'semi_annual', '123414', NULL, 1, 'John', 'BPLS head', '2026-01-01', '2026-12-31', '2026-02-23 01:07:24', '2026-02-23 01:09:17', '2026-02-23 01:09:32', '2026-02-23 01:09:37', '2026-02-23 01:09:49', 2, NULL, 2, NULL, '2026-02-23 01:07:24', '2026-02-23 01:09:49', NULL),
(15, 'APP-2026-00014', 2, 16, 17, 'new', 0, 2026, 'approved', 10000.00, NULL, 1, 'quarterly', '123451', NULL, 1, 'John', 'BPLS head', '2026-01-01', '2026-12-31', '2026-02-23 23:13:03', '2026-02-23 23:13:26', '2026-02-23 23:13:45', '2026-02-23 23:13:49', '2026-02-23 23:14:04', 2, NULL, 2, NULL, '2026-02-23 23:13:03', '2026-02-23 23:14:04', NULL),
(16, 'APP-2026-00015', 2, 17, 18, 'new', 0, 2026, 'verified', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-24 18:52:39', '2026-02-24 18:53:36', NULL, NULL, NULL, 2, NULL, NULL, NULL, '2026-02-24 18:52:39', '2026-02-24 18:53:36', NULL),
(17, 'APP-2026-00016', 2, 16, 17, 'renewal', 0, 2026, 'approved', 50000.00, NULL, 1, 'quarterly', '123455', NULL, 1, 'John', 'BPLS head', '2026-01-01', '2026-12-31', '2026-02-24 20:06:19', '2026-02-24 22:32:56', '2026-02-24 22:33:10', '2026-02-24 22:33:13', '2026-02-24 22:33:19', 2, NULL, 2, NULL, '2026-02-24 20:06:19', '2026-02-24 22:33:19', NULL),
(18, 'APP-2026-00017', 2, 19, 20, 'new', 0, 2026, 'submitted', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-25 01:21:55', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-25 01:21:55', '2026-02-25 01:21:55', NULL),
(19, 'APP-2026-00018', 2, 21, 22, 'new', 0, 2026, 'verified', NULL, NULL, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-25 01:38:01', '2026-03-08 21:39:18', NULL, NULL, NULL, 2, NULL, NULL, NULL, '2026-02-25 01:38:01', '2026-03-08 21:39:18', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bpls_online_payments`
--

CREATE TABLE `bpls_online_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `bpls_application_id` bigint(20) UNSIGNED NOT NULL,
  `bpls_assessment_id` bigint(20) UNSIGNED DEFAULT NULL,
  `reference_number` varchar(255) NOT NULL,
  `amount_paid` decimal(12,2) NOT NULL,
  `payment_year` int(11) NOT NULL DEFAULT 2026,
  `installment_number` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `installment_total` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `renewal_cycle` int(11) NOT NULL DEFAULT 0,
  `payment_method` varchar(255) NOT NULL,
  `status` enum('pending','paid','failed') NOT NULL DEFAULT 'pending',
  `gateway_transaction_id` varchar(255) DEFAULT NULL,
  `paymongo_payment_intent_id` varchar(255) DEFAULT NULL,
  `paymongo_checkout_url` varchar(255) DEFAULT NULL,
  `gateway_response` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`gateway_response`)),
  `paid_at` timestamp NULL DEFAULT NULL,
  `or_number` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bpls_online_payments`
--

INSERT INTO `bpls_online_payments` (`id`, `bpls_application_id`, `bpls_assessment_id`, `reference_number`, `amount_paid`, `payment_year`, `installment_number`, `installment_total`, `renewal_cycle`, `payment_method`, `status`, `gateway_transaction_id`, `paymongo_payment_intent_id`, `paymongo_checkout_url`, `gateway_response`, `paid_at`, `or_number`, `created_at`, `updated_at`) VALUES
(1, 5, NULL, 'PAY-20260222-315A23', 250.00, 2026, 1, 4, 0, 'gcash', 'pending', 'link_u98Y5TY6LxPxoD8orZhuDgkB', NULL, NULL, '{\"id\":\"link_u98Y5TY6LxPxoD8orZhuDgkB\",\"type\":\"link\",\"attributes\":{\"amount\":25000,\"archived\":false,\"currency\":\"PHP\",\"description\":\"Business Permit Fee \\u2014 APP-2026-00004 (Installment 1 of 4)\",\"livemode\":false,\"fee\":0,\"remarks\":\"PAY-20260222-315A23\",\"status\":\"unpaid\",\"tax_amount\":null,\"taxes\":[],\"checkout_url\":\"https:\\/\\/pm.link\\/org-NAEnmr2BnkcgqAztMHrZTCN1\\/test\\/hNqP5G1\",\"reference_number\":\"hNqP5G1\",\"created_at\":1771741384,\"updated_at\":1771741384,\"payments\":[]}}', NULL, NULL, '2026-02-21 22:12:35', '2026-02-21 22:23:05'),
(2, 6, NULL, 'PAY-20260222-D221EB', 3055.00, 2026, 1, 4, 0, 'gcash', 'pending', 'link_9qWSfqvJgG8Q9p8rFnjESqDR', NULL, NULL, '{\"id\":\"link_9qWSfqvJgG8Q9p8rFnjESqDR\",\"type\":\"link\",\"attributes\":{\"amount\":305500,\"archived\":false,\"currency\":\"PHP\",\"description\":\"Business Permit Fee \\u2014 APP-2026-00005 (Installment 1 of 4)\",\"livemode\":false,\"fee\":0,\"remarks\":\"PAY-20260222-D221EB\",\"status\":\"unpaid\",\"tax_amount\":null,\"taxes\":[],\"checkout_url\":\"https:\\/\\/pm.link\\/org-NAEnmr2BnkcgqAztMHrZTCN1\\/test\\/ixmkdm4\",\"reference_number\":\"ixmkdm4\",\"created_at\":1771750109,\"updated_at\":1771750109,\"payments\":[]}}', NULL, NULL, '2026-02-22 00:48:29', '2026-02-22 00:48:29'),
(3, 14, NULL, 'PAY-20260224-6E6162', 2500.00, 2026, 1, 2, 0, 'over_the_counter', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-23 21:53:10', '2026-02-23 22:21:32'),
(4, 14, NULL, 'PAY-20260224-D7C176', 2500.00, 2026, 2, 2, 0, 'over_the_counter', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-23 22:20:29', '2026-02-23 22:21:24'),
(5, 13, NULL, 'PAY-20260224-A198AA', 10000.00, 2026, 1, 1, 0, 'maya', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-23 22:49:46', '2026-02-23 23:00:20'),
(6, 15, NULL, 'PAY-20260224-1472BE', 2500.00, 2026, 1, 4, 0, 'gcash', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-23 23:14:41', '2026-02-23 23:14:41'),
(7, 15, NULL, 'PAY-20260224-B22B50', 2500.00, 2026, 2, 4, 0, 'gcash', 'pending', NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-23 23:41:47', '2026-02-23 23:41:47'),
(8, 15, NULL, 'PAY-20260224-38AD42', 2500.00, 2026, 3, 4, 0, 'gcash', 'paid', 'link_KtB54GbAKnJmJYmEAUiTrWRd', 'link_KtB54GbAKnJmJYmEAUiTrWRd', 'https://pm.link/org-NAEnmr2BnkcgqAztMHrZTCN1/test/Svjwqap', '{\"id\":\"link_KtB54GbAKnJmJYmEAUiTrWRd\",\"type\":\"link\",\"attributes\":{\"amount\":250000,\"archived\":false,\"currency\":\"PHP\",\"description\":\"Business Permit \\u2014 APP-2026-00014 (Installment 3 of 4)\",\"livemode\":false,\"fee\":6250,\"remarks\":\"PAY-20260224-38AD42\",\"status\":\"paid\",\"tax_amount\":null,\"taxes\":[],\"checkout_url\":\"https:\\/\\/pm.link\\/org-NAEnmr2BnkcgqAztMHrZTCN1\\/test\\/Svjwqap\",\"reference_number\":\"Svjwqap\",\"created_at\":1771919554,\"updated_at\":1771919554,\"payments\":[{\"data\":{\"id\":\"pay_m7KGVpc1CLM7Wja5EhhLYmbZ\",\"type\":\"payment\",\"attributes\":{\"access_url\":null,\"amount\":250000,\"balance_transaction_id\":\"bal_txn_gGomJAuuxxBMDXYWjS5GEmP9\",\"billing\":{\"address\":{\"city\":\"Taguig\",\"country\":\"PH\",\"line1\":\"12th floor The Trade and Financial Tower u1206\",\"line2\":\"32nd street and 7th Avenue\",\"postal_code\":\"1630\",\"state\":\"Bonifacio Global City\"},\"email\":\"gerr@gmail.com\",\"name\":\"Gerry\",\"phone\":\"0987654321\"},\"currency\":\"PHP\",\"description\":\"Business Permit \\u2014 APP-2026-00014 (Installment 3 of 4)\",\"digital_withholding_vat_amount\":0,\"disputed\":false,\"external_reference_number\":\"Svjwqap\",\"fee\":6250,\"instant_settlement\":null,\"livemode\":false,\"net_amount\":243750,\"origin\":\"links\",\"payment_intent_id\":null,\"payout\":null,\"source\":{\"id\":\"src_LTvwnVR7GbiGKwtDJtz8ktLv\",\"type\":\"gcash\",\"provider\":{\"id\":null},\"provider_id\":null},\"statement_descriptor\":\"ROHAN PANAY TAAR\",\"status\":\"paid\",\"tax_amount\":null,\"metadata\":{\"pm_reference_number\":\"Svjwqap\"},\"promotion\":null,\"refunds\":[],\"taxes\":[],\"available_at\":1772096400,\"created_at\":1771919581,\"credited_at\":1773277200,\"paid_at\":1771919581,\"updated_at\":1771919581}}}]}}', '2026-03-09 02:18:08', NULL, '2026-02-23 23:52:35', '2026-03-09 02:18:08'),
(9, 17, NULL, 'PAY-20260225-F349C8', 12500.00, 2026, 1, 4, 0, 'gcash', 'paid', 'link_Ec8MUMVzYQX6CmaY63YYdTaj', 'link_Ec8MUMVzYQX6CmaY63YYdTaj', 'https://pm.link/org-NAEnmr2BnkcgqAztMHrZTCN1/test/rPoBF8D', '{\"id\":\"link_Ec8MUMVzYQX6CmaY63YYdTaj\",\"type\":\"link\",\"attributes\":{\"amount\":1250000,\"archived\":false,\"currency\":\"PHP\",\"description\":\"Business Permit \\u2014 APP-2026-00016 (Installment 1 of 4)\",\"livemode\":false,\"fee\":31250,\"remarks\":\"PAY-20260225-F349C8\",\"status\":\"paid\",\"tax_amount\":null,\"taxes\":[],\"checkout_url\":\"https:\\/\\/pm.link\\/org-NAEnmr2BnkcgqAztMHrZTCN1\\/test\\/rPoBF8D\",\"reference_number\":\"rPoBF8D\",\"created_at\":1772010760,\"updated_at\":1772010760,\"payments\":[{\"data\":{\"id\":\"pay_H829HyGNQd9nuV7Z5Dc8PNGx\",\"type\":\"payment\",\"attributes\":{\"access_url\":null,\"amount\":1250000,\"balance_transaction_id\":\"bal_txn_KTEpT8x28jdU1TfSgUCJWc1K\",\"billing\":{\"address\":{\"city\":\"Taguig\",\"country\":\"PH\",\"line1\":\"12th floor The Trade and Financial Tower u1206\",\"line2\":\"32nd street and 7th Avenue\",\"postal_code\":\"1630\",\"state\":\"Bonifacio Global City\"},\"email\":\"gerr@sasadsa.com\",\"name\":\"asdasd\",\"phone\":\"0987654321\"},\"currency\":\"PHP\",\"description\":\"Business Permit \\u2014 APP-2026-00016 (Installment 1 of 4)\",\"digital_withholding_vat_amount\":0,\"disputed\":false,\"external_reference_number\":\"rPoBF8D\",\"fee\":31250,\"instant_settlement\":null,\"livemode\":false,\"net_amount\":1218750,\"origin\":\"links\",\"payment_intent_id\":null,\"payout\":null,\"source\":{\"id\":\"src_HnKAH3t3P1F3jjnrijquubdq\",\"type\":\"gcash\",\"provider\":{\"id\":null},\"provider_id\":null},\"statement_descriptor\":\"ROHAN PANAY TAAR\",\"status\":\"paid\",\"tax_amount\":null,\"metadata\":{\"pm_reference_number\":\"rPoBF8D\"},\"promotion\":null,\"refunds\":[],\"taxes\":[],\"available_at\":1772442000,\"created_at\":1772010797,\"credited_at\":1773277200,\"paid_at\":1772010797,\"updated_at\":1772010797}}}]}}', '2026-03-09 02:18:23', NULL, '2026-02-25 01:12:31', '2026-03-09 02:18:23');

-- --------------------------------------------------------

--
-- Table structure for table `bpls_owners`
--

CREATE TABLE `bpls_owners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `citizenship` varchar(255) DEFAULT NULL,
  `civil_status` varchar(255) DEFAULT NULL,
  `gender` varchar(255) DEFAULT NULL,
  `birthdate` date DEFAULT NULL,
  `mobile_no` varchar(255) DEFAULT NULL,
  `email` varchar(255) DEFAULT NULL,
  `is_pwd` tinyint(1) NOT NULL DEFAULT 0,
  `is_4ps` tinyint(1) NOT NULL DEFAULT 0,
  `is_solo_parent` tinyint(1) NOT NULL DEFAULT 0,
  `is_senior` tinyint(1) NOT NULL DEFAULT 0,
  `is_bmbe` tinyint(1) NOT NULL DEFAULT 0,
  `is_cooperative` tinyint(1) NOT NULL DEFAULT 0,
  `is_vaccine` tinyint(1) NOT NULL DEFAULT 0,
  `discount_10` tinyint(1) NOT NULL DEFAULT 0,
  `discount_5` tinyint(1) NOT NULL DEFAULT 0,
  `region` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `municipality` varchar(255) DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `emergency_contact_person` varchar(255) DEFAULT NULL,
  `emergency_mobile` varchar(255) DEFAULT NULL,
  `emergency_email` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bpls_owners`
--

INSERT INTO `bpls_owners` (`id`, `last_name`, `first_name`, `middle_name`, `citizenship`, `civil_status`, `gender`, `birthdate`, `mobile_no`, `email`, `is_pwd`, `is_4ps`, `is_solo_parent`, `is_senior`, `is_bmbe`, `is_cooperative`, `is_vaccine`, `discount_10`, `discount_5`, `region`, `province`, `municipality`, `barangay`, `street`, `emergency_contact_person`, `emergency_mobile`, `emergency_email`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Dela Cruz', 'Juan', 'Santos', 'Filipino', 'Single', 'Male', '2026-02-21', '09123456789', 'juan@example.com', 1, 0, 0, 0, 0, 0, 0, 0, 0, '2', 'sabel', 'lig', 'vic', '223', 'UIOP POIUY QWERTY', '09123456789', 'juan@example.com', '2026-02-20 21:56:18', '2026-02-20 21:56:18', NULL),
(5, 'Dela Cruz', 'Juan', 'Santos', 'Filipino', 'Single', NULL, '2026-02-21', '09123456789', 'juan@example.com', 0, 0, 1, 0, 0, 0, 0, 0, 0, '2', 'sabel', 'lig', 'vic', '223', 'UIOP POIUY QWERTY', '09123456789', 'juan@example.com', '2026-02-20 22:26:02', '2026-02-20 22:26:02', NULL),
(6, 'Dela vcru', 'ewe', 'qwe', 'Filipino', 'Single', 'Male', '2026-02-21', '0987654321', 'qwerty@gmail.com', 1, 0, 0, 0, 0, 0, 0, 0, 0, '2', 'qwe', 'qwe', 'qwe', 'qwe', NULL, NULL, NULL, '2026-02-20 23:48:56', '2026-02-20 23:48:56', NULL),
(7, 'qweqw', 'qweqwe', 'qweqwe', 'Filipino', 'Single', 'Female', '2026-02-21', '987654321', 'qwe@123123', 0, 0, 1, 0, 0, 0, 0, 0, 0, 'qwe', 'qwe', 'qwe', 'qwe', 'qwe', NULL, NULL, NULL, '2026-02-21 00:34:13', '2026-02-21 00:34:13', NULL),
(8, 'QWER', 'QWER', 'QWER', 'Filipino', 'Married', 'Female', '2026-02-22', '0987654321', 'qwe@123123', 1, 0, 0, 1, 0, 0, 0, 0, 0, '123', '123', '123', '123', '123', NULL, NULL, NULL, '2026-02-22 00:45:47', '2026-02-22 00:45:47', NULL),
(9, 'qwe', 'qwe', 'qweqe', 'Filipino', 'Married', 'Female', '2026-02-22', '1456789', NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-22 00:52:35', '2026-02-22 00:52:35', NULL),
(10, 'delass', 'John', 'Santos', 'Filipino', 'Single', 'Male', '2026-02-23', '0987654321', 'john@gmail.com', 0, 0, 1, 1, 0, 0, 0, 0, 0, '2', '2', '2', '2', '2', NULL, NULL, NULL, '2026-02-22 17:28:27', '2026-02-22 18:59:29', NULL),
(11, 'sasa', 'rara', 'asd', 'Filipino', 'Married', 'Male', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-22 19:09:45', '2026-02-22 19:21:40', NULL),
(12, 'asd', 'asd', 'asd', 'Filipino', 'Single', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-22 19:59:50', '2026-02-22 19:59:50', NULL),
(13, 'dada', 'daa', 'daad', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-22 21:45:24', '2026-02-22 21:45:24', NULL),
(14, 'qwe', 'qwe', 'qwe', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-23 00:20:39', '2026-02-23 00:20:39', NULL),
(15, 'asd', 'asd', 'asd', 'Foreign National', 'Widowed', NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-23 01:07:24', '2026-02-23 01:07:24', NULL),
(16, 'qwe', 'qwe', 'qwe', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-23 21:06:46', '2026-02-23 21:06:46', NULL),
(17, 'QWEEWQEQWE', 'QWEQWEQWEQW', 'QWEQWEWQE', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-02-23 23:13:03', '2026-02-23 23:13:03', NULL),
(18, 'John', 'Juan', NULL, 'Filipino', 'Single', 'Male', NULL, '0987654321', NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'Region IV-A (CALABARZON)', 'Laguna', 'City of Biñan', 'Biñan', 'Purok 1', 'gerr', NULL, NULL, '2026-02-24 18:52:39', '2026-02-24 18:52:39', NULL),
(19, 'QWERTY', 'UIOP', 'POIUY', 'Foreign National', 'Married', 'Female', '2026-02-25', NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, 'Philippines', NULL, NULL, NULL, '2026-02-24 19:25:17', '2026-02-24 19:25:17', NULL),
(20, 'pacman', 'papacc', 'pac', 'Filipino', 'Single', 'Female', NULL, '0987654321', NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'Region IV-A (CALABARZON)', 'Laguna', 'City of Biñan', 'Biñan', NULL, NULL, NULL, NULL, '2026-02-25 01:21:55', '2026-02-25 01:21:55', NULL),
(21, 'QWERTY', 'UIOP', 'POIUY', NULL, NULL, NULL, NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, 'Philippines', NULL, NULL, NULL, '2026-02-25 01:25:55', '2026-02-25 01:25:55', NULL),
(22, 'tata', 'tata', NULL, 'Filipino', 'Single', 'Male', NULL, '0987654321', NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, 'Region IV-A (CALABARZON)', 'Laguna', 'Magdalena', 'Poblacion', NULL, NULL, NULL, NULL, '2026-02-25 01:38:01', '2026-02-25 01:38:01', NULL),
(23, 'QWERTY', 'UIOP', 'POIUY', 'Filipino', 'Married', 'Female', NULL, NULL, NULL, 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, 'Philippines', NULL, NULL, NULL, '2026-03-08 21:20:32', '2026-03-08 21:20:32', NULL),
(24, 'Santiago', 'Gerry', NULL, 'Filipino', 'Single', NULL, NULL, NULL, 'santiagogerry79@gmail.com', 0, 0, 0, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL, 'Victoria, Mallig, Isabela', NULL, NULL, NULL, '2026-03-08 21:21:42', '2026-03-08 21:21:42', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bpls_owner_benefits`
--

CREATE TABLE `bpls_owner_benefits` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `owner_id` bigint(20) UNSIGNED NOT NULL,
  `benefit_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `bpls_payments`
--

CREATE TABLE `bpls_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payment_year` int(10) UNSIGNED NOT NULL DEFAULT 2026,
  `renewal_cycle` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `business_entry_id` bigint(20) UNSIGNED DEFAULT NULL,
  `bpls_application_id` bigint(20) UNSIGNED DEFAULT NULL,
  `or_number` varchar(50) NOT NULL,
  `payment_date` date NOT NULL,
  `quarters_paid` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`quarters_paid`)),
  `amount_paid` decimal(15,2) NOT NULL,
  `surcharges` decimal(15,2) NOT NULL DEFAULT 0.00,
  `backtaxes` decimal(15,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_collected` decimal(15,2) NOT NULL,
  `payment_method` varchar(255) NOT NULL DEFAULT 'cash',
  `drawee_bank` varchar(255) DEFAULT NULL,
  `check_number` varchar(50) DEFAULT NULL,
  `check_date` date DEFAULT NULL,
  `fund_code` varchar(20) NOT NULL DEFAULT '100',
  `payor` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `received_by` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bpls_payments`
--

INSERT INTO `bpls_payments` (`id`, `payment_year`, `renewal_cycle`, `business_entry_id`, `bpls_application_id`, `or_number`, `payment_date`, `quarters_paid`, `amount_paid`, `surcharges`, `backtaxes`, `discount`, `total_collected`, `payment_method`, `drawee_bank`, `check_number`, `check_date`, `fund_code`, `payor`, `remarks`, `received_by`, `created_at`, `updated_at`) VALUES
(1, 2026, 0, 16, NULL, '123451', '2026-02-25', '\"[1]\"', 2390.00, 0.00, 0.00, 0.00, 2390.00, 'cash', NULL, NULL, NULL, '100', 'QWEEWQEQWE, QWEQWEQWEQW QWEQWEWQE', '', 'sample', '2026-02-24 19:46:11', '2026-02-24 19:46:11'),
(2, 2026, 0, 16, NULL, '123452', '2026-02-25', '\"[2]\"', 2390.00, 0.00, 0.00, 239.00, 2151.00, 'cash', NULL, NULL, NULL, '100', 'QWEEWQEQWE, QWEQWEQWEQW QWEQWEWQE', '', 'sample', '2026-02-24 19:46:56', '2026-02-24 19:46:56'),
(3, 2027, 1, 16, NULL, '123453', '2026-02-25', '\"[1]\"', 2390.00, 0.00, 0.00, 239.00, 2151.00, 'cash', NULL, NULL, NULL, '100', 'QWEEWQEQWE, QWEQWEQWEQW QWEQWEWQE', '', 'sample', '2026-02-24 19:47:39', '2026-02-24 19:47:39'),
(4, 2027, 1, 16, NULL, '123454', '2027-06-15', '\"[2]\"', 2390.00, 47.80, 0.00, 0.00, 2437.80, 'cash', NULL, NULL, NULL, '100', 'QWEEWQEQWE, QWEQWEQWEQW QWEQWEWQE', '', 'sample', '2026-02-24 19:48:56', '2026-02-24 19:48:56'),
(5, 2026, 0, 18, NULL, '123455', '2026-02-25', '\"[1]\"', 10475.00, 0.00, 0.00, 0.00, 10475.00, 'cash', NULL, NULL, NULL, '100', 'QWERTY, UIOP POIUY', '', 'sample', '2026-02-24 23:59:36', '2026-02-24 23:59:36'),
(6, 2026, 0, 18, NULL, '123456', '2026-02-25', '\"[2]\"', 10475.00, 0.00, 0.00, 1047.50, 9427.50, 'cash', NULL, NULL, NULL, '100', 'QWERTY, UIOP POIUY', '', 'sample', '2026-02-25 00:00:04', '2026-02-25 00:00:04'),
(7, 2026, 0, 15, NULL, '123457', '2026-02-25', '\"[1,2,3,4]\"', 2030.00, 0.00, 0.00, 76.14, 1953.86, 'cash', NULL, NULL, NULL, '100', 'QWE, QWE QWE', '', 'sample', '2026-02-25 00:08:20', '2026-02-25 00:08:20'),
(8, 2026, 0, 24, NULL, '123501', '2026-03-09', '\"[1]\"', 645.00, 0.00, 0.00, 0.00, 645.00, 'cash', NULL, NULL, NULL, '100', 'SANTIAGO, GERRY', '', 'sample', '2026-03-08 22:24:45', '2026-03-08 22:24:45'),
(9, 2026, 0, 24, NULL, '123502', '2026-03-09', '\"[2]\"', 645.00, 0.00, 0.00, 32.25, 612.75, 'cash', NULL, NULL, NULL, '100', 'SANTIAGO, GERRY', '', 'sample', '2026-03-08 22:26:22', '2026-03-08 22:26:22'),
(10, 2026, 0, 24, NULL, '123503', '2026-03-09', '\"[3]\"', 645.00, 0.00, 0.00, 32.25, 612.75, 'cash', NULL, NULL, NULL, '100', 'SANTIAGO, GERRY', '', 'sample', '2026-03-08 22:30:15', '2026-03-08 22:30:15'),
(11, 2026, 0, 24, NULL, '123504', '2026-03-09', '\"[4]\"', 645.00, 0.00, 0.00, 32.25, 612.75, 'cash', NULL, NULL, NULL, '100', 'SANTIAGO, GERRY', '', 'sample', '2026-03-08 22:31:17', '2026-03-08 22:31:17'),
(12, 2026, 0, 23, NULL, '123505', '2026-03-09', '\"[1]\"', 507.50, 0.00, 0.00, 0.00, 507.50, 'cash', NULL, NULL, NULL, '100', 'QWERTY, UIOP POIUY', '', 'sample', '2026-03-08 22:32:40', '2026-03-08 22:32:40'),
(13, 2026, 0, 23, NULL, '123506', '2026-03-09', '\"[2]\"', 507.50, 0.00, 0.00, 25.38, 482.12, 'cash', NULL, NULL, NULL, '100', 'QWERTY, UIOP POIUY', '', 'sample', '2026-03-08 22:39:42', '2026-03-08 22:39:42'),
(14, 2026, 0, 23, NULL, '123507', '2026-03-09', '\"[3]\"', 507.50, 0.00, 0.00, 25.38, 482.12, 'cash', NULL, NULL, NULL, '100', 'QWERTY, UIOP POIUY', '', 'sample', '2026-03-08 22:42:46', '2026-03-08 22:42:46'),
(15, 2026, 0, NULL, 15, 'PAY-20260224-38AD42', '2026-03-09', '[3]', 2500.00, 0.00, 0.00, 0.00, 2500.00, 'online', NULL, NULL, NULL, '100', 'QWEQWEQWEQW QWEEWQEQWE', NULL, 'System (Online)', '2026-03-09 02:18:08', '2026-03-09 02:18:08'),
(16, 2026, 0, NULL, 17, 'PAY-20260225-F349C8', '2026-03-09', '[1]', 12500.00, 0.00, 0.00, 0.00, 12500.00, 'online', NULL, NULL, NULL, '100', 'QWEQWEQWEQW QWEEWQEQWE', NULL, 'System (Online)', '2026-03-09 02:18:23', '2026-03-09 02:18:23');

-- --------------------------------------------------------

--
-- Table structure for table `bpls_permit_signatories`
--

CREATE TABLE `bpls_permit_signatories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `position` varchar(255) NOT NULL,
  `department` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bpls_permit_signatories`
--

INSERT INTO `bpls_permit_signatories` (`id`, `name`, `position`, `department`, `is_active`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'John', 'BPLS head', 'BPLS', 1, 1, '2026-02-22 22:43:07', '2026-02-22 23:01:57', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `bpls_settings`
--

CREATE TABLE `bpls_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` varchar(255) NOT NULL,
  `label` varchar(255) DEFAULT NULL,
  `group` varchar(255) NOT NULL DEFAULT 'advance_discount',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `bpls_settings`
--

INSERT INTO `bpls_settings` (`id`, `key`, `value`, `label`, `group`, `created_at`, `updated_at`) VALUES
(1, 'advance_discount_annual', '20.00', 'Annual Payment Discount (%)', 'advance_discount', '2026-02-20 18:20:41', '2026-02-20 18:20:41'),
(2, 'advance_discount_semi_annual', '10.00', 'Semi-Annual Payment Discount (%)', 'advance_discount', '2026-02-20 18:20:41', '2026-02-20 18:20:41'),
(3, 'advance_discount_quarterly', '5.00', 'Quarterly Payment Discount (%)', 'advance_discount', '2026-02-20 18:20:41', '2026-02-20 18:20:41'),
(4, 'advance_discount_enabled', '1', 'Enable Advance Payment Discount', 'advance_discount', '2026-02-20 18:20:41', '2026-02-20 18:20:41'),
(5, 'advance_discount_days_before', '10', 'Days Before Due Date to Qualify as Advance', 'advance_discount', '2026-02-20 18:20:41', '2026-02-20 18:20:41'),
(6, 'receipt_header_line1', 'Official Receipt of the Republic of the Philippines', 'Receipt Header Line 1', 'receipt', NULL, NULL),
(7, 'receipt_office_name', 'Office of the Treasurer', 'Office Name (Main Title)', 'receipt', NULL, NULL),
(8, 'receipt_header_line3', 'Province of Laguna', 'Receipt Header Line 3 (Province/Location)', 'receipt', NULL, NULL),
(9, 'receipt_agency_name', 'MTO-Majayjay', 'Agency Name (e.g. MTO-Majayjay)', 'receipt', NULL, NULL),
(10, 'receipt_af_label', 'Accountable form No. 51', 'Accountable Form Label', 'receipt', NULL, NULL),
(11, 'receipt_received_text', 'Received the amount stated above', 'Received Text (above signatories)', 'receipt', NULL, NULL),
(12, 'receipt_footer_note', '', 'Footer Note (optional)', 'receipt', NULL, NULL),
(13, 'receipt_signatory1_name', '', 'Signatory 1 Name (leave blank to use logged-in cashier)', 'receipt', NULL, NULL),
(14, 'receipt_signatory1_title', 'Cashier Officer', 'Signatory 1 Title', 'receipt', NULL, NULL),
(15, 'receipt_signatory2_enabled', '0', 'Enable Signatory 2', 'receipt', NULL, NULL),
(16, 'receipt_signatory2_name', '', 'Signatory 2 Name', 'receipt', NULL, NULL),
(17, 'receipt_signatory2_title', '', 'Signatory 2 Title', 'receipt', NULL, NULL),
(18, 'receipt_signatory3_enabled', '0', 'Enable Signatory 3', 'receipt', NULL, NULL),
(19, 'receipt_signatory3_name', '', 'Signatory 3 Name', 'receipt', NULL, NULL),
(20, 'receipt_signatory3_title', '', 'Signatory 3 Title', 'receipt', NULL, NULL),
(21, 'beneficiary_discount_enabled', '1', 'Enable Beneficiary Discounts', 'beneficiary_discount', '2026-03-06 00:46:28', '2026-03-06 00:46:28'),
(22, 'beneficiary_discount_stack', 'highest_only', 'Discount Stacking Rule', 'beneficiary_discount', '2026-03-06 00:46:28', '2026-03-06 00:46:28'),
(23, 'pwd_discount_rate', '20', 'PWD Discount Rate (%)', 'beneficiary_discount', '2026-03-06 00:46:28', '2026-03-06 00:46:28'),
(24, 'pwd_discount_apply_to', 'total', 'PWD Discount Apply To', 'beneficiary_discount', '2026-03-06 00:46:28', '2026-03-06 00:46:28'),
(25, 'senior_discount_rate', '20', 'Senior Citizen Discount Rate (%)', 'beneficiary_discount', '2026-03-06 00:46:28', '2026-03-06 00:46:28'),
(26, 'senior_discount_apply_to', 'total', 'Senior Citizen Discount Apply To', 'beneficiary_discount', '2026-03-06 00:46:28', '2026-03-06 00:46:28'),
(27, 'solo_parent_discount_rate', '10', 'Solo Parent Discount Rate (%)', 'beneficiary_discount', '2026-03-06 00:46:28', '2026-03-06 00:46:28'),
(28, 'solo_parent_discount_apply_to', 'total', 'Solo Parent Discount Apply To', 'beneficiary_discount', '2026-03-06 00:46:28', '2026-03-06 00:46:28'),
(29, 'fourps_discount_rate', '10', '4Ps Discount Rate (%)', 'beneficiary_discount', '2026-03-06 00:46:28', '2026-03-06 00:46:28'),
(30, 'fourps_discount_apply_to', 'total', '4Ps Discount Apply To', 'beneficiary_discount', '2026-03-06 00:46:28', '2026-03-06 00:46:28'),
(31, 'business_id_format', '{muni}-{year}-{id}', 'Business ID Format', 'permit', '2026-03-08 20:47:18', '2026-03-08 20:47:18');

-- --------------------------------------------------------

--
-- Table structure for table `cache`
--

CREATE TABLE `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `cache_locks`
--

CREATE TABLE `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `clients`
--

CREATE TABLE `clients` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `walk_in_business_id` bigint(20) UNSIGNED DEFAULT NULL,
  `first_name` varchar(255) NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `mobile_no` varchar(255) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `status` enum('active','inactive','suspended') NOT NULL DEFAULT 'active',
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `verification_code` varchar(6) DEFAULT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `clients`
--

INSERT INTO `clients` (`id`, `walk_in_business_id`, `first_name`, `last_name`, `middle_name`, `email`, `mobile_no`, `password`, `status`, `email_verified_at`, `verification_code`, `remember_token`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, NULL, 'Juan', 'Dela Cruz', 'Santos', 'juan@example.com', '0987654321', '$2y$12$n/cMI.iQ4N5zL5xQTXod0u7sj17qyrKTOJz2eTZSjNqhQH6HN8UNC', 'active', NULL, NULL, NULL, '2026-02-20 18:23:03', '2026-02-20 18:23:03', NULL),
(2, NULL, 'john', 'qweryy', 'qwe', 'john@example.com', '123456789', '$2y$12$1peOhWG7BpGLCFY7EWu6.u5r5gUO8pr9CeAoIvE9ifP4B5wBTODcW', 'active', NULL, NULL, NULL, '2026-02-22 00:51:28', '2026-02-22 00:51:28', NULL),
(3, NULL, 'Gerry', 'Santiago', NULL, 'santiagogerry79@gmail.com', NULL, '$2y$12$ACAcemYD1mZBE9V9.zKOzOOw4P076gXig/D48QaI0g4uqeWvqeq4.', 'active', NULL, NULL, NULL, '2026-03-08 22:03:43', '2026-03-08 22:03:43', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `client_linked_properties`
--

CREATE TABLE `client_linked_properties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `client_id` bigint(20) UNSIGNED NOT NULL,
  `tax_declaration_id` bigint(20) UNSIGNED NOT NULL,
  `nickname` varchar(255) DEFAULT NULL,
  `linked_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `client_linked_properties`
--

INSERT INTO `client_linked_properties` (`id`, `client_id`, `tax_declaration_id`, `nickname`, `linked_at`, `created_at`, `updated_at`) VALUES
(1, 2, 16, NULL, '2026-03-09 02:51:46', '2026-03-09 02:51:46', '2026-03-09 02:51:46');

-- --------------------------------------------------------

--
-- Table structure for table `defaultz`
--

CREATE TABLE `defaultz` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `mun_assessor` varchar(255) DEFAULT NULL,
  `mun_ass_designation` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `defaultz`
--

INSERT INTO `defaultz` (`id`, `mun_assessor`, `mun_ass_designation`, `created_at`, `updated_at`) VALUES
(1, 'Municipal Assessor', 'Municipal Assessor', '2026-02-06 21:35:17', '2026-02-06 21:35:17'),
(2, 'Hi', 'Assessor', '2026-02-06 21:35:17', '2026-02-06 21:38:26');

-- --------------------------------------------------------

--
-- Table structure for table `departments`
--

CREATE TABLE `departments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `department_name` varchar(255) NOT NULL,
  `dep_code` varchar(50) DEFAULT NULL,
  `dep_desc` varchar(255) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `sector` varchar(100) DEFAULT NULL,
  `rank_order` int(11) DEFAULT NULL,
  `pay_name` varchar(100) DEFAULT NULL,
  `pay_full` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `departments`
--

INSERT INTO `departments` (`id`, `department_name`, `dep_code`, `dep_desc`, `category`, `sector`, `rank_order`, `pay_name`, `pay_full`, `created_at`, `updated_at`) VALUES
(1, 'Office of the Municipal Administrator', 'OMAD', NULL, 'Lgu Office', 'GENERAL SERVICES', 10, NULL, NULL, NULL, '2026-01-26 23:30:06'),
(2, 'Office of the Municipal Treasurer', 'MTO', NULL, 'Lgu Office', 'GENERAL SERVICES', 10, 'MTO', 'Municipal Treasurer\'s Office', NULL, '2026-01-26 23:30:21'),
(3, 'Office of the Municipal Assessor', 'OMASS', NULL, 'Lgu Office', 'GENERAL SERVICES', 11, 'AO', 'Assesor\'s Office', '2026-01-26 23:00:11', '2026-01-26 23:30:28');

-- --------------------------------------------------------

--
-- Table structure for table `divisions`
--

CREATE TABLE `divisions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `division_name` varchar(255) NOT NULL,
  `division_code` varchar(20) NOT NULL,
  `division_description` text DEFAULT NULL,
  `office_id` bigint(20) UNSIGNED NOT NULL,
  `division_head` varchar(100) DEFAULT NULL,
  `order_sequence` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_civil_service`
--

CREATE TABLE `employee_civil_service` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `eligibility` varchar(255) NOT NULL,
  `level` varchar(100) DEFAULT NULL,
  `exam_date` varchar(50) DEFAULT NULL,
  `exam_place` varchar(255) DEFAULT NULL,
  `license_number` varchar(100) DEFAULT NULL,
  `license_date_valid` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_documents`
--

CREATE TABLE `employee_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `document_type` varchar(100) NOT NULL,
  `file_name` varchar(255) NOT NULL,
  `file_path` varchar(255) NOT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `file_size` int(11) DEFAULT NULL,
  `document_date` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_education`
--

CREATE TABLE `employee_education` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `level` varchar(100) NOT NULL,
  `school_name` varchar(255) NOT NULL,
  `degree` varchar(255) DEFAULT NULL,
  `year_graduated` varchar(20) DEFAULT NULL,
  `units_earned` varchar(50) DEFAULT NULL,
  `attendance_from` varchar(20) DEFAULT NULL,
  `attendance_to` varchar(20) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_family_background`
--

CREATE TABLE `employee_family_background` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `relation` varchar(50) NOT NULL,
  `name` varchar(255) NOT NULL,
  `birthday` date DEFAULT NULL,
  `occupation` varchar(255) DEFAULT NULL,
  `employer` varchar(255) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_government_ids`
--

CREATE TABLE `employee_government_ids` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `id_type` varchar(50) NOT NULL,
  `id_number` varchar(100) NOT NULL,
  `date_issued` date DEFAULT NULL,
  `date_expiry` date DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_info`
--

CREATE TABLE `employee_info` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `last_name` varchar(255) NOT NULL,
  `birthday` date NOT NULL,
  `gender` enum('Male','Female','Other') NOT NULL,
  `contact_number` varchar(255) NOT NULL,
  `employee_address` varchar(255) NOT NULL,
  `hire_date` date NOT NULL,
  `end_of_contract_date` date DEFAULT NULL,
  `employee_group` varchar(255) NOT NULL,
  `designation` varchar(255) NOT NULL,
  `employee_remarks` text DEFAULT NULL,
  `biometrics_no` int(11) DEFAULT NULL,
  `rate_per_day` double NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `office_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `plantilla_position_id` bigint(20) UNSIGNED DEFAULT NULL,
  `salary_step` int(11) NOT NULL DEFAULT 1 COMMENT '1 through 8'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `employee_info`
--

INSERT INTO `employee_info` (`id`, `employee_id`, `email`, `first_name`, `middle_name`, `last_name`, `birthday`, `gender`, `contact_number`, `employee_address`, `hire_date`, `end_of_contract_date`, `employee_group`, `designation`, `employee_remarks`, `biometrics_no`, `rate_per_day`, `department_id`, `office_id`, `created_at`, `updated_at`, `plantilla_position_id`, `salary_step`) VALUES
(1, 'IT111', 'admin@gmail.com', 'admin', 'admin', 'admin', '2003-01-02', '', '123456789', 'Majayjay', '2019-01-04', '2034-01-05', 'PERMANENT', 'MIS', 'GOOD', 111, 10000, 1, NULL, NULL, NULL, NULL, 1),
(2, 'IT222', 'sample@gmail.com', 'SAMPLE 1', 'sample middle', 'sample last', '2004-02-17', 'Male', '123456789', 'Origuel, Majayjay, Laguna', '2025-02-04', '2027-10-21', 'CONTRACT', 'MIS', 'Goods', 12345, 10000, 1, NULL, '2026-01-16 23:57:53', '2026-01-16 23:57:53', NULL, 1),
(3, 'IT333', 'sample2@gmail.com', 'SAMPLE 2', 'sample middle 2', 'sample last 2', '2004-02-17', 'Female', '987654321', 'Origuel, Majayjay, Laguna', '2025-02-04', '2027-10-21', 'CONTRACT', 'MIS', 'Goods', 12345, 10000, 1, NULL, '2026-01-17 00:00:11', '2026-01-17 00:00:11', NULL, 1),
(4, 't111', 'treasury@gmail.com', 'treasury', 'treasuryM', 'treasuryL', '1995-03-02', 'Female', '1234567890', 'Origuel, Majayjay, Laguna', '2026-01-19', '2031-12-19', 'CONTRACT', 'Treasury', NULL, 12345, 10000, 2, NULL, '2026-01-18 20:11:44', '2026-01-18 20:11:44', NULL, 1);

-- --------------------------------------------------------

--
-- Table structure for table `employee_trainings`
--

CREATE TABLE `employee_trainings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `training_title` varchar(255) NOT NULL,
  `training_type` varchar(100) DEFAULT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `hours` int(11) DEFAULT NULL,
  `conducted_by` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employee_work_experience`
--

CREATE TABLE `employee_work_experience` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `position_title` varchar(255) NOT NULL,
  `company_name` varchar(255) NOT NULL,
  `date_from` date DEFAULT NULL,
  `date_to` date DEFAULT NULL,
  `salary` varchar(50) DEFAULT NULL,
  `pay_grade` varchar(20) DEFAULT NULL,
  `status_of_employment` varchar(100) DEFAULT NULL,
  `is_government` tinyint(1) NOT NULL DEFAULT 0,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `employment_types`
--

CREATE TABLE `employment_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `type_name` varchar(255) NOT NULL,
  `type_code` varchar(20) NOT NULL,
  `type_description` text DEFAULT NULL,
  `category` varchar(50) DEFAULT NULL,
  `is_permanent` tinyint(1) NOT NULL DEFAULT 0,
  `has_plantilla` tinyint(1) NOT NULL DEFAULT 0,
  `leave_credits_per_year` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faas_activity_logs`
--

CREATE TABLE `faas_activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `faas_property_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faas_activity_logs`
--

INSERT INTO `faas_activity_logs` (`id`, `faas_property_id`, `user_id`, `action`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 2, 'created', 'Draft FAAS auto-created via Quick Start from Registration #3. Component: land', '2026-03-06 07:19:28', '2026-03-06 07:19:28'),
(2, 2, 2, 'created', 'Draft FAAS auto-created via Quick Start from Registration #2. Component: land', '2026-03-06 07:20:28', '2026-03-06 07:20:28'),
(3, 2, 2, 'upload_attachment', 'Uploaded document: BusinessPermit-APP-2026-00014-2026.pdf (Deed of Donation)', '2026-03-06 07:21:11', '2026-03-06 07:21:11'),
(4, 2, 2, 'appraisal_land', 'Added Land parcel (25.0000 sqm) at Unit Value ₱150.00.', '2026-03-06 07:21:37', '2026-03-06 07:21:37'),
(5, 2, 2, 'submitted_review', 'Submitted for review.', '2026-03-06 07:21:42', '2026-03-06 07:21:42'),
(6, 2, 2, 'recommended_approval', 'Municipal Assessor recommended this record for final approval.', '2026-03-06 07:21:47', '2026-03-06 07:21:47'),
(7, 2, 2, 'returned_to_review', 'Returned to Municipal Assessor for review. Reason: assessed value recompute', '2026-03-06 07:22:10', '2026-03-06 07:22:10'),
(8, 2, 2, 'recomputed', 'Triggered bulk re-computation of all property components.', '2026-03-06 07:22:17', '2026-03-06 07:22:17'),
(9, 2, 2, 'recomputed', 'Triggered bulk re-computation of all property components.', '2026-03-06 07:25:02', '2026-03-06 07:25:02'),
(10, 2, 2, 'appraisal_land', 'Added Land parcel (259.0000 sqm) at Unit Value ₱2.00.', '2026-03-06 07:32:40', '2026-03-06 07:32:40'),
(11, 2, 2, 'recommended_approval', 'Municipal Assessor recommended this record for final approval.', '2026-03-06 07:33:55', '2026-03-06 07:33:55'),
(14, 2, 2, 'approved', 'Record approved and ARP No. 00-0002-00001 assigned.', '2026-03-06 07:39:50', '2026-03-06 07:39:50'),
(15, 3, 2, 'created', 'Initial DRAFT FAAS generated from Property Registration (Intake ID: 1).', '2026-03-07 18:36:30', '2026-03-07 18:36:30'),
(16, 3, 2, 'appraisal_land', 'Added Land parcel (250.0000 sqm) at Unit Value ₱2.00.', '2026-03-07 18:37:01', '2026-03-07 18:37:01'),
(17, 3, 2, 'upload_attachment', 'Uploaded document: BusinessPermit-APP-2026-00014-2026.pdf (Deed of Sale)', '2026-03-07 18:37:19', '2026-03-07 18:37:19'),
(18, 3, 2, 'submitted_review', 'Submitted for review.', '2026-03-07 18:37:29', '2026-03-07 18:37:29'),
(19, 3, 2, 'recommended_approval', 'Municipal Assessor recommended this record for final approval.', '2026-03-07 18:37:40', '2026-03-07 18:37:40'),
(20, 3, 2, 'approved', 'Record approved and ARP No. 00-0007-00002 assigned.', '2026-03-07 18:38:17', '2026-03-07 18:38:17'),
(21, 7, 2, 'created_by_subdivision', 'Parcel #1 (Lot: 102-A) — Owner: wawa, Kind: land. Subdivided from Mother ARP: 00-0007-00002.', '2026-03-07 18:54:20', '2026-03-07 18:54:20'),
(22, 8, 2, 'created_by_subdivision', 'Parcel #2 (Lot: 102-B) — Owner: rara, Kind: land. Subdivided from Mother ARP: 00-0007-00002.', '2026-03-07 18:54:20', '2026-03-07 18:54:20'),
(23, 3, 2, 'field_inspection', 'Field Inspection conducted by: WAQA on 2026-03-08. Subdivision of 2 parcels verified.', '2026-03-07 18:54:20', '2026-03-07 18:54:20'),
(24, 3, 2, 'subdivision_completed', 'Property subdivided into 2 child parcels. Mother record set to INACTIVE.', '2026-03-07 18:54:20', '2026-03-07 18:54:20'),
(25, 9, 2, 'created', 'Draft FAAS auto-created via Quick Start from Registration #4. Component: land', '2026-03-08 17:35:47', '2026-03-08 17:35:47'),
(26, 9, 2, 'appraisal_land', 'Added Land parcel (250.0000 sqm) at Unit Value ₱22.00.', '2026-03-08 17:36:01', '2026-03-08 17:36:01'),
(27, 9, 2, 'upload_attachment', 'Uploaded document: BusinessPermit-APP-2026-00014-2026.pdf (Deed of Sale)', '2026-03-08 17:36:21', '2026-03-08 17:36:21'),
(28, 9, 2, 'submitted_review', 'Submitted for review.', '2026-03-08 17:36:29', '2026-03-08 17:36:29'),
(29, 9, 2, 'recommended_approval', 'Municipal Assessor recommended this record for final approval.', '2026-03-08 17:36:35', '2026-03-08 17:36:35'),
(30, 9, 2, 'approved', 'Record approved and ARP No. 00-0002-00003 assigned.', '2026-03-08 17:36:46', '2026-03-08 17:36:46'),
(31, 9, 2, 'transfer_initiated', 'Transfer of Ownership initiated (CAR: eCAR-001). Original record remains APPROVED until new owner is approved.', '2026-03-08 17:50:59', '2026-03-08 17:50:59'),
(32, 10, 2, 'created_by_transfer', 'New FAAS Draft created for Transfer of Ownership (New Owner: Marksa Dela Cuzasd). Legal Docs: CAR eCAR-001 and Transfer Tax 112233', '2026-03-08 17:50:59', '2026-03-08 17:50:59'),
(33, 10, 2, 'upload_attachment', 'Uploaded document: BusinessPermit-APP-2026-00014-2026.pdf (Deed of Sale)', '2026-03-08 17:51:25', '2026-03-08 17:51:25'),
(34, 10, 2, 'submitted_review', 'Submitted for review.', '2026-03-08 17:51:29', '2026-03-08 17:51:29'),
(35, 10, 2, 'recommended_approval', 'Municipal Assessor recommended this record for final approval.', '2026-03-08 17:51:49', '2026-03-08 17:51:49'),
(36, 10, 2, 'approved', 'Record approved and ARP No. 00-0002-00004 assigned.', '2026-03-08 17:52:07', '2026-03-08 17:52:07'),
(37, 9, 2, 'deactivated_by_revision', 'Superseded and set to INACTIVE (including TDs) upon approval of new ARP 00-0002-00004.', '2026-03-08 17:52:07', '2026-03-08 17:52:07'),
(38, 11, 2, 'created', 'Draft FAAS auto-created via Quick Start from Registration #5. Component: land', '2026-03-08 19:01:52', '2026-03-08 19:01:52'),
(39, 11, 2, 'appraisal_land', 'Added Land parcel (23.0000 sqm) at Unit Value ₱212.00.', '2026-03-08 19:02:16', '2026-03-08 19:02:16'),
(40, 11, 2, 'upload_attachment', 'Uploaded document: TD-TD-00000019.pdf (Title (TCT/OCT))', '2026-03-08 19:02:26', '2026-03-08 19:02:26'),
(41, 11, 2, 'submitted_review', 'Submitted for review.', '2026-03-08 19:02:31', '2026-03-08 19:02:31'),
(42, 11, 2, 'recommended_approval', 'Municipal Assessor recommended this record for final approval.', '2026-03-08 19:02:37', '2026-03-08 19:02:37'),
(43, 11, 2, 'approved', 'Record approved and ARP No. 00-0002-00005 assigned.', '2026-03-08 19:02:51', '2026-03-08 19:02:51'),
(44, 20, 2, 'created', 'Initial DRAFT FAAS generated from Property Registration (Intake ID: 6).', '2026-03-09 00:35:07', '2026-03-09 00:35:07'),
(45, 21, 2, 'created_from_online', 'Property registered from online application RPT-20260309-6CA9A. Appraisal components must be added manually.', '2026-03-09 02:46:06', '2026-03-09 02:46:06'),
(46, 21, 2, 'appraisal_land', 'Added Land parcel (20.0000 sqm) at Unit Value ₱120.00.', '2026-03-09 02:49:49', '2026-03-09 02:49:49'),
(47, 21, 2, 'upload_attachment', 'Uploaded document: BusinessPermit-APP-2026-00013-2026.pdf (Deed of Sale)', '2026-03-09 02:50:03', '2026-03-09 02:50:03'),
(48, 21, 2, 'submitted_review', 'Submitted for review.', '2026-03-09 02:50:10', '2026-03-09 02:50:10'),
(49, 21, 2, 'recommended_approval', 'Municipal Assessor recommended this record for final approval.', '2026-03-09 02:50:16', '2026-03-09 02:50:16'),
(50, 21, 2, 'approved', 'Record approved and ARP No. 00-0002-04175 assigned.', '2026-03-09 02:50:21', '2026-03-09 02:50:21');

-- --------------------------------------------------------

--
-- Table structure for table `faas_attachments`
--

CREATE TABLE `faas_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `faas_property_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL COMMENT 'title_deed, sketch_plan, tax_clearance, others',
  `label` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `uploaded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faas_attachments`
--

INSERT INTO `faas_attachments` (`id`, `faas_property_id`, `type`, `label`, `file_path`, `original_filename`, `uploaded_by`, `created_at`, `updated_at`) VALUES
(1, 2, 'Deed of Donation', 'BusinessPermit-APP-2026-00014-2026.pdf', 'rpt/faas-attachments/K1T6zujhBa9Xfyjw76m4n3fHyieyyRcJh9ztJDdC.pdf', 'BusinessPermit-APP-2026-00014-2026.pdf', 2, '2026-03-06 07:21:11', '2026-03-06 07:21:11'),
(2, 3, 'Deed of Sale', 'BusinessPermit-APP-2026-00014-2026.pdf', 'rpt/faas-attachments/ZGvRmuhvJyLD1pqUtPdLXfJjg6efQVraJ7fVceDI.pdf', 'BusinessPermit-APP-2026-00014-2026.pdf', 2, '2026-03-07 18:37:19', '2026-03-07 18:37:19'),
(3, 3, 'legal_requirement', 'Subdivision Plan', 'rpt/faas-attachments/lYnbjC8SepSeuljvl1NHulfBR6GWVQqK3pOBxt3z.pdf', 'BusinessPermit-APP-2026-00025-2026.pdf', 2, '2026-03-07 18:54:20', '2026-03-07 18:54:20'),
(4, 9, 'Deed of Sale', 'BusinessPermit-APP-2026-00014-2026.pdf', 'rpt/faas-attachments/0lpgUl1jURAitsSDZJgSnxrkZMW48ei2MYnGVo5M.pdf', 'BusinessPermit-APP-2026-00014-2026.pdf', 2, '2026-03-08 17:36:21', '2026-03-08 17:36:21'),
(5, 10, 'Deed of Sale', 'BusinessPermit-APP-2026-00014-2026.pdf', 'rpt/faas-attachments/Yk59kR3C4751zAmrCvFs9gPTKdws1AXV8M8BFjGd.pdf', 'BusinessPermit-APP-2026-00014-2026.pdf', 2, '2026-03-08 17:51:25', '2026-03-08 17:51:25'),
(6, 11, 'Title (TCT/OCT)', 'TD-TD-00000019.pdf', 'rpt/faas-attachments/GpuhBdKQZHGDdntud8YkRS2MEfmYIcpN7uaqkFI0.pdf', 'TD-TD-00000019.pdf', 2, '2026-03-08 19:02:26', '2026-03-08 19:02:26'),
(7, 21, 'Deed of Sale', 'BusinessPermit-APP-2026-00013-2026.pdf', 'rpt/faas-attachments/iRH2puFeaoIeAzNyhx91CQ0Ez4DaCLCfnoGeIWIT.pdf', 'BusinessPermit-APP-2026-00013-2026.pdf', 2, '2026-03-09 02:50:03', '2026-03-09 02:50:03');

-- --------------------------------------------------------

--
-- Table structure for table `faas_buildings`
--

CREATE TABLE `faas_buildings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `faas_property_id` bigint(20) UNSIGNED NOT NULL,
  `faas_land_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rpta_bldg_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rpta_actual_use_id` bigint(20) UNSIGNED DEFAULT NULL,
  `building_name` varchar(255) DEFAULT NULL,
  `kind_of_building` varchar(255) DEFAULT NULL,
  `construction_materials` varchar(255) DEFAULT NULL,
  `building_type_base_value` decimal(18,2) DEFAULT NULL,
  `num_storeys` int(11) NOT NULL DEFAULT 1,
  `floor_area` decimal(14,4) NOT NULL COMMENT 'sq.m',
  `year_constructed` year(4) DEFAULT NULL,
  `year_appraised` year(4) DEFAULT NULL,
  `construction_cost_per_sqm` decimal(18,2) NOT NULL DEFAULT 0.00,
  `base_market_value` decimal(18,2) NOT NULL DEFAULT 0.00,
  `depreciation_rate` decimal(5,4) NOT NULL DEFAULT 0.0000,
  `depreciation_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `market_value` decimal(18,2) NOT NULL DEFAULT 0.00,
  `assessment_level` decimal(5,4) NOT NULL DEFAULT 0.0000,
  `assessed_value` decimal(18,2) NOT NULL DEFAULT 0.00,
  `additional_items` text DEFAULT NULL COMMENT 'JSON: [{description, area, unit_cost}]',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faas_building_improvements`
--

CREATE TABLE `faas_building_improvements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `building_id` bigint(20) UNSIGNED NOT NULL,
  `improvement_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(18,2) NOT NULL,
  `unit_value` decimal(18,2) NOT NULL,
  `total_value` decimal(18,2) NOT NULL,
  `depreciation_rate` decimal(18,2) NOT NULL DEFAULT 0.00,
  `remaining_value_percent` decimal(18,2) NOT NULL DEFAULT 100.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faas_gen_rev`
--

CREATE TABLE `faas_gen_rev` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kind` varchar(255) DEFAULT NULL,
  `transaction_type` varchar(255) DEFAULT NULL,
  `td_no` varchar(50) NOT NULL,
  `revision_type` varchar(255) DEFAULT NULL,
  `reason` text DEFAULT NULL,
  `memoranda` text DEFAULT NULL,
  `effectivity_quarter` int(11) DEFAULT NULL,
  `effectivity_year` int(11) DEFAULT NULL,
  `approved_by` varchar(255) DEFAULT NULL,
  `date_approved` date DEFAULT NULL,
  `draft_id` varchar(255) DEFAULT NULL,
  `pin` varchar(255) DEFAULT NULL,
  `lot_no` varchar(255) DEFAULT NULL,
  `arpn` varchar(50) DEFAULT NULL,
  `total_market_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_assessed_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `revised_year` int(11) NOT NULL,
  `gen_rev` int(11) NOT NULL,
  `bcode` varchar(10) NOT NULL,
  `rev_unit_val` varchar(50) NOT NULL,
  `gen_desc` text DEFAULT NULL,
  `inspection_date` date DEFAULT NULL,
  `inspected_by` varchar(255) DEFAULT NULL,
  `inspection_remarks` text DEFAULT NULL,
  `previous_td_id` bigint(20) UNSIGNED DEFAULT NULL,
  `statt` varchar(20) NOT NULL DEFAULT 'revised',
  `encoded_by` varchar(255) NOT NULL,
  `entry_date` date DEFAULT NULL,
  `entry_by` varchar(255) DEFAULT NULL,
  `encoded_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faas_gen_rev`
--

INSERT INTO `faas_gen_rev` (`id`, `kind`, `transaction_type`, `td_no`, `revision_type`, `reason`, `memoranda`, `effectivity_quarter`, `effectivity_year`, `approved_by`, `date_approved`, `draft_id`, `pin`, `lot_no`, `arpn`, `total_market_value`, `total_assessed_value`, `revised_year`, `gen_rev`, `bcode`, `rev_unit_val`, `gen_desc`, `inspection_date`, `inspected_by`, `inspection_remarks`, `previous_td_id`, `statt`, `encoded_by`, `entry_date`, `entry_by`, `encoded_date`, `created_at`, `updated_at`) VALUES
(44, NULL, 'NEW', 'TD-000000001', NULL, NULL, NULL, 3, 2027, 'sample', '2026-02-20', 'TD-000000001', '023-16-0010-004-13', NULL, '987654321', 1701127.40, 256133.84, 2026, 2026, '0007', '0', '', NULL, NULL, ' [CANCELLED: SUBDIVIDED]', NULL, 'CANCELLED', 'sample', '2026-02-20', 'sample', '2026-02-20 07:27:09', '2026-02-19 23:27:09', '2026-02-19 23:54:16'),
(45, NULL, 'NEW', 'TD-00000054', NULL, NULL, NULL, 3, 2027, 'sample', '2026-02-20', 'TD-000000001', '023-16-0010-004-15', NULL, '9876543299', 10847967.48, 1627195.12, 2026, 2026, '0007', '0', '', NULL, NULL, ' [CANCELLED: SUBDIVIDED]', 44, 'CANCELLED', 'sample', '2026-02-20', 'sample', '2026-02-20 07:27:09', '2026-02-19 23:54:16', '2026-02-20 01:16:43'),
(46, NULL, 'NEW', 'TD-00000055', NULL, NULL, NULL, 3, 2027, 'sample', '2026-02-20', 'TD-000000001', '023-16-0010-004-14', NULL, '9876543200', 17468264.94, 2620239.74, 2026, 2026, '0007', '0', '', NULL, NULL, NULL, 44, 'ACTIVE', 'sample', '2026-02-20', 'sample', '2026-02-20 07:27:09', '2026-02-19 23:54:16', '2026-02-19 23:54:16'),
(50, NULL, 'NEW', 'TD-00000071', NULL, NULL, NULL, 3, 2027, 'sample', '2026-02-20', 'TD-000000001', '023-16-0010-004-78', NULL, '9876543299', 147431.95, 22114.79, 2026, 2026, '0007', '0', '', NULL, NULL, NULL, 45, 'ACTIVE', 'sample', '2026-02-20', 'sample', '2026-02-20 07:27:09', '2026-02-20 01:16:43', '2026-02-20 01:16:43'),
(51, NULL, 'NEW', 'TD-00000072', NULL, NULL, NULL, 3, 2027, 'sample', '2026-02-20', 'TD-000000001', '023-16-0010-004-77', NULL, '9876543298', 152423.83, 30604.57, 2026, 2026, '0007', '0', '', NULL, NULL, NULL, 45, 'ACTIVE', 'sample', '2026-02-20', 'sample', '2026-02-20 07:27:09', '2026-02-20 01:16:43', '2026-02-20 03:47:02'),
(52, NULL, 'NEW', 'TD-000000002', NULL, NULL, NULL, 4, 2027, 'sample', '2026-02-20', 'TD-000000002', '2222222', NULL, '1111111', 178.00, 35.60, 2026, 2026, '0003', '0', '', NULL, NULL, NULL, NULL, 'ACTIVE', 'sample', '2026-02-20', 'sample', '2026-02-20 10:09:25', '2026-02-20 02:09:25', '2026-02-20 20:19:46');

-- --------------------------------------------------------

--
-- Table structure for table `faas_gen_rev_geometries`
--

CREATE TABLE `faas_gen_rev_geometries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `faas_id` bigint(20) UNSIGNED NOT NULL,
  `pin` varchar(100) DEFAULT NULL,
  `geometry` longtext NOT NULL COMMENT 'GeoJSON Polygon/MultiPolygon data',
  `area_sqm` decimal(15,2) DEFAULT NULL,
  `land_use_zone` varchar(255) DEFAULT NULL,
  `adj_north` varchar(255) DEFAULT NULL,
  `adj_south` varchar(255) DEFAULT NULL,
  `adj_east` varchar(255) DEFAULT NULL,
  `adj_west` varchar(255) DEFAULT NULL,
  `gps_lat` decimal(10,8) DEFAULT NULL,
  `gps_lng` decimal(11,8) DEFAULT NULL,
  `inspector_notes` text DEFAULT NULL,
  `fill_color` varchar(20) NOT NULL DEFAULT '#4F46E5',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faas_gen_rev_geometries`
--

INSERT INTO `faas_gen_rev_geometries` (`id`, `faas_id`, `pin`, `geometry`, `area_sqm`, `land_use_zone`, `adj_north`, `adj_south`, `adj_east`, `adj_west`, `gps_lat`, `gps_lng`, `inspector_notes`, `fill_color`, `created_at`, `updated_at`) VALUES
(10, 44, '023-16-0010-004-13', '{\"type\":\"Polygon\",\"coordinates\":[[[121.490612,14.156118],[121.490547,14.155957],[121.490279,14.155795],[121.489904,14.155816],[121.49044,14.1563],[121.490612,14.156118]]]}', 2051.90, '123', '12', '21', '12', '321', NULL, NULL, NULL, '#4F46E5', '2026-02-19 23:30:31', '2026-02-19 23:39:02'),
(11, 45, '023-16-0010-004-15', '{\"type\":\"Polygon\",\"coordinates\":[[[121.490241,14.156108],[121.490472,14.155926],[121.49060235746202,14.156094116175156],[121.49061186653663,14.156117669421489],[121.49044,14.156274],[121.490241,14.156108]]]}', 786.08, '', '', '', '', '', NULL, NULL, '', '#10B981', '2026-02-19 23:54:16', '2026-02-19 23:54:16'),
(12, 46, '023-16-0010-004-14', '{\"type\":\"Polygon\",\"coordinates\":[[[121.489904,14.155816],[121.490279,14.155795],[121.490547,14.155957],[121.49060235746202,14.156094116175156],[121.490472,14.155926],[121.490241,14.156108],[121.49044,14.156274],[121.49061186653663,14.156117669421489],[121.490612,14.156118],[121.49044,14.1563],[121.489904,14.155816]]]}', 1265.82, '', '', '', '', '', NULL, NULL, '', '#10B981', '2026-02-19 23:54:16', '2026-02-19 23:54:16'),
(14, 50, '023-16-0010-004-78', '{\"type\":\"Polygon\",\"coordinates\":[[[121.490241,14.156108],[121.4903890741661,14.155991335505492],[121.4903890741661,14.15623151915363],[121.490241,14.156108]]]}', 213.67, '', '', '', '', '', NULL, NULL, '', '#10B981', '2026-02-20 01:16:43', '2026-02-20 01:16:43'),
(15, 51, '023-16-0010-004-77', '{\"type\":\"Polygon\",\"coordinates\":[[[121.4903890741661,14.155991335505492],[121.490472,14.155926],[121.49060235746202,14.156094116175156],[121.49061186653663,14.156117669421489],[121.49044,14.156274],[121.4903890741661,14.15623151915363],[121.4903890741661,14.155991335505492]]]}', 572.42, '', '', '', '', '', NULL, NULL, '', '#10B981', '2026-02-20 01:16:43', '2026-02-20 01:16:43');

-- --------------------------------------------------------

--
-- Table structure for table `faas_lands`
--

CREATE TABLE `faas_lands` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `faas_property_id` bigint(20) UNSIGNED NOT NULL,
  `rpta_actual_use_id` bigint(20) UNSIGNED DEFAULT NULL,
  `survey_no` varchar(255) DEFAULT NULL,
  `lot_no` varchar(255) DEFAULT NULL,
  `blk_no` varchar(255) DEFAULT NULL,
  `area_sqm` decimal(14,4) NOT NULL,
  `unit_value` decimal(18,2) NOT NULL DEFAULT 0.00 COMMENT 'Base Market Value per sq.m',
  `base_market_value` decimal(18,2) NOT NULL DEFAULT 0.00 COMMENT 'area × unit_value',
  `market_value_adjustments` decimal(18,2) NOT NULL DEFAULT 0.00,
  `market_value` decimal(18,2) NOT NULL DEFAULT 0.00 COMMENT 'final FMV',
  `assessment_level` decimal(5,4) NOT NULL DEFAULT 0.0000,
  `assessed_value` decimal(18,2) NOT NULL DEFAULT 0.00,
  `latitude` decimal(10,8) DEFAULT NULL,
  `longitude` decimal(11,8) DEFAULT NULL,
  `is_corner_lot` tinyint(1) NOT NULL DEFAULT 0,
  `land_type` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `polygon_coordinates` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`polygon_coordinates`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faas_lands`
--

INSERT INTO `faas_lands` (`id`, `faas_property_id`, `rpta_actual_use_id`, `survey_no`, `lot_no`, `blk_no`, `area_sqm`, `unit_value`, `base_market_value`, `market_value_adjustments`, `market_value`, `assessment_level`, `assessed_value`, `latitude`, `longitude`, `is_corner_lot`, `land_type`, `created_at`, `updated_at`, `polygon_coordinates`) VALUES
(1, 2, NULL, NULL, '2', '2', 25.0000, 150.00, 3750.00, 0.00, 3750.00, 0.0000, 0.00, NULL, NULL, 0, NULL, '2026-03-06 07:21:37', '2026-03-06 07:24:58', NULL),
(2, 2, 3, NULL, '2', '2', 259.0000, 2.00, 518.00, 0.00, 518.00, 0.4000, 207.20, NULL, NULL, 0, NULL, '2026-03-06 07:32:40', '2026-03-06 07:32:40', NULL),
(3, 3, 3, NULL, '2', '2', 250.0000, 2.00, 500.00, 0.00, 500.00, 0.4000, 200.00, NULL, NULL, 0, NULL, '2026-03-07 18:37:01', '2026-03-07 18:37:01', NULL),
(6, 7, 3, NULL, '102-A', '2', 125.0000, 2.00, 250.00, 0.00, 250.00, 0.4000, 100.00, NULL, NULL, 0, 'land', '2026-03-07 18:54:20', '2026-03-07 18:54:20', NULL),
(7, 8, 3, NULL, '102-B', '2', 125.0000, 2.00, 250.00, 0.00, 250.00, 0.4000, 100.00, NULL, NULL, 0, 'land', '2026-03-07 18:54:20', '2026-03-07 18:54:20', NULL),
(8, 9, 3, NULL, '2', '2', 250.0000, 22.00, 5500.00, 0.00, 5500.00, 0.4000, 2200.00, NULL, NULL, 0, NULL, '2026-03-08 17:36:01', '2026-03-08 17:36:01', NULL),
(9, 10, 3, NULL, '2', '2', 250.0000, 22.00, 5500.00, 0.00, 5500.00, 0.4000, 2200.00, NULL, NULL, 0, NULL, '2026-03-08 17:50:59', '2026-03-08 17:50:59', NULL),
(10, 11, 3, NULL, '2', '2', 23.0000, 212.00, 4876.00, 0.00, 4876.00, 0.4000, 1950.40, NULL, NULL, 0, NULL, '2026-03-08 19:02:16', '2026-03-08 19:02:16', NULL),
(11, 14, 2, 'SUR-5330', NULL, NULL, 500.0000, 1000.00, 500000.00, 0.00, 500000.00, 0.2000, 100000.00, NULL, NULL, 0, NULL, '2026-03-08 19:13:00', '2026-03-08 19:13:00', NULL),
(12, 15, 2, 'SUR-2743', NULL, NULL, 500.0000, 1000.00, 500000.00, 0.00, 500000.00, 0.2000, 100000.00, NULL, NULL, 0, NULL, '2026-03-08 19:13:44', '2026-03-08 19:13:44', NULL),
(13, 16, 2, 'SUR-A-2156', NULL, NULL, 800.0000, 1000.00, 800000.00, 0.00, 800000.00, 0.2000, 160000.00, NULL, NULL, 0, NULL, '2026-03-08 19:13:44', '2026-03-08 19:13:44', NULL),
(14, 17, 2, 'SUR-1682', NULL, NULL, 500.0000, 1000.00, 500000.00, 0.00, 500000.00, 0.2000, 100000.00, NULL, NULL, 0, NULL, '2026-03-08 19:37:27', '2026-03-08 19:37:27', NULL),
(15, 18, 2, 'SUR-A-9782', NULL, NULL, 800.0000, 1000.00, 800000.00, 0.00, 800000.00, 0.2000, 160000.00, NULL, NULL, 0, NULL, '2026-03-08 19:37:27', '2026-03-08 19:37:27', NULL),
(16, 19, 2, 'SUR-L-6510', NULL, NULL, 600.0000, 1000.00, 600000.00, 0.00, 600000.00, 0.2000, 120000.00, NULL, NULL, 0, NULL, '2026-03-08 19:37:27', '2026-03-08 19:37:27', NULL),
(17, 21, 3, NULL, NULL, NULL, 20.0000, 120.00, 2400.00, 0.00, 2400.00, 0.4000, 960.00, NULL, NULL, 0, NULL, '2026-03-09 02:49:49', '2026-03-09 02:49:49', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `faas_land_improvements`
--

CREATE TABLE `faas_land_improvements` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `land_id` bigint(20) UNSIGNED NOT NULL,
  `improvement_id` bigint(20) UNSIGNED NOT NULL,
  `quantity` decimal(15,2) NOT NULL DEFAULT 1.00,
  `unit_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `total_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `depreciation_rate` decimal(5,2) NOT NULL DEFAULT 0.00,
  `remaining_value_percent` decimal(5,2) NOT NULL DEFAULT 100.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faas_land_improvements`
--

INSERT INTO `faas_land_improvements` (`id`, `land_id`, `improvement_id`, `quantity`, `unit_value`, `total_value`, `depreciation_rate`, `remaining_value_percent`, `created_at`, `updated_at`) VALUES
(4, 15, 2, 1.00, 670.00, 670.00, 0.00, 100.00, '2026-02-19 23:39:02', '2026-02-19 23:39:02'),
(5, 21, 4, 1.00, 1200.00, 1200.00, 0.00, 100.00, '2026-02-20 01:16:43', '2026-02-20 01:16:43');

-- --------------------------------------------------------

--
-- Table structure for table `faas_machineries`
--

CREATE TABLE `faas_machineries` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `faas_property_id` bigint(20) UNSIGNED NOT NULL,
  `faas_land_id` bigint(20) UNSIGNED DEFAULT NULL,
  `rpta_actual_use_id` bigint(20) UNSIGNED DEFAULT NULL,
  `machine_name` varchar(255) NOT NULL,
  `brand` varchar(255) DEFAULT NULL,
  `model_no` varchar(255) DEFAULT NULL,
  `serial_no` varchar(255) DEFAULT NULL,
  `year_acquired` year(4) DEFAULT NULL,
  `original_cost` decimal(18,2) NOT NULL DEFAULT 0.00,
  `useful_life` int(11) NOT NULL DEFAULT 10,
  `depreciation_rate` decimal(5,4) NOT NULL DEFAULT 0.0000,
  `depreciation_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `market_value` decimal(18,2) NOT NULL DEFAULT 0.00,
  `assessment_level` decimal(5,4) NOT NULL DEFAULT 0.0000,
  `assessed_value` decimal(18,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `faas_machines`
--

CREATE TABLE `faas_machines` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `faas_id` bigint(20) UNSIGNED NOT NULL,
  `td_no` varchar(50) DEFAULT NULL,
  `pin` varchar(50) DEFAULT NULL,
  `machine_name` varchar(255) NOT NULL,
  `brand_model` varchar(255) DEFAULT NULL,
  `serial_no` varchar(100) DEFAULT NULL,
  `capacity` varchar(100) DEFAULT NULL,
  `supplier_vendor` varchar(255) DEFAULT NULL,
  `year_manufactured` smallint(5) UNSIGNED DEFAULT NULL,
  `date_installed` date DEFAULT NULL,
  `acquisition_date` date DEFAULT NULL,
  `condition` varchar(50) DEFAULT NULL,
  `useful_life` smallint(5) UNSIGNED DEFAULT NULL,
  `remaining_life` smallint(5) UNSIGNED DEFAULT NULL,
  `invoice_no` varchar(100) DEFAULT NULL,
  `funding_source` varchar(255) DEFAULT NULL,
  `acquisition_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `freight_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `installation_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `other_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `year_acquired` smallint(5) UNSIGNED DEFAULT NULL,
  `age` smallint(5) UNSIGNED DEFAULT NULL,
  `depreciation_rate` decimal(8,2) DEFAULT NULL,
  `base_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `salvage_value_percent` decimal(6,2) NOT NULL DEFAULT 20.00,
  `residual_mode` enum('auto','manual') NOT NULL DEFAULT 'auto',
  `residual_percent` decimal(6,2) NOT NULL DEFAULT 100.00,
  `total_cost` decimal(15,2) DEFAULT NULL,
  `market_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `assessment_level` decimal(6,2) NOT NULL DEFAULT 0.00,
  `assessed_value` decimal(15,2) NOT NULL DEFAULT 0.00,
  `assmt_kind` varchar(100) DEFAULT NULL,
  `actual_use` varchar(100) DEFAULT NULL,
  `rev_year` varchar(10) DEFAULT NULL,
  `effectivity_date` date DEFAULT NULL,
  `status` enum('ACTIVE','RETIRED') NOT NULL DEFAULT 'ACTIVE',
  `remarks` text DEFAULT NULL,
  `memoranda` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faas_machines`
--

INSERT INTO `faas_machines` (`id`, `faas_id`, `td_no`, `pin`, `machine_name`, `brand_model`, `serial_no`, `capacity`, `supplier_vendor`, `year_manufactured`, `date_installed`, `acquisition_date`, `condition`, `useful_life`, `remaining_life`, `invoice_no`, `funding_source`, `acquisition_cost`, `freight_cost`, `installation_cost`, `other_cost`, `year_acquired`, `age`, `depreciation_rate`, `base_value`, `salvage_value_percent`, `residual_mode`, `residual_percent`, `total_cost`, `market_value`, `assessment_level`, `assessed_value`, `assmt_kind`, `actual_use`, `rev_year`, `effectivity_date`, `status`, `remarks`, `memoranda`, `created_at`, `updated_at`, `deleted_at`) VALUES
(2, 52, 'TD-000000002', '2222222', 'kuliglig', 'rusi', '321', '1', 'Ger', 2015, '2024-01-01', '2025-01-21', 'Good', 2, NULL, NULL, NULL, 299.00, 20.00, 15.00, 22.00, NULL, NULL, NULL, 356.00, 2.00, 'auto', 50.00, NULL, 178.00, 20.00, 35.60, 'RESIDENTIAL', 'RESIDENTIAL', '2026', '2026-02-21', 'ACTIVE', NULL, NULL, '2026-02-20 20:03:52', '2026-02-20 20:19:46', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `faas_owners`
--

CREATE TABLE `faas_owners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `faas_id` bigint(20) UNSIGNED NOT NULL,
  `owner_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faas_owners`
--

INSERT INTO `faas_owners` (`id`, `faas_id`, `owner_id`, `created_at`, `updated_at`) VALUES
(41, 44, 4, NULL, NULL),
(42, 44, 2, NULL, NULL),
(43, 45, 2, NULL, NULL),
(44, 46, 3, NULL, NULL),
(48, 50, 4, NULL, NULL),
(49, 51, 2, NULL, NULL),
(50, 52, 2, NULL, NULL),
(51, 51, 5, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `faas_predecessors`
--

CREATE TABLE `faas_predecessors` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `faas_property_id` bigint(20) UNSIGNED NOT NULL,
  `previous_faas_property_id` bigint(20) UNSIGNED NOT NULL,
  `relation_type` varchar(255) NOT NULL DEFAULT 'subdivision',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faas_predecessors`
--

INSERT INTO `faas_predecessors` (`id`, `faas_property_id`, `previous_faas_property_id`, `relation_type`, `created_at`, `updated_at`) VALUES
(1, 7, 3, 'subdivision', '2026-03-07 18:54:20', '2026-03-07 18:54:20'),
(2, 8, 3, 'subdivision', '2026-03-07 18:54:20', '2026-03-07 18:54:20');

-- --------------------------------------------------------

--
-- Table structure for table `faas_properties`
--

CREATE TABLE `faas_properties` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `property_registration_id` bigint(20) UNSIGNED DEFAULT NULL,
  `property_type` varchar(255) NOT NULL COMMENT 'land, building, machinery, mixed',
  `parent_land_faas_id` bigint(20) UNSIGNED DEFAULT NULL,
  `effectivity_date` date DEFAULT NULL,
  `revision_type` varchar(255) DEFAULT NULL COMMENT 'New Discovery, Reassessment, etc.',
  `arp_no` varchar(255) DEFAULT NULL COMMENT 'Assessment Roll Number — generated on approval',
  `pin` varchar(255) DEFAULT NULL COMMENT 'Property Identification Number',
  `section_no` varchar(3) DEFAULT NULL,
  `parcel_no` varchar(2) DEFAULT NULL,
  `owner_name` varchar(255) NOT NULL,
  `owner_tin` varchar(255) DEFAULT NULL,
  `owner_address` varchar(255) DEFAULT NULL,
  `owner_contact` varchar(255) DEFAULT NULL,
  `administrator_name` varchar(255) DEFAULT NULL,
  `administrator_address` varchar(255) DEFAULT NULL,
  `barangay_id` bigint(20) UNSIGNED DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `municipality` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `title_no` varchar(255) DEFAULT NULL,
  `lot_no` varchar(255) DEFAULT NULL,
  `blk_no` varchar(255) DEFAULT NULL,
  `survey_no` varchar(255) DEFAULT NULL,
  `boundary_north` varchar(255) DEFAULT NULL,
  `boundary_south` varchar(255) DEFAULT NULL,
  `boundary_east` varchar(255) DEFAULT NULL,
  `boundary_west` varchar(255) DEFAULT NULL,
  `status` enum('draft','for_review','recommended','approved','cancelled','inactive') DEFAULT 'draft',
  `revision_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `inactive_at` timestamp NULL DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `return_remarks` text DEFAULT NULL,
  `previous_faas_property_id` bigint(20) UNSIGNED DEFAULT NULL COMMENT 'For General Revision: the superseded FAAS record',
  `car_no` varchar(255) DEFAULT NULL COMMENT 'BIR Certificate Authorizing Registration',
  `car_date` date DEFAULT NULL,
  `transfer_tax_receipt_no` varchar(255) DEFAULT NULL COMMENT 'Local Transfer Tax Receipt',
  `transfer_tax_receipt_date` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `polygon_coordinates` longtext DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faas_properties`
--

INSERT INTO `faas_properties` (`id`, `property_registration_id`, `property_type`, `parent_land_faas_id`, `effectivity_date`, `revision_type`, `arp_no`, `pin`, `section_no`, `parcel_no`, `owner_name`, `owner_tin`, `owner_address`, `owner_contact`, `administrator_name`, `administrator_address`, `barangay_id`, `street`, `municipality`, `province`, `title_no`, `lot_no`, `blk_no`, `survey_no`, `boundary_north`, `boundary_south`, `boundary_east`, `boundary_west`, `status`, `revision_year_id`, `inactive_at`, `created_by`, `approved_by`, `approved_at`, `remarks`, `return_remarks`, `previous_faas_property_id`, `car_no`, `car_date`, `transfer_tax_receipt_no`, `transfer_tax_receipt_date`, `created_at`, `updated_at`, `deleted_at`, `polygon_coordinates`) VALUES
(1, 3, 'land', NULL, '2026-03-06', 'New Discovery', NULL, NULL, NULL, NULL, 'rara Dela Cruz', '1122333', 'Philippines', '0987654321', 'UIOP POIUY QWERTY', 'Philippines', 3, 'Victoria, Mallig, Isabela', 'Los Baños', 'Laguna', NULL, '2656', '2', 'adsad', NULL, NULL, NULL, NULL, 'draft', NULL, NULL, 2, NULL, NULL, 'Auto-draft from Registration #3', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-06 07:19:28', '2026-03-06 07:19:28', NULL, NULL),
(2, 2, 'land', NULL, '2026-03-06', 'New Discovery', '00-0002-00001', NULL, NULL, NULL, 'Juan Dela Cruz', '1122333', 'Philippines', '0987654321', 'UIOP POIUY QWERTY', 'Philippines', 1, 'Victoria, Mallig, Isabela', 'Los Baños', 'Laguna', NULL, '2656', '2', 'adsad', NULL, NULL, NULL, NULL, 'approved', NULL, NULL, 2, 2, '2026-03-06 07:39:50', 'Auto-draft from Registration #2', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-06 07:20:28', '2026-03-06 07:39:50', NULL, NULL),
(3, 1, 'land', NULL, '2026-03-08', 'New Discovery', '00-0007-00002', NULL, NULL, NULL, 'Juan Dela Cruz', '1122333', 'Philippines', '0987654321', 'UIOP POIUY QWERTY', 'Philippines', 4, 'Victoria, Mallig, Isabela', 'Los Baños', 'Laguna', NULL, '2656', '2', 'adsad', NULL, NULL, NULL, NULL, 'inactive', 2, '2026-03-07 18:54:20', 2, 2, '2026-03-07 18:38:17', 'DRAFT FAAS based on Intake Registration #1 | SUBDIVIDED into 2 parcels.', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-07 18:36:30', '2026-03-07 18:54:20', NULL, NULL),
(7, NULL, 'land', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'wawa', NULL, NULL, NULL, 'UIOP POIUY QWERTY', 'Philippines', 4, 'Victoria, Mallig, Isabela', 'Los Baños', 'Laguna', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', NULL, NULL, 2, NULL, NULL, 'Subdivision from Mother ARP: 00-0007-00002', NULL, 3, NULL, NULL, NULL, NULL, '2026-03-07 18:54:20', '2026-03-07 18:54:20', NULL, NULL),
(8, NULL, 'land', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'rara', NULL, NULL, NULL, 'UIOP POIUY QWERTY', 'Philippines', 4, 'Victoria, Mallig, Isabela', 'Los Baños', 'Laguna', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'draft', NULL, NULL, 2, NULL, NULL, 'Subdivision from Mother ARP: 00-0007-00002', NULL, 3, NULL, NULL, NULL, NULL, '2026-03-07 18:54:20', '2026-03-07 18:54:20', NULL, NULL),
(9, 4, 'land', NULL, '2026-03-09', 'New Discovery', '00-0002-00003', NULL, NULL, NULL, 'rara Dela Cruz', '1122333', 'Philippines', '0987654321', 'UIOP POIUY QWERTY', 'Philippines', 1, 'Victoria, Mallig, Isabela', 'Los Baños', 'Laguna', NULL, '2656', '2', 'adsad', NULL, NULL, NULL, NULL, 'inactive', 2, '2026-03-08 17:52:07', 2, 2, '2026-03-08 17:36:46', 'Auto-draft from Registration #4', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 17:35:47', '2026-03-08 17:52:07', NULL, NULL),
(10, NULL, 'land', NULL, NULL, 'TRANSFER', '00-0002-00004', NULL, NULL, NULL, 'Marksa Dela Cuzasd', '123-123-123-123', 'Laguna', NULL, 'UIOP POIUY QWERTY', 'Philippines', 1, 'Victoria, Mallig, Isabela', 'Los Baños', 'Laguna', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, 2, 2, '2026-03-08 17:52:07', 'Deed of Sale', NULL, 9, NULL, NULL, NULL, NULL, '2026-03-08 17:50:59', '2026-03-08 17:52:07', NULL, NULL),
(11, 5, 'land', NULL, '2026-03-09', 'New Discovery', '00-0002-00005', NULL, NULL, NULL, 'rara Dela Cruz', '1122333', 'Philippines', '0987654321', 'UIOP POIUY QWERTY', 'Philippines', 1, 'Victoria, Mallig, Isabela', 'Los Baños', 'Laguna', NULL, '2656', '2', 'adsad', NULL, NULL, NULL, NULL, 'approved', 2, NULL, 2, 2, '2026-03-08 19:02:51', 'Auto-draft from Registration #5', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 19:01:52', '2026-03-08 19:02:51', NULL, NULL),
(12, NULL, 'L', NULL, NULL, NULL, 'ARP-PROMPT-1764', 'PIN-PROMPT-3283', NULL, NULL, 'John Doe (Prompt Test)', NULL, '123 Fake Street', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 19:10:52', '2026-03-08 19:10:52', NULL, NULL),
(13, NULL, 'L', NULL, NULL, NULL, 'ARP-PROMPT-4540', 'PIN-PROMPT-5531', NULL, NULL, 'John Doe (Prompt Test)', NULL, '123 Fake Street', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 19:12:24', '2026-03-08 19:12:24', NULL, NULL),
(14, NULL, 'L', NULL, NULL, NULL, 'ARP-PROMPT-3414', 'PIN-PROMPT-7751', NULL, NULL, 'John Doe (Prompt Test)', NULL, '123 Fake Street', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 19:13:00', '2026-03-08 19:13:00', NULL, NULL),
(15, NULL, 'L', NULL, NULL, NULL, 'ARP-PROMPT-9722', 'PIN-PROMPT-4661', NULL, NULL, 'John Doe (Prompt Test)', NULL, '123 Fake Street', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 19:13:44', '2026-03-08 19:13:44', NULL, NULL),
(16, NULL, 'L', NULL, NULL, NULL, 'ARP-ADVANCE-7496', 'PIN-ADVANCE-3685', NULL, NULL, 'Jane Smith (Advance Test)', NULL, '456 Real Street', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 19:13:44', '2026-03-08 19:13:44', NULL, NULL),
(17, NULL, 'L', NULL, NULL, NULL, 'ARP-PROMPT-7064', 'PIN-PROMPT-1925', NULL, NULL, 'John Doe (Prompt Test)', NULL, '123 Fake Street', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 19:37:27', '2026-03-08 19:37:27', NULL, NULL),
(18, NULL, 'L', NULL, NULL, NULL, 'ARP-ADVANCE-7240', 'PIN-ADVANCE-9800', NULL, NULL, 'Jane Smith (Advance Test)', NULL, '456 Real Street', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 19:37:27', '2026-03-08 19:37:27', NULL, NULL),
(19, NULL, 'L', NULL, NULL, NULL, 'ARP-LATE-4174', 'PIN-LATE-9467', NULL, NULL, 'Bob Brown (Delinquent Test)', NULL, '789 Late Street', NULL, NULL, NULL, 1, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-08 19:37:27', '2026-03-08 19:37:27', NULL, NULL),
(20, 6, 'land', NULL, '2026-03-09', 'New Discovery', NULL, NULL, NULL, NULL, 'rara Dela Cruz', '1122333', 'Philippines', '0987654321', 'UIOP POIUY QWERTY', 'Philippines', 4, 'Victoria, Mallig, Isabela', 'Los Baños', 'Laguna', NULL, '2656', '2', 'adsad', NULL, NULL, NULL, NULL, 'draft', 2, NULL, 2, NULL, NULL, 'DRAFT FAAS based on Intake Registration #6', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-09 00:35:07', '2026-03-09 00:35:07', NULL, NULL),
(21, NULL, 'land', NULL, NULL, NULL, '00-0002-04175', NULL, NULL, NULL, 'rara Dela Cruz', '1122333', 'Philippines', '0987654321', NULL, NULL, 1, NULL, NULL, NULL, '2222', NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', 2, NULL, 2, 2, '2026-03-09 02:50:21', 'Created from online application: RPT-20260309-6CA9A', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-09 02:46:06', '2026-03-09 02:50:21', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `faas_revision_logs`
--

CREATE TABLE `faas_revision_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `faas_id` bigint(20) UNSIGNED NOT NULL,
  `component_id` bigint(20) UNSIGNED DEFAULT NULL,
  `component_type` varchar(255) DEFAULT NULL,
  `revision_type` varchar(255) NOT NULL,
  `reason` text DEFAULT NULL,
  `old_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_values`)),
  `new_values` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_values`)),
  `encoded_by` varchar(255) NOT NULL,
  `revision_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faas_revision_logs`
--

INSERT INTO `faas_revision_logs` (`id`, `faas_id`, `component_id`, `component_type`, `revision_type`, `reason`, `old_values`, `new_values`, `encoded_by`, `revision_date`, `created_at`, `updated_at`) VALUES
(33, 44, 15, 'LAND', 'Correction of Entry (CE)', 'change entry', '{\"component\":{\"id\":15,\"faas_id\":44,\"lot_no\":\"2656\",\"block\":\"123\",\"survey_no\":\"adsad\",\"zoning\":\"123\",\"use_restrictions\":\"NA\",\"is_corner\":\"0\",\"road_type\":\"Barangay\",\"location_class\":\"Interior\",\"area\":\"2051.9000\",\"assmt_kind\":\"RESIDENTIAL\",\"actual_use\":\"RESIDENTIAL\",\"unit_value\":\"690.00\",\"adjustment_factor\":\"20.00\",\"assessment_level\":\"15.00\",\"market_value\":\"1699643.20\",\"assessed_value\":\"254946.48\",\"effectivity_date\":\"2027-10-01T00:00:00.000000Z\",\"remarks\":null,\"improvement_kind_id\":null,\"memoranda\":null,\"created_at\":\"2026-02-20T07:30:31.000000Z\",\"updated_at\":\"2026-02-20T07:30:31.000000Z\"},\"master\":{\"id\":44,\"kind\":null,\"transaction_type\":\"NEW\",\"td_no\":\"TD-000000001\",\"revision_type\":null,\"reason\":null,\"memoranda\":null,\"effectivity_quarter\":3,\"effectivity_year\":2027,\"approved_by\":\"sample\",\"date_approved\":\"2026-02-20\",\"draft_id\":\"TD-000000001\",\"pin\":\"023-16-0010-004-13\",\"lot_no\":null,\"arpn\":\"987654321\",\"total_market_value\":\"1700992.20\",\"total_assessed_value\":\"256025.68\",\"revised_year\":2026,\"gen_rev\":2026,\"bcode\":\"0007\",\"rev_unit_val\":\"0\",\"gen_desc\":\"\",\"inspection_date\":null,\"inspected_by\":null,\"inspection_remarks\":null,\"previous_td_id\":null,\"statt\":\"ACTIVE\",\"encoded_by\":\"sample\",\"entry_date\":\"2026-02-20T00:00:00.000000Z\",\"entry_by\":\"sample\",\"encoded_date\":\"2026-02-20 15:27:09\",\"created_at\":\"2026-02-20T07:27:09.000000Z\",\"updated_at\":\"2026-02-20T07:38:22.000000Z\"}}', '{\"component\":{\"id\":15,\"faas_id\":44,\"lot_no\":\"2656\",\"block\":\"123\",\"survey_no\":\"adsad\",\"zoning\":\"123\",\"use_restrictions\":\"NA\",\"is_corner\":\"0\",\"road_type\":\"Barangay\",\"location_class\":\"Interior\",\"area\":\"2051.9000\",\"assmt_kind\":\"RESIDENTIAL\",\"actual_use\":\"RESIDENTIAL\",\"unit_value\":\"690.00\",\"adjustment_factor\":\"20.00\",\"assessment_level\":\"15.00\",\"market_value\":\"1699643.20\",\"assessed_value\":\"254946.48\",\"effectivity_date\":\"2027-10-01T00:00:00.000000Z\",\"remarks\":null,\"improvement_kind_id\":null,\"memoranda\":null,\"created_at\":\"2026-02-20T07:30:31.000000Z\",\"updated_at\":\"2026-02-20T07:30:31.000000Z\"},\"master\":{\"id\":44,\"kind\":null,\"transaction_type\":\"NEW\",\"td_no\":\"TD-000000001\",\"revision_type\":null,\"reason\":null,\"memoranda\":null,\"effectivity_quarter\":3,\"effectivity_year\":2027,\"approved_by\":\"sample\",\"date_approved\":\"2026-02-20\",\"draft_id\":\"TD-000000001\",\"pin\":\"023-16-0010-004-13\",\"lot_no\":null,\"arpn\":\"987654321\",\"total_market_value\":\"1700992.20\",\"total_assessed_value\":\"256025.68\",\"revised_year\":2026,\"gen_rev\":2026,\"bcode\":\"0007\",\"rev_unit_val\":\"0\",\"gen_desc\":\"\",\"inspection_date\":null,\"inspected_by\":null,\"inspection_remarks\":null,\"previous_td_id\":null,\"statt\":\"ACTIVE\",\"encoded_by\":\"sample\",\"entry_date\":\"2026-02-20T00:00:00.000000Z\",\"entry_by\":\"sample\",\"encoded_date\":\"2026-02-20 15:27:09\",\"created_at\":\"2026-02-20T07:27:09.000000Z\",\"updated_at\":\"2026-02-20T07:38:22.000000Z\"}}', 'sample', '2026-02-20 07:39:02', '2026-02-19 23:39:02', '2026-02-19 23:39:02'),
(34, 45, NULL, 'MASTER', 'SUBDIV', 'Split (Partitioned from TD-000000001)', '{\"td_no\":\"TD-000000001\",\"id\":44}', '{\"td_no\":\"TD-00000054\",\"id\":45}', 'sample', '2026-02-20 07:54:16', '2026-02-19 23:54:16', '2026-02-19 23:54:16'),
(35, 46, NULL, 'MASTER', 'SUBDIV', 'Split (Partitioned from TD-000000001)', '{\"td_no\":\"TD-000000001\",\"id\":44}', '{\"td_no\":\"TD-00000055\",\"id\":46}', 'sample', '2026-02-20 07:54:16', '2026-02-19 23:54:16', '2026-02-19 23:54:16'),
(37, 50, NULL, 'MASTER', 'SUBDIV', 'split (Partitioned from TD-00000054)', '{\"td_no\":\"TD-00000054\",\"id\":45}', '{\"td_no\":\"TD-00000071\",\"id\":50}', 'sample', '2026-02-20 09:16:43', '2026-02-20 01:16:43', '2026-02-20 01:16:43'),
(38, 51, NULL, 'MASTER', 'SUBDIV', 'split (Partitioned from TD-00000054)', '{\"td_no\":\"TD-00000054\",\"id\":45}', '{\"td_no\":\"TD-00000072\",\"id\":51}', 'sample', '2026-02-20 09:16:43', '2026-02-20 01:16:43', '2026-02-20 01:16:43'),
(39, 52, 2, 'MACH', 'Correction of Entry (CE)', 'REVISE', '{\"component\":{\"id\":2,\"faas_id\":52,\"td_no\":\"TD-000000002\",\"pin\":\"2222222\",\"machine_name\":\"kuliglig\",\"brand_model\":\"rusi\",\"serial_no\":\"321\",\"capacity\":\"1\",\"supplier_vendor\":\"Ger\",\"year_manufactured\":2015,\"date_installed\":\"2024-01-01T00:00:00.000000Z\",\"acquisition_date\":\"2025-01-21T00:00:00.000000Z\",\"condition\":\"Good\",\"useful_life\":2,\"remaining_life\":null,\"invoice_no\":null,\"funding_source\":null,\"acquisition_cost\":\"199.00\",\"freight_cost\":\"20.00\",\"installation_cost\":\"15.00\",\"other_cost\":\"22.00\",\"year_acquired\":null,\"age\":1,\"depreciation_rate\":null,\"base_value\":\"256.00\",\"salvage_value_percent\":\"2.00\",\"residual_mode\":\"auto\",\"residual_percent\":\"50.00\",\"total_cost\":null,\"market_value\":\"128.00\",\"assessment_level\":\"22.00\",\"assessed_value\":\"28.16\",\"assmt_kind\":\"RESIDENTIAL\",\"actual_use\":null,\"rev_year\":\"2026\",\"effectivity_date\":\"2026-02-21T00:00:00.000000Z\",\"status\":\"ACTIVE\",\"remarks\":null,\"memoranda\":null,\"created_at\":\"2026-02-21T04:03:52.000000Z\",\"updated_at\":\"2026-02-21T04:03:52.000000Z\",\"deleted_at\":null},\"master\":{\"id\":52,\"kind\":null,\"transaction_type\":\"NEW\",\"td_no\":\"TD-000000002\",\"revision_type\":null,\"reason\":null,\"memoranda\":null,\"effectivity_quarter\":4,\"effectivity_year\":2027,\"approved_by\":\"sample\",\"date_approved\":\"2026-02-20\",\"draft_id\":\"TD-000000002\",\"pin\":\"2222222\",\"lot_no\":null,\"arpn\":\"1111111\",\"total_market_value\":\"128.00\",\"total_assessed_value\":\"28.16\",\"revised_year\":2026,\"gen_rev\":2026,\"bcode\":\"0003\",\"rev_unit_val\":\"0\",\"gen_desc\":\"\",\"inspection_date\":null,\"inspected_by\":null,\"inspection_remarks\":null,\"previous_td_id\":null,\"statt\":\"ACTIVE\",\"encoded_by\":\"sample\",\"entry_date\":\"2026-02-20T00:00:00.000000Z\",\"entry_by\":\"sample\",\"encoded_date\":\"2026-02-20 18:09:25\",\"created_at\":\"2026-02-20T10:09:25.000000Z\",\"updated_at\":\"2026-02-21T04:03:52.000000Z\"}}', '{\"component\":{\"id\":2,\"faas_id\":52,\"td_no\":\"TD-000000002\",\"pin\":\"2222222\",\"machine_name\":\"kuliglig\",\"brand_model\":\"rusi\",\"serial_no\":\"321\",\"capacity\":\"1\",\"supplier_vendor\":\"Ger\",\"year_manufactured\":2015,\"date_installed\":\"2024-01-01T00:00:00.000000Z\",\"acquisition_date\":\"2025-01-21T00:00:00.000000Z\",\"condition\":\"Good\",\"useful_life\":2,\"remaining_life\":null,\"invoice_no\":null,\"funding_source\":null,\"acquisition_cost\":\"299.00\",\"freight_cost\":\"20.00\",\"installation_cost\":\"15.00\",\"other_cost\":\"22.00\",\"year_acquired\":null,\"age\":1,\"depreciation_rate\":null,\"base_value\":\"356.00\",\"salvage_value_percent\":\"2.00\",\"residual_mode\":\"auto\",\"residual_percent\":\"50.00\",\"total_cost\":null,\"market_value\":\"178.00\",\"assessment_level\":\"0.00\",\"assessed_value\":\"0.00\",\"assmt_kind\":\"RESIDENTIAL\",\"actual_use\":\"RESIDENTIAL\",\"rev_year\":\"2026\",\"effectivity_date\":\"2026-02-21T00:00:00.000000Z\",\"status\":\"ACTIVE\",\"remarks\":null,\"memoranda\":null,\"created_at\":\"2026-02-21T04:03:52.000000Z\",\"updated_at\":\"2026-02-21T04:19:19.000000Z\",\"deleted_at\":null},\"master\":{\"id\":52,\"kind\":null,\"transaction_type\":\"NEW\",\"td_no\":\"TD-000000002\",\"revision_type\":null,\"reason\":null,\"memoranda\":null,\"effectivity_quarter\":4,\"effectivity_year\":2027,\"approved_by\":\"sample\",\"date_approved\":\"2026-02-20\",\"draft_id\":\"TD-000000002\",\"pin\":\"2222222\",\"lot_no\":null,\"arpn\":\"1111111\",\"total_market_value\":\"128.00\",\"total_assessed_value\":\"28.16\",\"revised_year\":2026,\"gen_rev\":2026,\"bcode\":\"0003\",\"rev_unit_val\":\"0\",\"gen_desc\":\"\",\"inspection_date\":null,\"inspected_by\":null,\"inspection_remarks\":null,\"previous_td_id\":null,\"statt\":\"ACTIVE\",\"encoded_by\":\"sample\",\"entry_date\":\"2026-02-20T00:00:00.000000Z\",\"entry_by\":\"sample\",\"encoded_date\":\"2026-02-20 18:09:25\",\"created_at\":\"2026-02-20T10:09:25.000000Z\",\"updated_at\":\"2026-02-21T04:03:52.000000Z\"}}', 'sample', '2026-02-21 04:19:19', '2026-02-20 20:19:19', '2026-02-20 20:19:19'),
(40, 52, 2, 'MACH', 'Re-classification (RE)', 'SPLKIT', '{\"component\":{\"id\":2,\"faas_id\":52,\"td_no\":\"TD-000000002\",\"pin\":\"2222222\",\"machine_name\":\"kuliglig\",\"brand_model\":\"rusi\",\"serial_no\":\"321\",\"capacity\":\"1\",\"supplier_vendor\":\"Ger\",\"year_manufactured\":2015,\"date_installed\":\"2024-01-01T00:00:00.000000Z\",\"acquisition_date\":\"2025-01-21T00:00:00.000000Z\",\"condition\":\"Good\",\"useful_life\":2,\"remaining_life\":null,\"invoice_no\":null,\"funding_source\":null,\"acquisition_cost\":\"299.00\",\"freight_cost\":\"20.00\",\"installation_cost\":\"15.00\",\"other_cost\":\"22.00\",\"year_acquired\":null,\"age\":1,\"depreciation_rate\":null,\"base_value\":\"356.00\",\"salvage_value_percent\":\"2.00\",\"residual_mode\":\"auto\",\"residual_percent\":\"50.00\",\"total_cost\":null,\"market_value\":\"178.00\",\"assessment_level\":\"0.00\",\"assessed_value\":\"0.00\",\"assmt_kind\":\"RESIDENTIAL\",\"actual_use\":\"RESIDENTIAL\",\"rev_year\":\"2026\",\"effectivity_date\":\"2026-02-21T00:00:00.000000Z\",\"status\":\"ACTIVE\",\"remarks\":null,\"memoranda\":null,\"created_at\":\"2026-02-21T04:03:52.000000Z\",\"updated_at\":\"2026-02-21T04:19:19.000000Z\",\"deleted_at\":null},\"master\":{\"id\":52,\"kind\":null,\"transaction_type\":\"NEW\",\"td_no\":\"TD-000000002\",\"revision_type\":null,\"reason\":null,\"memoranda\":null,\"effectivity_quarter\":4,\"effectivity_year\":2027,\"approved_by\":\"sample\",\"date_approved\":\"2026-02-20\",\"draft_id\":\"TD-000000002\",\"pin\":\"2222222\",\"lot_no\":null,\"arpn\":\"1111111\",\"total_market_value\":\"178.00\",\"total_assessed_value\":\"0.00\",\"revised_year\":2026,\"gen_rev\":2026,\"bcode\":\"0003\",\"rev_unit_val\":\"0\",\"gen_desc\":\"\",\"inspection_date\":null,\"inspected_by\":null,\"inspection_remarks\":null,\"previous_td_id\":null,\"statt\":\"ACTIVE\",\"encoded_by\":\"sample\",\"entry_date\":\"2026-02-20T00:00:00.000000Z\",\"entry_by\":\"sample\",\"encoded_date\":\"2026-02-20 18:09:25\",\"created_at\":\"2026-02-20T10:09:25.000000Z\",\"updated_at\":\"2026-02-21T04:19:19.000000Z\"}}', '{\"component\":{\"id\":2,\"faas_id\":52,\"td_no\":\"TD-000000002\",\"pin\":\"2222222\",\"machine_name\":\"kuliglig\",\"brand_model\":\"rusi\",\"serial_no\":\"321\",\"capacity\":\"1\",\"supplier_vendor\":\"Ger\",\"year_manufactured\":2015,\"date_installed\":\"2024-01-01T00:00:00.000000Z\",\"acquisition_date\":\"2025-01-21T00:00:00.000000Z\",\"condition\":\"Good\",\"useful_life\":2,\"remaining_life\":null,\"invoice_no\":null,\"funding_source\":null,\"acquisition_cost\":\"299.00\",\"freight_cost\":\"20.00\",\"installation_cost\":\"15.00\",\"other_cost\":\"22.00\",\"year_acquired\":null,\"age\":1,\"depreciation_rate\":null,\"base_value\":\"356.00\",\"salvage_value_percent\":\"2.00\",\"residual_mode\":\"auto\",\"residual_percent\":\"50.00\",\"total_cost\":null,\"market_value\":\"178.00\",\"assessment_level\":\"20.00\",\"assessed_value\":\"35.60\",\"assmt_kind\":\"RESIDENTIAL\",\"actual_use\":\"RESIDENTIAL\",\"rev_year\":\"2026\",\"effectivity_date\":\"2026-02-21T00:00:00.000000Z\",\"status\":\"ACTIVE\",\"remarks\":null,\"memoranda\":null,\"created_at\":\"2026-02-21T04:03:52.000000Z\",\"updated_at\":\"2026-02-21T04:19:46.000000Z\",\"deleted_at\":null},\"master\":{\"id\":52,\"kind\":null,\"transaction_type\":\"NEW\",\"td_no\":\"TD-000000002\",\"revision_type\":null,\"reason\":null,\"memoranda\":null,\"effectivity_quarter\":4,\"effectivity_year\":2027,\"approved_by\":\"sample\",\"date_approved\":\"2026-02-20\",\"draft_id\":\"TD-000000002\",\"pin\":\"2222222\",\"lot_no\":null,\"arpn\":\"1111111\",\"total_market_value\":\"178.00\",\"total_assessed_value\":\"0.00\",\"revised_year\":2026,\"gen_rev\":2026,\"bcode\":\"0003\",\"rev_unit_val\":\"0\",\"gen_desc\":\"\",\"inspection_date\":null,\"inspected_by\":null,\"inspection_remarks\":null,\"previous_td_id\":null,\"statt\":\"ACTIVE\",\"encoded_by\":\"sample\",\"entry_date\":\"2026-02-20T00:00:00.000000Z\",\"entry_by\":\"sample\",\"encoded_date\":\"2026-02-20 18:09:25\",\"created_at\":\"2026-02-20T10:09:25.000000Z\",\"updated_at\":\"2026-02-21T04:19:19.000000Z\"}}', 'sample', '2026-02-21 04:19:46', '2026-02-20 20:19:46', '2026-02-20 20:19:46');

-- --------------------------------------------------------

--
-- Table structure for table `faas_rpta_audit`
--

CREATE TABLE `faas_rpta_audit` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `username` varchar(255) NOT NULL,
  `action_taken` varchar(20) NOT NULL,
  `new_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`new_data`)),
  `old_data` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`old_data`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faas_rpta_audit`
--

INSERT INTO `faas_rpta_audit` (`id`, `username`, `action_taken`, `new_data`, `old_data`, `created_at`, `updated_at`) VALUES
(1, 'system', 'update', '{\"SECTION\":\"TO Revision Year\",\"REVISION YEAR\":\"2026\"}', '{\"SECTION\":\"FROM Revision Year\",\"REVISION YEAR\":2026}', '2026-02-06 21:37:40', '2026-02-06 21:37:40'),
(2, 'system', 'update', '{\"SECTION\":\"Report\\/Certificate Signatory\",\"TYPE\":\"Municipal\\/City Assessor\",\"SIGNATORY\":\"Hi\",\"DESIGNATION\":\"Assessor\"}', '{\"SECTION\":\"Report\\/Certificate Signatory\",\"TYPE\":\"Municipal\\/City Assessor\",\"SIGNATORY\":\"Provincial Assessor\",\"DESIGNATION\":\"Provincial Assessor\"}', '2026-02-06 21:38:26', '2026-02-06 21:38:26'),
(3, 'system', 'create', '{\"SECTION\":\"Addition of Signatory\",\"SIGNATORY NAME\":\"Hello\",\"DESIGNATION\":\"Assessor\",\"DATE ASSIGN\":\"2026-02-07\"}', NULL, '2026-02-06 21:38:36', '2026-02-06 21:38:36'),
(4, 'system', 'update', '{\"SECTION\":\"TO Revision Year\",\"REVISION YEAR\":\"2026\"}', '{\"SECTION\":\"FROM Revision Year\",\"REVISION YEAR\":2026}', '2026-02-06 21:57:35', '2026-02-06 21:57:35'),
(5, 'system', 'create', '{\"SECTION\":\"Addition of Signatory\",\"SIGNATORY NAME\":\"Hello2\",\"DESIGNATION\":\"Assessor\",\"DATE ASSIGN\":\"2026-02-07\"}', NULL, '2026-02-06 22:01:00', '2026-02-06 22:01:00'),
(6, 'system', 'create', '{\"SECTION\":\"Addition of Signatory\",\"SIGNATORY NAME\":\"Hello2\",\"DESIGNATION\":\"Assessor\",\"DATE ASSIGN\":\"2026-02-07\"}', NULL, '2026-02-06 22:01:37', '2026-02-06 22:01:37'),
(7, 'system', 'update', '{\"SECTION\":\"Update Signatory\",\"SIGNATORY NAME\":\"Hello3\",\"DESIGNATION\":\"Assessor\",\"DATE ASSIGN\":\"2026-02-07\"}', '{\"SECTION\":\"Previous Signatory\",\"SIGNATORY NAME\":\"Hello2\",\"DESIGNATION\":\"Assessor\",\"DATE ASSIGN\":\"2026-02-07\"}', '2026-02-06 22:05:11', '2026-02-06 22:05:11'),
(8, 'system', 'delete', NULL, '{\"SECTION\":\"Deletion of Signatory\",\"SIGNATORY NAME\":\"Hello3\",\"DESIGNATION\":\"Assessor\",\"DATE ASSIGN\":\"2026-02-07\"}', '2026-02-06 22:05:17', '2026-02-06 22:05:17'),
(9, 'system', 'delete', NULL, '{\"SECTION\":\"Deletion of Signatory\",\"SIGNATORY NAME\":\"Hello2\",\"DESIGNATION\":\"Assessor\",\"DATE ASSIGN\":\"2026-02-07\"}', '2026-02-06 22:05:19', '2026-02-06 22:05:19'),
(10, 'system', 'create', '{\"SECTION\":\"Addition of Signatory\",\"SIGNATORY NAME\":\"Hello2\",\"DESIGNATION\":\"Assessor\",\"DATE ASSIGN\":\"2026-02-07\"}', NULL, '2026-02-06 22:09:01', '2026-02-06 22:09:01'),
(11, 'system', 'update', '{\"SECTION\":\"Update Signatory\",\"SIGNATORY NAME\":\"Hello3\",\"DESIGNATION\":\"Assessor\",\"DATE ASSIGN\":\"2026-02-07\"}', '{\"SECTION\":\"Previous Signatory\",\"SIGNATORY NAME\":\"Hello2\",\"DESIGNATION\":\"Assessor\",\"DATE ASSIGN\":\"2026-02-07\"}', '2026-02-06 22:09:10', '2026-02-06 22:09:10'),
(12, 'system', 'delete', NULL, '{\"SECTION\":\"Deletion of Signatory\",\"SIGNATORY NAME\":\"Hello3\",\"DESIGNATION\":\"Assessor\",\"DATE ASSIGN\":\"2026-02-07\"}', '2026-02-06 22:09:13', '2026-02-06 22:09:13'),
(13, 'system', 'create', '{\"SECTION\":\"Addition of Signatory\",\"SIGNATORY NAME\":\"Hello2\",\"DESIGNATION\":\"Assessor\",\"DATE ASSIGN\":\"2026-02-07\"}', NULL, '2026-02-06 22:09:27', '2026-02-06 22:09:27'),
(14, 'system', 'create', '{\"SECTION\":\"Addition of Signatory\",\"SIGNATORY NAME\":\"Hello2\",\"DESIGNATION\":\"Assessor\",\"DATE ASSIGN\":\"2026-02-07\"}', NULL, '2026-02-06 22:09:28', '2026-02-06 22:09:28'),
(15, 'system', 'delete', NULL, '{\"SECTION\":\"Deletion of Signatory\",\"SIGNATORY NAME\":\"Hello2\",\"DESIGNATION\":\"Assessor\",\"DATE ASSIGN\":\"2026-02-07\"}', '2026-02-06 22:09:31', '2026-02-06 22:09:31'),
(16, 'system', 'update', '{\"SECTION\":\"Update Signatory\",\"SIGNATORY NAME\":\"Hello3\",\"DESIGNATION\":\"Assessor\",\"DATE ASSIGN\":\"2026-02-07\"}', '{\"SECTION\":\"Previous Signatory\",\"SIGNATORY NAME\":\"Hello2\",\"DESIGNATION\":\"Assessor\",\"DATE ASSIGN\":\"2026-02-07\"}', '2026-02-06 22:09:38', '2026-02-06 22:09:38'),
(17, 'system', 'delete', NULL, '{\"SECTION\":\"Deletion of Signatory\",\"SIGNATORY NAME\":\"Hello3\",\"DESIGNATION\":\"Assessor\",\"DATE ASSIGN\":\"2026-02-07\"}', '2026-02-06 22:09:40', '2026-02-06 22:09:40'),
(18, 'system', 'create', '{\"SECTION\":\"Transaction Code for RPU\",\"TRANSACTION CODE\":\"hello\",\"DESC\":\"Na\"}', NULL, '2026-02-06 22:22:05', '2026-02-06 22:22:05'),
(19, 'system', 'delete', NULL, '{\"SECTION\":\"Transaction Code for RPU\",\"TRANSACTION CODE\":\"hello\",\"DESC\":\"Na\"}', '2026-02-06 22:22:35', '2026-02-06 22:22:35'),
(20, 'system', 'create', '{\"SECTION\":\"Transaction Code for RPU\",\"TRANSACTION CODE\":\"hello\",\"DESC\":\"Na\"}', NULL, '2026-02-06 22:22:38', '2026-02-06 22:22:38'),
(21, 'system', 'delete', NULL, '{\"SECTION\":\"Transaction Code for RPU\",\"TRANSACTION CODE\":\"hello\",\"DESC\":\"Na\"}', '2026-02-06 22:22:46', '2026-02-06 22:22:46'),
(22, 'system', 'create', '{\"SECTION\":\"Transaction Code for RPU\",\"TRANSACTION CODE\":\"hello\",\"DESC\":\"Na\"}', NULL, '2026-02-06 22:22:49', '2026-02-06 22:22:49'),
(23, 'system', 'delete', NULL, '{\"SECTION\":\"Transaction Code for RPU\",\"TRANSACTION CODE\":\"hello\",\"DESC\":\"Na\"}', '2026-02-06 22:32:41', '2026-02-06 22:32:41'),
(24, 'system', 'create', '{\"SECTION\":\"Transaction Code for RPU\",\"TRANSACTION CODE\":\"hello\",\"DESC\":\"Na\"}', NULL, '2026-02-06 22:32:43', '2026-02-06 22:32:43'),
(25, 'system', 'delete', NULL, '{\"SECTION\":\"Transaction Code for RPU\",\"TRANSACTION CODE\":\"hello\",\"DESC\":\"Na\"}', '2026-02-06 22:32:46', '2026-02-06 22:32:46'),
(26, 'system', 'create', '{\"SECTION\":\"Transaction Code for RPU\",\"TRANSACTION CODE\":\"hello\",\"DESC\":\"Na\"}', NULL, '2026-02-06 22:32:48', '2026-02-06 22:32:48'),
(27, 'system', 'update', '{\"SECTION\":\"Transaction Code for RPU\",\"TRANSACTION CODE\":\"hi\",\"DESC\":\"Na\"}', '{\"SECTION\":\"Transaction Code for RPU\",\"TRANSACTION CODE\":\"hello\",\"DESC\":\"Na\"}', '2026-02-06 22:33:25', '2026-02-06 22:33:25'),
(28, 'system', 'create', '{\"SECTION\":\"Transaction Code for RPU\",\"TRANSACTION CODE\":\"CL\",\"DESC\":\"Cancellation for revision, correction, insertion of addional information, retirement of section, and others..\"}', NULL, '2026-02-19 21:01:33', '2026-02-19 21:01:33');

-- --------------------------------------------------------

--
-- Table structure for table `faas_rpta_owner_select`
--

CREATE TABLE `faas_rpta_owner_select` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `owner_address` text DEFAULT NULL,
  `owner_tel` varchar(50) DEFAULT NULL,
  `owner_tin` varchar(20) DEFAULT NULL,
  `encoded_by` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `faas_rpta_owner_select`
--

INSERT INTO `faas_rpta_owner_select` (`id`, `owner_name`, `owner_address`, `owner_tel`, `owner_tin`, `encoded_by`, `created_at`, `updated_at`) VALUES
(2, 'pogi', 'laguna', '0987654321', NULL, 'sample', '2026-02-06 01:05:43', '2026-02-15 17:34:48'),
(3, 'gwapo', 'mjj', '123456789', '987654321', 'sample', '2026-02-15 18:49:18', '2026-02-15 18:49:18'),
(4, 'handsome', 'mjj', '123124', '123123', 'sample', '2026-02-19 19:20:16', '2026-02-19 19:20:16'),
(5, 'igop', 'mjj', NULL, NULL, 'sample', '2026-02-20 00:52:05', '2026-02-20 00:52:05');

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
-- Table structure for table `fee_rules`
--

CREATE TABLE `fee_rules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `base_type` varchar(255) NOT NULL DEFAULT 'flat',
  `formula_type` varchar(255) NOT NULL,
  `flat_amount` decimal(12,4) DEFAULT NULL,
  `percentage` decimal(8,4) DEFAULT NULL,
  `rate_table` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`rate_table`)),
  `scale_table` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`scale_table`)),
  `notes` text DEFAULT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `enabled` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `fee_rules`
--

INSERT INTO `fee_rules` (`id`, `name`, `base_type`, `formula_type`, `flat_amount`, `percentage`, `rate_table`, `scale_table`, `notes`, `sort_order`, `enabled`, `created_at`, `updated_at`) VALUES
(1, 'Gross Sales Tax (LBT)', 'gross_sales', 'graduated_rate', NULL, NULL, '[{\"max\":500000,\"rate\":0.018},{\"max\":1000000,\"rate\":0.0175},{\"max\":2000000,\"rate\":0.016},{\"max\":3000000,\"rate\":0.015},{\"max\":4000000,\"rate\":0.0145},{\"max\":5000000,\"rate\":0.014},{\"max\":6500000,\"rate\":0.013},{\"max\":8000000,\"rate\":0.012},{\"max\":10000000,\"rate\":0.011},{\"max\":15000000,\"rate\":0.01},{\"max\":20000000,\"rate\":0.009},{\"max\":30000000,\"rate\":0.008},{\"max\":40000000,\"rate\":0.007},{\"max\":50000000,\"rate\":0.006},{\"max\":null,\"rate\":0.005}]', NULL, 'Local Business Tax based on graduated rates per LGU Revenue Code.', 1, 1, '2026-02-19 20:14:53', '2026-02-19 20:14:53'),
(2, 'Business Permit (Mayor\'s Permit)', 'scale', 'scale_table', NULL, NULL, NULL, '{\"1\":500,\"2\":1000,\"3\":2000,\"4\":3000,\"5\":5000}', 'Fixed amount per business scale classification.', 2, 1, '2026-02-19 20:14:53', '2026-02-19 20:14:53'),
(3, 'Garbage Fees', 'scale', 'scale_table', NULL, NULL, NULL, '{\"1\":350,\"2\":400,\"3\":450,\"4\":600,\"5\":800}', 'Solid waste management fee based on scale.', 3, 1, '2026-02-19 20:14:53', '2026-02-19 20:14:53'),
(4, 'Annual Inspection Fee', 'flat', 'flat_amount', 200.0000, NULL, NULL, NULL, 'BFP Annual Inspection — flat rate.', 4, 1, '2026-02-19 20:14:53', '2026-02-19 20:15:30'),
(5, 'Sanitary Permit Fee', 'flat', 'flat_amount', 100.0000, NULL, NULL, NULL, 'Sanitation compliance permit.', 5, 1, '2026-02-19 20:14:53', '2026-02-19 20:14:53'),
(6, 'Sticker Fee', 'flat', 'flat_amount', 200.0000, NULL, NULL, NULL, 'Business permit sticker.', 6, 1, '2026-02-19 20:14:53', '2026-02-19 20:14:53'),
(7, 'Locational / Zoning Fee', 'flat', 'flat_amount', 500.0000, NULL, NULL, NULL, 'MPDC zoning clearance fee.', 7, 1, '2026-02-19 20:14:53', '2026-02-19 20:14:53');

-- --------------------------------------------------------

--
-- Table structure for table `form_options`
--

CREATE TABLE `form_options` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `category` varchar(60) NOT NULL,
  `value` varchar(255) NOT NULL,
  `sort_order` smallint(5) UNSIGNED NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `form_options`
--

INSERT INTO `form_options` (`id`, `category`, `value`, `sort_order`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'type_of_business', 'Retail', 0, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(2, 'type_of_business', 'Wholesale', 1, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(3, 'type_of_business', 'Manufacturing', 2, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(4, 'type_of_business', 'Service', 3, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(5, 'type_of_business', 'Food & Beverage', 4, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(6, 'type_of_business', 'Construction', 5, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(7, 'type_of_business', 'Transportation', 6, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(8, 'type_of_business', 'Other', 7, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(9, 'business_organization', 'Sole Proprietorship', 0, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(10, 'business_organization', 'Partnership', 1, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(11, 'business_organization', 'Corporation', 2, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(12, 'business_organization', 'Cooperative', 3, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(13, 'business_organization', 'One Person Corporation (OPC)', 4, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(14, 'business_area_type', 'Owned', 0, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(15, 'business_area_type', 'Leased', 1, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(16, 'business_area_type', 'Rented', 2, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(17, 'business_area_type', 'Government-owned', 3, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(18, 'business_scale', 'Micro (Assets up to P3M)', 0, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(19, 'business_scale', 'Small (P3M - P15M)', 1, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(20, 'business_scale', 'Medium (P15M - P100M)', 2, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(21, 'business_scale', 'Large (Above P100M)', 3, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(22, 'business_sector', 'Agriculture', 0, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(23, 'business_sector', 'Industry', 1, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(24, 'business_sector', 'Services', 2, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(25, 'business_sector', 'Tourism', 3, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(26, 'business_sector', 'Health', 4, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(27, 'business_sector', 'Education', 5, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(28, 'business_sector', 'IT/BPO', 6, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(29, 'business_sector', 'Finance', 7, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(30, 'zone', 'Zone 1 - Commercial', 0, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(31, 'zone', 'Zone 2 - Industrial', 1, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(32, 'zone', 'Zone 3 - Residential', 2, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(33, 'zone', 'Zone 4 - Mixed Use', 3, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(34, 'zone', 'Zone 5 - Agricultural', 4, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(35, 'zone', 'Special Economic Zone', 5, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(36, 'occupancy', 'Ground Floor', 0, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(37, 'occupancy', '2nd Floor', 1, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(38, 'occupancy', '3rd Floor', 2, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(39, 'occupancy', 'Basement', 3, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(40, 'occupancy', 'Multi-level', 4, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(41, 'occupancy', 'Entire Building', 5, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(42, 'amendment_from', 'Sole Proprietorship', 0, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(43, 'amendment_from', 'Partnership', 1, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(44, 'amendment_from', 'Corporation', 2, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(45, 'amendment_from', 'Cooperative', 3, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(46, 'amendment_from', 'One Person Corporation (OPC)', 4, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(47, 'amendment_to', 'Sole Proprietorship', 0, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(48, 'amendment_to', 'Partnership', 1, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(49, 'amendment_to', 'Corporation', 2, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(50, 'amendment_to', 'Cooperative', 3, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL),
(51, 'amendment_to', 'One Person Corporation (OPC)', 4, '2026-03-08 20:48:12', '2026-03-08 20:48:12', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `hr_daily_time_records`
--

CREATE TABLE `hr_daily_time_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `record_date` date NOT NULL,
  `am_in` time DEFAULT NULL,
  `am_out` time DEFAULT NULL,
  `pm_in` time DEFAULT NULL,
  `pm_out` time DEFAULT NULL,
  `tardiness_minutes` int(11) NOT NULL DEFAULT 0,
  `undertime_minutes` int(11) NOT NULL DEFAULT 0,
  `overtime_minutes` int(11) NOT NULL DEFAULT 0,
  `is_absent` tinyint(1) NOT NULL DEFAULT 0,
  `remarks` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_deduction_types`
--

CREATE TABLE `hr_deduction_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `is_mandatory` tinyint(1) NOT NULL DEFAULT 0,
  `is_percentage` tinyint(1) NOT NULL DEFAULT 0,
  `default_rate` decimal(10,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_employee_deductions`
--

CREATE TABLE `hr_employee_deductions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `deduction_type_id` bigint(20) UNSIGNED NOT NULL,
  `amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_employee_schedules`
--

CREATE TABLE `hr_employee_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `schedule_id` bigint(20) UNSIGNED NOT NULL,
  `effective_from` date NOT NULL,
  `effective_to` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_leave_applications`
--

CREATE TABLE `hr_leave_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reference_no` varchar(50) NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `leave_type_id` bigint(20) UNSIGNED NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `total_days` decimal(5,2) NOT NULL,
  `reason` text DEFAULT NULL,
  `status` enum('pending','approved','rejected','cancelled') NOT NULL DEFAULT 'pending',
  `approver_remarks` text DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `filed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_leave_balances`
--

CREATE TABLE `hr_leave_balances` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `leave_type_id` bigint(20) UNSIGNED NOT NULL,
  `year` year(4) NOT NULL,
  `earned` decimal(6,2) NOT NULL DEFAULT 0.00,
  `used` decimal(6,2) NOT NULL DEFAULT 0.00,
  `carry_over` decimal(6,2) NOT NULL DEFAULT 0.00,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_leave_types`
--

CREATE TABLE `hr_leave_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `code` varchar(20) NOT NULL,
  `description` text DEFAULT NULL,
  `max_days_per_year` decimal(5,2) NOT NULL DEFAULT 0.00,
  `is_convertible` tinyint(1) NOT NULL DEFAULT 0,
  `requires_medical` tinyint(1) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_payroll_periods`
--

CREATE TABLE `hr_payroll_periods` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `period_name` varchar(100) NOT NULL,
  `date_from` date NOT NULL,
  `date_to` date NOT NULL,
  `status` enum('draft','finalized') NOT NULL DEFAULT 'draft',
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_payroll_records`
--

CREATE TABLE `hr_payroll_records` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `payroll_period_id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `basic_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
  `gross_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
  `total_deductions` decimal(12,2) NOT NULL DEFAULT 0.00,
  `net_pay` decimal(12,2) NOT NULL DEFAULT 0.00,
  `days_worked` int(11) NOT NULL DEFAULT 0,
  `days_absent` int(11) NOT NULL DEFAULT 0,
  `tardiness_deduction` decimal(12,2) NOT NULL DEFAULT 0.00,
  `undertime_deduction` decimal(12,2) NOT NULL DEFAULT 0.00,
  `deductions_json` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`deductions_json`)),
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_plantilla_positions`
--

CREATE TABLE `hr_plantilla_positions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_number` varchar(255) NOT NULL,
  `position_title` varchar(255) NOT NULL,
  `salary_grade_id` bigint(20) UNSIGNED NOT NULL,
  `department_id` bigint(20) UNSIGNED NOT NULL,
  `office_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employment_status` enum('Permanent','Casual','Co-terminous','Elective') NOT NULL DEFAULT 'Permanent',
  `is_filled` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_salary_grades`
--

CREATE TABLE `hr_salary_grades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `grade` int(11) NOT NULL,
  `step_1` decimal(10,2) NOT NULL,
  `step_2` decimal(10,2) NOT NULL,
  `step_3` decimal(10,2) NOT NULL,
  `step_4` decimal(10,2) NOT NULL,
  `step_5` decimal(10,2) NOT NULL,
  `step_6` decimal(10,2) NOT NULL,
  `step_7` decimal(10,2) NOT NULL,
  `step_8` decimal(10,2) NOT NULL,
  `implementation_year` varchar(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `hr_salary_grades`
--

INSERT INTO `hr_salary_grades` (`id`, `grade`, `step_1`, `step_2`, `step_3`, `step_4`, `step_5`, `step_6`, `step_7`, `step_8`, `implementation_year`, `created_at`, `updated_at`) VALUES
(1, 1, 13000.00, 13109.00, 13219.00, 13330.00, 13441.00, 13554.00, 13668.00, 13782.00, '2024', '2026-03-06 05:16:57', '2026-03-06 05:21:02'),
(2, 2, 13691.00, 13796.00, 13901.00, 14008.00, 14115.00, 14223.00, 14332.00, 14442.00, '2024', '2026-03-06 05:16:57', '2026-03-06 05:21:02'),
(3, 3, 14678.00, 14790.00, 14904.00, 15018.00, 15133.00, 15249.00, 15366.00, 15484.00, '2024', '2026-03-06 05:16:57', '2026-03-06 05:21:02'),
(4, 4, 15586.00, 15705.00, 15825.00, 15946.00, 16068.00, 16191.00, 16315.00, 16440.00, '2024', '2026-03-06 05:16:57', '2026-03-06 05:21:02'),
(5, 5, 16543.00, 16670.00, 16798.00, 16926.00, 17056.00, 17187.00, 17319.00, 17452.00, '2024', '2026-03-06 05:16:57', '2026-03-06 05:21:02'),
(6, 6, 17553.00, 17688.00, 17823.00, 17960.00, 18097.00, 18236.00, 18376.00, 18517.00, '2024', '2026-03-06 05:16:57', '2026-03-06 05:21:02'),
(7, 7, 18620.00, 18762.00, 18906.00, 19051.00, 19197.00, 19344.00, 19492.00, 19641.00, '2024', '2026-03-06 05:16:57', '2026-03-06 05:21:02'),
(8, 8, 19744.00, 19918.00, 20093.00, 20270.00, 20448.00, 20628.00, 20810.00, 20993.00, '2024', '2026-03-06 05:16:57', '2026-03-06 05:21:02'),
(9, 9, 21211.00, 21385.00, 21560.00, 21737.00, 21915.00, 22095.00, 22276.00, 22459.00, '2024', '2026-03-06 05:16:57', '2026-03-06 05:21:02'),
(10, 10, 23176.00, 23366.00, 23558.00, 23751.00, 23946.00, 24142.00, 24340.00, 24540.00, '2024', '2026-03-06 05:16:57', '2026-03-06 05:21:02'),
(11, 11, 27000.00, 27339.00, 27684.00, 28038.00, 28400.00, 28770.00, 29151.00, 29541.00, '2024', '2026-03-06 05:16:57', '2026-03-06 05:21:02'),
(12, 12, 29165.00, 29517.00, 29875.00, 30240.00, 30613.00, 30993.00, 31382.00, 31779.00, '2024', '2026-03-06 05:16:57', '2026-03-06 05:21:02'),
(13, 13, 31320.00, 31711.00, 32110.00, 32517.00, 32932.00, 33355.00, 33786.00, 34227.00, '2024', '2026-03-06 05:16:57', '2026-03-06 05:21:02'),
(14, 14, 33843.00, 34278.00, 34722.00, 35175.00, 35637.00, 36108.00, 36589.00, 37080.00, '2024', '2026-03-06 05:16:57', '2026-03-06 05:21:02'),
(15, 15, 36619.00, 37105.00, 37600.00, 38105.00, 38621.00, 39147.00, 39684.00, 40232.00, '2024', '2026-03-06 05:16:57', '2026-03-06 05:21:02'),
(16, 16, 39672.00, 40212.00, 40763.00, 41325.00, 41899.00, 42484.00, 43080.00, 43688.00, '2024', '2026-03-06 05:16:57', '2026-03-06 05:21:02'),
(17, 17, 43030.00, 43632.00, 44247.00, 44874.00, 45513.00, 46166.00, 46831.00, 47510.00, '2024', '2026-03-06 05:16:57', '2026-03-06 05:21:02'),
(18, 18, 46725.00, 47394.00, 48076.00, 48772.00, 49482.00, 50206.00, 50944.00, 51696.00, '2024', '2026-03-06 05:16:57', '2026-03-06 05:21:02'),
(19, 19, 51357.00, 52140.00, 52939.00, 53754.00, 54585.00, 55431.00, 56294.00, 57174.00, '2024', '2026-03-06 05:16:57', '2026-03-06 05:21:02'),
(20, 20, 57347.00, 58221.00, 59111.00, 60018.00, 60943.00, 61884.00, 62846.00, 63826.00, '2024', '2026-03-06 05:16:57', '2026-03-06 05:21:02');

-- --------------------------------------------------------

--
-- Table structure for table `hr_time_logs`
--

CREATE TABLE `hr_time_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `log_date` date NOT NULL,
  `log_time` time NOT NULL,
  `log_type` enum('IN','OUT') NOT NULL DEFAULT 'IN',
  `source` enum('biometric','manual') NOT NULL DEFAULT 'biometric',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `hr_work_schedules`
--

CREATE TABLE `hr_work_schedules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(100) NOT NULL,
  `am_in` time NOT NULL DEFAULT '08:00:00',
  `am_out` time NOT NULL DEFAULT '12:00:00',
  `pm_in` time NOT NULL DEFAULT '13:00:00',
  `pm_out` time NOT NULL DEFAULT '17:00:00',
  `is_default` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `interviews`
--

CREATE TABLE `interviews` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `applicant_id` bigint(20) UNSIGNED NOT NULL,
  `interviewer_id` bigint(20) UNSIGNED DEFAULT NULL,
  `interview_type` varchar(50) NOT NULL DEFAULT 'initial',
  `scheduled_at` datetime NOT NULL,
  `location` varchar(255) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `result` enum('pending','passed','failed','rescheduled','cancelled') NOT NULL DEFAULT 'pending',
  `rating` decimal(3,2) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `conducted_at` datetime DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `jobs`
--

CREATE TABLE `jobs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `queue` varchar(255) NOT NULL,
  `payload` longtext NOT NULL,
  `attempts` tinyint(3) UNSIGNED NOT NULL,
  `reserved_at` int(10) UNSIGNED DEFAULT NULL,
  `available_at` int(10) UNSIGNED NOT NULL,
  `created_at` int(10) UNSIGNED NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_batches`
--

CREATE TABLE `job_batches` (
  `id` varchar(255) NOT NULL,
  `name` varchar(255) NOT NULL,
  `total_jobs` int(11) NOT NULL,
  `pending_jobs` int(11) NOT NULL,
  `failed_jobs` int(11) NOT NULL,
  `failed_job_ids` longtext NOT NULL,
  `options` mediumtext DEFAULT NULL,
  `cancelled_at` int(11) DEFAULT NULL,
  `created_at` int(11) NOT NULL,
  `finished_at` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_positions`
--

CREATE TABLE `job_positions` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `position_name` varchar(255) NOT NULL,
  `position_code` varchar(30) NOT NULL,
  `position_description` text DEFAULT NULL,
  `office_id` bigint(20) UNSIGNED DEFAULT NULL,
  `division_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `salary_grade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `employment_type_id` bigint(20) UNSIGNED DEFAULT NULL,
  `position_level` varchar(50) DEFAULT NULL,
  `item_number` int(11) DEFAULT NULL,
  `workstation` varchar(255) DEFAULT NULL,
  `plantilla_count` int(11) NOT NULL DEFAULT 1,
  `is_vacant` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `job_vacancies`
--

CREATE TABLE `job_vacancies` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `vacancy_title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `office_id` bigint(20) UNSIGNED NOT NULL,
  `plantilla_id` bigint(20) UNSIGNED DEFAULT NULL,
  `salary_grade_id` bigint(20) UNSIGNED DEFAULT NULL,
  `number_of_positions` int(11) NOT NULL DEFAULT 1,
  `position_level` varchar(50) DEFAULT NULL,
  `qualifications` text DEFAULT NULL,
  `duties_and_responsibilities` text DEFAULT NULL,
  `posting_date` date DEFAULT NULL,
  `closing_date` date DEFAULT NULL,
  `status` enum('draft','open','closed','cancelled') NOT NULL DEFAULT 'draft',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `machinery_valuations`
--

CREATE TABLE `machinery_valuations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `machine_id` bigint(20) UNSIGNED NOT NULL,
  `td_no` varchar(50) DEFAULT NULL,
  `acquisition_cost` decimal(15,2) NOT NULL,
  `freight_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `installation_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `other_cost` decimal(15,2) NOT NULL DEFAULT 0.00,
  `base_value` decimal(15,2) NOT NULL,
  `acquisition_date` date DEFAULT NULL,
  `useful_life` smallint(5) UNSIGNED DEFAULT NULL,
  `salvage_value_percent` decimal(6,2) NOT NULL,
  `computed_age` smallint(5) UNSIGNED NOT NULL,
  `computed_dep_rate` decimal(8,4) NOT NULL,
  `residual_mode` enum('auto','manual') NOT NULL,
  `residual_used` decimal(6,2) NOT NULL,
  `assessment_level` decimal(6,2) NOT NULL,
  `market_value` decimal(15,2) NOT NULL,
  `assessed_value` decimal(15,2) NOT NULL,
  `action` varchar(20) NOT NULL DEFAULT 'created',
  `computed_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_by_name` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `machinery_valuations`
--

INSERT INTO `machinery_valuations` (`id`, `machine_id`, `td_no`, `acquisition_cost`, `freight_cost`, `installation_cost`, `other_cost`, `base_value`, `acquisition_date`, `useful_life`, `salvage_value_percent`, `computed_age`, `computed_dep_rate`, `residual_mode`, `residual_used`, `assessment_level`, `market_value`, `assessed_value`, `action`, `computed_at`, `created_by`, `created_by_name`, `created_at`, `updated_at`) VALUES
(1, 2, 'TD-000000002', 199.00, 20.00, 15.00, 22.00, 256.00, '2025-01-21', 2, 2.00, 1, 0.5000, 'auto', 50.00, 22.00, 128.00, 28.16, 'created', '2026-02-20 20:03:52', 2, NULL, '2026-02-20 20:03:52', '2026-02-20 20:03:52'),
(2, 2, 'TD-000000002', 299.00, 20.00, 15.00, 22.00, 356.00, '2025-01-21', 2, 2.00, 1, 0.5000, 'auto', 50.00, 0.00, 178.00, 0.00, 'updated', '2026-02-20 20:19:19', 2, NULL, '2026-02-20 20:19:19', '2026-02-20 20:19:19'),
(3, 2, 'TD-000000002', 299.00, 20.00, 15.00, 22.00, 356.00, '2025-01-21', 2, 2.00, 1, 0.5000, 'auto', 50.00, 20.00, 178.00, 35.60, 'updated', '2026-02-20 20:19:46', 2, NULL, '2026-02-20 20:19:46', '2026-02-20 20:19:46');

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
(1, '0001_01_01_000000_create_users_table', 1),
(2, '0001_01_01_000001_create_cache_table', 1),
(3, '0001_01_01_000002_create_jobs_table', 1),
(4, '2026_01_17_061332_create_departments_table', 1),
(5, '2026_01_17_063104_create_employee_info_table', 1),
(6, '2026_01_17_063128_modify_users_table', 1),
(7, '2026_01_19_031242_remove_email_from_users_table', 2),
(8, '2026_01_27_062616_add_columns_to_departments_table', 3),
(12, '2026_01_27_073607_create_barangays_table', 4),
(13, '2026_01_28_063707_create_rpt_au_tbl_table', 5),
(14, '2026_01_28_063729_create_faas_rpta_audit_table', 5),
(15, '2026_01_28_072904_create_rpta_additional_items_table', 6),
(16, '2026_01_28_075323_create_rpta_assmnt_lvl_table', 7),
(17, '2026_01_28_083243_create_rpt_au_value_table', 8),
(18, '2026_02_06_071910_create_rpta_deprate_bldg_table', 9),
(19, '2026_02_06_085034_create_faas_rpta_owner_select_table', 10),
(20, '2026_02_07_005740_create_rpta_other_improvement_table', 11),
(21, '2026_02_07_052929_create_rpta_rev_yr_table', 12),
(22, '2026_02_07_052929_create_rpta_signatories_table', 12),
(23, '2026_02_07_052930_create_defaultz_table', 12),
(24, '2026_02_07_061229_create_rpt_tc_tbl_table', 13),
(26, '2026_02_07_065905_create_faas_gen_rev_table', 14),
(27, '2026_02_16_024320_create_faas_lands_table', 15),
(28, '2026_02_16_024323_create_faas_owners_table', 15),
(29, '2026_02_16_025011_add_memoranda_to_faas_lands_table', 16),
(31, '2026_02_16_030038_create_faas_buildings_table', 18),
(32, '2026_02_16_032237_add_memoranda_to_faas_lands_fix', 19),
(33, '2026_02_16_034615_add_arpn_to_faas_tables', 20),
(34, '2026_02_16_043952_restructure_faas_to_td_system', 21),
(35, '2026_02_16_050521_add_pin_to_faas_gen_rev_table', 22),
(36, '2026_02_16_052115_create_faas_revision_logs_table', 23),
(37, '2026_02_16_070933_add_pin_and_lot_no_to_faas_gen_rev_table', 24),
(38, '2026_02_16_072638_add_status_workflow_fields_to_faas_gen_rev', 25),
(39, '2026_02_16_073246_create_faas_attachments_table', 26),
(40, '2026_02_18_092000_create_faas_gen_rev_geometries_table', 27),
(41, '2026_02_18_093100_add_enhancements_to_gis_and_attachments', 28),
(42, '2026_02_18_094000_add_land_use_zone_to_geometries', 29),
(44, '2026_02_19_062846_create_road_types_and_location_classes_tables', 30),
(45, '2026_02_19_062848_add_missing_fields_to_faas_lands_table', 30),
(46, '2026_02_19_080450_create_faas_land_improvements_table', 31),
(47, '2026_02_19_083721_add_code_and_condition_to_buildings_and_create_improvements_table', 32),
(48, '2026_02_19_085635_add_category_to_rpta_other_improvement_table', 33),
(49, '2026_02_19_091045_add_depreciation_to_faas_land_improvements', 34),
(51, '2026_02_19_040442_create_bpls_business_entries_table', 36),
(52, '2026_02_19_044609_create_bpls_businesses_table', 36),
(53, '2026_02_19_044609_create_bpls_owners_table', 36),
(54, '2026_02_19_084233_add_assessment_fields_to_bpls_business_entries', 36),
(55, '2026_02_20_000001_create_fee_rules_table', 36),
(56, '2026_02_20_044849_add_revision_details_to_faas_gen_rev', 37),
(57, '2026_02_20_091157_add_effectivity_fields_to_faas_lands_table', 38),
(60, 'xxxx_create_bpls_settings_table', 40),
(61, '2024_xx_xx_xxxxxx_fix_discount_settings_typo', 41),
(62, '2026_02_20_000001_add_total_due_to_bpls_business_entries', 41),
(63, '2026_02_20_000002_add_renewal_and_year_columns', 41),
(65, '2026_02_20_000003_fix_existing_payment_year_and_cycle', 42),
(66, '2026_02_21_000001_create_or_assignments_table', 42),
(67, '2026_02_21_014158_create_clients_table', 42),
(68, '2026_02_21_014159_create_bpls_applications_table', 42),
(69, '2026_02_21_014159_create_bpls_documents_table', 42),
(70, '2026_02_21_014200_create_bpls_assessments_table', 42),
(75, '2026_02_21_014200_create_bpls_online_payments_table', 43),
(76, '2026_02_21_014201_create_bpls_activity_logs_table', 43),
(77, '2026_02_20_000002_create_bpls_payments_table', 44),
(78, 'xxxx_add_discount_to_bpls_payments_table', 45),
(79, 'xxxx_xx_xx_add_retirement_fields_to_business_entries_table', 45),
(80, '2026_02_16_025247_create_faas_machines_table', 46),
(81, '2026_02_20_123232_update_faas_machines_cost_structure', 47),
(82, '2026_02_21_033750_create_machinery_valuations_table', 47),
(83, '2026_02_21_055937_add_soft_deletes_to_bpls_documents_table', 48),
(84, '2026_02_21_062532_fix_document_type_enum_on_bpls_documents_table', 49),
(85, '2026_02_21_072517_fix_workflow_status_enum_on_bpls_applications_table', 50),
(86, '2026_02_21_081201_fix_bpls_payments_table', 51),
(87, '2026_02_22_022518_add_payment_fields_to_bpls_applications_table', 52),
(89, '2026_02_22_050434_add_installment_columns_to_bpls_online_payments', 53),
(90, '2026_02_22_060935_update_bpls_online_payments_for_installments_and_paymongo', 54),
(91, '2026_02_23_034709_create_bpls_application_ors_table', 55),
(92, '2026_02_23_052149_add_ors_confirmed_to_bpls_applications_table', 56),
(93, '2024_01_01_000001_seed_bpls_receipt_settings', 57),
(94, '2025_01_01_000001_create_audit_logs_table', 57),
(95, '2026_02_23_062217_add_permit_validity_to_bpls_applications_table', 58),
(96, '2026_02_23_062342_create_bpls_permit_signatories_table', 58),
(97, '2026_02_24_000001_create_roles_table', 59),
(98, '2026_02_24_000002_create_modules_table', 59),
(99, '2026_02_24_000003_create_role_module_table', 59),
(100, '2026_02_24_000004_create_role_user_table', 59),
(101, '2026_02_24_000005_add_is_super_admin_to_users_table', 59),
(102, '2026_02_23_063333_laterenewal', 60),
(103, '2026_02_26_062311_change_payment_method_to_string_on_bpls_online_payments_table', 61),
(104, '2026_02_27_012618_add_hybrid_discount_flags_to_multiple_tables', 61),
(105, '2026_02_27_015928_change_document_type_in_bpls_documents_table', 61),
(106, '2026_03_02_084354_rename_bpls_applications_to_bpls_online_applications', 61),
(107, '2026_03_02_084434_drop_business_entry_id_from_bpls_online_applications', 61),
(108, '2026_03_03_013948_add_business_nature_to_bpls_businesses_table', 61),
(109, '2026_03_03_062345_modify_bpls_payments_for_online_applications', 61),
(110, '2026_03_04_000001_create_rpt_module_tables', 62),
(111, '2026_03_04_000002_create_faas_tables', 63),
(112, '2026_03_04_000003_create_tax_declaration_tables', 63),
(113, '2026_03_04_000004_create_rpt_online_applications_table', 63),
(114, '2026_03_04_000005_create_rpt_billing_tables', 63),
(115, '2026_03_04_004524_add_previous_faas_and_inactive_to_faas_properties', 63),
(116, '2026_03_04_024014_add_snapshots_to_tax_declarations', 63),
(117, '2026_03_04_024153_tie_settings_to_revision_year', 63),
(118, '2026_03_04_024636_create_rpta_settings_table', 63),
(119, '2026_03_04_030549_add_property_specifics_to_online_applications', 63),
(120, '2026_03_04_031319_add_construction_materials_to_faas_buildings', 63),
(121, '2026_03_04_045407_add_label_to_faas_attachments_table', 63),
(122, '2026_03_04_054811_add_missing_lifecycle_fields_to_rpt_tables', 63),
(123, '2026_03_04_061241_add_revision_year_id_to_faas_properties', 63),
(124, '2026_03_04_062831_add_expanded_rpt_fields', 63),
(125, '2026_03_04_070711_create_rpt_property_registrations_table', 63),
(126, '2026_03_04_091422_fix_rpta_assessment_levels_unique_constraint', 64),
(127, '2026_03_04_100000_harden_rpt_integrity', 64),
(128, '2026_03_05_005224_add_component_links_to_tax_declarations', 64),
(129, '2026_03_05_025710_drop_unique_td_per_year_status_from_tax_declarations', 64),
(130, '2026_03_05_033500_add_inactive_to_faas_and_td_status', 64),
(131, '2026_03_05_040000_create_faas_predecessors_table', 64),
(132, '2026_03_05_043544_add_coordinates_to_faas_lands', 64),
(133, '2026_03_05_153200_make_owner_address_nullable_in_faas_properties', 64),
(134, '2026_03_06_000001_create_offices_table', 64),
(135, '2026_03_06_000002_create_divisions_table', 64),
(136, '2026_03_06_000004_create_salary_grades_table', 65),
(137, '2026_03_06_000005_create_employment_types_table', 65),
(138, '2026_03_06_000006_create_job_positions_table', 66),
(139, '2026_03_06_000006_create_plantilla_table', 66),
(140, '2026_03_06_000007_create_job_vacancies_table', 66),
(141, '2026_03_06_000008_create_applicants_table', 66),
(142, '2026_03_06_000009_create_applicant_documents_table', 66),
(143, '2026_03_06_000010_create_interviews_table', 66),
(144, '2026_03_06_000011_create_appointments_table', 66),
(145, '2026_03_06_000012_create_employee_201_tables', 66),
(146, '2026_03_06_000013_add_office_id_to_employee_info', 66),
(147, '2026_03_06_011029_add_return_remarks_to_faas_properties_table', 66),
(148, '2026_03_06_012253_modify_status_column_in_faas_properties_table', 66),
(149, '2026_03_06_130000_add_transfer_audit_fields_to_faas_properties', 66),
(150, '2026_xx_xx_000001_seed_beneficiary_discount_bpls_settings', 66),
(151, '2026_03_06_131535_create_hr_salary_grades_table', 67),
(152, '2026_03_06_131536_create_hr_plantilla_positions_table', 68),
(153, '2026_03_06_131537_add_plantilla_fields_to_employee_info_table', 69),
(154, '2026_03_06_232500_add_missing_schema_to_faas_properties_table', 70),
(155, '2026_03_06_234500_harden_td_unique_constraints', 71),
(156, '2026_03_08_104500_add_subdivision_fields_to_faas_lands_table', 72),
(157, '2026_03_08_111000_add_pin_components_to_faas_properties_table', 73),
(158, '2026_03_08_051818_add_faas_land_id_to_faas_machineries_table', 74),
(159, '2026_03_08_000001_create_rpt_registration_attachments_table', 75),
(160, '2026_03_03_000002_backfill_business_id_for_existing_entries', 76),
(161, '2026_03_03_000002_seed_business_id_format_setting', 76),
(162, '2026_03_03_025649_alter_client_table_business_id', 76),
(163, '2026_03_03_074254_alter_table_bpls_business_entries_business_id', 77),
(164, '2026_03_05_000001_create_form_options_table', 77),
(165, '2026_03_06_000001_create_franchises_table', 77),
(166, '2026_03_06_000001_create_vf_tables', 77),
(167, '2026_03_09_030148_create_bpls_benefits_tables', 77),
(168, '2026_03_09_180000_create_client_linked_properties_table', 78),
(169, '2026_03_09_190000_expand_rpt_payment_mode_column', 79),
(170, '2026_03_12_005602_create_vf_payments_table', 80),
(171, '2024 01 01 000001_create_vf_collection_natures_table', 81),
(172, '2026_03_16_023020_create_vf_settings_and_franchise_date', 82),
(173, 'xxxx_xx_xx_create_vf_settings_and_franchise_date', 83),
(174, '2026_03_16_091330_altering_vf_francise_idk', 84),
(175, '2026_03_16_091651_altering_vf_francise_idk', 85),
(176, '2026_03_10_000001_add_capital_investment_to_bpls_businesses_table', 86),
(177, '2026_03_10_012305_add_status_to_rpt_payments', 86),
(178, '2026_03_10_085713_add_apply_to_to_bpls_benefits_table', 86),
(179, '2026_03_10_130939_add_verification_code_to_clients_table', 86),
(180, '2026_03_11_071508_add_is_vaccine_to_bpls_owners_table', 86),
(181, '2026_03_12_014808_add_polygon_coordinates_to_rpt_tables', 86),
(182, '2026_03_12_050345_add_stage3_fields_to_tax_declarations', 86),
(183, '2026_03_12_103000_add_boundary_fields_to_rpt_tables', 86),
(184, '2026_03_13_031404_add_polygon_coordinates_to_faas_properties_table', 86),
(185, '2026_03_13_085609_seed_employee_portal_module_and_role', 86),
(186, '2026_03_13_154400_create_hr_leave_types_table', 86),
(187, '2026_03_13_154401_create_hr_leave_balances_table', 86),
(188, '2026_03_13_154402_create_hr_leave_applications_table', 86),
(189, '2026_03_13_160600_create_hr_work_schedules_table', 86),
(190, '2026_03_13_160601_create_hr_employee_schedules_table', 86),
(191, '2026_03_13_160602_create_hr_time_logs_table', 86),
(192, '2026_03_13_160603_create_hr_daily_time_records_table', 86),
(193, '2026_03_13_162700_create_hr_deduction_types_table', 86),
(194, '2026_03_13_162701_create_hr_employee_deductions_table', 86),
(195, '2026_03_13_162702_create_hr_payroll_periods_table', 86),
(196, '2026_03_13_162703_create_hr_payroll_records_table', 86),
(197, '2026_03_14_042911_add_parent_land_to_rpt_property_registrations', 86),
(198, '2026_03_14_043947_add_parent_land_to_faas_properties', 86),
(199, '2026_03_14_110000_add_boundary_to_faas_properties', 86),
(200, '2026_03_17_053221_add_retirement_fields_to_vf_franchises_table', 87);

-- --------------------------------------------------------

--
-- Table structure for table `modules`
--

CREATE TABLE `modules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `route_name` varchar(255) DEFAULT NULL,
  `route_prefix` varchar(255) DEFAULT NULL,
  `icon_svg` text DEFAULT NULL,
  `sort_order` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `modules`
--

INSERT INTO `modules` (`id`, `name`, `slug`, `route_name`, `route_prefix`, `icon_svg`, `sort_order`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Admin', 'admin', 'admin.dashboard.index', 'admin', NULL, 1, 1, '2026-02-23 17:41:47', '2026-02-23 17:41:47'),
(2, 'BPLS', 'bpls', 'bpls.index', 'bpls', NULL, 2, 1, '2026-02-23 17:41:47', '2026-02-23 17:41:47'),
(3, 'RPT', 'rpt', 'rpt.index', 'rpt', NULL, 3, 1, '2026-02-23 17:41:47', '2026-02-23 17:41:47'),
(4, 'Human Resource', 'hr', 'hr.employees.index', 'hr/employees', NULL, 4, 1, '2026-02-23 17:41:47', '2026-03-06 06:55:48'),
(5, 'Treasury', 'treasury', 'treasury.index', 'treasury', NULL, 5, 1, '2026-02-23 17:41:47', '2026-02-23 17:41:47'),
(6, 'Executive', 'executive', NULL, NULL, NULL, 6, 1, '2026-02-23 17:41:47', '2026-02-23 17:41:47'),
(7, 'Accounting', 'accounting', NULL, NULL, NULL, 7, 1, '2026-02-23 17:41:47', '2026-02-23 17:41:47'),
(8, 'Agriculture Module', 'agriculture', NULL, NULL, NULL, 8, 1, '2026-02-23 17:41:47', '2026-02-23 17:41:47'),
(9, 'PPMP/APP', 'ppmp', NULL, NULL, NULL, 9, 1, '2026-02-23 17:41:47', '2026-02-23 17:41:47'),
(10, 'Budget', 'budget', NULL, NULL, NULL, 10, 1, '2026-02-23 17:41:47', '2026-02-23 17:41:47'),
(11, 'MSWD', 'mswd', NULL, NULL, NULL, 11, 1, '2026-02-23 17:41:47', '2026-02-23 17:41:47'),
(12, 'Vehicle Franchising', 'vehicle-franchising', 'vf.index', 'vf', NULL, 12, 1, '2026-03-11 06:41:28', '2026-03-11 06:41:28'),
(13, 'Audit Logs', 'audit-logs', 'audit-logs.index', 'audit-logs', NULL, 13, 1, '2026-03-11 06:44:01', '2026-03-11 06:44:01'),
(15, 'Employee Portal', 'employee_portal', 'hr.portal.dashboard', 'hr/portal', 'M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z', 10, 1, '2026-03-16 18:30:26', '2026-03-16 18:30:26');

-- --------------------------------------------------------

--
-- Table structure for table `offices`
--

CREATE TABLE `offices` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `office_name` varchar(255) NOT NULL,
  `office_code` varchar(20) NOT NULL,
  `office_short_name` varchar(50) DEFAULT NULL,
  `office_description` text DEFAULT NULL,
  `parent_office_id` bigint(20) UNSIGNED DEFAULT NULL,
  `office_head` varchar(100) DEFAULT NULL,
  `office_location` varchar(255) DEFAULT NULL,
  `contact_number` varchar(50) DEFAULT NULL,
  `email` varchar(100) DEFAULT NULL,
  `order_sequence` int(11) NOT NULL DEFAULT 0,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `or_assignments`
--

CREATE TABLE `or_assignments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `start_or` varchar(255) NOT NULL,
  `end_or` varchar(255) NOT NULL,
  `receipt_type` enum('51C','AF51','CTC','56C') NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `cashier_name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `or_assignments`
--

INSERT INTO `or_assignments` (`id`, `start_or`, `end_or`, `receipt_type`, `user_id`, `cashier_name`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, '123400', '123450', '51C', 2, 'samples', NULL, '2026-02-23 19:32:52', '2026-02-23 19:32:52'),
(2, '123451', '123500', '51C', 2, 'sample', '2026-02-23 19:32:01', '2026-02-23 19:32:01', NULL),
(3, '123400', '123450', '51C', 3, 'treasury', '2026-02-23 19:33:24', '2026-02-23 19:33:24', NULL),
(4, '123451', '123500', '51C', 2, 'sample', '2026-03-06 07:41:11', '2026-03-06 07:41:11', NULL),
(5, '123501', '123550', 'AF51', 2, 'sample', '2026-03-08 22:06:15', '2026-03-08 22:06:15', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `password_reset_tokens`
--

CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) NOT NULL,
  `token` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `plantilla`
--

CREATE TABLE `plantilla` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `item_number` varchar(50) NOT NULL,
  `position_title` varchar(255) NOT NULL,
  `office_id` bigint(20) UNSIGNED NOT NULL,
  `division_id` bigint(20) UNSIGNED DEFAULT NULL,
  `department_id` bigint(20) UNSIGNED DEFAULT NULL,
  `salary_grade_id` bigint(20) UNSIGNED NOT NULL,
  `salary_step` tinyint(4) NOT NULL DEFAULT 1,
  `employment_type_id` bigint(20) UNSIGNED NOT NULL,
  `position_level` varchar(50) DEFAULT NULL,
  `workstation` varchar(255) DEFAULT NULL,
  `funding_source` varchar(255) DEFAULT NULL,
  `effectivity_date` date DEFAULT NULL,
  `is_vacant` tinyint(1) NOT NULL DEFAULT 1,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `roles`
--

CREATE TABLE `roles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `slug` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `roles`
--

INSERT INTO `roles` (`id`, `name`, `slug`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Super Admin', 'super-admin', 'Full access to all modules and system settings.', '2026-02-23 17:41:47', '2026-02-23 17:41:47'),
(2, 'BPLS Staff', 'bpls-staff', 'Access to Business Permit and Licensing System module.', '2026-02-23 17:41:47', '2026-02-23 17:41:47'),
(3, 'RPT Assessor', 'rpt-assessor', 'Access to Real Property Tax module.', '2026-02-23 17:41:47', '2026-02-23 17:41:47'),
(4, 'HR Officer', 'hr-officer', 'Access to Human Resource module.', '2026-02-23 17:41:47', '2026-02-23 17:41:47'),
(5, 'Treasury Officer', 'treasury-officer', 'Access to Treasury module.', '2026-02-23 17:41:47', '2026-02-23 17:41:47'),
(6, 'Employee', 'employee', NULL, '2026-03-16 18:30:26', '2026-03-16 18:30:26');

-- --------------------------------------------------------

--
-- Table structure for table `role_module`
--

CREATE TABLE `role_module` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `module_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_module`
--

INSERT INTO `role_module` (`id`, `role_id`, `module_id`, `created_at`, `updated_at`) VALUES
(1, 2, 2, NULL, NULL),
(2, 3, 3, NULL, NULL),
(3, 4, 4, NULL, NULL),
(4, 5, 5, NULL, NULL),
(5, 6, 15, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `role_user`
--

CREATE TABLE `role_user` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `role_user`
--

INSERT INTO `role_user` (`id`, `role_id`, `user_id`, `created_at`, `updated_at`) VALUES
(1, 5, 3, NULL, NULL),
(2, 2, 3, NULL, NULL),
(3, 1, 4, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rpta_actual_uses`
--

CREATE TABLE `rpta_actual_uses` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rpta_class_id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpta_actual_uses`
--

INSERT INTO `rpta_actual_uses` (`id`, `rpta_class_id`, `name`, `code`, `is_active`, `created_at`, `updated_at`) VALUES
(2, 1, 'Residential', 'RES-HOUSE', 1, '2026-03-06 07:28:26', '2026-03-06 07:28:26'),
(3, 2, 'Agricultural', 'AGR-LAND', 1, '2026-03-06 07:28:26', '2026-03-06 07:28:26'),
(4, 3, 'Commercial', 'COM-BLDG', 1, '2026-03-06 07:28:26', '2026-03-06 07:28:26'),
(5, 4, 'Industrial', 'IND-FACTORY', 1, '2026-03-06 07:28:26', '2026-03-06 07:28:26'),
(6, 5, 'Cultural/Scientific', 'SPE-CUL', 1, '2026-03-06 07:28:26', '2026-03-06 07:28:26'),
(7, 5, 'Hospital', 'SPE-HOSP', 1, '2026-03-06 07:28:26', '2026-03-06 07:28:26');

-- --------------------------------------------------------

--
-- Table structure for table `rpta_additional_items`
--

CREATE TABLE `rpta_additional_items` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `add_name` text NOT NULL,
  `add_q` enum('YES','NO') NOT NULL DEFAULT 'YES',
  `add_unitval` decimal(15,2) DEFAULT NULL,
  `add_percent` decimal(8,2) DEFAULT NULL,
  `add_desc` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpta_additional_items`
--

INSERT INTO `rpta_additional_items` (`id`, `add_name`, `add_q`, `add_unitval`, `add_percent`, `add_desc`, `created_at`, `updated_at`) VALUES
(1, 'Carport', 'NO', NULL, 10.00, '10% of Base Unit Construction Cost', '2026-01-27 23:48:18', '2026-01-27 23:48:18');

-- --------------------------------------------------------

--
-- Table structure for table `rpta_assessment_levels`
--

CREATE TABLE `rpta_assessment_levels` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rpta_actual_use_id` bigint(20) UNSIGNED NOT NULL,
  `revision_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `min_value` decimal(18,2) NOT NULL DEFAULT 0.00,
  `max_value` decimal(18,2) DEFAULT NULL COMMENT 'null = unlimited',
  `rate` decimal(5,4) NOT NULL COMMENT 'e.g. 0.2000 = 20%',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpta_assessment_levels`
--

INSERT INTO `rpta_assessment_levels` (`id`, `rpta_actual_use_id`, `revision_year_id`, `min_value`, `max_value`, `rate`, `created_at`, `updated_at`) VALUES
(1, 2, 2, 0.00, NULL, 0.2000, '2026-03-06 07:28:26', '2026-03-06 07:28:26'),
(2, 3, 2, 0.00, NULL, 0.4000, '2026-03-06 07:28:26', '2026-03-06 07:28:26'),
(3, 4, 2, 0.00, NULL, 0.5000, '2026-03-06 07:28:26', '2026-03-06 07:28:26'),
(4, 5, 2, 0.00, NULL, 0.5000, '2026-03-06 07:28:26', '2026-03-06 07:28:26'),
(5, 6, 2, 0.00, NULL, 0.1500, '2026-03-06 07:28:26', '2026-03-06 07:28:26'),
(6, 7, 2, 0.00, NULL, 0.1500, '2026-03-06 07:28:26', '2026-03-06 07:28:26');

-- --------------------------------------------------------

--
-- Table structure for table `rpta_assmnt_lvl`
--

CREATE TABLE `rpta_assmnt_lvl` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `assmnt_name` varchar(50) NOT NULL,
  `assmnt_from` decimal(15,2) DEFAULT NULL,
  `assmnt_to` decimal(15,2) DEFAULT NULL,
  `assmnt_percent` decimal(8,2) NOT NULL,
  `assmnt_cat` enum('LAND','BUILDING','MACHINE') NOT NULL,
  `assmnt_kind` enum('RESIDENTIAL','AGRICULTURAL','COMMERCIAL','INDUSTRIAL','MINERAL','TIMBERLAND','SPECIAL','GOVERNMENT','RELIGIOUS','CHARITABLE','EDUCATIONAL','OTHERS','ACI') NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpta_assmnt_lvl`
--

INSERT INTO `rpta_assmnt_lvl` (`id`, `assmnt_name`, `assmnt_from`, `assmnt_to`, `assmnt_percent`, `assmnt_cat`, `assmnt_kind`, `created_at`, `updated_at`) VALUES
(3, 'RESIDENTIAL', 123.00, 321.00, 80.00, 'BUILDING', 'RESIDENTIAL', '2026-02-15 18:57:52', '2026-02-15 18:57:52'),
(5, 'AGRICULTURAL', 1.00, 999999999.00, 35.00, 'LAND', 'AGRICULTURAL', '2026-02-20 02:15:34', '2026-02-20 02:15:34'),
(6, 'AGRICULTURAL', 1.00, 300000.00, 25.00, 'BUILDING', 'AGRICULTURAL', '2026-02-20 03:38:57', '2026-02-20 03:38:57'),
(7, 'mchn', 1.00, 300000.00, 25.00, 'MACHINE', 'AGRICULTURAL', '2026-02-20 04:02:35', '2026-02-20 04:02:35');

-- --------------------------------------------------------

--
-- Table structure for table `rpta_bldg_types`
--

CREATE TABLE `rpta_bldg_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(20) NOT NULL,
  `base_construction_cost` decimal(18,2) NOT NULL DEFAULT 0.00 COMMENT 'Cost per sq.m',
  `useful_life` decimal(5,2) NOT NULL DEFAULT 50.00,
  `residual_value_rate` decimal(5,4) NOT NULL DEFAULT 0.2000 COMMENT 'e.g. 0.20 = 20%',
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rpta_classes`
--

CREATE TABLE `rpta_classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `code` varchar(10) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpta_classes`
--

INSERT INTO `rpta_classes` (`id`, `name`, `code`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Residential', 'Res', 1, '2026-03-06 07:19:57', '2026-03-06 07:28:26'),
(2, 'Agricultural', 'AGR', 1, '2026-03-06 07:28:26', '2026-03-06 07:28:26'),
(3, 'Commercial', 'COM', 1, '2026-03-06 07:28:26', '2026-03-06 07:28:26'),
(4, 'Industrial', 'IND', 1, '2026-03-06 07:28:26', '2026-03-06 07:28:26'),
(5, 'Special', 'SPE', 1, '2026-03-06 07:28:26', '2026-03-06 07:28:26');

-- --------------------------------------------------------

--
-- Table structure for table `rpta_deprate_bldg`
--

CREATE TABLE `rpta_deprate_bldg` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `dep_name` text NOT NULL,
  `dep_rate` decimal(8,2) NOT NULL,
  `dep_desc` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpta_deprate_bldg`
--

INSERT INTO `rpta_deprate_bldg` (`id`, `dep_name`, `dep_rate`, `dep_desc`, `created_at`, `updated_at`) VALUES
(2, 'TYPE V - AGE 1-3', 3.00, NULL, '2026-02-06 00:48:08', '2026-02-06 00:48:08');

-- --------------------------------------------------------

--
-- Table structure for table `rpta_other_improvement`
--

CREATE TABLE `rpta_other_improvement` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `kind_name` varchar(255) NOT NULL,
  `category` varchar(20) DEFAULT NULL COMMENT 'LAND, BUILDING, or NULL (both)',
  `kind_value` decimal(15,2) NOT NULL,
  `kind_date` date NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpta_other_improvement`
--

INSERT INTO `rpta_other_improvement` (`id`, `kind_name`, `category`, `kind_value`, `kind_date`, `created_at`, `updated_at`) VALUES
(2, 'Lanzones fr-2', 'LAND', 680.00, '2026-02-06', '2026-02-06 17:20:11', '2026-02-20 02:18:08'),
(3, 'Carport (Setting)', 'BUILDING', 15000.00, '2026-02-19', '2026-02-19 01:04:35', '2026-02-19 01:04:35'),
(4, 'Concrete Fence (Setting)', 'LAND', 1200.00, '2026-02-19', '2026-02-19 01:04:35', '2026-02-19 01:04:35');

-- --------------------------------------------------------

--
-- Table structure for table `rpta_revision_years`
--

CREATE TABLE `rpta_revision_years` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `year` year(4) NOT NULL,
  `is_current` tinyint(1) NOT NULL DEFAULT 0,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpta_revision_years`
--

INSERT INTO `rpta_revision_years` (`id`, `year`, `is_current`, `created_at`, `updated_at`) VALUES
(1, '2026', 0, '2026-03-06 07:20:16', '2026-03-06 07:20:16'),
(2, '2024', 1, '2026-03-06 07:28:25', '2026-03-07 19:17:42');

-- --------------------------------------------------------

--
-- Table structure for table `rpta_rev_yr`
--

CREATE TABLE `rpta_rev_yr` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rev_yr` int(11) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpta_rev_yr`
--

INSERT INTO `rpta_rev_yr` (`id`, `rev_yr`, `created_at`, `updated_at`) VALUES
(1, 2026, '2026-02-06 21:35:17', '2026-02-06 21:35:17');

-- --------------------------------------------------------

--
-- Table structure for table `rpta_settings`
--

CREATE TABLE `rpta_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `setting_key` varchar(255) NOT NULL,
  `setting_value` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpta_settings`
--

INSERT INTO `rpta_settings` (`id`, `setting_key`, `setting_value`, `created_at`, `updated_at`) VALUES
(1, 'province_code', '045', NULL, NULL),
(2, 'municipality_code', '02', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rpta_signatories`
--

CREATE TABLE `rpta_signatories` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `role` varchar(255) NOT NULL COMMENT 'Assessor, Provincial Assessor, etc.',
  `name` varchar(255) NOT NULL,
  `designation` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rpta_unit_values`
--

CREATE TABLE `rpta_unit_values` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rpta_actual_use_id` bigint(20) UNSIGNED NOT NULL,
  `revision_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `barangay_id` bigint(20) UNSIGNED DEFAULT NULL,
  `value_per_sqm` decimal(18,2) NOT NULL,
  `effectivity_year` year(4) NOT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rpt_application_documents`
--

CREATE TABLE `rpt_application_documents` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rpt_online_application_id` bigint(20) UNSIGNED NOT NULL,
  `type` enum('title_deed','tax_clearance','deed_of_sale','sketch_plan','special_power_of_attorney','gov_id','others') DEFAULT 'others',
  `label` varchar(255) DEFAULT NULL COMMENT 'Friendly name from the applicant',
  `file_path` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `verification_status` enum('pending','verified','rejected') NOT NULL DEFAULT 'pending',
  `rejection_reason` text DEFAULT NULL,
  `verified_by` bigint(20) UNSIGNED DEFAULT NULL,
  `verified_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpt_application_documents`
--

INSERT INTO `rpt_application_documents` (`id`, `rpt_online_application_id`, `type`, `label`, `file_path`, `original_filename`, `verification_status`, `rejection_reason`, `verified_by`, `verified_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, 'title_deed', 'SDS', 'rpt/online-applications/1/8yAGZ7jKkQjUQ7ZD27AnzslEsPSogwOCgETM1RtT.pdf', 'BusinessPermit-APP-2026-00013-2026.pdf', 'verified', NULL, 2, '2026-03-09 02:46:01', '2026-03-09 02:28:23', '2026-03-09 02:46:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rpt_au_tbl`
--

CREATE TABLE `rpt_au_tbl` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `actual_use` varchar(50) NOT NULL,
  `au_cat` enum('RESIDENTIAL','AGRICULTURAL','COMMERCIAL','INDUSTRIAL','MINERAL','TIMBERLAND','SPECIAL','GOVERNMENT','RELIGIOUS','CHARITABLE','EDUCATIONAL','OTHERS','ACI') NOT NULL,
  `au_desc` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpt_au_tbl`
--

INSERT INTO `rpt_au_tbl` (`id`, `actual_use`, `au_cat`, `au_desc`, `created_at`, `updated_at`) VALUES
(3, 'Improvements', 'RESIDENTIAL', 'Improvements', '2026-02-15 18:20:47', '2026-02-15 18:20:47'),
(4, 'AGRICULTURAL', 'AGRICULTURAL', 'Improvements', '2026-02-17 18:01:22', '2026-02-17 18:01:22');

-- --------------------------------------------------------

--
-- Table structure for table `rpt_au_value`
--

CREATE TABLE `rpt_au_value` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `actual_use` varchar(50) NOT NULL,
  `class_struc` varchar(10) NOT NULL,
  `unit_value` decimal(15,2) NOT NULL,
  `au_cat` enum('LAND','BUILDING','MACHINE') NOT NULL,
  `assmt_kind` enum('RESIDENTIAL','AGRICULTURAL','COMMERCIAL','INDUSTRIAL','MINERAL','TIMBERLAND','SPECIAL','GOVERNMENT','RELIGIOUS','CHARITABLE','EDUCATIONAL','OTHERS','ACI') NOT NULL,
  `rev_date` year(4) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpt_au_value`
--

INSERT INTO `rpt_au_value` (`id`, `actual_use`, `class_struc`, `unit_value`, `au_cat`, `assmt_kind`, `rev_date`, `created_at`, `updated_at`) VALUES
(1, 'RESIDENTIAL', 'R-1', 690.00, 'LAND', 'RESIDENTIAL', '2026', '2026-01-28 00:46:07', '2026-02-15 18:20:22'),
(3, 'RESIDENTIAL', 'R-1', 700.00, 'MACHINE', 'RESIDENTIAL', '2026', '2026-02-15 18:56:19', '2026-02-15 18:56:19'),
(4, 'RESIDENTIAL', 'R-1', 700.00, 'BUILDING', 'RESIDENTIAL', '2026', '2026-02-15 18:58:10', '2026-02-15 18:58:10'),
(5, 'AGRICULTURAL', 'R-2', 900.00, 'LAND', 'AGRICULTURAL', '2026', '2026-02-17 18:02:11', '2026-02-17 18:02:11'),
(6, 'A/Warehouse', 'II-C (30)', 3930.00, 'BUILDING', 'AGRICULTURAL', '2026', '2026-02-18 22:58:11', '2026-02-18 22:58:11'),
(7, 'Agro-Industrial', 'Ag-In', 260.00, 'LAND', 'AGRICULTURAL', '2026', '2026-02-18 22:59:26', '2026-02-18 22:59:26'),
(8, 'A/Livestocks', 'III-B (43', 2470.00, 'BUILDING', 'AGRICULTURAL', '2026', '2026-02-20 03:30:17', '2026-02-20 03:32:07'),
(9, 'machine', 'smchhnn', 500.00, 'MACHINE', 'AGRICULTURAL', '2026', '2026-02-20 04:03:49', '2026-02-20 04:03:49');

-- --------------------------------------------------------

--
-- Table structure for table `rpt_billings`
--

CREATE TABLE `rpt_billings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tax_declaration_id` bigint(20) UNSIGNED NOT NULL,
  `tax_year` year(4) NOT NULL,
  `quarter` int(11) DEFAULT NULL COMMENT '1-4 for quarterly, null for annual',
  `basic_tax` decimal(18,2) NOT NULL DEFAULT 0.00,
  `sef_tax` decimal(18,2) NOT NULL DEFAULT 0.00 COMMENT 'Special Education Fund',
  `total_tax_due` decimal(18,2) NOT NULL DEFAULT 0.00,
  `discount_amount` decimal(18,2) NOT NULL DEFAULT 0.00 COMMENT 'Early payment discount',
  `penalty_amount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_amount_due` decimal(18,2) NOT NULL DEFAULT 0.00,
  `amount_paid` decimal(18,2) NOT NULL DEFAULT 0.00,
  `balance` decimal(18,2) NOT NULL DEFAULT 0.00,
  `status` enum('unpaid','partial','paid') NOT NULL DEFAULT 'unpaid',
  `due_date` date DEFAULT NULL,
  `paid_at` timestamp NULL DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpt_billings`
--

INSERT INTO `rpt_billings` (`id`, `tax_declaration_id`, `tax_year`, `quarter`, `basic_tax`, `sef_tax`, `total_tax_due`, `discount_amount`, `penalty_amount`, `total_amount_due`, `amount_paid`, `balance`, `status`, `due_date`, `paid_at`, `created_at`, `updated_at`) VALUES
(1, 6, '2026', 1, 1.04, 0.52, 1.55, 0.00, 0.00, 1.55, 1.55, 0.00, 'paid', '2026-03-31', '2026-03-06 07:41:29', '2026-03-06 07:40:38', '2026-03-06 07:41:29'),
(2, 6, '2026', 2, 1.04, 0.52, 1.55, 0.00, 0.00, 1.55, 1.55, 0.00, 'paid', '2026-06-30', '2026-03-08 18:30:31', '2026-03-06 07:40:38', '2026-03-08 18:30:31'),
(3, 6, '2026', 3, 1.04, 0.52, 1.55, 0.00, 0.00, 1.55, 1.55, 0.00, 'paid', '2026-09-30', '2026-03-08 18:32:11', '2026-03-06 07:40:38', '2026-03-08 18:32:11'),
(4, 6, '2026', 4, 1.04, 0.52, 1.55, 0.00, 0.00, 1.55, 1.55, 0.00, 'paid', '2026-12-31', '2026-03-08 18:49:59', '2026-03-06 07:40:38', '2026-03-08 18:49:59'),
(5, 5, '2026', 1, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'unpaid', '2026-03-31', NULL, '2026-03-08 17:11:56', '2026-03-08 17:11:56'),
(6, 5, '2026', 2, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'unpaid', '2026-06-30', NULL, '2026-03-08 17:11:56', '2026-03-08 17:11:56'),
(7, 5, '2026', 3, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'unpaid', '2026-09-30', NULL, '2026-03-08 17:11:56', '2026-03-08 17:11:56'),
(8, 5, '2026', 4, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 0.00, 'unpaid', '2026-12-31', NULL, '2026-03-08 17:11:56', '2026-03-08 17:11:56'),
(9, 8, '2026', 1, 11.00, 5.50, 16.50, 0.00, 0.00, 16.50, 16.50, 0.00, 'paid', '2026-03-31', '2026-03-08 17:38:10', '2026-03-08 17:37:59', '2026-03-08 17:38:10'),
(10, 8, '2026', 2, 11.00, 5.50, 16.50, 0.00, 0.00, 16.50, 16.50, 0.00, 'paid', '2026-06-30', '2026-03-08 17:38:25', '2026-03-08 17:37:59', '2026-03-08 17:38:25'),
(11, 8, '2026', 3, 11.00, 5.50, 16.50, 0.00, 0.00, 16.50, 16.50, 0.00, 'paid', '2026-09-30', '2026-03-08 17:38:30', '2026-03-08 17:37:59', '2026-03-08 17:38:30'),
(12, 8, '2026', 4, 11.00, 5.50, 16.50, 0.00, 0.00, 16.50, 16.50, 0.00, 'paid', '2026-12-31', '2026-03-08 17:38:40', '2026-03-08 17:37:59', '2026-03-08 17:38:40'),
(13, 10, '2026', 1, 9.75, 4.88, 14.63, 0.00, 0.00, 14.63, 14.63, 0.00, 'paid', '2026-03-31', '2026-03-08 19:03:47', '2026-03-08 19:03:22', '2026-03-08 19:03:47'),
(14, 10, '2026', 2, 9.75, 4.88, 14.63, 0.00, 0.00, 14.63, 0.00, 14.63, 'unpaid', '2026-06-30', NULL, '2026-03-08 19:03:22', '2026-03-08 19:03:22'),
(15, 10, '2026', 3, 9.75, 4.88, 14.63, 0.00, 0.00, 14.63, 0.00, 14.63, 'unpaid', '2026-09-30', NULL, '2026-03-08 19:03:22', '2026-03-08 19:03:22'),
(16, 10, '2026', 4, 9.75, 4.88, 14.63, 0.00, 0.00, 14.63, 0.00, 14.63, 'unpaid', '2026-12-31', NULL, '2026-03-08 19:03:22', '2026-03-08 19:03:22'),
(17, 11, '2026', 1, 250.00, 250.00, 500.00, 0.00, 0.00, 500.00, 500.00, 0.00, 'paid', '2026-03-31', '2026-03-08 19:15:05', '2026-03-08 19:13:44', '2026-03-08 19:15:05'),
(18, 11, '2026', 2, 250.00, 250.00, 500.00, 50.00, 0.00, 450.00, 450.00, 0.00, 'paid', '2026-06-30', '2026-03-08 19:28:10', '2026-03-08 19:13:44', '2026-03-08 19:28:10'),
(19, 12, '2027', 1, 400.00, 400.00, 800.00, 160.00, 0.00, 640.00, 640.00, 0.00, 'paid', '2027-03-31', '2026-03-08 19:33:09', '2026-03-08 19:13:44', '2026-03-08 19:33:09'),
(20, 12, '2027', 2, 400.00, 400.00, 800.00, 160.00, 0.00, 640.00, 640.00, 0.00, 'paid', '2027-06-30', '2026-03-08 19:33:26', '2026-03-08 19:13:44', '2026-03-08 19:33:26'),
(21, 11, '2026', 3, 500.00, 250.00, 750.00, 75.00, 0.00, 675.00, 675.00, 0.00, 'paid', '2026-09-30', '2026-03-08 19:31:32', '2026-03-08 19:14:23', '2026-03-08 19:31:32'),
(22, 11, '2026', 4, 500.00, 250.00, 750.00, 75.00, 0.00, 675.00, 675.00, 0.00, 'paid', '2026-12-31', '2026-03-08 19:31:49', '2026-03-08 19:14:23', '2026-03-08 19:31:49'),
(23, 12, '2026', 1, 800.00, 400.00, 1200.00, 120.00, 0.00, 1080.00, 1080.00, 0.00, 'paid', '2026-03-31', '2026-03-08 19:32:20', '2026-03-08 19:31:56', '2026-03-08 19:32:20'),
(24, 12, '2026', 2, 800.00, 400.00, 1200.00, 120.00, 0.00, 1080.00, 1080.00, 0.00, 'paid', '2026-06-30', '2026-03-08 19:32:37', '2026-03-08 19:31:56', '2026-03-08 19:32:37'),
(25, 12, '2026', 3, 800.00, 400.00, 1200.00, 120.00, 0.00, 1080.00, 1080.00, 0.00, 'paid', '2026-09-30', '2026-03-08 19:32:52', '2026-03-08 19:31:56', '2026-03-08 19:32:52'),
(26, 12, '2026', 4, 800.00, 400.00, 1200.00, 120.00, 0.00, 1080.00, 1080.00, 0.00, 'paid', '2026-12-31', '2026-03-08 19:33:01', '2026-03-08 19:31:56', '2026-03-08 19:33:01'),
(27, 13, '2026', 1, 250.00, 250.00, 500.00, 50.00, 0.00, 450.00, 0.00, 450.00, 'unpaid', '2026-03-31', NULL, '2026-03-08 19:37:27', '2026-03-08 20:12:35'),
(28, 13, '2026', 2, 250.00, 250.00, 500.00, 50.00, 0.00, 450.00, 0.00, 450.00, 'unpaid', '2026-06-30', NULL, '2026-03-08 19:37:27', '2026-03-08 20:12:35'),
(29, 14, '2027', 1, 400.00, 400.00, 800.00, 0.00, 0.00, 800.00, 0.00, 800.00, 'unpaid', '2027-03-31', NULL, '2026-03-08 19:37:27', '2026-03-08 19:37:27'),
(30, 14, '2027', 2, 400.00, 400.00, 800.00, 0.00, 0.00, 800.00, 0.00, 800.00, 'unpaid', '2027-06-30', NULL, '2026-03-08 19:37:27', '2026-03-08 19:37:27'),
(31, 15, '2024', 1, 300.00, 300.00, 600.00, 0.00, 278.21, 878.21, 0.00, 878.21, 'unpaid', '2024-03-31', NULL, '2026-03-08 19:37:27', '2026-03-08 19:38:05'),
(32, 15, '2024', 2, 300.00, 300.00, 600.00, 0.00, 242.64, 842.64, 0.00, 842.64, 'unpaid', '2024-06-30', NULL, '2026-03-08 19:37:27', '2026-03-08 19:38:05'),
(33, 15, '2026', 1, 600.00, 300.00, 900.00, 0.00, 0.00, 900.00, 0.00, 900.00, 'unpaid', '2026-03-31', NULL, '2026-03-08 19:38:05', '2026-03-08 19:38:05'),
(34, 15, '2026', 2, 600.00, 300.00, 900.00, 0.00, 0.00, 900.00, 0.00, 900.00, 'unpaid', '2026-06-30', NULL, '2026-03-08 19:38:05', '2026-03-08 19:38:05'),
(35, 15, '2026', 3, 600.00, 300.00, 900.00, 0.00, 0.00, 900.00, 0.00, 900.00, 'unpaid', '2026-09-30', NULL, '2026-03-08 19:38:05', '2026-03-08 19:38:05'),
(36, 15, '2026', 4, 600.00, 300.00, 900.00, 0.00, 0.00, 900.00, 0.00, 900.00, 'unpaid', '2026-12-31', NULL, '2026-03-08 19:38:05', '2026-03-08 19:38:05'),
(37, 14, '2026', 1, 800.00, 400.00, 1200.00, 120.00, 0.00, 1080.00, 0.00, 1080.00, 'unpaid', '2026-03-31', NULL, '2026-03-08 19:39:57', '2026-03-08 19:39:57'),
(38, 14, '2026', 2, 800.00, 400.00, 1200.00, 120.00, 0.00, 1080.00, 0.00, 1080.00, 'unpaid', '2026-06-30', NULL, '2026-03-08 19:39:57', '2026-03-08 19:39:57'),
(39, 14, '2026', 3, 800.00, 400.00, 1200.00, 120.00, 0.00, 1080.00, 0.00, 1080.00, 'unpaid', '2026-09-30', NULL, '2026-03-08 19:39:57', '2026-03-08 19:39:57'),
(40, 14, '2026', 4, 800.00, 400.00, 1200.00, 120.00, 0.00, 1080.00, 0.00, 1080.00, 'unpaid', '2026-12-31', NULL, '2026-03-08 19:39:57', '2026-03-08 19:39:57'),
(41, 13, '2026', 3, 500.00, 250.00, 750.00, 75.00, 0.00, 675.00, 0.00, 675.00, 'unpaid', '2026-09-30', NULL, '2026-03-08 20:12:35', '2026-03-08 20:12:35'),
(42, 13, '2026', 4, 500.00, 250.00, 750.00, 75.00, 0.00, 675.00, 0.00, 675.00, 'unpaid', '2026-12-31', NULL, '2026-03-08 20:12:35', '2026-03-08 20:12:35'),
(43, 16, '2026', 1, 4.80, 2.40, 7.20, 0.72, 0.00, 6.48, 6.48, 0.00, 'paid', '2026-03-31', '2026-03-09 02:51:03', '2026-03-09 02:50:53', '2026-03-09 02:51:03'),
(44, 16, '2026', 2, 4.80, 2.40, 7.20, 0.72, 0.00, 6.48, 0.00, 6.48, 'unpaid', '2026-06-30', NULL, '2026-03-09 02:50:53', '2026-03-09 02:50:53'),
(45, 16, '2026', 3, 4.80, 2.40, 7.20, 0.72, 0.00, 6.48, 0.00, 6.48, 'unpaid', '2026-09-30', NULL, '2026-03-09 02:50:53', '2026-03-09 02:50:53'),
(46, 16, '2026', 4, 4.80, 2.40, 7.20, 0.72, 0.00, 6.48, 0.00, 6.48, 'unpaid', '2026-12-31', NULL, '2026-03-09 02:50:53', '2026-03-09 02:50:53');

-- --------------------------------------------------------

--
-- Table structure for table `rpt_location_classes`
--

CREATE TABLE `rpt_location_classes` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpt_location_classes`
--

INSERT INTO `rpt_location_classes` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'Prime', '2026-02-18 22:29:57', '2026-02-18 22:29:57'),
(2, 'Secondary', '2026-02-18 22:29:57', '2026-02-18 22:29:57'),
(3, 'Interior', '2026-02-18 22:29:57', '2026-02-18 22:29:57');

-- --------------------------------------------------------

--
-- Table structure for table `rpt_online_applications`
--

CREATE TABLE `rpt_online_applications` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `reference_no` varchar(255) NOT NULL COMMENT 'Public-facing tracking number',
  `client_id` bigint(20) UNSIGNED DEFAULT NULL,
  `owner_name` varchar(255) NOT NULL,
  `owner_tin` varchar(255) DEFAULT NULL,
  `owner_address` varchar(255) NOT NULL,
  `owner_contact` varchar(255) DEFAULT NULL,
  `owner_email` varchar(255) DEFAULT NULL,
  `administrator_name` varchar(255) DEFAULT NULL,
  `administrator_address` varchar(500) DEFAULT NULL,
  `barangay_id` bigint(20) UNSIGNED DEFAULT NULL,
  `street` varchar(255) DEFAULT NULL,
  `municipality` varchar(255) DEFAULT NULL,
  `province` varchar(255) DEFAULT NULL,
  `property_type` enum('land','building','machinery','mixed') NOT NULL DEFAULT 'land',
  `lot_no` varchar(255) DEFAULT NULL,
  `blk_no` varchar(255) DEFAULT NULL,
  `survey_no` varchar(255) DEFAULT NULL,
  `title_no` varchar(255) DEFAULT NULL,
  `land_area` decimal(14,4) DEFAULT NULL,
  `building_floor_area` decimal(14,2) DEFAULT NULL,
  `building_type` varchar(255) DEFAULT NULL,
  `building_materials` varchar(255) DEFAULT NULL,
  `machinery_cost` decimal(18,2) DEFAULT NULL,
  `machinery_useful_life` int(11) DEFAULT NULL,
  `machinery_acquisition_date` date DEFAULT NULL,
  `property_description` text DEFAULT NULL,
  `boundary_north` varchar(255) DEFAULT NULL,
  `boundary_south` varchar(255) DEFAULT NULL,
  `boundary_east` varchar(255) DEFAULT NULL,
  `boundary_west` varchar(255) DEFAULT NULL,
  `status` enum('pending','under_review','for_inspection','approved','returned','rejected') NOT NULL DEFAULT 'pending',
  `staff_remarks` text DEFAULT NULL,
  `reviewed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `reviewed_at` timestamp NULL DEFAULT NULL,
  `faas_property_id` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `polygon_coordinates` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`polygon_coordinates`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpt_online_applications`
--

INSERT INTO `rpt_online_applications` (`id`, `reference_no`, `client_id`, `owner_name`, `owner_tin`, `owner_address`, `owner_contact`, `owner_email`, `administrator_name`, `administrator_address`, `barangay_id`, `street`, `municipality`, `province`, `property_type`, `lot_no`, `blk_no`, `survey_no`, `title_no`, `land_area`, `building_floor_area`, `building_type`, `building_materials`, `machinery_cost`, `machinery_useful_life`, `machinery_acquisition_date`, `property_description`, `boundary_north`, `boundary_south`, `boundary_east`, `boundary_west`, `status`, `staff_remarks`, `reviewed_by`, `reviewed_at`, `faas_property_id`, `created_at`, `updated_at`, `deleted_at`, `polygon_coordinates`) VALUES
(1, 'RPT-20260309-6CA9A', 2, 'rara Dela Cruz', '1122333', 'Philippines', '0987654321', 'john@example.com', NULL, NULL, 1, NULL, NULL, NULL, 'land', NULL, NULL, NULL, '2222', NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, NULL, 'approved', 'goods', 2, '2026-03-09 02:46:06', 21, '2026-03-09 02:28:23', '2026-03-09 02:46:06', NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rpt_payments`
--

CREATE TABLE `rpt_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rpt_billing_id` bigint(20) UNSIGNED NOT NULL,
  `or_no` varchar(255) DEFAULT NULL COMMENT 'Official Receipt Number',
  `amount` decimal(18,2) NOT NULL,
  `basic_tax` decimal(18,2) NOT NULL DEFAULT 0.00,
  `sef_tax` decimal(18,2) NOT NULL DEFAULT 0.00,
  `discount` decimal(18,2) NOT NULL DEFAULT 0.00,
  `penalty` decimal(18,2) NOT NULL DEFAULT 0.00,
  `payment_mode` varchar(50) NOT NULL DEFAULT 'cash',
  `check_no` varchar(255) DEFAULT NULL,
  `bank_name` varchar(255) DEFAULT NULL,
  `payment_date` date NOT NULL,
  `collected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` varchar(20) NOT NULL DEFAULT 'completed',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpt_payments`
--

INSERT INTO `rpt_payments` (`id`, `rpt_billing_id`, `or_no`, `amount`, `basic_tax`, `sef_tax`, `discount`, `penalty`, `payment_mode`, `check_no`, `bank_name`, `payment_date`, `collected_by`, `remarks`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, '123458', 1.55, 1.04, 0.52, 0.00, 0.00, 'cash', NULL, NULL, '2026-03-06', 2, NULL, 'completed', '2026-03-06 07:41:29', '2026-03-06 07:41:29'),
(2, 9, '123459', 16.50, 11.00, 5.50, 0.00, 0.00, 'cash', NULL, NULL, '2026-03-09', 2, NULL, 'completed', '2026-03-08 17:38:10', '2026-03-08 17:38:10'),
(3, 10, '123460', 16.50, 11.00, 5.50, 0.00, 0.00, 'cash', NULL, NULL, '2026-03-09', 2, NULL, 'completed', '2026-03-08 17:38:25', '2026-03-08 17:38:25'),
(4, 11, '123461', 16.50, 11.00, 5.50, 0.00, 0.00, 'cash', NULL, NULL, '2026-03-09', 2, NULL, 'completed', '2026-03-08 17:38:30', '2026-03-08 17:38:30'),
(5, 12, '123462', 16.50, 11.00, 5.50, 0.00, 0.00, 'cash', NULL, NULL, '2026-03-09', 2, NULL, 'completed', '2026-03-08 17:38:40', '2026-03-08 17:38:40'),
(6, 2, '123463', 1.55, 1.04, 0.52, 0.00, 0.00, 'cash', NULL, NULL, '2026-03-09', 2, NULL, 'completed', '2026-03-08 18:30:31', '2026-03-08 18:30:31'),
(7, 3, '123464', 1.55, 1.04, 0.52, 0.00, 0.00, 'cash', NULL, NULL, '2026-03-09', 2, NULL, 'completed', '2026-03-08 18:32:11', '2026-03-08 18:32:11'),
(8, 4, '123465', 1.55, 1.04, 0.52, 0.00, 0.00, 'cash', NULL, NULL, '2026-03-09', 2, NULL, 'completed', '2026-03-08 18:49:59', '2026-03-08 18:49:59'),
(9, 13, '123466', 14.63, 9.75, 4.88, 0.00, 0.00, 'cash', NULL, NULL, '2026-03-09', 2, NULL, 'completed', '2026-03-08 19:03:47', '2026-03-08 19:03:47'),
(10, 17, '123467', 500.00, 250.00, 250.00, 0.00, 0.00, 'cash', NULL, NULL, '2026-03-09', 2, NULL, 'completed', '2026-03-08 19:15:05', '2026-03-08 19:15:05'),
(11, 18, '123468', 450.00, 250.00, 250.00, 0.00, 0.00, 'cash', NULL, NULL, '2026-03-09', 2, NULL, 'completed', '2026-03-08 19:28:10', '2026-03-08 19:28:10'),
(12, 21, '123469', 675.00, 500.00, 250.00, 0.00, 0.00, 'cash', NULL, NULL, '2026-03-09', 2, NULL, 'completed', '2026-03-08 19:31:32', '2026-03-08 19:31:32'),
(13, 22, '123470', 675.00, 500.00, 250.00, 0.00, 0.00, 'cash', NULL, NULL, '2026-03-09', 2, NULL, 'completed', '2026-03-08 19:31:49', '2026-03-08 19:31:49'),
(14, 23, '123471', 1080.00, 800.00, 400.00, 0.00, 0.00, 'cash', NULL, NULL, '2026-03-09', 2, NULL, 'completed', '2026-03-08 19:32:20', '2026-03-08 19:32:20'),
(15, 24, '123472', 1080.00, 800.00, 400.00, 0.00, 0.00, 'cash', NULL, NULL, '2026-03-09', 2, NULL, 'completed', '2026-03-08 19:32:37', '2026-03-08 19:32:37'),
(16, 25, '123473', 1080.00, 800.00, 400.00, 0.00, 0.00, 'cash', NULL, NULL, '2026-03-09', 2, NULL, 'completed', '2026-03-08 19:32:52', '2026-03-08 19:32:52'),
(17, 26, '123474', 1080.00, 800.00, 400.00, 0.00, 0.00, 'cash', NULL, NULL, '2026-03-09', 2, NULL, 'completed', '2026-03-08 19:33:01', '2026-03-08 19:33:01'),
(18, 19, '123475', 640.00, 400.00, 400.00, 0.00, 0.00, 'cash', NULL, NULL, '2026-03-09', 2, NULL, 'completed', '2026-03-08 19:33:09', '2026-03-08 19:33:09'),
(19, 20, '123476', 640.00, 400.00, 400.00, 0.00, 0.00, 'cash', NULL, NULL, '2026-03-09', 2, NULL, 'completed', '2026-03-08 19:33:26', '2026-03-08 19:33:26'),
(20, 43, '123477', 6.48, 4.80, 2.40, 0.00, 0.00, 'cash', NULL, NULL, '2026-03-09', 2, NULL, 'completed', '2026-03-09 02:51:03', '2026-03-09 02:51:03');

-- --------------------------------------------------------

--
-- Table structure for table `rpt_property_registrations`
--

CREATE TABLE `rpt_property_registrations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `owner_name` varchar(255) NOT NULL,
  `owner_tin` varchar(255) DEFAULT NULL,
  `owner_address` varchar(255) NOT NULL,
  `owner_contact` varchar(255) DEFAULT NULL,
  `owner_email` varchar(255) DEFAULT NULL,
  `administrator_name` varchar(255) DEFAULT NULL,
  `administrator_address` varchar(255) DEFAULT NULL,
  `property_type` enum('land','building','machinery','mixed') NOT NULL,
  `parent_land_faas_id` bigint(20) UNSIGNED DEFAULT NULL,
  `barangay_id` bigint(20) UNSIGNED NOT NULL,
  `street` varchar(255) DEFAULT NULL,
  `municipality` varchar(255) NOT NULL,
  `province` varchar(255) NOT NULL,
  `title_no` varchar(255) DEFAULT NULL,
  `lot_no` varchar(255) DEFAULT NULL,
  `blk_no` varchar(255) DEFAULT NULL,
  `survey_no` varchar(255) DEFAULT NULL,
  `boundary_north` varchar(255) DEFAULT NULL,
  `boundary_south` varchar(255) DEFAULT NULL,
  `boundary_east` varchar(255) DEFAULT NULL,
  `boundary_west` varchar(255) DEFAULT NULL,
  `estimated_floor_area` decimal(12,4) DEFAULT NULL,
  `machinery_description` text DEFAULT NULL,
  `status` enum('registered','archived') NOT NULL DEFAULT 'registered',
  `remarks` text DEFAULT NULL,
  `created_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `polygon_coordinates` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`polygon_coordinates`))
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpt_property_registrations`
--

INSERT INTO `rpt_property_registrations` (`id`, `owner_name`, `owner_tin`, `owner_address`, `owner_contact`, `owner_email`, `administrator_name`, `administrator_address`, `property_type`, `parent_land_faas_id`, `barangay_id`, `street`, `municipality`, `province`, `title_no`, `lot_no`, `blk_no`, `survey_no`, `boundary_north`, `boundary_south`, `boundary_east`, `boundary_west`, `estimated_floor_area`, `machinery_description`, `status`, `remarks`, `created_by`, `created_at`, `updated_at`, `polygon_coordinates`) VALUES
(1, 'Juan Dela Cruz', '1122333', 'Philippines', '0987654321', NULL, 'UIOP POIUY QWERTY', 'Philippines', 'land', NULL, 4, 'Victoria, Mallig, Isabela', 'Los Baños', 'Laguna', NULL, '2656', '2', 'adsad', NULL, NULL, NULL, NULL, NULL, NULL, 'registered', NULL, 2, '2026-03-06 05:20:40', '2026-03-06 05:20:40', NULL),
(2, 'Juan Dela Cruz', '1122333', 'Philippines', '0987654321', NULL, 'UIOP POIUY QWERTY', 'Philippines', 'land', NULL, 1, 'Victoria, Mallig, Isabela', 'Los Baños', 'Laguna', NULL, '2656', '2', 'adsad', NULL, NULL, NULL, NULL, NULL, NULL, 'registered', NULL, 2, '2026-03-06 07:14:38', '2026-03-06 07:14:38', NULL),
(3, 'rara Dela Cruz', '1122333', 'Philippines', '0987654321', NULL, 'UIOP POIUY QWERTY', 'Philippines', 'land', NULL, 3, 'Victoria, Mallig, Isabela', 'Los Baños', 'Laguna', NULL, '2656', '2', 'adsad', NULL, NULL, NULL, NULL, NULL, NULL, 'registered', NULL, 2, '2026-03-06 07:15:50', '2026-03-06 07:15:50', NULL),
(4, 'rara Dela Cruz', '1122333', 'Philippines', '0987654321', NULL, 'UIOP POIUY QWERTY', 'Philippines', 'land', NULL, 1, 'Victoria, Mallig, Isabela', 'Los Baños', 'Laguna', NULL, '2656', '2', 'adsad', NULL, NULL, NULL, NULL, NULL, NULL, 'archived', '\n[Archived by  on Mar 09, 2026]: discard', 2, '2026-03-08 17:35:36', '2026-03-08 23:38:12', NULL),
(5, 'rara Dela Cruz', '1122333', 'Philippines', '0987654321', NULL, 'UIOP POIUY QWERTY', 'Philippines', 'land', NULL, 1, 'Victoria, Mallig, Isabela', 'Los Baños', 'Laguna', NULL, '2656', '2', 'adsad', NULL, NULL, NULL, NULL, NULL, NULL, 'archived', '\n[Archived by  on Mar 09, 2026]: Archieve', 2, '2026-03-08 19:01:45', '2026-03-08 23:47:04', NULL),
(6, 'rara Dela Cruz', '1122333', 'Philippines', '0987654321', NULL, 'UIOP POIUY QWERTY', 'Philippines', 'land', NULL, 4, 'Victoria, Mallig, Isabela', 'Los Baños', 'Laguna', NULL, '2656', '2', 'adsad', NULL, NULL, NULL, NULL, NULL, NULL, 'registered', NULL, 2, '2026-03-08 23:55:09', '2026-03-08 23:55:09', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `rpt_registration_attachments`
--

CREATE TABLE `rpt_registration_attachments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `rpt_property_registration_id` bigint(20) UNSIGNED NOT NULL,
  `type` varchar(255) NOT NULL,
  `label` varchar(255) DEFAULT NULL,
  `file_path` varchar(255) NOT NULL,
  `original_filename` varchar(255) NOT NULL,
  `uploaded_by` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `rpt_road_types`
--

CREATE TABLE `rpt_road_types` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpt_road_types`
--

INSERT INTO `rpt_road_types` (`id`, `name`, `created_at`, `updated_at`) VALUES
(1, 'National', '2026-02-18 22:29:57', '2026-02-18 22:29:57'),
(2, 'Provincial', '2026-02-18 22:29:57', '2026-02-18 22:29:57'),
(3, 'Municipal', '2026-02-18 22:29:57', '2026-02-18 22:29:57'),
(4, 'Barangay', '2026-02-18 22:29:57', '2026-02-18 22:29:57');

-- --------------------------------------------------------

--
-- Table structure for table `rpt_tc_tbl`
--

CREATE TABLE `rpt_tc_tbl` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tcode` varchar(5) NOT NULL,
  `tcode_desc` varchar(200) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `rpt_tc_tbl`
--

INSERT INTO `rpt_tc_tbl` (`id`, `tcode`, `tcode_desc`, `created_at`, `updated_at`) VALUES
(5, 'hi', 'Na', '2026-02-06 22:32:48', '2026-02-06 22:33:25'),
(6, 'CL', 'Cancellation for revision, correction, insertion of addional information, retirement of section, and others..', '2026-02-19 21:01:33', '2026-02-19 21:01:33');

-- --------------------------------------------------------

--
-- Table structure for table `salary_grades`
--

CREATE TABLE `salary_grades` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `grade_number` int(11) NOT NULL,
  `grade_name` varchar(50) DEFAULT NULL,
  `step_1` decimal(15,2) DEFAULT NULL,
  `step_2` decimal(15,2) DEFAULT NULL,
  `step_3` decimal(15,2) DEFAULT NULL,
  `step_4` decimal(15,2) DEFAULT NULL,
  `step_5` decimal(15,2) DEFAULT NULL,
  `step_6` decimal(15,2) DEFAULT NULL,
  `step_7` decimal(15,2) DEFAULT NULL,
  `step_8` decimal(15,2) DEFAULT NULL,
  `salary_schedule` varchar(50) DEFAULT NULL,
  `effectivity_year` int(11) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `sessions`
--

CREATE TABLE `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `sessions`
--

INSERT INTO `sessions` (`id`, `user_id`, `ip_address`, `user_agent`, `payload`, `last_activity`) VALUES
('QaQFvjA2fd2KVbFODQP8elJiwKxoNcRbNhGZu4cD', 2, '127.0.0.1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/146.0.0.0 Safari/537.36 Edg/146.0.0.0', 'YTo0OntzOjY6Il90b2tlbiI7czo0MDoiZDZCQVZhemdsV0tZNGV0eU5lS3lzdHQyRXdWeUZtQjVObGVnYTBsSyI7czo5OiJfcHJldmlvdXMiO2E6Mjp7czozOiJ1cmwiO3M6NTM6Imh0dHA6Ly8xMjcuMC4wLjE6ODAwMC9icGxzL2J1c2luZXNzLWxpc3QvMTgvZWRpdC1kYXRhIjtzOjU6InJvdXRlIjtzOjI4OiJicGxzLmJ1c2luZXNzLWxpc3QuZWRpdC1kYXRhIjt9czo2OiJfZmxhc2giO2E6Mjp7czozOiJvbGQiO2E6MDp7fXM6MzoibmV3IjthOjA6e319czo1MDoibG9naW5fd2ViXzU5YmEzNmFkZGMyYjJmOTQwMTU4MGYwMTRjN2Y1OGVhNGUzMDk4OWQiO2k6Mjt9', 1773891522);

-- --------------------------------------------------------

--
-- Table structure for table `tax_declarations`
--

CREATE TABLE `tax_declarations` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `td_no` varchar(255) DEFAULT NULL COMMENT 'Official TD number, generated on approval',
  `prev_td_no` varchar(255) DEFAULT NULL COMMENT 'Previous TD if this is a revision',
  `cancelled_td_no` varchar(255) DEFAULT NULL,
  `cancellation_reason` text DEFAULT NULL,
  `faas_property_id` bigint(20) UNSIGNED NOT NULL,
  `faas_land_id` bigint(20) UNSIGNED DEFAULT NULL,
  `faas_building_id` bigint(20) UNSIGNED DEFAULT NULL,
  `faas_machinery_id` bigint(20) UNSIGNED DEFAULT NULL,
  `revision_year_id` bigint(20) UNSIGNED DEFAULT NULL,
  `effectivity_year` year(4) NOT NULL,
  `effectivity_quarter` tinyint(3) UNSIGNED NOT NULL DEFAULT 1,
  `property_type` enum('land','building','machinery','mixed') NOT NULL DEFAULT 'land',
  `property_kind` varchar(20) DEFAULT NULL,
  `total_market_value` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_market_value_snapshot` decimal(16,2) DEFAULT NULL,
  `total_assessed_value` decimal(18,2) NOT NULL DEFAULT 0.00,
  `total_assessed_value_snapshot` decimal(16,2) DEFAULT NULL,
  `is_taxable` tinyint(1) NOT NULL DEFAULT 1,
  `exemption_basis` varchar(255) DEFAULT NULL,
  `tax_rate` decimal(6,5) NOT NULL DEFAULT 0.02000 COMMENT 'e.g. 0.02 = 2% basic RPT',
  `tax_rate_snapshot` decimal(10,5) DEFAULT NULL,
  `basic_tax_snapshot` decimal(16,2) DEFAULT NULL,
  `declaration_reason` enum('initial','revision_general','revision_specific','transfer','cancellation') NOT NULL DEFAULT 'initial',
  `status` enum('draft','for_review','approved','forwarded','cancelled','inactive') DEFAULT 'draft',
  `created_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_by` bigint(20) UNSIGNED DEFAULT NULL,
  `approved_at` timestamp NULL DEFAULT NULL,
  `inactive_at` timestamp NULL DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `tax_declarations`
--

INSERT INTO `tax_declarations` (`id`, `td_no`, `prev_td_no`, `cancelled_td_no`, `cancellation_reason`, `faas_property_id`, `faas_land_id`, `faas_building_id`, `faas_machinery_id`, `revision_year_id`, `effectivity_year`, `effectivity_quarter`, `property_type`, `property_kind`, `total_market_value`, `total_market_value_snapshot`, `total_assessed_value`, `total_assessed_value_snapshot`, `is_taxable`, `exemption_basis`, `tax_rate`, `tax_rate_snapshot`, `basic_tax_snapshot`, `declaration_reason`, `status`, `created_by`, `approved_by`, `approved_at`, `inactive_at`, `remarks`, `created_at`, `updated_at`, `deleted_at`) VALUES
(5, '2026-TD-000001', NULL, NULL, NULL, 2, 1, NULL, NULL, NULL, '2027', 1, 'land', 'land', 3750.00, NULL, 0.00, NULL, 1, NULL, 0.02000, NULL, NULL, 'initial', 'forwarded', 2, 2, '2026-03-06 07:39:50', NULL, 'Auto-generated upon approval of FAAS ARP 00-0002-00001.', '2026-03-06 07:39:50', '2026-03-08 17:11:21', NULL),
(6, '2026-TD-000002', NULL, NULL, NULL, 2, 2, NULL, NULL, NULL, '2027', 1, 'land', 'land', 518.00, NULL, 207.20, NULL, 1, NULL, 0.02000, NULL, NULL, 'initial', 'forwarded', 2, 2, '2026-03-06 07:39:50', NULL, 'Auto-generated upon approval of FAAS ARP 00-0002-00001.', '2026-03-06 07:39:50', '2026-03-06 07:40:28', NULL),
(7, '2026-TD-000003', NULL, NULL, NULL, 3, 3, NULL, NULL, NULL, '2027', 1, 'land', 'land', 500.00, NULL, 200.00, NULL, 1, NULL, 0.02000, NULL, NULL, 'initial', 'cancelled', 2, 2, '2026-03-07 18:38:17', NULL, 'SUBDIVIDED/SPLIT: Lineage maintained through successor ARPs. Base Area: 250.0000 sqm.', '2026-03-07 18:38:17', '2026-03-07 18:54:20', NULL),
(8, '2026-TD-000004', NULL, NULL, NULL, 9, 8, NULL, NULL, NULL, '2027', 1, 'land', 'land', 5500.00, NULL, 2200.00, NULL, 1, NULL, 0.02000, NULL, NULL, 'initial', 'inactive', 2, 2, '2026-03-08 17:36:46', '2026-03-08 17:52:07', 'Auto-generated upon approval of FAAS ARP 00-0002-00003.', '2026-03-08 17:36:46', '2026-03-08 17:52:07', NULL),
(9, '2026-TD-000005', NULL, NULL, NULL, 10, 9, NULL, NULL, NULL, '2027', 1, 'land', 'land', 5500.00, NULL, 2200.00, NULL, 1, NULL, 0.02000, NULL, NULL, 'initial', 'forwarded', 2, 2, '2026-03-08 17:52:07', NULL, 'Auto-generated upon approval of FAAS ARP 00-0002-00004.', '2026-03-08 17:52:07', '2026-03-08 23:55:37', NULL),
(10, '2026-TD-000006', NULL, NULL, NULL, 11, 10, NULL, NULL, NULL, '2027', 1, 'land', 'land', 4876.00, NULL, 1950.40, NULL, 1, NULL, 0.02000, NULL, NULL, 'initial', 'forwarded', 2, 2, '2026-03-08 19:02:51', NULL, 'Auto-generated upon approval of FAAS ARP 00-0002-00005.', '2026-03-08 19:02:51', '2026-03-08 19:03:10', NULL),
(11, 'TD-PROMPT-1244', NULL, NULL, NULL, 15, NULL, NULL, NULL, NULL, '2026', 1, 'land', NULL, 0.00, NULL, 100000.00, NULL, 1, NULL, 0.02000, NULL, NULL, 'initial', 'forwarded', NULL, NULL, NULL, NULL, NULL, '2026-03-08 19:13:44', '2026-03-08 19:13:44', NULL),
(12, 'TD-ADVANCE-3303', NULL, NULL, NULL, 16, NULL, NULL, NULL, NULL, '2026', 1, 'land', NULL, 0.00, NULL, 160000.00, NULL, 1, NULL, 0.02000, NULL, NULL, 'initial', 'forwarded', NULL, NULL, NULL, NULL, NULL, '2026-03-08 19:13:44', '2026-03-08 19:13:44', NULL),
(13, 'TD-PROMPT-3050', NULL, NULL, NULL, 17, NULL, NULL, NULL, NULL, '2026', 1, 'land', NULL, 0.00, NULL, 100000.00, NULL, 1, NULL, 0.02000, NULL, NULL, 'initial', 'forwarded', NULL, NULL, NULL, NULL, NULL, '2026-03-08 19:37:27', '2026-03-08 19:37:27', NULL),
(14, 'TD-ADVANCE-9281', NULL, NULL, NULL, 18, NULL, NULL, NULL, NULL, '2026', 1, 'land', NULL, 0.00, NULL, 160000.00, NULL, 1, NULL, 0.02000, NULL, NULL, 'initial', 'forwarded', NULL, NULL, NULL, NULL, NULL, '2026-03-08 19:37:27', '2026-03-08 19:37:27', NULL),
(15, 'TD-LATE-3758', NULL, NULL, NULL, 19, NULL, NULL, NULL, NULL, '2023', 1, 'land', NULL, 0.00, NULL, 120000.00, NULL, 1, NULL, 0.02000, NULL, NULL, 'initial', 'forwarded', NULL, NULL, NULL, NULL, NULL, '2026-03-08 19:37:27', '2026-03-08 19:37:27', NULL),
(16, '2026-TD-000007', NULL, NULL, NULL, 21, 17, NULL, NULL, NULL, '2027', 1, 'land', 'land', 2400.00, NULL, 960.00, NULL, 1, NULL, 0.02000, NULL, NULL, 'initial', 'forwarded', 2, 2, '2026-03-09 02:50:21', NULL, 'Auto-generated upon approval of FAAS ARP 00-0002-04175.', '2026-03-09 02:50:21', '2026-03-09 02:50:40', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `td_activity_logs`
--

CREATE TABLE `td_activity_logs` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `tax_declaration_id` bigint(20) UNSIGNED NOT NULL,
  `user_id` bigint(20) UNSIGNED DEFAULT NULL,
  `action` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `meta` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin DEFAULT NULL CHECK (json_valid(`meta`)),
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `td_activity_logs`
--

INSERT INTO `td_activity_logs` (`id`, `tax_declaration_id`, `user_id`, `action`, `description`, `meta`, `created_at`) VALUES
(3, 5, 2, 'approved', 'TD Auto-Generated upon FAAS Approval.', '{\"assessed_value\":0,\"auto_generated\":true}', '2026-03-06 07:39:50'),
(4, 6, 2, 'approved', 'TD Auto-Generated upon FAAS Approval.', '{\"assessed_value\":207.2,\"auto_generated\":true}', '2026-03-06 07:39:50'),
(5, 6, 2, 'forwarded', 'Forwarded to Treasury by .', '{\"forwarded_by\":2,\"forwarded_at\":\"2026-03-06 15:40:28\"}', '2026-03-06 07:40:28'),
(6, 7, 2, 'approved', 'TD Auto-Generated upon FAAS Approval.', '{\"assessed_value\":200,\"auto_generated\":true}', '2026-03-07 18:38:17'),
(7, 5, 2, 'forwarded', 'Forwarded to Treasury by .', '{\"forwarded_by\":2,\"forwarded_at\":\"2026-03-09 01:11:21\"}', '2026-03-08 17:11:21'),
(8, 8, 2, 'approved', 'TD Auto-Generated upon FAAS Approval.', '{\"assessed_value\":2200,\"auto_generated\":true}', '2026-03-08 17:36:46'),
(9, 8, 2, 'forwarded', 'Forwarded to Treasury by .', '{\"forwarded_by\":2,\"forwarded_at\":\"2026-03-09 01:37:48\"}', '2026-03-08 17:37:48'),
(10, 9, 2, 'approved', 'TD Auto-Generated upon FAAS Approval.', '{\"assessed_value\":2200,\"auto_generated\":true}', '2026-03-08 17:52:07'),
(11, 10, 2, 'approved', 'TD Auto-Generated upon FAAS Approval.', '{\"assessed_value\":1950.4,\"auto_generated\":true}', '2026-03-08 19:02:51'),
(12, 10, 2, 'forwarded', 'Forwarded to Treasury by .', '{\"forwarded_by\":2,\"forwarded_at\":\"2026-03-09 03:03:10\"}', '2026-03-08 19:03:10'),
(13, 9, 2, 'forwarded', 'Forwarded to Treasury by .', '{\"forwarded_by\":2,\"forwarded_at\":\"2026-03-09 07:55:37\"}', '2026-03-08 23:55:37'),
(14, 16, 2, 'approved', 'TD Auto-Generated upon FAAS Approval.', '{\"assessed_value\":960,\"auto_generated\":true}', '2026-03-09 02:50:21'),
(15, 16, 2, 'forwarded', 'Forwarded to Treasury by .', '{\"forwarded_by\":2,\"forwarded_at\":\"2026-03-09 10:50:40\"}', '2026-03-09 02:50:40');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `employee_id` bigint(20) UNSIGNED NOT NULL,
  `uname` varchar(255) NOT NULL,
  `encoded_date` datetime DEFAULT NULL,
  `encoded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `is_super_admin` tinyint(1) NOT NULL DEFAULT 0,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `employee_id`, `uname`, `encoded_date`, `encoded_by`, `is_super_admin`, `password`, `created_at`, `updated_at`) VALUES
(2, 2, 'sample', '2026-01-19 03:18:54', 1, 1, '$2y$12$5q7Sf6hjwH2uVqgTefS/tel7P1/Wu32z3h3iYxCzbgHxYfxa7reMm', '2026-01-18 19:18:54', '2026-02-23 17:41:47'),
(3, 4, 'treasury', '2026-01-19 04:12:17', 1, 0, '$2y$12$qhZeAA7.i6zD5.SY5b5ppu2xCeZ2wq.UW0FufX9x8g4KqmaYa748.', '2026-01-18 20:12:17', '2026-01-18 20:12:17'),
(4, 1, 'admin', '2026-01-27 09:02:57', 2, 0, '$2y$12$RyQhI7qAqXl3CQn2zI8nr.qXmZGeqqcxIMoVJZh2cMqNX8AttDV6a', '2026-01-27 01:02:57', '2026-01-27 01:02:57'),
(5, 3, 'mi22@gmail.com', '2026-02-24 01:50:35', 2, 0, '$2y$12$vPXtP25yxuQj9roNeXCSou01wkY16SM.df6r3GrhksQmNZvNhImNW', '2026-02-23 17:50:35', '2026-02-23 17:50:35');

-- --------------------------------------------------------

--
-- Table structure for table `vf_collection_natures`
--

CREATE TABLE `vf_collection_natures` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `account_code` varchar(255) DEFAULT NULL,
  `default_amount` decimal(12,2) NOT NULL DEFAULT 0.00,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `sort_order` int(10) UNSIGNED NOT NULL DEFAULT 0,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vf_collection_natures`
--

INSERT INTO `vf_collection_natures` (`id`, `name`, `account_code`, `default_amount`, `is_active`, `sort_order`, `description`, `created_at`, `updated_at`) VALUES
(1, 'Franchise Fee', '1-01-01', 500.00, 1, 1, NULL, '2026-03-11 19:13:06', '2026-03-11 19:13:06'),
(2, 'Sticker Fee', '1-01-02', 100.00, 1, 2, NULL, '2026-03-11 19:13:06', '2026-03-11 19:13:06'),
(3, 'Penalty', '1-01-03', 0.00, 1, 3, NULL, '2026-03-11 19:13:06', '2026-03-11 19:13:06'),
(4, 'MTOP Fee', '1-01-04', 200.00, 1, 4, NULL, '2026-03-11 19:13:06', '2026-03-11 19:13:06'),
(5, 'Drivers ID Fee', '1-01-05', 150.00, 1, 5, NULL, '2026-03-11 19:13:06', '2026-03-11 19:13:06'),
(6, 'Garage Inspection Fee', '1-01-06', 50.00, 1, 6, NULL, '2026-03-11 19:13:06', '2026-03-11 19:13:06');

-- --------------------------------------------------------

--
-- Table structure for table `vf_franchises`
--

CREATE TABLE `vf_franchises` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `fn_number` int(10) UNSIGNED NOT NULL,
  `permit_number` varchar(255) NOT NULL,
  `permit_date` date NOT NULL,
  `permit_type` enum('new','renewal','transfer','amendment') NOT NULL DEFAULT 'new',
  `franchised_at` date DEFAULT NULL,
  `owner_id` bigint(20) UNSIGNED NOT NULL,
  `toda_id` bigint(20) UNSIGNED DEFAULT NULL,
  `driver_name` varchar(255) DEFAULT NULL,
  `driver_contact` varchar(255) DEFAULT NULL,
  `license_number` varchar(255) DEFAULT NULL,
  `remarks` text DEFAULT NULL,
  `status` enum('active','pending','inactive','retired') NOT NULL DEFAULT 'active',
  `retirement_reason` varchar(255) DEFAULT NULL,
  `retirement_date` date DEFAULT NULL,
  `retirement_remarks` text DEFAULT NULL,
  `retired_at` timestamp NULL DEFAULT NULL,
  `retired_by` bigint(20) UNSIGNED DEFAULT NULL,
  `encoded_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vf_franchises`
--

INSERT INTO `vf_franchises` (`id`, `fn_number`, `permit_number`, `permit_date`, `permit_type`, `franchised_at`, `owner_id`, `toda_id`, `driver_name`, `driver_contact`, `license_number`, `remarks`, `status`, `retirement_reason`, `retirement_date`, `retirement_remarks`, `retired_at`, `retired_by`, `encoded_by`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 1, '1-2026', '2026-03-11', 'new', NULL, 1, 1, 'gerry', '0230293023232', '123123', '23', 'retired', NULL, NULL, NULL, NULL, NULL, 2, '2026-03-10 23:32:15', '2026-03-16 01:17:22', NULL),
(2, 2, '2-2027', '2026-03-16', 'renewal', '2025-01-01', 2, 1, 'gerry', '0230293023232', '123123', NULL, 'retired', NULL, NULL, NULL, NULL, NULL, 2, '2026-03-11 22:24:25', '2026-03-16 01:17:09', NULL),
(3, 3, '3-2026', '2026-03-17', 'new', '2024-01-01', 3, 1, 'gerry', '0230293023232', '123123', NULL, 'active', NULL, NULL, NULL, NULL, NULL, 2, '2026-03-16 22:10:16', '2026-03-16 22:10:16', NULL),
(4, 999, '999-2026', '2024-01-01', 'new', NULL, 1, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-18 02:32:35', '2026-03-18 02:32:35', NULL),
(5, 998, '998-2023', '2023-01-01', 'new', '2023-01-01', 1, NULL, NULL, NULL, NULL, NULL, 'active', NULL, NULL, NULL, NULL, NULL, NULL, '2026-03-18 02:39:59', '2026-03-18 02:39:59', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vf_franchise_history`
--

CREATE TABLE `vf_franchise_history` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `franchise_id` bigint(20) UNSIGNED NOT NULL,
  `action` enum('created','amended','renewed','transferred','retired','reactivated') NOT NULL,
  `permit_number` varchar(255) DEFAULT NULL,
  `action_date` date NOT NULL,
  `notes` text DEFAULT NULL,
  `performed_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vf_franchise_history`
--

INSERT INTO `vf_franchise_history` (`id`, `franchise_id`, `action`, `permit_number`, `action_date`, `notes`, `performed_by`, `created_at`, `updated_at`) VALUES
(1, 1, 'created', '1-2026', '2026-03-11', 'Initial franchise registration.', 2, '2026-03-10 23:32:15', '2026-03-10 23:32:15'),
(2, 2, 'created', '2-2026', '2026-03-12', 'Initial franchise registration.', 2, '2026-03-11 22:24:25', '2026-03-11 22:24:25'),
(3, 2, 'renewed', '3-2026', '2026-03-14', 'Franchise renewed.', 2, '2026-03-13 23:38:51', '2026-03-13 23:38:51'),
(4, 2, 'renewed', '4-2026', '2026-03-14', 'Franchise renewed. OR #123502.', 2, '2026-03-13 23:48:47', '2026-03-13 23:48:47'),
(5, 2, 'renewed', '2-2027', '2026-03-16', 'Renewed for 2027. OR #123503. Late penalty of ₱100 applied (-72 days late).', 2, '2026-03-15 23:52:25', '2026-03-15 23:52:25'),
(6, 2, 'retired', '2-2027', '2026-03-16', 'This person is iron', 2, '2026-03-16 01:17:09', '2026-03-16 01:17:09'),
(7, 1, 'retired', '1-2026', '2026-03-16', 'asd', 2, '2026-03-16 01:17:22', '2026-03-16 01:17:22'),
(8, 3, 'created', '3-2026', '2026-03-17', 'Initial franchise registration.', 2, '2026-03-16 22:10:16', '2026-03-16 22:10:16');

-- --------------------------------------------------------

--
-- Table structure for table `vf_owners`
--

CREATE TABLE `vf_owners` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `last_name` varchar(255) NOT NULL,
  `first_name` varchar(255) NOT NULL,
  `middle_name` varchar(255) DEFAULT NULL,
  `citizenship` varchar(255) NOT NULL DEFAULT 'FILIPINO',
  `civil_status` enum('single','married','widowed','separated') DEFAULT NULL,
  `gender` enum('male','female') DEFAULT NULL,
  `ownership_type` varchar(255) DEFAULT NULL,
  `contact_number` varchar(255) DEFAULT NULL,
  `birthday` date DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `current_address` text DEFAULT NULL,
  `ctc_receipt_number` varchar(255) DEFAULT NULL,
  `ctc_date_issued` date DEFAULT NULL,
  `ctc_issued_at` varchar(255) DEFAULT 'MTO-Majayjay',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vf_owners`
--

INSERT INTO `vf_owners` (`id`, `last_name`, `first_name`, `middle_name`, `citizenship`, `civil_status`, `gender`, `ownership_type`, `contact_number`, `birthday`, `barangay`, `current_address`, `ctc_receipt_number`, `ctc_date_issued`, `ctc_issued_at`, `created_at`, `updated_at`, `deleted_at`) VALUES
(1, 'Taar', 'Rohan', 'P', 'FILIPINO', 'married', 'male', 'for_hire', '92992929', '2026-03-11', 'San Francisco', 'asdasd\r\nasdasd', '232323', '2026-03-11', 'MTO-Majayjay', '2026-03-10 23:32:15', '2026-03-10 23:32:15', NULL),
(2, 'Taar', 'Rohan', 'P', 'FILIPINO', 'married', 'male', 'private', '92992929', '2026-03-11', 'Bakia', 'imbunia', '232323', '2026-03-11', 'MTO-Majayjay', '2026-03-11 22:24:25', '2026-03-11 22:24:25', NULL),
(3, 'Taar', 'Rohan', 'P', 'FILIPINO', 'married', 'male', 'for_hire', '92992929', '2026-03-11', 'Origuel', 'asdasd\r\nasdasd', '232323', '2026-03-11', 'MTO-Majayjay', '2026-03-16 22:10:16', '2026-03-16 22:10:16', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `vf_payments`
--

CREATE TABLE `vf_payments` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `or_number` varchar(255) NOT NULL,
  `or_date` date NOT NULL,
  `agency` varchar(255) DEFAULT NULL,
  `fund` varchar(255) DEFAULT NULL,
  `payor` varchar(255) NOT NULL,
  `franchise_id` bigint(20) UNSIGNED NOT NULL,
  `collection_items` longtext CHARACTER SET utf8mb4 COLLATE utf8mb4_bin NOT NULL CHECK (json_valid(`collection_items`)),
  `total_amount` decimal(10,2) NOT NULL,
  `amount_in_words` varchar(255) DEFAULT NULL,
  `payment_method` enum('cash','check','money_order') NOT NULL DEFAULT 'cash',
  `drawee_bank` varchar(255) DEFAULT NULL,
  `check_mo_number` varchar(255) DEFAULT NULL,
  `check_mo_date` date DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'paid',
  `remarks` text DEFAULT NULL,
  `collected_by` bigint(20) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vf_payments`
--

INSERT INTO `vf_payments` (`id`, `or_number`, `or_date`, `agency`, `fund`, `payor`, `franchise_id`, `collection_items`, `total_amount`, `amount_in_words`, `payment_method`, `drawee_bank`, `check_mo_number`, `check_mo_date`, `status`, `remarks`, `collected_by`, `created_at`, `updated_at`) VALUES
(1, '2026-0001', '2026-03-12', 'LGU – Municipality/City', 'General Fund', 'Taar, Rohan P.', 1, '[{\"nature\":\"Sticker Fee\",\"account_code\":\"1-01-02\",\"amount\":\"100.00\"},{\"nature\":\"MTOP Fee\",\"account_code\":\"1-01-04\",\"amount\":\"200.00\"},{\"nature\":\"Drivers ID Fee\",\"account_code\":\"1-01-05\",\"amount\":\"150.00\"},{\"nature\":\"Garage Inspection Fee\",\"account_code\":\"1-01-06\",\"amount\":\"50.00\"}]', 500.00, 'FIVE HUNDRED PESOS ONLY', 'cash', NULL, NULL, NULL, 'paid', NULL, 2, '2026-03-11 20:16:01', '2026-03-11 20:16:01'),
(2, '123533', '2026-03-12', 'LGU – Municipality/City', 'General Fund', 'Taar, Rohan P.', 1, '[{\"nature\":\"Franchise Fee\",\"account_code\":\"1-01-01\",\"amount\":\"500.00\"},{\"nature\":\"Sticker Fee\",\"account_code\":\"1-01-02\",\"amount\":\"100.00\"},{\"nature\":\"MTOP Fee\",\"account_code\":\"1-01-04\",\"amount\":\"200.00\"},{\"nature\":\"Drivers ID Fee\",\"account_code\":\"1-01-05\",\"amount\":\"150.00\"},{\"nature\":\"Garage Inspection Fee\",\"account_code\":\"1-01-06\",\"amount\":\"50.00\"}]', 1000.00, 'ONE THOUSAND PESOS ONLY', 'cash', NULL, NULL, NULL, 'paid', NULL, 2, '2026-03-12 00:04:13', '2026-03-12 00:04:13'),
(3, '123501', '2026-03-13', 'LGU – Municipality/City', 'General Fund', 'Taar, Rohan P.', 2, '[{\"nature\":\"Franchise Fee\",\"account_code\":\"1-01-01\",\"amount\":\"500.00\"},{\"nature\":\"Sticker Fee\",\"account_code\":\"1-01-02\",\"amount\":\"100.00\"},{\"nature\":\"MTOP Fee\",\"account_code\":\"1-01-04\",\"amount\":\"200.00\"},{\"nature\":\"Drivers ID Fee\",\"account_code\":\"1-01-05\",\"amount\":\"150.00\"},{\"nature\":\"Garage Inspection Fee\",\"account_code\":\"1-01-06\",\"amount\":\"50.00\"}]', 1000.00, 'ONE THOUSAND PESOS ONLY', 'cash', NULL, NULL, NULL, 'paid', NULL, 2, '2026-03-12 22:23:17', '2026-03-12 22:23:17'),
(4, '123502', '2026-03-14', 'LGU – Municipality/City', 'General Fund', 'TAAR, ROHAN P', 2, '[{\"nature\":\"Franchise Fee\",\"account_code\":\"1-01-01\",\"amount\":\"500.00\"},{\"nature\":\"Sticker Fee\",\"account_code\":\"1-01-02\",\"amount\":\"100.00\"},{\"nature\":\"MTOP Fee\",\"account_code\":\"1-01-04\",\"amount\":\"200.00\"},{\"nature\":\"Drivers ID Fee\",\"account_code\":\"1-01-05\",\"amount\":\"150.00\"},{\"nature\":\"Garage Inspection Fee\",\"account_code\":\"1-01-06\",\"amount\":\"50.00\"}]', 1000.00, 'ONE THOUSAND PESOS ONLY', 'cash', NULL, NULL, NULL, 'paid', NULL, 2, '2026-03-13 23:48:47', '2026-03-13 23:48:47'),
(5, '123504', '2026-03-16', 'LGU – Municipality/City', 'General Fund', 'Taar, Rohan P.', 2, '[{\"nature\":\"Franchise Fee\",\"account_code\":\"1-01-01\",\"amount\":\"500.00\"},{\"nature\":\"Sticker Fee\",\"account_code\":\"1-01-02\",\"amount\":\"100.00\"},{\"nature\":\"MTOP Fee\",\"account_code\":\"1-01-04\",\"amount\":\"200.00\"},{\"nature\":\"Drivers ID Fee\",\"account_code\":\"1-01-05\",\"amount\":\"150.00\"},{\"nature\":\"Garage Inspection Fee\",\"account_code\":\"1-01-06\",\"amount\":\"50.00\"}]', 1000.00, 'ONE THOUSAND PESOS ONLY', 'cash', NULL, NULL, NULL, 'paid', NULL, 2, '2026-03-15 16:41:25', '2026-03-15 16:41:25'),
(6, '123503', '2026-03-16', 'LGU – Municipality/City', 'General Fund', 'Taar, Rohan P.', 2, '[{\"nature\":\"Franchise Fee\",\"account_code\":\"1-01-01\",\"amount\":\"500.00\"},{\"nature\":\"Sticker Fee\",\"account_code\":\"1-01-02\",\"amount\":\"100.00\"},{\"nature\":\"MTOP Fee\",\"account_code\":\"1-01-04\",\"amount\":\"200.00\"},{\"nature\":\"Drivers ID Fee\",\"account_code\":\"1-01-05\",\"amount\":\"150.00\"},{\"nature\":\"Garage Inspection Fee\",\"account_code\":\"1-01-06\",\"amount\":\"50.00\"},{\"nature\":\"Late Renewal Penalty (10%)\",\"account_code\":\"55252\",\"amount\":100}]', 1100.00, 'ONE THOUSAND ONE HUNDRED PESOS ONLY', 'cash', NULL, NULL, NULL, 'paid', NULL, 2, '2026-03-15 23:52:25', '2026-03-15 23:52:25'),
(7, 'TEST-001', '2023-03-01', NULL, NULL, 'Test Owner', 5, '[{\"nature\":\"Franchise Fee\",\"account_code\":\"1-01-01\",\"amount\":\"500.00\"}]', 500.00, 'FIVE HUNDRED PESOS ONLY', 'cash', NULL, NULL, NULL, 'paid', NULL, NULL, '2026-03-18 02:39:59', '2026-03-18 02:39:59');

-- --------------------------------------------------------

--
-- Table structure for table `vf_settings`
--

CREATE TABLE `vf_settings` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `key` varchar(255) NOT NULL,
  `value` text DEFAULT NULL,
  `label` varchar(255) DEFAULT NULL,
  `type` varchar(255) NOT NULL DEFAULT 'string',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vf_settings`
--

INSERT INTO `vf_settings` (`id`, `key`, `value`, `label`, `type`, `created_at`, `updated_at`) VALUES
(1, 'penalty_type', 'percentage', 'Default Penalty Type', 'string', '2026-03-15 23:31:03', '2026-03-15 23:47:37'),
(2, 'penalty_value', '10', 'Default Penalty Value', 'decimal', '2026-03-15 23:31:03', '2026-03-15 23:47:37'),
(3, 'penalty_grace_days', '2', 'Grace Period (Days)', 'integer', '2026-03-15 23:31:03', '2026-03-15 23:47:37'),
(4, 'penalty_account_code', '55252', 'Penalty Account Code', 'string', '2026-03-15 23:31:03', '2026-03-15 23:47:37');

-- --------------------------------------------------------

--
-- Table structure for table `vf_settings_and_franchise_date`
--

CREATE TABLE `vf_settings_and_franchise_date` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------

--
-- Table structure for table `vf_todas`
--

CREATE TABLE `vf_todas` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `name` varchar(255) NOT NULL,
  `abbreviation` varchar(255) DEFAULT NULL,
  `barangay` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vf_todas`
--

INSERT INTO `vf_todas` (`id`, `name`, `abbreviation`, `barangay`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'TODA Majayjay Central', 'TMC', 'Poblacion', 1, '2026-03-11 07:29:19', '2026-03-11 07:29:19'),
(2, 'TODA Buenavista', 'TB', 'Buenavista', 1, '2026-03-11 07:29:19', '2026-03-11 07:29:19'),
(3, 'TODA San Jose', 'TSJ', 'San Jose', 1, '2026-03-11 07:29:19', '2026-03-11 07:29:19');

-- --------------------------------------------------------

--
-- Table structure for table `vf_vehicles`
--

CREATE TABLE `vf_vehicles` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `franchise_id` bigint(20) UNSIGNED NOT NULL,
  `make` varchar(255) DEFAULT NULL,
  `model` varchar(255) DEFAULT NULL,
  `franchise_type` varchar(255) DEFAULT NULL,
  `motor_number` varchar(255) DEFAULT NULL,
  `chassis_number` varchar(255) DEFAULT NULL,
  `plate_number` varchar(255) DEFAULT NULL,
  `year_model` year(4) DEFAULT NULL,
  `color` varchar(255) DEFAULT NULL,
  `sticker_number` varchar(255) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `vf_vehicles`
--

INSERT INTO `vf_vehicles` (`id`, `franchise_id`, `make`, `model`, `franchise_type`, `motor_number`, `chassis_number`, `plate_number`, `year_model`, `color`, `sticker_number`, `created_at`, `updated_at`) VALUES
(1, 1, 'Yamaha', '4545', 'Kuliglig', '3434', '34343', '34343', '2010', 'BLACK', '23232', '2026-03-10 23:32:15', '2026-03-10 23:32:15'),
(2, 2, 'Yamaha', '4545', 'Kuliglig', '3434', '34343', '34343', NULL, 'BLACK', '23232', '2026-03-11 22:24:25', '2026-03-11 22:24:25'),
(3, 3, 'Honda', '4545', 'Kuliglig', '3434', '34343', '34343', NULL, 'BLACK', '23232', '2026-03-16 22:10:16', '2026-03-16 22:10:16');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `applicants`
--
ALTER TABLE `applicants`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `applicants_application_number_unique` (`application_number`),
  ADD KEY `applicants_job_vacancy_id_status_index` (`job_vacancy_id`,`status`);

--
-- Indexes for table `applicant_documents`
--
ALTER TABLE `applicant_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `applicant_documents_applicant_id_foreign` (`applicant_id`);

--
-- Indexes for table `appointments`
--
ALTER TABLE `appointments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `appointments_appointment_number_unique` (`appointment_number`),
  ADD KEY `appointments_applicant_id_foreign` (`applicant_id`),
  ADD KEY `appointments_plantilla_id_foreign` (`plantilla_id`),
  ADD KEY `appointments_employment_type_id_foreign` (`employment_type_id`),
  ADD KEY `appointments_salary_grade_id_foreign` (`salary_grade_id`),
  ADD KEY `appointments_created_by_foreign` (`created_by`),
  ADD KEY `appointments_employee_id_status_index` (`employee_id`,`status`),
  ADD KEY `appointments_office_id_appointment_type_index` (`office_id`,`appointment_type`);

--
-- Indexes for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `audit_logs_user_id_foreign` (`user_id`),
  ADD KEY `audit_logs_model_type_model_id_index` (`model_type`,`model_id`),
  ADD KEY `audit_logs_module_created_at_index` (`module`,`created_at`),
  ADD KEY `audit_logs_module_index` (`module`),
  ADD KEY `audit_logs_action_index` (`action`),
  ADD KEY `audit_logs_model_type_index` (`model_type`),
  ADD KEY `audit_logs_model_id_index` (`model_id`),
  ADD KEY `audit_logs_status_index` (`status`);

--
-- Indexes for table `barangays`
--
ALTER TABLE `barangays`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `barangays_brgy_code_unique` (`brgy_code`);

--
-- Indexes for table `bpls_activity_logs`
--
ALTER TABLE `bpls_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bpls_activity_logs_bpls_application_id_foreign` (`bpls_application_id`);

--
-- Indexes for table `bpls_application_ors`
--
ALTER TABLE `bpls_application_ors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bpls_application_ors_or_number_unique` (`or_number`),
  ADD KEY `bpls_application_ors_or_assignment_id_foreign` (`or_assignment_id`),
  ADD KEY `bpls_application_ors_bpls_application_id_index` (`bpls_application_id`);

--
-- Indexes for table `bpls_assessments`
--
ALTER TABLE `bpls_assessments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bpls_assessments_bpls_application_id_foreign` (`bpls_application_id`),
  ADD KEY `bpls_assessments_assessed_by_foreign` (`assessed_by`);

--
-- Indexes for table `bpls_benefits`
--
ALTER TABLE `bpls_benefits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bpls_benefits_field_key_unique` (`field_key`);

--
-- Indexes for table `bpls_businesses`
--
ALTER TABLE `bpls_businesses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bpls_business_amendments`
--
ALTER TABLE `bpls_business_amendments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bpls_business_amendments_amended_by_foreign` (`amended_by`),
  ADD KEY `bpls_business_amendments_business_entry_id_index` (`business_entry_id`),
  ADD KEY `bpls_business_amendments_amendment_type_index` (`amendment_type`),
  ADD KEY `bpls_business_amendments_amended_at_index` (`amended_at`);

--
-- Indexes for table `bpls_business_entries`
--
ALTER TABLE `bpls_business_entries`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bpls_business_entries_business_id_unique` (`business_id`);

--
-- Indexes for table `bpls_documents`
--
ALTER TABLE `bpls_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bpls_documents_bpls_application_id_foreign` (`bpls_application_id`);

--
-- Indexes for table `bpls_entry_benefits`
--
ALTER TABLE `bpls_entry_benefits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bpls_entry_benefits_business_entry_id_benefit_id_unique` (`business_entry_id`,`benefit_id`),
  ADD KEY `bpls_entry_benefits_benefit_id_foreign` (`benefit_id`);

--
-- Indexes for table `bpls_online_applications`
--
ALTER TABLE `bpls_online_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bpls_applications_application_number_unique` (`application_number`),
  ADD KEY `bpls_applications_client_id_foreign` (`client_id`),
  ADD KEY `bpls_applications_bpls_business_id_foreign` (`bpls_business_id`),
  ADD KEY `bpls_applications_bpls_owner_id_foreign` (`bpls_owner_id`),
  ADD KEY `bpls_applications_verified_by_foreign` (`verified_by`),
  ADD KEY `bpls_applications_assessed_by_foreign` (`assessed_by`),
  ADD KEY `bpls_applications_approved_by_foreign` (`approved_by`);

--
-- Indexes for table `bpls_online_payments`
--
ALTER TABLE `bpls_online_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bpls_online_payments_reference_number_unique` (`reference_number`),
  ADD KEY `bpls_online_payments_bpls_application_id_foreign` (`bpls_application_id`),
  ADD KEY `bpls_online_payments_bpls_assessment_id_foreign` (`bpls_assessment_id`);

--
-- Indexes for table `bpls_owners`
--
ALTER TABLE `bpls_owners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bpls_owner_benefits`
--
ALTER TABLE `bpls_owner_benefits`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bpls_owner_benefits_owner_id_benefit_id_unique` (`owner_id`,`benefit_id`),
  ADD KEY `bpls_owner_benefits_benefit_id_foreign` (`benefit_id`);

--
-- Indexes for table `bpls_payments`
--
ALTER TABLE `bpls_payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `bpls_payments_business_entry_id_foreign` (`business_entry_id`),
  ADD KEY `bpls_payments_bpls_application_id_foreign` (`bpls_application_id`);

--
-- Indexes for table `bpls_permit_signatories`
--
ALTER TABLE `bpls_permit_signatories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `bpls_settings`
--
ALTER TABLE `bpls_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `bpls_settings_key_unique` (`key`);

--
-- Indexes for table `cache`
--
ALTER TABLE `cache`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `cache_locks`
--
ALTER TABLE `cache_locks`
  ADD PRIMARY KEY (`key`);

--
-- Indexes for table `clients`
--
ALTER TABLE `clients`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `clients_email_unique` (`email`);

--
-- Indexes for table `client_linked_properties`
--
ALTER TABLE `client_linked_properties`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `client_linked_properties_client_id_tax_declaration_id_unique` (`client_id`,`tax_declaration_id`),
  ADD KEY `client_linked_properties_tax_declaration_id_foreign` (`tax_declaration_id`);

--
-- Indexes for table `defaultz`
--
ALTER TABLE `defaultz`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `departments`
--
ALTER TABLE `departments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `departments_department_name_unique` (`department_name`);

--
-- Indexes for table `divisions`
--
ALTER TABLE `divisions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `divisions_division_code_unique` (`division_code`),
  ADD KEY `divisions_office_id_index` (`office_id`);

--
-- Indexes for table `employee_civil_service`
--
ALTER TABLE `employee_civil_service`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_documents`
--
ALTER TABLE `employee_documents`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_education`
--
ALTER TABLE `employee_education`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_family_background`
--
ALTER TABLE `employee_family_background`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_government_ids`
--
ALTER TABLE `employee_government_ids`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_info`
--
ALTER TABLE `employee_info`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employee_info_employee_id_unique` (`employee_id`),
  ADD UNIQUE KEY `employee_info_email_unique` (`email`),
  ADD KEY `employee_info_department_id_foreign` (`department_id`),
  ADD KEY `employee_info_plantilla_position_id_foreign` (`plantilla_position_id`);

--
-- Indexes for table `employee_trainings`
--
ALTER TABLE `employee_trainings`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employee_work_experience`
--
ALTER TABLE `employee_work_experience`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `employment_types`
--
ALTER TABLE `employment_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `employment_types_type_code_unique` (`type_code`);

--
-- Indexes for table `faas_activity_logs`
--
ALTER TABLE `faas_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faas_activity_logs_faas_property_id_foreign` (`faas_property_id`),
  ADD KEY `faas_activity_logs_user_id_foreign` (`user_id`);

--
-- Indexes for table `faas_attachments`
--
ALTER TABLE `faas_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faas_attachments_faas_property_id_foreign` (`faas_property_id`),
  ADD KEY `faas_attachments_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `faas_buildings`
--
ALTER TABLE `faas_buildings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faas_buildings_faas_property_id_foreign` (`faas_property_id`),
  ADD KEY `faas_buildings_faas_land_id_foreign` (`faas_land_id`),
  ADD KEY `faas_buildings_rpta_bldg_type_id_foreign` (`rpta_bldg_type_id`),
  ADD KEY `faas_buildings_rpta_actual_use_id_foreign` (`rpta_actual_use_id`);

--
-- Indexes for table `faas_building_improvements`
--
ALTER TABLE `faas_building_improvements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faas_building_improvements_building_id_foreign` (`building_id`),
  ADD KEY `faas_building_improvements_improvement_id_foreign` (`improvement_id`);

--
-- Indexes for table `faas_gen_rev`
--
ALTER TABLE `faas_gen_rev`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `faas_gen_rev_td_no_unique` (`td_no`),
  ADD KEY `faas_gen_rev_kind_revised_year_bcode_index` (`revised_year`,`bcode`);

--
-- Indexes for table `faas_gen_rev_geometries`
--
ALTER TABLE `faas_gen_rev_geometries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faas_gen_rev_geometries_faas_id_foreign` (`faas_id`),
  ADD KEY `faas_gen_rev_geometries_pin_index` (`pin`);

--
-- Indexes for table `faas_lands`
--
ALTER TABLE `faas_lands`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faas_lands_faas_property_id_foreign` (`faas_property_id`),
  ADD KEY `faas_lands_rpta_actual_use_id_foreign` (`rpta_actual_use_id`);

--
-- Indexes for table `faas_land_improvements`
--
ALTER TABLE `faas_land_improvements`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faas_land_improvements_land_id_foreign` (`land_id`),
  ADD KEY `faas_land_improvements_improvement_id_foreign` (`improvement_id`);

--
-- Indexes for table `faas_machineries`
--
ALTER TABLE `faas_machineries`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faas_machineries_faas_property_id_foreign` (`faas_property_id`),
  ADD KEY `faas_machineries_rpta_actual_use_id_foreign` (`rpta_actual_use_id`),
  ADD KEY `faas_machineries_faas_land_id_foreign` (`faas_land_id`);

--
-- Indexes for table `faas_machines`
--
ALTER TABLE `faas_machines`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faas_machines_faas_id_index` (`faas_id`),
  ADD KEY `faas_machines_td_no_index` (`td_no`);

--
-- Indexes for table `faas_owners`
--
ALTER TABLE `faas_owners`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faas_owners_faas_id_foreign` (`faas_id`),
  ADD KEY `faas_owners_owner_id_foreign` (`owner_id`);

--
-- Indexes for table `faas_predecessors`
--
ALTER TABLE `faas_predecessors`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_predecessor` (`faas_property_id`,`previous_faas_property_id`),
  ADD KEY `faas_predecessors_previous_faas_property_id_foreign` (`previous_faas_property_id`);

--
-- Indexes for table `faas_properties`
--
ALTER TABLE `faas_properties`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `faas_properties_arp_no_unique` (`arp_no`),
  ADD KEY `faas_properties_barangay_id_foreign` (`barangay_id`),
  ADD KEY `faas_properties_created_by_foreign` (`created_by`),
  ADD KEY `faas_properties_approved_by_foreign` (`approved_by`),
  ADD KEY `faas_properties_previous_faas_property_id_foreign` (`previous_faas_property_id`),
  ADD KEY `faas_properties_property_registration_id_foreign` (`property_registration_id`),
  ADD KEY `faas_properties_revision_year_id_foreign` (`revision_year_id`),
  ADD KEY `faas_properties_parent_land_faas_id_foreign` (`parent_land_faas_id`);

--
-- Indexes for table `faas_revision_logs`
--
ALTER TABLE `faas_revision_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faas_revision_logs_faas_id_foreign` (`faas_id`);

--
-- Indexes for table `faas_rpta_audit`
--
ALTER TABLE `faas_rpta_audit`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faas_rpta_audit_username_index` (`username`),
  ADD KEY `faas_rpta_audit_action_taken_index` (`action_taken`);

--
-- Indexes for table `faas_rpta_owner_select`
--
ALTER TABLE `faas_rpta_owner_select`
  ADD PRIMARY KEY (`id`),
  ADD KEY `faas_rpta_owner_select_owner_name_index` (`owner_name`),
  ADD KEY `faas_rpta_owner_select_encoded_by_index` (`encoded_by`);

--
-- Indexes for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`);

--
-- Indexes for table `fee_rules`
--
ALTER TABLE `fee_rules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `form_options`
--
ALTER TABLE `form_options`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `form_options_category_value_unique` (`category`,`value`),
  ADD KEY `form_options_category_index` (`category`);

--
-- Indexes for table `hr_daily_time_records`
--
ALTER TABLE `hr_daily_time_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_daily_time_records_employee_id_record_date_unique` (`employee_id`,`record_date`);

--
-- Indexes for table `hr_deduction_types`
--
ALTER TABLE `hr_deduction_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_deduction_types_code_unique` (`code`);

--
-- Indexes for table `hr_employee_deductions`
--
ALTER TABLE `hr_employee_deductions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_employee_deductions_employee_id_foreign` (`employee_id`),
  ADD KEY `hr_employee_deductions_deduction_type_id_foreign` (`deduction_type_id`);

--
-- Indexes for table `hr_employee_schedules`
--
ALTER TABLE `hr_employee_schedules`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_employee_schedules_employee_id_foreign` (`employee_id`),
  ADD KEY `hr_employee_schedules_schedule_id_foreign` (`schedule_id`);

--
-- Indexes for table `hr_leave_applications`
--
ALTER TABLE `hr_leave_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_leave_applications_reference_no_unique` (`reference_no`),
  ADD KEY `hr_leave_applications_employee_id_foreign` (`employee_id`),
  ADD KEY `hr_leave_applications_leave_type_id_foreign` (`leave_type_id`),
  ADD KEY `hr_leave_applications_approved_by_foreign` (`approved_by`),
  ADD KEY `hr_leave_applications_filed_by_foreign` (`filed_by`);

--
-- Indexes for table `hr_leave_balances`
--
ALTER TABLE `hr_leave_balances`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_leave_balances_employee_id_leave_type_id_year_unique` (`employee_id`,`leave_type_id`,`year`),
  ADD KEY `hr_leave_balances_leave_type_id_foreign` (`leave_type_id`);

--
-- Indexes for table `hr_leave_types`
--
ALTER TABLE `hr_leave_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_leave_types_code_unique` (`code`);

--
-- Indexes for table `hr_payroll_periods`
--
ALTER TABLE `hr_payroll_periods`
  ADD PRIMARY KEY (`id`),
  ADD KEY `hr_payroll_periods_created_by_foreign` (`created_by`);

--
-- Indexes for table `hr_payroll_records`
--
ALTER TABLE `hr_payroll_records`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_payroll_records_payroll_period_id_employee_id_unique` (`payroll_period_id`,`employee_id`),
  ADD KEY `hr_payroll_records_employee_id_foreign` (`employee_id`);

--
-- Indexes for table `hr_plantilla_positions`
--
ALTER TABLE `hr_plantilla_positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_plantilla_positions_item_number_unique` (`item_number`),
  ADD KEY `hr_plantilla_positions_salary_grade_id_foreign` (`salary_grade_id`),
  ADD KEY `hr_plantilla_positions_department_id_foreign` (`department_id`),
  ADD KEY `hr_plantilla_positions_office_id_foreign` (`office_id`);

--
-- Indexes for table `hr_salary_grades`
--
ALTER TABLE `hr_salary_grades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `hr_sg_grade_year_unique` (`grade`,`implementation_year`);

--
-- Indexes for table `hr_time_logs`
--
ALTER TABLE `hr_time_logs`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_employee_log` (`employee_id`,`log_date`,`log_time`);

--
-- Indexes for table `hr_work_schedules`
--
ALTER TABLE `hr_work_schedules`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `interviews`
--
ALTER TABLE `interviews`
  ADD PRIMARY KEY (`id`),
  ADD KEY `interviews_interviewer_id_foreign` (`interviewer_id`),
  ADD KEY `interviews_applicant_id_scheduled_at_index` (`applicant_id`,`scheduled_at`);

--
-- Indexes for table `jobs`
--
ALTER TABLE `jobs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `jobs_queue_index` (`queue`);

--
-- Indexes for table `job_batches`
--
ALTER TABLE `job_batches`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `job_positions`
--
ALTER TABLE `job_positions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `job_positions_position_code_unique` (`position_code`),
  ADD KEY `job_positions_division_id_foreign` (`division_id`),
  ADD KEY `job_positions_department_id_foreign` (`department_id`),
  ADD KEY `job_positions_salary_grade_id_foreign` (`salary_grade_id`),
  ADD KEY `job_positions_employment_type_id_foreign` (`employment_type_id`),
  ADD KEY `job_positions_office_id_division_id_department_id_index` (`office_id`,`division_id`,`department_id`);

--
-- Indexes for table `job_vacancies`
--
ALTER TABLE `job_vacancies`
  ADD PRIMARY KEY (`id`),
  ADD KEY `job_vacancies_office_id_foreign` (`office_id`),
  ADD KEY `job_vacancies_plantilla_id_foreign` (`plantilla_id`),
  ADD KEY `job_vacancies_salary_grade_id_foreign` (`salary_grade_id`),
  ADD KEY `job_vacancies_created_by_foreign` (`created_by`);

--
-- Indexes for table `machinery_valuations`
--
ALTER TABLE `machinery_valuations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `machinery_valuations_machine_id_index` (`machine_id`);

--
-- Indexes for table `migrations`
--
ALTER TABLE `migrations`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `modules`
--
ALTER TABLE `modules`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `modules_slug_unique` (`slug`);

--
-- Indexes for table `offices`
--
ALTER TABLE `offices`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `offices_office_code_unique` (`office_code`),
  ADD KEY `offices_parent_office_id_foreign` (`parent_office_id`);

--
-- Indexes for table `or_assignments`
--
ALTER TABLE `or_assignments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `or_assignments_user_id_foreign` (`user_id`);

--
-- Indexes for table `password_reset_tokens`
--
ALTER TABLE `password_reset_tokens`
  ADD PRIMARY KEY (`email`);

--
-- Indexes for table `plantilla`
--
ALTER TABLE `plantilla`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `plantilla_item_number_unique` (`item_number`),
  ADD KEY `plantilla_division_id_foreign` (`division_id`),
  ADD KEY `plantilla_department_id_foreign` (`department_id`),
  ADD KEY `plantilla_employment_type_id_foreign` (`employment_type_id`),
  ADD KEY `plantilla_office_id_is_vacant_index` (`office_id`,`is_vacant`),
  ADD KEY `plantilla_salary_grade_id_is_vacant_index` (`salary_grade_id`,`is_vacant`);

--
-- Indexes for table `roles`
--
ALTER TABLE `roles`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `roles_slug_unique` (`slug`);

--
-- Indexes for table `role_module`
--
ALTER TABLE `role_module`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_module_role_id_module_id_unique` (`role_id`,`module_id`),
  ADD KEY `role_module_module_id_foreign` (`module_id`);

--
-- Indexes for table `role_user`
--
ALTER TABLE `role_user`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `role_user_role_id_user_id_unique` (`role_id`,`user_id`),
  ADD KEY `role_user_user_id_foreign` (`user_id`);

--
-- Indexes for table `rpta_actual_uses`
--
ALTER TABLE `rpta_actual_uses`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rpta_actual_uses_code_unique` (`code`),
  ADD KEY `rpta_actual_uses_rpta_class_id_foreign` (`rpta_class_id`);

--
-- Indexes for table `rpta_additional_items`
--
ALTER TABLE `rpta_additional_items`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rpta_assessment_levels`
--
ALTER TABLE `rpta_assessment_levels`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_assessment_level_per_use_rev` (`rpta_actual_use_id`,`revision_year_id`),
  ADD UNIQUE KEY `unique_assessment_lvl_range` (`rpta_actual_use_id`,`revision_year_id`,`min_value`,`max_value`),
  ADD KEY `rpta_assessment_levels_revision_year_id_foreign` (`revision_year_id`);

--
-- Indexes for table `rpta_assmnt_lvl`
--
ALTER TABLE `rpta_assmnt_lvl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rpta_bldg_types`
--
ALTER TABLE `rpta_bldg_types`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rpta_bldg_types_code_unique` (`code`);

--
-- Indexes for table `rpta_classes`
--
ALTER TABLE `rpta_classes`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rpta_classes_code_unique` (`code`);

--
-- Indexes for table `rpta_deprate_bldg`
--
ALTER TABLE `rpta_deprate_bldg`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rpta_other_improvement`
--
ALTER TABLE `rpta_other_improvement`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rpta_other_improvement_kind_name_index` (`kind_name`);

--
-- Indexes for table `rpta_revision_years`
--
ALTER TABLE `rpta_revision_years`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rpta_rev_yr`
--
ALTER TABLE `rpta_rev_yr`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rpta_settings`
--
ALTER TABLE `rpta_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rpta_settings_setting_key_unique` (`setting_key`);

--
-- Indexes for table `rpta_signatories`
--
ALTER TABLE `rpta_signatories`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rpta_unit_values`
--
ALTER TABLE `rpta_unit_values`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `unique_unit_value_per_loc_rev` (`rpta_actual_use_id`,`barangay_id`,`revision_year_id`),
  ADD KEY `rpta_unit_values_barangay_id_foreign` (`barangay_id`),
  ADD KEY `rpta_unit_values_revision_year_id_foreign` (`revision_year_id`);

--
-- Indexes for table `rpt_application_documents`
--
ALTER TABLE `rpt_application_documents`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rpt_application_documents_rpt_online_application_id_foreign` (`rpt_online_application_id`),
  ADD KEY `rpt_application_documents_verified_by_foreign` (`verified_by`);

--
-- Indexes for table `rpt_au_tbl`
--
ALTER TABLE `rpt_au_tbl`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rpt_au_value`
--
ALTER TABLE `rpt_au_value`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rpt_au_value_au_cat_assmt_kind_index` (`au_cat`,`assmt_kind`);

--
-- Indexes for table `rpt_billings`
--
ALTER TABLE `rpt_billings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rpt_billings_tax_declaration_id_foreign` (`tax_declaration_id`);

--
-- Indexes for table `rpt_location_classes`
--
ALTER TABLE `rpt_location_classes`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rpt_online_applications`
--
ALTER TABLE `rpt_online_applications`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rpt_online_applications_reference_no_unique` (`reference_no`),
  ADD KEY `rpt_online_applications_client_id_foreign` (`client_id`),
  ADD KEY `rpt_online_applications_barangay_id_foreign` (`barangay_id`),
  ADD KEY `rpt_online_applications_reviewed_by_foreign` (`reviewed_by`),
  ADD KEY `rpt_online_applications_faas_property_id_foreign` (`faas_property_id`);

--
-- Indexes for table `rpt_payments`
--
ALTER TABLE `rpt_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rpt_payments_or_no_unique` (`or_no`),
  ADD KEY `rpt_payments_rpt_billing_id_foreign` (`rpt_billing_id`),
  ADD KEY `rpt_payments_collected_by_foreign` (`collected_by`);

--
-- Indexes for table `rpt_property_registrations`
--
ALTER TABLE `rpt_property_registrations`
  ADD PRIMARY KEY (`id`),
  ADD KEY `rpt_property_registrations_barangay_id_foreign` (`barangay_id`),
  ADD KEY `rpt_property_registrations_created_by_foreign` (`created_by`),
  ADD KEY `rpt_property_registrations_parent_land_faas_id_foreign` (`parent_land_faas_id`);

--
-- Indexes for table `rpt_registration_attachments`
--
ALTER TABLE `rpt_registration_attachments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_rpt_reg_attach` (`rpt_property_registration_id`),
  ADD KEY `rpt_registration_attachments_uploaded_by_foreign` (`uploaded_by`);

--
-- Indexes for table `rpt_road_types`
--
ALTER TABLE `rpt_road_types`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `rpt_tc_tbl`
--
ALTER TABLE `rpt_tc_tbl`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `rpt_tc_tbl_tcode_unique` (`tcode`);

--
-- Indexes for table `salary_grades`
--
ALTER TABLE `salary_grades`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `salary_grades_grade_number_unique` (`grade_number`);

--
-- Indexes for table `sessions`
--
ALTER TABLE `sessions`
  ADD PRIMARY KEY (`id`),
  ADD KEY `sessions_user_id_index` (`user_id`),
  ADD KEY `sessions_last_activity_index` (`last_activity`);

--
-- Indexes for table `tax_declarations`
--
ALTER TABLE `tax_declarations`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `tax_declarations_td_no_unique` (`td_no`),
  ADD UNIQUE KEY `unique_td_component_year_status` (`faas_property_id`,`faas_land_id`,`faas_building_id`,`faas_machinery_id`,`effectivity_year`,`status`),
  ADD KEY `tax_declarations_revision_year_id_foreign` (`revision_year_id`),
  ADD KEY `tax_declarations_created_by_foreign` (`created_by`),
  ADD KEY `tax_declarations_approved_by_foreign` (`approved_by`),
  ADD KEY `tax_declarations_faas_land_id_foreign` (`faas_land_id`),
  ADD KEY `tax_declarations_faas_building_id_foreign` (`faas_building_id`),
  ADD KEY `tax_declarations_faas_machinery_id_foreign` (`faas_machinery_id`);

--
-- Indexes for table `td_activity_logs`
--
ALTER TABLE `td_activity_logs`
  ADD PRIMARY KEY (`id`),
  ADD KEY `td_activity_logs_user_id_foreign` (`user_id`),
  ADD KEY `td_activity_logs_tax_declaration_id_index` (`tax_declaration_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD KEY `users_employee_id_foreign` (`employee_id`),
  ADD KEY `users_encoded_by_foreign` (`encoded_by`);

--
-- Indexes for table `vf_collection_natures`
--
ALTER TABLE `vf_collection_natures`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vf_franchises`
--
ALTER TABLE `vf_franchises`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vf_franchises_fn_number_unique` (`fn_number`),
  ADD UNIQUE KEY `vf_franchises_permit_number_unique` (`permit_number`),
  ADD KEY `vf_franchises_owner_id_foreign` (`owner_id`),
  ADD KEY `vf_franchises_toda_id_foreign` (`toda_id`),
  ADD KEY `vf_franchises_encoded_by_foreign` (`encoded_by`);

--
-- Indexes for table `vf_franchise_history`
--
ALTER TABLE `vf_franchise_history`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vf_franchise_history_franchise_id_foreign` (`franchise_id`),
  ADD KEY `vf_franchise_history_performed_by_foreign` (`performed_by`);

--
-- Indexes for table `vf_owners`
--
ALTER TABLE `vf_owners`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vf_payments`
--
ALTER TABLE `vf_payments`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vf_payments_or_number_unique` (`or_number`),
  ADD KEY `vf_payments_franchise_id_foreign` (`franchise_id`),
  ADD KEY `vf_payments_collected_by_foreign` (`collected_by`);

--
-- Indexes for table `vf_settings`
--
ALTER TABLE `vf_settings`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `vf_settings_key_unique` (`key`);

--
-- Indexes for table `vf_settings_and_franchise_date`
--
ALTER TABLE `vf_settings_and_franchise_date`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vf_todas`
--
ALTER TABLE `vf_todas`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `vf_vehicles`
--
ALTER TABLE `vf_vehicles`
  ADD PRIMARY KEY (`id`),
  ADD KEY `vf_vehicles_franchise_id_foreign` (`franchise_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `applicants`
--
ALTER TABLE `applicants`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `applicant_documents`
--
ALTER TABLE `applicant_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `appointments`
--
ALTER TABLE `appointments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `audit_logs`
--
ALTER TABLE `audit_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=77;

--
-- AUTO_INCREMENT for table `barangays`
--
ALTER TABLE `barangays`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `bpls_activity_logs`
--
ALTER TABLE `bpls_activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=100;

--
-- AUTO_INCREMENT for table `bpls_application_ors`
--
ALTER TABLE `bpls_application_ors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `bpls_assessments`
--
ALTER TABLE `bpls_assessments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bpls_benefits`
--
ALTER TABLE `bpls_benefits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bpls_businesses`
--
ALTER TABLE `bpls_businesses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `bpls_business_entries`
--
ALTER TABLE `bpls_business_entries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `bpls_documents`
--
ALTER TABLE `bpls_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=63;

--
-- AUTO_INCREMENT for table `bpls_entry_benefits`
--
ALTER TABLE `bpls_entry_benefits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `bpls_online_applications`
--
ALTER TABLE `bpls_online_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `bpls_online_payments`
--
ALTER TABLE `bpls_online_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `bpls_owners`
--
ALTER TABLE `bpls_owners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=25;

--
-- AUTO_INCREMENT for table `bpls_owner_benefits`
--
ALTER TABLE `bpls_owner_benefits`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `bpls_payments`
--
ALTER TABLE `bpls_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `bpls_permit_signatories`
--
ALTER TABLE `bpls_permit_signatories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `bpls_settings`
--
ALTER TABLE `bpls_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `clients`
--
ALTER TABLE `clients`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `client_linked_properties`
--
ALTER TABLE `client_linked_properties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `defaultz`
--
ALTER TABLE `defaultz`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `departments`
--
ALTER TABLE `departments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `divisions`
--
ALTER TABLE `divisions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_civil_service`
--
ALTER TABLE `employee_civil_service`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_documents`
--
ALTER TABLE `employee_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_education`
--
ALTER TABLE `employee_education`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_family_background`
--
ALTER TABLE `employee_family_background`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_government_ids`
--
ALTER TABLE `employee_government_ids`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_info`
--
ALTER TABLE `employee_info`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `employee_trainings`
--
ALTER TABLE `employee_trainings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employee_work_experience`
--
ALTER TABLE `employee_work_experience`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `employment_types`
--
ALTER TABLE `employment_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faas_activity_logs`
--
ALTER TABLE `faas_activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=51;

--
-- AUTO_INCREMENT for table `faas_attachments`
--
ALTER TABLE `faas_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `faas_buildings`
--
ALTER TABLE `faas_buildings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faas_building_improvements`
--
ALTER TABLE `faas_building_improvements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `faas_gen_rev`
--
ALTER TABLE `faas_gen_rev`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=53;

--
-- AUTO_INCREMENT for table `faas_gen_rev_geometries`
--
ALTER TABLE `faas_gen_rev_geometries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `faas_lands`
--
ALTER TABLE `faas_lands`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;

--
-- AUTO_INCREMENT for table `faas_land_improvements`
--
ALTER TABLE `faas_land_improvements`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `faas_machineries`
--
ALTER TABLE `faas_machineries`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `faas_machines`
--
ALTER TABLE `faas_machines`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `faas_owners`
--
ALTER TABLE `faas_owners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `faas_predecessors`
--
ALTER TABLE `faas_predecessors`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `faas_properties`
--
ALTER TABLE `faas_properties`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=22;

--
-- AUTO_INCREMENT for table `faas_revision_logs`
--
ALTER TABLE `faas_revision_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=41;

--
-- AUTO_INCREMENT for table `faas_rpta_audit`
--
ALTER TABLE `faas_rpta_audit`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- AUTO_INCREMENT for table `faas_rpta_owner_select`
--
ALTER TABLE `faas_rpta_owner_select`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `failed_jobs`
--
ALTER TABLE `failed_jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `fee_rules`
--
ALTER TABLE `fee_rules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `form_options`
--
ALTER TABLE `form_options`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=52;

--
-- AUTO_INCREMENT for table `hr_daily_time_records`
--
ALTER TABLE `hr_daily_time_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_deduction_types`
--
ALTER TABLE `hr_deduction_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_employee_deductions`
--
ALTER TABLE `hr_employee_deductions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_employee_schedules`
--
ALTER TABLE `hr_employee_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_leave_applications`
--
ALTER TABLE `hr_leave_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_leave_balances`
--
ALTER TABLE `hr_leave_balances`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_leave_types`
--
ALTER TABLE `hr_leave_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_payroll_periods`
--
ALTER TABLE `hr_payroll_periods`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_payroll_records`
--
ALTER TABLE `hr_payroll_records`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_plantilla_positions`
--
ALTER TABLE `hr_plantilla_positions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_salary_grades`
--
ALTER TABLE `hr_salary_grades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `hr_time_logs`
--
ALTER TABLE `hr_time_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `hr_work_schedules`
--
ALTER TABLE `hr_work_schedules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `interviews`
--
ALTER TABLE `interviews`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `jobs`
--
ALTER TABLE `jobs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_positions`
--
ALTER TABLE `job_positions`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `job_vacancies`
--
ALTER TABLE `job_vacancies`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `machinery_valuations`
--
ALTER TABLE `machinery_valuations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `migrations`
--
ALTER TABLE `migrations`
  MODIFY `id` int(10) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=201;

--
-- AUTO_INCREMENT for table `modules`
--
ALTER TABLE `modules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `offices`
--
ALTER TABLE `offices`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `or_assignments`
--
ALTER TABLE `or_assignments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `plantilla`
--
ALTER TABLE `plantilla`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `roles`
--
ALTER TABLE `roles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `role_module`
--
ALTER TABLE `role_module`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `role_user`
--
ALTER TABLE `role_user`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rpta_actual_uses`
--
ALTER TABLE `rpta_actual_uses`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rpta_additional_items`
--
ALTER TABLE `rpta_additional_items`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rpta_assessment_levels`
--
ALTER TABLE `rpta_assessment_levels`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rpta_assmnt_lvl`
--
ALTER TABLE `rpta_assmnt_lvl`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `rpta_bldg_types`
--
ALTER TABLE `rpta_bldg_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rpta_classes`
--
ALTER TABLE `rpta_classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `rpta_deprate_bldg`
--
ALTER TABLE `rpta_deprate_bldg`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rpta_other_improvement`
--
ALTER TABLE `rpta_other_improvement`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rpta_revision_years`
--
ALTER TABLE `rpta_revision_years`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rpta_rev_yr`
--
ALTER TABLE `rpta_rev_yr`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rpta_settings`
--
ALTER TABLE `rpta_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `rpta_signatories`
--
ALTER TABLE `rpta_signatories`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rpta_unit_values`
--
ALTER TABLE `rpta_unit_values`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rpt_application_documents`
--
ALTER TABLE `rpt_application_documents`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rpt_au_tbl`
--
ALTER TABLE `rpt_au_tbl`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rpt_au_value`
--
ALTER TABLE `rpt_au_value`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `rpt_billings`
--
ALTER TABLE `rpt_billings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;

--
-- AUTO_INCREMENT for table `rpt_location_classes`
--
ALTER TABLE `rpt_location_classes`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `rpt_online_applications`
--
ALTER TABLE `rpt_online_applications`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `rpt_payments`
--
ALTER TABLE `rpt_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT for table `rpt_property_registrations`
--
ALTER TABLE `rpt_property_registrations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `rpt_registration_attachments`
--
ALTER TABLE `rpt_registration_attachments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `rpt_road_types`
--
ALTER TABLE `rpt_road_types`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `rpt_tc_tbl`
--
ALTER TABLE `rpt_tc_tbl`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `salary_grades`
--
ALTER TABLE `salary_grades`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `tax_declarations`
--
ALTER TABLE `tax_declarations`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `td_activity_logs`
--
ALTER TABLE `td_activity_logs`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `vf_collection_natures`
--
ALTER TABLE `vf_collection_natures`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `vf_franchises`
--
ALTER TABLE `vf_franchises`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `vf_franchise_history`
--
ALTER TABLE `vf_franchise_history`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT for table `vf_owners`
--
ALTER TABLE `vf_owners`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vf_payments`
--
ALTER TABLE `vf_payments`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `vf_settings`
--
ALTER TABLE `vf_settings`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT for table `vf_settings_and_franchise_date`
--
ALTER TABLE `vf_settings_and_franchise_date`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `vf_todas`
--
ALTER TABLE `vf_todas`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vf_vehicles`
--
ALTER TABLE `vf_vehicles`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `applicants`
--
ALTER TABLE `applicants`
  ADD CONSTRAINT `applicants_job_vacancy_id_foreign` FOREIGN KEY (`job_vacancy_id`) REFERENCES `job_vacancies` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `applicant_documents`
--
ALTER TABLE `applicant_documents`
  ADD CONSTRAINT `applicant_documents_applicant_id_foreign` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `appointments`
--
ALTER TABLE `appointments`
  ADD CONSTRAINT `appointments_applicant_id_foreign` FOREIGN KEY (`applicant_id`) REFERENCES `applicants` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `appointments_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `appointments_employment_type_id_foreign` FOREIGN KEY (`employment_type_id`) REFERENCES `employment_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_office_id_foreign` FOREIGN KEY (`office_id`) REFERENCES `offices` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `appointments_plantilla_id_foreign` FOREIGN KEY (`plantilla_id`) REFERENCES `plantilla` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `appointments_salary_grade_id_foreign` FOREIGN KEY (`salary_grade_id`) REFERENCES `salary_grades` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `audit_logs`
--
ALTER TABLE `audit_logs`
  ADD CONSTRAINT `audit_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bpls_activity_logs`
--
ALTER TABLE `bpls_activity_logs`
  ADD CONSTRAINT `bpls_activity_logs_bpls_application_id_foreign` FOREIGN KEY (`bpls_application_id`) REFERENCES `bpls_online_applications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bpls_application_ors`
--
ALTER TABLE `bpls_application_ors`
  ADD CONSTRAINT `bpls_application_ors_bpls_application_id_foreign` FOREIGN KEY (`bpls_application_id`) REFERENCES `bpls_online_applications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bpls_application_ors_or_assignment_id_foreign` FOREIGN KEY (`or_assignment_id`) REFERENCES `or_assignments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bpls_assessments`
--
ALTER TABLE `bpls_assessments`
  ADD CONSTRAINT `bpls_assessments_assessed_by_foreign` FOREIGN KEY (`assessed_by`) REFERENCES `users` (`id`),
  ADD CONSTRAINT `bpls_assessments_bpls_application_id_foreign` FOREIGN KEY (`bpls_application_id`) REFERENCES `bpls_online_applications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bpls_documents`
--
ALTER TABLE `bpls_documents`
  ADD CONSTRAINT `bpls_documents_bpls_application_id_foreign` FOREIGN KEY (`bpls_application_id`) REFERENCES `bpls_online_applications` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bpls_entry_benefits`
--
ALTER TABLE `bpls_entry_benefits`
  ADD CONSTRAINT `bpls_entry_benefits_benefit_id_foreign` FOREIGN KEY (`benefit_id`) REFERENCES `bpls_benefits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bpls_entry_benefits_business_entry_id_foreign` FOREIGN KEY (`business_entry_id`) REFERENCES `bpls_business_entries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bpls_online_applications`
--
ALTER TABLE `bpls_online_applications`
  ADD CONSTRAINT `bpls_applications_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bpls_applications_assessed_by_foreign` FOREIGN KEY (`assessed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bpls_applications_bpls_business_id_foreign` FOREIGN KEY (`bpls_business_id`) REFERENCES `bpls_businesses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bpls_applications_bpls_owner_id_foreign` FOREIGN KEY (`bpls_owner_id`) REFERENCES `bpls_owners` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `bpls_applications_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bpls_applications_verified_by_foreign` FOREIGN KEY (`verified_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `bpls_online_payments`
--
ALTER TABLE `bpls_online_payments`
  ADD CONSTRAINT `bpls_online_payments_bpls_application_id_foreign` FOREIGN KEY (`bpls_application_id`) REFERENCES `bpls_online_applications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bpls_online_payments_bpls_assessment_id_foreign` FOREIGN KEY (`bpls_assessment_id`) REFERENCES `bpls_assessments` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bpls_owner_benefits`
--
ALTER TABLE `bpls_owner_benefits`
  ADD CONSTRAINT `bpls_owner_benefits_benefit_id_foreign` FOREIGN KEY (`benefit_id`) REFERENCES `bpls_benefits` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bpls_owner_benefits_owner_id_foreign` FOREIGN KEY (`owner_id`) REFERENCES `bpls_owners` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `bpls_payments`
--
ALTER TABLE `bpls_payments`
  ADD CONSTRAINT `bpls_payments_bpls_application_id_foreign` FOREIGN KEY (`bpls_application_id`) REFERENCES `bpls_online_applications` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `bpls_payments_business_entry_id_foreign` FOREIGN KEY (`business_entry_id`) REFERENCES `bpls_business_entries` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `client_linked_properties`
--
ALTER TABLE `client_linked_properties`
  ADD CONSTRAINT `client_linked_properties_client_id_foreign` FOREIGN KEY (`client_id`) REFERENCES `clients` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `client_linked_properties_tax_declaration_id_foreign` FOREIGN KEY (`tax_declaration_id`) REFERENCES `tax_declarations` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `divisions`
--
ALTER TABLE `divisions`
  ADD CONSTRAINT `divisions_office_id_foreign` FOREIGN KEY (`office_id`) REFERENCES `offices` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `employee_info`
--
ALTER TABLE `employee_info`
  ADD CONSTRAINT `employee_info_department_id_foreign` FOREIGN KEY (`department_id`) REFERENCES `departments` (`id`),
  ADD CONSTRAINT `employee_info_plantilla_position_id_foreign` FOREIGN KEY (`plantilla_position_id`) REFERENCES `hr_plantilla_positions` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `faas_activity_logs`
--
ALTER TABLE `faas_activity_logs`
  ADD CONSTRAINT `faas_activity_logs_faas_property_id_foreign` FOREIGN KEY (`faas_property_id`) REFERENCES `faas_properties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `faas_activity_logs_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `faas_attachments`
--
ALTER TABLE `faas_attachments`
  ADD CONSTRAINT `faas_attachments_faas_property_id_foreign` FOREIGN KEY (`faas_property_id`) REFERENCES `faas_properties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `faas_attachments_uploaded_by_foreign` FOREIGN KEY (`uploaded_by`) REFERENCES `users` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `faas_buildings`
--
ALTER TABLE `faas_buildings`
  ADD CONSTRAINT `faas_buildings_faas_land_id_foreign` FOREIGN KEY (`faas_land_id`) REFERENCES `faas_lands` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `faas_buildings_faas_property_id_foreign` FOREIGN KEY (`faas_property_id`) REFERENCES `faas_properties` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `faas_buildings_rpta_actual_use_id_foreign` FOREIGN KEY (`rpta_actual_use_id`) REFERENCES `rpta_actual_uses` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `faas_buildings_rpta_bldg_type_id_foreign` FOREIGN KEY (`rpta_bldg_type_id`) REFERENCES `rpta_bldg_types` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `faas_building_improvements`
--
ALTER TABLE `faas_building_improvements`
  ADD CONSTRAINT `faas_building_improvements_building_id_foreign` FOREIGN KEY (`building_id`) REFERENCES `faas_buildings` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `faas_building_improvements_improvement_id_foreign` FOREIGN KEY (`improvement_id`) REFERENCES `rpta_other_improvement` (`id`);

--
-- Constraints for table `faas_gen_rev_geometries`
--
ALTER TABLE `faas_gen_rev_geometries`
  ADD CONSTRAINT `faas_gen_rev_geometries_faas_id_foreign` FOREIGN KEY (`faas_id`) REFERENCES `faas_gen_rev` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `faas_properties`
--
ALTER TABLE `faas_properties`
  ADD CONSTRAINT `faas_properties_parent_land_faas_id_foreign` FOREIGN KEY (`parent_land_faas_id`) REFERENCES `faas_properties` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `hr_daily_time_records`
--
ALTER TABLE `hr_daily_time_records`
  ADD CONSTRAINT `hr_daily_time_records_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee_info` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_employee_deductions`
--
ALTER TABLE `hr_employee_deductions`
  ADD CONSTRAINT `hr_employee_deductions_deduction_type_id_foreign` FOREIGN KEY (`deduction_type_id`) REFERENCES `hr_deduction_types` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_employee_deductions_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee_info` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_employee_schedules`
--
ALTER TABLE `hr_employee_schedules`
  ADD CONSTRAINT `hr_employee_schedules_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee_info` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_employee_schedules_schedule_id_foreign` FOREIGN KEY (`schedule_id`) REFERENCES `hr_work_schedules` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_leave_applications`
--
ALTER TABLE `hr_leave_applications`
  ADD CONSTRAINT `hr_leave_applications_approved_by_foreign` FOREIGN KEY (`approved_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_leave_applications_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee_info` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_leave_applications_filed_by_foreign` FOREIGN KEY (`filed_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `hr_leave_applications_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `hr_leave_types` (`id`);

--
-- Constraints for table `hr_leave_balances`
--
ALTER TABLE `hr_leave_balances`
  ADD CONSTRAINT `hr_leave_balances_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee_info` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_leave_balances_leave_type_id_foreign` FOREIGN KEY (`leave_type_id`) REFERENCES `hr_leave_types` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_payroll_periods`
--
ALTER TABLE `hr_payroll_periods`
  ADD CONSTRAINT `hr_payroll_periods_created_by_foreign` FOREIGN KEY (`created_by`) REFERENCES `users` (`id`);

--
-- Constraints for table `hr_payroll_records`
--
ALTER TABLE `hr_payroll_records`
  ADD CONSTRAINT `hr_payroll_records_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee_info` (`id`) ON DELETE CASCADE,
  ADD CONSTRAINT `hr_payroll_records_payroll_period_id_foreign` FOREIGN KEY (`payroll_period_id`) REFERENCES `hr_payroll_periods` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `hr_time_logs`
--
ALTER TABLE `hr_time_logs`
  ADD CONSTRAINT `hr_time_logs_employee_id_foreign` FOREIGN KEY (`employee_id`) REFERENCES `employee_info` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `rpt_property_registrations`
--
ALTER TABLE `rpt_property_registrations`
  ADD CONSTRAINT `rpt_property_registrations_parent_land_faas_id_foreign` FOREIGN KEY (`parent_land_faas_id`) REFERENCES `faas_properties` (`id`) ON DELETE SET NULL;

--
-- Constraints for table `vf_payments`
--
ALTER TABLE `vf_payments`
  ADD CONSTRAINT `vf_payments_collected_by_foreign` FOREIGN KEY (`collected_by`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  ADD CONSTRAINT `vf_payments_franchise_id_foreign` FOREIGN KEY (`franchise_id`) REFERENCES `vf_franchises` (`id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
