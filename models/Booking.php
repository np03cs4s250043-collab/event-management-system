<?php
require_once __DIR__ . '/../config/db_connect.php';

class Booking {
    private PDO $db;

    public function __construct() { $this->db = getDB(); }

<<<<<<< HEAD
=======
     /**
     * Create new booking
     */
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
    public function create(string $ref, int $attendeeId, int $eventId, int $qty, float $total): int {
        // Get the first ticket for this event
        $stmtT = $this->db->prepare('SELECT id FROM tickets WHERE event_id = ? LIMIT 1');
        $stmtT->execute([$eventId]);
        $ticketId = (int) $stmtT->fetchColumn();

        $stmt = $this->db->prepare('INSERT INTO bookings (booking_ref, attendee_id, event_id, ticket_id, quantity, total_amount) VALUES (?,?,?,?,?,?)');
        $stmt->execute([$ref, $attendeeId, $eventId, $ticketId, $qty, $total]);
        return (int) $this->db->lastInsertId();
    }

<<<<<<< HEAD
=======
     /**
     * Confirm booking
     */
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
    public function confirm(int $id): bool {
        $stmt = $this->db->prepare('UPDATE bookings SET status = "confirmed" WHERE id = ?');
        return $stmt->execute([$id]);
    }

<<<<<<< HEAD
=======
      /**
     * Cancel confirmed booking
     */
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
    public function cancel(int $id, int $attendeeId): bool {
        $stmt = $this->db->prepare('UPDATE bookings SET status = "cancelled", cancelled_at = NOW() WHERE id = ? AND attendee_id = ? AND status = "confirmed"');
        return $stmt->execute([$id, $attendeeId]);
    }

<<<<<<< HEAD
=======
    /**
     * Find booking using booking ID
     */
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
    public function findById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT b.*, b.id AS booking_id, e.title, DATE(e.date_start) AS event_date, TIME(e.date_start) AS event_time, e.venue, ec.name AS category FROM bookings b JOIN events e ON b.event_id = e.id JOIN event_categories ec ON e.category_id = ec.id WHERE b.id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

<<<<<<< HEAD
=======
    /**
     * Find booking using booking reference code
     */
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
    public function findByRef(string $ref): ?array {
        $stmt = $this->db->prepare('SELECT b.*, b.id AS booking_id, e.title, DATE(e.date_start) AS event_date, TIME(e.date_start) AS event_time, e.venue, ec.name AS category, p.method AS payment_method, p.gateway_txn_id AS payment_txn FROM bookings b JOIN events e ON b.event_id = e.id JOIN event_categories ec ON e.category_id = ec.id LEFT JOIN payments p ON p.booking_id = b.id WHERE b.booking_ref = ?');
        $stmt->execute([$ref]);
        return $stmt->fetch() ?: null;
    }

<<<<<<< HEAD
=======
    /**
     * Cancel pending booking
     */
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
    public function cancelPending(int $id): bool {
        $stmt = $this->db->prepare('UPDATE bookings SET status = "cancelled", cancelled_at = NOW() WHERE id = ? AND status = "pending"');
        return $stmt->execute([$id]);
    }

<<<<<<< HEAD
=======
    /**
     * Get attendee bookings
     * upcoming = future events
     * past = completed events
     */
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
    public function getByAttendee(int $attendeeId, string $type = 'upcoming'): array {
        $dateOp = $type === 'upcoming' ? '>=' : '<';
        $order = $type === 'upcoming' ? 'ASC' : 'DESC';
        $stmt = $this->db->prepare("SELECT b.*, b.id AS booking_id, e.title, DATE(e.date_start) AS event_date, TIME(e.date_start) AS event_time, e.venue, ec.name AS category, e.cover_image FROM bookings b JOIN events e ON b.event_id = e.id JOIN event_categories ec ON e.category_id = ec.id WHERE b.attendee_id = ? AND b.status != 'pending' AND DATE(e.date_start) $dateOp CURDATE() ORDER BY e.date_start $order");
        $stmt->execute([$attendeeId]);
        return $stmt->fetchAll();
    }

<<<<<<< HEAD
=======
    /**
     * Get attendee statistics
     */
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
    public function attendeeStats(int $attendeeId): array {
        $stmt = $this->db->prepare('SELECT COUNT(CASE WHEN DATE(e.date_start) >= CURDATE() AND b.status="confirmed" THEN 1 END) AS upcoming, COUNT(CASE WHEN DATE(e.date_start) < CURDATE() THEN 1 END) AS past, COALESCE(SUM(CASE WHEN b.status="confirmed" THEN b.total_amount END), 0) AS total_spent FROM bookings b JOIN events e ON b.event_id = e.id WHERE b.attendee_id = ?');
        $stmt->execute([$attendeeId]);
        return $stmt->fetch();
    }

<<<<<<< HEAD
=======
    /**
     * Get organizer dashboard statistics
     */
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
    public function organizerStats(int $orgId): array {
        $stmt = $this->db->prepare('SELECT COUNT(DISTINCT e.id) AS total_events, COUNT(b.id) AS total_bookings, COALESCE(SUM(CASE WHEN b.status="confirmed" THEN b.total_amount END), 0) AS total_revenue, COUNT(CASE WHEN e.status="published" THEN 1 END) AS active_events FROM events e LEFT JOIN bookings b ON e.id = b.event_id WHERE e.organizer_id = ?');
        $stmt->execute([$orgId]);
        return $stmt->fetch();
    }

<<<<<<< HEAD
=======
    /**
     * Get admin dashboard statistics
     */
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
    public function adminStats(): array {
        $users = (int) getDB()->query('SELECT COUNT(*) FROM users')->fetchColumn();
        $events = (int) getDB()->query('SELECT COUNT(*) FROM events')->fetchColumn();
        $bookings = (int) getDB()->query('SELECT COUNT(*) FROM bookings WHERE status="confirmed"')->fetchColumn();
        $revenue = (float) getDB()->query('SELECT COALESCE(SUM(total_amount),0) FROM bookings WHERE status="confirmed"')->fetchColumn();
        return compact('users', 'events', 'bookings', 'revenue');
    }

<<<<<<< HEAD
=======
     /**
     * Get all bookings for admin panel
     */
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
    public function getAllBookings(int $limit = 20, int $offset = 0): array {
        $stmt = $this->db->prepare('SELECT b.*, b.id AS booking_id, b.created_at AS booked_at, e.title, ec.name AS category, u.name AS attendee_name FROM bookings b JOIN events e ON b.event_id = e.id JOIN event_categories ec ON e.category_id = ec.id JOIN users u ON b.attendee_id = u.id ORDER BY b.created_at DESC LIMIT ? OFFSET ?');
        $stmt->execute([$limit, $offset]);
        return $stmt->fetchAll();
    }

<<<<<<< HEAD
=======
     /**
     * Count total bookings
     */
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
    public function totalBookings(): int {
        return (int) $this->db->query('SELECT COUNT(*) FROM bookings')->fetchColumn();
    }

<<<<<<< HEAD
=======
    /**
     * Get revenue report grouped by event
     */
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
    public function revenueByEvent(): array {
        $stmt = $this->db->prepare('SELECT e.title, e.id AS event_id, COALESCE(SUM(b.total_amount),0) AS revenue, COUNT(b.id) AS tickets FROM events e LEFT JOIN bookings b ON e.id = b.event_id AND b.status="confirmed" WHERE e.status = "published" GROUP BY e.id ORDER BY revenue DESC');
        $stmt->execute();
        return $stmt->fetchAll();
    }

<<<<<<< HEAD
=======
    /**
     * Create payment record
     */
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
    public function createPayment(int $bookingId, string $txnId, float $amount, string $method = 'card'): bool {
        $stmt = $this->db->prepare('INSERT INTO payments (booking_id, amount, currency, method, gateway_txn_id, status, paid_at) VALUES (?,?,?,?,?,?,NOW())');
        return $stmt->execute([$bookingId, $amount, 'NPR', $method, $txnId, 'success']);
    }

<<<<<<< HEAD
=======
    /**
     * Add review for event
     */
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
    public function addReview(int $eventId, int $userId, int $rating, ?string $text): bool {
        $stmt = $this->db->prepare('INSERT INTO reviews (event_id, user_id, rating, comment) VALUES (?,?,?,?)');
        return $stmt->execute([$eventId, $userId, $rating, $text]);
    }
}
