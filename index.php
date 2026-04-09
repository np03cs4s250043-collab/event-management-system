<?php
// index.php — Front Controller / Router

declare(strict_types=1);

require_once __DIR__ . '/config/db_connect.php';
require_once __DIR__ . '/core/csrf_helper.php';
require_once __DIR__ . '/core/session_helper.php';

$page = trim($_GET['page'] ?? 'home');

$routes = [
    'home'              => 'views/home.php',
    'login'             => 'views/auth/login.php',
    'register'          => 'views/auth/register.php',
    'logout'            => 'controllers/AuthController.php',
    'events'            => 'views/events/index.php',
    'event.detail'      => 'views/events/detail.php',
    'event.create'      => 'views/events/create.php',
    'admin.dashboard'   => 'views/admin/dashboard.php',
    'admin.users'       => 'views/admin/users.php',
    'admin.events'      => 'views/admin/events.php',
    'organizer.dashboard' => 'views/organizer/dashboard.php',
    'attendee.dashboard'  => 'views/attendee/dashboard.php',
    'attendee.bookings'   => 'views/attendee/bookings.php',
    'profile'           => 'views/attendee/profile.php',


    'api.search'        => 'controllers/SearchController.php',
    'api.auth'          => 'controllers/AuthController.php',
    'api.event'         => 'controllers/EventController.php',
    'api.booking'       => 'controllers/BookingController.php',
];

$target = $routes[$page] ?? null;

if ($target === null) {
    http_response_code(404);
    echo '<h1>404 — Page Not Found</h1>';
    exit;
}

if (!file_exists(__DIR__ . '/' . $target)) {
    http_response_code(500);
    echo '<h1>500 — View not found: ' . htmlspecialchars($target) . '</h1>';
    exit;
}

require_once __DIR__ . '/' . $target;
