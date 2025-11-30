# Eventify Checkout Flow - Complete Process

## 🔄 Payment Flow Diagram

```
┌─────────────────────────────────────────────────────────────┐
│ 1. USER ADDS ITEMS TO CART                                  │
│    Files: actions/add_to_cart_action.php                    │
│    Database: eventify_cart table updated                    │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 2. USER GOES TO CHECKOUT                                    │
│    File: view/checkout.php                                  │
│    - Loads cart items from database                         │
│    - Calculates subtotal                                    │
│    - Adds 15% service fee                                   │
│    - Shows total amount                                     │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 3. USER CLICKS "PAY NOW"                                    │
│    File: js/checkout.js (processCheckout function)          │
│    Action: Sends AJAX to paystack_init_transaction.php      │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 4. INITIALIZE PAYSTACK PAYMENT                              │
│    File: actions/paystack_init_transaction.php              │
│    ✓ Generate reference: EVENTIFY-{customer_id}-{timestamp} │
│    ✓ Store in session: paystack_ref, paystack_amount        │
│    ✓ Store in database: eventify_payment_init table         │
│    ✓ Call Paystack API with callback URL                    │
│    ✓ Return authorization_url to frontend                   │
│                                                              │
│    IMPORTANT: Callback URL is built from SERVER constant    │
│    - Local: http://localhost/Eventify/view/paystack_callback.php
│    - Production: http://169.239.251.102:442/~maisy.baer/eventify/Eventify/view/paystack_callback.php
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 5. REDIRECT TO PAYSTACK                                     │
│    - User is sent to Paystack payment gateway               │
│    - User enters card details and pays                      │
│    - Paystack processes payment                             │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 6. PAYSTACK REDIRECTS BACK                                  │
│    URL: {CALLBACK_URL}?reference={REFERENCE}                │
│    File: view/paystack_callback.php                         │
│    - Shows "Verifying payment..." message                   │
│    - Triggers JavaScript verification                       │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 7. VERIFY PAYMENT                                           │
│    File: actions/paystack_verify_payment.php                │
│    Step 1: Verify with Paystack API                         │
│    Step 2: Check payment status = 'success'                 │
│    Step 3: Verify amount matches                            │
│    Step 4: Acquire database lock (prevent duplicates)       │
│    Step 5: Check if already processed                       │
│    Step 6: Get cart items                                   │
│    Step 7: Create order                                     │
│    Step 8: Add order details                                │
│    Step 9: Record payment with transaction_ref              │
│    Step 10: Empty cart                                      │
│    Step 11: Release lock                                    │
│    Step 12: Return success                                  │
└─────────────────────────────────────────────────────────────┘
                            ↓
┌─────────────────────────────────────────────────────────────┐
│ 8. SHOW SUCCESS & REDIRECT                                  │
│    File: view/paystack_callback.php (JavaScript)            │
│    - Shows success message                                  │
│    - Redirects to payment_success.php                       │
│    - Shows order details                                    │
└─────────────────────────────────────────────────────────────┘
```

## 🎯 Key Files & Their Roles

### **Configuration Files:**
1. **settings/db_cred.php** - Auto-detects environment, sets SERVER constant
2. **settings/paystack_config.php** - Builds PAYSTACK_CALLBACK_URL from SERVER

### **Frontend Files:**
1. **view/checkout.php** - Checkout page with cart items
2. **js/checkout.js** - Handles payment button click, calls init API

### **Backend Files:**
1. **actions/paystack_init_transaction.php** - Initializes payment with Paystack
2. **actions/paystack_verify_payment.php** - Verifies payment and creates order

### **Callback Files:**
1. **view/paystack_callback.php** - Landing page after Paystack redirect
2. **view/payment_success.php** - Final success page

## 📊 Database Tables Updated

### During Initialization:
- **eventify_payment_init** - Stores payment reference and amount

### During Verification:
- **eventify_orders** - New order created
- **eventify_orderdetails** - Order items added
- **eventify_payment** - Payment recorded with transaction_ref
- **eventify_cart** - Cart emptied for customer

## ✅ What Makes It Work Now

### **Auto-Detection in db_cred.php:**
```php
// Detects if on localhost or production
if (localhost) {
    SERVER = "localhost"
} else {
    SERVER = "http://169.239.251.102:442/~maisy.baer/eventify/Eventify"
}
```

### **Result:**
- ✅ **Local**: Callback goes to localhost - works!
- ✅ **Production**: Callback goes to production server - works!
- ✅ **Same file works everywhere** - no manual changes needed!

## 🚨 Critical Points

### **Why Cart Was Empty Before:**
1. Payment initiated on **production server** (cart exists there)
2. Paystack callback went to **localhost** (different database)
3. Verification looked for cart on **localhost** (empty!)
4. Error: "Cart is empty"

### **Why It Works Now:**
1. Payment initiated on **production server**
2. Paystack callback goes to **production server** (same place!)
3. Verification finds cart on **production server** (has items!)
4. Success: Order created, cart emptied

## 📝 Testing Checklist

After uploading the updated `db_cred.php`:

1. ✅ Visit `settings/check_config.php` on production
   - Should show: Environment = PRODUCTION SERVER
   - Should show: Callback URL with your server address (NO localhost)

2. ✅ Add items to cart on production

3. ✅ Go to checkout on production

4. ✅ Click "Pay Now"
   - Should redirect to Paystack
   - Check URL - should show amount in pesewas

5. ✅ Complete payment on Paystack

6. ✅ Should redirect back to YOUR production server
   - NOT to localhost
   - Should show "Verifying payment..."

7. ✅ Verification should succeed
   - Order created in eventify_orders
   - Payment recorded in eventify_payment
   - Cart emptied
   - Success page shown

## 🔧 Troubleshooting

### If callback still goes to localhost:
- Clear browser cache
- Check `check_config.php` output
- Verify `db_cred.php` was uploaded to server
- Check error logs in `/tmp/` or server error logs

### If "Cart is empty" error:
- Verify you're testing on the SAME server (not localhost)
- Check eventify_cart table has items for your customer_id
- Check eventify_payment_init table has the reference

### If payment succeeds but order not created:
- Check eventify_payment table for transaction_ref
- Check error logs for "Database transaction rolled back"
- Verify eventify_payment_init table exists

---

**The flow is now complete and should work seamlessly on both environments!**
