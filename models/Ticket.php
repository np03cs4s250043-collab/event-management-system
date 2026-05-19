<?php
require_once __DIR__ . '/../config/db_connect.php';
<<<<<<< HEAD

class Ticket {
    private PDO $db;

    public function __construct() { $this->db = getDB(); }

=======
// Ticket model class
class Ticket {
    // Store database connection
    private PDO $db;

    // Constructor - connect database
    public function __construct() { $this->db = getDB(); }

    /**
     * Get ticket details using booking ID
     */
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
    public function getByBooking(int $bookingId): ?array {
        $stmt = $this->db->prepare('SELECT b.*, b.id AS booking_id, e.title, DATE(e.date_start) AS event_date, TIME(e.date_start) AS event_time, e.venue, ec.name AS category, e.cover_image, u.name AS attendee_name, u.name AS full_name, u.email AS attendee_email FROM bookings b JOIN events e ON b.event_id = e.id JOIN event_categories ec ON e.category_id = ec.id JOIN users u ON b.attendee_id = u.id WHERE b.id = ?');
        $stmt->execute([$bookingId]);
        return $stmt->fetch() ?: null;
    }

<<<<<<< HEAD
=======
    /**
     * Get ticket details using booking reference
     */
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
    public function getByRef(string $ref): ?array {
        $stmt = $this->db->prepare('SELECT b.*, b.id AS booking_id, e.title, DATE(e.date_start) AS event_date, TIME(e.date_start) AS event_time, e.venue, ec.name AS category, e.cover_image, u.name AS attendee_name, u.name AS full_name, u.email AS attendee_email FROM bookings b JOIN events e ON b.event_id = e.id JOIN event_categories ec ON e.category_id = ec.id JOIN users u ON b.attendee_id = u.id WHERE b.booking_ref = ?');
        $stmt->execute([$ref]);
        return $stmt->fetch() ?: null;
    }

<<<<<<< HEAD
=======
    /**
     * Get all active tickets for attendee
     * Only confirmed future events are included
     */
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
    public function getActiveByAttendee(int $attendeeId): array {
        $stmt = $this->db->prepare('SELECT b.*, b.id AS booking_id, e.title, DATE(e.date_start) AS event_date, TIME(e.date_start) AS event_time, e.venue, ec.name AS category FROM bookings b JOIN events e ON b.event_id = e.id JOIN event_categories ec ON e.category_id = ec.id WHERE b.attendee_id = ? AND b.status = "confirmed" AND DATE(e.date_start) >= CURDATE() ORDER BY e.date_start ASC');
        $stmt->execute([$attendeeId]);
        return $stmt->fetchAll();
    }

<<<<<<< HEAD
=======
    /**
     * Get payment information using booking ID
     */
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
    public function getPaymentInfo(int $bookingId): ?array {
        $stmt = $this->db->prepare('SELECT * FROM payments WHERE booking_id = ?');
        $stmt->execute([$bookingId]);
        return $stmt->fetch() ?: null;
    }
}
