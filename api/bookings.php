<?php
/**
 * Bookings API
 *
 * GET                               — My bookings (attendee, ?type=upcoming|past)
 * POST                              — Create a booking (attendee)
 * POST   ?action=cancel&id=X       — Cancel a booking (attendee)
 * GET    ?action=detail&id=X       — Booking detail (attendee own / admin)
 * GET    ?action=ref&ref=X         — Booking by reference (attendee own / admin)
 *
 * GET    ?action=stats              — Dashboard stats (role-based)
 * GET    ?action=all                — All bookings (admin, ?page, ?limit)
 * GET    ?action=revenue            — Revenue report (admin)
 */

$method = getRequestMethod();
$action = $_GET['action'] ?? '';
$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$bookingModel = new Booking();
$eventModel   = new Event();

// ── Action routes ────────────────────────────────────────────

if ($action) {
    switch ($action) {

        // Cancel a booking
        case 'cancel':
            if ($method !== 'POST') jsonResponse(405, ['error' => 'Use POST.']);
            $user = requireApiAuth();
            requireApiRole($user, 'attendee');

            $bookingId = $id ?: (int) (jsonInput()['booking_id'] ?? 0);
            if (!$bookingId) jsonResponse(422, ['error' => 'Booking id is required.']);

            $result = $bookingModel->cancel($bookingId, $user['user_id']);
            if (!$result) {
                jsonResponse(400, ['error' => 'Unable to cancel. Booking not found or already cancelled.']);
            }
            jsonResponse(200, ['message' => 'Booking cancelled.']);
            break;

        // Single booking detail
        case 'detail':
            if ($method !== 'GET') jsonResponse(405, ['error' => 'Use GET.']);
            $user = requireApiAuth();
            if (!$id) jsonResponse(422, ['error' => 'Booking id is required.']);

            $booking = $bookingModel->findById($id);
            if (!$booking) jsonResponse(404, ['error' => 'Booking not found.']);

            // Attendees can only see their own bookings
            if ($user['role'] === 'attendee' && $booking['attendee_id'] != $user['user_id']) {
                jsonResponse(403, ['error' => 'Access denied.']);
            }

            jsonResponse(200, ['booking' => $booking]);
            break;

        // Booking by reference
        case 'ref':
            if ($method !== 'GET') jsonResponse(405, ['error' => 'Use GET.']);
            $user = requireApiAuth();
            $ref  = trim($_GET['ref'] ?? '');
            if (!$ref) jsonResponse(422, ['error' => 'Booking ref is required.']);

            $booking = $bookingModel->findByRef($ref);
            if (!$booking) jsonResponse(404, ['error' => 'Booking not found.']);

            if ($user['role'] === 'attendee' && $booking['attendee_id'] != $user['user_id']) {
                jsonResponse(403, ['error' => 'Access denied.']);
            }

            jsonResponse(200, ['booking' => $booking]);
            break;

        // Dashboard stats (role-aware)
        case 'stats':
            if ($method !== 'GET') jsonResponse(405, ['error' => 'Use GET.']);
            $user = requireApiAuth();

            if ($user['role'] === 'admin') {
                $stats = $bookingModel->adminStats();
            } elseif ($user['role'] === 'organizer') {
                $stats = $bookingModel->organizerStats($user['user_id']);
            } else {
                $stats = $bookingModel->attendeeStats($user['user_id']);
            }

            jsonResponse(200, ['stats' => $stats]);
            break;

        // Admin: all bookings
        case 'all':
            if ($method !== 'GET') jsonResponse(405, ['error' => 'Use GET.']);
            $user = requireApiAuth();
            requireApiRole($user, 'admin');

            $page   = max(1, (int) ($_GET['page'] ?? 1));
            $limit  = min(50, max(1, (int) ($_GET['limit'] ?? 20)));
            $offset = ($page - 1) * $limit;

            $total    = $bookingModel->totalBookings();
            $bookings = $bookingModel->getAllBookings($limit, $offset);

            jsonResponse(200, [
                'bookings'   => $bookings,
                'pagination' => [
                    'total'       => $total,
                    'page'        => $page,
                    'limit'       => $limit,
                    'total_pages' => max(1, (int) ceil($total / $limit)),
                ]
            ]);
            break;

        // Admin: revenue report
        case 'revenue':
            if ($method !== 'GET') jsonResponse(405, ['error' => 'Use GET.']);
            $user = requireApiAuth();
            requireApiRole($user, 'admin');

            $stats          = $bookingModel->adminStats();
            $revenueByEvent = $bookingModel->revenueByEvent();

            jsonResponse(200, [
                'summary'          => $stats,
                'revenue_by_event' => $revenueByEvent,
            ]);
            break;

        default:
            jsonResponse(400, ['error' => 'Unknown action.', 'actions' => ['cancel', 'detail', 'ref', 'stats', 'all', 'revenue']]);
    }
    exit;
}

// ── Standard REST routes ─────────────────────────────────────

switch ($method) {

    // GET: Attendee's own bookings
    case 'GET':
        $user = requireApiAuth();
        requireApiRole($user, 'attendee');

        $type = $_GET['type'] ?? 'upcoming';
        if (!in_array($type, ['upcoming', 'past'])) $type = 'upcoming';

        $bookings = $bookingModel->getByAttendee($user['user_id'], $type);

        jsonResponse(200, [
            'type'     => $type,
            'count'    => count($bookings),
            'bookings' => $bookings,
        ]);
        break;

    // POST: Create a booking
    case 'POST':
        $user = requireApiAuth();
        requireApiRole($user, 'attendee');

        $input   = jsonInput();
        $eventId = (int) ($input['event_id'] ?? 0);
        $qty     = max(1, min(5, (int) ($input['quantity'] ?? 1)));

        if (!$eventId) jsonResponse(422, ['error' => 'event_id is required.']);

        $event = $eventModel->findById($eventId);
        if (!$event || $event['status'] !== 'published') {
            jsonResponse(404, ['error' => 'Event not found or not available.']);
        }
        if ($event['available_seats'] < $qty) {
            jsonResponse(400, ['error' => 'Not enough seats available. Available: ' . $event['available_seats']]);
        }

        $total      = $event['ticket_price'] * $qty;
        $bookingRef = generateBookingRef();
        $bookingId  = $bookingModel->create($bookingRef, $user['user_id'], $eventId, $qty, $total);
        $bookingModel->confirm($bookingId);
        $eventModel->decrementSeats($eventId, $qty);
        $bookingModel->createPayment($bookingId, 'API-' . $bookingRef, $total);

        $booking = $bookingModel->findById($bookingId);

        jsonResponse(201, [
            'message' => 'Booking confirmed.',
            'booking' => $booking,
        ]);
        break;

    default:
        jsonResponse(405, ['error' => 'Method not allowed.']);
}