# Eventify — Event Management System

**Herald College Kathmandu | Web Technologies Module | April 2026**

## Quick Start (XAMPP)

1. **Copy** the `EMS_personal/` folder into `C:\xampp\htdocs\` (or `/Applications/XAMPP/xamppfiles/htdocs/`)
2. **Import database**: Open phpMyAdmin → Import → select `sql/eventify.sql`
3. **Configure**: Edit `config/db_connect.php` — set `DB_USER`, `DB_PASS`, and `APP_URL`
4. **Upload dir**: Ensure `public/uploads/` is writable
5. **Open**: Visit `http://localhost/EMS_personal/index.php`

## Demo Credentials

| Role | Email | Password |
|------|-------|----------|
| Admin | admin@eventify.com | password |
| Organizer | organizer@eventify.com | password |
| Attendee | attendee@eventify.com | password |

> **Note**: The seeded password hash corresponds to `password` (bcrypt).

## Tech Stack

- **Backend**: PHP 8 (MVC architecture, no framework), PDO, MySQL 8
- **Frontend**: HTML5, CSS3, Vanilla JavaScript, Fetch API
- **Design**: Custom CSS (Material Design 3 inspired), Google Material Icons
- **Payment**: Direct booking confirmation flow
- **Server**: Apache 2.4 (XAMPP)

## Folder Structure (MVC)

```
eventify/
├── index.php              # Front controller / router
├── db_setup.php           # One-time database setup
├── config/
│   └── db_connect.php     # DB connection & app config
├── core/
│   ├── api_helpers.php    # Utility functions (h, redirect, flash, etc.)
│   ├── csrf_helper.php    # CSRF token generation & validation
│   └── session_helper.php # Session management & auth guard
├── models/
│   ├── User.php           # User data access (login, register, CRUD)
│   ├── Event.php          # Event data access (CRUD, search, admin)
│   ├── Booking.php        # Booking data access (create, cancel, stats)
│   └── Ticket.php         # Ticket lookups by ref/booking
├── controllers/
│   ├── AuthController.php    # Login, register, logout
│   ├── BookingController.php # Checkout, cancel, attendee dashboard
│   ├── EventController.php   # Events CRUD, admin & organizer dashboards
│   └── SearchController.php  # Autocomplete & filter endpoints
├── api/
│   ├── index.php          # API router
│   ├── auth.php           # Email availability check
│   ├── bookings.php       # Attendee bookings (Ajax)
│   ├── categories.php     # Category list
│   ├── events.php         # Event filter/search (Ajax)
│   ├── search.php         # Autocomplete titles
│   ├── tickets.php        # Ticket lookup by ref
│   └── users.php          # User info endpoints
├── views/
│   ├── layouts/
│   │   ├── header.php           # HTML head + navbar
│   │   ├── footer.php           # Footer + JS include
│   │   ├── sidebar_admin.php    # Admin sidebar navigation
│   │   ├── sidebar_attendee.php # Attendee sidebar navigation
│   │   └── sidebar_organizer.php# Organizer sidebar navigation
│   ├── auth/
│   │   ├── login.php      # Login form
│   │   └── register.php   # Registration form
│   ├── events/
│   │   ├── index.php      # Homepage hero + featured events
│   │   ├── detail.php     # Browse listing + single event detail
│   │   └── create.php     # Create/edit event form
│   ├── admin/
│   │   ├── dashboard.php  # Admin stats + pending events
│   │   ├── events.php     # Manage all events
│   │   ├── users.php      # Manage users
│   │   ├── bookings.php   # All bookings table
│   │   └── revenue.php    # Revenue report + charts
│   ├── attendee/
│   │   ├── dashboard.php  # Attendee stats + upcoming bookings
│   │   ├── bookings.php   # My bookings (upcoming/past tabs)
│   │   ├── profile.php    # User profile view
│   │   ├── checkout.php   # Order summary + payment
│   │   ├── confirmation.php # Booking confirmed page
│   │   └── rate.php       # Star rating + review form
│   └── organizer/
│       ├── dashboard.php  # Organizer stats + events table
│       └── home.php       # Redirect to dashboard
├── public/
│   ├── css/
│   │   └── style.css      # Full design system stylesheet
│   ├── js/
│   │   └── search.js      # All client-side JavaScript
│   └── uploads/           # Event cover images
├── sql/
│   └── eventify.sql       # Schema + seed data
└── README.md
```

## Architecture

- **Front Controller**: `index.php` routes all requests via `?page=` parameter
- **Controllers**: Handle business logic, validate input, call models, render views
- **Models**: Data access layer using PDO prepared statements
- **Views**: Pure HTML/PHP templates — no business logic
- **API**: JSON endpoints for Ajax features (search, filter, bookings)

## Features

- 3 user roles: Admin, Organizer, Attendee
- Full CRUD for events with admin approval workflow
- Ajax autocomplete search (300ms debounce)
- Live email validation on registration
- Category filtering without page reload
- Direct booking confirmation at checkout
- Booking confirmation with unique reference IDs
- Star rating and review system
- Revenue reports with visual bar charts
- CSRF protection on all forms
- XSS prevention via htmlspecialchars()
- PDO prepared statements (no raw SQL)
- bcrypt password hashing
- Session-based auth with 2hr expiry
- Responsive design (320px–2560px)

## eSewa Sandbox (Test) Setup

The project is configured for eSewa sandbox with v2 API in `core/EsewaHelper.php`:

- Product Code: `EPAYTEST`
- Secret Key: `8gBm/:&EnhH.1/q`
- Form URL: `https://rc-epay.esewa.com.np/api/epay/main/v2/form`
- Status URL: `https://rc-epay.esewa.com.np/api/epay/transaction/status/`

Callback routes are handled by the front controller:

- Success: `index.php?page=esewa/success`
- Failure: `index.php?page=esewa/failure`

## Security

- All queries use PDO prepared statements
- CSRF tokens on every state-changing form
- Passwords hashed with `password_hash()` (bcrypt)
- File upload MIME validation with `finfo_file()`
- Session regeneration on login
- Role-based access control on all protected pages

## URL Routing

| URL | Page |
|-----|------|
| `index.php` | Homepage |
| `index.php?page=events` | Browse events |
| `index.php?page=event&id=1` | Event detail |
| `index.php?page=login` | Login |
| `index.php?page=register` | Register |
| `index.php?page=checkout` | Checkout (POST) |
| `index.php?page=attendee/dashboard` | Attendee dashboard |
| `index.php?page=organizer/dashboard` | Organizer dashboard |
| `index.php?page=admin/dashboard` | Admin dashboard |
