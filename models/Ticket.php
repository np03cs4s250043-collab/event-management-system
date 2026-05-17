<?php
require_once __DIR__ . '/../config/db_connect.php';

class Ticket {
    private PDO $db;

    public function __construct() { $this->db = getDB(); }

    public function getByBooking(int $bookingId): ?array {
        $stmt = $this->db->prepare('SELECT b.*, b.id AS booking_id, e.title, DATE(e.date_start) AS event_date, TIME(e.date_start) AS event_time, e.venue, ec.name AS category, e.cover_image, u.name AS attendee_name, u.name AS full_name, u.email AS attendee_email FROM bookings b JOIN events e ON b.event_id = e.id JOIN event_categories ec ON e.category_id = ec.id JOIN users u ON b.attendee_id = u.id WHERE b.id = ?');
        $stmt->execute([$bookingId]);
        return $stmt->fetch() ?: null;
    }

    public function getByRef(string $ref): ?array {
        $stmt = $this->db->prepare('SELECT b.*, b.id AS booking_id, e.title, DATE(e.date_start) AS event_date, TIME(e.date_start) AS event_time, e.venue, ec.name AS category, e.cover_image, u.name AS attendee_name, u.name AS full_name, u.email AS attendee_email FROM bookings b JOIN events e ON b.event_id = e.id JOIN event_categories ec ON e.category_id = ec.id JOIN users u ON b.attendee_id = u.id WHERE b.booking_ref = ?');
        $stmt->execute([$ref]);
        return $stmt->fetch() ?: null;
    }

    public function getActiveByAttendee(int $attendeeId): array {
        $stmt = $this->db->prepare('SELECT b.*, b.id AS booking_id, e.title, DATE(e.date_start) AS event_date, TIME(e.date_start) AS event_time, e.venue, ec.name AS category FROM bookings b JOIN events e ON b.event_id = e.id JOIN event_categories ec ON e.category_id = ec.id WHERE b.attendee_id = ? AND b.status = "confirmed" AND DATE(e.date_start) >= CURDATE() ORDER BY e.date_start ASC');
        $stmt->execute([$attendeeId]);
        return $stmt->fetchAll();
    }

    public function getPaymentInfo(int $bookingId): ?array {
        $stmt = $this->db->prepare('SELECT * FROM payments WHERE booking_id = ?');
        $stmt->execute([$bookingId]);
        return $stmt->fetch() ?: null;
    }
}