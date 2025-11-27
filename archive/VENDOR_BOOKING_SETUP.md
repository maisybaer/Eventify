# Vendor Booking System Setup Guide

## Overview
This document explains the vendor booking functionality that has been implemented for Eventify. The system allows customers to book vendors for their events.

## Database Requirements

### New Table Required: `eventify_vendor_bookings`

You need to create this table by running the setup script:

**Run this in your browser:**
```
http://localhost/Eventify/actions/create_vendor_bookings_table.php
```

This will create the `eventify_vendor_bookings` table with the following structure:

| Field | Type | Description |
|-------|------|-------------|
| booking_id | INT(11) AUTO_INCREMENT | Primary key |
| vendor_id | INT(11) | The vendor being booked (customer_id with role=2) |
| customer_id | INT(11) | The customer making the booking |
| event_id | INT(11) | The customer's event for which vendor is being booked |
| booking_date | DATETIME | When the booking was created |
| booking_status | VARCHAR(50) | Status: pending, confirmed, completed, cancelled |
| service_date | DATE | Date when vendor service is needed (optional) |
| notes | TEXT | Additional booking notes (optional) |
| price | DECIMAL(10,2) | Agreed price for service (optional) |

## How It Works

### 1. Customer Perspective

**Booking a Vendor:**
1. Customer browses vendors at `/view/browse_vendors.php`
2. Clicks on a vendor to view their profile at `/view/single_vendor.php`
3. Customer selects ONE OF THEIR OWN EVENTS from a dropdown
4. Optionally adds service date and notes
5. Clicks "Book This Vendor" button
6. Booking is created with status "pending"

**Viewing Bookings:**
- Customer visits `/view/my_bookings.php`
- Sees all bookings they've made
- Can cancel pending bookings
- Can see booking status (pending, confirmed, completed, cancelled)

### 2. Vendor Perspective

**Managing Bookings:**
- Vendor visits `/view/my_bookings.php`
- Sees all bookings from customers for their services
- Can confirm or decline pending bookings
- Can mark confirmed bookings as completed

## Files Created/Modified

### New Files Created:

1. **Database Setup**
   - `/actions/create_vendor_bookings_table.php` - Creates the vendor bookings table

2. **Class Layer**
   - `/classes/vendor_booking_class.php` - Database operations for bookings

3. **Controller Layer**
   - `/controllers/vendor_booking_controller.php` - Business logic for bookings

4. **Action Layer (API)**
   - `/actions/create_vendor_booking_action.php` - Creates new booking
   - `/actions/fetch_customer_events_action.php` - Gets logged-in user's events
   - `/actions/update_booking_status_action.php` - Updates booking status

5. **View Layer**
   - `/view/single_vendor.php` - UPDATED: Vendor booking page
   - `/view/my_bookings.php` - NEW: View and manage bookings

## Key Features

### For Customers:
- ✅ Browse vendors
- ✅ View vendor profiles with contact info
- ✅ Book vendors for their own events
- ✅ Add service date and notes
- ✅ View all their bookings
- ✅ Cancel pending bookings
- ✅ See booking status updates

### For Vendors (role=2):
- ✅ Receive booking requests
- ✅ Confirm or decline bookings
- ✅ Mark bookings as completed
- ✅ View customer contact information
- ✅ See which event they're booked for

## Booking Status Flow

```
pending → confirmed → completed
   ↓
cancelled
```

- **pending**: Initial state when booking is created
- **confirmed**: Vendor has accepted the booking
- **completed**: Service has been delivered (vendor marks this)
- **cancelled**: Either party cancelled the booking

## Important Differences from Cart System

**Cart System (for event tickets):**
- Customer adds event tickets to cart
- Customer purchases tickets for events
- Uses `eventify_cart`, `eventify_orders`, `eventify_orderdetails` tables

**Booking System (for vendor services):**
- Customer books a vendor for their own event
- Vendor provides service at customer's event
- Uses `eventify_vendor_bookings` table
- No payment integration (can be added later)

## API Endpoints

### Create Booking
```
POST /actions/create_vendor_booking_action.php
Parameters:
  - vendor_id (int, required)
  - event_id (int, required)
  - service_date (date, optional)
  - notes (text, optional)
  - price (decimal, optional)

Response:
{
  "status": "success",
  "message": "Vendor booking created successfully",
  "booking_id": 123
}
```

### Fetch Customer Events
```
GET /actions/fetch_customer_events_action.php

Response:
{
  "status": "success",
  "data": [
    {
      "event_id": 1,
      "event_desc": "Birthday Party",
      "event_date": "2025-12-01",
      ...
    }
  ]
}
```

### Update Booking Status
```
POST /actions/update_booking_status_action.php
Parameters:
  - booking_id (int, required)
  - status (string, required: pending|confirmed|completed|cancelled)

Response:
{
  "status": "success",
  "message": "Booking status updated successfully"
}
```

## Integration Points

The booking system integrates with:
1. **Vendor System** - Uses existing vendor profiles
2. **Event System** - Links bookings to customer events
3. **Customer System** - Uses existing authentication
4. **Navigation** - Added to main menu structure

## Testing Checklist

- [ ] Run `create_vendor_bookings_table.php` to create the table
- [ ] Login as a customer (role != 2)
- [ ] Create at least one event
- [ ] Browse vendors and click on one
- [ ] Verify your events appear in the dropdown
- [ ] Book a vendor for your event
- [ ] Visit "My Bookings" page to see the booking
- [ ] Login as a vendor (role = 2)
- [ ] Visit "My Bookings" to see customer bookings
- [ ] Confirm a pending booking
- [ ] Mark a confirmed booking as completed

## Future Enhancements

Possible additions:
1. Payment integration for vendor bookings
2. Vendor pricing/packages system
3. Booking calendar view
4. Email notifications for booking status changes
5. Vendor availability calendar
6. Rating/review system for completed bookings
7. Booking contracts/agreements upload
8. Multi-vendor booking for single event

## Support

If you encounter any issues:
1. Check browser console for JavaScript errors
2. Check PHP error logs
3. Verify database table was created successfully
4. Ensure user is logged in
5. Verify user has created at least one event before booking
