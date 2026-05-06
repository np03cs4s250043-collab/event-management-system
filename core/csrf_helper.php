<?php
/**
 * CSRF Protection Helpers
 */

function generateCSRF(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function validateCSRF(): bool {
    if (!isset($_POST['csrf_token'], $_SESSION['csrf_token'])) return false;
    $valid = hash_equals($_SESSION['csrf_token'], $_POST['csrf_token']);
    unset($_SESSION['csrf_token']);
    return $valid;
}

function csrfField(): string {
    return '<input type="hidden" name="csrf_token" value="' . h(generateCSRF()) . '">';
}
