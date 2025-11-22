-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 22, 2025 at 08:23 PM
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
-- Database: `eventify`
--

-- --------------------------------------------------------

--
-- Table structure for table `eventify_cart`
--

CREATE TABLE `eventify_cart` (
  `event_id` int(11) NOT NULL,
  `cart_id` int(11) DEFAULT NULL,
  `qty` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `eventify_cart`
--

INSERT INTO `eventify_cart` (`event_id`, `cart_id`, `qty`, `customer_id`) VALUES
(13, NULL, 4, 9),
(14, NULL, 2, 2);

-- --------------------------------------------------------

--
-- Table structure for table `eventify_categories`
--

CREATE TABLE `eventify_categories` (
  `cat_id` int(11) NOT NULL,
  `cat_name` varchar(100) NOT NULL,
  `added_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--

-- --------------------------------------------------------

--
-- Table structure for table `eventify_customer`
--

CREATE TABLE `eventify_customer` (
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(50) NOT NULL,
  `customer_pass` varchar(150) NOT NULL,
  `customer_country` varchar(30) NOT NULL,
  `customer_city` varchar(30) NOT NULL,
  `customer_contact` varchar(15) NOT NULL,
  `customer_image` varchar(100) DEFAULT NULL,
  `user_role` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;


-- --------------------------------------------------------

--
-- Table structure for table `eventify_orderdetails`
--

CREATE TABLE `eventify_orderdetails` (
  `order_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `eventify_orders`
--

CREATE TABLE `eventify_orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `invoice_no` int(11) NOT NULL,
  `order_date` date NOT NULL,
  `order_status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `eventify_payment`
--

CREATE TABLE `eventify_payment` (
  `pay_id` int(11) NOT NULL,
  `amt` double NOT NULL,
  `customer_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `currency` text NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL COMMENT 'Payment method: paystack, cash, bank_transfer, etc.',
  `transaction_ref` varchar(100) DEFAULT NULL COMMENT 'Paystack transaction reference',
  `authorization_code` varchar(100) DEFAULT NULL COMMENT 'Authorization code from payment gateway',
  `payment_channel` varchar(50) DEFAULT NULL COMMENT 'Payment channel: card, mobile_money, etc.'
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

-- --------------------------------------------------------

--
-- Table structure for table `eventify_products`
--

CREATE TABLE `eventify_products` (
  `event_id` int(11) NOT NULL,
  `event_cat` int(11) NOT NULL,
  `event_desc` varchar(200) DEFAULT NULL,
  `event_price` double NOT NULL,
  `event_location` varchar(500) NOT NULL,
  `event_start` time NOT NULL,
  `event_end` time NOT NULL,
  `flyer` varchar(100) DEFAULT NULL,
  `event_keywords` varchar(100) DEFAULT NULL,
  `added_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- --------------------------------------------------------

--
-- Table structure for table `eventify_vendor`
--

CREATE TABLE `eventify_vendor` (
  `vendor_id` int(11) NOT NULL,
  `vendor_desc` varchar(150) NOT NULL,
  `vendor_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `eventify_cart`
--
ALTER TABLE `eventify_cart`
  ADD KEY `event_id` (`event_id`) USING BTREE,
  ADD KEY `customer_id` (`cart_id`) USING BTREE;

--
-- Indexes for table `eventify_categories`
--
ALTER TABLE `eventify_categories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indexes for table `eventify_customer`
--
ALTER TABLE `eventify_customer`
  ADD PRIMARY KEY (`customer_id`),
  ADD UNIQUE KEY `customer_email` (`customer_email`);

--
-- Indexes for table `eventify_orderdetails`
--
ALTER TABLE `eventify_orderdetails`
  ADD KEY `order_id` (`order_id`),
  ADD KEY `event_id` (`event_id`) USING BTREE;

--
-- Indexes for table `eventify_orders`
--
ALTER TABLE `eventify_orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `eventify_payment`
--
ALTER TABLE `eventify_payment`
  ADD PRIMARY KEY (`pay_id`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `order_id` (`order_id`),
  ADD KEY `idx_transaction_ref` (`transaction_ref`),
  ADD KEY `idx_payment_method` (`payment_method`);

--
-- Indexes for table `eventify_products`
--
ALTER TABLE `eventify_products`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `event_cat` (`event_cat`) USING BTREE;

--
-- Indexes for table `eventify_vendor`
--
ALTER TABLE `eventify_vendor`
  ADD PRIMARY KEY (`vendor_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `eventify_categories`
--
ALTER TABLE `eventify_categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `eventify_customer`
--
ALTER TABLE `eventify_customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `eventify_orders`
--
ALTER TABLE `eventify_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eventify_payment`
--
ALTER TABLE `eventify_payment`
  MODIFY `pay_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eventify_products`
--
ALTER TABLE `eventify_products`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `eventify_vendor`
--
ALTER TABLE `eventify_vendor`
  MODIFY `vendor_id` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `eventify_cart`
--
ALTER TABLE `eventify_cart`
  ADD CONSTRAINT `eventify_cart_ibfk_1` FOREIGN KEY (`event_id`) REFERENCES `eventify_products` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `eventify_cart_ibfk_2` FOREIGN KEY (`cart_id`) REFERENCES `eventify_customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Constraints for table `eventify_orderdetails`
--
ALTER TABLE `eventify_orderdetails`
  ADD CONSTRAINT `eventify_orderdetails_ibfk_1` FOREIGN KEY (`order_id`) REFERENCES `eventify_orders` (`order_id`),
  ADD CONSTRAINT `eventify_orderdetails_ibfk_2` FOREIGN KEY (`event_id`) REFERENCES `eventify_products` (`event_id`);

--
-- Constraints for table `eventify_orders`
--
ALTER TABLE `eventify_orders`
  ADD CONSTRAINT `eventify_orders_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `eventify_customer` (`customer_id`);

--
-- Constraints for table `eventify_payment`
--
ALTER TABLE `eventify_payment`
  ADD CONSTRAINT `eventify_payment_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `eventify_customer` (`customer_id`),
  ADD CONSTRAINT `eventify_payment_ibfk_2` FOREIGN KEY (`order_id`) REFERENCES `eventify_orders` (`order_id`);

--
-- Constraints for table `eventify_products`
--
ALTER TABLE `eventify_products`
  ADD CONSTRAINT `eventify_products_ibfk_1` FOREIGN KEY (`event_cat`) REFERENCES `eventify_categories` (`cat_id`),
  ADD CONSTRAINT `eventify_products_ibfk_2` FOREIGN KEY (`product_brand`) REFERENCES `events` (`event_id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;