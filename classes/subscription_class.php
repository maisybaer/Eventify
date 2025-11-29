<?php
/**
 * Subscription Class
 * Handles premium subscription database operations
 */

require_once dirname(__FILE__) . '/../settings/db_class.php';

class Subscription extends db_connection
{
    /**
     * Create a new subscription
     */
    public function createSubscription($customer_id, $subscription_type, $amount, $currency = 'GHS')
    {
        if (!$this->db) $this->db_connect();

        $sql = "INSERT INTO eventify_subscriptions
                (customer_id, subscription_type, status, start_date, end_date, amount, currency)
                VALUES (?, ?, 'pending', CURDATE(), DATE_ADD(CURDATE(), INTERVAL 30 DAY), ?, ?)";

        $stmt = $this->db->prepare($sql);
        if (!$stmt) {
            error_log("Prepare failed: " . $this->db->error);
            return false;
        }

        $stmt->bind_param("isds", $customer_id, $subscription_type, $amount, $currency);

        if ($stmt->execute()) {
            $inserted_id = $this->db->insert_id;
            error_log("Subscription created successfully with ID: $inserted_id");
            return $inserted_id;
        } else {
            error_log("Execute failed: " . $stmt->error);
            return false;
        }
    }

    /**
     * Get active subscription for a customer
     */
    public function getActiveSubscription($customer_id, $subscription_type = 'analytics_premium')
    {
        if (!$this->db) $this->db_connect();

        $sql = "SELECT * FROM eventify_subscriptions
                WHERE customer_id = ?
                AND subscription_type = ?
                AND status = 'active'
                AND end_date >= CURDATE()
                ORDER BY end_date DESC
                LIMIT 1";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("is", $customer_id, $subscription_type);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Check if customer has active premium subscription
     */
    public function hasActivePremium($customer_id, $subscription_type = 'analytics_premium')
    {
        $subscription = $this->getActiveSubscription($customer_id, $subscription_type);
        return !empty($subscription);
    }

    /**
     * Get all subscriptions for a customer
     */
    public function getCustomerSubscriptions($customer_id)
    {
        if (!$this->db) $this->db_connect();

        $sql = "SELECT * FROM eventify_subscriptions
                WHERE customer_id = ?
                ORDER BY created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $customer_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Get subscription by ID
     */
    public function getSubscriptionById($subscription_id)
    {
        if (!$this->db) $this->db_connect();

        $sql = "SELECT * FROM eventify_subscriptions WHERE subscription_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $subscription_id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    /**
     * Update subscription status after payment
     */
    public function activateSubscription($subscription_id, $payment_reference, $invoice_no)
    {
        if (!$this->db) $this->db_connect();

        $sql = "UPDATE eventify_subscriptions
                SET status = 'active',
                    payment_reference = ?,
                    invoice_no = ?,
                    start_date = CURDATE(),
                    end_date = DATE_ADD(CURDATE(), INTERVAL 30 DAY),
                    updated_at = NOW()
                WHERE subscription_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("ssi", $payment_reference, $invoice_no, $subscription_id);
        return $stmt->execute();
    }

    /**
     * Cancel a subscription
     */
    public function cancelSubscription($subscription_id)
    {
        if (!$this->db) $this->db_connect();

        $sql = "UPDATE eventify_subscriptions
                SET status = 'cancelled', updated_at = NOW()
                WHERE subscription_id = ?";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $subscription_id);
        return $stmt->execute();
    }

    /**
     * Get expiring subscriptions (for renewal reminders)
     */
    public function getExpiringSubscriptions($days = 7)
    {
        if (!$this->db) $this->db_connect();

        $sql = "SELECT s.*, c.customer_name, c.customer_email
                FROM eventify_subscriptions s
                LEFT JOIN eventify_customer c ON s.customer_id = c.customer_id
                WHERE s.status = 'active'
                AND s.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL ? DAY)
                ORDER BY s.end_date ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->bind_param("i", $days);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * Expire old subscriptions (run this in a cron job)
     */
    public function expireOldSubscriptions()
    {
        if (!$this->db) $this->db_connect();

        $sql = "UPDATE eventify_subscriptions
                SET status = 'expired', updated_at = NOW()
                WHERE status = 'active'
                AND end_date < CURDATE()";

        return $this->db->query($sql);
    }

    /**
     * Get subscription statistics
     */
    public function getSubscriptionStats()
    {
        if (!$this->db) $this->db_connect();

        $sql = "SELECT
                    COUNT(*) as total_subscriptions,
                    SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active_count,
                    SUM(CASE WHEN status = 'expired' THEN 1 ELSE 0 END) as expired_count,
                    SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count,
                    SUM(CASE WHEN status = 'active' THEN amount ELSE 0 END) as monthly_revenue
                FROM eventify_subscriptions";

        $result = $this->db->query($sql);
        return $result->fetch_assoc();
    }
}
?>
