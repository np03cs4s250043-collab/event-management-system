<?php
/**
 * Eventify — Database Connection & App Configuration
 */

<<<<<<< HEAD
// Application
=======
// Application settings
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
define('APP_NAME', 'Eventify');
define('APP_URL', 'http://localhost/EMS_personal');
define('APP_ENV', 'development');
define('BASE_PATH', dirname(__DIR__));

<<<<<<< HEAD
// Database
=======
// Database configuration
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
define('DB_HOST', 'localhost');
define('DB_NAME', 'eventify');
define('DB_USER', 'root');
define('DB_PASS', '');

<<<<<<< HEAD
// Gemini AI (chatbot) — get a free key at https://aistudio.google.com/apikey
// Set as env var GEMINI_API_KEY, or replace the empty string below for local dev.
define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: 'AIzaSyCzKBWTISycD7jphU5JZHQHnojEgeb4ZwI');
define('GEMINI_MODEL', 'gemini-2.5-flash-lite');

// Session
define('SESSION_LIFETIME', 7200);

// Upload
=======
// Gemini AI API configuration
define('GEMINI_API_KEY', getenv('GEMINI_API_KEY') ?: 'AIzaSyCzKBWTISycD7jphU5JZHQHnojEgeb4ZwI');
define('GEMINI_MODEL', 'gemini-2.5-flash-lite');

// Session timeout (in seconds)
define('SESSION_LIFETIME', 7200);

// File upload settings
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
define('UPLOAD_MAX_SIZE', 2 * 1024 * 1024);
define('UPLOAD_DIR', BASE_PATH . '/public/uploads/');
define('UPLOAD_URL', APP_URL . '/public/uploads/');

<<<<<<< HEAD
// Pagination
define('EVENTS_PER_PAGE', 6);

// Error reporting
=======
// Events shown per page
define('EVENTS_PER_PAGE', 6);

// Error reporting settings
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
if (APP_ENV === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

/**
<<<<<<< HEAD
 * PDO Database Connection Singleton
 */
function getDB(): PDO {
=======
 * Create and return database connection
 */
function getDB(): PDO {
    // Store single database instance
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
    static $pdo = null;

    // Connect only if not connected before
    if ($pdo === null) {
        try {
<<<<<<< HEAD
            $dsn = 'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4';
            $pdo = new PDO($dsn, DB_USER, DB_PASS, [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]);
        } catch (PDOException $e) {
=======

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
>>>>>>> ab276b0e5f1949ae1291e04308f8288d48605168
            if (APP_ENV === 'development') {
                die('Database connection failed: ' . $e->getMessage());
            }
            die('Database connection failed. Please try again later.');
        }
    }
    // Return database connection
    return $pdo;
}
