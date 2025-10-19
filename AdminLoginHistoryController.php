<?php
/**
 * Admin Login History Controller
 * Displays complete login history with filtering and pagination
 *
 * IT Capstone Project - Dispatch Management System
 */

// Security check: Prevent direct access to this file
if (!defined('INDEX_ENTRY_POINT')) {
    header('Location: index.php?page=login');
    exit;
}

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id'])) {
    header('Location: ?page=login');
    exit;
}

if ($_SESSION['role'] !== 'admin') {
    header('Location: ?page=dashboard');
    exit;
}

require_once 'functions.php';

$db = get_db();
$pdo = $db->getConnection();

// Pagination settings
$records_per_page = 25;
$current_page = isset($_GET['pg']) && $_GET['pg'] !== '' ? max(1, intval($_GET['pg'])) : 1;
$offset = ($current_page - 1) * $records_per_page;

// Filter parameters
$filter_status = isset($_GET['status']) && $_GET['status'] !== '' ? $_GET['status'] : '';
$filter_role = isset($_GET['role']) && $_GET['role'] !== '' ? $_GET['role'] : '';
$filter_date = isset($_GET['date']) && $_GET['date'] !== '' ? $_GET['date'] : '';
$search_username = isset($_GET['search']) && $_GET['search'] !== '' ? trim($_GET['search']) : '';

// Build WHERE clause
$where_conditions = [];
$params = [];

if ($filter_status && in_array($filter_status, ['success', 'failed'])) {
    $where_conditions[] = "login_status = :status";
    $params[':status'] = $filter_status;
}

if ($filter_role && in_array($filter_role, ['admin', 'dispatcher', 'responder'])) {
    $where_conditions[] = "role = :role";
    $params[':role'] = $filter_role;
}

if ($filter_date) {
    $where_conditions[] = "DATE(login_time) = :date";
    $params[':date'] = $filter_date;
}

if ($search_username) {
    $where_conditions[] = "username LIKE :username";
    $params[':username'] = '%' . $search_username . '%';
}

$where_clause = count($where_conditions) > 0 ? 'WHERE ' . implode(' AND ', $where_conditions) : '';

// Get total records for pagination
try {
    $count_sql = "SELECT COUNT(*) FROM login_history " . $where_clause;
    $stmt = $pdo->prepare($count_sql);
    $stmt->execute($params);
    $total_records = $stmt->fetchColumn();
    $total_pages = ceil($total_records / $records_per_page);
} catch (PDOException $e) {
    error_log("Login history count error: " . $e->getMessage());
    $total_records = 0;
    $total_pages = 0;
}

// Fetch login history records
try {
    $sql = "SELECT * FROM login_history " . $where_clause . " ORDER BY login_time DESC LIMIT :limit OFFSET :offset";
    $stmt = $pdo->prepare($sql);
    
    // Bind filter parameters
    foreach ($params as $key => $value) {
        $stmt->bindValue($key, $value);
    }
    
    // Bind pagination parameters
    $stmt->bindValue(':limit', $records_per_page, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    
    $stmt->execute();
    $login_records = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    error_log("Login history fetch error: " . $e->getMessage());
    $login_records = [];
}

// Get summary statistics
try {
    $stats_sql = "
        SELECT 
            COUNT(*) as total_logins,
            SUM(CASE WHEN login_status = 'success' THEN 1 ELSE 0 END) as successful_logins,
            SUM(CASE WHEN login_status = 'failed' THEN 1 ELSE 0 END) as failed_logins,
            COUNT(DISTINCT username) as unique_users,
            COUNT(DISTINCT ip_address) as unique_ips
        FROM login_history " . $where_clause;
    
    $stmt = $pdo->prepare($stats_sql);
    $stmt->execute($params);
    $stats = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Handle null values
    $stats['total_logins'] = $stats['total_logins'] ?? 0;
    $stats['successful_logins'] = $stats['successful_logins'] ?? 0;
    $stats['failed_logins'] = $stats['failed_logins'] ?? 0;
    $stats['unique_users'] = $stats['unique_users'] ?? 0;
    $stats['unique_ips'] = $stats['unique_ips'] ?? 0;
} catch (PDOException $e) {
    error_log("Login history stats error: " . $e->getMessage());
    $stats = [
        'total_logins' => 0,
        'successful_logins' => 0,
        'failed_logins' => 0,
        'unique_users' => 0,
        'unique_ips' => 0
    ];
}

$pageTitle = "Login History";
$hideNavigation = true;
include 'header.php';
?>

<div class="row g-3 mb-4">
    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center">
            <h2 class="mb-0">
                <i class="fas fa-history text-danger"></i> Complete Login History
            </h2>
            <a href="?page=dashboard" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row g-3 mb-4">
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card shadow-soft h-100">
            <div class="card-body text-center">
                <i class="fas fa-list fa-2x text-primary mb-2"></i>
                <h5 class="mb-0"><?= number_format($stats['total_logins']) ?></h5>
                <small class="text-muted">Total Logins</small>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card shadow-soft h-100">
            <div class="card-body text-center">
                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                <h5 class="mb-0"><?= number_format($stats['successful_logins']) ?></h5>
                <small class="text-muted">Successful</small>
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6">
        <div class="card shadow-soft h-100">
            <div class="card-body text-center">
                <i class="fas fa-times-circle fa-2x text-danger mb-2"></i>
                <h5 class="mb-0"><?= number_format($stats['failed_logins']) ?></h5>
                <small class="text-muted">Failed</small>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-soft h-100">
            <div class="card-body text-center">
                <i class="fas fa-users fa-2x text-info mb-2"></i>
                <h5 class="mb-0"><?= number_format($stats['unique_users']) ?></h5>
                <small class="text-muted">Unique Users</small>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="card shadow-soft h-100">
            <div class="card-body text-center">
                <i class="fas fa-network-wired fa-2x text-warning mb-2"></i>
                <h5 class="mb-0"><?= number_format($stats['unique_ips']) ?></h5>
                <small class="text-muted">Unique IP Addresses</small>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card shadow-soft mb-4">
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <input type="hidden" name="page" value="admin_login_history">
            
            <div class="col-md-3">
                <label class="form-label">Search Username</label>
                <input type="text" name="search" class="form-control" placeholder="Enter username..." 
                       value="<?= htmlspecialchars($search_username) ?>">
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Status</label>
                <select name="status" class="form-select">
                    <option value="">All Status</option>
                    <option value="success" <?= $filter_status === 'success' ? 'selected' : '' ?>>Success</option>
                    <option value="failed" <?= $filter_status === 'failed' ? 'selected' : '' ?>>Failed</option>
                </select>
            </div>
            
            <div class="col-md-2">
                <label class="form-label">Role</label>
                <select name="role" class="form-select">
                    <option value="">All Roles</option>
                    <option value="admin" <?= $filter_role === 'admin' ? 'selected' : '' ?>>Admin</option>
                    <option value="dispatcher" <?= $filter_role === 'dispatcher' ? 'selected' : '' ?>>Dispatcher</option>
                    <option value="responder" <?= $filter_role === 'responder' ? 'selected' : '' ?>>Responder</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Date</label>
                <input type="date" name="date" class="form-control" value="<?= htmlspecialchars($filter_date) ?>">
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100 me-2">
                    <i class="fas fa-filter"></i> Filter
                </button>
                <a href="?page=admin_login_history" class="btn btn-secondary">
                    <i class="fas fa-redo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Login History Table -->
<div class="card shadow-soft">
    <div class="card-header bg-danger text-white">
        <h5 class="mb-0">
            <i class="fas fa-table"></i> Login Records
            <span class="badge bg-light text-dark ms-2"><?= number_format($total_records) ?> total</span>
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover table-striped align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ID</th>
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
                    <?php if (count($login_records) > 0): ?>
                        <?php foreach ($login_records as $record): ?>
                            <?php
                            $role_badges = [
                                'admin' => 'danger',
                                'dispatcher' => 'warning',
                                'responder' => 'info'
                            ];
                            $badge_color = $role_badges[$record['role']] ?? 'secondary';
                            
                            $device_icons = [
                                'Mobile' => 'fa-mobile-alt',
                                'Tablet' => 'fa-tablet-alt',
                                'Desktop' => 'fa-desktop'
                            ];
                            $device_icon = $device_icons[$record['device']] ?? 'fa-question';
                            ?>
                            <tr>
                                <td><small class="text-muted">#<?= htmlspecialchars((string)($record['id'] ?? '')) ?></small></td>
                                <td><strong><?= htmlspecialchars((string)($record['username'] ?? 'N/A')) ?></strong></td>
                                <td>
                                    <span class="badge bg-<?= $badge_color ?>">
                                        <?= htmlspecialchars(ucfirst((string)($record['role'] ?? 'unknown'))) ?>
                                    </span>
                                </td>
                                <td>
                                    <small>
                                        <?= htmlspecialchars(formatDate($record['login_time'] ?? '', 'M d, Y')) ?><br>
                                        <span class="text-muted"><?= htmlspecialchars(formatDate($record['login_time'] ?? '', 'h:i:s A')) ?></span>
                                    </small>
                                </td>
                                <td><code><?= htmlspecialchars((string)($record['ip_address'] ?? 'N/A')) ?></code></td>
                                <td><small><?= htmlspecialchars((string)($record['browser'] ?? 'N/A')) ?></small></td>
                                <td>
                                    <small>
                                        <i class="fas <?= $device_icon ?>"></i> 
                                        <?= htmlspecialchars((string)($record['device'] ?? 'Unknown')) ?>
                                    </small>
                                </td>
                                <td>
                                    <?php if (($record['login_status'] ?? '') === 'success'): ?>
                                        <span class="badge bg-success">
                                            <i class="fas fa-check"></i> Success
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times"></i> Failed
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                <i class="fas fa-inbox fa-3x text-muted mb-3 d-block"></i>
                                <p class="text-muted">No login records found.</p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <?php if ($total_pages > 1): ?>
            <nav aria-label="Login history pagination">
                <ul class="pagination justify-content-center mb-0 mt-3">
                    <?php
                    // Build query string for pagination
                    $query_params = $_GET;
                    unset($query_params['pg']);
                    $query_string = http_build_query($query_params);
                    ?>
                    
                    <!-- Previous Button -->
                    <li class="page-item <?= $current_page <= 1 ? 'disabled' : '' ?>">
                        <a class="page-link" href="?<?= $query_string ?>&pg=<?= $current_page - 1 ?>">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                    </li>

                    <?php
                    // Show page numbers
                    $start_page = max(1, $current_page - 2);
                    $end_page = min($total_pages, $current_page + 2);

                    if ($start_page > 1):
                    ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= $query_string ?>&pg=1">1</a>
                        </li>
                        <?php if ($start_page > 2): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
                        <li class="page-item <?= $i === $current_page ? 'active' : '' ?>">
                            <a class="page-link" href="?<?= $query_string ?>&pg=<?= $i ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>

                    <?php if ($end_page < $total_pages): ?>
                        <?php if ($end_page < $total_pages - 1): ?>
                            <li class="page-item disabled"><span class="page-link">...</span></li>
                        <?php endif; ?>
                        <li class="page-item">
                            <a class="page-link" href="?<?= $query_string ?>&pg=<?= $total_pages ?>"><?= $total_pages ?></a>
                        </li>
                    <?php endif; ?>

                    <!-- Next Button -->
                    <li class="page-item <?= $current_page >= $total_pages ? 'disabled' : '' ?>">
                        <a class="page-link" href="?<?= $query_string ?>&pg=<?= $current_page + 1 ?>">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                    </li>
                </ul>
            </nav>

            <p class="text-center text-muted mt-2 mb-0">
                Showing <?= number_format($offset + 1) ?> to <?= number_format(min($offset + $records_per_page, $total_records)) ?> 
                of <?= number_format($total_records) ?> records
            </p>
        <?php endif; ?>
    </div>
</div>

<?php include 'footer.php'; ?>