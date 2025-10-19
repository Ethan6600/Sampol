<?php
/**
 * Dashboard Controller
 * Handles the logic for the Dashboard page, fetching real-time statistics from the database.
 *
 * IT Capstone Project - Dispatch Management System
 */

// Security check: Prevent direct access to this file
if (!defined('INDEX_ENTRY_POINT')) {
    header('Location: index.php?page=login');
    exit;
}

// Ensure session is started to access $_SESSION variables
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: ?page=login');
    exit;
}

require_once 'functions.php';

$db = get_db(); 
$pdo = $db->getConnection();

// Function to fetch dashboard statistics
function getDashboardStats($pdo) {
    $stats = [
        'active_dispatches' => 0,
        'pending_incidents' => 0,
        'available_vehicles' => 0,
        'on_duty_responders' => 0,
        'total_incidents_today' => 0,
        'total_dispatches_today' => 0
    ];

    try {
        // Count Active Dispatches
        $stmt = $pdo->query("SELECT COUNT(*) FROM dispatches WHERE status IN ('assigned', 'en_route', 'on_scene', 'returning')");
        $stats['active_dispatches'] = $stmt->fetchColumn();

        // Count Pending Incidents
        $stmt = $pdo->query("SELECT COUNT(*) FROM incidents WHERE status = 'pending'");
        $stats['pending_incidents'] = $stmt->fetchColumn();

        // Count Available Vehicles
        $stmt = $pdo->query("SELECT COUNT(*) FROM vehicles WHERE status = 'available'");
        $stats['available_vehicles'] = $stmt->fetchColumn();

        // Count On Duty Responders
        $stmt = $pdo->query("SELECT COUNT(*) FROM responders WHERE status = 'on_duty'");
        $stats['on_duty_responders'] = $stmt->fetchColumn();

        // Count Total Incidents Today
        $stmt = $pdo->query("SELECT COUNT(*) FROM incidents WHERE DATE(reported_at) = CURDATE()");
        $stats['total_incidents_today'] = $stmt->fetchColumn();

        // Count Total Dispatches Today
        $stmt = $pdo->query("SELECT COUNT(*) FROM dispatches WHERE DATE(dispatch_time) = CURDATE()");
        $stats['total_dispatches_today'] = $stmt->fetchColumn();

    } catch (PDOException $e) {
        error_log("Dashboard stats error: " . $e->getMessage());
    }
    return $stats;
}

// Check if this is an AJAX request for refreshing data
if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
    header('Content-Type: application/json');
    echo json_encode(getDashboardStats($pdo));
    exit;
}

// Fetch initial dashboard stats for the page load
$dashboardStats = getDashboardStats($pdo);

$pageTitle = "Dashboard";
include 'header.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tachometer-alt text-primary"></i> Dashboard Overview
        </h1>
        <span class="text-muted">
            <i class="far fa-clock"></i> <?= date('l, F j, Y - g:i A') ?>
        </span>
    </div>

    <!-- Statistics Cards Row -->
    <div class="row">
        <!-- Active Dispatches Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                Active Dispatches
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-active-dispatches">
                                <?= htmlspecialchars($dashboardStats['active_dispatches']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-truck fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Pending Incidents Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                Pending Incidents
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-pending-incidents">
                                <?= htmlspecialchars($dashboardStats['pending_incidents']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Vehicles Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                Available Vehicles
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-available-vehicles">
                                <?= htmlspecialchars($dashboardStats['available_vehicles']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-car fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- On-Duty Responders Card -->
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                On-Duty Responders
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800 stat-on-duty-responders">
                                <?= htmlspecialchars($dashboardStats['on_duty_responders']) ?>
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Content Row -->
    <div class="row">
        <!-- Recent Incidents -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-list"></i> Recent Incidents (Last 24 hours)
                    </h6>
                    <a href="?page=incidents" class="btn btn-sm btn-primary">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Incident #</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $stmt = $pdo->query("SELECT incident_id, incident_number, incident_type, location, status, reported_at FROM incidents WHERE reported_at >= NOW() - INTERVAL 1 DAY ORDER BY reported_at DESC LIMIT 5");
                                    $recent_incidents = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    if (count($recent_incidents) > 0) {
                                        foreach ($recent_incidents as $incident) {
                                            $status_class = [
                                                'pending' => 'secondary',
                                                'active' => 'danger',
                                                'resolved' => 'success',
                                                'cancelled' => 'dark'
                                            ];
                                            $badge_class = $status_class[$incident['status']] ?? 'info';
                                            
                                            echo '<tr>';
                                            echo '<td><strong>' . htmlspecialchars($incident['incident_number']) . '</strong></td>';
                                            echo '<td>' . htmlspecialchars(ucwords(str_replace('_', ' ', $incident['incident_type']))) . '</td>';
                                            echo '<td><small>' . htmlspecialchars(substr($incident['location'], 0, 30)) . (strlen($incident['location']) > 30 ? '...' : '') . '</small></td>';
                                            echo '<td><span class="badge badge-' . $badge_class . '">' . htmlspecialchars(ucwords($incident['status'])) . '</span></td>';
                                            echo '<td><small>' . htmlspecialchars(date('h:i A', strtotime($incident['reported_at']))) . '</small></td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="5" class="text-center text-muted py-3">No recent incidents.</td></tr>';
                                    }
                                } catch (PDOException $e) {
                                    error_log("Dashboard recent incidents error: " . $e->getMessage());
                                    echo '<tr><td colspan="5" class="text-center text-danger">Error loading recent incidents.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Dispatches -->
        <div class="col-xl-6 col-lg-6 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex flex-row align-items-center justify-content-between">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-truck-moving"></i> Recent Dispatches (Last 24 hours)
                    </h6>
                    <a href="?page=dispatch" class="btn btn-sm btn-info">View All</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Incident #</th>
                                    <th>Vehicle</th>
                                    <th>Responder</th>
                                    <th>Status</th>
                                    <th>Time</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $stmt = $pdo->query("
                                        SELECT 
                                            i.incident_number, 
                                            v.name AS vehicle_name, 
                                            r.full_name AS responder_name,
                                            d.status AS dispatch_status, 
                                            d.dispatch_time
                                        FROM dispatches d
                                        LEFT JOIN incidents i ON d.incident_id = i.incident_id
                                        LEFT JOIN vehicles v ON d.vehicle_id = v.vehicle_id
                                        LEFT JOIN responders r ON d.responder_id = r.responder_id
                                        WHERE d.dispatch_time >= NOW() - INTERVAL 1 DAY
                                        ORDER BY d.dispatch_time DESC
                                        LIMIT 5
                                    ");
                                    $recent_dispatches = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    if (count($recent_dispatches) > 0) {
                                        foreach ($recent_dispatches as $dispatch) {
                                            $status_class = [
                                                'assigned' => 'primary',
                                                'en_route' => 'warning',
                                                'on_scene' => 'danger',
                                                'returning' => 'info',
                                                'completed' => 'success'
                                            ];
                                            $badge_class = $status_class[$dispatch['dispatch_status']] ?? 'secondary';
                                            
                                            echo '<tr>';
                                            echo '<td><strong>' . htmlspecialchars($dispatch['incident_number'] ?? 'N/A') . '</strong></td>';
                                            echo '<td>' . htmlspecialchars($dispatch['vehicle_name'] ?? 'N/A') . '</td>';
                                            echo '<td><small>' . htmlspecialchars(substr($dispatch['responder_name'] ?? 'N/A', 0, 20)) . '</small></td>';
                                            echo '<td><span class="badge badge-' . $badge_class . '">' . htmlspecialchars(ucwords(str_replace('_', ' ', $dispatch['dispatch_status']))) . '</span></td>';
                                            echo '<td><small>' . htmlspecialchars(date('h:i A', strtotime($dispatch['dispatch_time']))) . '</small></td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="5" class="text-center text-muted py-3">No recent dispatches.</td></tr>';
                                    }
                                } catch (PDOException $e) {
                                    error_log("Dashboard recent dispatches error: " . $e->getMessage());
                                    echo '<tr><td colspan="5" class="text-center text-danger">Error loading recent dispatches.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
    <!-- Login History Section - ADMIN ONLY -->
    <div class="row">
        <div class="col-12 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-danger text-white d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-history"></i> Recent Login Activity
                    </h6>
                    <a href="?page=admin_login_history" class="btn btn-sm btn-light">
                        <i class="fas fa-external-link-alt"></i> View Full History
                    </a>
                </div>
                <div class="card-body">
                    <!-- Login Statistics -->
                    <div class="row mb-3">
                        <?php
                        try {
                            $stmt = $pdo->query("
                                SELECT 
                                    COUNT(*) as total_logins_today,
                                    SUM(CASE WHEN login_status = 'success' THEN 1 ELSE 0 END) as successful_today,
                                    SUM(CASE WHEN login_status = 'failed' THEN 1 ELSE 0 END) as failed_today
                                FROM login_history 
                                WHERE DATE(login_time) = CURDATE()
                            ");
                            $login_stats = $stmt->fetch(PDO::FETCH_ASSOC);
                            
                            $login_stats['total_logins_today'] = $login_stats['total_logins_today'] ?? 0;
                            $login_stats['successful_today'] = $login_stats['successful_today'] ?? 0;
                            $login_stats['failed_today'] = $login_stats['failed_today'] ?? 0;
                        } catch (PDOException $e) {
                            error_log("Login stats error: " . $e->getMessage());
                            $login_stats = ['total_logins_today' => 0, 'successful_today' => 0, 'failed_today' => 0];
                        }
                        ?>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-sign-in-alt fa-2x text-primary mb-2"></i>
                                <h4 class="mb-0"><?= number_format((int)$login_stats['total_logins_today']) ?></h4>
                                <small class="text-muted">Total Logins Today</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <h4 class="mb-0"><?= number_format((int)$login_stats['successful_today']) ?></h4>
                                <small class="text-muted">Successful</small>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="text-center">
                                <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                                <h4 class="mb-0"><?= number_format((int)$login_stats['failed_today']) ?></h4>
                                <small class="text-muted">Failed</small>
                            </div>
                        </div>
                    </div>

                    <hr>

                    <div class="table-responsive">
                        <table class="table table-hover table-sm">
                            <thead>
                                <tr>
                                    <th>Username</th>
                                    <th>Role</th>
                                    <th>Login Time</th>
                                    <th>IP Address</th>
                                    <th>Browser</th>
                                    <th>Device</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                try {
                                    $stmt = $pdo->query("
                                        SELECT * FROM login_history 
                                        ORDER BY login_time DESC 
                                        LIMIT 10
                                    ");
                                    $recent_logins = $stmt->fetchAll(PDO::FETCH_ASSOC);
                                    
                                    if (count($recent_logins) > 0) {
                                        foreach ($recent_logins as $login) {
                                            $role_badges = [
                                                'admin' => 'danger',
                                                'dispatcher' => 'warning',
                                                'responder' => 'info'
                                            ];
                                            $badge_color = $role_badges[$login['role']] ?? 'secondary';
                                            
                                            $device_icons = [
                                                'Mobile' => 'fa-mobile-alt',
                                                'Tablet' => 'fa-tablet-alt',
                                                'Desktop' => 'fa-desktop'
                                            ];
                                            $icon = $device_icons[$login['device']] ?? 'fa-question';
                                            
                                            echo '<tr>';
                                            echo '<td><strong>' . htmlspecialchars($login['username']) . '</strong></td>';
                                            echo '<td><span class="badge badge-' . $badge_color . '">' . htmlspecialchars(ucfirst($login['role'])) . '</span></td>';
                                            echo '<td><small>' . htmlspecialchars(date('M d, Y h:i A', strtotime($login['login_time']))) . '</small></td>';
                                            echo '<td><code style="font-size:0.75rem;">' . htmlspecialchars($login['ip_address']) . '</code></td>';
                                            echo '<td><small>' . htmlspecialchars($login['browser']) . '</small></td>';
                                            echo '<td><small><i class="fas ' . $icon . '"></i> ' . htmlspecialchars($login['device']) . '</small></td>';
                                            echo '<td>';
                                            if ($login['login_status'] === 'success') {
                                                echo '<span class="badge badge-success"><i class="fas fa-check"></i> Success</span>';
                                            } else {
                                                echo '<span class="badge badge-danger"><i class="fas fa-times"></i> Failed</span>';
                                            }
                                            echo '</td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="7" class="text-center text-muted py-3">No login history available yet.</td></tr>';
                                    }
                                } catch (PDOException $e) {
                                    error_log("Dashboard login history error: " . $e->getMessage());
                                    echo '<tr><td colspan="7" class="text-center text-danger">Error loading login history.</td></tr>';
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
/* Dashboard specific styles */
.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}
.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}
.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}
.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}
.text-xs {
    font-size: 0.7rem;
}
.badge {
    padding: 0.35em 0.65em;
    font-size: 0.75em;
}
</style>

<?php include 'footer.php'; ?>