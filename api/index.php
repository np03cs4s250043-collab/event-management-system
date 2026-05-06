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
// Allow requests from any origin (for frontend access)
header('Access-Control-Allow-Origin: *');

// Allow these HTTP methods
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');

// Allow these headers in requests
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Store the preflight response in cache for 24 hours
header('Access-Control-Max-Age: 86400');

// Response will be in JSON format
header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

// Bootstrap
// Database connection
require_once __DIR__ . '/../config/db_connect.php';

// Helper functions (like jsonResponse)
require_once __DIR__ . '/../core/api_helpers.php';

// Authentication logic (Bearer token)
require_once __DIR__ . '/../core/api_auth.php';

// Models (data handling)
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../models/Event.php';
require_once __DIR__ . '/../models/Booking.php';
require_once __DIR__ . '/../models/Ticket.php';

// Determine requested resource
$resource = $_GET['resource'] ?? '';

$allowed = ['auth', 'events', 'search', 'bookings', 'categories', 'tickets', 'users', 'reviews', 'chatbot'];

// If resource is not in allowed list, return error
if (!in_array($resource, $allowed)) {
    jsonResponse(404, [
        'error'     => 'Resource not found.',
        'available' => $allowed,
        'docs'      => 'Use GET /api/index.php?resource={name} to access each endpoint.'
    ]);
}

// Load the corresponding API file (e.g., events.php)
require_once __DIR__ . '/' . $resource . '.php';
