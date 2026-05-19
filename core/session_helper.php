<?php
/**
 * Session & Authentication Guard
 */
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly'  => true,
        'samesite'  => 'Lax',
    ]);
    session_start();
}

// Session expiry check — honor extended lifetime when "Keep me signed in" was chosen
$maxIdle = $_SESSION['remember_lifetime'] ?? SESSION_LIFETIME;
if (isset($_SESSION['last_activity']) && (time() - $_SESSION['last_activity']) > $maxIdle) {
    session_unset();
    session_destroy();
    session_start();
}
$_SESSION['last_activity'] = time();

// Refresh the persistent cookie on each request so it keeps sliding forward
if (!empty($_SESSION['remember_lifetime'])) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        session_id(),
        time() + $_SESSION['remember_lifetime'],
        $params['path'],
        $params['domain'],
        $params['secure'],
        $params['httponly']
    );
}
