<?php
require_once __DIR__ . '/../config/db_connect.php';

class EventNegotiation {
    private PDO $db;

    public function __construct() {
        $this->db = getDB();
        $this->ensureTable();
    }

    private function ensureTable(): void {
        $this->db->exec("CREATE TABLE IF NOT EXISTS event_negotiations (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            event_id INT UNSIGNED NOT NULL UNIQUE,
            organizer_offer_percent DECIMAL(5,2) NOT NULL,
            admin_counter_percent DECIMAL(5,2) DEFAULT NULL,
            agreed_percent DECIMAL(5,2) DEFAULT NULL,
            organizer_note VARCHAR(255) DEFAULT NULL,
            admin_note VARCHAR(255) DEFAULT NULL,
            status ENUM('pending_admin','countered','agreed','rejected') NOT NULL DEFAULT 'pending_admin',
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            CONSTRAINT fk_neg_event FOREIGN KEY (event_id) REFERENCES events(id) ON DELETE CASCADE ON UPDATE CASCADE,
            INDEX idx_neg_status (status)
        ) ENGINE=InnoDB");
    }

    public function createForEvent(int $eventId, float $offerPercent, ?string $organizerNote = null): void {
        $stmt = $this->db->prepare('INSERT INTO event_negotiations (event_id, organizer_offer_percent, organizer_note, status) VALUES (?,?,?,\'pending_admin\')');
        $stmt->execute([$eventId, $offerPercent, $organizerNote]);
    }

    public function getByEventId(int $eventId): ?array {
        $stmt = $this->db->prepare('SELECT * FROM event_negotiations WHERE event_id = ?');
        $stmt->execute([$eventId]);
        return $stmt->fetch() ?: null;
    }

    public function getByEventIds(array $eventIds): array {
        if (empty($eventIds)) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($eventIds), '?'));
        $stmt = $this->db->prepare("SELECT * FROM event_negotiations WHERE event_id IN ($placeholders)");
        $stmt->execute($eventIds);

        $map = [];
        foreach ($stmt->fetchAll() as $row) {
            $map[(int)$row['event_id']] = $row;
        }
        return $map;
    }

    public function setAgreedToOffer(int $eventId, ?string $adminNote = null): bool {
        $stmt = $this->db->prepare('UPDATE event_negotiations SET agreed_percent = organizer_offer_percent, admin_note = ?, status = \'agreed\' WHERE event_id = ?');
        return $stmt->execute([$adminNote, $eventId]);
    }

    public function counterOffer(int $eventId, float $counterPercent, ?string $adminNote = null): bool {
        $stmt = $this->db->prepare('UPDATE event_negotiations SET admin_counter_percent = ?, admin_note = ?, status = \'countered\' WHERE event_id = ?');
        return $stmt->execute([$counterPercent, $adminNote, $eventId]);
    }

    public function reject(int $eventId, ?string $adminNote = null): bool {
        $stmt = $this->db->prepare('UPDATE event_negotiations SET admin_note = ?, status = \'rejected\' WHERE event_id = ?');
        return $stmt->execute([$adminNote, $eventId]);
    }

    public function organizerAcceptCounter(int $eventId): bool {
        $stmt = $this->db->prepare('UPDATE event_negotiations SET agreed_percent = admin_counter_percent, status = \'agreed\' WHERE event_id = ? AND status = \'countered\'');
        return $stmt->execute([$eventId]);
    }

    public function organizerRejectCounter(int $eventId): bool {
        $stmt = $this->db->prepare('UPDATE event_negotiations SET status = \'rejected\' WHERE event_id = ? AND status = \'countered\'');
        return $stmt->execute([$eventId]);
    }

    public function isAgreed(int $eventId): bool {
        $stmt = $this->db->prepare('SELECT status FROM event_negotiations WHERE event_id = ?');
        $stmt->execute([$eventId]);
        $status = $stmt->fetchColumn();
        return $status === 'agreed';
    }

    public function getAllWithEvents(): array {
        $sql = "SELECT n.*, e.title AS event_title, e.status AS event_status,
                       u.name AS organizer_name, u.email AS organizer_email,
                       (SELECT MIN(price) FROM tickets t WHERE t.event_id = e.id) AS ticket_price,
                       e.capacity AS max_capacity
                FROM event_negotiations n
                JOIN events e ON n.event_id = e.id
                JOIN users u ON e.organizer_id = u.id
                ORDER BY FIELD(n.status,'pending_admin','countered','agreed','rejected'), n.updated_at DESC";
        return $this->db->query($sql)->fetchAll();
    }

    public function getByOrganizer(int $organizerId): array {
        $sql = "SELECT n.*, e.title AS event_title, e.status AS event_status,
                       (SELECT MIN(price) FROM tickets t WHERE t.event_id = e.id) AS ticket_price,
                       e.capacity AS max_capacity
                FROM event_negotiations n
                JOIN events e ON n.event_id = e.id
                WHERE e.organizer_id = ?
                ORDER BY FIELD(n.status,'countered','pending_admin','agreed','rejected'), n.updated_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$organizerId]);
        return $stmt->fetchAll();
    }
}