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

function h(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function formatPrice(float|int|string $amount): string
{
    return 'Rs ' . number_format((float) $amount, 2);
}

function csrfField(): string
{
    return '<input type="hidden" name="csrf_token" value="' . getCsrfTokenField() . '">';
}

function redirect(string $url): never
{
    header('Location: ' . $url);
    exit;
}

function validateCSRF(): bool
{
    if (($_SERVER['REQUEST_METHOD'] ?? 'GET') !== 'POST') {
        return true;
    }

    $sessionToken = $_SESSION[CSRF_TOKEN_KEY] ?? '';
    $inputToken = $_POST['csrf_token'] ?? $_POST['_csrf_token'] ?? '';

    return !empty($sessionToken) && !empty($inputToken) && hash_equals($sessionToken, $inputToken);
}