<?php
/**
 * Subscription Controller
 * Handles subscription business logic
 */

require_once dirname(__FILE__) . '/../classes/subscription_class.php';

/**
 * Create a new subscription
 */
function create_subscription_ctr($customer_id, $subscription_type, $amount, $currency = 'GHS')
{
    $subscription = new Subscription();
    return $subscription->createSubscription($customer_id, $subscription_type, $amount, $currency);
}

/**
 * Get active subscription for customer
 */
function get_active_subscription_ctr($customer_id, $subscription_type = 'analytics_premium')
{
    $subscription = new Subscription();
    return $subscription->getActiveSubscription($customer_id, $subscription_type);
}

/**
 * Check if customer has active premium
 */
function has_active_premium_ctr($customer_id, $subscription_type = 'analytics_premium')
{
    $subscription = new Subscription();
    return $subscription->hasActivePremium($customer_id, $subscription_type);
}

/**
 * Get all customer subscriptions
 */
function get_customer_subscriptions_ctr($customer_id)
{
    $subscription = new Subscription();
    return $subscription->getCustomerSubscriptions($customer_id);
}

/**
 * Get subscription by ID
 */
function get_subscription_by_id_ctr($subscription_id)
{
    $subscription = new Subscription();
    return $subscription->getSubscriptionById($subscription_id);
}

/**
 * Activate subscription after payment
 */
function activate_subscription_ctr($subscription_id, $payment_reference, $invoice_no)
{
    $subscription = new Subscription();
    return $subscription->activateSubscription($subscription_id, $payment_reference, $invoice_no);
}

/**
 * Cancel subscription
 */
function cancel_subscription_ctr($subscription_id)
{
    $subscription = new Subscription();
    return $subscription->cancelSubscription($subscription_id);
}

/**
 * Get expiring subscriptions
 */
function get_expiring_subscriptions_ctr($days = 7)
{
    $subscription = new Subscription();
    return $subscription->getExpiringSubscriptions($days);
}

/**
 * Expire old subscriptions
 */
function expire_old_subscriptions_ctr()
{
    $subscription = new Subscription();
    return $subscription->expireOldSubscriptions();
}

/**
 * Get subscription statistics
 */
function get_subscription_stats_ctr()
{
    $subscription = new Subscription();
    return $subscription->getSubscriptionStats();
}
?>
