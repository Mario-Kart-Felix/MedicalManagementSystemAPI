-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jan 19, 2021 at 08:08 AM
-- Server version: 10.4.11-MariaDB
-- PHP Version: 7.4.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `azmiunanistore`
--

-- --------------------------------------------------------

--
-- Table structure for table `admin`
--

CREATE TABLE `admin` (
  `id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `username` varchar(20) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(100) NOT NULL,
  `image` varchar(200) NOT NULL,
  `status` tinyint(5) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `admin`
--

INSERT INTO `admin` (`id`, `name`, `username`, `email`, `password`, `image`, `status`, `timestamp`) VALUES
(2, 'Azmi Unani Store', 'azmiunanistore', 'azmiunanistore@gmail.com', '$2y$10$mj40jM/kQRLg2Y5scscBouzna/e5I2RxyJSeRhbWjdAMm5vhg.h86', '', 1, '2020-12-21 10:05:47'),
(194, 'Umair', 'Umair Farooqui', 'info.umairfarooqui@gmail.com', '$2y$10$mj40jM/kQRLg2Y5scscBouzna/e5I2RxyJSeRhbWjdAMm5vhg.h86', '', 1, '2021-01-06 16:50:53');

-- --------------------------------------------------------

--
-- Table structure for table `brands`
--

CREATE TABLE `brands` (
  `brand_id` int(11) NOT NULL,
  `brand_name` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `brands`
--

INSERT INTO `brands` (`brand_id`, `brand_name`, `created_at`) VALUES
(17, 'DEHLVI', '2020-12-21 12:30:15'),
(18, 'HAMDARD', '2020-12-21 12:31:29'),
(19, 'OEBA', '2020-12-21 12:35:52'),
(20, 'MEGHDOOT', '2021-01-01 15:34:40'),
(21, 'SHAMA', '2021-01-02 16:02:15'),
(22, 'SADAR', '2021-01-03 13:45:54'),
(23, 'HBM', '2021-01-06 13:08:19'),
(24, 'FHC', '2021-01-11 15:47:52'),
(25, 'AMC', '2021-01-16 16:37:28'),
(27, 'SOCIAL CODIA', '2021-01-19 07:01:02');

-- --------------------------------------------------------

--
-- Table structure for table `categories`
--

CREATE TABLE `categories` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `categories`
--

INSERT INTO `categories` (`category_id`, `category_name`, `created_at`) VALUES
(12, 'SHARBAT', '2020-12-21 12:30:33'),
(13, 'SYRUP', '2020-12-21 12:31:41'),
(14, 'MAJOON', '2020-12-21 12:31:46'),
(15, 'SHAMPOO', '2021-01-01 15:37:32'),
(16, 'ROGHAN', '2021-01-01 16:00:30'),
(17, 'TABLET', '2021-01-02 15:52:14'),
(18, 'HABBE', '2021-01-07 14:23:50'),
(19, 'CAPSULE', '2021-01-11 12:21:09'),
(20, 'CHOORAN', '2021-01-11 15:48:27'),
(21, 'KHAMIRA', '2021-01-12 16:16:23'),
(22, 'ARAQ', '2021-01-17 11:25:39'),
(23, 'PROGRAMMING', '2021-01-19 07:01:09');

-- --------------------------------------------------------

--
-- Table structure for table `invoices`
--

CREATE TABLE `invoices` (
  `invoice_id` int(11) NOT NULL,
  `invoice_number` varchar(100) NOT NULL,
  `seller_id` int(100) NOT NULL,
  `invoice_date` date NOT NULL,
  `invoice_url` varchar(600) NOT NULL,
  `total_amount` int(100) NOT NULL DEFAULT 0,
  `paid_amount` int(100) NOT NULL DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `invoices`
--

INSERT INTO `invoices` (`invoice_id`, `invoice_number`, `seller_id`, `invoice_date`, `invoice_url`, `total_amount`, `paid_amount`, `created_at`) VALUES
(87, 'FHC10001', 12, '2021-01-19', 'uploads/invoices/FHC1000127637035861.pdf', 0, 0, '2021-01-19 07:07:48');

-- --------------------------------------------------------

--
-- Table structure for table `locations`
--

CREATE TABLE `locations` (
  `location_id` int(11) NOT NULL,
  `location_name` varchar(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `locations`
--

INSERT INTO `locations` (`location_id`, `location_name`, `created_at`) VALUES
(23, 'A1', '2020-12-21 12:31:10'),
(24, 'A2', '2020-12-21 12:31:14'),
(25, 'A3', '2020-12-21 12:31:18'),
(26, 'A4', '2020-12-21 12:31:21'),
(27, 'D1', '2021-01-01 15:39:11'),
(28, 'D2', '2021-01-01 15:39:13'),
(29, 'D3', '2021-01-01 15:39:16'),
(30, 'B1', '2021-01-02 14:33:37'),
(31, 'B2', '2021-01-02 14:33:41'),
(32, 'C1', '2021-01-04 08:12:41'),
(33, 'D3', '2021-01-04 08:12:51'),
(34, 'D4', '2021-01-04 08:12:53'),
(35, 'E1', '2021-01-04 08:12:58'),
(36, 'E2', '2021-01-04 08:13:00'),
(37, 'F1', '2021-01-04 08:13:02'),
(38, 'F2', '2021-01-04 08:13:06'),
(39, 'F3', '2021-01-04 08:13:09'),
(40, 'C2', '2021-01-07 14:24:33'),
(42, 'UNDEFINED', '2021-01-11 15:48:04'),
(43, 'D5', '2021-01-17 11:26:32'),
(44, 'D6', '2021-01-17 11:26:34');

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `payment_mode` varchar(100) NOT NULL,
  `payment_date` datetime NOT NULL,
  `payment_amount` int(100) NOT NULL,
  `payment_receiver` int(200) NOT NULL,
  `invoice_number` varchar(200) NOT NULL,
  `seller_id` int(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(200) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `size_id` int(200) NOT NULL,
  `brand_id` int(200) NOT NULL,
  `product_price` int(200) NOT NULL,
  `product_quantity` int(200) NOT NULL,
  `location_id` int(200) NOT NULL,
  `product_manufacture` date NOT NULL,
  `product_expire` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `product_name`, `size_id`, `brand_id`, `product_price`, `product_quantity`, `location_id`, `product_manufacture`, `product_expire`, `created_at`) VALUES
(371, 23, 'ANDROID APPLICATION DEVELOPMENT', 26, 27, 300, 100, 24, '2018-07-01', '2024-08-01', '2021-01-19 07:02:09'),
(372, 16, 'FAROOQUI MESSAGE OIL', 17, 24, 280, 500, 23, '2022-02-01', '2024-03-01', '2021-01-19 07:03:18'),
(373, 16, 'FAROOQUI MESSAGE OIL', 15, 24, 80, 200, 23, '2022-02-01', '2024-03-01', '2021-01-19 07:03:37');

-- --------------------------------------------------------

--
-- Table structure for table `products_record`
--

CREATE TABLE `products_record` (
  `product_id` int(11) NOT NULL,
  `category_id` int(200) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `size_id` int(200) NOT NULL,
  `brand_id` int(200) NOT NULL,
  `product_price` int(200) NOT NULL,
  `product_quantity` int(200) NOT NULL,
  `location_id` int(200) NOT NULL,
  `product_manufacture` date NOT NULL,
  `product_expire` date NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `products_record`
--

INSERT INTO `products_record` (`product_id`, `category_id`, `product_name`, `size_id`, `brand_id`, `product_price`, `product_quantity`, `location_id`, `product_manufacture`, `product_expire`, `created_at`) VALUES
(371, 23, 'ANDROID APPLICATION DEVELOPMENT', 26, 27, 300, 100, 24, '2018-07-01', '2024-08-01', '2021-01-19 07:02:09'),
(372, 16, 'FAROOQUI MESSAGE OIL', 17, 24, 280, 500, 23, '2022-02-01', '2024-03-01', '2021-01-19 07:03:18'),
(373, 16, 'FAROOQUI MESSAGE OIL', 15, 24, 80, 200, 23, '2022-02-01', '2024-03-01', '2021-01-19 07:03:37');

-- --------------------------------------------------------

--
-- Table structure for table `quantities`
--

CREATE TABLE `quantities` (
  `quantity_id` int(11) NOT NULL,
  `quantity` int(200) NOT NULL,
  `product_id` int(200) NOT NULL,
  `size_id` int(200) NOT NULL,
  `brand_id` int(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- --------------------------------------------------------

--
-- Table structure for table `sellers`
--

CREATE TABLE `sellers` (
  `seller_id` int(11) NOT NULL,
  `seller_fname` varchar(50) NOT NULL,
  `seller_lname` varchar(50) NOT NULL,
  `seller_email` varchar(50) NOT NULL,
  `seller_contact` varchar(12) NOT NULL,
  `seller_contact_1` varchar(12) NOT NULL,
  `seller_image` varchar(100) NOT NULL,
  `seller_address` varchar(1000) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sellers`
--

INSERT INTO `sellers` (`seller_id`, `seller_fname`, `seller_lname`, `seller_email`, `seller_contact`, `seller_contact_1`, `seller_image`, `seller_address`) VALUES
(12, 'UMAIR', 'FAROOQUI', 'SOCIALCODIA@GMAIL.COM', '9867503256', '7506597967', 'uploads/6006848de4f00.png', 'KHARDI VILLAGE ROAD, KAUSA, MUMBRA');

-- --------------------------------------------------------

--
-- Table structure for table `sellers_sells`
--

CREATE TABLE `sellers_sells` (
  `sellers_sell_id` int(11) NOT NULL,
  `invoice_number` varchar(100) NOT NULL,
  `product_id` int(200) NOT NULL,
  `sell_quantity` int(200) NOT NULL,
  `sell_discount` float NOT NULL,
  `sell_price` int(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sellers_sells`
--

INSERT INTO `sellers_sells` (`sellers_sell_id`, `invoice_number`, `product_id`, `sell_quantity`, `sell_discount`, `sell_price`, `created_at`, `updated_at`) VALUES
(11, 'FHC10001', 371, 10, 40, 1800, '2021-01-19 07:04:55', NULL),
(12, 'FHC10001', 372, 100, 40, 16800, '2021-01-19 07:04:59', NULL),
(13, 'FHC10001', 373, 150, 40, 7200, '2021-01-19 07:05:02', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sells`
--

CREATE TABLE `sells` (
  `sell_id` int(11) NOT NULL,
  `product_id` int(200) NOT NULL,
  `sell_quantity` int(200) NOT NULL,
  `sell_price` int(200) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `updated_at` timestamp NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sells`
--

INSERT INTO `sells` (`sell_id`, `product_id`, `sell_quantity`, `sell_price`, `created_at`, `updated_at`) VALUES
(483, 371, 10, 1800, '2021-01-19 07:02:14', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `sizes`
--

CREATE TABLE `sizes` (
  `size_id` int(11) NOT NULL,
  `size_name` varchar(200) NOT NULL,
  `size_type` int(11) NOT NULL,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

--
-- Dumping data for table `sizes`
--

INSERT INTO `sizes` (`size_id`, `size_name`, `size_type`, `created_at`) VALUES
(14, '25ML', 0, '2020-12-28 12:39:36'),
(15, '50ML', 0, '2020-12-28 12:39:39'),
(16, '100ML', 0, '2020-12-28 12:39:42'),
(17, '200ML', 0, '2020-12-28 12:39:46'),
(18, '500ML', 0, '2020-12-28 12:39:52'),
(19, '300ML', 0, '2021-01-01 15:35:49'),
(20, '40 TABLET', 0, '2021-01-02 16:03:01'),
(21, '50 TABLET', 0, '2021-01-02 16:06:34'),
(22, '100 TABLET', 0, '2021-01-02 16:07:43'),
(23, '125GM', 0, '2021-01-04 08:03:41'),
(24, '150GM', 0, '2021-01-04 08:34:44'),
(25, '300GM', 0, '2021-01-05 11:23:25'),
(26, '200GM', 0, '2021-01-06 12:10:34'),
(27, '250GM', 0, '2021-01-06 14:50:26'),
(28, '500GM', 0, '2021-01-06 16:20:08'),
(29, '50 PILLS', 0, '2021-01-07 14:23:05'),
(30, '100 pills', 0, '2021-01-09 16:17:23'),
(31, '25 PILLS', 0, '2021-01-10 09:29:47'),
(32, '15 PILLS', 0, '2021-01-10 09:33:32'),
(33, '10 PILLS', 0, '2021-01-10 12:02:42'),
(34, '5 PILLS', 0, '2021-01-10 12:18:18'),
(35, '20 PILLS', 0, '2021-01-10 12:44:47'),
(36, '30 PILLS', 0, '2021-01-10 13:23:13'),
(37, '40 PILLS', 0, '2021-01-10 16:01:21'),
(38, '20ML', 0, '2021-01-11 12:15:25'),
(39, '50 CAPSULE', 0, '2021-01-11 12:21:34'),
(40, '50GM', 0, '2021-01-11 15:49:53'),
(41, '100GM', 0, '2021-01-11 15:49:57'),
(42, '60GM', 0, '2021-01-12 14:58:14'),
(43, '75GM', 0, '2021-01-12 16:19:02'),
(44, '60 PILLS', 0, '2021-01-13 10:53:01'),
(45, '120 PILLS', 0, '2021-01-13 10:54:54'),
(46, '60 TABLET', 0, '2021-01-13 11:05:49');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `admin`
--
ALTER TABLE `admin`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `brands`
--
ALTER TABLE `brands`
  ADD PRIMARY KEY (`brand_id`);

--
-- Indexes for table `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `invoices`
--
ALTER TABLE `invoices`
  ADD PRIMARY KEY (`invoice_id`);

--
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`location_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `products_record`
--
ALTER TABLE `products_record`
  ADD PRIMARY KEY (`product_id`);

--
-- Indexes for table `quantities`
--
ALTER TABLE `quantities`
  ADD PRIMARY KEY (`quantity_id`);

--
-- Indexes for table `sellers`
--
ALTER TABLE `sellers`
  ADD PRIMARY KEY (`seller_id`);

--
-- Indexes for table `sellers_sells`
--
ALTER TABLE `sellers_sells`
  ADD PRIMARY KEY (`sellers_sell_id`);

--
-- Indexes for table `sells`
--
ALTER TABLE `sells`
  ADD PRIMARY KEY (`sell_id`);

--
-- Indexes for table `sizes`
--
ALTER TABLE `sizes`
  ADD PRIMARY KEY (`size_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `admin`
--
ALTER TABLE `admin`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=204;

--
-- AUTO_INCREMENT for table `brands`
--
ALTER TABLE `brands`
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `invoices`
--
ALTER TABLE `invoices`
  MODIFY `invoice_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=88;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=45;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=374;

--
-- AUTO_INCREMENT for table `products_record`
--
ALTER TABLE `products_record`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=374;

--
-- AUTO_INCREMENT for table `quantities`
--
ALTER TABLE `quantities`
  MODIFY `quantity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sellers`
--
ALTER TABLE `sellers`
  MODIFY `seller_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `sellers_sells`
--
ALTER TABLE `sellers_sells`
  MODIFY `sellers_sell_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `sells`
--
ALTER TABLE `sells`
  MODIFY `sell_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=484;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `size_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=47;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
