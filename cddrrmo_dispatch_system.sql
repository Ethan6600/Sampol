-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Oct 09, 2025 at 07:32 AM
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
-- Database: `cddrrmo_dispatch_system`
--

-- --------------------------------------------------------

--
-- Table structure for table `dispatches`
--

CREATE TABLE `dispatches` (
  `dispatch_id` int(11) NOT NULL,
  `incident_id` int(11) NOT NULL,
  `vehicle_id` int(11) DEFAULT NULL,
  `responder_id` int(11) DEFAULT NULL,
  `dispatch_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('assigned','en_route','on_scene','returning','completed','cancelled') NOT NULL DEFAULT 'assigned',
  `estimated_minutes` int(11) DEFAULT NULL,
  `actual_minutes` int(11) DEFAULT NULL,
  `cost` decimal(10,2) DEFAULT NULL,
  `trip_ticket_status` enum('pending','approved','rejected') NOT NULL DEFAULT 'pending',
  `approved_by_mayor_at` timestamp NULL DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `dispatches`
--

INSERT INTO `dispatches` (`dispatch_id`, `incident_id`, `vehicle_id`, `responder_id`, `dispatch_time`, `status`, `estimated_minutes`, `actual_minutes`, `cost`, `trip_ticket_status`, `approved_by_mayor_at`, `notes`, `last_updated`) VALUES
(1, 1, 4, 1, '2025-08-18 07:20:00', 'en_route', 30, 25, 420.00, 'pending', NULL, 'Hurry up', '2025-08-18 07:23:21');

-- --------------------------------------------------------

--
-- Table structure for table `dispatch_responders`
--

CREATE TABLE `dispatch_responders` (
  `dispatch_id` int(11) NOT NULL,
  `responder_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `dispatch_vehicles`
--

CREATE TABLE `dispatch_vehicles` (
  `dispatch_id` int(11) NOT NULL,
  `vehicle_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `incidents`
--

CREATE TABLE `incidents` (
  `incident_id` int(11) NOT NULL,
  `incident_number` varchar(100) NOT NULL,
  `incident_type` enum('fire','flood','medical','landslide','earthquake','rescue','other') NOT NULL,
  `description` text NOT NULL,
  `location` varchar(255) NOT NULL,
  `reported_by` varchar(100) NOT NULL DEFAULT 'Walk-in',
  `reported_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `status` enum('pending','active','resolved','cancelled') NOT NULL DEFAULT 'pending',
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `incidents`
--

INSERT INTO `incidents` (`incident_id`, `incident_number`, `incident_type`, `description`, `location`, `reported_by`, `reported_at`, `status`, `last_updated`) VALUES
(1, '002', 'landslide', 'Need help!', 'Barangay Ichon', 'Walk-in', '2025-08-18 07:03:00', 'active', '2025-08-18 07:04:26');

-- --------------------------------------------------------

--
-- Table structure for table `login_history`
--

CREATE TABLE `login_history` (
  `history_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `role` enum('admin','dispatcher','responder') NOT NULL,
  `login_time` timestamp NOT NULL DEFAULT current_timestamp(),
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` varchar(255) DEFAULT NULL,
  `browser` varchar(50) DEFAULT NULL,
  `device` varchar(50) DEFAULT NULL,
  `login_status` enum('success','failed') NOT NULL DEFAULT 'success'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `login_history`
--

INSERT INTO `login_history` (`history_id`, `user_id`, `username`, `role`, `login_time`, `ip_address`, `user_agent`, `browser`, `device`, `login_status`) VALUES
(1, 3, 'responder', 'responder', '2025-10-09 05:25:28', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'Chrome', 'Desktop', 'success'),
(2, 1, 'admin', 'admin', '2025-10-09 05:26:11', '::1', 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/141.0.0.0 Safari/537.36', 'Chrome', 'Desktop', 'success');

-- --------------------------------------------------------

--
-- Table structure for table `responders`
--

CREATE TABLE `responders` (
  `responder_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `contact_number` varchar(30) DEFAULT NULL,
  `status` enum('available','on_duty','off_duty') NOT NULL DEFAULT 'available',
  `specialty` varchar(100) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `responders`
--

INSERT INTO `responders` (`responder_id`, `full_name`, `contact_number`, `status`, `specialty`, `last_updated`) VALUES
(1, 'Juan Dela Cruz', '09171234567', 'available', 'Fire Fighting', '2025-08-18 02:52:02'),
(2, 'Maria Santos', '09281234567', 'available', 'Medical Response', '2025-08-18 02:52:02'),
(3, 'Pedro Garcia', '09391234567', 'available', 'Search and Rescue', '2025-08-18 02:52:02');

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `user_id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','dispatcher','responder') NOT NULL DEFAULT 'responder',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `last_login` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`user_id`, `username`, `password`, `role`, `created_at`, `last_login`) VALUES
(1, 'admin', 'admin123', 'admin', '2025-08-18 02:56:50', '2025-10-09 05:26:11'),
(2, 'dispatcher', 'password', 'dispatcher', '2025-08-18 02:56:50', NULL),
(3, 'responder', 'password', 'responder', '2025-08-18 02:56:50', '2025-10-09 05:25:28');

-- --------------------------------------------------------

--
-- Table structure for table `vehicles`
--

CREATE TABLE `vehicles` (
  `vehicle_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `plate_number` varchar(50) NOT NULL,
  `vehicle_type` enum('fire_truck','ambulance','rescue_vehicle','water_tanker','other') NOT NULL,
  `status` enum('available','dispatched','maintenance','out_of_service') NOT NULL DEFAULT 'available',
  `current_location` varchar(255) DEFAULT NULL,
  `last_updated` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `vehicles`
--

INSERT INTO `vehicles` (`vehicle_id`, `name`, `plate_number`, `vehicle_type`, `status`, `current_location`, `last_updated`) VALUES
(1, 'Fire Truck 001', 'FT-001', 'fire_truck', 'available', 'Fire Station', '2025-08-18 02:52:02'),
(2, 'Ambulance 001', 'AMB-001', 'ambulance', 'available', 'Health Center', '2025-08-18 02:52:02'),
(3, 'Rescue Vehicle 001', 'RV-001', 'rescue_vehicle', 'available', 'CDRRMO Office', '2025-08-18 02:52:02'),
(4, '420 truck', '8822', 'rescue_vehicle', 'available', 'CDRRMO Office', '2025-08-18 06:52:53');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `dispatches`
--
ALTER TABLE `dispatches`
  ADD PRIMARY KEY (`dispatch_id`),
  ADD KEY `fk_dispatch_incident` (`incident_id`),
  ADD KEY `fk_dispatch_vehicle` (`vehicle_id`),
  ADD KEY `fk_dispatch_responder` (`responder_id`);

--
-- Indexes for table `dispatch_responders`
--
ALTER TABLE `dispatch_responders`
  ADD PRIMARY KEY (`dispatch_id`,`responder_id`),
  ADD KEY `idx_dr_responder` (`responder_id`);

--
-- Indexes for table `dispatch_vehicles`
--
ALTER TABLE `dispatch_vehicles`
  ADD PRIMARY KEY (`dispatch_id`,`vehicle_id`),
  ADD KEY `idx_dv_vehicle` (`vehicle_id`);

--
-- Indexes for table `incidents`
--
ALTER TABLE `incidents`
  ADD PRIMARY KEY (`incident_id`),
  ADD UNIQUE KEY `incident_number` (`incident_number`);

--
-- Indexes for table `login_history`
--
ALTER TABLE `login_history`
  ADD PRIMARY KEY (`history_id`),
  ADD KEY `fk_login_user` (`user_id`);

--
-- Indexes for table `responders`
--
ALTER TABLE `responders`
  ADD PRIMARY KEY (`responder_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`user_id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- Indexes for table `vehicles`
--
ALTER TABLE `vehicles`
  ADD PRIMARY KEY (`vehicle_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `dispatches`
--
ALTER TABLE `dispatches`
  MODIFY `dispatch_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `incidents`
--
ALTER TABLE `incidents`
  MODIFY `incident_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `login_history`
--
ALTER TABLE `login_history`
  MODIFY `history_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `responders`
--
ALTER TABLE `responders`
  MODIFY `responder_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `user_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `vehicles`
--
ALTER TABLE `vehicles`
  MODIFY `vehicle_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `dispatches`
--
ALTER TABLE `dispatches`
  ADD CONSTRAINT `fk_dispatch_incident` FOREIGN KEY (`incident_id`) REFERENCES `incidents` (`incident_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_dispatch_responder` FOREIGN KEY (`responder_id`) REFERENCES `responders` (`responder_id`) ON DELETE SET NULL,
  ADD CONSTRAINT `fk_dispatch_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`) ON DELETE SET NULL;

--
-- Constraints for table `dispatch_responders`
--
ALTER TABLE `dispatch_responders`
  ADD CONSTRAINT `fk_dr_dispatch` FOREIGN KEY (`dispatch_id`) REFERENCES `dispatches` (`dispatch_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_dr_responder` FOREIGN KEY (`responder_id`) REFERENCES `responders` (`responder_id`);

--
-- Constraints for table `dispatch_vehicles`
--
ALTER TABLE `dispatch_vehicles`
  ADD CONSTRAINT `fk_dv_dispatch` FOREIGN KEY (`dispatch_id`) REFERENCES `dispatches` (`dispatch_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_dv_vehicle` FOREIGN KEY (`vehicle_id`) REFERENCES `vehicles` (`vehicle_id`);

--
-- Constraints for table `login_history`
--
ALTER TABLE `login_history`
  ADD CONSTRAINT `fk_login_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
