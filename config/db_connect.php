<?php
/**
 * Eventify — Database Connection & App Configuration
 */

// Application settings
define('APP_NAME', 'Eventify');
define('APP_URL', 'http://localhost/EMS_personal');
define('APP_ENV', 'development');
define('BASE_PATH', dirname(__DIR__));

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'eventify');
define('DB_USER', 'root');
define('DB_PASS', '');

// Gemini AI API configuration
define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: 'AIzaSyCzKBWTISycD7jphU5JZHQHnojEgeb4ZwI');
define('GEMINI_MODEL', 'gemini-2.5-flash-lite');

// Session timeout (in seconds)
define('SESSION_LIFETIME', 7200);

// File upload settings
define('UPLOAD_MAX_SIZE', 2 * 1024 * 1024);
define('UPLOAD_DIR', BASE_PATH . '/public/uploads/');
define('UPLOAD_URL', APP_URL . '/public/uploads/');

// Events shown per page
define('EVENTS_PER_PAGE', 6);

// Error reporting settings
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

/**
 * Create and return database connection
 */
function getDB(): PDO {
    // Store single database instance
    static $pdo = null;

    // Connect only if not connected before
    if ($pdo === null) {
        try {

         // Database connection string
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';

            // Create PDO connection
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                // Show database errors
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                // Fetch results as associative array
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                // Disable emulated prepared statements
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
            // Show detailed error in development mode
            if (APP_ENV === 'development') {
                die('Database connection failed: ' . $e->getMessage());
            }
            die('Database connection failed. Please try again later.');
        }
    }
    // Return database connection
    return $pdo;
}
