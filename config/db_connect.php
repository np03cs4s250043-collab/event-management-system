<?php
/**
 * Eventify — Database Connection & App Configuration
 */

// Application
define('APP_NAME', 'Eventify');
define('APP_URL', 'http://localhost/EMS_personal');
define('APP_ENV', 'development');
define('BASE_PATH', dirname(__DIR__));

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'eventify');
define('DB_USER', 'root');
define('DB_PASS', '');

// Session
define('SESSION_LIFETIME', 7200);

// Upload
define('UPLOAD_MAX_SIZE', 2 * 1024 * 1024);
define('UPLOAD_DIR', BASE_PATH . '/public/uploads/');
define('UPLOAD_URL', APP_URL . '/public/uploads/');

// Pagination
define('EVENTS_PER_PAGE', 6);

// Error reporting
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

/**
 * PDO Database Connection Singleton
 */
function getDB(): PDO {
    static $pdo = null;
    if ($pdo === null) {
        try {
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            if (APP_ENV === 'development') {
                die('Database connection failed: ' . $e->getMessage());
            }
            die('Database connection failed. Please try again later.');
        }
    }
    return $pdo;
}
