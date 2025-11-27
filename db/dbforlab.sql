-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Nov 27, 2025 at 02:26 PM
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
-- Table structure for table `cart`
--

CREATE TABLE `cart` (
  `cart_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `cart`
--

INSERT INTO `cart` (`cart_id`, `event_id`, `customer_id`, `qty`) VALUES
(1, 35, 2, 1);

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
-- Dumping data for table `eventify_categories`
--

INSERT INTO `eventify_categories` (`cat_id`, `cat_name`, `added_by`) VALUES
(4, 'Party', 2),
(6, 'Brunch', 2),
(12, 'Lunch', 2),
(13, 'Concert', 2);

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

--
-- Dumping data for table `eventify_customer`
--

INSERT INTO `eventify_customer` (`customer_id`, `customer_name`, `customer_email`, `customer_pass`, `customer_country`, `customer_city`, `customer_contact`, `customer_image`, `user_role`) VALUES
(1, 'Ama Mensah', 'test@email.com', '$2y$10$nrZ1BwLi8CA7RjQL6XXJGePrQogh2YqorQGRmAmMleEdZvv7gQnBe', 'Ghana', 'Accra', '0245678910', '../uploads/IMG_68d5750bbeac23.83433048.jpg', 2),
(2, 'Admin', 'admin@email.com', '$2y$10$do.UHBOipK1WtcNKglOX7O.osYZNrCxcbO9wu.ngT2zgg/7Nn9/yy', 'Ghana', 'Accra', '0000000000', NULL, 1),
(3, 'Lisa', 'lisa@email.com', '$2y$10$hDh338Un1el05ORz5DWys.OgzzBQwqrzo0EAJOZIuBRIF8EtJnaOC', 'Ghana', 'Accra', '0000000000', NULL, 1),
(4, 'Kofi Mensah', 'kofi@email.com', '$2y$10$vVH4Ybn9ThLukynyL4sWKeYDCgNgLDgYP/y3kw4jL1RG2smU1fTjy', 'Ghana', 'Accra', '0000000000', '../uploads/IMG_68fde21ec92227.29789876.jpg', 2),
(7, 'Test', 'test@test.com', '$2y$10$j.qy8t8xHZCgsfnUF0qB8uEmzht9PQ7jtDAj1uAymZjUIizVlFX/i', 'Ghana', 'Accra', '0000000000', NULL, 1),
(8, 'Test', 'test@gmail.com', '$2y$10$TKIOvDd.mKewZ9KF.TvWX.VJ07DgjfPZus1o.r7aS3131sLlTr1k.', 'Ghana', 'Accra', '0000000000', NULL, 2),
(9, 'Nana', 'nana@gmail.com', '$2y$10$DjOgc77hjFFm/SXdHzNkcOpIH7bbS0UFMZKgXsMa/3gnvk0bUb8xG', 'Ghana', 'Accra', '0000000000', NULL, 2),
(10, 'Mimosami', 'mimosami@email.com', '$2y$10$Ad2RVagWBxT8MTy/aidiC.IHxIlNMMrtpy3HtGTwiR7XZ.Nb5Sshy', 'Ghana', 'Accra', '0000000000', NULL, 2);

-- --------------------------------------------------------

--
-- Table structure for table `eventify_orderdetails`
--

CREATE TABLE `eventify_orderdetails` (
  `order_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `qty` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `eventify_orderdetails`
--

INSERT INTO `eventify_orderdetails` (`order_id`, `event_id`, `qty`) VALUES
(2, 12, 3),
(2, 14, 2),
(3, 14, 1),
(4, 12, 1);

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

--
-- Dumping data for table `eventify_orders`
--

INSERT INTO `eventify_orders` (`order_id`, `customer_id`, `invoice_no`, `order_date`, `order_status`) VALUES
(1, 2, 0, '2025-11-25', 'Paid'),
(2, 2, 0, '2025-11-25', 'Paid'),
(3, 2, 0, '2025-11-25', 'Paid'),
(4, 2, 0, '2025-11-25', 'Paid'),
(5, 2, 0, '2025-11-26', 'Paid');

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

--
-- Dumping data for table `eventify_payment`
--

INSERT INTO `eventify_payment` (`pay_id`, `amt`, `customer_id`, `order_id`, `currency`, `payment_date`, `payment_method`, `transaction_ref`, `authorization_code`, `payment_channel`) VALUES
(1, 120.75, 2, 2, 'GHS', '2025-11-25', NULL, NULL, NULL, NULL),
(2, 34.5, 2, 3, 'GHS', '2025-11-25', NULL, NULL, NULL, NULL),
(3, 17.25, 2, 4, 'GHS', '2025-11-25', NULL, NULL, NULL, NULL);

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
  `event_date` date NOT NULL,
  `event_start` time NOT NULL,
  `event_end` time NOT NULL,
  `flyer` varchar(100) DEFAULT NULL,
  `event_keywords` varchar(100) DEFAULT NULL,
  `added_by` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=latin1 COLLATE=latin1_swedish_ci;

--
-- Dumping data for table `eventify_products`
--

INSERT INTO `eventify_products` (`event_id`, `event_cat`, `event_desc`, `event_price`, `event_location`, `event_date`, `event_start`, `event_end`, `flyer`, `event_keywords`, `added_by`) VALUES
(35, 13, 'Afronation 2025', 150, 'Laboma', '2025-11-27', '12:00:00', '23:00:00', 'IMG_69272b2277b543.73843693.jpg', 'music, concert', 2),
(36, 4, 'Tanks and Bikinis', 100, 'Laboma', '2025-11-30', '12:00:00', '22:00:00', 'IMG_69272bad836dc2.23613864.jpeg', 'music, party, beach', 2);

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
-- Dumping data for table `eventify_vendor`
--

INSERT INTO `eventify_vendor` (`vendor_id`, `vendor_desc`, `vendor_type`) VALUES
(1, 'Mimosami', 'default');

-- --------------------------------------------------------

--
-- Table structure for table `eventify_vendor_bookings`
--

CREATE TABLE `eventify_vendor_bookings` (
  `booking_id` int(11) NOT NULL,
  `vendor_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `event_id` int(11) NOT NULL,
  `booking_date` datetime NOT NULL DEFAULT current_timestamp(),
  `booking_status` varchar(50) NOT NULL DEFAULT 'pending',
  `service_date` date DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `price` decimal(10,2) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `eventify_vendor_bookings`
--

INSERT INTO `eventify_vendor_bookings` (`booking_id`, `vendor_id`, `customer_id`, `event_id`, `booking_date`, `booking_status`, `service_date`, `notes`, `price`) VALUES
(1, 10, 2, 35, '2025-11-27 12:31:29', 'pending', NULL, NULL, NULL),
(2, 10, 2, 35, '2025-11-27 12:31:29', 'pending', NULL, NULL, NULL),
(3, 10, 2, 35, '2025-11-27 12:31:29', 'cancelled', NULL, NULL, NULL);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `cart`
--
ALTER TABLE `cart`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `event_id` (`event_id`),
  ADD KEY `customer_id` (`customer_id`);

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
  ADD KEY `event_cat` (`event_cat`) USING BTREE,
  ADD KEY `fk_products_added_by` (`added_by`);

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
-- AUTO_INCREMENT for table `cart`
--
ALTER TABLE `cart`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `eventify_categories`
--
ALTER TABLE `eventify_categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=14;

--
-- AUTO_INCREMENT for table `eventify_customer`
--
ALTER TABLE `eventify_customer`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `eventify_orders`
--
ALTER TABLE `eventify_orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `eventify_payment`
--
ALTER TABLE `eventify_payment`
  MODIFY `pay_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT for table `eventify_products`
--
ALTER TABLE `eventify_products`
  MODIFY `event_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=37;

--
-- AUTO_INCREMENT for table `eventify_vendor`
--
ALTER TABLE `eventify_vendor`
  MODIFY `vendor_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT for table `eventify_vendor_bookings`
--
ALTER TABLE `eventify_vendor_bookings`
  MODIFY `booking_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `cart`
--
ALTER TABLE `cart`
  ADD CONSTRAINT `cart_customer_fk` FOREIGN KEY (`customer_id`) REFERENCES `eventify_customer` (`customer_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `cart_event_fk` FOREIGN KEY (`event_id`) REFERENCES `eventify_products` (`event_id`) ON DELETE CASCADE;

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