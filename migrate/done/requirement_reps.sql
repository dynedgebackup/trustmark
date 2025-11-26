-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 05, 2025 at 05:51 AM
-- Server version: 10.6.22-MariaDB-ubu2204
-- PHP Version: 8.2.28

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `trustmark`
--

-- --------------------------------------------------------

--
-- Table structure for table `requirement_reps`
--

CREATE TABLE `requirement_reps` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `description` varchar(255) DEFAULT NULL,
  `with_expiration` int(1) NOT NULL DEFAULT 0 COMMENT '0=no expiration, 1 with expiration',
  `status` varchar(255) DEFAULT NULL,
  `created_by` int(11) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(11) DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `requirement_reps`
--

INSERT INTO `requirement_reps` (`id`, `description`, `with_expiration`, `status`, `created_by`, `created_at`, `updated_by`, `updated_at`) VALUES
(1, 'National ID (PhilSys)', 0, 'Active', 2, '2025-07-01 19:02:37', 22, '2025-07-03 09:12:47'),
(2, 'Valid Passport', 0, 'Active', 2, '2025-07-01 19:03:01', 22, '2025-07-03 09:13:33'),
(3, 'Driver\'s License', 0, 'Active', 2, '2025-07-01 19:39:49', 22, '2025-07-03 09:14:11'),
(4, 'Professional Regulations Commission (PRC) ID', 0, 'Active', 2, '2025-07-01 19:40:07', 22, '2025-07-03 09:14:54'),
(5, 'PSA issued Birth Certificate', 0, 'Active', 2, '2025-07-01 19:40:19', 22, '2025-07-03 09:14:40'),
(6, 'National Bureau of Investigation (NBI) Clearance', 0, 'Active', 2, '2025-07-01 19:40:43', 22, '2025-07-03 09:15:30'),
(7, 'Philippine National Police (PNP) ID/Police Clearance', 0, 'Active', 22, '2025-07-02 09:15:12', 22, '2025-07-03 09:15:42'),
(8, 'Postal ID issued by Philippine Postal Corporation (PhilPost)', 0, 'Active', 22, '2025-07-03 09:15:56', NULL, '2025-07-03 09:15:56'),
(9, 'Voter’s ID issued by the Commission on Elections (COMELEC)', 0, 'Active', 22, '2025-07-03 09:16:14', NULL, '2025-07-03 09:16:14'),
(10, 'Government Service Insurance System (GSIS) Unified Multi-Purpose ID/eCard', 0, 'Active', 22, '2025-07-03 09:16:30', NULL, '2025-07-03 09:16:30'),
(11, 'Social Security System (SSS) Unified Multi-Purpose ID', 0, 'Active', 22, '2025-07-03 09:16:41', NULL, '2025-07-03 09:16:41'),
(12, 'Seaman’s/Seawoman’s Book issued by the Maritime Industry Authority (MARINA)', 0, 'Active', 22, '2025-07-03 09:16:54', 22, '2025-07-03 09:17:09'),
(13, 'Senior Citizen’s ID issued by the Office of Senior Citizens Affairs (OSCA) and/or local government units (LGUs)', 0, 'Active', 22, '2025-07-03 09:17:24', NULL, '2025-07-03 09:17:24'),
(14, 'Person with Disability (PWD) ID issued by the National Council on Disability Affairs (NCDA) or its Regional, City, Municipal, and Barangay counterpart', 0, 'Active', 22, '2025-07-03 09:19:46', NULL, '2025-07-03 09:19:46'),
(15, 'Philippine Health Insurance Corporation (Philhealth) ID', 0, 'Active', 22, '2025-07-03 09:20:04', NULL, '2025-07-03 09:20:04'),
(16, 'Home Development Mutual Fund (Pag-IBIG) Loyalty Card', 0, 'Active', 22, '2025-07-03 09:20:12', NULL, '2025-07-03 09:20:12'),
(17, 'OFW ID issued by the Department of Labor and Employment (DOLE)', 0, 'Active', 22, '2025-07-03 09:20:21', NULL, '2025-07-03 09:20:21'),
(18, 'Overseas Workers Welfare Administration (OWWA) ID', 0, 'Active', 22, '2025-07-03 09:20:52', NULL, '2025-07-03 09:20:52'),
(19, '4Ps ID issued by the DSWD', 0, 'Active', 22, '2025-07-03 09:21:24', NULL, '2025-07-03 09:21:24'),
(20, 'Barangay ID/Certification with picture and signature', 0, 'Active', 22, '2025-07-03 09:21:34', NULL, '2025-07-03 09:21:34');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `requirement_reps`
--
ALTER TABLE `requirement_reps`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `requirement_reps`
--
ALTER TABLE `requirement_reps`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
