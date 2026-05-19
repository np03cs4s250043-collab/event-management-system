<?php
/**
 * Reviews API
 *
 * GET   ?event_id=X    — List reviews for an event (public)
 * POST                 — Add a review (attendee)
 */

$method = getRequestMethod();

switch ($method) {

    // GET: List reviews for an event
    case 'GET':
        $eventId = (int) ($_GET['event_id'] ?? 0);
        if (!$eventId) jsonResponse(422, ['error' => 'event_id is required.']);

        $eventModel = new Event();
        $event = $eventModel->findById($eventId);
        if (!$event) jsonResponse(404, ['error' => 'Event not found.']);

        $reviews   = $eventModel->getReviews($eventId);
        $avgRating = $eventModel->getAvgRating($eventId);

        jsonResponse(200, [
            'event_id'   => $eventId,
            'event_title'=> $event['title'],
            'avg_rating' => $avgRating,
            'count'      => count($reviews),
            'reviews'    => $reviews,
        ]);
        break;

    // POST: Add a review
    case 'POST':
        $user = requireApiAuth();
        requireApiRole($user, 'attendee');

        $input   = jsonInput();
        $eventId = (int) ($input['event_id'] ?? 0);
        $rating  = max(1, min(5, (int) ($input['rating'] ?? 0)));
        $comment = trim($input['comment'] ?? '');

        if (!$eventId) jsonResponse(422, ['error' => 'event_id is required.']);
        if (!$rating)  jsonResponse(422, ['error' => 'rating is required (1-5).']);

        $eventModel = new Event();
        $event = $eventModel->findById($eventId);
        if (!$event) jsonResponse(404, ['error' => 'Event not found.']);

        // Verify the attendee has a confirmed booking for this event
        $db = getDB();
        $stmt = $db->prepare('SELECT 1 FROM bookings WHERE attendee_id = ? AND event_id = ? AND status = "confirmed"');
        $stmt->execute([$user['user_id'], $eventId]);
        if (!$stmt->fetch()) {
            jsonResponse(403, ['error' => 'You can only review events you have attended.']);
        }

        // Check for existing review
        $stmt = $db->prepare('SELECT 1 FROM reviews WHERE event_id = ? AND user_id = ?');
        $stmt->execute([$eventId, $user['user_id']]);
        if ($stmt->fetch()) {
            jsonResponse(409, ['error' => 'You have already reviewed this event.']);
        }

        $bookingModel = new Booking();
        $bookingModel->addReview($eventId, $user['user_id'], $rating, $comment ?: null);

        jsonResponse(201, [
            'message' => 'Review submitted.',
            'review'  => [
                'event_id' => $eventId,
                'user_id'  => (int) $user['user_id'],
                'rating'   => $rating,
                'comment'  => $comment ?: null,
            ]
        ]);
        break;

    default:
        jsonResponse(405, ['error' => 'Method not allowed.']);
}
