<?php
// models/User.php
// User model for handling database operations related to users
require_once __DIR__ . '/../config/db_connect.php';

class User
{
    private PDO $db;

    // Connect to database when object is created
    public function __construct() { $this->db = getDB(); }

    // Find user by email
    public function findByEmail(string $email): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
        $stmt->execute([':email' => $email]);
        return $stmt->fetch() ?: null;
    }

    // Find user by ID
    public function findById(int $id): ?array
    {
        $stmt = $this->db->prepare('SELECT * FROM users WHERE id = :id LIMIT 1');
        $stmt->execute([':id' => $id]);
        return $stmt->fetch() ?: null;
    }

    // Create a new user
    public function create(array $data): int
    {
        $stmt = $this->db->prepare('
            INSERT INTO users (name, email, phone, password_hash, role)
            VALUES (:name, :email, :phone, :password_hash, :role)
        ');
        $stmt->execute([
            ':name'          => $data['name'],
            ':email'         => $data['email'],
            ':phone'         => $data['phone'] ?? null,
            ':password_hash' => password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => 12]),
            ':role'          => $data['role'] ?? 'attendee',
        ]);
        return (int)$this->db->lastInsertId();
    }

    // Update user name and phone
    public function update(int $id, array $data): bool
    {
        $stmt = $this->db->prepare('
            UPDATE users SET name = :name, phone = :phone WHERE id = :id
        ');
        return $stmt->execute([':name' => $data['name'], ':phone' => $data['phone'], ':id' => $id]);
    }

    public function getAll(int $limit = 50, int $offset = 0): array
    {
        $stmt = $this->db->prepare('
            SELECT id, name, email, phone, role, is_active, created_at
            FROM users ORDER BY created_at DESC LIMIT :limit OFFSET :offset
        ');
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Count total users
    public function countAll(): int
    {
        return (int)$this->db->query('SELECT COUNT(*) FROM users')->fetchColumn();
    }

    public function toggleActive(int $id): bool
    {
        $stmt = $this->db->prepare('UPDATE users SET is_active = NOT is_active WHERE id = :id');
        return $stmt->execute([':id' => $id]);
    }
}
