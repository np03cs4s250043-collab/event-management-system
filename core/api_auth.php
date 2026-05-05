<?php
/**
 * API Token Authentication Middleware
 * Uses the `api_tokens` table for stateless token-based auth.
 *
 * Token is sent via: Authorization: Bearer <token>
 */
require_once __DIR__ . '/../config/db_connect.php';
require_once __DIR__ . '/api_helpers.php';

/**
 * Generate a new API token for a user and store its hash.
 */
function generateApiToken(int $userId, int $expiresInHours = 72): string {
    $token = bin2hex(random_bytes(32));
    $hash  = hash('sha256', $token);
    $expiresAt = date('Y-m-d H:i:s', time() + ($expiresInHours * 3600));

    $db = getDB();
    $stmt = $db->prepare('INSERT INTO api_tokens (user_id, token_hash, expires_at) VALUES (?, ?, ?)');
    $stmt->execute([$userId, $hash, $expiresAt]);

    return $token;
}

/**
 * Revoke a specific token.
 */
function revokeApiToken(string $token): bool {
    $hash = hash('sha256', $token);
    $db   = getDB();
    $stmt = $db->prepare('DELETE FROM api_tokens WHERE token_hash = ?');
    return $stmt->execute([$hash]);
}

/**
 * Revoke all tokens for a user.
 */
function revokeAllUserTokens(int $userId): bool {
    $db   = getDB();
    $stmt = $db->prepare('DELETE FROM api_tokens WHERE user_id = ?');
    return $stmt->execute([$userId]);
}

/**
 * Extract the Bearer token from the Authorization header.
 */
function getBearerToken(): ?string {
    $header = $_SERVER['HTTP_AUTHORIZATION'] ?? $_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '';
    if (preg_match('/^Bearer\s+(.+)$/i', $header, $matches)) {
        return $matches[1];
    }
    return null;
}

/**
 * Validate a token and return the associated user row (without password_hash).
 * Returns null if invalid or expired.
 */
function validateApiToken(string $token): ?array {
    $hash = hash('sha256', $token);
    $db   = getDB();
    $stmt = $db->prepare(
        'SELECT u.*, u.id AS user_id, u.name AS full_name
         FROM api_tokens t
         JOIN users u ON t.user_id = u.id
         WHERE t.token_hash = ? AND t.expires_at > NOW() AND u.is_active = 1'
    );
    $stmt->execute([$hash]);
    $user = $stmt->fetch();
    if ($user) {
        unset($user['password_hash']);
        return $user;
    }
    return null;
}

/**
 * Require a valid API token. Halts with 401 if missing/invalid.
 * Returns the authenticated user array.
 */
function requireApiAuth(): array {
    $token = getBearerToken();
    if (!$token) {
        jsonResponse(401, ['error' => 'Authentication required. Provide a Bearer token.']);
    }
    $user = validateApiToken($token);
    if (!$user) {
        jsonResponse(401, ['error' => 'Invalid or expired token.']);
    }
    return $user;
}

/**
 * Require a specific role. Halts with 403 if role doesn't match.
 */
function requireApiRole(array $user, string ...$roles): void {
    if (!in_array($user['role'], $roles)) {
        jsonResponse(403, ['error' => 'Access denied. Required role: ' . implode(' or ', $roles)]);
    }
}

/**
 * Send a JSON response and exit.
 */
function jsonResponse(int $statusCode, array $data): void {
    http_response_code($statusCode);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Read JSON body from the request.
 */
function jsonInput(): array {
    $input = json_decode(file_get_contents('php://input'), true);
    return is_array($input) ? $input : [];
}

/**
 * Get the HTTP method, respecting _method override for form submissions.
 */
function getRequestMethod(): string {
    $method = strtoupper($_SERVER['REQUEST_METHOD']);
    if ($method === 'POST' && isset($_POST['_method'])) {
        $override = strtoupper($_POST['_method']);
        if (in_array($override, ['PUT', 'PATCH', 'DELETE'])) {
            return $override;
        }
    }
    return $method;
}

/**
 * Clean up expired tokens (can be called periodically).
 */
function purgeExpiredTokens(): int {
    $db   = getDB();
    $stmt = $db->prepare('DELETE FROM api_tokens WHERE expires_at < NOW()');
    $stmt->execute();
    return $stmt->rowCount();
}