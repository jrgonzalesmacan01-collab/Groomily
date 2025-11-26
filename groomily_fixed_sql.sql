-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 19, 2025 at 03:19 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

-- Use the groomily database
USE `groomily`;

-- Disable foreign key checks to allow dropping tables
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- Table structure for table `admins`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `admins`;
CREATE TABLE `admins` (
  `admin_id` int(11) NOT NULL AUTO_INCREMENT,
  `admin_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `secret_code` varchar(50) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`admin_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `admins` (`admin_id`, `admin_name`, `email`, `password`, `secret_code`, `created_at`) VALUES
(1, 'Admin', 'admin@groomily.com', 'riotcoding', 'WOOF2024', '2025-11-19 14:17:54');

-- --------------------------------------------------------
-- Table structure for table `users`  
-- --------------------------------------------------------

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `password` varchar(255) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------
-- Table structure for table `services`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `services`;
CREATE TABLE `services` (
  `service_id` int(11) NOT NULL AUTO_INCREMENT,
  `service_name` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `price` decimal(10,2) NOT NULL,
  `duration_minutes` int(11) NOT NULL,
  `status` enum('active','inactive') DEFAULT 'active',
  PRIMARY KEY (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `services` (`service_id`, `service_name`, `description`, `price`, `duration_minutes`, `status`) VALUES
(1, 'Basic Grooming', 'Bath, brush, nail trim, and ear cleaning', 500.00, 60, 'active'),
(2, 'Deluxe Grooming', 'Basic grooming plus haircut and styling', 800.00, 90, 'active'),
(3, 'Nail Polish & Care', 'Nail trimming, filing, and polish application', 200.00, 30, 'active'),
(4, 'Spa Treatment', 'Premium bath with aromatherapy and massage', 1000.00, 120, 'active'),
(5, 'Teeth Cleaning', 'Professional dental cleaning and breath treatment', 400.00, 45, 'active'),
(6, 'Flea & Tick Treatment', 'Complete flea and tick removal treatment', 600.00, 60, 'active'),
(7, 'De-shedding Treatment', 'Special treatment to reduce shedding', 700.00, 75, 'active'),
(8, 'Puppy Package', 'Gentle grooming for puppies (includes bath and trim)', 450.00, 45, 'active');

-- --------------------------------------------------------
-- Table structure for table `booking_slots`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `booking_slots`;
CREATE TABLE `booking_slots` (
  `slot_id` int(11) NOT NULL AUTO_INCREMENT,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `max_bookings` int(11) DEFAULT 3,
  `current_bookings` int(11) DEFAULT 0,
  PRIMARY KEY (`slot_id`),
  UNIQUE KEY `date_time` (`booking_date`,`booking_time`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

INSERT INTO `booking_slots` (`slot_id`, `booking_date`, `booking_time`, `max_bookings`, `current_bookings`) VALUES
(1, '2025-11-19', '09:00:00', 3, 0),
(2, '2025-11-19', '10:00:00', 3, 0),
(3, '2025-11-19', '11:00:00', 3, 0),
(4, '2025-11-19', '13:00:00', 3, 0),
(5, '2025-11-19', '14:00:00', 3, 0),
(6, '2025-11-19', '15:00:00', 3, 0),
(7, '2025-11-19', '16:00:00', 3, 0),
(8, '2025-11-20', '09:00:00', 3, 0),
(9, '2025-11-20', '10:00:00', 3, 0),
(10, '2025-11-20', '11:00:00', 3, 0),
(11, '2025-11-20', '13:00:00', 3, 0),
(12, '2025-11-20', '14:00:00', 3, 0),
(13, '2025-11-20', '15:00:00', 3, 0),
(14, '2025-11-20', '16:00:00', 3, 0),
(15, '2025-11-21', '09:00:00', 3, 0),
(16, '2025-11-21', '10:00:00', 3, 0),
(17, '2025-11-21', '11:00:00', 3, 0),
(18, '2025-11-21', '13:00:00', 3, 0),
(19, '2025-11-21', '14:00:00', 3, 0),
(20, '2025-11-21', '15:00:00', 3, 0),
(21, '2025-11-21', '16:00:00', 3, 0),
(22, '2025-11-22', '09:00:00', 3, 0),
(23, '2025-11-22', '10:00:00', 3, 0),
(24, '2025-11-22', '11:00:00', 3, 0),
(25, '2025-11-22', '13:00:00', 3, 0),
(26, '2025-11-22', '14:00:00', 3, 0),
(27, '2025-11-22', '15:00:00', 3, 0),
(28, '2025-11-22', '16:00:00', 3, 0),
(29, '2025-11-24', '09:00:00', 3, 0),
(30, '2025-11-24', '10:00:00', 3, 0),
(31, '2025-11-24', '11:00:00', 3, 0),
(32, '2025-11-24', '13:00:00', 3, 0),
(33, '2025-11-24', '14:00:00', 3, 0),
(34, '2025-11-24', '15:00:00', 3, 0),
(35, '2025-11-24', '16:00:00', 3, 0),
(36, '2025-11-25', '09:00:00', 3, 0),
(37, '2025-11-25', '10:00:00', 3, 0),
(38, '2025-11-25', '11:00:00', 3, 0),
(39, '2025-11-25', '13:00:00', 3, 0),
(40, '2025-11-25', '14:00:00', 3, 0),
(41, '2025-11-25', '15:00:00', 3, 0),
(42, '2025-11-25', '16:00:00', 3, 0),
(43, '2025-11-26', '09:00:00', 3, 0),
(44, '2025-11-26', '10:00:00', 3, 0),
(45, '2025-11-26', '11:00:00', 3, 0),
(46, '2025-11-26', '13:00:00', 3, 0),
(47, '2025-11-26', '14:00:00', 3, 0),
(48, '2025-11-26', '15:00:00', 3, 0),
(49, '2025-11-26', '16:00:00', 3, 0),
(50, '2025-11-27', '09:00:00', 3, 0),
(51, '2025-11-27', '10:00:00', 3, 0),
(52, '2025-11-27', '11:00:00', 3, 0),
(53, '2025-11-27', '13:00:00', 3, 0),
(54, '2025-11-27', '14:00:00', 3, 0),
(55, '2025-11-27', '15:00:00', 3, 0),
(56, '2025-11-27', '16:00:00', 3, 0),
(57, '2025-11-28', '09:00:00', 3, 0),
(58, '2025-11-28', '10:00:00', 3, 0),
(59, '2025-11-28', '11:00:00', 3, 0),
(60, '2025-11-28', '13:00:00', 3, 0),
(61, '2025-11-28', '14:00:00', 3, 0),
(62, '2025-11-28', '15:00:00', 3, 0),
(63, '2025-11-28', '16:00:00', 3, 0),
(64, '2025-11-29', '09:00:00', 3, 0),
(65, '2025-11-29', '10:00:00', 3, 0),
(66, '2025-11-29', '11:00:00', 3, 0),
(67, '2025-11-29', '13:00:00', 3, 0),
(68, '2025-11-29', '14:00:00', 3, 0),
(69, '2025-11-29', '15:00:00', 3, 0),
(70, '2025-11-29', '16:00:00', 3, 0),
(71, '2025-12-01', '09:00:00', 3, 0),
(72, '2025-12-01', '10:00:00', 3, 0),
(73, '2025-12-01', '11:00:00', 3, 0),
(74, '2025-12-01', '13:00:00', 3, 0),
(75, '2025-12-01', '14:00:00', 3, 0),
(76, '2025-12-01', '15:00:00', 3, 0),
(77, '2025-12-01', '16:00:00', 3, 0),
(78, '2025-12-02', '09:00:00', 3, 0),
(79, '2025-12-02', '10:00:00', 3, 0),
(80, '2025-12-02', '11:00:00', 3, 0),
(81, '2025-12-02', '13:00:00', 3, 0),
(82, '2025-12-02', '14:00:00', 3, 0),
(83, '2025-12-02', '15:00:00', 3, 0),
(84, '2025-12-02', '16:00:00', 3, 0),
(85, '2025-12-03', '09:00:00', 3, 0),
(86, '2025-12-03', '10:00:00', 3, 0),
(87, '2025-12-03', '11:00:00', 3, 0),
(88, '2025-12-03', '13:00:00', 3, 0),
(89, '2025-12-03', '14:00:00', 3, 0),
(90, '2025-12-03', '15:00:00', 3, 0),
(91, '2025-12-03', '16:00:00', 3, 0),
(92, '2025-12-04', '09:00:00', 3, 0),
(93, '2025-12-04', '10:00:00', 3, 0),
(94, '2025-12-04', '11:00:00', 3, 0),
(95, '2025-12-04', '13:00:00', 3, 0),
(96, '2025-12-04', '14:00:00', 3, 0),
(97, '2025-12-04', '15:00:00', 3, 0),
(98, '2025-12-04', '16:00:00', 3, 0),
(99, '2025-12-05', '09:00:00', 3, 0),
(100, '2025-12-05', '10:00:00', 3, 0),
(101, '2025-12-05', '11:00:00', 3, 0),
(102, '2025-12-05', '13:00:00', 3, 0),
(103, '2025-12-05', '14:00:00', 3, 0),
(104, '2025-12-05', '15:00:00', 3, 0),
(105, '2025-12-05', '16:00:00', 3, 0),
(106, '2025-12-06', '09:00:00', 3, 0),
(107, '2025-12-06', '10:00:00', 3, 0),
(108, '2025-12-06', '11:00:00', 3, 0),
(109, '2025-12-06', '13:00:00', 3, 0),
(110, '2025-12-06', '14:00:00', 3, 0),
(111, '2025-12-06', '15:00:00', 3, 0),
(112, '2025-12-06', '16:00:00', 3, 0),
(113, '2025-12-08', '09:00:00', 3, 0),
(114, '2025-12-08', '10:00:00', 3, 0),
(115, '2025-12-08', '11:00:00', 3, 0),
(116, '2025-12-08', '13:00:00', 3, 0),
(117, '2025-12-08', '14:00:00', 3, 0),
(118, '2025-12-08', '15:00:00', 3, 0),
(119, '2025-12-08', '16:00:00', 3, 0),
(120, '2025-12-09', '09:00:00', 3, 0),
(121, '2025-12-09', '10:00:00', 3, 0),
(122, '2025-12-09', '11:00:00', 3, 0),
(123, '2025-12-09', '13:00:00', 3, 0),
(124, '2025-12-09', '14:00:00', 3, 0),
(125, '2025-12-09', '15:00:00', 3, 0),
(126, '2025-12-09', '16:00:00', 3, 0),
(127, '2025-12-10', '09:00:00', 3, 0),
(128, '2025-12-10', '10:00:00', 3, 0),
(129, '2025-12-10', '11:00:00', 3, 0),
(130, '2025-12-10', '13:00:00', 3, 0),
(131, '2025-12-10', '14:00:00', 3, 0),
(132, '2025-12-10', '15:00:00', 3, 0),
(133, '2025-12-10', '16:00:00', 3, 0),
(134, '2025-12-11', '09:00:00', 3, 0),
(135, '2025-12-11', '10:00:00', 3, 0),
(136, '2025-12-11', '11:00:00', 3, 0),
(137, '2025-12-11', '13:00:00', 3, 0),
(138, '2025-12-11', '14:00:00', 3, 0),
(139, '2025-12-11', '15:00:00', 3, 0),
(140, '2025-12-11', '16:00:00', 3, 0),
(141, '2025-12-12', '09:00:00', 3, 0),
(142, '2025-12-12', '10:00:00', 3, 0),
(143, '2025-12-12', '11:00:00', 3, 0),
(144, '2025-12-12', '13:00:00', 3, 0),
(145, '2025-12-12', '14:00:00', 3, 0),
(146, '2025-12-12', '15:00:00', 3, 0),
(147, '2025-12-12', '16:00:00', 3, 0),
(148, '2025-12-13', '09:00:00', 3, 0),
(149, '2025-12-13', '10:00:00', 3, 0),
(150, '2025-12-13', '11:00:00', 3, 0),
(151, '2025-12-13', '13:00:00', 3, 0),
(152, '2025-12-13', '14:00:00', 3, 0),
(153, '2025-12-13', '15:00:00', 3, 0),
(154, '2025-12-13', '16:00:00', 3, 0),
(155, '2025-12-15', '09:00:00', 3, 0),
(156, '2025-12-15', '10:00:00', 3, 0),
(157, '2025-12-15', '11:00:00', 3, 0),
(158, '2025-12-15', '13:00:00', 3, 0),
(159, '2025-12-15', '14:00:00', 3, 0),
(160, '2025-12-15', '15:00:00', 3, 0),
(161, '2025-12-15', '16:00:00', 3, 0),
(162, '2025-12-16', '09:00:00', 3, 0),
(163, '2025-12-16', '10:00:00', 3, 0),
(164, '2025-12-16', '11:00:00', 3, 0),
(165, '2025-12-16', '13:00:00', 3, 0),
(166, '2025-12-16', '14:00:00', 3, 0),
(167, '2025-12-16', '15:00:00', 3, 0),
(168, '2025-12-16', '16:00:00', 3, 0),
(169, '2025-12-17', '09:00:00', 3, 0),
(170, '2025-12-17', '10:00:00', 3, 0),
(171, '2025-12-17', '11:00:00', 3, 0),
(172, '2025-12-17', '13:00:00', 3, 0),
(173, '2025-12-17', '14:00:00', 3, 0),
(174, '2025-12-17', '15:00:00', 3, 0),
(175, '2025-12-17', '16:00:00', 3, 0),
(176, '2025-12-18', '09:00:00', 3, 0),
(177, '2025-12-18', '10:00:00', 3, 0),
(178, '2025-12-18', '11:00:00', 3, 0),
(179, '2025-12-18', '13:00:00', 3, 0),
(180, '2025-12-18', '14:00:00', 3, 0),
(181, '2025-12-18', '15:00:00', 3, 0),
(182, '2025-12-18', '16:00:00', 3, 0),
(183, '2025-12-19', '09:00:00', 3, 0),
(184, '2025-12-19', '10:00:00', 3, 0),
(185, '2025-12-19', '11:00:00', 3, 0),
(186, '2025-12-19', '13:00:00', 3, 0),
(187, '2025-12-19', '14:00:00', 3, 0),
(188, '2025-12-19', '15:00:00', 3, 0),
(189, '2025-12-19', '16:00:00', 3, 0);

-- --------------------------------------------------------
-- Table structure for table `bookings`
-- --------------------------------------------------------

DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
  `booking_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `booking_code` varchar(20) NOT NULL,
  `owner_name` varchar(100) NOT NULL,
  `dog_name` varchar(100) NOT NULL,
  `dog_age` int(11) NOT NULL,
  `dog_breed` varchar(100) NOT NULL,
  `owner_phone` varchar(20) NOT NULL,
  `booking_date` date NOT NULL,
  `booking_time` time NOT NULL,
  `service_id` int(11) NOT NULL,
  `payment_method` enum('cash','card','online') NOT NULL,
  `payment_status` enum('pending','paid','cancelled') DEFAULT 'pending',
  `card_number` varchar(20) DEFAULT NULL,
  `card_holder` varchar(100) DEFAULT NULL,
  `online_payment_id` varchar(100) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` enum('pending','confirmed','completed','cancelled') DEFAULT 'pending',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`booking_id`),
  UNIQUE KEY `booking_code` (`booking_code`),
  KEY `user_id` (`user_id`),
  KEY `service_id` (`service_id`),
  CONSTRAINT `bookings_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`user_id`) ON DELETE CASCADE,
  CONSTRAINT `bookings_ibfk_2` FOREIGN KEY (`service_id`) REFERENCES `services` (`service_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- Re-enable foreign key checks
SET FOREIGN_KEY_CHECKS = 1;

COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;