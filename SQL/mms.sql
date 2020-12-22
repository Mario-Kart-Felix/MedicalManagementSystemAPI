-- phpMyAdmin SQL Dump
-- version 5.0.2
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 22, 2020 at 08:07 PM
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
(194, 'Umair', 'UmairFarooqui', 'info.umairfarooqui@gmail.com', '$2y$10$mj40jM/kQRLg2Y5scscBouzna/e5I2RxyJSeRhbWjdAMm5vhg.h86', '', 1, '2020-08-01 02:12:14');

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
(19, 'OEBA', '2020-12-21 12:35:52');

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
(14, 'MAJOON', '2020-12-21 12:31:46');

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
(26, 'A4', '2020-12-21 12:31:21');

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
(52, 12, 'SHARBAT SADAR', 11, 17, 121, 5, 23, '2016-12-01', '2019-12-01', '2020-12-21 12:34:41'),
(53, 12, 'SHARBAT SUAL', 11, 19, 151, 16, 23, '2017-11-01', '2020-11-01', '2020-12-21 12:38:23'),
(54, 13, 'SYRUP LIV ON', 11, 17, 149, 1, 23, '2018-12-01', '2021-11-01', '2020-12-21 12:42:32'),
(55, 13, 'SYRUP LIV ON', 11, 17, 149, 6, 23, '2019-02-01', '2022-01-01', '2020-12-21 12:43:22'),
(56, 13, 'SYRUP LIV ON', 10, 17, 70, 4, 23, '2014-10-01', '2017-10-01', '2020-12-21 12:45:34'),
(57, 13, 'SYRUP GASTREEN', 11, 17, 149, 4, 23, '2022-01-01', '2022-12-01', '2020-12-21 12:47:51');

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
(52, 12, 'SHARBAT SADAR', 11, 17, 121, 5, 23, '2016-12-01', '2019-12-01', '2020-12-21 12:34:41'),
(53, 12, 'SHARBAT SUAL', 11, 19, 151, 16, 23, '2017-11-01', '2020-11-01', '2020-12-21 12:38:23'),
(54, 13, 'SYRUP LIV ON', 11, 17, 149, 1, 23, '2018-12-01', '2021-11-01', '2020-12-21 12:42:32'),
(55, 13, 'SYRUP LIV ON', 11, 17, 149, 6, 23, '2019-02-01', '2022-01-01', '2020-12-21 12:43:22'),
(56, 13, 'SYRUP LIV ON', 10, 17, 70, 4, 23, '2014-10-01', '2017-10-01', '2020-12-21 12:45:34'),
(57, 13, 'SYRUP GASTREEN', 11, 17, 149, 4, 23, '2022-01-01', '2022-12-01', '2020-12-21 12:47:51');

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
(403, 57, 1, 149, '2020-12-22 16:06:16', NULL),
(404, 56, 1, 70, '2020-12-22 16:06:58', NULL),
(407, 56, 1, 70, '2020-12-22 16:13:05', NULL),
(408, 55, 1, 149, '2020-12-22 18:37:10', NULL),
(409, 57, 1, 149, '2020-12-22 18:38:29', NULL),
(410, 56, 1, 70, '2020-12-22 18:38:31', NULL);

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
(10, '100ML', 0, '2020-12-21 12:30:48'),
(11, '200ML', 0, '2020-12-21 12:30:54'),
(12, '50ML', 0, '2020-12-21 12:30:59');

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
-- Indexes for table `locations`
--
ALTER TABLE `locations`
  ADD PRIMARY KEY (`location_id`);

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
  MODIFY `brand_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=20;

--
-- AUTO_INCREMENT for table `categories`
--
ALTER TABLE `categories`
  MODIFY `category_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `locations`
--
ALTER TABLE `locations`
  MODIFY `location_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=27;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `products_record`
--
ALTER TABLE `products_record`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=58;

--
-- AUTO_INCREMENT for table `quantities`
--
ALTER TABLE `quantities`
  MODIFY `quantity_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=3;

--
-- AUTO_INCREMENT for table `sells`
--
ALTER TABLE `sells`
  MODIFY `sell_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=411;

--
-- AUTO_INCREMENT for table `sizes`
--
ALTER TABLE `sizes`
  MODIFY `size_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
