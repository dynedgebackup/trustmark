-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jul 15, 2025 at 03:37 PM
-- Server version: 10.4.18-MariaDB
-- PHP Version: 8.2.12

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
-- Table structure for table `menu_modules`
--

CREATE TABLE `menu_modules` (
  `id` bigint(20) UNSIGNED NOT NULL,
  `menu_group_id` int(10) UNSIGNED NOT NULL,
  `code` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(40) COLLATE utf8mb4_unicode_ci NOT NULL,
  `description` text COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `icon` varchar(40) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `slug` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `order` int(10) UNSIGNED DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `created_by` int(10) UNSIGNED NOT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `updated_by` int(10) UNSIGNED DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Dumping data for table `menu_modules`
--

INSERT INTO `menu_modules` (`id`, `menu_group_id`, `code`, `name`, `description`, `icon`, `slug`, `order`, `created_at`, `created_by`, `updated_at`, `updated_by`, `is_active`) VALUES
(1, 3, 'Requirements', 'Requirements', 'Authorized Representative', NULL, 'authorized', NULL, '2025-07-15 16:04:26', 2, '2025-07-15 16:05:53', 2, 1),
(2, 3, 'Application Status', 'Application Status', 'Canned Message', NULL, 'master-data/ApplicationStatusCannedMessage', NULL, '2025-07-15 16:06:43', 2, '2025-07-15 16:06:43', 2, 1);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `menu_modules`
--
ALTER TABLE `menu_modules`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `menu_modules`
--
ALTER TABLE `menu_modules`
  MODIFY `id` bigint(20) UNSIGNED NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
