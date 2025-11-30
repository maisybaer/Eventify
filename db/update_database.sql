-- Database updates for Paystack payment flow
-- Run this file to update your database structure

-- 1. Fix invoice_no to accept varchar instead of int
ALTER TABLE `eventify_orders`
MODIFY COLUMN `invoice_no` VARCHAR(50) NOT NULL;

-- 2. Create payment initialization tracking table
CREATE TABLE IF NOT EXISTS `eventify_payment_init` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `customer_id` int(11) NOT NULL,
  `reference` varchar(100) NOT NULL,
  `amount` decimal(10,2) NOT NULL,
  `email` varchar(255) NOT NULL,
  `created_at` datetime NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `reference` (`reference`),
  KEY `customer_id` (`customer_id`),
  KEY `created_at` (`created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 3. Ensure payment table has correct indexes for transaction_ref
-- (Already exists in your schema, but adding for completeness)
ALTER TABLE `eventify_payment`
ADD INDEX IF NOT EXISTS `idx_transaction_ref` (`transaction_ref`);

-- 4. Update existing orders with placeholder invoice numbers if they have 0
UPDATE `eventify_orders`
SET `invoice_no` = CONCAT('INV-', DATE_FORMAT(order_date, '%Y%m%d'), '-', LPAD(order_id, 6, '0'))
WHERE `invoice_no` = '0' OR `invoice_no` = 0;
