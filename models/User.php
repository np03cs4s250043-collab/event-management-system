<?php
require_once __DIR__ . '/../config/db_connect.php';

class User {
    private PDO $db;

    public function __construct() { $this->db = getDB(); }

    public function register(string $name, string $email, string $phone, string $password, string $role): bool {
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $stmt = $this->db->prepare('INSERT INTO users (name, email, phone, password_hash, role) VALUES (?, ?, ?, ?, ?)');
        return $stmt->execute([$name, $email, $phone, $hash, $role]);
    }

    public function login(string $email, string $password): ?array {
        $stmt = $this->db->prepare('SELECT *, id AS user_id, name AS full_name FROM users WHERE email = ? AND is_active = 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        return null;
    }

    public function emailExists(string $email): bool {
        $stmt = $this->db->prepare('SELECT 1 FROM users WHERE email = ?');
        $stmt->execute([$email]);
        return (bool) $stmt->fetch();
    }

    public function findById(int $id): ?array {
        $stmt = $this->db->prepare('SELECT *, id AS user_id, name AS full_name FROM users WHERE id = ?');
        $stmt->execute([$id]);
        return $stmt->fetch() ?: null;
    }

    public function getAll(string $search = '', string $role = '', int $limit = 20, int $offset = 0): array {
        $sql = 'SELECT *, id AS user_id, name AS full_name FROM users WHERE 1=1';
        $params = [];
        if ($search) { $sql .= ' AND (name LIKE ? OR email LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; }
        if ($role)   { $sql .= ' AND role = ?'; $params[] = $role; }
        $sql .= ' ORDER BY created_at DESC LIMIT ? OFFSET ?';
        $params[] = $limit; $params[] = $offset;
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function countAll(string $search = '', string $role = ''): int {
        $sql = 'SELECT COUNT(*) FROM users WHERE 1=1';
        $params = [];
        if ($search) { $sql .= ' AND (name LIKE ? OR email LIKE ?)'; $params[] = "%$search%"; $params[] = "%$search%"; }
        if ($role)   { $sql .= ' AND role = ?'; $params[] = $role; }
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int) $stmt->fetchColumn();
    }

    public function toggleStatus(int $id): bool {
        $stmt = $this->db->prepare('UPDATE users SET is_active = IF(is_active=1,0,1) WHERE id = ?');
        return $stmt->execute([$id]);
    }

    public function totalUsers(): int {
        return (int) $this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }
}