-- phpMyAdmin SQL Dump
-- version 5.2.1deb3
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Nov 30, 2025 at 03:00 PM
-- Server version: 8.0.44-0ubuntu0.24.04.1
-- PHP Version: 8.3.6

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `ecommerce_2025A_maisy_baer`
--

-- --------------------------------------------------------

--
-- Table structure for table `eventify_cart`
--

CREATE TABLE `eventify_cart` (
  `event_id` int NOT NULL,
  `cart_id` int DEFAULT NULL,
  `qty` int NOT NULL,
  `customer_id` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eventify_categories`
--

CREATE TABLE `eventify_categories` (
  `cat_id` int NOT NULL,
  `cat_name` varchar(100) NOT NULL,
  `added_by` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eventify_customer`
--

CREATE TABLE `eventify_customer` (
  `customer_id` int NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_email` varchar(50) NOT NULL,
  `customer_pass` varchar(150) NOT NULL,
  `customer_country` varchar(30) NOT NULL,
  `customer_city` varchar(30) NOT NULL,
  `customer_contact` varchar(15) NOT NULL,
  `customer_image` varchar(100) DEFAULT NULL,
  `user_role` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eventify_orderdetails`
--

CREATE TABLE `eventify_orderdetails` (
  `order_id` int NOT NULL,
  `event_id` int NOT NULL,
  `qty` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eventify_orders`
--

CREATE TABLE `eventify_orders` (
  `order_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `invoice_no` varchar(50) NOT NULL,
  `order_date` date NOT NULL,
  `order_status` varchar(100) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eventify_payment`
--

CREATE TABLE `eventify_payment` (
  `pay_id` int NOT NULL,
  `amt` double NOT NULL,
  `customer_id` int NOT NULL,
  `order_id` int NOT NULL,
  `currency` text NOT NULL,
  `payment_date` date NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL COMMENT 'Payment method: paystack, cash, bank_transfer, etc.',
  `transaction_ref` varchar(100) DEFAULT NULL COMMENT 'Paystack transaction reference',
  `authorization_code` varchar(100) DEFAULT NULL COMMENT 'Authorization code from payment gateway',
  `payment_channel` varchar(50) DEFAULT NULL COMMENT 'Payment channel: card, mobile_money, etc.'
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eventify_payment_init`
--

CREATE TABLE `eventify_payment_init` (
  `id` int NOT NULL,
  `customer_id` int NOT NULL,
  `reference` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `eventify_products`
--

CREATE TABLE `eventify_products` (
  `event_id` int NOT NULL,
  `event_cat` int NOT NULL,
  `event_desc` varchar(200) DEFAULT NULL,
  `event_price` double NOT NULL,
  `event_location` varchar(500) NOT NULL,
  `event_date` date NOT NULL,
  `event_start` time NOT NULL,
  `event_end` time NOT NULL,
  `flyer` varchar(100) DEFAULT NULL,
  `event_keywords` varchar(100) DEFAULT NULL,
  `added_by` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eventify_subscriptions`
--

CREATE TABLE `eventify_subscriptions` (
  `subscription_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `subscription_type` varchar(50) COLLATE utf8mb4_general_ci DEFAULT 'analytics_premium',
  `status` enum('active','expired','cancelled','pending') COLLATE utf8mb4_general_ci DEFAULT 'pending',
  `start_date` date NOT NULL,
  `end_date` date NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `currency` varchar(10) COLLATE utf8mb4_general_ci DEFAULT 'GHS',
  `payment_reference` varchar(255) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `invoice_no` varchar(100) COLLATE utf8mb4_general_ci DEFAULT NULL,
  `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `eventify_vendor`
--

CREATE TABLE `eventify_vendor` (
  `vendor_id` int NOT NULL,
  `vendor_desc` varchar(150) NOT NULL,
  `vendor_type` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

-- --------------------------------------------------------

--
-- Table structure for table `eventify_vendor_bookings`
--

CREATE TABLE `eventify_vendor_bookings` (
  `booking_id` int NOT NULL,
  `booking_type` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'customer_to_vendor' COMMENT 'customer_to_vendor or vendor_to_event',
  `vendor_id` int NOT NULL,
  `customer_id` int NOT NULL,
  `event_id` int NOT NULL,
  `booking_date` datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `booking_status` varchar(50) COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'pending',
  `approved_by_vendor` tinyint(1) DEFAULT '1' COMMENT '1 if vendor approved (for vendor_to_event requests)',
  `approved_by_event_manager` tinyint(1) DEFAULT '0' COMMENT '1 if event manager approved (for vendor_to_event requests)',
  `event_manager_approved_date` datetime DEFAULT NULL COMMENT 'When event manager approved/rejected the request',
  `service_date` date DEFAULT NULL,
  `notes` text COLLATE utf8mb4_general_ci,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

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
-- Indexes for table `eventify_payment_init`
--
ALTER TABLE `eventify_payment_init`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `reference` (`reference`),
  ADD KEY `customer_id` (`customer_id`),
  ADD KEY `created_at` (`created_at`),
  ADD KEY `idx_reference_lookup` (`reference`,`created_at`);

--
-- Indexes for table `eventify_products`
--
ALTER TABLE `eventify_products`
  ADD PRIMARY KEY (`event_id`),
  ADD KEY `event_cat` (`event_cat`) USING BTREE,
  ADD KEY `fk_products_added_by` (`added_by`);

--
-- Indexes for table `eventify_subscriptions`
--
ALTER TABLE `eventify_subscriptions`
  ADD PRIMARY KEY (`subscription_id`),
  ADD KEY `idx_customer_status` (`customer_id`,`status`),
  ADD KEY `idx_end_date` (`end_date`),
  ADD KEY `idx_subscription_type` (`subscription_type`);

--
-- Indexes for table `eventify_vendor`
--
ALTER TABLE `eventify_vendor`
  ADD PRIMARY KEY (`vendor_id`);

--
-- Indexes for table `eventify_vendor_bookings`
--
ALTER TABLE `eventify_vendor_bookings`
  ADD PRIMARY KEY (`booking_id`),
  ADD KEY `fk_vendor_booking_vendor` (`vendor_id`),
  ADD KEY `fk_vendor_booking_customer` (`customer_id`),
  ADD KEY `fk_vendor_booking_event` (`event_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `eventify_categories`
--
ALTER TABLE `eventify_categories`
  MODIFY `cat_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eventify_customer`
--
ALTER TABLE `eventify_customer`
  MODIFY `customer_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eventify_orders`
--
ALTER TABLE `eventify_orders`
  MODIFY `order_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eventify_payment`
--
ALTER TABLE `eventify_payment`
  MODIFY `pay_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eventify_payment_init`
--
ALTER TABLE `eventify_payment_init`
  MODIFY `id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eventify_products`
--
ALTER TABLE `eventify_products`
  MODIFY `event_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eventify_subscriptions`
--
ALTER TABLE `eventify_subscriptions`
  MODIFY `subscription_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eventify_vendor`
--
ALTER TABLE `eventify_vendor`
  MODIFY `vendor_id` int NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eventify_vendor_bookings`
--
ALTER TABLE `eventify_vendor_bookings`
  MODIFY `booking_id` int NOT NULL AUTO_INCREMENT;

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
  ADD CONSTRAINT `fk_products_added_by` FOREIGN KEY (`added_by`) REFERENCES `eventify_customer` (`customer_id`);

--
-- Constraints for table `eventify_subscriptions`
--
ALTER TABLE `eventify_subscriptions`
  ADD CONSTRAINT `eventify_subscriptions_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `eventify_customer` (`customer_id`) ON DELETE CASCADE;

--
-- Constraints for table `eventify_vendor_bookings`
--
ALTER TABLE `eventify_vendor_bookings`
  ADD CONSTRAINT `fk_vendor_booking_customer` FOREIGN KEY (`customer_id`) REFERENCES `eventify_customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_vendor_booking_event` FOREIGN KEY (`event_id`) REFERENCES `eventify_products` (`event_id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `fk_vendor_booking_vendor` FOREIGN KEY (`vendor_id`) REFERENCES `eventify_customer` (`customer_id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;