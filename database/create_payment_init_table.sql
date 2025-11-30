-- Create table to store payment initialization data as backup for session loss
-- This helps prevent "Payment amount does not match" errors when sessions are lost on server

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
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add index for faster lookups
ALTER TABLE `eventify_payment_init`
ADD INDEX `idx_reference_lookup` (`reference`, `created_at`);

-- Optional: Add cleanup job to remove old records (older than 24 hours)
-- You can run this periodically via a cron job
-- DELETE FROM eventify_payment_init WHERE created_at < DATE_SUB(NOW(), INTERVAL 24 HOUR);
