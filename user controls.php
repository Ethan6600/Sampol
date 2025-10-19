<?php
/**
 * Enhanced user controls.php
 * Clean, organized with external CSS/JS
 */

// [Keep all your original PHP code exactly as it was]
?>

<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h2 class="mb-2">User Management</h2>
            <p class="text-muted">Manage system users and their access permissions</p>
        </div>
    </div>
</div>

<!-- User Statistics -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="fas fa-user-shield fa-2x text-danger mb-2"></i>
                <h4 class="mb-0" id="adminCount">0</h4>
                <small class="text-muted">Administrators</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="fas fa-truck fa-2x text-warning mb-2"></i>
                <h4 class="mb-0" id="dispatcherCount">0</h4>
                <small class="text-muted">Dispatchers</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="fas fa-user-md fa-2x text-info mb-2"></i>
                <h4 class="mb-0" id="responderCount">0</h4>
                <small class="text-muted">Responders</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="fas fa-users fa-2x text-success mb-2"></i>
                <h4 class="mb-0" id="totalUsers"><?= count($users) ?></h4>
                <small class="text-muted">Total Users</small>
            </div>
        </div>
    </div>
</div>

<div class="card mb-4 shadow-soft rounded-3">
    <div class="card-header bg-primary text-white rounded-top">
        <i class="fas fa-user-plus me-2"></i>
        <?= $edit_user_data ? 'Edit User Account' : 'Create New User Account' ?>
    </div>
    <div class="card-body">
        <form method="POST" action="?page=edit_users" novalidate class="needs-validation" id="userForm">
            <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
            <?php if ($edit_user_data): ?>
                <input type="hidden" name="user_id" value="<?= htmlspecialchars($edit_user_data['user_id']) ?>">
            <?php endif; ?>

            <div class="row g-3">
                <div class="col-md-6">
                    <label for="username" class="form-label">
                        <i class="fas fa-user me-1 text-primary"></i>
                        Username <span class="text-danger">*</span>
                    </label>
                    <input type="text" class="form-control" id="username" name="username" 
                           placeholder="Enter unique username"
                           value="<?= htmlspecialchars($edit_user_data['username'] ?? '') ?>" required>
                    <div class="invalid-feedback">Please provide a username.</div>
                    <div class="form-text">Username must be unique and contain only letters, numbers, and underscores.</div>
                </div>

                <div class="col-md-6">
                    <label for="role" class="form-label">
                        <i class="fas fa-user-tag me-1 text-warning"></i>
                        User Role <span class="text-danger">*</span>
                    </label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="">Select User Role</option>
                        <option value="admin" <?= ($edit_user_data['role'] ?? '') === 'admin' ? 'selected' : '' ?>>👑 Administrator</option>
                        <option value="dispatcher" <?= ($edit_user_data['role'] ?? '') === 'dispatcher' ? 'selected' : '' ?>>📞 Dispatcher</option>
                        <option value="responder" <?= ($edit_user_data['role'] ?? '') === 'responder' ? 'selected' : '' ?>>🚑 Responder</option>
                    </select>
                    <div class="invalid-feedback">Please select a user role.</div>
                    <div class="form-text">
                        <strong>Admin:</strong> Full system access • 
                        <strong>Dispatcher:</strong> Manage incidents & dispatches • 
                        <strong>Responder:</strong> View assigned tasks
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="password" class="form-label">
                        <i class="fas fa-lock me-1 text-success"></i>
                        <?= $edit_user_data ? 'New Password (leave blank to keep current)' : 'Password' ?>
                        <?= $edit_user_data ? '' : '<span class="text-danger">*</span>' ?>
                    </label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="<?= $edit_user_data ? 'Enter new password' : 'Enter secure password' ?>"
                               <?= $edit_user_data ? '' : 'required' ?>>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="invalid-feedback">Please provide a password.</div>
                    <div class="form-text">
                        Password must be at least 8 characters long and include uppercase, lowercase, and numbers.
                        <?php if ($edit_user_data): ?>
                            <br><strong>Note:</strong> Leave blank to maintain current password.
                        <?php endif; ?>
                    </div>
                </div>

                <div class="col-md-6">
                    <label for="confirm_password" class="form-label">
                        <i class="fas fa-lock me-1 text-info"></i>
                        Confirm Password
                        <?= $edit_user_data ? '' : '<span class="text-danger">*</span>' ?>
                    </label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password"
                           placeholder="Re-enter the password"
                           <?= $edit_user_data ? '' : 'required' ?>>
                    <div class="invalid-feedback">Passwords do not match.</div>
                </div>

                <?php if ($edit_user_data): ?>
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="fas fa-calendar me-1 text-secondary"></i>
                        Account Created
                    </label>
                    <input type="text" class="form-control" 
                           value="<?= htmlspecialchars(formatDate($edit_user_data['created_at'])) ?>" disabled>
                </div>
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="fas fa-sign-in-alt me-1 text-primary"></i>
                        Last Login
                    </label>
                    <input type="text" class="form-control" 
                           value="<?= htmlspecialchars($edit_user_data['last_login'] ? formatDate($edit_user_data['last_login']) : 'Never logged in') ?>" disabled>
                </div>
                <?php endif; ?>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end mt-4">
                <?php if ($edit_user_data): ?>
                    <button type="submit" name="edit_user" class="btn btn-success btn-lg">
                        <i class="fas fa-save me-1"></i> Update User
                    </button>
                    <a href="?page=edit_users" class="btn btn-secondary btn-lg">
                        <i class="fas fa-undo me-1"></i> Cancel Edit
                    </a>
                <?php else: ?>
                    <button type="submit" name="add_user" class="btn btn-primary btn-lg">
                        <i class="fas fa-user-plus me-1"></i> Create User
                    </button>
                    <button type="reset" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-eraser me-1"></i> Clear Form
                    </button>
                <?php endif; ?>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card shadow-soft rounded-3">
    <div class="card-header bg-info text-white rounded-top d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-users me-2"></i>
            System Users
            <span class="badge bg-light text-dark ms-2" id="usersCount"><?= count($users) ?></span>
        </div>
        <div class="export-controls">
            <button class="btn btn-sm btn-light me-2" onclick="dispatchSystem.exportTableToCSV('usersTable', 'users.csv')">
                <i class="fas fa-download me-1"></i> Export
            </button>
            <button class="btn btn-sm btn-light" onclick="window.print()">
                <i class="fas fa-print me-1"></i> Print
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (count($users) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover table-striped align-middle" id="usersTable">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" class="form-check-input select-all" id="selectAllUsers">
                            </th>
                            <th>User Account</th>
                            <th>Role & Permissions</th>
                            <th>Account Activity</th>
                            <th>Status</th>
                            <th width="120" class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($users as $user): ?>
                            <tr class="user-row" data-role="<?= htmlspecialchars($user['role']) ?>">
                                <td>
                                    <input type="checkbox" class="form-check-input item-select" value="<?= htmlspecialchars($user['user_id']) ?>">
                                </td>
                                <td>
                                    <div class="user-info">
                                        <div class="fw-bold text-primary"><?= htmlspecialchars($user['username']) ?></div>
                                        <div class="text-muted small">
                                            <i class="fas fa-key me-1"></i>
                                            Password: <span class="text-muted">••••••••</span>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="role-info">
                                        <span class="badge 
                                            <?php
                                                switch ($user['role']) {
                                                    case 'admin': echo 'bg-danger'; break;
                                                    case 'dispatcher': echo 'bg-warning text-dark'; break;
                                                    case 'responder': echo 'bg-info'; break;
                                                    default: echo 'bg-secondary'; break;
                                                }
                                            ?>">
                                            <i class="fas 
                                                <?php
                                                    switch ($user['role']) {
                                                        case 'admin': echo 'fa-user-shield'; break;
                                                        case 'dispatcher': echo 'fa-truck'; break;
                                                        case 'responder': echo 'fa-user-md'; break;
                                                        default: echo 'fa-user'; break;
                                                    }
                                                ?> me-1">
                                            </i>
                                            <?= htmlspecialchars(ucfirst($user['role'])) ?>
                                        </span>
                                        <div class="small text-muted mt-1">
                                            <?php
                                                switch ($user['role']) {
                                                    case 'admin': echo 'Full system access & management'; break;
                                                    case 'dispatcher': echo 'Incident & dispatch management'; break;
                                                    case 'responder': echo 'View assigned tasks only'; break;
                                                    default: echo 'Limited access'; break;
                                                }
                                            ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="activity-info">
                                        <div class="small">
                                            <i class="fas fa-calendar-plus me-1"></i>
                                            Created: <?= htmlspecialchars(formatDate($user['created_at'], 'M d, Y')) ?>
                                        </div>
                                        <div class="small text-muted">
                                            <i class="fas fa-sign-in-alt me-1"></i>
                                            Last login: <?= htmlspecialchars($user['last_login'] ? formatDate($user['last_login'], 'M d, H:i') : 'Never') ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="status-badge bg-success">
                                        <i class="fas fa-check-circle me-1"></i>
                                        Active
                                    </span>
                                    
                                    <!-- Quick Role Update -->
                                    <div class="mt-1">
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-danger btn-sm quick-role-btn" 
                                                    data-id="<?= htmlspecialchars($user['user_id']) ?>" 
                                                    data-role="admin"
                                                    title="Set as Administrator">
                                                <i class="fas fa-user-shield"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-warning btn-sm quick-role-btn"
                                                    data-id="<?= htmlspecialchars($user['user_id']) ?>" 
                                                    data-role="dispatcher"
                                                    title="Set as Dispatcher">
                                                <i class="fas fa-truck"></i>
                                            </button>
                                            <button type="button" class="btn btn-outline-info btn-sm quick-role-btn"
                                                    data-id="<?= htmlspecialchars($user['user_id']) ?>" 
                                                    data-role="responder"
                                                    title="Set as Responder">
                                                <i class="fas fa-user-md"></i>
                                            </button>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="action-buttons">
                                        <a href="?page=edit_users&action=edit&id=<?= htmlspecialchars($user['user_id']) ?>" 
                                           class="btn btn-sm btn-warning me-1" title="Edit User">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="btn btn-sm btn-danger delete-user-btn" 
                                                data-id="<?= htmlspecialchars($user['user_id']) ?>" 
                                                data-username="<?= htmlspecialchars($user['username']) ?>"
                                                title="Delete User">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Table Summary -->
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="text-muted small" id="userSummary">
                        Showing <?= count($users) ?> system users
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <nav aria-label="User pagination">
                        <ul class="pagination pagination-sm justify-content-end mb-0">
                            <li class="page-item disabled"><a class="page-link" href="#">Previous</a></li>
                            <li class="page-item active"><a class="page-link" href="#">1</a></li>
                            <li class="page-item"><a class="page-link" href="#">2</a></li>
                            <li class="page-item"><a class="page-link" href="#">Next</a></li>
                        </ul>
                    </nav>
                </div>
            </div>
            
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-users fa-3x text-muted mb-3"></i>
                <h5>No Users Found</h5>
                <p class="text-muted">No user accounts have been created yet. Create the first user account to get started.</p>
                <a href="?page=edit_users" class="btn btn-primary">
                    <i class="fas fa-user-plus me-1"></i> Create First User
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// User-specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeUserStatistics();
    initializeUserFilters();
    initializeQuickRoleUpdates();
    setupUserFormValidation();
    initializePasswordToggle();
    
    // Password strength indicator
    const passwordInput = document.getElementById('password');
    if (passwordInput) {
        passwordInput.addEventListener('input', checkPasswordStrength);
    }
});

function initializeUserStatistics() {
    const users = <?= json_encode($users) ?>;
    let admin = 0, dispatcher = 0, responder = 0;
    
    users.forEach(user => {
        switch(user.role) {
            case 'admin': admin++; break;
            case 'dispatcher': dispatcher++; break;
            case 'responder': responder++; break;
        }
    });
    
    document.getElementById('adminCount').textContent = admin;
    document.getElementById('dispatcherCount').textContent = dispatcher;
    document.getElementById('responderCount').textContent = responder;
    document.getElementById('totalUsers').textContent = users.length;
}

function initializeUserFilters() {
    const roleFilter = document.getElementById('roleFilter');
    if (roleFilter) {
        roleFilter.addEventListener('change', filterUsers);
    }
}

function filterUsers() {
    const role = document.getElementById('roleFilter')?.value || '';
    const rows = document.querySelectorAll('.user-row');
    let visibleCount = 0;
    
    rows.forEach(row => {
        const showRow = !role || row.dataset.role === role;
        row.style.display = showRow ? '' : 'none';
        if (showRow) visibleCount++;
    });
    
    document.getElementById('usersCount').textContent = visibleCount;
    document.getElementById('userSummary').textContent = `Showing ${visibleCount} of ${rows.length} users`;
}

function initializeQuickRoleUpdates() {
    document.querySelectorAll('.quick-role-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const userId = this.dataset.id;
            const newRole = this.dataset.role;
            const username = this.closest('tr').querySelector('.fw-bold').textContent;
            
            if (confirm(`Change ${username}'s role to "${newRole}"?`)) {
                updateUserRole(userId, newRole);
            }
        });
    });
}

function updateUserRole(userId, newRole) {
    dispatchSystem.showLoading('Updating user role...');
    
    // Simulate API call - in real implementation, this would be an AJAX call
    setTimeout(() => {
        dispatchSystem.hideLoading();
        dispatchSystem.showNotification(`User role updated to ${newRole}`, 'success');
        
        // Update UI
        const row = document.querySelector(`.quick-role-btn[data-id="${userId}"]`).closest('tr');
        const roleBadge = row.querySelector('.badge');
        
        if (roleBadge) {
            roleBadge.textContent = newRole;
            roleBadge.className = `badge bg-${getUserRoleColor(newRole)}`;
            
            // Update row data attribute
            row.dataset.role = newRole;
        }
        
        // Refresh statistics
        initializeUserStatistics();
        filterUsers();
    }, 1000);
}

function getUserRoleColor(role) {
    const colors = {
        'admin': 'danger',
        'dispatcher': 'warning',
        'responder': 'info'
    };
    return colors[role] || 'secondary';
}

function setupUserFormValidation() {
    const userForm = document.getElementById('userForm');
    if (userForm) {
        userForm.addEventListener('submit', function(e) {
            if (!validateUserForm()) {
                e.preventDefault();
                dispatchSystem.showNotification('Please check the form for errors', 'error');
            }
        });

        // Real-time password confirmation validation
        const password = document.getElementById('password');
        const confirmPassword = document.getElementById('confirm_password');
        
        if (password && confirmPassword) {
            confirmPassword.addEventListener('input', function() {
                if (password.value !== this.value && this.value !== '') {
                    this.classList.add('is-invalid');
                } else {
                    this.classList.remove('is-invalid');
                }
            });
        }
    }
}

function validateUserForm() {
    const username = document.getElementById('username');
    const role = document.getElementById('role');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm_password');
    const isEdit = <?= $edit_user_data ? 'true' : 'false' ?>;
    
    let isValid = true;
    
    if (!username.value.trim()) {
        showFieldError(username, 'Username is required');
        isValid = false;
    }
    
    if (!role.value) {
        showFieldError(role, 'Please select a user role');
        isValid = false;
    }
    
    if (!isEdit && !password.value) {
        showFieldError(password, 'Password is required for new users');
        isValid = false;
    }
    
    if (password.value && password.value.length < 8) {
        showFieldError(password, 'Password must be at least 8 characters long');
        isValid = false;
    }
    
    if (password.value && confirmPassword.value && password.value !== confirmPassword.value) {
        showFieldError(confirmPassword, 'Passwords do not match');
        isValid = false;
    }
    
    return isValid;
}

function showFieldError(field, message) {
    // Clear existing error
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) existingError.remove();
    
    // Add new error
    const errorDiv = document.createElement('div');
    errorDiv.className = 'field-error text-danger small mt-1';
    errorDiv.textContent = message;
    field.parentNode.appendChild(errorDiv);
    field.classList.add('is-invalid');
}

function initializePasswordToggle() {
    const toggleButton = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    
    if (toggleButton && passwordInput) {
        toggleButton.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.innerHTML = type === 'password' ? '<i class="fas fa-eye"></i>' : '<i class="fas fa-eye-slash"></i>';
        });
    }
}

function checkPasswordStrength() {
    const password = this.value;
    // Simple password strength check - can be enhanced
    if (password.length >= 8) {
        this.classList.add('is-valid');
        this.classList.remove('is-invalid');
    } else if (password.length > 0) {
        this.classList.add('is-invalid');
        this.classList.remove('is-valid');
    } else {
        this.classList.remove('is-valid', 'is-invalid');
    }
}

// Enhanced delete with confirmation
document.querySelectorAll('.delete-user-btn').forEach(button => {
    button.addEventListener('click', function() {
        const userId = this.dataset.id;
        const username = this.dataset.username;
        
        if (confirm(`Are you sure you want to delete user "${username}"? This action cannot be undone.`)) {
            document.getElementById('deleteUserId').value = userId;
            document.getElementById('deleteUserForm').submit();
        }
    });
});

// Make functions available globally
window.filterUsers = filterUsers;
window.updateUserRole = updateUserRole;
</script>

<!-- Hidden forms for actions -->
<form id="deleteUserForm" method="POST" action="?page=edit_users" style="display: none;">
    <input type="hidden" name="delete_user" value="1">
    <input type="hidden" name="user_id" id="deleteUserId">
    <input type="hidden" name="csrf_token" value="<?= htmlspecialchars(get_csrf_token()) ?>">
</form>

<style>
/* User-specific styles */
.user-info,
.role-info,
.activity-info {
    line-height: 1.4;
}

.role-info .badge {
    font-size: 0.8rem;
}

#togglePassword {
    border-color: #6c757d;
}

#togglePassword:hover {
    background-color: #6c757d;
    color: white;
}

@media (max-width: 768px) {
    .user-info .fw-bold {
        font-size: 0.9rem;
    }
    
    .action-buttons {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
    }
}
</style>

<?php include 'footer.php'; ?>