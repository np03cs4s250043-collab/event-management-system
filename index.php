<?php
/**
 * Eventify — Front Controller (Router)
 * All requests route through this file.
 */

// Bootstrap
require_once __DIR__ . '/config/db_connect.php';
require_once __DIR__ . '/core/session_helper.php';
require_once __DIR__ . '/core/csrf_helper.php';
require_once __DIR__ . '/core/api_helpers.php';

// Determine the requested page.
// eSewa may return to URLs like: index.php?page=esewa/success?data=...
// In that case PHP treats everything after page= as part of page value.
// Normalize it so routing still works and callback params are preserved.
$pageRaw = $_GET['page'] ?? 'home';
$page = is_string($pageRaw) ? $pageRaw : 'home';

if (strpos($page, '?') !== false) {
    [$normalizedPage, $tailQuery] = explode('?', $page, 2);
    $page = $normalizedPage ?: 'home';

    if ($tailQuery !== '') {
        parse_str($tailQuery, $extraQuery);
        foreach ($extraQuery as $k => $v) {
            if (!isset($_GET[$k])) {
                $_GET[$k] = $v;
            }
        }
    }
}

// Route to the appropriate controller action
switch ($page) {

    // ── Public pages ──────────────────────────
    case 'home':
        require_once __DIR__ . '/controllers/EventController.php';
        (new EventController())->home();
        break;

    case 'events':
        require_once __DIR__ . '/controllers/EventController.php';
        $ctrl = new EventController();
        $ctrl->browse();
        break;

    case 'event':
        require_once __DIR__ . '/controllers/EventController.php';
        (new EventController())->detail();
        break;

    // ── Auth ──────────────────────────────────
    case 'login':
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController())->loginPage();
        break;

    case 'register':
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController())->registerPage();
        break;

    case 'logout':
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController())->logout();
        break;

    case 'forgot-password':
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController())->forgotPasswordPage();
        break;

    case 'verify-otp':
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController())->verifyOtpPage();
        break;

    case 'reset-password':
        require_once __DIR__ . '/controllers/AuthController.php';
        (new AuthController())->resetPasswordPage();
        break;

    // ── Booking flow ─────────────────────────
    case 'checkout':
        require_once __DIR__ . '/controllers/BookingController.php';
        (new BookingController())->checkout();
        break;

    case 'confirmation':
        require_once __DIR__ . '/controllers/BookingController.php';
        (new BookingController())->confirmation();
        break;

    // ── Attendee dashboard ───────────────────
    case 'attendee/dashboard':
        require_once __DIR__ . '/controllers/BookingController.php';
        (new BookingController())->attendeeDashboard();
        break;

    case 'attendee/bookings':
        require_once __DIR__ . '/controllers/BookingController.php';
        (new BookingController())->attendeeBookings();
        break;

    // ── eSewa payment flow ───────────────────
    case 'esewa/initiate':
        require_once __DIR__ . '/controllers/BookingController.php';
        (new BookingController())->esewaInitiate();
        break;

    case 'esewa/success':
        require_once __DIR__ . '/controllers/BookingController.php';
        (new BookingController())->esewaSuccess();
        break;

    case 'esewa/failure':
        require_once __DIR__ . '/controllers/BookingController.php';
        (new BookingController())->esewaFailure();
        break;

    case 'attendee/cancel':
        require_once __DIR__ . '/controllers/BookingController.php';
        (new BookingController())->cancel();
        break;

    case 'attendee/rate':
        require_once __DIR__ . '/controllers/BookingController.php';
        (new BookingController())->rate();
        break;

    case 'attendee/profile':
        require_once __DIR__ . '/controllers/BookingController.php';
        (new BookingController())->attendeeProfile();
        break;

    // ── Organizer dashboard ──────────────────
    case 'organizer/dashboard':
        require_once __DIR__ . '/controllers/EventController.php';
        (new EventController())->organizerDashboard();
        break;

    case 'organizer/events':
        require_once __DIR__ . '/controllers/EventController.php';
        (new EventController())->organizerHome();
        break;

    case 'organizer/create':
        require_once __DIR__ . '/controllers/EventController.php';
        (new EventController())->create();
        break;

    case 'organizer/edit':
        require_once __DIR__ . '/controllers/EventController.php';
        (new EventController())->edit();
        break;

    case 'organizer/delete':
        require_once __DIR__ . '/controllers/EventController.php';
        (new EventController())->deleteOrganizer();
        break;

    // ── Admin dashboard ──────────────────────
    case 'admin/dashboard':
        require_once __DIR__ . '/controllers/EventController.php';
        (new EventController())->adminDashboard();
        break;

    case 'admin/events':
        require_once __DIR__ . '/controllers/EventController.php';
        (new EventController())->adminEvents();
        break;

    case 'admin/users':
        require_once __DIR__ . '/controllers/EventController.php';
        (new EventController())->adminUsers();
        break;

    case 'admin/bookings':
        require_once __DIR__ . '/controllers/BookingController.php';
        (new BookingController())->adminBookings();
        break;

    case 'admin/revenue':
        require_once __DIR__ . '/controllers/BookingController.php';
        (new BookingController())->adminRevenue();
        break;

    case 'admin/approve':
        require_once __DIR__ . '/controllers/EventController.php';
        (new EventController())->approve();
        break;

    case 'admin/commission':
        require_once __DIR__ . '/controllers/EventController.php';
        (new EventController())->adminCommission();
        break;

    case 'admin/delete_event':
        require_once __DIR__ . '/controllers/EventController.php';
        (new EventController())->deleteAdmin();
        break;

    case 'admin/toggle_user':
        require_once __DIR__ . '/controllers/EventController.php';
        (new EventController())->toggleUser();
        break;

    case 'organizer/commission':
        require_once __DIR__ . '/controllers/EventController.php';
        (new EventController())->organizerCommission();
        break;

    // ── 404 fallback ─────────────────────────
    default:
        http_response_code(404);
        $pageTitle = 'Page Not Found';
        require_once __DIR__ . '/views/layouts/header.php';
        echo '<div class="container" style="text-align:center;padding:6rem 1rem"><h1 style="font-size:4rem;font-weight:800;color:var(--primary)">404</h1><p style="color:var(--secondary);margin:1rem 0 2rem">Page not found.</p><a href="' . APP_URL . '/index.php" class="btn btn-primary">Go Home</a></div>';
        require_once __DIR__ . '/views/layouts/footer.php';
        break;
}
