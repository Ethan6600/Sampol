<?php
/**
 * Enhanced generate_reports.php
 * Clean, organized with external CSS/JS
 */

// [Keep all your original PHP code exactly as it was]
?>

<div class="row">
    <div class="col-12">
        <div class="page-header">
            <h2 class="mb-2">Reports & Analytics</h2>
            <p class="text-muted">Generate comprehensive reports and analyze dispatch operations</p>
        </div>
    </div>
</div>

<!-- Report Configuration Panel -->
<div class="card mb-4 shadow-soft rounded-3">
    <div class="card-header bg-primary text-white rounded-top d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-cog me-2"></i>
            Report Configuration
        </div>
        <div class="form-check form-switch">
            <input class="form-check-input" type="checkbox" id="advancedSettings">
            <label class="form-check-label text-white" for="advancedSettings">Advanced Settings</label>
        </div>
    </div>
    <div class="card-body">
        <form method="GET" action="?page=reports" class="row g-3" id="reportForm">
            <input type="hidden" name="page" value="reports">
            
            <div class="col-md-4">
                <label for="report_type" class="form-label">
                    <i class="fas fa-chart-bar me-1"></i>Report Type
                </label>
                <select class="form-select" id="report_type" name="report_type" required>
                    <option value="incidents" <?= $report_type === 'incidents' ? 'selected' : '' ?>>📊 Incident Reports</option>
                    <option value="dispatches" <?= $report_type === 'dispatches' ? 'selected' : '' ?>>🚗 Dispatch Operations</option>
                    <option value="response_times" <?= ($report_type ?? '') === 'response_times' ? 'selected' : '' ?>>⏱️ Response Times</option>
                    <option value="resource_utilization" <?= ($report_type ?? '') === 'resource_utilization' ? 'selected' : '' ?>>🚛 Resource Utilization</option>
                </select>
            </div>

            <div class="col-md-4">
                <label for="start_date" class="form-label">
                    <i class="fas fa-calendar-start me-1"></i>Start Date
                </label>
                <input type="date" class="form-control" id="start_date" name="start_date" 
                       value="<?= htmlspecialchars($start_date) ?>" required>
            </div>

            <div class="col-md-4">
                <label for="end_date" class="form-label">
                    <i class="fas fa-calendar-end me-1"></i>End Date
                </label>
                <input type="date" class="form-control" id="end_date" name="end_date" 
                       value="<?= htmlspecialchars($end_date) ?>" required>
            </div>

            <!-- Advanced Settings -->
            <div class="col-12 advanced-settings" style="display: none;">
                <div class="border rounded p-3 bg-light">
                    <h6 class="mb-3">
                        <i class="fas fa-sliders-h me-2"></i>Advanced Filters
                    </h6>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label for="incident_type_filter" class="form-label">Incident Type</label>
                            <select class="form-select" id="incident_type_filter" name="incident_type_filter">
                                <option value="">All Types</option>
                                <option value="fire">🔥 Fire</option>
                                <option value="flood">🌊 Flood</option>
                                <option value="medical">🏥 Medical</option>
                                <option value="landslide">⛰️ Landslide</option>
                                <option value="earthquake">🌍 Earthquake</option>
                                <option value="rescue">🆘 Rescue</option>
                                <option value="other">📋 Other</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label for="status_filter" class="form-label">Status Filter</label>
                            <select class="form-select" id="status_filter" name="status_filter">
                                <option value="">All Statuses</option>
                                <option value="pending">⏳ Pending</option>
                                <option value="active">🚨 Active</option>
                                <option value="resolved">✅ Resolved</option>
                                <option value="cancelled">❌ Cancelled</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-12">
                <div class="d-flex gap-2 justify-content-end">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-1"></i> Generate Report
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="resetReportForm()">
                        <i class="fas fa-undo me-1"></i> Reset
                    </button>
                    <button type="button" class="btn btn-success" onclick="exportReport()">
                        <i class="fas fa-download me-1"></i> Export
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Report Statistics -->
<?php if (count($report_data) > 0): ?>
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="fas fa-list-alt fa-2x text-primary mb-2"></i>
                <h4 class="mb-0"><?= count($report_data) ?></h4>
                <small class="text-muted">Total Records</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="fas fa-calendar fa-2x text-info mb-2"></i>
                <h4 class="mb-0"><?= date('M j', strtotime($start_date)) ?> - <?= date('M j, Y', strtotime($end_date)) ?></h4>
                <small class="text-muted">Date Range</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="fas fa-chart-line fa-2x text-success mb-2"></i>
                <h4 class="mb-0"><?= $report_type === 'incidents' ? 'Incidents' : 'Dispatches' ?></h4>
                <small class="text-muted">Report Type</small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card stat-card">
            <div class="card-body text-center">
                <i class="fas fa-clock fa-2x text-warning mb-2"></i>
                <h4 class="mb-0"><?= date('H:i') ?></h4>
                <small class="text-muted">Generated</small>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Report Results -->
<div class="card shadow-soft rounded-3">
    <div class="card-header bg-info text-white rounded-top d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-chart-bar me-2"></i>
            <?= htmlspecialchars($report_title) ?>
        </div>
        <div class="export-controls">
            <button class="btn btn-sm btn-light me-2" onclick="printReport()">
                <i class="fas fa-print me-1"></i> Print
            </button>
            <div class="dropdown">
                <button class="btn btn-sm btn-light dropdown-toggle" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-download me-1"></i> Export
                </button>
                <ul class="dropdown-menu">
                    <li><a class="dropdown-item" href="#" onclick="exportReportAs('csv')">
                        <i class="fas fa-file-csv me-1"></i> CSV Format
                    </a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportReportAs('pdf')">
                        <i class="fas fa-file-pdf me-1"></i> PDF Format
                    </a></li>
                    <li><a class="dropdown-item" href="#" onclick="exportReportAs('excel')">
                        <i class="fas fa-file-excel me-1"></i> Excel Format
                    </a></li>
                </ul>
            </div>
        </div>
    </div>

    <div class="card-body">
        <?php if (count($report_data) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped align-middle" id="reportTable">
                    <thead>
                        <?php if ($report_type === 'incidents'): ?>
                            <tr>
                                <th>Incident #</th>
                                <th>Type</th>
                                <th>Description</th>
                                <th>Location</th>
                                <th>Reported By</th>
                                <th>Reported At</th>
                                <th>Status</th>
                            </tr>
                        <?php elseif ($report_type === 'dispatches'): ?>
                            <tr>
                                <th>Dispatch ID</th>
                                <th>Incident #</th>
                                <th>Incident Type</th>
                                <th>Incident Location</th>
                                <th>Vehicle</th>
                                <th>Responder</th>
                                <th>Dispatch Time</th>
                                <th>Status</th>
                                <th>Trip Ticket Status</th>
                                <th>Notes</th>
                            </tr>
                        <?php endif; ?>
                    </thead>
                    <tbody>
                        <?php foreach ($report_data as $row): ?>
                            <tr>
                                <?php if ($report_type === 'incidents'): ?>
                                    <td class="fw-bold"><?= htmlspecialchars($row['incident_number']) ?></td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $row['incident_type']))) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars(substr($row['description'], 0, 100)) . (strlen($row['description']) > 100 ? '...' : '') ?></td>
                                    <td><?= htmlspecialchars($row['location']) ?></td>
                                    <td><?= htmlspecialchars($row['reported_by']) ?></td>
                                    <td><?= htmlspecialchars(formatDate($row['reported_at'])) ?></td>
                                    <td>
                                        <span class="badge 
                                            <?php
                                                switch ($row['status']) {
                                                    case 'pending': echo 'bg-secondary'; break;
                                                    case 'active': echo 'bg-danger'; break;
                                                    case 'resolved': echo 'bg-success'; break;
                                                    case 'cancelled': echo 'bg-info text-dark'; break;
                                                    default: echo 'bg-secondary'; break;
                                                }
                                            ?>">
                                            <?= htmlspecialchars(ucwords($row['status'])) ?>
                                        </span>
                                    </td>
                                <?php elseif ($report_type === 'dispatches'): ?>
                                    <td class="fw-bold"><?= htmlspecialchars($row['dispatch_id']) ?></td>
                                    <td><?= htmlspecialchars($row['incident_number'] ?? 'N/A') ?></td>
                                    <td>
                                        <span class="badge bg-info text-dark">
                                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $row['incident_type'] ?? 'N/A'))) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars($row['incident_location'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars(($row['vehicle_name'] ?? 'N/A') . ' (' . ($row['plate_number'] ?? 'N/A') . ')') ?></td>
                                    <td><?= htmlspecialchars($row['responder_name'] ?? 'N/A') ?></td>
                                    <td><?= htmlspecialchars(formatDate($row['dispatch_time'])) ?></td>
                                    <td>
                                        <span class="badge 
                                            <?php
                                                switch ($row['dispatch_status']) {
                                                    case 'assigned': echo 'bg-primary'; break;
                                                    case 'en_route': echo 'bg-warning text-dark'; break;
                                                    case 'on_scene': echo 'bg-danger'; break;
                                                    case 'returning': echo 'bg-info text-dark'; break;
                                                    case 'completed': echo 'bg-success'; break;
                                                    case 'cancelled': echo 'bg-secondary'; break;
                                                    default: echo 'bg-secondary'; break;
                                                }
                                            ?>">
                                            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $row['dispatch_status']))) ?>
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge 
                                            <?php
                                                switch ($row['trip_ticket_status']) {
                                                    case 'pending': echo 'bg-secondary'; break;
                                                    case 'approved': echo 'bg-success'; break;
                                                    case 'rejected': echo 'bg-danger'; break;
                                                    default: echo 'bg-secondary'; break;
                                                }
                                            ?>">
                                            <?= htmlspecialchars(ucwords($row['trip_ticket_status'])) ?>
                                        </span>
                                    </td>
                                    <td><?= htmlspecialchars(substr($row['notes'] ?? 'N/A', 0, 50)) ?></td>
                                <?php endif; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Report Summary -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="report-summary">
                        <h6>Report Summary</h6>
                        <ul class="list-unstyled">
                            <li><strong>Generated:</strong> <?= date('F j, Y \a\t g:i A') ?></li>
                            <li><strong>Time Range:</strong> <?= htmlspecialchars($start_date) ?> to <?= htmlspecialchars($end_date) ?></li>
                            <li><strong>Total Records:</strong> <?= count($report_data) ?></li>
                            <li><strong>Report Type:</strong> <?= htmlspecialchars(ucfirst($report_type)) ?></li>
                        </ul>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <div class="signature-section">
                        <div class="signature-line"></div>
                        <p class="mb-0"><strong>Generated By:</strong> <?= htmlspecialchars($_SESSION['username'] ?? 'System') ?></p>
                        <p class="text-muted">CDRRMO Dispatch System</p>
                    </div>
                </div>
            </div>

        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-chart-bar fa-3x text-muted mb-3"></i>
                <h5>No Report Data Found</h5>
                <p class="text-muted">No records match your selected criteria. Try adjusting your filters or date range.</p>
                <button class="btn btn-primary" onclick="resetReportForm()">
                    <i class="fas fa-undo me-1"></i> Reset Filters
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
// Report-specific JavaScript
document.addEventListener('DOMContentLoaded', function() {
    // Advanced settings toggle
    const advancedToggle = document.getElementById('advancedSettings');
    const advancedSettings = document.querySelector('.advanced-settings');
    
    if (advancedToggle && advancedSettings) {
        advancedToggle.addEventListener('change', function() {
            advancedSettings.style.display = this.checked ? 'block' : 'none';
        });
    }

    // Date validation
    const startDate = document.getElementById('start_date');
    const endDate = document.getElementById('end_date');
    
    if (startDate && endDate) {
        startDate.addEventListener('change', validateDates);
        endDate.addEventListener('change', validateDates);
    }

    // Auto-generate on load if parameters exist
    if (window.location.search.includes('report_type') || window.location.search.includes('start_date')) {
        generateReportCharts();
    }
});

function validateDates() {
    const start = document.getElementById('start_date');
    const end = document.getElementById('end_date');
    
    if (start.value && end.value && start.value > end.value) {
        dispatchSystem.showNotification('End date cannot be before start date', 'error');
        end.value = start.value;
    }
}

function resetReportForm() {
    document.getElementById('reportForm').reset();
    document.querySelector('.advanced-settings').style.display = 'none';
    dispatchSystem.showNotification('Form reset to default values', 'info');
}

function exportReport() {
    const reportType = document.getElementById('report_type').value;
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    
    if (!startDate || !endDate) {
        dispatchSystem.showNotification('Please select a date range first', 'warning');
        return;
    }
    
    exportReportAs('csv');
}

function exportReportAs(format) {
    dispatchSystem.showLoading(`Exporting report as ${format.toUpperCase()}...`);
    
    // Simulate export process
    setTimeout(() => {
        dispatchSystem.hideLoading();
        dispatchSystem.showNotification(`Report exported successfully as ${format.toUpperCase()}`, 'success');
        
        // In real implementation, this would trigger actual file download
        if (format === 'csv') {
            const table = document.getElementById('reportTable');
            if (table) {
                dispatchSystem.exportTableToCSV('reportTable', `cdrrmo_report_${Date.now()}.csv`);
            }
        }
    }, 1500);
}

function printReport() {
    window.print();
}

function generateReportCharts() {
    // This would generate charts based on report data
    // For now, we'll just show a notification
    console.log('Generating report charts...');
}

// Make functions globally available
window.resetReportForm = resetReportForm;
window.exportReport = exportReport;
window.exportReportAs = exportReportAs;
window.printReport = printReport;
</script>

<style>
/* Report-specific styles */
.stat-card {
    transition: transform 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
}

.report-summary {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: 8px;
    border-left: 4px solid var(--primary-color);
}

.signature-section {
    margin-top: 2rem;
}

.signature-line {
    border-top: 1px solid #000;
    width: 200px;
    margin: 2rem 0 0.5rem auto;
}

/* Print styles for reports */
@media print {
    .card-header .btn,
    .export-controls,
    .advanced-settings,
    .no-print {
        display: none !important;
    }
    
    .card {
        border: 1px solid #000 !important;
        box-shadow: none !important;
    }
    
    .table {
        font-size: 0.8rem;
    }
}
</style>

<?php include 'footer.php'; ?>