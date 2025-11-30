# Eventify - Event Management Platform

A comprehensive event management platform that connects event managers, vendors, and customers. Eventify simplifies event creation, ticket booking, vendor management, and payment processing all in one place.

## 📋 Table of Contents

- [Features](#-features)
- [Demo](#-demo)
- [Installation](#-installation)
- [Configuration](#-configuration)
- [Usage](#-usage)
- [Architecture](#-architecture)
- [API Documentation](#-api-documentation)
- [Database Schema](#-database-schema)
- [Technologies Used](#-technologies-used)
- [Contact](#-contact)

## Features

### For Event Managers
- **Event Creation & Management**: Create and manage events with detailed information (date, location, pricing, categories)
- **Vendor Approval System**: Review and approve vendor requests to service your events
- **Analytics Dashboard**: Track ticket sales, bookings, and revenue (Premium subscription)
- **Category Management**: Organize events with custom categories
- **Image Management**: Upload and manage event images

### For Vendors
- **Vendor Profiles**: Showcase services with comprehensive profiles
- **Two-Way Booking System**:
  - Accept bookings from customers for their events
  - Request to provide services at any event (requires event manager approval)
- **Booking Management**: Track and manage all bookings and requests
- **Dashboard**: View statistics and manage service requests

### For Customers
- **Event Discovery**: Browse and search events by category, date, and location
- **Ticket Booking**: Add events to cart and purchase tickets securely
- **Vendor Booking**: Book vendors for your own events
- **Order History**: View all past orders and bookings
- **Secure Payments**: Integrated Paystack payment gateway

### 🔐 Security Features
- Password hashing with PHP's `password_hash()`
- SQL injection prevention with prepared statements
- XSS protection with output escaping
- CSRF token protection
- Session-based authentication
- Role-based access control (RBAC)

## 🎬 Demo

**Live Demo**: http://169.239.251.102:442/~maisy.baer/eventify/Eventify/home.php

### Test Accounts

```
Event Manager:
Email: admin@email.com
Password: Admin@123

Vendor:
Email: Mimosami@email.com
Password: Mimo@123

Customer:
Email: customer@email.com
Password: cust@123
```

## 🚀 Installation

### Prerequisites

- PHP 7.4 or higher
- MySQL 8.0 or higher
- Apache/Nginx web server
- Composer (optional)

### Step 1: Clone the Repository

```bash
git clone https://github.com/maisybaer/eventify.git
cd eventify
```

### Step 2: Database Setup

1. Create a MySQL database:

```sql
CREATE DATABASE ecommerce_2025A_maisy_baer;
```

2. Import the database schema:

```bash
mysql -u root -p ecommerce_2025A_maisy_baer < db/dbforlab.sql
```

Or use phpMyAdmin to import `db/dbforlab.sql`

### Step 3: Configuration

1. Configure database connection in `settings/connection.php`:

```php
<?php
define('DB_HOST', 'localhost');
define('DB_USER', 'your_username');
define('DB_PASS', 'your_password');
define('DB_NAME', 'ecommerce_2025A_maisy_baer');
?>
```

2. Set up Paystack API keys in `settings/paystack_config.php`:

```php
<?php
define('PAYSTACK_PUBLIC_KEY', 'pk_test_xxxxxxxxxxxxx');
define('PAYSTACK_SECRET_KEY', 'sk_test_xxxxxxxxxxxxx');
?>
```

Get your API keys from [Paystack Dashboard](https://dashboard.paystack.com/#/settings/developer)

### Step 4: Set Permissions

```bash
chmod 755 images/
chmod 755 images/events/
chmod 755 images/vendors/
chmod 755 images/users/
```

### Step 5: Access the Application

```
http://localhost/Eventify/home.php
```

## ⚙️ Configuration

### Environment Variables (Recommended for Production)

Create a `.env` file in the root directory:

```env
# Database Configuration
DB_HOST=localhost
DB_USER=your_username
DB_PASS=your_password
DB_NAME=ecommerce_2025A_maisy_baer

# Paystack Configuration
PAYSTACK_PUBLIC_KEY=pk_live_xxxxxxxxxxxxx
PAYSTACK_SECRET_KEY=sk_live_xxxxxxxxxxxxx

# Application Settings
APP_ENV=production
APP_DEBUG=false
APP_URL=https://yourdomain.com
```

### User Roles

The system supports three user roles:

| Role | Value | Description |
|------|-------|-------------|
| Customer | 0 | Can browse events, buy tickets, and book vendors |
| Event Manager | 1 | Can create events, manage bookings, approve vendors |
| Vendor | 2 | Can offer services, manage bookings, request event participation |

## 📖 Usage

### Creating an Event (Event Manager)

1. Log in as an Event Manager
2. Navigate to Dashboard → Manage Events
3. Click "Add New Event"
4. Fill in event details:
   - Event name and description
   - Category
   - Price
   - Date and location
   - Upload event image
5. Click "Submit"

### Booking a Ticket (Customer)

1. Browse events on the homepage
2. Click on an event to view details
3. Click "Add to Cart"
4. View cart and proceed to checkout
5. Enter email and complete payment via Paystack
6. Receive order confirmation

### Vendor Booking Workflow

#### Customer Books Vendor:
1. Customer browses vendors
2. Selects vendor profile
3. Chooses their own event
4. Submits booking request
5. Vendor approves/declines
6. Service is provided

#### Vendor Requests Event:
1. Vendor browses available events
2. Requests to provide services
3. Event manager reviews request
4. Event manager approves/rejects
5. Vendor services the event

## 🏗️ Architecture

Eventify follows the MVC (Model-View-Controller) architectural pattern:

```
┌─────────────────────────────────────────┐
│           Presentation Layer            │
│  (Views - HTML/CSS/JavaScript)          │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│          Application Layer              │
│  (Controllers - Business Logic)         │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│           Data Access Layer             │
│  (Classes - Database Operations)        │
└─────────────────┬───────────────────────┘
                  │
┌─────────────────▼───────────────────────┐
│           Database Layer                │
│  (MySQL - Data Storage)                 │
└─────────────────────────────────────────┘
```

### Project Structure

```
Eventify/
├── actions/              # API endpoints and action handlers
│   ├── add_event_action.php
│   ├── add_to_cart_action.php
│   ├── paystack_init_transaction.php
│   └── paystack_verify_payment.php
├── admin/               # Admin panel pages
│   ├── event.php
│   ├── category.php
│   └── dashboard.php
├── classes/             # Data access layer (Models)
│   ├── cart_class.php
│   ├── event_class.php
│   ├── order_class.php
│   └── vendor_booking_class.php
├── controllers/         # Business logic layer
│   ├── cart_controller.php
│   ├── event_controller.php
│   └── vendor_booking_controller.php
├── css/                 # Stylesheets
├── db/                  # Database schema
│   └── dbforlab.sql
├── images/              # Uploaded images
│   ├── events/
│   ├── vendors/
│   └── users/
├── includes/            # Reusable components
│   ├── header.php
│   ├── footer.php
│   └── nav.php
├── js/                  # JavaScript files
│   ├── event.js
│   ├── cart.js
│   └── checkout.js
├── login/               # Authentication
│   ├── login.php
│   ├── register_customer.php
│   └── logout.php
├── settings/            # Configuration files
│   ├── core.php
│   ├── db_class.php
│   └── paystack_config.php
├── view/                # Frontend pages
│   ├── all_event.php
│   ├── single_event.php
│   ├── checkout.php
│   └── browse_vendors.php
├── home.php             # Landing page
└── README.md            # This file
```

## 📡 API Documentation

### Event APIs

#### Fetch All Events
```http
GET /actions/fetch_event_action.php
```

**Response:**
```json
{
  "status": "success",
  "data": [
    {
      "event_id": 1,
      "event_desc": "Summer Music Festival",
      "event_price": "50.00",
      "event_date": "2025-08-15",
      "event_location": "Central Park"
    }
  ]
}
```

#### Add Event
```http
POST /actions/add_event_action.php
```

**Parameters:**
- `event_desc` (string, required)
- `event_cat` (int, required)
- `event_price` (decimal, required)
- `event_location` (string, required)
- `event_date` (date, required)
- `event_image` (file, optional)

### Cart APIs

#### Add to Cart
```http
POST /actions/add_to_cart_action.php
```

**Parameters:**
- `event_id` (int, required)
- `qty` (int, default: 1)

### Payment APIs

#### Initialize Payment
```http
POST /actions/paystack_init_transaction.php
```

**Parameters:**
- `amount` (decimal, required)
- `email` (string, required)

**Response:**
```json
{
  "status": "success",
  "authorization_url": "https://checkout.paystack.com/...",
  "reference": "EVENTIFY-5-1732978543"
}
```

#### Verify Payment
```http
POST /actions/paystack_verify_payment.php
```

**Parameters:**
- `reference` (string, required)
- `total_amount` (decimal, required)

For complete API documentation, see [API_DOCS.md](docs/API_DOCS.md)

## 🗄️ Database Schema

### Key Tables

#### eventify_customer
For the complete schema, see [db/dbforlab.sql](db/dbforlab.sql)

## 🛠️ Technologies Used

### Backend
- **PHP 7.4+**: Server-side scripting
- **MySQL 8.0**: Relational database
- **Apache**: Web server

### Frontend
- **HTML5**: Markup language
- **CSS3**: Styling with modern features
- **JavaScript (ES6+)**: Client-side interactivity
- **jQuery**: DOM manipulation and AJAX
- **SweetAlert2**: Beautiful alert notifications
- **Font Awesome**: Icon library

### Payment Integration
- **Paystack API**: Payment processing for African markets

### Development Tools
- **Git**: Version control
- **phpMyAdmin**: Database management
- **VS Code**: Code editor

## 🧪 Testing

### Running Tests

```bash
# Test database connection
php actions/test_database.php

# Test payment initialization
php actions/test_paystack.php

# Test vendor booking system
php actions/test_vendor_booking.php
```

### Manual Testing Checklist

- [ ] User registration and login
- [ ] Event creation and management
- [ ] Shopping cart functionality
- [ ] Payment processing
- [ ] Vendor booking (both types)
- [ ] Order confirmation
- [ ] Role-based access control

## 📝 Contributing

Contributions are welcome! Please follow these steps:

1. **Fork the repository**
2. **Create a feature branch**
   ```bash
   git checkout -b feature/amazing-feature
   ```
3. **Commit your changes**
   ```bash
   git commit -m 'Add some amazing feature'
   ```
4. **Push to the branch**
   ```bash
   git push origin feature/amazing-feature
   ```
5. **Open a Pull Request**

### Coding Standards

- Follow PSR-12 coding standards for PHP
- Use meaningful variable and function names
- Comment complex logic
- Write unit tests for new features
- Update documentation as needed


## 🐛 Known Issues

- [ ] Email notifications not yet implemented
- [ ] Mobile responsiveness needs improvement on some pages
- [ ] Analytics dashboard requires premium subscription
- [ ] Password reset functionality pending

## Acknowledgments

- [Paystack](https://paystack.com/) for payment processing
- [SweetAlert2](https://sweetalert2.github.io/) for beautiful alerts
- [Font Awesome](https://fontawesome.com/) for icons
- [jQuery](https://jquery.com/) for DOM manipulation

## Contact

**Project Link**: [https://github.com/maisybaer/eventify](https://github.com/maisybaer/eventify)

**Documentation**: https://docs.google.com/document/d/1H-f7BMR_7_X2W8jhjIHoksc-zr2E-c3_iHI4Fudmz0I/edit?usp=sharing

Made by [Maisy Baer](https://github.com/maisybaer)

