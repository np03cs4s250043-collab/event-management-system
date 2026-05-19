<?php
<<<<<<< HEAD
/**
 * Eventify Helper Functions
 */

function h(string $str): string {
    return htmlspecialchars($str, ENT_QUOTES, 'UTF-8');
}

function redirect(string $url): void {
=======

if (!defined('BASE_PATH')) {
    define('BASE_PATH', '/event-management-system');
}

if (!defined('APP_URL')) {
    define('APP_URL', BASE_PATH);
}

if (!defined('UPLOAD_URL')) {
    define('UPLOAD_URL', APP_URL . '/public/uploads/');
}

if (!defined('EVENTS_PER_PAGE')) {
    define('EVENTS_PER_PAGE', 12);
}

function paginate(int $total, int $perPage, int $page): array
{
    $perPage = max(1, $perPage);
    $totalPages = max(1, (int) ceil($total / $perPage));
    $current = min(max(1, $page), $totalPages);
    return [
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $current,
        'total_pages' => $totalPages,
        'offset' => ($current - 1) * $perPage,
    ];
}

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function formatPrice(float|int|string $amount): string
{
    return 'Rs ' . number_format((float) $amount, 2);
}

function redirect(string $url): never
{
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
    header('Location: ' . $url);
    exit;
}

<<<<<<< HEAD
function setFlash(string $type, string $message): void {
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function getFlash(): ?array {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

function isLoggedIn(): bool {
    return isset($_SESSION['user_id']);
}

function currentRole(): ?string {
    return $_SESSION['role'] ?? null;
}

function currentUserId(): ?int {
    return $_SESSION['user_id'] ?? null;
}

function requireLogin(): void {
    if (!isLoggedIn()) {
        setFlash('error', 'Please login to continue.');
        redirect(APP_URL . '/index.php?page=login');
    }
}

function requireRole(string ...$roles): void {
    requireLogin();
    if (!in_array(currentRole(), $roles)) {
        setFlash('error', 'Access denied.');
        redirect(APP_URL . '/index.php');
    }
}

function generateBookingRef(): string {
    return 'EVT-' . strtoupper(bin2hex(random_bytes(3)));
}

function paginate(int $total, int $perPage, int $currentPage): array {
    $totalPages = max(1, (int) ceil($total / $perPage));
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $perPage;
    return [
        'total' => $total,
        'per_page' => $perPage,
        'current_page' => $currentPage,
        'total_pages' => $totalPages,
        'offset' => $offset,
    ];
}

function uploadImage(array $file, string &$error = ''): ?string {
    if ($file['error'] !== UPLOAD_ERR_OK) {
        $error = 'File upload failed. Please try again.';
        return null;
    }
    if ($file['size'] > UPLOAD_MAX_SIZE) {
        $error = 'Image too large. Maximum size is 2MB.';
        return null;
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp'];
    if (!isset($allowed[$mime])) {
        $error = 'Invalid image format. Allowed: JPG, PNG, WebP.';
        return null;
    }

    if (!is_dir(UPLOAD_DIR) && !mkdir(UPLOAD_DIR, 0775, true)) {
        $error = 'Upload directory could not be created. Contact admin.';
        return null;
    }
    if (!is_writable(UPLOAD_DIR)) {
        $error = 'Upload directory is not writable. Contact admin.';
        return null;
    }

    $filename = bin2hex(random_bytes(16)) . '.' . $allowed[$mime];
    $dest = UPLOAD_DIR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        $error = 'Failed to save image. Please try again.';
        return null;
    }
    return $filename;
}

function formatPrice(float $amount): string {
    return 'Rs. ' . number_format($amount, 2);
}

function categoryColor(string $cat): string {
    return match ($cat) {
        'Concert'     => 'bg-purple-500',
        'Music Event' => 'bg-blue-500',
        'Football'    => 'bg-green-500',
        'Cricket'     => 'bg-orange-500',
        default       => 'bg-gray-500',
    };
}
=======
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
