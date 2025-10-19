<?php
/**
 * config.php
 * Database configuration constants
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'cddrrmo_dispatch_system'); // Fixed: Use the actual database name with underscores
define('DB_USER', 'root');
define('DB_PASS', ''); // Empty for default XAMPP

// Optional: Application settings
define('APP_NAME', 'CDRRMO Dispatch Management System');
define('APP_VERSION', '1.0');
define('TIMEZONE', 'Asia/Manila');
// Environment: 'development' or 'production'
if (!defined('APP_ENV')) {
    define('APP_ENV', getenv('APP_ENV') ?: 'development');
}

// Set timezone
date_default_timezone_set(TIMEZONE);

// Centralized error display configuration based on environment
if (APP_ENV === 'production') {
    ini_set('display_errors', 0);
    ini_set('display_startup_errors', 0);
    error_reporting(E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED);
} else {
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);
}
?>