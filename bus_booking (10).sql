-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Mar 26, 2025 at 12:18 PM
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
-- Database: `bus_booking`
--

-- --------------------------------------------------------

--
-- Table structure for table `bookings`
--

CREATE TABLE `bookings` (
  `id` int(11) NOT NULL,
  `booking_id` varchar(20) DEFAULT NULL,
  `bus_id` int(11) NOT NULL,
  `bus_name` varchar(100) NOT NULL,
  `departure_date` date NOT NULL,
  `source` varchar(100) NOT NULL,
  `destination` varchar(100) NOT NULL,
  `passenger_name` varchar(100) NOT NULL,
  `passenger_age` int(11) NOT NULL,
  `passenger_gender` enum('male','female','other') NOT NULL,
  `passenger_mobile` varchar(15) NOT NULL,
  `alternate_mobile` varchar(15) DEFAULT NULL,
  `seat_numbers` varchar(100) NOT NULL,
  `total_price` decimal(10,2) NOT NULL,
  `booking_date` timestamp NOT NULL DEFAULT current_timestamp(),
  `payment_status` enum('pending','completed','failed') DEFAULT 'completed',
  `status` enum('pending','confirmed','cancelled') DEFAULT 'pending'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bookings`
--

INSERT INTO `bookings` (`id`, `booking_id`, `bus_id`, `bus_name`, `departure_date`, `source`, `destination`, `passenger_name`, `passenger_age`, `passenger_gender`, `passenger_mobile`, `alternate_mobile`, `seat_numbers`, `total_price`, `booking_date`, `payment_status`, `status`) VALUES
(13, 'BK67e3daeb9ac3e', 105, 'Express Bus', '2025-03-27', 'Sakoli', 'Chichgad', 'Aayush Raut', 19, 'male', '5262664127', '5262664127', '3', 900.00, '2025-03-26 10:46:03', 'completed', 'pending'),
(14, 'BK67e3de8189ad3', 101, 'Express Bus', '2025-03-26', 'Sakoli', 'Mahur', 'Sneha Raut', 19, 'female', '5262664127', '5262664127', '14', 750.00, '2025-03-26 11:01:21', 'completed', 'pending'),
(15, 'BK67e3e085b71b8', 107, 'Express Bus', '2025-03-26', 'Sakoli', 'Paratwada', 'Ranju Sonwane', 45, 'female', '5262664127', '5262664127', '13', 150.00, '2025-03-26 11:09:57', 'completed', 'pending');

-- --------------------------------------------------------

--
-- Table structure for table `buses`
--

CREATE TABLE `buses` (
  `id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `source` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `departure_time` time NOT NULL,
  `male_price` decimal(10,2) NOT NULL,
  `female_price` decimal(10,2) NOT NULL,
  `schedule` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `buses`
--

INSERT INTO `buses` (`id`, `name`, `source`, `destination`, `departure_time`, `male_price`, `female_price`, `schedule`) VALUES
(88, 'Express Bus', 'Sakoli', 'Nagpur', '05:45:00', 195.00, 95.00, 'Daily'),
(89, 'Express Bus', 'Sakoli', 'Deori', '06:00:00', 70.00, 35.00, 'Daily'),
(90, 'Express Bus', 'Sakoli', 'Navegaon', '06:30:00', 80.00, 40.00, 'Daily'),
(91, 'Express Bus', 'Sakoli', 'Aamgoan', '07:00:00', 50.00, 25.00, 'Daily'),
(92, 'Express Bus', 'Sakoli', 'Kakodi', '07:30:00', 150.00, 75.00, 'Daily'),
(93, 'Express Bus', 'Sakoli', 'Rajnandgaon', '08:00:00', 500.00, 250.00, 'Daily'),
(94, 'Express Bus', 'Sakoli', 'Lalbarra', '08:30:00', 250.00, 175.00, 'Daily'),
(95, 'Express Bus', 'Sakoli', 'Katol', '09:00:00', 350.00, 225.00, 'Daily'),
(96, 'Express Bus', 'Sakoli', 'Yawatmal', '09:30:00', 500.00, 250.00, 'Daily'),
(97, 'Express Bus', 'Sakoli', 'Chindwada', '10:00:00', 100.00, 50.00, 'Daily'),
(98, 'Express Bus', 'Sakoli', 'Amravati', '10:30:00', 900.00, 450.00, 'Daily'),
(99, 'Express Bus', 'Sakoli', 'Nanded', '11:00:00', 300.00, 250.00, 'Daily'),
(100, 'Express Bus', 'Sakoli', 'Pusad', '11:30:00', 1400.00, 700.00, 'Daily'),
(101, 'Express Bus', 'Sakoli', 'Mahur', '12:00:00', 1500.00, 750.00, 'Daily'),
(102, 'Express Bus', 'Sakoli', 'Umarkhed', '12:30:00', 600.00, 300.00, 'Daily'),
(103, 'Express Bus', 'Sakoli', 'Shegaon', '13:00:00', 700.00, 550.00, 'Daily'),
(104, 'Express Bus', 'Sakoli', 'Pratapgad', '13:30:00', 80.00, 40.00, 'Daily'),
(105, 'Express Bus', 'Sakoli', 'Chichgad', '14:00:00', 900.00, 450.00, 'Daily'),
(106, 'Express Bus', 'Sakoli', 'Lonar', '14:30:00', 200.00, 100.00, 'Daily'),
(107, 'Express Bus', 'Sakoli', 'Paratwada', '15:00:00', 300.00, 150.00, 'Daily'),
(108, 'Express Bus', 'Sakoli', 'Wardha', '15:30:00', 1100.00, 800.00, 'Daily'),
(109, 'Express Bus', 'Sakoli', 'Aheri', '16:00:00', 300.00, 150.00, 'Daily'),
(110, 'Express Bus', 'Sakoli', 'Chandrapur', '16:30:00', 1400.00, 700.00, 'Daily'),
(111, 'Express Bus', 'Sakoli', 'Balaghat', '17:00:00', 1500.00, 750.00, 'Daily'),
(112, 'Express Bus', 'Sakoli', 'Bhandara', '17:30:00', 80.00, 40.00, 'Daily'),
(113, 'Express Bus', 'Sakoli', 'Magardoh', '18:00:00', 200.00, 100.00, 'Daily'),
(114, 'Express Bus', 'Sakoli', 'Kurkheda', '18:30:00', 80.00, 40.00, 'Daily'),
(115, 'Express Bus', 'Sakoli', 'Mehtakheda', '19:00:00', 200.00, 100.00, 'Daily'),
(116, 'Express Bus', 'Sakoli', 'Raypur', '19:30:00', 3000.00, 1500.00, 'Daily'),
(117, 'Express Bus', 'Sakoli', 'Palandur', '20:00:00', 60.00, 30.00, 'Daily'),
(118, 'Express Bus', 'Sakoli', 'Wadsa', '20:30:00', 200.00, 100.00, 'Daily'),
(119, 'Express Bus', 'Sakoli', 'Rajura', '21:00:00', 300.00, 150.00, 'Daily'),
(120, 'Express Bus', 'Sakoli', 'Lakhandur', '21:30:00', 60.00, 30.00, 'Daily'),
(121, 'Express Bus', 'Sakoli', 'Mohrana', '22:00:00', 250.00, 175.00, 'Daily'),
(122, 'Express Bus', 'Sakoli', 'Keshori', '22:30:00', 50.00, 25.00, 'Daily'),
(123, 'Express Bus', 'Sakoli', 'Ballarsha', '23:00:00', 700.00, 350.00, 'Daily'),
(124, 'Express Bus', 'Sakoli', 'Khamba', '23:30:00', 40.00, 20.00, 'Daily'),
(125, 'Express Bus', 'Sakoli', 'Bolde', '00:00:00', 40.00, 20.00, 'Daily'),
(126, 'Express Bus', 'Sakoli', 'Gondumari', '00:30:00', 60.00, 30.00, 'Daily'),
(127, 'Express Bus', 'Sakoli', 'Mahalgoan', '01:00:00', 46.00, 23.00, 'Daily'),
(128, 'Express Bus', 'Sakoli', 'Parsodi', '01:30:00', 42.00, 21.00, 'Daily'),
(129, 'Express Bus', 'Sakoli', 'Jambhli', '02:00:00', 40.00, 20.00, 'Daily'),
(130, 'Express Bus', 'Sakoli', 'Salebhata', '02:30:00', 40.00, 20.00, 'Daily'),
(131, 'Express Bus', 'Sakoli', 'Shenda', '03:00:00', 45.00, 22.00, 'Daily'),
(132, 'Express Bus', 'Sakoli', 'Dinkarnagar', '03:30:00', 70.00, 35.00, 'Daily'),
(133, 'Express Bus', 'Sakoli', 'Arjuni', '04:00:00', 47.00, 23.00, 'Daily'),
(134, 'Express Bus', 'Sakoli', 'Bharnoli', '04:30:00', 48.00, 24.00, 'Daily'),
(135, 'Express Bus', 'Sakoli', 'Lendezari', '05:00:00', 49.00, 24.00, 'Daily'),
(136, 'Express Bus', 'Sakoli', 'Khodshiwni', '05:30:00', 50.00, 25.00, 'Daily'),
(137, 'Express Bus', 'Sakoli', 'Kosamtondi', '06:00:00', 45.00, 25.00, 'Daily'),
(138, 'Express Bus', 'Sakoli', 'Satalwada', '06:30:00', 25.00, 15.00, 'Daily'),
(139, 'Express Bus', 'Sakoli', 'Mokhe', '07:00:00', 20.00, 10.00, 'Daily'),
(140, 'Express Bus', 'Sakoli', 'Dhanori', '07:30:00', 40.00, 20.00, 'Daily'),
(141, 'Express Bus', 'Sakoli', 'Tumsar', '08:00:00', 100.00, 50.00, 'Daily'),
(142, 'Express Bus', 'Sakoli', 'Sonegaon', '08:30:00', 70.00, 35.00, 'Daily'),
(143, 'Express Bus', 'Sakoli', 'Tiroda', '09:00:00', 110.00, 55.00, 'Daily'),
(144, 'Express Bus', 'Sakoli', 'Umarzari', '09:30:00', 80.00, 40.00, 'Daily'),
(145, 'Express Bus', 'Sakoli', 'Parastola', '10:00:00', 50.00, 25.00, 'Daily'),
(146, 'Express Bus', 'Sakoli', 'Ghanod', '10:30:00', 40.00, 20.00, 'Daily'),
(147, 'Express Bus', 'Sakoli', 'Pitezari', '11:00:00', 70.00, 35.00, 'Daily'),
(148, 'Express Bus', 'Sakoli', 'Adyal', '11:30:00', 200.00, 100.00, 'Daily'),
(149, 'Express Bus', 'Deori', 'Nagpur', '07:15:00', 200.00, 100.00, 'Daily'),
(150, 'Express Bus', 'Deori', 'Nagpur', '08:15:00', 200.00, 100.00, 'Daily'),
(151, 'Express Bus', 'Navegaon', 'Nagpur', '07:25:00', 250.00, 125.00, 'Daily'),
(152, 'Express Bus', 'Gondia', 'Lonar', '07:30:00', 1200.00, 600.00, 'Daily'),
(153, 'Express Bus', 'Gondia', 'Nagpur', '07:40:00', 400.00, 200.00, 'Daily'),
(154, 'Express Bus', 'Sakoli', 'Aurangabaad', '08:00:00', 700.00, 350.00, 'Daily'),
(155, 'Express Bus', 'Sakoli', 'Shegaon', '08:30:00', 800.00, 400.00, 'Daily'),
(156, 'Express Bus', 'Gondia', 'Nanded', '20:35:00', 1200.00, 600.00, 'Daily'),
(157, 'Express Bus', 'Aamgaon', 'Nagpur', '20:40:00', 250.00, 125.00, 'Daily'),
(158, 'Express Bus', 'Gondia', 'Akola', '08:40:00', 700.00, 350.00, 'Daily'),
(159, 'Express Bus', 'Sakoli', 'Katol', '09:00:00', 800.00, 400.00, 'Daily'),
(160, 'Express Bus', 'Sakoli', 'Pusad', '21:15:00', 1200.00, 600.00, 'Daily'),
(161, 'Express Bus', 'Gondia', 'Umarkhed', '21:40:00', 900.00, 450.00, 'Daily'),
(162, 'Express Bus', 'Deori', 'Bhandara', '09:55:00', 100.00, 50.00, 'Daily'),
(163, 'Express Bus', 'Kakodi', 'Nagpur', '10:15:00', 800.00, 400.00, 'Daily'),
(164, 'Express Bus', 'Rajnandgaon', 'Nagpur', '22:30:00', 1200.00, 600.00, 'Daily'),
(165, 'Express Bus', 'Gondia', 'Mahur', '22:35:00', 900.00, 450.00, 'Daily'),
(166, 'Express Bus', 'Sakoli', 'Akola', '11:00:00', 700.00, 350.00, 'Daily'),
(167, 'Express Bus', 'Lalbarra', 'Nagpur', '11:05:00', 700.00, 350.00, 'Daily'),
(168, 'Express Bus', 'Gondia', 'Chindwada', '11:10:00', 700.00, 350.00, 'Daily'),
(169, 'Express Bus', 'Deori', 'Nagpur', '11:15:00', 700.00, 350.00, 'Daily'),
(170, 'Express Bus', 'Gondia', 'Nagpur', '11:30:00', 700.00, 350.00, 'Daily'),
(171, 'Luxury Bus', 'Sakoli', 'Nagpur', '07:00:00', 500.00, 250.00, 'Daily'),
(172, 'Luxury Bus', 'Sakoli', 'Deori', '08:00:00', 150.00, 75.00, 'Daily'),
(173, 'Luxury Bus', 'Sakoli', 'Navegaon', '08:30:00', 200.00, 100.00, 'Daily'),
(174, 'Luxury Bus', 'Sakoli', 'Aamgoan', '09:00:00', 100.00, 50.00, 'Daily'),
(175, 'Luxury Bus', 'Sakoli', 'Kakodi', '09:30:00', 300.00, 150.00, 'Daily'),
(176, 'Luxury Bus', 'Sakoli', 'Rajnandgaon', '10:00:00', 700.00, 350.00, 'Daily'),
(177, 'Luxury Bus', 'Sakoli', 'Lalbarra', '10:30:00', 400.00, 250.00, 'Daily'),
(178, 'Luxury Bus', 'Sakoli', 'Katol', '11:00:00', 500.00, 300.00, 'Daily'),
(179, 'Luxury Bus', 'Sakoli', 'Yawatmal', '11:30:00', 700.00, 350.00, 'Daily'),
(180, 'Luxury Bus', 'Sakoli', 'Chindwada', '12:00:00', 200.00, 100.00, 'Daily'),
(181, 'Luxury Bus', 'Sakoli', 'Amravati', '12:30:00', 1200.00, 600.00, 'Daily'),
(182, 'Luxury Bus', 'Sakoli', 'Nanded', '13:00:00', 400.00, 350.00, 'Daily'),
(183, 'Luxury Bus', 'Sakoli', 'Pusad', '13:30:00', 1600.00, 800.00, 'Daily'),
(184, 'Luxury Bus', 'Sakoli', 'Mahur', '14:00:00', 1700.00, 850.00, 'Daily'),
(185, 'Luxury Bus', 'Sakoli', 'Umarkhed', '14:30:00', 800.00, 400.00, 'Daily'),
(186, 'Luxury Bus', 'Sakoli', 'Shegaon', '15:00:00', 900.00, 700.00, 'Daily'),
(187, 'Luxury Bus', 'Sakoli', 'Pratapgad', '15:30:00', 120.00, 60.00, 'Daily'),
(188, 'Luxury Bus', 'Sakoli', 'Chichgad', '16:00:00', 1100.00, 550.00, 'Daily'),
(189, 'Luxury Bus', 'Sakoli', 'Lonar', '16:30:00', 300.00, 150.00, 'Daily'),
(190, 'Luxury Bus', 'Sakoli', 'Paratwada', '17:00:00', 400.00, 200.00, 'Daily'),
(191, 'Luxury Bus', 'Sakoli', 'Wardha', '17:30:00', 1300.00, 1000.00, 'Daily'),
(192, 'Luxury Bus', 'Sakoli', 'Aheri', '18:00:00', 400.00, 200.00, 'Daily'),
(193, 'Luxury Bus', 'Sakoli', 'Chandrapur', '18:30:00', 1600.00, 800.00, 'Daily'),
(194, 'Luxury Bus', 'Sakoli', 'Balaghat', '19:00:00', 1700.00, 850.00, 'Daily'),
(195, 'Luxury Bus', 'Sakoli', 'Bhandara', '19:30:00', 120.00, 60.00, 'Daily'),
(196, 'Luxury Bus', 'Sakoli', 'Magardoh', '20:00:00', 300.00, 150.00, 'Daily'),
(197, 'Luxury Bus', 'Sakoli', 'Kurkheda', '20:30:00', 120.00, 60.00, 'Daily'),
(198, 'Luxury Bus', 'Sakoli', 'Mehtakheda', '21:00:00', 300.00, 150.00, 'Daily'),
(199, 'Luxury Bus', 'Sakoli', 'Raypur', '21:30:00', 4000.00, 2000.00, 'Daily'),
(200, 'Luxury Bus', 'Sakoli', 'Palandur', '22:00:00', 100.00, 50.00, 'Daily'),
(201, 'Luxury Bus', 'Sakoli', 'Wadsa', '22:30:00', 300.00, 150.00, 'Daily'),
(202, 'Luxury Bus', 'Sakoli', 'Rajura', '23:00:00', 400.00, 200.00, 'Daily'),
(203, 'Luxury Bus', 'Sakoli', 'Lakhandur', '23:30:00', 100.00, 50.00, 'Daily'),
(204, 'Luxury Bus', 'Sakoli', 'Mohrana', '00:00:00', 350.00, 250.00, 'Daily'),
(205, 'Luxury Bus', 'Sakoli', 'Keshori', '00:30:00', 80.00, 40.00, 'Daily'),
(206, 'Luxury Bus', 'Sakoli', 'Ballarsha', '01:00:00', 900.00, 450.00, 'Daily'),
(207, 'Luxury Bus', 'Sakoli', 'Khamba', '01:30:00', 60.00, 30.00, 'Daily'),
(208, 'Luxury Bus', 'Sakoli', 'Bolde', '02:00:00', 60.00, 30.00, 'Daily'),
(209, 'Luxury Bus', 'Sakoli', 'Gondumari', '02:30:00', 100.00, 50.00, 'Daily'),
(210, 'Luxury Bus', 'Sakoli', 'Mahalgoan', '03:00:00', 70.00, 35.00, 'Daily'),
(211, 'Luxury Bus', 'Sakoli', 'Parsodi', '03:30:00', 65.00, 32.00, 'Daily'),
(212, 'Luxury Bus', 'Sakoli', 'Jambhli', '04:00:00', 60.00, 30.00, 'Daily'),
(213, 'Luxury Bus', 'Sakoli', 'Salebhata', '04:30:00', 60.00, 30.00, 'Daily'),
(214, 'Luxury Bus', 'Sakoli', 'Shenda', '05:00:00', 70.00, 35.00, 'Daily'),
(215, 'Luxury Bus', 'Sakoli', 'Dinkarnagar', '05:30:00', 100.00, 50.00, 'Daily'),
(216, 'Luxury Bus', 'Sakoli', 'Arjuni', '06:00:00', 70.00, 35.00, 'Daily'),
(217, 'Luxury Bus', 'Sakoli', 'Bharnoli', '06:30:00', 75.00, 37.00, 'Daily'),
(218, 'Luxury Bus', 'Sakoli', 'Lendezari', '07:00:00', 80.00, 40.00, 'Daily'),
(219, 'Luxury Bus', 'Sakoli', 'Khodshiwni', '07:30:00', 80.00, 40.00, 'Daily'),
(220, 'Luxury Bus', 'Sakoli', 'Kosamtondi', '08:00:00', 70.00, 35.00, 'Daily'),
(221, 'Luxury Bus', 'Sakoli', 'Satalwada', '08:30:00', 40.00, 20.00, 'Daily'),
(222, 'Luxury Bus', 'Sakoli', 'Mokhe', '09:00:00', 35.00, 17.00, 'Daily'),
(223, 'Luxury Bus', 'Sakoli', 'Dhanori', '09:30:00', 60.00, 30.00, 'Daily'),
(224, 'Luxury Bus', 'Sakoli', 'Tumsar', '10:00:00', 150.00, 75.00, 'Daily'),
(225, 'Luxury Bus', 'Sakoli', 'Sonegaon', '10:30:00', 100.00, 50.00, 'Daily'),
(226, 'Luxury Bus', 'Sakoli', 'Tiroda', '11:00:00', 160.00, 80.00, 'Daily'),
(227, 'Luxury Bus', 'Sakoli', 'Umarzari', '11:30:00', 120.00, 60.00, 'Daily'),
(228, 'Luxury Bus', 'Sakoli', 'Parastola', '12:00:00', 80.00, 40.00, 'Daily'),
(229, 'Luxury Bus', 'Sakoli', 'Ghanod', '12:30:00', 60.00, 30.00, 'Daily'),
(230, 'Luxury Bus', 'Sakoli', 'Pitezari', '13:00:00', 100.00, 50.00, 'Daily'),
(231, 'Luxury Bus', 'Sakoli', 'Adyal', '13:30:00', 300.00, 150.00, 'Daily');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `phone` varchar(20) NOT NULL,
  `registration_date` datetime DEFAULT current_timestamp(),
  `total_bookings` int(11) DEFAULT 0,
  `status` enum('active','inactive') DEFAULT 'active'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `id` int(11) NOT NULL,
  `booking_id` int(11) NOT NULL,
  `transaction_id` varchar(50) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` enum('UPI','QR') NOT NULL DEFAULT 'UPI',
  `upi_id` varchar(50) DEFAULT NULL,
  `payment_status` enum('pending','completed','failed') DEFAULT 'pending',
  `payment_date` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `routes`
--

CREATE TABLE `routes` (
  `id` int(11) NOT NULL,
  `source` varchar(255) NOT NULL,
  `destination` varchar(255) NOT NULL,
  `departure_date` date NOT NULL,
  `bus_name` varchar(255) NOT NULL,
  `male_price` decimal(10,2) NOT NULL,
  `female_price` decimal(10,2) NOT NULL,
  `departure_time` time NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `routes`
--

INSERT INTO `routes` (`id`, `source`, `destination`, `departure_date`, `bus_name`, `male_price`, `female_price`, `departure_time`) VALUES
(356, 'Sakoli', 'Wardha', '2025-03-26', '', 0.00, 0.00, '00:00:00'),
(357, 'Sakoli', 'Yawatmal', '2025-03-26', '', 0.00, 0.00, '00:00:00'),
(358, 'Sakoli', 'Kosamtondi', '2025-03-26', '', 0.00, 0.00, '00:00:00'),
(359, 'Deori', 'Aamgoan', '2025-03-26', '', 0.00, 0.00, '00:00:00'),
(360, 'Sakoli', 'Aamgoan', '2025-03-26', '', 0.00, 0.00, '00:00:00'),
(361, 'Sakoli', 'Chichgad', '2025-03-27', '', 0.00, 0.00, '00:00:00'),
(362, 'Sakoli', 'Mahur', '2025-03-26', '', 0.00, 0.00, '00:00:00'),
(363, 'Sakoli', 'Paratwada', '2025-03-26', '', 0.00, 0.00, '00:00:00'),
(364, 'Sakoli', 'Pratapgad', '2025-03-26', '', 0.00, 0.00, '00:00:00');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bookings`
--
ALTER TABLE `bookings`
  ADD PRIMARY KEY (`id`),
  ADD KEY `fk_bus` (`bus_id`);

--
-- Indexes for table `buses`
--
ALTER TABLE `buses`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`id`),
  ADD KEY `booking_id` (`booking_id`);

--
-- Indexes for table `routes`
--
ALTER TABLE `routes`
  ADD PRIMARY KEY (`id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `bookings`
--
ALTER TABLE `bookings`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `buses`
--
ALTER TABLE `buses`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=232;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `routes`
--
ALTER TABLE `routes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=365;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bookings`
--
ALTER TABLE `bookings`
  ADD CONSTRAINT `fk_bus` FOREIGN KEY (`bus_id`) REFERENCES `buses` (`id`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`booking_id`) REFERENCES `bookings` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
