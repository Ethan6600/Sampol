<?php
/**
 * functions.php
 * Contains utility functions
 */

// Include configuration constants from config.php
require_once 'config.php';

// Include the Database class. This is where get_db() is now exclusively defined.
// The get_db() function itself will become available globally once Database.php is loaded.
require_once 'Database.php';

// Note: The get_db() function is now defined ONLY within Database.php.
// You can call it directly from this file or any other file that includes functions.php.

// Function to check if user is logged in (used in login.php)
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// You can add other utility functions here as needed
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

// Function to validate email format
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Function to generate a secure random token
function generate_token($length = 32) {
    return bin2hex(random_bytes($length));
}

// Function to check session timeout (optional)
function check_session_timeout($timeout = 3600) { // 1 hour default
    if (isset($_SESSION['last_activity'])) {
        if (time() - $_SESSION['last_activity'] > $timeout) {
            session_unset();
            session_destroy();
            return false;
        }
    }
    $_SESSION['last_activity'] = time();
    return true;
}

// CSRF utilities
function get_csrf_token() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verify_csrf_token($token) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    if (!isset($_SESSION['csrf_token']) || !is_string($token) || $token === '') {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token);
}

// Function to redirect if not logged in
function require_login() {
    if (!is_logged_in()) {
        header('Location: ?page=login');
        exit;
    }
}

// Enforce that the current user has a specific minimum role
function require_role($required_role) {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $role_hierarchy = ['user' => 1, 'dispatcher' => 2, 'admin' => 3];
    $current_role = $_SESSION['role'] ?? '';
    $current_level = $role_hierarchy[$current_role] ?? 0;
    $required_level = $role_hierarchy[$required_role] ?? 99;
    if ($current_level < $required_level) {
        header('Location: ?page=dashboard');
        exit;
    }
}

// Additional utility functions for dispatch management system

// Function to check user role
function has_role($required_role) {
    if (!is_logged_in()) {
        return false;
    }
    
    $user_role = $_SESSION['role'] ?? '';
    // Define a simple role hierarchy for comparison
    $role_hierarchy = ['user' => 1, 'dispatcher' => 2, 'admin' => 3];
    
    // Check if the user's role has sufficient privileges
    return ($role_hierarchy[$user_role] ?? 0) >= ($role_hierarchy[$required_role] ?? 99);
}

/**
 * Formats a timestamp or date string into a more readable format.
 * This function was previously named `format_date` and has been renamed to `formatDate` for consistency.
 *
 * @param string|null $datetime The datetime string or timestamp to format.
 * @param string $format The desired date/time format (default: 'M d, Y H:i A').
 * @return string The formatted date/time string, or 'N/A' if input is null/empty or invalid.
 */
function formatDate(?string $datetime, string $format = 'M d, Y H:i A'): string {
    if (empty($datetime) || $datetime === '0000-00-00 00:00:00') {
        return 'N/A';
    }
    try {
        $date = new DateTime($datetime);
        return $date->format($format);
    } catch (Exception $e) {
        // Log the error for debugging, but don't stop the script
        error_log("Error formatting date '{$datetime}': " . $e->getMessage());
        return 'Invalid Date'; // Return a friendly message if parsing fails
    }
    if (empty($date) || $date === '0000-00-00 00:00:00') {
        return '';
    }

}

/**
 * Formats a numeric value as currency (Philippine Peso).
 *
 * @param float|int|string $amount The amount to format.
 * @return string The formatted currency string.
 */
function formatCurrency($amount): string {
    return '₱' . number_format((float)$amount, 2);
}

// Function to generate alert/notification messages
function set_flash_message($message, $type = 'info') {
    $_SESSION['flash_message'] = $message;
    $_SESSION['flash_type'] = $type; // success, error, warning, info
}

// Function to display and clear flash messages
function get_flash_message() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'info';
        unset($_SESSION['flash_message'], $_SESSION['flash_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

// Function to log user activities
function log_user_activity($action, $details = '') {
    if (is_logged_in()) {
        try {
            // Call the get_db() function from Database.php (it's globally available now)
            $db = get_db(); 
            $db->execute(
                "INSERT INTO user_logs (user_id, action, details, ip_address, created_at) VALUES (?, ?, ?, ?, NOW())",
                [$_SESSION['user_id'], $action, $details, $_SERVER['REMOTE_ADDR'] ?? 'unknown']
            );
        } catch (Exception $e) {
            error_log("Failed to log user activity: " . $e->getMessage());
        }
    }
}

// Function to validate phone number (Philippine format)
function is_valid_phone($phone) {
    // Remove all non-digit characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Check if it's a valid Philippine mobile number (e.g., 09xxxxxxxxx or 639xxxxxxxxx)
    return preg_match('/^(09\d{9}|639\d{9})$/', $phone);
}

// Function to format phone number for display
function format_phone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    
    // Example: Formats 09171234567 to 0917-123-4567
    if (strlen($phone) == 11 && substr($phone, 0, 2) == '09') {
        return substr($phone, 0, 4) . '-' . substr($phone, 4, 3) . '-' . substr($phone, 7);
    }
    // Example: Formats 639171234567 to +63 917-123-4567
    if (strlen($phone) == 12 && substr($phone, 0, 3) == '639') {
        return '+63 ' . substr($phone, 2, 3) . '-' . substr($phone, 5, 3) . '-' . substr($phone, 8);
    }
    
    return $phone; // Return as-is if format doesn't match expected patterns
}
