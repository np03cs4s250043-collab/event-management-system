<?php
/**
 * Eventify REST API Router
 *
 * All API requests route through: /api/index.php?resource={name}
 * Or via .htaccess rewrite:       /api/{resource}
 *
 * Authentication: Bearer token in Authorization header
 * Content-Type:   application/json
 */

// CORS headers — allow cross-origin requests
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Access-Control-Max-Age: 86400');
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Bootstrap
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/../core/api_helpers.php';
require_once __DIR__ . '/../core/api_auth.php';
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Ticket.php';

// Determine requested resource
$resource = $_GET['resource'] ?? '';

$allowed = ['auth', 'events', 'search', 'bookings', 'categories', 'tickets', 'users', 'reviews', 'chatbot'];

if (!in_array($resource, $allowed)) {
    jsonResponse(404, [
        'error'     => 'Resource not found.',
        'available' => $allowed,
        'docs'      => 'Use GET /api/index.php?resource={name} to access each endpoint.'
    ]);
}

require_once __DIR__ . '/' . $resource . '.php';
