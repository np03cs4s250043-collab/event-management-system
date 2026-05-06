<?php

function isLoggedIn(): bool {
    return !empty($_SESSION['user_id']);
}

function currentUserId(): int {
    return (int)($_SESSION['user_id'] ?? 0);
}

function currentRole(): string {
    return $_SESSION['role'] ?? 'guest';
}

function requireRole(string $role): void {
    if (!isLoggedIn() || currentRole() !== $role) {
        setFlash('error', 'Please log in to access that page.');
        redirect(APP_URL . '/index.php?page=login');
    }
}

function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (!isset($_SESSION['flash'])) return null;
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}

function uploadImage(array $file, string &$error): string|false {
    $allowed = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    $maxSize = 5 * 1024 * 1024;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'File upload failed.';
        return false;
    }
    if (!in_array($file['type'], $allowed, true)) {
        $error = 'Only JPG, PNG, GIF, and WebP images are allowed.';
        return false;
    }
    if ($file['size'] > $maxSize) {
        $error = 'Image must be under 5 MB.';
        return false;
    }

    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = bin2hex(random_bytes(16)) . '.' . $ext;
    $uploadDir = __DIR__ . '/../public/uploads/';

    if (!move_uploaded_file($file['tmp_name'], $uploadDir . $filename)) {
        $error = 'Could not save the uploaded file.';
        return false;
    }

    return $filename;
}