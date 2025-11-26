-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 12, 2025 at 04:32 AM
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
-- Table structure for table `setting_documents`
--

CREATE TABLE `setting_documents` (
  `id` bigint(11) NOT NULL,
  `name` text DEFAULT NULL,
  `var_name` text DEFAULT NULL,
  `path_url` text NOT NULL,
  `created_by` bigint(20) NOT NULL COMMENT 'Ref-Table: users.id',
  `created_date` datetime DEFAULT NULL,
  `modified_by` bigint(20) DEFAULT NULL,
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `setting_documents`
--

INSERT INTO `setting_documents` (`id`, `name`, `var_name`, `path_url`, `created_by`, `created_date`, `modified_by`, `modified_date`) VALUES
(1, 'Internal Redress Mechanism', 'internal_redress_mechanism', '', 1, '2025-07-12 04:30:58', 1, '2025-07-12 04:30:58');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `setting_documents`
--
ALTER TABLE `setting_documents`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `setting_documents`
--
ALTER TABLE `setting_documents`
  MODIFY `id` bigint(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
