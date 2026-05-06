<?php
// Main API router file for handling all incoming requests
declare(strict_types=1);

// Load helper functions (JSON response, auth, etc.)
require_once __DIR__ . '/../core/api_helpers.php';

setCorsHeaders();
//Get URL and break it into parts
$url      = trim($_GET['_url'] ?? '', '/');
$segments = $url !== '' ? explode('/', $url) : [];
$method   = getMethod();

// Extract main parts of the route
$resource    = $segments[0] ?? '';
$resourceId  = isset($segments[1]) ? $segments[1] : null;
$subResource = $segments[2] ?? null;

// Map each resource to its handler file
$handlers = [
    'auth'       => __DIR__ . '/auth.php',
    'users'      => __DIR__ . '/users.php',
    'events'     => __DIR__ . '/events.php',
    'tickets'    => __DIR__ . '/tickets.php',
    'bookings'   => __DIR__ . '/bookings.php',
    'categories' => __DIR__ . '/categories.php',
    'search'     => __DIR__ . '/search.php',
];

// If no resource is provided, show API info
if ($resource === '' ) {
    jsonSuccess([
        'message' => 'EMS REST API v1',
        'endpoints' => [
            'POST   /api/auth/login',
            'POST   /api/auth/register',
            'GET    /api/users',
            'GET    /api/users/{id}',
            'PUT    /api/users/{id}',
            'PATCH  /api/users/{id}/toggle',
            'GET    /api/events',
            'GET    /api/events/{id}',
            'POST   /api/events',
            'PUT    /api/events/{id}',
            'PATCH  /api/events/{id}/cancel',
            'GET    /api/events/{id}/tickets',
            'POST   /api/events/{id}/tickets',
            'GET    /api/bookings',
            'POST   /api/bookings',
            'PATCH  /api/bookings/{id}/cancel',
            'GET    /api/categories',
            'GET    /api/search?q=...',
        ],
    ]);
}

if ($resource === 'events' && $subResource === 'tickets') {
    $resource = 'tickets';
    $_GET['event_id'] = $resourceId;
    $resourceId = null;
}

if ($resource === 'events' && $subResource === 'cancel') {
    $_GET['_action'] = 'cancel';
}

if ($resource === 'users' && $subResource === 'toggle') {
    $_GET['_action'] = 'toggle';
}


if ($resource === 'bookings' && $subResource === 'cancel') {
    $_GET['_action'] = 'cancel';
}

if ($resource === 'auth') {
    $_GET['_action'] = $resourceId ?? '';
    $resourceId = null;
}

// If resource is not found, return error
if (!isset($handlers[$resource])) {
    jsonError("Unknown endpoint: /{$resource}", 404);
}

// Store values globally so handler files can use them
$GLOBALS['_resource_id'] = $resourceId;
$GLOBALS['_method']      = $method;

// Load the correct handler file
require_once $handlers[$resource];
