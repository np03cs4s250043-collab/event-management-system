<?php

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
    header('Location: ' . $url);
    exit;
}

