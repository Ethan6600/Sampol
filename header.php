<?php
/**
 * Clean Common header layout
 * No inline CSS/JS - everything in external files
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>CDRRMO Dispatch System - Maasin City - <?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></title>

  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  
  <link rel="stylesheet" href="css.css">
</head>
<body>
  <?php if (!isset($hideNavigation) || !$hideNavigation): ?>
  
  <aside class="sidebar" id="sidebar">
    <div class="sidebar-header">
      <a href="?page=dashboard" class="sidebar-logo">
        <img src="image/cdrr.jpg" alt="CDRRMO Logo">
        <div class="sidebar-logo-text">
          <span class="sidebar-logo-title">CDRRMO</span>
          <span class="sidebar-logo-subtitle">Dispatch System</span>
        </div>
      </a>
    </div>

    <nav class="sidebar-nav">
      <div class="nav-section-title">Main Menu</div>
      <a href="?page=dashboard" class="sidebar-nav-link <?= ($page ?? '') === 'dashboard' ? 'active' : '' ?>">
        <i class="fas fa-gauge-high"></i>
        <span>Dashboard Overview</span>
      </a>
      <a href="?page=incidents" class="sidebar-nav-link <?= ($page ?? '') === 'incidents' ? 'active' : '' ?>">
        <i class="fas fa-triangle-exclamation"></i>
        <span>Incident Management</span>
      </a>
      <a href="?page=dispatch" class="sidebar-nav-link <?= ($page ?? '') === 'dispatch' ? 'active' : '' ?>">
        <i class="fas fa-truck"></i>
        <span>Dispatch Operations</span>
      </a>

      <div class="nav-section-title">Resource Management</div>
      <a href="?page=vehicles" class="sidebar-nav-link <?= ($page ?? '') === 'vehicles' ? 'active' : '' ?>">
        <i class="fas fa-car-side"></i>
        <span>Vehicle Fleet</span>
      </a>
      <a href="?page=reports" class="sidebar-nav-link <?= ($page ?? '') === 'reports' ? 'active' : '' ?>">
        <i class="fas fa-chart-bar"></i>
        <span>Reports & Analytics</span>
      </a>

      <?php if (isset($_SESSION['role']) && $_SESSION['role'] === 'admin'): ?>
      <div class="nav-section-title">Administration</div>
      <a href="?page=edit_users" class="sidebar-nav-link <?= ($page ?? '') === 'edit_users' ? 'active' : '' ?>">
        <i class="fas fa-user-gear"></i>
        <span>User Management</span>
      </a>
      <a href="?page=admin_login_history" class="sidebar-nav-link <?= ($page ?? '') === 'admin_login_history' ? 'active' : '' ?>">
        <i class="fas fa-history"></i>
        <span>Login History</span>
      </a>
      <?php endif; ?>

      <div class="nav-section-title">Account</div>
      <div class="sidebar-user-info">
        <div class="user-greeting-small">Welcome,</div>
        <div class="username"><?= htmlspecialchars($_SESSION['username'] ?? 'User') ?></div>
        <div class="user-role"><?= htmlspecialchars(ucfirst($_SESSION['role'] ?? 'user')) ?></div>
      </div>
      <a href="index.php?page=logout" class="sidebar-nav-link">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout Session</span>
      </a>
    </nav>
  </aside>

  <div class="sidebar-overlay" id="sidebarOverlay"></div>

  <header class="top-bar">
    <div class="top-bar-left">
      <button class="menu-toggle" id="menuToggle">
        <i class="fas fa-bars"></i>
      </button>
      <div class="page-info">
        <h1 class="page-title"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
        <div class="breadcrumb">
          <span>CDRRMO</span>
          <i class="fas fa-chevron-right mx-2"></i>
          <span><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></span>
        </div>
      </div>
    </div>
    <div class="top-bar-right">
      <div class="top-bar-widgets">
        <div class="time-widget">
          <i class="fas fa-clock me-2"></i>
          <span id="current-time">Loading...</span>
        </div>
        <?php if (isset($_SESSION['username'])): ?>
        <div class="user-widget">
          <i class="fas fa-user-circle me-2"></i>
          <span><?= htmlspecialchars($_SESSION['username']) ?></span>
          <span class="role-badge"><?= htmlspecialchars(ucfirst($_SESSION['role'])) ?></span>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </header>

  <?php if (($page ?? 'dashboard') === 'dashboard'): ?>
  <section class="hero-section">
    <div class="hero-bg-pattern"></div>
    <div class="hero-content">
      <div class="hero-greeting">Good <?= date('H') < 12 ? 'Morning' : (date('H') < 18 ? 'Afternoon' : 'Evening') ?>,</div>
      <h2 class="hero-title">Welcome back, <?= htmlspecialchars(explode(' ', $_SESSION['username'] ?? 'Team')[0]) ?>! 👋</h2>
      <p class="hero-subtitle">Monitor and manage emergency dispatches efficiently</p>
      <div class="hero-stats">
        <div class="stat-item">
          <i class="fas fa-truck"></i>
          <span>Active Dispatches: <strong id="hero-active-dispatches">0</strong></span>
        </div>
        <div class="stat-item">
          <i class="fas fa-triangle-exclamation"></i>
          <span>Pending Incidents: <strong id="hero-pending-incidents">0</strong></span>
        </div>
      </div>
    </div>
    <div class="hero-character">
      <img src="image/cdrr.jpg" alt="CDRRMO Character">
    </div>
  </section>
  <?php endif; ?>

  <div class="main-content <?= ($page ?? 'dashboard') === 'dashboard' ? 'with-hero' : '' ?>">
    <!-- Global Search and Quick Actions -->
    <div class="global-controls no-print">
      <div class="container-fluid">
        <div class="row align-items-center mb-4">
          <div class="col-md-6">
            <div class="search-box">
              <i class="fas fa-search"></i>
              <input type="text" class="form-control" placeholder="Search across all records..." id="globalSearch">
            </div>
          </div>
          <div class="col-md-6">
            <div class="quick-actions justify-content-end">
              <button class="btn btn-outline-primary quick-action-btn" data-action="refresh" title="Refresh Data (Ctrl+R)">
                <i class="fas fa-sync-alt"></i> Refresh
              </button>
              <button class="btn btn-outline-success quick-action-btn" data-action="add-new" title="Add New Record">
                <i class="fas fa-plus"></i> New
              </button>
              <div class="dropdown">
                <button class="btn btn-outline-info dropdown-toggle" type="button" data-bs-toggle="dropdown">
                  <i class="fas fa-download"></i> Export
                </button>
                <ul class="dropdown-menu">
                  <li><a class="dropdown-item export-btn" href="#" data-format="csv"><i class="fas fa-file-csv"></i> Export as CSV</a></li>
                  <li><a class="dropdown-item export-btn" href="#" data-format="pdf"><i class="fas fa-file-pdf"></i> Export as PDF</a></li>
                  <li><a class="dropdown-item export-btn" href="#" data-format="excel"><i class="fas fa-file-excel"></i> Export as Excel</a></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Bulk Actions Panel -->
    <div class="bulk-actions no-print">
      <div class="container-fluid">
        <div class="d-flex align-items-center">
          <span class="selected-count me-3"><strong>0</strong> items selected</span>
          <button class="btn btn-sm btn-outline-primary me-2" onclick="dispatchSystem.executeBulkAction('edit')">
            <i class="fas fa-edit"></i> Edit Selected
          </button>
          <button class="btn btn-sm btn-outline-danger me-2" onclick="dispatchSystem.executeBulkAction('delete')">
            <i class="fas fa-trash"></i> Delete Selected
          </button>
          <button class="btn btn-sm btn-outline-secondary" onclick="dispatchSystem.clearSelections()">
            <i class="fas fa-times"></i> Clear Selection
          </button>
        </div>
      </div>
    </div>
  <?php endif; ?>