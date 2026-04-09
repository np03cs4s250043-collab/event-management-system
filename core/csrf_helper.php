<?php
// core/csrf_helper.php
//Implements CSRF protection by generating, storing, validating, and refreshing secure tokens for form requests to prevent unauthorized submissions
if (session_status() === PHP_SESSION_NONE) { session_start(); }

const CSRF_TOKEN_KEY    = '_csrf_token';
const CSRF_TOKEN_LENGTH = 32;

function generateCsrfToken(): string
{
    $token = bin2hex(random_bytes(CSRF_TOKEN_LENGTH));
    $_SESSION[CSRF_TOKEN_KEY] = $token;
    return $token;
}

function getCsrfToken(): string
{
    if (empty($_SESSION[CSRF_TOKEN_KEY])) return generateCsrfToken();
    return $_SESSION[CSRF_TOKEN_KEY];
}

function getCsrfTokenField(): string
{
    return htmlspecialchars(getCsrfToken(), ENT_QUOTES, 'UTF-8');
}


function generate_csrf_token(): string
{
    return getCsrfTokenField();
}

function validateCsrfToken(string $submitted): void
{
    $session = $_SESSION[CSRF_TOKEN_KEY] ?? '';
    $valid   = !empty($session) && !empty($submitted) && hash_equals($session, $submitted);
    if (!$valid) {
        http_response_code(403);
        error_log(sprintf('[EMS CSRF FAIL] IP:%s URI:%s', $_SERVER['REMOTE_ADDR'] ?? '-', $_SERVER['REQUEST_URI'] ?? '-'));
        unset($_SESSION[CSRF_TOKEN_KEY]);
        die('Invalid security token. Please go back and try again.');
    }
    generateCsrfToken();
}

function isPost(): bool  { return $_SERVER['REQUEST_METHOD'] === 'POST'; }
function isAjax(): bool  { return strtolower($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'xmlhttprequest'; }
