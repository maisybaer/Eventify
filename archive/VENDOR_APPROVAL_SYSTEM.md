# Vendor Approval System - Complete Guide

## Overview
This system provides a two-way vendor booking platform:
1. **Customer-to-Vendor**: Customers book vendors for their events
2. **Vendor-to-Event**: Vendors request to provide services at events (requires event manager approval)

## Database Setup

### Step 1: Run Database Setup Scripts

**1. Create the base vendor bookings table:**
```
http://localhost/Eventify/actions/create_vendor_bookings_table.php
```

**2. Update table with approval fields:**
```
http://localhost/Eventify/actions/update_vendor_bookings_table.php
```

This adds the following fields:
- `booking_type` - 'customer_to_vendor' or 'vendor_to_event'
- `approved_by_vendor` - Vendor approval status (0/1)
- `approved_by_event_manager` - Event manager approval status (0/1)
- `vendor_approved_date` - When vendor approved
- `event_manager_approved_date` - When event manager approved

## System Components

### For Vendors (role=2)

**1. Browse Events** ([browse_events_vendor.php](view/browse_events_vendor.php))
- View all available events
- Search and filter events by category
- Request to provide vendor services at events
- See pending/approved status of requests

**2. Vendor Dashboard** ([vendor_dashboard.php](view/vendor_dashboard.php))
- View all event requests made by vendor
- Track approval status
- See statistics (total requests, pending, approved)

**3. Customer Bookings** ([my_bookings.php](view/my_bookings.php))
- View bookings from customers who want to hire them
- Approve or decline customer bookings
- Mark bookings as completed

**4. Approve Vendors** ([vendor_requests.php](view/vendor_requests.php))
- Review vendor requests for their own events
- Approve or reject other vendors who want to participate
- View vendor contact information

### For Customers (role≠2)

**1. Browse Vendors** ([browse_vendors.php](view/browse_vendors.php))
- View all available vendors
- Filter by vendor type/category
- Book vendors for their events

**2. Book Vendor** ([single_vendor.php](view/single_vendor.php))
- Select one of their own events
- Book vendor for that event
- Add service date and notes

**3. My Bookings** ([my_bookings.php](view/my_bookings.php))
- View all bookings they've made
- Cancel pending bookings
- See booking status

### For Event Managers

**1. Vendor Requests** ([vendor_requests.php](view/vendor_requests.php))
- View vendor requests for their events
- Approve or reject vendors
- See vendor information and contact details

## Workflows

### Workflow 1: Customer Books Vendor

```
1. Customer browses vendors → browse_vendors.php
2. Customer clicks vendor → single_vendor.php
3. Customer selects their event
4. Customer books vendor → create_vendor_booking_action.php
5. Vendor receives booking → my_bookings.php
6. Vendor approves booking
7. Service is provided
8. Vendor marks as completed
```

**Booking Type**: `customer_to_vendor`
**Approval Flow**: Vendor approves → Confirmed

### Workflow 2: Vendor Requests to Service an Event

```
1. Vendor browses events → browse_events_vendor.php
2. Vendor requests to vendor event → vendor_request_event_action.php
3. Event manager receives request → vendor_requests.php
4. Event manager approves request → approve_vendor_request_action.php
5. Vendor sees approval → vendor_dashboard.php
6. Vendor provides service at event
```

**Booking Type**: `vendor_to_event`
**Approval Flow**: Event manager approves → Confirmed

## API Endpoints

### Vendor-to-Event System

**1. Request to Vendor an Event**
```php
POST /actions/vendor_request_event_action.php
Parameters:
  - event_id (int, required)
  - notes (text, optional)
```

**2. Fetch Vendor Event Requests**
```php
GET /actions/fetch_vendor_event_requests_action.php
Returns: Array of vendor's event requests
```

**3. Fetch All Events**
```php
GET /actions/fetch_all_events_action.php
Returns: Array of all events
```

**4. Approve/Reject Vendor Request**
```php
POST /actions/approve_vendor_request_action.php
Parameters:
  - booking_id (int, required)
  - approved (int, 1=approve, 0=reject)
```

### Customer-to-Vendor System

**1. Create Vendor Booking**
```php
POST /actions/create_vendor_booking_action.php
Parameters:
  - vendor_id (int, required)
  - event_id (int, required)
  - service_date (date, optional)
  - notes (text, optional)
```

**2. Update Booking Status**
```php
POST /actions/update_booking_status_action.php
Parameters:
  - booking_id (int, required)
  - status (string: pending|confirmed|completed|cancelled)
```

**3. Fetch Customer Events**
```php
GET /actions/fetch_customer_events_action.php
Returns: Array of logged-in user's events
```

## Page Navigation Structure

### Vendor Menu (role=2)
```
Home → index.php
My Events → admin/event.php
Browse Events → browse_events_vendor.php
My Event Requests → vendor_dashboard.php
Customer Bookings → my_bookings.php
Approve Vendors → vendor_requests.php
Logout
```

### Customer Menu (role≠2)
```
Home → index.php
Events → all_event.php
Browse Vendors → browse_vendors.php
My Bookings → my_bookings.php
Cart → cart.php
Logout
```

### Event Manager Menu (anyone who created events)
```
Home → index.php
My Events → admin/event.php
Vendor Requests → vendor_requests.php (approve vendors for MY events)
My Bookings → my_bookings.php
Logout
```

## Files Created/Modified

### New Files

**Database Setup:**
- `/actions/update_vendor_bookings_table.php`

**Views:**
- `/view/browse_events_vendor.php` - Vendors browse events
- `/view/vendor_dashboard.php` - Vendor event request dashboard
- `/view/vendor_requests.php` - Event manager approval page

**Actions:**
- `/actions/vendor_request_event_action.php` - Vendor requests event
- `/actions/fetch_vendor_event_requests_action.php` - Get vendor's requests
- `/actions/fetch_all_events_action.php` - Get all events
- `/actions/approve_vendor_request_action.php` - Approve/reject vendor

**Class Methods Added:**
- `VendorBooking::createVendorEventRequest()`
- `VendorBooking::checkVendorEventRequest()`
- `VendorBooking::getVendorEventRequests()`
- `VendorBooking::getEventManagerVendorRequests()`
- `VendorBooking::updateEventManagerApproval()`

### Modified Files
- `/classes/vendor_booking_class.php` - Added vendor-to-event methods
- `/view/single_vendor.php` - Updated booking form

## Key Features

### Vendor Features
✅ Browse all events in the platform
✅ Request to provide services at any event
✅ Track request status (pending/approved/rejected)
✅ View approval statistics
✅ Receive and manage customer bookings
✅ Approve vendors for their own events

### Event Manager Features
✅ Receive vendor requests for their events
✅ View vendor profiles and contact info
✅ Approve or reject vendor requests
✅ See approval statistics
✅ Track vendor participation

### Customer Features
✅ Browse and book vendors
✅ Select events for vendor services
✅ Track booking status
✅ Cancel pending bookings

## Approval Statuses

### Booking Status
- **pending**: Initial state, awaiting approval
- **confirmed**: Approved by relevant party
- **completed**: Service delivered (vendor marks this)
- **cancelled**: Rejected or cancelled

### Approval Fields
- **approved_by_vendor**: For customer-to-vendor bookings
- **approved_by_event_manager**: For vendor-to-event requests

## Testing Checklist

### Setup
- [ ] Run `create_vendor_bookings_table.php`
- [ ] Run `update_vendor_bookings_table.php`
- [ ] Verify new columns exist in database

### Vendor-to-Event Flow
- [ ] Login as vendor (role=2)
- [ ] Visit "Browse Events" page
- [ ] Request to vendor an event
- [ ] Verify request appears in "My Event Requests"
- [ ] Login as event creator
- [ ] Visit "Vendor Requests" page
- [ ] Approve the vendor request
- [ ] Login back as vendor
- [ ] Verify approval in dashboard

### Customer-to-Vendor Flow
- [ ] Login as customer (role≠2)
- [ ] Create at least one event
- [ ] Browse vendors
- [ ] Book a vendor for your event
- [ ] Login as vendor
- [ ] See booking in "Customer Bookings"
- [ ] Approve the booking
- [ ] Mark booking as completed

### Event Manager Flow
- [ ] Create an event
- [ ] Have vendors request to service your event
- [ ] Visit "Vendor Requests"
- [ ] Approve/reject vendors
- [ ] Verify status updates

## Database Schema

### eventify_vendor_bookings Table

| Field | Type | Description |
|-------|------|-------------|
| booking_id | INT AUTO_INCREMENT | Primary key |
| booking_type | VARCHAR(20) | 'customer_to_vendor' or 'vendor_to_event' |
| vendor_id | INT | Vendor providing service |
| customer_id | INT | Customer/event manager |
| event_id | INT | Related event |
| booking_date | DATETIME | When booking was created |
| booking_status | VARCHAR(50) | pending, confirmed, completed, cancelled |
| approved_by_vendor | TINYINT(1) | 0=pending, 1=approved |
| approved_by_event_manager | TINYINT(1) | 0=pending, 1=approved |
| vendor_approved_date | DATETIME | When vendor approved |
| event_manager_approved_date | DATETIME | When event manager approved |
| service_date | DATE | Service date (optional) |
| notes | TEXT | Additional notes |
| price | DECIMAL(10,2) | Agreed price (optional) |

## Security Considerations

1. **Authorization Checks**: All endpoints verify user permissions
2. **Ownership Validation**: Users can only approve bookings for their events
3. **Role Verification**: Vendor-specific pages check role=2
4. **SQL Injection Prevention**: All queries use prepared statements
5. **XSS Prevention**: All output uses htmlspecialchars()

## Future Enhancements

- [ ] Email notifications for approval status changes
- [ ] Vendor calendar integration
- [ ] Payment processing for vendor bookings
- [ ] Rating/review system
- [ ] Vendor portfolio/gallery
- [ ] Multi-vendor packages
- [ ] Booking contracts/agreements
- [ ] Vendor availability calendar
- [ ] Automated reminders
- [ ] Analytics dashboard

## Support

For issues or questions:
1. Check browser console for errors
2. Review PHP error logs
3. Verify database table structure
4. Ensure user roles are correctly set
5. Confirm user has created events (for booking vendors)
