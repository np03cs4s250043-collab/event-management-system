<?php
// core/session_helper.php
if (session_status() === PHP_SESSION_NONE) { session_start(); }

function isLoggedIn(): bool        { return !empty($_SESSION['user_id']); }
function currentUser(): ?array
{
    if (!empty($_SESSION['user']) && is_array($_SESSION['user'])) {
        return $_SESSION['user'];
    }

    if (!empty($_SESSION['user_id'])) {
        return [
            'user_id' => (int) $_SESSION['user_id'],
            'role' => (string) ($_SESSION['role'] ?? 'guest'),
            'full_name' => (string) ($_SESSION['full_name'] ?? ''),
            'email' => (string) ($_SESSION['email'] ?? ''),
        ];
    }

    return null;
}

function currentRole(): string
{
    if (!empty($_SESSION['role'])) {
        return (string) $_SESSION['role'];
    }

    return (string) ($_SESSION['user']['role'] ?? 'guest');
}
function currentUserId(): ?int     { return isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null; }

//forces user to login if not already logged in
function requireLogin(): void
{
    if (!isLoggedIn()) {
        header('Location: ' . BASE_PATH . '/index.php?page=login');
        exit;
    }
}

//allows access only if ther user has required role
function requireRole(string ...$roles): void
{
    requireLogin();
    if (!in_array(currentRole(), $roles, true)) {
        http_response_code(403);
        die('Access denied.');
    }
}


function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}


function getFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}