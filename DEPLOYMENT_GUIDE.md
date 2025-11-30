# Eventify Deployment Guide

## Environment Configuration

Your Eventify application needs different configurations for **local development** vs **production server**.

---

## 🏠 Local Development Setup (Your Computer)

Keep these settings in `settings/db_cred.php`:

```php
<?php
if (!defined("SERVER")) {
    define("SERVER", "localhost");
}

if (!defined("USERNAME")) {
    define("USERNAME", "root");
}

if (!defined("PASSWD")) {
    define("PASSWD", "");
}

if (!defined("DATABASE")) {
    define("DATABASE", "eventify");
}
?>
```

**Local URLs will be:**
- App: `http://localhost/Eventify/`
- Callback: `http://localhost/Eventify/view/paystack_callback.php`

---

## 🌐 Production Server Setup

**BEFORE uploading to server**, change `settings/db_cred.php` to:

```php
<?php
if (!defined("SERVER")) {
    define("SERVER", "http://169.239.251.102:442/~maisy.baer/eventify/Eventify");
}

if (!defined("USERNAME")) {
    define("USERNAME", "root"); // Update if different on server
}

if (!defined("PASSWD")) {
    define("PASSWD", ""); // Update if different on server
}

if (!defined("DATABASE")) {
    define("DATABASE", "eventify");
}
?>
```

**Production URLs will be:**
- App: `http://169.239.251.102:442/~maisy.baer/eventify/Eventify/`
- Callback: `http://169.239.251.102:442/~maisy.baer/eventify/Eventify/view/paystack_callback.php`

---

## ⚙️ How It Works

### 1. **settings/db_cred.php**
- Defines the `SERVER` constant
- This is used by `paystack_config.php` to build the callback URL

### 2. **settings/paystack_config.php**
- Reads `SERVER` constant
- If it contains `http://` or `https://`, uses it as-is
- Otherwise, adds `http://` and `/Eventify`
- Creates `PAYSTACK_CALLBACK_URL` for Paystack redirects

### 3. **Payment Flow**
```
1. User clicks "Pay Now" on server
   ↓
2. paystack_init_transaction.php initializes payment
   - Stores reference in session AND database (eventify_payment_init)
   - Sends callback URL to Paystack
   ↓
3. User pays on Paystack
   ↓
4. Paystack redirects to CALLBACK URL
   - If SERVER = localhost → redirects to localhost ❌
   - If SERVER = production URL → redirects to production ✅
   ↓
5. paystack_verify_payment.php verifies payment
   - Checks for existing payment (duplicate prevention)
   - Gets cart items
   - Creates order
   - Records payment with transaction_ref
   - Empties cart
   ↓
6. Success!
```

---

## 🔧 Deployment Checklist

### Before Uploading to Production:

- [ ] Change `SERVER` in `settings/db_cred.php` to production URL
- [ ] Update database credentials if different on server
- [ ] Upload all files to server
- [ ] Run SQL migration for `eventify_payment_init` table
- [ ] Test configuration: Visit `settings/check_config.php`
- [ ] Verify callback URL shows production address (NOT localhost)

### Testing Payment:

1. ✅ Add items to cart on **production server**
2. ✅ Checkout on **production server**
3. ✅ Complete payment on Paystack
4. ✅ Verify redirect comes back to **production server**
5. ✅ Check order was created successfully

---

## 🚨 Common Issues

### Issue: "Cart is empty" error after payment

**Cause:** `SERVER` constant points to `localhost` on production server

**Fix:** Update `SERVER` in `settings/db_cred.php` to full production URL

### Issue: Payment works locally but fails on server

**Cause:** Forgot to change `db_cred.php` before uploading

**Fix:** Update server file to use production URL

### Issue: Callback goes to localhost instead of server

**Cause:** Paystack received localhost URL during initialization

**Fix:** Check `APP_BASE_URL` and `PAYSTACK_CALLBACK_URL` in logs

---

## 📝 Database Migration Required

Run this SQL on your production database:

```sql
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
```

---

## 🎯 Quick Reference

| Environment | SERVER Value | Callback URL |
|------------|--------------|--------------|
| **Local** | `localhost` | `http://localhost/Eventify/view/paystack_callback.php` |
| **Production** | `http://169.239.251.102:442/~maisy.baer/eventify/Eventify` | `http://169.239.251.102:442/~maisy.baer/eventify/Eventify/view/paystack_callback.php` |

---

## ✅ Verification

After deploying, visit:
```
http://169.239.251.102:442/~maisy.baer/eventify/Eventify/settings/check_config.php
```

Look for:
- ✅ Environment: **PRODUCTION SERVER** (not LOCAL)
- ✅ Callback URL: Should contain your server address
- ✅ No "localhost" in callback URL

---

**Remember:** Always update `db_cred.php` when switching between environments!
