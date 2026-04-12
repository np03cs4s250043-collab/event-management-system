<?php
require_once __DIR__ . '/../config/db_connect.php';

class Event {
    private PDO $db;

    public function __construct() { $this->db = getDB(); }

    /**
     * Common SELECT fragment that aliases new columns to old names for view compatibility.
     */
    private function selectFields(string $eAlias = 'e'): string {
        return "$eAlias.*, $eAlias.id AS event_id, $eAlias.capacity AS max_capacity,
                DATE($eAlias.date_start) AS event_date, TIME($eAlias.date_start) AS event_time,
                ec.name AS category,
                COALESCE((SELECT MIN(t.price) FROM tickets t WHERE t.event_id = $eAlias.id), 0) AS ticket_price,
                $eAlias.capacity - COALESCE((SELECT SUM(t.quantity_sold) FROM tickets t WHERE t.event_id = $eAlias.id), 0) AS available_seats";
    }

    private function generateSlug(string $title): string {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title), '-'));
        return $slug . '-' . bin2hex(random_bytes(4));
    }

    private function lookupCategoryId(string $categoryName): ?int {
        $stmt = $this->db->prepare('SELECT id FROM event_categories WHERE name = ?');
        $stmt->execute([$categoryName]);
        $id = $stmt->fetchColumn();
        return $id !== false ? (int) $id : null;
    }

    public function create(array $data): int {
        $categoryId = $data['category_id'] ?? $this->lookupCategoryId($data['category'] ?? '');
        $slug = $this->generateSlug($data['title']);
        $dateStart = ($data['event_date'] ?? '') . ' ' . ($data['event_time'] ?? '00:00:00');
        $dateEnd = $dateStart; // same as start if no separate end provided
        $city = $data['city'] ?? '';

        $stmt = $this->db->prepare('INSERT INTO events (organizer_id, category_id, title, slug, description, venue, city, date_start, date_end, capacity, cover_image, status) VALUES (?,?,?,?,?,?,?,?,?,?,?,?)');
        $stmt->execute([
            $data['organizer_id'], $categoryId, $data['title'], $slug, $data['description'],
            $data['venue'], $city, $dateStart, $dateEnd,
            $data['max_capacity'] ?? $data['capacity'] ?? 100,
            $data['cover_image'] ?? null, 'draft'
        ]);
        $eventId = (int) $this->db->lastInsertId();

        // Create a default ticket tier for this event
        $ticketPrice = $data['ticket_price'] ?? 0;
        $capacity = $data['max_capacity'] ?? $data['capacity'] ?? 100;
        $stmtT = $this->db->prepare('INSERT INTO tickets (event_id, name, price, quantity) VALUES (?, ?, ?, ?)');
        $stmtT->execute([$eventId, 'General Admission', $ticketPrice, $capacity]);

        return $eventId;
    }

    public function update(int $id, array $data): bool {
        $categoryId = $data['category_id'] ?? $this->lookupCategoryId($data['category'] ?? '');
        $dateStart = ($data['event_date'] ?? '') . ' ' . ($data['event_time'] ?? '00:00:00');
        $city = $data['city'] ?? '';

        $stmt = $this->db->prepare('UPDATE events SET category_id=?, title=?, description=?, venue=?, city=?, date_start=?, date_end=?, capacity=?, cover_image=COALESCE(?,cover_image) WHERE id=?');
        $result = $stmt->execute([
            $categoryId, $data['title'], $data['description'], $data['venue'], $city,
            $dateStart, $dateStart,
            $data['max_capacity'] ?? $data['capacity'] ?? 100,
            $data['cover_image'] ?? null, $id
        ]);

        // Update default ticket price if provided
        if (isset($data['ticket_price'])) {
            $stmtT = $this->db->prepare('UPDATE tickets SET price=?, quantity=? WHERE event_id=? LIMIT 1');
            $stmtT->execute([$data['ticket_price'], $data['max_capacity'] ?? $data['capacity'] ?? 100, $id]);
        }

        return $result;
    }

    public function delete(int $id): bool {
        $stmt = $this->db->prepare('DELETE FROM events WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function findById(int $id): ?array {
        $fields = $this->selectFields('e');
        $stmt = $this->db->prepare("SELECT $fields, u.name AS organizer_name FROM events e JOIN users u ON e.organizer_id = u.id JOIN event_categories ec ON e.category_id = ec.id WHERE e.id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getApproved(string $search = '', string $category = '', int $limit = 6, int $offset = 0): array {
        $fields = $this->selectFields('e');
        $sql = "SELECT $fields, u.name AS organizer_name FROM events e JOIN users u ON e.organizer_id = u.id JOIN event_categories ec ON e.category_id = ec.id WHERE e.status = 'published'";
        $params = [];
        if ($search)   { $sql .= ' AND (e.title LIKE ? OR e.description LIKE ? OR e.venue LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if ($category) { $sql .= ' AND ec.name = ?'; $params[] = $category; }
        $sql .= ' ORDER BY e.date_start ASC LIMIT ? OFFSET ?';
        $params[] = $limit; $params[] = $offset;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countApproved(string $search = '', string $category = ''): int {
        $sql = "SELECT COUNT(*) FROM events e JOIN event_categories ec ON e.category_id = ec.id WHERE e.status = 'published'";
        $params = [];
        if ($search)   { $sql .= ' AND (e.title LIKE ? OR e.description LIKE ? OR e.venue LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; $params[] = "%$search%"; }
        if ($category) { $sql .= ' AND ec.name = ?'; $params[] = $category; }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function searchTitles(string $q, int $limit = 8): array {
        $stmt = $this->db->prepare("SELECT e.id AS event_id, e.title, ec.name AS category FROM events e JOIN event_categories ec ON e.category_id = ec.id WHERE e.status = 'published' AND e.title LIKE ? LIMIT ?");
        $stmt->execute(["%$q%", $limit]);
        return $stmt->fetchAll();
    }

    public function getByOrganizer(int $orgId): array {
        $fields = $this->selectFields('e');
        $stmt = $this->db->prepare("SELECT $fields, ec.name AS category,
            (SELECT COUNT(*) FROM bookings b WHERE b.event_id = e.id AND b.status='confirmed') AS bookings_count,
            (SELECT COALESCE(SUM(b.total_amount),0) FROM bookings b WHERE b.event_id = e.id AND b.status='confirmed') AS revenue
            FROM events e JOIN event_categories ec ON e.category_id = ec.id WHERE e.organizer_id = ? ORDER BY e.created_at DESC");
        $stmt->execute([$orgId]);
        return $stmt->fetchAll();
    }

    public function getPending(): array {
        $fields = $this->selectFields('e');
        $stmt = $this->db->prepare("SELECT $fields, u.name AS organizer_name FROM events e JOIN users u ON e.organizer_id = u.id JOIN event_categories ec ON e.category_id = ec.id WHERE e.status = 'draft' ORDER BY e.created_at DESC");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getAllAdmin(string $search = '', string $category = '', string $status = '', int $limit = 20, int $offset = 0): array {
        $fields = $this->selectFields('e');
        $sql = "SELECT $fields, u.name AS organizer_name FROM events e JOIN users u ON e.organizer_id = u.id JOIN event_categories ec ON e.category_id = ec.id WHERE 1=1";
        $params = [];
        if ($search)   { $sql .= ' AND (e.title LIKE ?)'; $params[] = "%$search%"; }
        if ($category) { $sql .= ' AND ec.name = ?'; $params[] = $category; }
        if ($status)   { $sql .= ' AND e.status = ?'; $params[] = $status; }
        $sql .= ' ORDER BY e.created_at DESC LIMIT ? OFFSET ?';
        $params[] = $limit; $params[] = $offset;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countAllAdmin(string $search = '', string $category = '', string $status = ''): int {
        $sql = 'SELECT COUNT(*) FROM events e JOIN event_categories ec ON e.category_id = ec.id WHERE 1=1';
        $params = [];
        if ($search)   { $sql .= ' AND e.title LIKE ?'; $params[] = "%$search%"; }
        if ($category) { $sql .= ' AND ec.name = ?'; $params[] = $category; }
        if ($status)   { $sql .= ' AND e.status = ?'; $params[] = $status; }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function setStatus(int $id, string $status): bool {
        $stmt = $this->db->prepare('UPDATE events SET status = ? WHERE id = ?');
        return $stmt->execute([$status, $id]);
    }

    public function decrementSeats(int $eventId, int $qty): bool {
        // In the new schema, increment quantity_sold on the ticket
        $stmt = $this->db->prepare('UPDATE tickets SET quantity_sold = quantity_sold + ? WHERE event_id = ? AND (quantity - quantity_sold) >= ? LIMIT 1');
        return $stmt->execute([$qty, $eventId, $qty]);
    }

    public function totalEvents(): int {
        return (int) $this->db->query('SELECT COUNT(*) FROM events')->fetchColumn();
    }

    public function getAvgRating(int $eventId): float {
        $stmt = $this->db->prepare('SELECT AVG(rating) FROM reviews WHERE event_id = ?');
        $stmt->execute([$eventId]);
        return round((float) $stmt->fetchColumn(), 1);
    }

    public function getReviews(int $eventId): array {
        $stmt = $this->db->prepare('SELECT r.*, r.comment AS review_text, u.name AS full_name FROM reviews r JOIN users u ON r.user_id = u.id WHERE r.event_id = ? ORDER BY r.created_at DESC');
        $stmt->execute([$eventId]);
        return $stmt->fetchAll();
    }
}