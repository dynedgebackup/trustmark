-- phpMyAdmin SQL Dump
-- version 5.1.1deb5ubuntu1
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jul 08, 2025 at 04:36 AM
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
-- Table structure for table `application_canned_messages`
--

CREATE TABLE `application_canned_messages` (
  `id` int(20) NOT NULL,
  `app_status_id` bigint(20) DEFAULT NULL COMMENT 'Ref-Table:application_status.id',
  `description` text DEFAULT NULL,
  `status` int(1) DEFAULT NULL COMMENT '0=Cancelled,1=Active',
  `created_by` int(20) DEFAULT NULL COMMENT 'Ref-Table: user.id',
  `created_date` datetime DEFAULT NULL,
  `modified_by` int(11) DEFAULT NULL COMMENT 'Ref-Table: users.id',
  `modified_date` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `application_canned_messages`
--
ALTER TABLE `application_canned_messages`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `application_canned_messages`
--
ALTER TABLE `application_canned_messages`
  MODIFY `id` int(20) NOT NULL AUTO_INCREMENT;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
