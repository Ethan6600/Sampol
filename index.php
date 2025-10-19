<?php
/**
 * Dispatch Management System for CDRRMO Maasin City
 * Main entry point that handles routing
 */

// Define a constant to prevent direct access to included files.
// This should be the very first thing in your index.php
define('INDEX_ENTRY_POINT', true);

// Start the session (important for user login/state)
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include your general utility functions file.
// This file will now include 'Database.php' which sets up the database connection.
require_once 'functions.php';

// Simple routing based on 'page' parameter
$page = $_GET['page'] ?? 'dashboard';

// Removed the redundant 'if ($page === 'logout')' block here.
// Logout logic is now fully handled by the 'switch' statement below.

// Check if user is logged in (except for login page)
// If not logged in, redirect to the login page
if ($page !== 'login' && !isset($_SESSION['user_id'])) {
    header('Location: ?page=login');
    exit;
}

// Route to appropriate controller/view
switch ($page) {
    case 'login':
        // No security check here as this is the entry point for non-logged-in users
        include 'login.php';
        break;
    case 'logout': // This case will now correctly handle the logout request
        require_once 'logout.php';
        break;
    case 'dashboard':
        // Ensure that DashboardController.php has the INDEX_ENTRY_POINT check
        require_once 'DashboardController.php';
        break;
    case 'dispatch':
        // Ensure that dispatch_logs.php has the INDEX_ENTRY_POINT check
        require_once 'dispatch_logs.php';
        break;
    case 'vehicles':
        // Ensure that manage_vehicles.php has the INDEX_ENTRY_POINT check
        require_once 'manage_vehicles.php';
        break;
    case 'incidents':
        // Ensure that log_incident.php has the INDEX_ENTRY_POINT check
        require_once 'log_incident.php';
        break;
    case 'edit_users':
        require_once 'user controls.php';
        break;
    case 'reports':
        // Ensure that generate_reports.php has the INDEX_ENTRY_POINT check
        require_once 'generate_reports.php';
        break;
    case 'admin_login_history':
        // Admin-only page for complete login history
        require_once 'AdminLoginHistoryController.php';
        break;
    default:
        // For any unknown page, redirect to dashboard or show a 404
        // Ensure that views/dashboard.php has the INDEX_ENTRY_POINT check
        // If it's a view, it should typically just include the view content.
        // If it's a controller, it should handle logic before including the view.
        if (file_exists('views/' . $page . '.php')) {
            include 'views/' . $page . '.php'; // Assuming views are simple
        } else {
            // Fallback to dashboard or a custom 404 page
            header('Location: ?page=dashboard');
            exit;
        }
        break;
}
?>