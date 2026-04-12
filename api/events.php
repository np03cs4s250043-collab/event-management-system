<?php
/**
 * Events API
 *
 * GET       — List published events (public, supports ?search, ?category, ?page, ?limit)
 * GET    ?id=X              — Single event detail (public)
 * POST                      — Create event (organizer)
 * PUT    ?id=X              — Update event (organizer, own events only)
 * DELETE ?id=X              — Delete event (organizer own / admin any)
 *
 * GET    ?action=organizer           — Organizer's own events (organizer)
 * GET    ?action=pending             — Pending events awaiting approval (admin)
 * GET    ?action=all                 — All events with filters (admin)
 * POST   ?action=approve&id=X       — Approve event (admin)
 * POST   ?action=reject&id=X        — Reject event (admin)
 * GET    ?action=reviews&id=X       — Get reviews for an event (public)
 */

$method = getRequestMethod();
$action = $_GET['action'] ?? '';
$id     = isset($_GET['id']) ? (int) $_GET['id'] : 0;

$eventModel = new Event();

// ── Action-based routes ──────────────────────────────────────

if ($action) {
    switch ($action) {

        // Organizer's own events
        case 'organizer':
            if ($method !== 'GET') jsonResponse(405, ['error' => 'Use GET.']);
            $user = requireApiAuth();
            requireApiRole($user, 'organizer');
            $events = $eventModel->getByOrganizer($user['user_id']);
            jsonResponse(200, ['events' => $events]);
            break;

        // Admin: pending events
        case 'pending':
            if ($method !== 'GET') jsonResponse(405, ['error' => 'Use GET.']);
            $user = requireApiAuth();
            requireApiRole($user, 'admin');
            $events = $eventModel->getPending();
            jsonResponse(200, ['events' => $events]);
            break;

        // Admin: all events with filters
        case 'all':
            if ($method !== 'GET') jsonResponse(405, ['error' => 'Use GET.']);
            $user = requireApiAuth();
            requireApiRole($user, 'admin');

            $search   = trim($_GET['search'] ?? '');
            $category = $_GET['category'] ?? '';
            $status   = $_GET['status'] ?? '';
            $page     = max(1, (int) ($_GET['page'] ?? 1));
            $limit    = min(50, max(1, (int) ($_GET['limit'] ?? 20)));
            $offset   = ($page - 1) * $limit;

            $total  = $eventModel->countAllAdmin($search, $category, $status);
            $events = $eventModel->getAllAdmin($search, $category, $status, $limit, $offset);

            jsonResponse(200, [
                'events'     => $events,
                'pagination' => [
                    'total'        => $total,
                    'page'         => $page,
                    'limit'        => $limit,
                    'total_pages'  => max(1, (int) ceil($total / $limit)),
                ]
            ]);
            break;

        // Admin: approve event
        case 'approve':
            if ($method !== 'POST') jsonResponse(405, ['error' => 'Use POST.']);
            if (!$id) jsonResponse(422, ['error' => 'Event id is required.']);
            $user = requireApiAuth();
            requireApiRole($user, 'admin');

            $event = $eventModel->findById($id);
            if (!$event) jsonResponse(404, ['error' => 'Event not found.']);

            $eventModel->setStatus($id, 'published');
            jsonResponse(200, ['message' => 'Event approved and published.']);
            break;

        // Admin: reject event
        case 'reject':
            if ($method !== 'POST') jsonResponse(405, ['error' => 'Use POST.']);
            if (!$id) jsonResponse(422, ['error' => 'Event id is required.']);
            $user = requireApiAuth();
            requireApiRole($user, 'admin');

            $event = $eventModel->findById($id);
            if (!$event) jsonResponse(404, ['error' => 'Event not found.']);

            $eventModel->setStatus($id, 'cancelled');
            jsonResponse(200, ['message' => 'Event rejected.']);
            break;

        // Public: reviews for an event
        case 'reviews':
            if ($method !== 'GET') jsonResponse(405, ['error' => 'Use GET.']);
            if (!$id) jsonResponse(422, ['error' => 'Event id is required.']);

            $event = $eventModel->findById($id);
            if (!$event) jsonResponse(404, ['error' => 'Event not found.']);

            $reviews   = $eventModel->getReviews($id);
            $avgRating = $eventModel->getAvgRating($id);
            jsonResponse(200, [
                'event_id'   => $id,
                'avg_rating' => $avgRating,
                'count'      => count($reviews),
                'reviews'    => $reviews,
            ]);
            break;

        default:
            jsonResponse(400, ['error' => 'Unknown action.', 'actions' => ['organizer', 'pending', 'all', 'approve', 'reject', 'reviews']]);
    }
    exit;
}

// ── Standard REST routes ─────────────────────────────────────

switch ($method) {

    // ── GET: List or detail ──────────────────────────────────
    case 'GET':
        if ($id) {
            // Single event detail
            $event = $eventModel->findById($id);
            if (!$event || $event['status'] !== 'published') {
                jsonResponse(404, ['error' => 'Event not found.']);
            }
            $avgRating = $eventModel->getAvgRating($id);
            $reviews   = $eventModel->getReviews($id);
            $seatPercent = $event['max_capacity'] > 0
                ? round(($event['max_capacity'] - $event['available_seats']) / $event['max_capacity'] * 100)
                : 0;

            jsonResponse(200, [
                'event'        => $event,
                'avg_rating'   => $avgRating,
                'reviews'      => $reviews,
                'seat_percent' => $seatPercent,
            ]);
        }

        // List published events
        $search   = trim($_GET['search'] ?? '');
        $category = $_GET['category'] ?? '';
        $page     = max(1, (int) ($_GET['page'] ?? 1));
        $limit    = min(50, max(1, (int) ($_GET['limit'] ?? 6)));
        $offset   = ($page - 1) * $limit;

        $total  = $eventModel->countApproved($search, $category);
        $events = $eventModel->getApproved($search, $category, $limit, $offset);

        jsonResponse(200, [
            'events'     => $events,
            'pagination' => [
                'total'       => $total,
                'page'        => $page,
                'limit'       => $limit,
                'total_pages' => max(1, (int) ceil($total / $limit)),
            ]
        ]);
        break;

    // ── POST: Create event ───────────────────────────────────
    case 'POST':
        $user = requireApiAuth();
        requireApiRole($user, 'organizer');

        $input = jsonInput();
        $data  = [
            'organizer_id'  => $user['user_id'],
            'title'         => trim($input['title'] ?? ''),
            'description'   => trim($input['description'] ?? ''),
            'category'      => $input['category'] ?? '',
            'category_id'   => isset($input['category_id']) ? (int) $input['category_id'] : null,
            'event_date'    => $input['event_date'] ?? '',
            'event_time'    => $input['event_time'] ?? '00:00:00',
            'venue'         => trim($input['venue'] ?? ''),
            'city'          => trim($input['city'] ?? ''),
            'max_capacity'  => (int) ($input['max_capacity'] ?? 100),
            'ticket_price'  => (float) ($input['ticket_price'] ?? 0),
            'cover_image'   => null,
        ];

        $errors = [];
        if (!$data['title'])       $errors[] = 'Title is required.';
        if (!$data['description']) $errors[] = 'Description is required.';
        if (!$data['event_date'])  $errors[] = 'Event date is required (YYYY-MM-DD).';
        if (!$data['venue'])       $errors[] = 'Venue is required.';
        if ($data['max_capacity'] < 1)  $errors[] = 'Capacity must be at least 1.';
        if ($data['ticket_price'] <= 0) $errors[] = 'Price must be greater than 0.';

        if (!empty($errors)) {
            jsonResponse(422, ['error' => 'Validation failed.', 'details' => $errors]);
        }

        $eventId = $eventModel->create($data);
        $event   = $eventModel->findById($eventId);

        jsonResponse(201, [
            'message' => 'Event created. It will be visible after admin approval.',
            'event'   => $event,
        ]);
        break;

    // ── PUT: Update event ────────────────────────────────────
    case 'PUT':
        if (!$id) jsonResponse(422, ['error' => 'Event id is required.']);
        $user = requireApiAuth();
        requireApiRole($user, 'organizer');

        $event = $eventModel->findById($id);
        if (!$event) jsonResponse(404, ['error' => 'Event not found.']);
        if ($event['organizer_id'] != $user['user_id']) {
            jsonResponse(403, ['error' => 'You can only edit your own events.']);
        }

        $input = jsonInput();
        $data  = [
            'title'        => trim($input['title'] ?? $event['title']),
            'description'  => trim($input['description'] ?? $event['description']),
            'category'     => $input['category'] ?? $event['category'],
            'category_id'  => isset($input['category_id']) ? (int) $input['category_id'] : null,
            'event_date'   => $input['event_date'] ?? $event['event_date'],
            'event_time'   => $input['event_time'] ?? $event['event_time'],
            'venue'        => trim($input['venue'] ?? $event['venue']),
            'city'         => trim($input['city'] ?? ($event['city'] ?? '')),
            'max_capacity' => (int) ($input['max_capacity'] ?? $event['max_capacity']),
            'ticket_price' => isset($input['ticket_price']) ? (float) $input['ticket_price'] : ($event['ticket_price'] ?? 0),
            'cover_image'  => null,
        ];

        $eventModel->update($id, $data);
        $updated = $eventModel->findById($id);

        jsonResponse(200, ['message' => 'Event updated.', 'event' => $updated]);
        break;

    // ── DELETE: Delete event ─────────────────────────────────
    case 'DELETE':
        if (!$id) jsonResponse(422, ['error' => 'Event id is required.']);
        $user = requireApiAuth();

        $event = $eventModel->findById($id);
        if (!$event) jsonResponse(404, ['error' => 'Event not found.']);

        // Organizers can delete own events, admins can delete any
        if ($user['role'] === 'organizer' && $event['organizer_id'] != $user['user_id']) {
            jsonResponse(403, ['error' => 'You can only delete your own events.']);
        }
        if ($user['role'] === 'attendee') {
            jsonResponse(403, ['error' => 'Access denied.']);
        }

        $eventModel->delete($id);
        jsonResponse(200, ['message' => 'Event deleted.']);
        break;

    default:
        jsonResponse(405, ['error' => 'Method not allowed.']);
}