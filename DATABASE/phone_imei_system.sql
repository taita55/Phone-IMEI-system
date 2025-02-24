-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Feb 24, 2025 at 01:11 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.0.30

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `phone_imei_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `password_resets`
--

CREATE TABLE `password_resets` (
  `id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `token` varchar(255) NOT NULL,
  `expires_at` datetime NOT NULL,
  `created_at` datetime DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `password_resets`
--

INSERT INTO `password_resets` (`id`, `user_id`, `token`, `expires_at`, `created_at`) VALUES
(2, 4, '6d6572de87884dda602f5ee4077b19ed', '2025-02-24 13:58:03', '2025-02-24 13:58:03');

-- --------------------------------------------------------

--
-- Table structure for table `phones`
--

CREATE TABLE `phones` (
  `phone_id` int(11) NOT NULL,
  `imei` varchar(50) NOT NULL,
  `vendor_id` int(11) DEFAULT NULL,
  `customer_id` int(11) DEFAULT NULL,
  `status` varchar(50) DEFAULT 'in_stock',
  `sim_activated` tinyint(1) DEFAULT 0,
  `date_inserted` datetime DEFAULT current_timestamp(),
  `date_sold` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `phones`
--

INSERT INTO `phones` (`phone_id`, `imei`, `vendor_id`, `customer_id`, `status`, `sim_activated`, `date_inserted`, `date_sold`) VALUES
(1, '356663764377151', 1, 2, 'blocked', 1, '2025-02-21 22:22:32', '2025-02-21 22:28:08');

-- --------------------------------------------------------

--
-- Table structure for table `theft_reports`
--

CREATE TABLE `theft_reports` (
  `report_id` int(11) NOT NULL,
  `phone_id` int(11) NOT NULL,
  `reported_by` int(11) NOT NULL,
  `report_date` datetime DEFAULT current_timestamp(),
  `approved_by_law` tinyint(1) DEFAULT 0,
  `law_approval_date` datetime DEFAULT NULL,
  `blocked_by_telecom` tinyint(1) DEFAULT 0,
  `block_date` datetime DEFAULT NULL,
  `unblocked_by_telecom` tinyint(1) DEFAULT 0,
  `unblock_date` datetime DEFAULT NULL,
  `notes` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `theft_reports`
--

INSERT INTO `theft_reports` (`report_id`, `phone_id`, `reported_by`, `report_date`, `approved_by_law`, `law_approval_date`, `blocked_by_telecom`, `block_date`, `unblocked_by_telecom`, `unblock_date`, `notes`) VALUES
(1, 1, 2, '2025-02-21 22:38:40', 1, '2025-02-21 22:42:11', 1, '2025-02-21 22:43:31', 0, NULL, NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `phone` varchar(50) DEFAULT NULL,
  `role` varchar(50) NOT NULL,
  `rdb_certificate` varchar(255) DEFAULT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` datetime DEFAULT current_timestamp(),
  `updated_at` datetime DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `name`, `phone`, `role`, `rdb_certificate`, `username`, `password`, `created_at`, `updated_at`) VALUES
(1, 'Aime', '0788200887', 'vendor', '12345678', 'Ladder Shop', '$2y$10$bJL47/GEBY0L5pxf7CNMIeeWxUyfkkmDgKnFcAXlcAohEFCs.rJdG', '2025-02-21 22:20:55', '2025-02-21 22:20:55'),
(2, 'Sam', '0784141832', 'customer', '', 'Sammeiz', '$2y$10$YCbEZQLU.v4Z8krZRcdq1Og6.XwuDJ5Hqdr9supc8IIxVYIeCSJtW', '2025-02-21 22:26:21', '2025-02-21 22:26:21'),
(3, 'Danny', '0788000003', 'law_enforcement', '', 'kdanny', '$2y$10$hUx8tTUlRVmXAiuHGEpx6uW60KcV3Fx4qnqC8CqU2vdkWPibiZQsm', '2025-02-21 22:35:15', '2025-02-21 22:35:15'),
(4, 'Moise', '0788000001', 'telecom', '', 'mozilla', '$2y$10$p3QQlX23fS3u7Qha849eoOB6tfrwTdWwmywekZtLTVZ7yP.Xgztaa', '2025-02-21 22:35:59', '2025-02-22 22:57:30'),
(5, 'emelyne', '0788000002', 'regulatory', '', 'ruby', '$2y$10$tC7Glek8dNtLvtTz/chl5edHHB8QS.EqsX5o6PFe3Y2YQ0kG12HvC', '2025-02-21 22:36:52', '2025-02-21 22:36:52');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `phones`
--
ALTER TABLE `phones`
  ADD PRIMARY KEY (`phone_id`),
  ADD UNIQUE KEY `imei` (`imei`),
  ADD KEY `vendor_id` (`vendor_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `theft_reports`
--
ALTER TABLE `theft_reports`
  ADD PRIMARY KEY (`report_id`),
  ADD KEY `phone_id` (`phone_id`),
  ADD KEY `reported_by` (`reported_by`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `password_resets`
--
ALTER TABLE `password_resets`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `phones`
--
ALTER TABLE `phones`
  MODIFY `phone_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `theft_reports`
--
ALTER TABLE `theft_reports`
  MODIFY `report_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `password_resets`
--
ALTER TABLE `password_resets`
  ADD CONSTRAINT `password_resets_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;

--
-- Constraints for table `phones`
--
ALTER TABLE `phones`
  ADD CONSTRAINT `phones_ibfk_1` FOREIGN KEY (`vendor_id`) REFERENCES `users` (`user_id`),
  ADD CONSTRAINT `phones_ibfk_2` FOREIGN KEY (`customer_id`) REFERENCES `users` (`user_id`);

--
-- Constraints for table `theft_reports`
--
ALTER TABLE `theft_reports`
  ADD CONSTRAINT `theft_reports_ibfk_1` FOREIGN KEY (`phone_id`) REFERENCES `phones` (`phone_id`),
  ADD CONSTRAINT `theft_reports_ibfk_2` FOREIGN KEY (`reported_by`) REFERENCES `users` (`user_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
