<?php
/**
 * login.php
 * Handles user login and session management with login history tracking.
 * Authenticates users against the 'users' table in MySQL.
 *
 * IT Capstone Project - Dispatch Management System
 */

// Start the session if it hasn't been started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Include necessary functions
require_once 'functions.php';

// Initialize error message variable
$error_message = '';
$success_message = '';

/**
 * Function to log login attempts to login_history table
 */
function log_login_attempt($db, $user_id, $username, $role, $status) {
    try {
        // Get IP address
        $ip_address = $_SERVER['REMOTE_ADDR'] ?? 'Unknown';
        
        // Get user agent
        $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'Unknown';
        
        // Parse browser from user agent
        $browser = 'Unknown';
        if (strpos($user_agent, 'Chrome') !== false && strpos($user_agent, 'Edg') === false) {
            $browser = 'Chrome';
        } elseif (strpos($user_agent, 'Firefox') !== false) {
            $browser = 'Firefox';
        } elseif (strpos($user_agent, 'Safari') !== false && strpos($user_agent, 'Chrome') === false) {
            $browser = 'Safari';
        } elseif (strpos($user_agent, 'Edg') !== false) {
            $browser = 'Edge';
        } elseif (strpos($user_agent, 'OPR') !== false || strpos($user_agent, 'Opera') !== false) {
            $browser = 'Opera';
        } elseif (strpos($user_agent, 'MSIE') !== false || strpos($user_agent, 'Trident') !== false) {
            $browser = 'Internet Explorer';
        }
        
        // Parse device from user agent
        $device = 'Desktop';
        if (preg_match('/mobile|android|iphone|ipad|tablet/i', $user_agent)) {
            if (preg_match('/tablet|ipad/i', $user_agent)) {
                $device = 'Tablet';
            } else {
                $device = 'Mobile';
            }
        }
        
        // Insert login history
        $sql = "INSERT INTO login_history (user_id, username, role, login_time, ip_address, user_agent, browser, device, login_status) 
                VALUES (:user_id, :username, :role, NOW(), :ip_address, :user_agent, :browser, :device, :login_status)";
        
        $params = [
            ':user_id' => $user_id,
            ':username' => $username,
            ':role' => $role,
            ':ip_address' => $ip_address,
            ':user_agent' => $user_agent,
            ':browser' => $browser,
            ':device' => $device,
            ':login_status' => $status
        ];
        
        $db->execute($sql, $params);
        
    } catch (Exception $e) {
        // Log error but don't stop login process
        error_log("Failed to log login attempt: " . $e->getMessage());
    }
}

/**
 * Check for rate limiting (prevent brute force)
 */
function check_rate_limit($username) {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
    }
    
    // Clean old attempts (older than 15 minutes)
    $current_time = time();
    $_SESSION['login_attempts'] = array_filter($_SESSION['login_attempts'], function($attempt) use ($current_time) {
        return ($current_time - $attempt['time']) < 900; // 15 minutes
    });
    
    // Count attempts for this username
    $attempts = array_filter($_SESSION['login_attempts'], function($attempt) use ($username) {
        return $attempt['username'] === $username;
    });
    
    return count($attempts) < 5; // Max 5 attempts per 15 minutes
}

function record_login_attempt($username) {
    if (!isset($_SESSION['login_attempts'])) {
        $_SESSION['login_attempts'] = [];
    }
    
    $_SESSION['login_attempts'][] = [
        'username' => $username,
        'time' => time()
    ];
}

// Check if user is already logged in, redirect to dashboard
if (is_logged_in()) {
    header('Location: ?page=dashboard');
    exit;
}

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!verify_csrf_token($_POST['csrf_token'] ?? '')) {
        $error_message = 'Invalid session token. Please refresh and try again.';
    } elseif (empty($username) || empty($password)) {
        $error_message = 'Please enter both username and password.';
    } elseif (!check_rate_limit($username)) {
        $error_message = 'Too many failed login attempts. Please try again in 15 minutes.';
    } else {
        try {
            $db = get_db();
            $user = $db->fetch("SELECT user_id, username, password, role FROM users WHERE username = :username LIMIT 1", [':username' => $username]);

            $passwordOk = false;
            if ($user) {
                if (password_get_info($user['password'])['algo']) {
                    $passwordOk = password_verify($password, $user['password']);
                } else {
                    $passwordOk = hash_equals($user['password'], $password);
                }
            }

            if ($user && $passwordOk) {
                // Login successful - Clear failed attempts
                unset($_SESSION['login_attempts']);
                
                // Log the successful attempt
                log_login_attempt($db, $user['user_id'], $user['username'], $user['role'], 'success');
                
                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                // Set session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['last_activity'] = time();
                $_SESSION['created_at'] = time();

                // Update last_login timestamp in the database
                $db->execute("UPDATE users SET last_login = NOW() WHERE user_id = :user_id", [':user_id' => $user['user_id']]);

                // Rehash legacy plain-text password to bcrypt
                if (!password_get_info($user['password'])['algo']) {
                    $newHash = password_hash($password, PASSWORD_DEFAULT);
                    $db->execute("UPDATE users SET password = :p WHERE user_id = :id", [':p' => $newHash, ':id' => $user['user_id']]);
                }

                // Redirect to dashboard
                header('Location: ?page=dashboard');
                exit;
            } else {
                // Record failed attempt
                record_login_attempt($username);
                
                // Login failed - Log the failed attempt
                $user_id_for_log = $user ? $user['user_id'] : null;
                $role_for_log = $user ? $user['role'] : null;
                log_login_attempt($db, $user_id_for_log, $username, $role_for_log, 'failed');
                
                $error_message = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            error_log("Login PDO error: " . $e->getMessage());
            $error_message = 'A database error occurred during login. Please try again later.';
        } catch (Exception $e) {
            error_log("Application error during login: " . $e->getMessage());
            $error_message = 'An unexpected error occurred. Please try again later.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - CDRRMO Dispatch System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="css.css">
</head>
<body>

<div class="container d-flex align-items-center justify-content-center" style="min-height:100vh;background:linear-gradient(135deg, #e9f2ff 0%, #ffffff 60%);">
    <div class="col-11 col-sm-8 col-md-6 col-lg-4">
        <div class="card shadow-lg p-4 rounded-3" style="backdrop-filter:saturate(180%) blur(6px);">
            <div class="text-center mb-3">
                <img src="image/cdrr.jpg" alt="CDRRMO Logo" class="mb-2" style="height:80px;width:auto;object-fit:contain;">
                <h4 class="mb-0 fw-bold">CDRRMO Dispatch System</h4>
                <small class="text-muted">Maasin City, Southern Leyte</small>
            </div>
            <div class="card-body p-0">
                <h5 class="text-center mb-3 fw-bold">Welcome Back</h5>
                <p class="text-center text-muted small mb-4">Sign in to your account</p>

                <?php if (!empty($error_message)): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <?= htmlspecialchars($error_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <?php if (!empty($success_message)): ?>
                    <div class="alert alert-success alert-dismissible fade show" role="alert">
                        <i class="fas fa-check-circle me-2"></i>
                        <?= htmlspecialchars($success_message); ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                    </div>
                <?php endif; ?>

                <form method="POST" action="?page=login" novalidate class="needs-validation" autocomplete="on">
                    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
                    
                    <div class="mb-3">
                        <label for="username" class="form-label small fw-semibold">
                            <i class="fas fa-user me-1"></i>Username
                        </label>
                        <input type="text" 
                               class="form-control" 
                               id="username" 
                               name="username" 
                               placeholder="Enter your username" 
                               required 
                               autocomplete="username"
                               value="<?= isset($_POST['username']) ? htmlspecialchars($_POST['username']) : '' ?>">
                        <div class="invalid-feedback">Please enter your username.</div>
                    </div>
                    
                    <div class="mb-4">
                        <label for="password" class="form-label small fw-semibold">
                            <i class="fas fa-lock me-1"></i>Password
                        </label>
                        <input type="password" 
                               class="form-control" 
                               id="password" 
                               name="password" 
                               placeholder="Enter your password" 
                               required 
                               autocomplete="current-password">
                        <div class="invalid-feedback">Please enter your password.</div>
                    </div>
                    
                    <div class="d-grid gap-2 mb-3">
                        <button type="submit" name="login" class="btn btn-primary btn-lg rounded-3">
                            <i class="fas fa-sign-in-alt me-2"></i>Sign In
                        </button>
                    </div>
                </form>

                <div class="text-center mt-4">
                    <small class="text-muted">Demo Access:</small><br>
                    <small class="text-muted">
                        <strong>Admin:</strong> admin / admin123<br>
                        <strong>Dispatcher:</strong> dispatcher / password<br>
                        <strong>Responder:</strong> responder / password
                    </small>
                </div>
            </div>

            <div class="text-center mt-3">
                <small class="text-muted">Secure Emergency Dispatch System</small><br>
                <small class="text-muted">Version 2.0 © 2025 CDRRMO Maasin</small>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
// Form validation
(function () {
    'use strict'
    var forms = document.querySelectorAll('.needs-validation')
    Array.prototype.slice.call(forms).forEach(function (form) {
        form.addEventListener('submit', function (event) {
            if (!form.checkValidity()) {
                event.preventDefault()
                event.stopPropagation()
            }
            form.classList.add('was-validated')
        }, false)
    })
})()
</script>

</body>
</html>

<?php include 'footer.php'; ?>