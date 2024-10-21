-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Oct 21, 2024 at 02:55 PM
-- Server version: 10.4.24-MariaDB
-- PHP Version: 8.0.19

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `autokantadb`
--

-- --------------------------------------------------------

--
-- Table structure for table `person`
--

CREATE TABLE `person` (
  `ssn` varchar(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `address` varchar(255) NOT NULL,
  `phone_number` varchar(15) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` enum('admin','manager','user') NOT NULL DEFAULT 'user',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `person`
--

INSERT INTO `person` (`ssn`, `name`, `address`, `phone_number`, `password`, `role`, `created_at`, `user_id`) VALUES
('12345678901', 'John Doe', '123 Elm St', '555-1234', 'password', 'user', '2024-10-12 22:15:33', NULL),
('12345678902', 'Vicram', 'Espoo', '00358466103347', '$2y$10$mN26xunp57LWXR7fpsTBnuBdMAXmREdrSQyfcdtVDWrz6LinkFzVG', 'manager', '2024-10-14 15:39:22', NULL),
('12345CAPE', 'Pedrovic', 'Curlew 10 Electricity', '0123334568', '$2y$10$dIk0tkFSZC2P2FfrwJI9veKd9Qo1kE/wzBu.rNguaN2rJ4UwlNZeq', 'user', '2024-10-12 22:42:34', NULL),
('12345CAPET', 'PedrovicRAM', 'Curlew 10 Electricity', '0123334567', '$2y$10$IyUgDkZczkeG9CLkuVvc/.Q9EpD8cuw0X9HuGo9vWSR5g08GX/XVC', 'admin', '2024-10-12 22:43:09', NULL),
('12345CAPtow', 'Cape Town', 'werrek', '22222222222', '$2y$10$wqz6K4UmMHKZ7cMz4XO6aeV68gQktv6YYRuMZl5aG//g12v9UYT1W', 'user', '2024-10-21 11:44:55', NULL),
('12345wf1', 'Pedron', 'Curlew 10 Electricity', '0123334567', '$2y$10$IQxDYG82iaHIPyQTFdntj.YrQxLiKohpVyOBioE49xiLuVSsiokl2', '', '2024-10-12 22:25:39', NULL),
('23456789012', 'Jane Smith', '456 Oak St', '555-5678', 'password', 'admin', '2024-10-12 22:15:33', NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `person`
--
ALTER TABLE `person`
  ADD PRIMARY KEY (`ssn`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
