<?php
/**
 * Tickets API
 *
 * GET  ?ref=X             — Get ticket/booking by reference (authenticated)
 * GET  ?id=X              — Get ticket/booking by booking ID (authenticated)
 * GET  ?action=active     — Active tickets for current attendee (attendee)
 * GET  ?action=payment&id=X  — Payment info for a booking (authenticated)
 */

$method = getRequestMethod();

if ($method !== 'GET') {
    jsonResponse(405, ['error' => 'Method not allowed. Use GET.']);
}

$action = $_GET['action'] ?? '';
$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$ref    = trim($_GET['ref'] ?? '');

$ticketModel = new Ticket();

// ── Action routes ────────────────────────────────────────────

if ($action) {
    switch ($action) {

        // Active tickets for current attendee
        case 'active':
            $user = requireApiAuth();
            requireApiRole($user, 'attendee');

            $tickets = $ticketModel->getActiveByAttendee($user['user_id']);
            jsonResponse(200, ['count' => count($tickets), 'tickets' => $tickets]);
            break;

        // Payment info for a booking
        case 'payment':
            $user = requireApiAuth();
            if (!$id) jsonResponse(422, ['error' => 'Booking id is required.']);

            $payment = $ticketModel->getPaymentInfo($id);
            if (!$payment) jsonResponse(404, ['error' => 'Payment not found.']);

            jsonResponse(200, ['payment' => $payment]);
            break;

        default:
            jsonResponse(400, ['error' => 'Unknown action.', 'actions' => ['active', 'payment']]);
    }
    exit;
}

// ── Lookup by ref or id ──────────────────────────────────────

$user = requireApiAuth();

if ($ref) {
    $ticket = $ticketModel->getByRef($ref);
    if (!$ticket) jsonResponse(404, ['error' => 'Ticket not found.']);

    // Attendees can only see their own tickets
    if ($user['role'] === 'attendee' && $ticket['attendee_id'] != $user['user_id']) {
        jsonResponse(403, ['error' => 'Access denied.']);
    }

    jsonResponse(200, ['ticket' => $ticket]);
}

if ($id) {
    $ticket = $ticketModel->getByBooking($id);
    if (!$ticket) jsonResponse(404, ['error' => 'Ticket not found.']);

    if ($user['role'] === 'attendee' && $ticket['attendee_id'] != $user['user_id']) {
        jsonResponse(403, ['error' => 'Access denied.']);
    }

    jsonResponse(200, ['ticket' => $ticket]);
}

jsonResponse(422, ['error' => 'Provide ?ref=X or ?id=X or ?action=active']);
